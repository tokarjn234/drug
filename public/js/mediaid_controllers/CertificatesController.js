(function (window, $, App) {

    var jsonData = window.jsonData;

    function CertificatesController($scope, $ajax, $timeout) {
        $scope.totalCerCount = jsonData.countData.totalCerCount;
        $scope.dividedToStoreCount = jsonData.countData.dividedToStoreCount;
        $scope.dividedToDeviceCount = jsonData.countData.dividedToDeviceCount;
        $scope.availableCount = jsonData.countData.availableCount;
        $scope.inactiveCount = jsonData.countData.inactiveCount;
        $scope.paginate = jsonData.paginate;

        $scope.certificates = jsonData.certificates || [];
        $scope.status = App.Models.Certificate;

        $scope.getCheckedCertificate = function() {
            return Linq.From( $scope.certificates).Where('i=>i.$checked===true').ToArray();
        }

        $scope.restoreCertificate = function(item) {
            App.confirmDialog('この証明書を「未割当」に戻しますか？', 'この操作をすると既に割り当てられた店舗で端末証明書の導入はできなくなります。', function(ok) {
                if (ok) {
                   $ajax.$post(jsonData.restoreCertificateUrl, {alias: item.alias}, function(r) {
                        if (r.error == 0) {
                            item.name = '-';
                            item.store_name= '-';
                            item.store_id = '-';
                            item.issued_to_store_at = '-';
                            item.status = App.Models.Certificate.STATUS_NOT_DIVIDE;
                            item.$status = r.data.$status;
                            item.updated_at = r.data.updated_at;
                            $scope.totalCerCount = r.data.count.totalCerCount;
                            $scope.dividedToStoreCount =  r.data.count.dividedToStoreCount;
                            $scope.dividedToDeviceCount =  r.data.count.dividedToDeviceCount;
                            $scope.availableCount =  r.data.count.availableCount;
                            $scope.inactiveCount =  r.data.count.inactiveCount;

                        }
                   });
                }
            });
        }

       $scope.disableCertificate = function(item) {
           App.confirmDialog('この証明書を「無効」にしますか？', 'この操作をすると導入された端末でスマホ処方めーるの機能が利用できなくなります。', function(ok) {
               if (ok) {
                   $ajax.$post(jsonData.disableCertificateUrl, {alias: item.alias}, function(r) {
                       if (r.error == 0) {
                           item.status = App.Models.Certificate.STATUS_INACTIVE;
                           item.$status = r.data.$status;
                           item.updated_at = r.data.updated_at;
                           $scope.totalCerCount = r.data.count.totalCerCount;
                           $scope.dividedToStoreCount =  r.data.count.dividedToStoreCount;
                           $scope.dividedToDeviceCount =  r.data.count.dividedToDeviceCount;
                           $scope.availableCount =  r.data.count.availableCount;
                           $scope.inactiveCount =  r.data.count.inactiveCount;
                       }
                   });
               }
           });
       }
    }

    function IssueCertificatesController($scope, $ajax, $timeout) {

        $scope.stores = jsonData.stores;
        $scope.currentStore = jsonData.firstStoreAlias;

        $timeout(function() {
            $('#stores').select2();
        });

        $scope.save = function() {
            var storeName = $scope.stores[$scope.currentStore];

            App.confirmDialog('以下の内容で割当しますか？', '割当数：' + jsonData.certificateCount + '件<br>割当先店舗：' + storeName, function(ok) {
                if (ok) {
                    $('#issueForm').submit();
                }
            })
        }
    }

    function InActiveCertificatesController($scope, $ajax, $timeout) {

        $scope.disableMediaidCertificate = function(cert) {
           App.confirmDialog('この証明書を「無効」にしますか？', 'この操作をすると導入された端末でスマホ処方めーるの機能が利用できなくなります。', function(ok) {
               if (ok) {
                   var inactiveUrl = $('#disableCertUrl').val();
                   $ajax.$post(inactiveUrl, {alias: cert}, function(r) {
                        if (r.error == 0) {
                           window.location.reload();
                        }
                   });
               }
           });
       }
    }

    App.registerController('CertificatesController', CertificatesController, ['$scope', '$ajax', '$timeout']);
    App.registerController('IssueCertificatesController', IssueCertificatesController, ['$scope', '$ajax', '$timeout']);
    App.registerController('InActiveCertificatesController', InActiveCertificatesController, ['$scope', '$ajax', '$timeout']);

})(window, window.jQuery, window.App);