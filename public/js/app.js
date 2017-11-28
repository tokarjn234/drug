
!function (window, $) {
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    var App = angular.module('mainApp', ['ajax']);
    App.Models = {
        Order : {
            STATUS_RECEIVED: 0,
            STATUS_RECEIVED_NOTIFIED: 1,
            STATUS_PREPARED_NOTIFIED: 2,
            STATUS_INVALID: 3,
            STATUS_ALL: -1
        },
        MessageTemplate: {
             MSG_TYPE_RECEIVED_NOTIFY  : 0,
             MSG_TYPE_PREPARED_NOTIFY  : 1,
             MSG_TYPE_OTHER_NOTIFY  : 2,
             MSG_TYPE_NOTICE : 3,
             MSG_TYPE_OTHER : 4,
             TYPE_GROUP : 1,
             TYPE_COMPANY: 2,
             TYPE_STORE : 3
        },
        Certificate: {
             STATUS_NOT_DIVIDE : 0,
             STATUS_DIVIDED_TO_STORE : 1,
             STATUS_DIVIDED_TO_DEVICE : 2,
             STATUS_INACTIVE : 3
        }
    };


    window.Linq = window.Enumerable;

    App.registerController = function (name, controller, dependencies) {

        dependencies = dependencies || [];
        controller.$inject = dependencies;

        App.controller(name, controller);
    }

    App.run(function ($location, $window, $rootScope) {
        $window.addEventListener('message', function(e) {
            $rootScope.$apply(function() {
                $location.path("/abc");
                console.log($location.path());
            });
        });
    });

    App.run(['$location', function ($location) {
        $location.updateUrl = function (obj, overwrite) {
            if (overwrite) {
                $location.search(obj).replace();
                return;
            }

            if (typeof obj !== 'object') {
                throw new Error("Obj must be an object");
            }

            var current = $location.search();

            angular.forEach(obj, function (value, key) {
                if (value === '') {
                    delete current[key];
                } else {
                    current[key] = value;
                }

            });

            $location.search(current).replace();

        }

        $location.refresh = function () {
            return $location.updateUrl({ _: Date.now() });
        }

    }]);

    window.log = function() {
        console.log.apply(console, arguments);
    }

    window.error = function() {
        console.error.apply(console, arguments);
    };

    window.warn = function() {
        console.warn.apply(console, arguments);
    }

    App.filter("rawHtml", ['$sce', function ($sce) {
        return function (s) {
            s = !s ? '' : s.toString();
            return $sce.trustAsHtml(s);
        }
    }]);

    App.filter('truncate', function () {
        return function (value, wordwise, max, tail) {
            if (!value) return '';

            max = parseInt(max, 10);
            if (!max) return value;
            if (value.length <= max) return value;

            value = value.substr(0, max);
            if (wordwise) {
                var lastspace = value.lastIndexOf(' ');
                if (lastspace != -1) {
                    value = value.substr(0, lastspace);
                }
            }

            return value + (tail || ' …');
        };
    });

    App.filter('dateFormatHii', function () {
        return function (value) {
           if (!value) {
               return '';
           }

            var d = Date.fromMysqlTimestamp(value);
            var m = d.getMinutes();
            var h = d.getHours();

            if (h < 10) {
                h = '0' + h;
            }

            if (m < 10) {
                m = '0' + m;
            }

            return h + ':' + m;

        };
    });

    App.filter('dateFormatYmd', function () {
        return function (value) {
           if (!value) {
               return '';
           }

            var d = Date.fromMysqlTimestamp(value);
            var y = d.getFullYear();
            var m = d.getMonth() + 1;
            var dd = d.getDate();

            if (m < 10) {
                m = '0' + m;
            }

            if (dd < 10) {
                dd = '0' + dd;
            }

            return y + '/' + m + '/' + dd;

        };
    });

    App.filter('nl2br', function () {
        return function (value) {
           if (!value) {
               return '';
           }
		   
			return String.nl2br(value);

        };
    });

    App.alertDialog = function(title, content, callback) {
        var id = uuid(64);
        var template = '<div class="modal fade popup-cmn popview2" id="' +id+ '" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">'+
            '<div class="modal-dialog" role="document">'+
            '<div class="modal-content">'+
            '<div class="modal-body">'+
            '<div class="title-popup">' +title+'</div>'+content+'</div>'+
            '<div class="modal-footer">'+
            '<div>'+
            '<button type="button" class="btn btn-info okBtn" ng-click="restire()" >OK</button>'+
            '</div></div></div></div></div>';

        $('body').append(template);
        var $dialog = $('#' + id);
        $dialog.modal('show');

        $dialog.find('.okBtn').on('click', function() {
            if (typeof callback === 'function') {
                callback();
            }

            $dialog.modal('hide');

        });

        $dialog.on('hidden.bs.modal', function (e) {
            $dialog.remove();
        });

    }



    App.confirmDialog = function(title, content, callback) {
        var id = uuid(64);
        var template = '<div class="modal fade popup-cmn popview2" id="' +id+ '" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">'+
            '<div class="modal-dialog" role="document">'+
            '<div class="modal-content">'+
            '<div class="modal-body">'+
            '<div class="title-popup">' +title+'</div>'+content+'</div>'+
            '<div class="modal-footer">'+
            '<div>'+
            '<button type="button" class="btn btn-default cancelBtn" data-dismiss="modal">キャンセル</button>'+
            '<button type="button" class="btn btn-info okBtn" ng-click="restire()" >OK</button>'+
            '</div></div></div></div></div>';

        $('body').append(template);
        var $dialog = $('#' + id);
        $dialog.modal('show');

        $dialog.find('.okBtn').on('click', function() {
            if (typeof callback === 'function') {
                callback(true);
            }

            $dialog.modal('hide');

        });

        $dialog.find('.cancelBtn').on('click', function() {
            if (typeof callback === 'function') {
                callback(false);
            }

            $dialog.modal('hide');

        });

        $dialog.on('hidden.bs.modal', function (e) {
            $dialog.remove();
        });



    }

	String.nl2br = function(str, is_xhtml) {
		var breakTag = (is_xhtml || typeof is_xhtml === 'undefined') ? '<br />' : '<br>';
		return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1' + breakTag + '$2');
	}
	
    Date.fromMysqlTimestamp = function (s) {
        if (!s) {
            return new Date();
        }

        var t = s.split(/[- :]/);

        return new Date(t[0], t[1]-1, t[2], t[3], t[4], t[5]);
    }
	
	window.__ = function(s) {
		return window.Msg && Msg[s] !== undefined ? Msg[s] : s;
 	}

    window.uuid = function(n)
    {
        n = n || 10;
        var text = "";
        var possible = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789";

        for( var i=0; i < n; i++ )
            text += possible.charAt(Math.floor(Math.random() * possible.length));

        return text;
    }

	App.formatMessage = function(s, params) {
        if (typeof s  !== 'string') {
            return '';
        }

		params = params || {};
		
		angular.forEach(params, function(v, k) {
            v = v || '';
			s = s.replace(new RegExp('<%' + k + '%>', 'g'), v).replace(new RegExp('＜％' + k + '％＞', 'g'), v);			
		});
		
		return s;		
	}
	

    window.App = App;

    $(function() {
        $.datetimepicker.setLocale('ja');

        $('input.datepicker').datetimepicker({
            format:'Y年m月d日',
            formatDate:'Y/m/d',
            validateOnBlur: false,
            lang: 'ja',
            timepicker: false,
            onSelectDate:function(ct,$i){
                id = '#'+$i.attr('id')+'Time';
                $(id).attr('disabled', false);
                $(id).datetimepicker({
                    datepicker:false,
                    format:'H:i',
                    lang: 'ja'
                });
            }
        });

        $('input.datepicker').on("input", function() {
            id = '#'+$(this).attr('id')+'Time';
            if($(this).val() != '') {
                $(id).attr('disabled', false);
            }
            else {
                $(id).attr('disabled', 'disabled');
                $(id).val('');
            }
        });

        $('input.datepicker').each(function(){
            id = '#'+$(this).attr('id')+'Time';
            if($(this).val() != '')
                $(id).attr('disabled', false);
                $(id).datetimepicker({
                    datepicker:false,
                    format:'H:i',
                    lang: 'ja'
                });
        });

        $('input.monthpicker').datetimepicker({
            format:'Y年m月',
            validateOnBlur: false,
            lang: 'ja',
            timepicker: false,

            onChangeMonth:function(ct,$i){
                var month = ct.getMonth()+ 1;
                if(month < 10) {
                    month = '0' + month
                }
                 $i.val(ct.getFullYear()+'年'+ month + '月');
            }
        });
    })

}(window, window.jQuery);

        


