<?php
$working_time = json_decode($store['working_time'], true);
?>

@extends('layouts.company')

@section('title', '店舗情報管理')

@section('content')
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places"></script>
    <style type="text/css">
        #map {
            width: 100%;
            height: 300px;
        }
    </style>


    <div id="page-wrapper" class="infor-mannager" ng-controller="StoresController">
        {!! Form::open(array('id' => 'form-store','ng-submit' => 'confirm($event)', 'name' => 'form', 'action' => (array('Company\StoresController@postUpdate')), 'method' => 'post', 'enctype' => 'multipart/form-data')) !!}

                <!-- /.row -->
        <div class="row">
            <div class="col-lg-12">
                <p class="txtNote clearfix">アプリの店舗検索画面上に表示されている内容です。
                    <span class="name-company">店舗ID：{{ $store['id'] }}</span>
                </p>
                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <table id="dataTables-example" class="table table-striped">
                    <colgroup>
                        <col width="5%">
                        <col width="16%">
                        <col width="54%">
                        <col width="20%">
                        <col width="%">
                    </colgroup>

                    <tr>
                        <th colspan="2">項目名</th>
                        <th colspan="3">登録情報</th>
                    </tr>

                    <tr>
                        <th colspan="2">店舗ID</th>
                        <td colspan="3"><input value="{{$store['internal_code']}}" class="form-control" required
                                               maxlength="30" type="text" name="internal_code"></td>
                    </tr>

                    <tr>
                        <th colspan="2">店舗名</th>
                        <td colspan="3"><input value="{{$store['name']}}" class="form-control" required maxlength="30"
                                               type="text" name="name"></td>
                    </tr>

                    <tr>
                        <th colspan="2">店舗画像</th>
                        <td colspan="3">
                            <div class="photo-company">
                                {!! $store['photo_url'] ? HTML::image($store['photo_url'] , 'image Store', ['id' => 'image-store']) : '<img id="image-store">' !!}

                            </div>
                            <div class="fileUpload btn btn-default">
                                <span>参照</span>
                                {!! Form::file('photo_url', array('accept' => 'image/jpeg, image/bmp, image/png, image/jpg','class' => 'upload', 'onchange' => 'readURL(this);')) !!}


                            </div>
                            <div class="row">
                                <span id="fileErrorMsg" style="color:red;margin-left: 20px;"></span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th colspan="2">郵便番号</th>
                        <td colspan="3">
                            <?php $postalCodes = explode('-', $store['postal_code']); ?>
                            <input pattern="^[a-zA-Z0-9]+$" title="文字及び数字しか入力しない事。" required
                                   class="form-control w80 display-inline alpha-numeric" maxlength="3"
                                   ng-value="'{{$postalCodes[0]}}'" name="postal_code[0]"> -
                            <input pattern="^[a-zA-Z0-9]+$" title="文字及び数字しか入力しない事" required
                                   class="form-control w80 display-inline alpha-numeric" maxlength="4"
                                   ng-value="'{{$postalCodes[1] or ''}}'" name="postal_code[1]">

                            <p ng-if="validationErrors.postal_code" class="has-error"
                               ng-bind="validationErrors.postal_code"></p>
                        </td>
                    </tr>
                    <tr>
                        <th rowspan="2">住所</th>
                        <th>都道府県</th>
                        <td>
                            {!! Form::select('province', $provinces, $store['province'] , ['class'=> 'form-control w150 address', 'id' => 'province', 'style' => 'width: 200px']) !!}
                            <p ng-if="provinceErrorMsg" class="has-error ng-cloak">このフィールドが必須です。</p>
                        </td>
                        <th width="50%">市区町</th>
                        <td>
                            <select class="form-control w150" id="city" name="city1" style="width: 200px">
                                <option value="@{{city.name}}" ng-repeat="city in cities" ng-bind="city.name"></option>
                            </select>

                            <p ng-if="cityErrorMsg" class="has-error ng-cloak">このフィールドが必須です。</p>
                        </td>
                    </tr>
                    <tr>
                        <th>番地・建物名等</th>

                        <td colspan="3">
                            <input name="alias" type="hidden" value="{{ $store['alias'] }}">
                            <input id="address" maxlength="40" required class="form-control display-inline margin_b3"
                                   value="{{$store['address']}}" name="address">

                            <div id="map"></div>
                            <input type="hidden" name="map_coordinates_lat" id="map_coordinates_lat"
                                   value="{{$store['map_coordinates_lat'] ? $store['map_coordinates_lat'] : 35.6735408}}">
                            <input type="hidden" name="map_coordinates_long" id="map_coordinates_long"
                                   value="{{$store['map_coordinates_long'] ? $store['map_coordinates_long'] : 139.5703048}}">
                        </td>
                    </tr>
                    <tr>
                        <th colspan="2">電話</th>
                        <td colspan="3">
                            <?php $phoneNumbers = explode('-', $store['phone_number']) ?>
                            <input required class="form-control w80 display-inline number-only" type="text"
                                   maxlength="5" value="{{$phoneNumbers[0]}}" name="phone_number[0]"> -
                            <input required class="form-control w80 display-inline number-only" type="text"
                                   maxlength="4" value="{{$phoneNumbers[1] or ''}}" name="phone_number[1]"> -
                            <input required class="form-control w80 display-inline number-only" type="text"
                                   maxlength="4" value="{{$phoneNumbers[2] or ''}}" name="phone_number[2]">
                        </td>
                    </tr>
                    <tr>
                        <th colspan="2">FAX</th>
                        <td colspan="3">
                            <?php $faxNumbers = explode('-', $store['fax_number']) ?>
                            <input class="form-control w80 display-inline number-only" type="text" maxlength="5"
                                   value="{{$faxNumbers[0]}}" name="fax_number[0]"> -
                            <input class="form-control w80 display-inline number-only" type="text" maxlength="4"
                                   value="{{$faxNumbers[1] or ''}}" name="fax_number[1]"> -
                            <input class="form-control w80 display-inline number-only" type="text" maxlength="4"
                                   value="{{$faxNumbers[2] or ''}}" name="fax_number[2]">
                        </td>
                        <td style="background-color:inherit;border:none !important;" colspan="2" rowspan="4"></td>
                    </tr>
                    <tr>
                        <th colspan="2">クレジットカード利用</th>
                        <td colspan="3">
                            {!! Form::select('accept_credit_card', array('0' => '不可', '1' => '可能'), $store['accept_credit_card'], array('class'=> 'form-control w100 display-inline pa4','id' => 'accept_credit_card')) !!}
                            -
                            <input id="credit_card_type" class="form-control w60-percent display-inline"
                                   value="{{$store['credit_card_type'] or ''}}" name="credit_card_type">
                        </td>
                    </tr>
                    <tr>
                        <th colspan="2">駐車場</th>
                        <td colspan="3">
                            <input maxlength="30" class="form-control w60-percent" type="text"
                                   value="{{$store['park_info'] }}" name="park_info" placeholder="あり（20台）">
                        </td>
                    </tr>
                    <tr>
                        <th colspan="2">店舗からのお知らせ</th>
                        <td colspan="3">
                            <textarea rows="4" class="form-control w60-percent display-inline" id="description"
                                      maxlength="200" placeholder="江田駅から徒歩10分です。"
                                      name="description">{{$store['description']}}</textarea>
                            <span>※200文字まで</span>
                        </td>
                    </tr>
                    <tr>
                        <th colspan="2">営業時間</th>
                        <td colspan="3" style="vertical-align:top !important">

                            @foreach (\App\Models\Store::$days as $key => $val)

                                <div class="clearfix pb5">
                                    {!! $val . '' . Form::select('times_open[' . $key . ']', \App\Models\Store::$hoursOpen, empty($working_time['data'][$key]['start']) ? null : $working_time['data'][$key]['start'], array('class' => 'form-control select-time', 'id' => 'times_'.$key)) !!}
                                    ～
                                    {!! Form::select('times_close[' . $key . ']', \App\Models\Store::$hoursClose, empty($working_time['data'][$key]['end']) ? null : $working_time['data'][$key]['end'], array('class' => 'form-control select-time', 'id' => 'times_'.$key.'_close')) !!}
                                </div>

                            @endforeach

                            <div class="txtInfo txtInfo-edit">
                                {!! Form::textarea('note', $working_time['note'], ['class' => 'form-control', 'rows' => 2, 'id' => 'note', 'maxlength' => 100]) !!}
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th colspan="2">公開ステータス</th>
                        <td colspan="3">
                            @if($store['is_published'] == 0 && !$enableCreate)
                                <label class="pl10 font-normal">{!! Form::radio('is_published', 0, $store['is_published'] != 1 ? true : null, array('class'=>'pl10') ) !!}
                                    非公開 </label>
                            @else
                                <label class="pl10 font-normal">{!! Form::radio('is_published', 1, $store['is_published'] == 1 ? true : null, array('class'=>'pl10') ) !!}
                                    公開 </label>
                                <label class="pl10 font-normal">{!! Form::radio('is_published', 0, $store['is_published'] != 1 ? true : null, array('class'=>'pl10') ) !!}
                                    非公開 </label>

                            @endif
                        </td>
                    </tr>
                </table>

                <div class="btn-Registration btn-Company-Registration">
                    <button class="btn btn-danger center-block inline-block w150" ng-click="deleteStore($event)">削除
                    </button>
                    <button class="btn btn-primary center-block inline-block w150" type="submit">登録</button>
                </div>
            </div>
            <!-- /.col-lg-12 -->
        </div>
        {!! Form::close() !!}

                <!--Modal members set-->
        <div class="modal fade popup-cmn popview2" id="ConfirmDialog" tabindex="-1" role="dialog"
             aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="panel panel-info cont-temp">
                            <div class="panel-body">
                                <p>登録してよろしいですか？</p>
                            </div>
                            <!--/cont-temp--></div>
                    </div>
                    <div class="modal-footer">
                        <div>
                            <button ng-click="cancel()" type="button" class="btn btn-default" data-dismiss="modal">
                                キャンセル
                            </button>
                            <button ng-click="save()" type="button" class="btn btn-info btn-update">OK</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade popup-cmn popview2" id="ConfirmDialogDelete" tabindex="-1" role="dialog"
             aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="panel panel-info cont-temp">
                            <div class="panel-body">
                                <p>削除してよろしいですか？</p>
                            </div>
                            <!--/cont-temp--></div>
                    </div>
                    <div class="modal-footer">
                        <div>
                            <button type="button" class="btn btn-default" data-dismiss="modal">
                                キャンセル
                            </button>
                            <button ng-click="deleteAccept($event)" type="button" class="btn btn-info btn-update">OK
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--End Modal-->

    <!-- /#page-wrapper -->

    <script type="text/javascript">
        var allowedExts = {jpg: true, png: true, bmp: true, jpeg: true};


        function readURL(input) {
            if (input.files && input.files[0]) {
                var file = input.files[0];
                var ext = file.name.split('.').pop().toLowerCase();

                if (!allowedExts[ext]) {
                    input.value = '';
                    $('#fileErrorMsg').text(__('AllowOnlyImage'));
                    return;
                } else {
                    $('#fileErrorMsg').text('');
                }

                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#image-store')
                            .attr('src', e.target.result)
                };

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("input[type=select][name^='times_open']").change(function () {
            console.log(0);
        });

        $(document).ready(function () {
            $('select').on('change', function () {
                if (this.id.indexOf('_close') >= 0) {
                    var arr = this.id.split('_close');
                    var check = '#' + arr[0]
                }
                else {
                    var check = '#' + this.id + '_close';
                }

                if (this.value == "休") {
                    $(check).attr("disabled", true);
                    $(check + ' option').each(function () {
                        if ($(this).attr('selected', 'selected')) {
                            $(this).removeAttr('selected');
                        }
                        if ($(this).val() == "休")
                            $(this).attr("selected", "selected");
                    });

                }
                else {
                    $(check).attr("disabled", false);
                }
            });

            $(".number-only").keydown(function (e) {
                // Allow: backspace, delete, tab, escape, enter and .
                if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110]) !== -1 ||
                            // Allow: Ctrl+A, Command+A
                        (e.keyCode == 65 && ( e.ctrlKey === true || e.metaKey === true ) ) ||
                            // Allow: home, end, left, right, down, up
                        (e.keyCode >= 35 && e.keyCode <= 40)) {
                    // let it happen, don't do anything
                    return;
                }
                // Ensure that it is a number and stop the keypress

                if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                    e.preventDefault();
                }
            });

            $(".alpha-numeric").keydown(function (e) {
                if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110]) !== -1 ||
                        (e.keyCode == 65 && ( e.ctrlKey === true || e.metaKey === true ) ) ||
                        (e.keyCode >= 35 && e.keyCode <= 40)) {
                    return;
                }
                if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.ctrlKey || e.keyCode < 65 || e.keyCode > 90) && (e.keyCode < 96 || e.keyCode > 105)) {
                    e.preventDefault();
                }
            });
        })

    </script>

    <script>
        $(function () {

            $('#credit_card_type')[0].disabled = ($('#accept_credit_card').val() == 0);

            $('#accept_credit_card').on('change', function () {
                $('#credit_card_type')[0].disabled = (this.value == 0)
            });
        });
    </script>

@endsection