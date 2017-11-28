/**
 * Ajax service with loading indicator (jQuery ajax wrapper)
 * */

 angular.module('ajax', []).service('$ajax', ['$rootScope', function($rootScope) {
    var $this = this;
    this.showLoading = false;

    this.showLoadingBar = function() {
        this.showLoading = true;
    }

    /**
     * jQuery ajax wrapper
     * @param options - Just like jQuery.ajax options
     * @created 12/10/14 - 9:02 AM
     * */
    this.request = function(options) {


        var $showLoading = this.showLoading && (typeof $.showLoading === 'function');

        if ($showLoading) {
            $.showLoading(true);
        }
     
        $.ajax({
            url: options.url,
            type: options.method,
            timeout: options.timeout || 20000,
            data : options.data,
            cache: false,
            success: function(data) {

                if ($showLoading) {
                    $.showLoading(false);
                    $this.showLoading = false;
                }

                if (typeof options.success == 'function') {
                    var jsonData = null;

                    if (typeof data === 'object') {
                        jsonData = data;
                    } else {
                        try {
                            jsonData = JSON.parse(data);
                        } catch (e) { }
                    }                    

                    if (!jsonData) {
                        if (data.indexOf('id="UserName"') != -1) {
                            
                            return location.replace(window.loginUrl);
                        }
                        
                    } else {
                        $rootScope.$apply(function() {
                            options.success(jsonData, 'json');
                        });
                    }
                }
            },
            error: function(x, t, m) {

                if ($showLoading) {
                    $.showLoading(false);
                    $this.showLoading = false;
                }

                if (t === 'timeout') {
                    alert('Request timed out');
                    return;
                }

                if (typeof options.error == 'function') {
                    options.error(x);
                } else {
                    if (x.status == 403) {
                        location.reload();
                        //alert('You have been logged out!! Please try to reload');
                    } else {
                        console.error('Invalid response data!');
                    }
                }
            }
        });

    }

    this.$get = function(url, data, success, error) {
        this.request({
            url: url, data: data, success: success, error: error, method: 'GET'
        });
    }

    this.$post = function(url, data, success, error) {
        this.request({
            url: url, data: data, success: success, error: error, method: 'POST'
        });
    }

    this.$save = function(url, data, success, error) {
        this.request({
            url: url, data: data, success: success, error: error, method: 'POST'
        });
    }

    this.$update = function(url, data, success, error) {
        this.request({
            url: url, data: data, success: success, error: error, method: 'PUT'
        });
    }

    this.$query = function(url, data, success, error) {
        this.request({
            url: url, data: data, success: success, error: error, method: 'GET'
        });
    }

    this.$delete = function(url, data, success, error) {
        this.request({
            url: url, data: data, success: success, error: error, method: 'DELETE'
        });
    }

    this.$upload = function(url, formData, done, progress) {
        var xhr = new XMLHttpRequest();
        var data = new FormData();

        angular.forEach(formData, function(v, k) {
            data.append(k, v);
        });


        if (typeof progress == 'function') {
            xhr.upload.addEventListener('progress', function (e) {
                progress.call(xhr, parseInt(e.loaded / e.total * 100));
            });
        }

        if (typeof done == 'function') {
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4) {
                    $rootScope.$apply(function() {
                        done.call(xhr, xhr.responseText);
                    });

                }
            }
        }

        xhr.open('POST', url, true);
        xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
        xhr.setRequestHeader("X-CSRF-TOKEN", $('meta[name="csrf-token"]').attr('content'));
        xhr.send(data);
    }
}]);
