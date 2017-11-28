(function (window, $, App) {

    function AuthUpdateProfileController($scope, $timeout) {
        $scope.Staff = jsonData.staff || {};
        $scope.isConfirmMode = false;
        //$scope.jobCategory = jsonData.jobCategory;
        //console.log($scope.jobCategory);
        var allowedSubmit = jsonData.cancelable || false;

        $scope.confirm = function($event) {
            if (!allowedSubmit) {
                $event.preventDefault();
                $scope.isConfirmMode = true;
            }

        };

        $scope.save = function() {
            allowedSubmit = true;
            $timeout(function() {
                $('#profileForm').submit();
            })

        };

        $.fn.onlyKana = function(config) {
            var defaults = {
            };
            var options = $.extend(defaults, config);
            return this.each(function(){
                $(this).bind('blur', function(){
                    $(this).val($(this).val().replace(/[^ア-ン゛゜ァ-ォャ-ョーｱ-ﾝﾞﾟｦｧ-ｫｬ-ｮｯｰ]/g, ''));
                });
            });
        };

        $('.katakana').onlyKana();
    }

    function AuthCertificatesIssueController($scope, $timeout, $ajax) {
        $scope.downloadCert = function($event) {
            $('#infoDialog').modal('show');
            
            $('#submitBtn').attr('disabled', true);
            $('input[name="cert_name"]').attr('readonly', true);

            $ajax.$get(jsonData.securedLoginUrl);

            $timeout(function() {
                $('#submitBtn').attr('disabled', false);
            }, 3000);
        };

        $scope.goToSecuredLogin = function() {
            location.replace(jsonData.securedLoginUrl);
        }
    }

    function AuthChangePasswordController($scope) {
        var $password = $('#password');
        var $icon = $('#eye-icon');
        var $error = $('#errorMsg');
        var showPassword = function(show) {
            $password.attr('type', show ? 'text': 'password');
            $icon.attr('class', show ? 'fa fa-eye-slash' : 'fa fa-eye');
        };

        $(function() {
            $('#form').submit(function(e) {
                var password = $password.val();
                var regExp = /^(?=.*[0-9])(?=.*[a-zA-Z])([a-zA-Z0-9]+)$/;

                if (password.length < 6 || !regExp.test(password)) {
                    $error.css('color', 'red');
                    e.preventDefault();
                }


            });

            $('#showPassword').on(
                {
                    mousedown: function() {showPassword(true);},
                    mouseup: function() {showPassword(false);},
                    mouseleave: function() {showPassword(false);}
                }
            );
        })

    }

    App.registerController('AuthUpdateProfileController', AuthUpdateProfileController, ['$scope', '$timeout']);
    App.registerController('AuthChangePasswordController', AuthChangePasswordController, ['$scope']);
    App.registerController('AuthCertificatesIssueController', AuthCertificatesIssueController, ['$scope', '$timeout', '$ajax']);

})(window, window.jQuery, window.App);