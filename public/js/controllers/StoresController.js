(function (window, $, App) {
    var jsonData = window.jsonData;

    function StoresController($scope, $timeout, $ajax) {

        $scope.provinces = jsonData.provinces;
        $scope.cities = jsonData.cities;

        var $province = $('#province');
        var $city = $('#city');

        $timeout(function () {

            if (typeof jsonData.allowAdd != 'undefined') {
                if (jsonData.allowAdd.province != '') {
                    $province.append('<option value="' + jsonData.allowAdd.province + '" selected>' + jsonData.allowAdd.province + '</option>');
                    //$province.select2('val', '123');
                }
            }
            $province.select2({
                tags: true
            });
            //$province.select2('val','123');
            if (typeof jsonData.allowAdd != 'undefined') {
                if (jsonData.allowAdd.city != '') {
                    $city.append('<option value="' + jsonData.allowAdd.city + '" selected>' + jsonData.allowAdd.city + '</option>');
                }
            }
            $city.select2({tags: true});
        });

        var submitEnable = false;

        $scope.confirm = function ($event) {

            $scope.provinceErrorMsg = !$province.val();
            $scope.cityErrorMsg = !$city.val();

            if ($scope.provinceErrorMsg || $scope.cityErrorMsg) {
                return $event.preventDefault();
            }

            if (!submitEnable) {
                $('#ConfirmDialog').modal('show');
                $event.preventDefault();
            }

        }


        $scope.save = function () {
            submitEnable = true;
            $timeout(function () {
                $('#form-store').submit();

            })

        }


        $(function () {
            $city.on("change", function (e) {
                $scope.$apply(function () {
                    $scope.cityErrorMsg = !$city.val();
                });
            });


            $province.on("change", function (e) {
                $scope.$apply(function () {
                    $scope.provinceErrorMsg = !$province.val();

                });

                $ajax.$get(jsonData.getCitiesListUrl, {province: $province.val()}, function (r) {
                    $scope.cities = r.data;
                    $city.html('');
                    $scope.cities = r.data;
                    var city = r.data;
                    for (i in city) {
                        $city.append('<option value="' + city[i].name + '" selected>' + city[i].name + '</option>');
                    }
                    $timeout(function () {
                        $city.select2('destroy');
                        $city.select2({tags: true});

                        $scope.cityErrorMsg = !$city.val();
                    });
                });
            });

            function initialize() {
                var $map = document.getElementById('map');
                if (!$map) {
                    return;
                }
                //35.6735408,139.5703048
                var currentLat = parseFloat($('#map_coordinates_lat').val() || 35.6735408);
                var currentLng = parseFloat($('#map_coordinates_long').val() || 139.5703048);

                var myLatLng = {lat: currentLat, lng: currentLng};
                var map = new google.maps.Map($map, {
                    center: myLatLng,
                    zoom: 13,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                });

                var marker = new google.maps.Marker({
                    position: myLatLng,
                    draggable: true,
                    animation: google.maps.Animation.DROP,
                    map: map
                });

                $('#address').on('keydown', function (e) {
                    if (e.keyCode == 13) {
                        e.preventDefault();
                    }
                });
                // Create the autocomplete object, restricting the search
                // to geographical location types.
                var autocomplete = new google.maps.places.Autocomplete(
                    /** @type {HTMLInputElement} */(document.getElementById('address')),
                    {types: ['geocode']});
                // When the user selects an address from the dropdown,
                // populate the address fields in the form.
                google.maps.event.addListener(autocomplete, 'place_changed', function () {
                    /// var bounds = new google.maps.LatLngBounds();
                    var place = autocomplete.getPlace();
                    if (place.geometry) {
                        var latlng = {lat: place.geometry.location.lat(), lng: place.geometry.location.lng()};
                        $('#map_coordinates_lat').val(latlng.lat);
                        $('#map_coordinates_long').val(latlng.lng);
                        marker.setPosition(latlng);
                        map.setCenter(latlng);
                    } else {
                        log(place)
                    }

                    // bounds.extend(autocomplete.getPlace().geometry.location);
                    //map.fitBounds(bounds);
                });

                google.maps.event.addListener(marker, 'dragend', function (e) {

                    $('#map_coordinates_lat').val(marker.position.lat);
                    $('#map_coordinates_long').val(marker.position.lng);
                });
            }

            google.maps.event.addDomListener(window, 'load', initialize);

        });
    }

    function StoresShowController() {
        function initialize() {
            //35.6735408,139.5703048
            var currentLat = parseFloat($('#map_coordinates_lat').val() || 35.6735408);
            var currentLng = parseFloat($('#map_coordinates_long').val() || 139.5703048);

            var myLatLng = {lat: currentLat, lng: currentLng};
            var map = new google.maps.Map(document.getElementById('map'), {
                center: myLatLng,
                zoom: 13,
                mapTypeId: google.maps.MapTypeId.ROADMAP
            });

            var marker = new google.maps.Marker({
                position: myLatLng,

                animation: google.maps.Animation.DROP,
                map: map

            });

        }

        google.maps.event.addDomListener(window, 'load', initialize);
    }

    App.registerController('StoresController', StoresController, ['$scope', '$timeout', '$ajax']);
    App.registerController('StoresShowController', StoresShowController);

})(window, window.jQuery, window.App);