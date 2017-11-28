(function (window, $, App) {

    var loggedOutCode = 401;

    function MainController($scope, $ajax, $timeout) {
        $scope.unreadMessageCount = 0;
        $scope.msgMenuText = '処方せんメッセージ管理';

        (function checkNewMessages() {
            $ajax.$post(msgNotifyCheckUrl, null, function (r) {
                if (r.err_code == loggedOutCode) {
                    return location.reload(true);
                }

                $scope.unreadMessageCount = r.data.UnreadMessageCount;
                $scope.msgMenuText = $scope.unreadMessageCount == 0 ? '処方せんメッセージ管理' : '処方せんメッセージ管理(' + $scope.unreadMessageCount + ')';
                $timeout(checkNewMessages, 5000);
            });
        })();
    }

    App.registerController('MainController', MainController, ['$scope', '$ajax', '$timeout']);

})(window, window.jQuery, window.App);