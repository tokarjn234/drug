(function (window, $, App) {

    function MessagesController($scope, $ajax, $location, $timeout, $q) {
        var jsonData = window.jsonData;
		var Order = $scope.Order = App.Models.Order;
		var senderName = '送信者名';
		var storeName = '店舗名';
		var msgFormatParams = {};
		msgFormatParams[storeName] = jsonData.storeName;
		var MessageTemplate = App.Models.MessageTemplate;
        $scope.msgOrdering = jsonData.currentMsgOrdering || '1';

        $scope.formatMsg = function(s) {

            if ($.isEmptyObject($scope.currentOrder)) {
                return '' ;
            }

            msgFormatParams[senderName] =  $scope.currentOrder.full_name;
            return App.formatMessage(s, msgFormatParams);
        }

        $scope.getMessageLength = function() {

            return $('#messageContent').val().replace(/(\r\n|\n|\r)/g, '--').length;
        }

		$scope.btnText = {
			others: 'その他通知',
			received : '受付通知',
			prepared: '調剤完了通知'
		};


        $scope.activeIndex = 0;
        $scope.orders = jsonData.orders || [];

        $scope.currentOrder = {};
        $scope.messageTemplates = jsonData.messageTemplates;
        $scope.Message = {
            type:'others',
            header: jsonData.SentMsgConfirm
        };


        $scope.init = function() {
            var search = $location.search();
            var id = search.id;
            var type = search.type;
            $scope.loading = false;

            if (id) {
                var order = $scope.currentOrder = Linq.From($scope.orders).Where(function(i) {return i.order_alias == id}).FirstOrDefault();

				if (!order) {
                    return toastr.error(__('OrderNotFound'));
                }

				if (type) {
					$scope.singleMode = true;
                    $scope.orders = [order];

				}

                $scope.activeIndex = $scope.orders.indexOf(order);

            } else {
				if ($scope.orders.length === 0) {
					return toastr.error(__('ThereIsNoOrder'));
				}

                id = $scope.orders[$scope.activeIndex].order_alias;
            }

            $scope.currentOrder = $scope.orders[$scope.activeIndex];

            $ajax.$get(jsonData.getMessageUrl, {order_alias: id}, function(r) {

                if (!$scope.messages || !angular.equals($scope.messages , r.data.messages))  {
                    log('Updated new messages');
                    $scope.messages = r.data.messages;
                }

                if (type === 'prepared' || type === 'received') {
                    if ($('#msgTemplateHeader' + jsonData.defaultMessages[type].id).hasClass('disabledMsg')) {
                        return;
                    }

                    $scope.Message.type = type;
					msgFormatParams[senderName] = $scope.currentOrder.full_name;
                    $scope.Message.title = App.formatMessage(jsonData.defaultMessages[type].title, msgFormatParams);
                    $scope.Message.content = App.formatMessage(jsonData.defaultMessages[type].content, msgFormatParams);
                    $scope.Message.header = jsonData.defaultMessages[type].header;
                    $('#msgTemplate' + jsonData.defaultMessages[type].id).slideDown(500);

                }

                $timeout(function() {
                    $ajax.$post(jsonData.updateSeenMessagesUrl,{order_alias: id}, function(r) {
                        $timeout($scope.init, 5000);
                    });
                }, 2000);


            })
        }

        $scope.showConfirm = function(msg, $event) {

            if (msg.title && msg.content) {
                $event.preventDefault();
                $('#ConfirmDialog').modal('show');
            }
        }

        /**
         * Processes send message
         * */
        $scope.sendMessage = function(msg, $event) {
            $event.target.disabled = true;
            msg.target = 1;
            $scope.loading = true;
            $('.uil-default-css').removeClass('hidden');
            $('.modal-backdrop-loading').removeClass('hidden');

            $ajax.$post(jsonData.sendMsgUrl, {title: msg.title, content: msg.content, order_alias: $scope.currentOrder.order_alias, type: msg.type}, function(r) {
                $event.target.disabled = false;

                $('#ConfirmDialog').modal('hide');

                if (r.error > 0) {
                    toastr.error(r.data[0]);
                    $('#ConfirmDialog').modal('hide');
                    $('.uil-default-css').addClass('hidden');
                    $('.modal-backdrop-loading').addClass('hidden');
                    return $location.updateUrl({type: ''});
                }

				msg.created_at  = r.data.sent_at;
				msg.full_name  = jsonData.staff.first_name + jsonData.staff.last_name;

                toastr.success(__('SendMsgSuccess'));
                $scope.messages.unshift(msg);

				$scope.Message = {
					type:'others'
				};

				angular.extend($scope.currentOrder, r.data);
				$(".msg-content").slideUp(500);

                $('.uil-default-css').addClass('hidden');
                $('.modal-backdrop-loading').addClass('hidden');

                $ajax.$post(jsonData.sendMsgMailUrl, {title: msg.title, content: msg.content, order_alias: $scope.currentOrder.order_alias, type: msg.type, newMessageId: r.data.newMessageId}, function(r) {
                    console.log('send mail OK');
                });
            })
        }

        /**
         * On user clicked to order
         * */
        $scope.showOrderMessages = function(order, $index, $event) {
			if ($scope.useEnableFlag && !order.$enabled) {
				return;
			}

            $scope.Message.title = '';
            $scope.Message.content = '';
            $scope.Message.type = 'others';
			$(".msg-content").slideUp(500);
            $scope.activeIndex = $index;
            $scope.currentOrder = order;

            $location.updateUrl({id: order.order_alias});
        }

        /**
         * Shows message template
         * */
        $scope.showTemplate = function(item, $event) {
            if ($($event.target).hasClass('disabledMsg')) {
                return;
            }

            var current = $('#msgTemplate' + item.id);

            $(".msg-content").each(function(){
                if (current[0] !== this) {
                    $(this).slideUp(500);
                }

            })

			current.slideToggle(500);
        }

        /**
         * ON user clicked select template
         * */
        $scope.selectTemplate = function(item) {
			msgFormatParams[senderName] = $scope.currentOrder.full_name;
			$scope.Message.title = App.formatMessage(item.title, msgFormatParams);
            $scope.Message.content = App.formatMessage(item.content, msgFormatParams);

			if (item.message_type == MessageTemplate.MSG_TYPE_RECEIVED_NOTIFY && item.type == MessageTemplate.TYPE_COMPANY) {
				 $scope.Message.header = jsonData.defaultMessages.received.header;
				 $scope.Message.type = 'received';
			} else if (item.message_type == MessageTemplate.MSG_TYPE_PREPARED_NOTIFY && item.type == MessageTemplate.TYPE_COMPANY ) {
				$scope.Message.type = 'prepared';
				$scope.Message.header = jsonData.defaultMessages.prepared.header;
			} else {
				$scope.Message.type = 'others';
				$scope.Message.header = jsonData.SentMsgConfirm;
			}
            toastr.success(__('MessageHasBeenSelected'));
			//$('#msgTemplate' + item.id).slideToggle(500);
        }

		$scope.getMsgClass = function(item) {
			if (!$scope.currentOrder) {
				return;
			}

			if ($scope.currentOrder.status == Order.STATUS_INVALID) {
				return 'disabledMsg invalidItem';
			}

			if ($scope.currentOrder.completed_flag == 1 &&
                (item.message_type == MessageTemplate.MSG_TYPE_RECEIVED_NOTIFY
                || item.message_type == MessageTemplate.MSG_TYPE_PREPARED_NOTIFY)
                && item.type == MessageTemplate.TYPE_COMPANY
            ) {
				return 'disabledMsg completeItem';
			}

			if (item.message_type == MessageTemplate.MSG_TYPE_RECEIVED_NOTIFY
                && item.type ==  MessageTemplate.TYPE_COMPANY
                && ($scope.currentOrder.sent_received_msg_at || $scope.currentOrder.sent_prepared_msg_at)) {
				return 'disabledMsg blockReceivedMsg';
			}

			return '';
		}

		$scope.getMessageType = function(type) {
			if (type == 0) {
				return  '受付通知';
			} else if (type == 1) {
				return '調剤完了';
			}

			return 'その他通知';
		}

		$scope.onMsgOrderChanged = function(value) {
            location.replace(jsonData.changeMsgOrderingUrl + '?type=' + value);
        }


        $scope.$on('$locationChangeSuccess', $scope.init);
    }

    App.registerController('MessagesController', MessagesController, ['$scope', '$ajax', '$location', '$timeout', '$q']);

})(window, window.jQuery, window.App);