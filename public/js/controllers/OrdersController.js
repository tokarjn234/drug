(function (window, $, App) {
    function OrdersIndexController ($scope, $ajax, $timeout) {
        var jsonData = window.jsonData;
		var senderName = '送信者名';
		var storeName = '店舗名';
		var msgFormatParams = {};
		msgFormatParams[storeName] = jsonData.storeName;
		
        var Order = $scope.Order = App.Models.Order;
        $scope.orders = jsonData.orders;
        $scope.ordersNotPagination = jsonData.ordersNotPagination;
        $scope.messageTemplates = jsonData.messageTemplates;
		$scope.formatMsg = function(s) {
			if (!$scope.currentOrder) {
				return '' ;
			}
			
			msgFormatParams[senderName] =  $scope.currentOrder.first_name + $scope.currentOrder.last_name;
			return App.formatMessage(s, msgFormatParams);			
		};

        $scope.init = function() {
            $scope.loading = false;
        };

        /**
         * Shows delete popup
         * */
        $scope.showDeletePopup = function(order, $event) {
            $scope.showDeleteReasonError = false;
            $event.preventDefault();
            $scope.currentOrder = order;
            $scope.tmpDeleteReason = '';

            $('#DeletedConfirmDialog').modal('show');

            $timeout(function() {

                $('#DeletedConfirmDialog textarea').focus();
            }, 500)

        }

        /**
         * Deletes an order
         * */
        $scope.setDeletedOrder = function(order, reason, $event) {
            if ($.trim(reason) == '') {
                $scope.showDeleteReasonError = true;
                return;
            }
            $scope.showDeleteReasonError = false;
            $event.target.disabled = true;
            $ajax.$post(jsonData.deleteOrderUrl, {order_alias: order.alias, reason: reason}, function(r) {
                $('#DeletedConfirmDialog').modal('hide');
				$event.target.disabled =  false;
				
				if (r.error == 0) {
					order.status = Order.STATUS_INVALID;
					order.delete_reason = reason;
				} else {
					toastr.error(r.data[0]);
				}
             
            });
        }

        /**
         * Shows complete popup
         * */
        $scope.showCompletePopup = function(order, $event) {
            $event.preventDefault();

            if (order.completed_flag == 1) {
                $ajax.$post(jsonData.completeOrderUrl, {order_alias: order.alias, completed: 0}, function (r) {
                    if (r.error == 0) {
						angular.extend(order, r.data);					
					} else {
						toastr.error(r.data[0]);
					}
                });
                return;
            }


            $scope.currentOrder = order;
            $('#CompletedConfirmDialog').modal('show');
        }

        /**
         * Sets compelete order
         * */
        $scope.setCompleteOrder = function(order, $event) {
            $event.target.disabled = true;

            $ajax.$post(jsonData.completeOrderUrl, {order_alias: order.alias, completed: 1}, function (r) {
				
				
				if (r.error != 0) {
					toastr.error(r.data[0]);
				} else {
					angular.extend(order, r.data);	
				}
				
				$('#CompletedConfirmDialog').modal('hide');
				$event.target.disabled = false;
         
            })
        }

        $scope.getReceivedMsgClass = function(order) {
            if (order.sent_received_msg_at || order.status == Order.STATUS_INVALID || order.completed_flag == 1)  {
                return '';
            }

            return 'border-red';
        }

        $scope.getReceivedMsgBtnClass = function(order) {
            return {disabled: order.status == Order.STATUS_INVALID || order.completed_flag == 1};
        }

        $scope.getPreparedMsgClass = function(order) {
            if (order.sent_prepared_msg_at || order.status == Order.STATUS_INVALID || order.completed_flag == 1)  {
                return '';
            }

            return 'border-red';
        }

        $scope.getPreparedMsgBtnClass = function(order) {
            return {disabled: order.status == Order.STATUS_INVALID || order.completed_flag == 1};
        }

        $scope.getRowClass = function(order) {
            return {invalidItem: order.status == Order.STATUS_INVALID, completedItem: order.completed_flag == 1};
        }

        $scope.isFinishedItem = function(order) {
            return order.status == Order.STATUS_INVALID || order.completed_flag == 1;
        }

        /**
         * On user clicked send received order messages
         * */
        $scope.sendReceivedOrderMsg = function(order, $event) {
            if ($($event.target).hasClass('disabled')) {
                return;
            }

			msgFormatParams[senderName] = order.full_name;
            $scope.currentOrder = order;
            $scope.sendMsgType = 'received';

            $("#SendMsgDialog").modal("show");

        }

        /**
         * On user clicked send prepared order messages
         * */
        $scope.sendPreparedOrderMsg = function (order, $event) {
            if ($($event.target).hasClass('disabled')) {
                return;
            }

            $scope.currentOrder = order;
            $scope.sendMsgType = 'prepared';

            $("#SendMsgDialog").modal("show");
        }

        /**
         * Processes sending message
         * @param order
         * @param type ('received'|'prepared')
         * */
        $scope.sendMessage = function(order, message, $event) {
            $scope.loading = true;
            $event.target.disabled = true;
            $('.uil-default-css').removeClass('hidden');
            $('.modal-backdrop-loading').removeClass('hidden');

            $ajax.$post( jsonData.sendMsgUrl, {user_alias: order.user_alias, order_alias: order.alias, title: $scope.formatMsg(message.title), content: $scope.formatMsg(message.content), type: $scope.sendMsgType}, function(r) {
                $("#SendMsgDialog").modal('hide');

                $event.target.disabled = false;
				
				if (r.error == 0) {
					angular.extend(order, r.data);
					toastr.success(__('SendMsgSuccess'));
				} else {
					toastr.error(r.data[0]);
				}

                $('.uil-default-css').addClass('hidden');
                $('.modal-backdrop-loading').addClass('hidden');
				
            })
        }

        $scope.editAndSendMsg = function(order, status, $event) {
            if ($($event.target).hasClass('disabled')) {
                return;
            }

            angular.forEach($scope.ordersNotPagination, function(alias, key) {
                if (alias == order.alias) {
                    var pagination = Math.ceil(($scope.ordersNotPagination.length - key)/10);
                    location.href = jsonData.msgUrl + '&page=' + pagination + '#?id=' + order.alias + '&type=' + status;
                }
            });
        };

        $scope.viewPhoto = function(order) {

            window.open(jsonData.photoUrl + '/' + order.alias);
        }


        $scope.search = function($event) {
            $event.preventDefault();
            var isFormNotEmpty = true;

            $($event.target).find('input').each(function() {
                if (!$.trim(this.value)) {
                    $(this).removeAttr('name');
                    isFormNotEmpty = false;
                }
            });

            $event.target.submit();

        }

        $scope.clearSearch = function() {
            $scope.clearSearchConditions = true;

            $timeout(function() {
                $('#searchForm').submit();
            });
        }

    }

    App.registerController('OrdersIndexController', OrdersIndexController, ['$scope', '$ajax', '$timeout']);

})(window, window.jQuery, window.App);