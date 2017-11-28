(function (window, $, App) {
	var jsonData = window.jsonData;

    function SettingsEditController($scope) {
    	jsonData.message.message_type = jsonData.message.message_type == null ? ''  : jsonData.message.message_type.toString()
    	$scope.Message = jsonData.message ;
    	$scope.EditMessage = jsonData.messageEdit;
    	var $txtContent = $('[name="txtContent"]');

  		$scope.saveMessage = function(status) {
            //console.log(jsonData.messageEdit);
			$txtContent.val($.trim($txtContent.val()));
			$('#status').val(status);
	    	if (jsonData.messageEdit >= 3  && status == 1 &&  jsonData.message.status == 0 && jsonData.message.type == 3) {
				$('#errorModal').modal('show');

			} else {
				$('#submitBtn').click();
			}
		}
    }

    function SettingsAddMessageController($scope) {
		var $txtContent = $('[name="txtContent"]');

		$scope.init = function() {
			$scope.checkSave = false;
		};

    	$scope.saveMessage = function(status) {
			if($scope.settingForm.$valid) {
				$scope.checkSave = true;
			}

			$txtContent.val($.trim($txtContent.val()));
			$('#status').val(status);
			if (jsonData.messageCount >= 3 && status == 1) {

    			$('#errorModal').modal('show');
    		} else {
				$('#submitBtn').click();
    			
    		}    		
    	}
    }
    	

    App.registerController('SettingsEditController', SettingsEditController, ['$scope']);
 App.registerController('SettingsAddMessageController', SettingsAddMessageController, ['$scope']);
})(window, window.jQuery, window.App);