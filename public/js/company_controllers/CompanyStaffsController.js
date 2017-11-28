(function (window, $, App) {


    function CompanyStaffsController($scope, $ajax, $timeout) {
        $scope.removeStaff = function(alias, name) {
            App.confirmDialog(name + 'アカウントを削除しますか？', '' , function(ok) {
               if (ok) {
                    $('#' + alias).submit();
               }
            });
        }

        $scope.lockAccount = function(alias, name) {
            App.confirmDialog(name + 'アカウントをロックしますか？', '' , function(ok) {
               if (ok) {
                    $('#' + alias).submit();
               }
            });
        }

        $scope.changePass = function(alias, name) {
            App.confirmDialog(name + 'パスワードをリセットしますか？', '' , function(ok) {
               if (ok) {
                    $('#' + alias).submit();
               }
            });
        }
    }

    App.registerController('CompanyStaffsController', CompanyStaffsController, ['$scope', '$ajax', '$timeout']);

})(window, window.jQuery, window.App);