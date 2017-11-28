(function (window, $, App) {
    var jsonData = window.jsonData;

    function CompaniesIndexController() {
        var startMonthPicker = $('input.monthpicker-companies'),
            endMonthPicker = $('input.monthpicker-companies-end'),
            startDatePicker = $('input.datepicker-companies'),
            endDatePicker = $('input.datepicker-companies-end');

        startDatePicker.attr("placeholder", getDefaultDate(1));
        endDatePicker.attr("placeholder", getDefaultDate(0));
        startMonthPicker.prop('disabled', true);
        endMonthPicker.prop('disabled', true);

        $(".optionsDateMonth").change(function () {
            if ($(this).val() == 1) {
                endMonthPicker.attr("placeholder", getDefaultDate(3));
                startMonthPicker.attr("placeholder", getDefaultDate(2));
                startDatePicker.attr("placeholder", "----年--月--日");
                startDatePicker.val('');
                endDatePicker.attr("placeholder", "----年--月--日");
                endDatePicker.val('');
                startDatePicker.prop('disabled', true);
                endDatePicker.prop('disabled', true);
                startMonthPicker.prop('disabled', false);
                endMonthPicker.prop('disabled', false);
            }
            else {
                endMonthPicker.attr("placeholder", "----年--月");
                endMonthPicker.val('');
                startMonthPicker.attr("placeholder", "----年--月");
                startMonthPicker.val('');
                endDatePicker.attr("placeholder", getDefaultDate(0));
                startDatePicker.attr("placeholder", getDefaultDate(1));
                startDatePicker.prop('disabled', false);
                endDatePicker.prop('disabled', false);
                startMonthPicker.prop('disabled', true);
                endMonthPicker.prop('disabled', true);
            }
        });

        $("#submit-csv").click(function (e) {
            var radio = $('input:radio[name=optionsRadios]:checked').val();
            if (radio == 0) {
                var startD = startDatePicker.val() ? convertDate(startDatePicker.val()) : convertDate(startDatePicker.attr("placeholder")),
                    endD = endDatePicker.val() ? convertDate(endDatePicker.val()) : convertDate(endDatePicker.attr("placeholder"));
                if (dayDiff(startD, endD) > 31) {
                    $(".day-condition-csv").addClass('validate-csv');
                    $(".month-condition-csv").removeClass('validate-csv');
                    e.preventDefault();
                }
                else {
                    $(".day-condition-csv").removeClass('validate-csv');
                }
            }
            else {
                var startM = startMonthPicker.val() ? convertDate(startMonthPicker.val() + '01日') : convertDate(startMonthPicker.attr("placeholder") + '01日'),
                    endM = endMonthPicker.val() ? convertDate(endMonthPicker.val() + '01日') : convertDate(endMonthPicker.attr("placeholder") + '01日');
                if (monthDiff(startM, endM) > 2) {
                    $(".month-condition-csv").addClass('validate-csv');
                    $(".day-condition-csv").removeClass('validate-csv');
                    e.preventDefault();
                }
                else {
                    $(".month-condition-csv").removeClass('validate-csv');
                }
            }

        });

        function dayDiff(first, second) {
            first = new Date(first);
            second = new Date(second);
            return Math.round((second - first) / (1000 * 60 * 60 * 24));
        }

        function monthDiff(first, second) {
            first = new Date(first);
            second = new Date(second);
            return Math.round(second.getMonth() - first.getMonth()
                + (12 * (second.getFullYear() - first.getFullYear())));
        }

        $('.disabled-input').on('keydown', function (e) {
            var key = e.charCode || e.keyCode;
            if (key == 122 || key == 27) {
            }
            else
                e.preventDefault();
        });

        function getDefaultDate(type) {
            var d = new Date();
            var month = d.getMonth() + 1;
            var day = d.getDate();
            var year = d.getFullYear();

            d.setMonth(d.getMonth() - 2);
            var monthStartMonth = d.getMonth() + 1;
            var yearStartMonth = d.getFullYear();

            switch (type) {
                case 1: //startDate
                    return year + '年' + (month < 10 ? '0' : '') + month + '月' + '01日';
                    break;
                case 2: //startMonth
                    return yearStartMonth + '年' + (monthStartMonth < 10 ? '0' : '') + monthStartMonth + '月';
                    break;
                case 3: //endMonth
                    return year + '年' + (month < 10 ? '0' : '') + month + '月';
                    break;
                default: //endDate
                    return year + '年' + (month < 10 ? '0' : '') + month + '月' + (day < 10 ? '0' : '') + day + '日';
                    break;
            }
        }

        //check-all
        $("#select-all").change(function () {
            $(".select-store").prop('checked', $(this).prop("checked"));
        });

        $('#menu-current-view').click(function () {
            $('#tab-current-view a').trigger('click');
        });

        $('.statistic-view .year-month').each(function () {
            var colSpan = 1;
            while ($(this).text() == $(this).next().text()) {
                $(this).next().remove();
                colSpan++;
            }
            $(this).attr('colSpan', colSpan);
        });

        var cities = JSON.parse($('#list-cites').text());
        var city1 = $('#search-city1').text();

        var province = $("#province_list");
        province.change(function () {
            var parent = $(this).val(); //get option value from parent
            list(cities[parent]);
        });

        if (cities) {
            if (cities[province.val()]) {
                list(cities[province.val()]);
            }
        }

        function list(array_list) {
            $("#city1_list").html(""); //reset child options
            if (array_list) {
                $.each(array_list, function (i, val) {
                    $("#city1_list").append("<option value=\"" + i + "\">" + val + "</option>");
                    if (i == city1) {
                        $("#city1_list").val(city1);
                    }
                });
            }
        }

        //Companies Datepicker

        function convertDate(date) {
            if (!date) {
                return false;
            }
            var dateConvert = date.replace(/年|月/g, "-").replace(/日/g, ""),
                comp = dateConvert.split('-'),
                m = parseInt(comp[1], 10),
                d = parseInt(comp[2], 10),
                y = parseInt(comp[0], 10),
                date = new Date(y, m - 1, d);

            if (date.getFullYear() == y && date.getMonth() + 1 == m && date.getDate() == d) {
                return dateConvert;
            }
        }
    }

    function CompanyStoreIndexController($scope, $timeout, $ajax) {
        var jsonData = window.jsonData;
        $scope.stores = jsonData.stores;
        $scope.changeToPublished = 0;
        $scope.InvalidTypeImage = false;
        $scope.AcceptSave = false;
        $scope.nameCsv = '';

        var Store = $scope.Store = App.Models.Store;
        var allowedType = ["image/png", "image/jpg", "image/jpeg", "image/bmp"];
        var $mimes = ['application/vnd.ms-excel', 'text/plain', 'text/csv', 'text/tsv'];

        $scope.changePublic = function (store, $event, n) {
            $event.target.disabled = false;
            $scope.changeToPublished = n == 1 ? 1 : 0;
            $('#PublicConfirmDialog').modal('show');
            $scope.currentStore = store;
        };

        $scope.changeIsPublished = function (currentStore, $event) {
            $ajax.$post(jsonData.publicStoreUrl, {
                store_id: currentStore.id,
                is_public: currentStore.is_published
            }, function (r) {
                $('#PublicConfirmDialog').modal('hide');
                $event.target.disabled = false;

                if (r.error == 0) {
                    if (currentStore.is_published == 1) {
                        currentStore.is_published = 0;
                    }
                    else {
                        currentStore.is_published = 1;
                    }
                    toastr.success(__('Successfully'));
                } else {
                    toastr.error(__('Unsuccessfully'));
                }
            });
        };

        $scope.selectImage = '';

        $scope.changePhoto = function (store, $event) {
            $scope.InvalidTypeImage = false;
            $scope.AcceptSave = false;
            $('#PhotoConfirmDialog').modal('show');
            $scope.selectImage = '';
            $scope.currentStore = store;
        };

        $scope.CsvSelect = function (elm) {
            var file = elm.target.files[0]; //FileList object
            $scope.nameCsv = '';
            if ($mimes.indexOf(file.type) != -1) {
                $scope.nameCsv = file.name;
            }
            $scope.$apply();
        };

        $scope.imageChange = function (elm) {
            var file = elm.target.files[0]; //FileList object
            if (allowedType.indexOf(file.type) == -1) {
                $scope.InvalidTypeImage = true;
                $scope.AcceptSave = false;
            }
            else {
                $scope.file = file;
                var reader = new FileReader();
                reader.onload = $scope.imageIsLoaded;
                reader.readAsDataURL(file);
                $scope.InvalidTypeImage = false;
                $scope.AcceptSave = true;
            }
            $scope.$apply();
        };

        $scope.imageIsLoaded = function (e) {
            $scope.$apply(function () {
                $scope.selectImage = e.target.result;
            });
        };

        $scope.acceptChangePhoto = function (currentStore, $event) {
            $event.target.disabled = false;
            $ajax.$upload(jsonData.changePhotoUrl, {
                file: $scope.file,
                store_id: currentStore.id,
                photo_url: currentStore.photo_url,
                store_alias: currentStore.alias
            }, function (r) {
                var result = $.parseJSON(r);

                if (result.error == 0) {
                    currentStore.photo_url = result.data;
                    $('#PhotoConfirmDialog').modal('hide');
                    toastr.success(__('Successfully'));
                } else {
                    toastr.error(__('Unsuccessfully'));
                }
            });
        };

        $scope.deletePhoto = function (currentStore, $event) {
            if (!$scope.selectImage) {
                $ajax.$post(jsonData.deletePhotoUrl, {
                    store_id: currentStore.id,
                    photo_url: currentStore.photo_url
                }, function (r) {
                    $event.target.disabled = false;

                    if (r.error == 0) {
                        toastr.success(__('Successfully'));
                    } else {
                        toastr.error(__('Unsuccessfully'));
                    }
                });
            }
            $scope.selectImage = '';
            currentStore.photo_url = '';
        };
    }

    function StoresController($scope, $timeout, $ajax) {

        $scope.provinces = jsonData.provinces;
        $scope.cities = jsonData.cities;

        var $province = $('#province');
        var $city = $('#city');
        //console.log(jsonData);

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

        $scope.deleteStore = function ($event) {
            $event.preventDefault();
            $('#ConfirmDialogDelete').modal('show');
        };

        $scope.deleteAccept = function ($event) {
            $event.preventDefault();
            $ajax.$post(jsonData.postDeleteStoreUrl, {store_id: jsonData.store.id}, function (r) {
                if (r.error == 0) {
                    $('#ConfirmDialogDelete').modal('hide');
                    window.location.href = jsonData.getStoreIndexUrl;
                } else {

                }
            });
        };


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


    App.registerController('CompanyStoreIndexController', CompanyStoreIndexController, ['$scope', '$timeout', '$ajax']);
    App.registerController('CompaniesIndexController', CompaniesIndexController);
    App.registerController('StoresShowController', StoresShowController);
    App.registerController('StoresController', StoresController, ['$scope', '$timeout', '$ajax']);

})(window, window.jQuery, window.App);