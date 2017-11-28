<?php
$working_time = json_decode($store['working_time'], true);
?>

@extends('layouts.sb2admin')

@section('title', '店舗情報管理')

@section('content')
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places&language=ja"></script>
    <style type="text/css">
        #map {
            width: 100%;
            height: 300px;
        }
    </style>


    <div id="page-wrapper" class="infor-mannager" ng-controller="StoresController">
        {!! Form::open(array('id' => 'form-store','ng-submit' => 'confirm($event)', 'name' => 'form', 'action' => (array('Home\StoresController@postUpdate')), 'method' => 'post', 'enctype' => 'multipart/form-data')) !!}

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
                        <td colspan="3">
                            @if ($storeSetting->internal_code['edit'])

                                <input value="{{$store['internal_code']}}" class="form-control" required maxlength="30"
                                       type="text"
                                       name="internal_code">
                            @else
                                <span>{{$store['internal_code']}}</span>
                            @endif

                        </td>
                    </tr>

                    <tr>
                        <th colspan="2">店舗名</th>
                        <td colspan="3">
                            @if ($storeSetting->name['edit'])

                                <input value="{{$store['name']}}" class="form-control" required maxlength="30"
                                       type="text"
                                       name="name">
                            @else
                                <span>{{$store['name']}}</span>
                            @endif

                        </td>

                    </tr>
                    {{--@if ( $storeSetting->photo_url['display'])--}}
                    <tr class="{{ $storeSetting->photo_url['display'] ? '' : 'color-block'}}">
                        <th colspan="2">店舗画像</th>
                        <td colspan="3">
                            <div class="photo-company">
                                {!! $store['photo_url'] ? HTML::image($store['photo_url'] , 'image Store', ['id' => 'image-store']) : '<img id="image-store">' !!}
                            </div>
                            @if ($storeSetting->photo_url['edit'])
                                <div class="fileUpload btn btn-default">
                                    <span>参照</span>
                                    {!! Form::file('photo_url', array('accept' => 'image/jpeg, image/bmp, image/png, image/jpg','class' => 'upload', 'onchange' => 'readURL(this);', $storeSetting->photo_url['display'] ? '' : 'disabled')) !!}


                                </div>
                                <div class="row">
                                    <span id="fileErrorMsg" style="color:red;margin-left: 20px;"></span>
                                </div>
                            @endif
                        </td>
                    </tr>
                    {{--@endif--}}
                    <tr>
                        <th colspan="2">郵便番号</th>
                        <td colspan="3">
                            <?php
                            $postalCodes = explode('-', $store['postal_code']);
                            ?>
                            @if ($storeSetting->postal_code['edit'])
                                <input pattern="^[a-zA-Z0-9]+$" title="文字及び数字しか入力しない事。" required
                                       class="form-control w80 display-inline alpha-numeric" maxlength="3"
                                       ng-value="'{{$postalCodes[0]}}'" name="postal_code[0]"> -
                                <input pattern="^[a-zA-Z0-9]+$" title="文字及び数字しか入力しない事" required
                                       class="form-control w80 display-inline alpha-numeric" maxlength="4"
                                       ng-value="'{{$postalCodes[1] or ''}}'" name="postal_code[1]">

                                <p ng-if="validationErrors.postal_code" class="has-error"
                                   ng-bind="validationErrors.postal_code"></p>
                            @else
                                <span>{{$store['postal_code']}}</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th rowspan="2">住所</th>
                        <th>都道府県</th>
                        <td>
                            {!! Form::select('province', $provinces, $store['province'] , ['class'=> 'form-control w150 address', 'id' => 'province', 'style' => 'width: 200px', !$storeSetting->address['edit'] ? 'disabled' : '']) !!}
                            <p ng-if="provinceErrorMsg" class="has-error ng-cloak">このフィールドが必須です。</p>
                        </td>
                        <th width="50%">市区町</th>
                        <td>
                            <select class="form-control w150" id="city" name="city1"
                                    style="width: 200px" {{!$storeSetting->address['edit'] ? 'disabled' : ''}}>
                                <option value="@{{city.name}}" ng-repeat="city in cities" ng-bind="city.name"></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th>番地・建物名等</th>

                        <td colspan="3">
                            @if ($storeSetting->address['edit'])
                                <input id="address" maxlength="40" required
                                       class="form-control display-inline margin_b3"
                                       value="{{$store['address']}}"
                                       name="address">
                                <div id="map"></div>
                                <input type="hidden" name="map_coordinates_lat" id="map_coordinates_lat"
                                       value="{{$store['map_coordinates_lat'] ? $store['map_coordinates_lat'] : 35.6735408}}">
                                <input type="hidden" name="map_coordinates_long" id="map_coordinates_long"
                                       value="{{$store['map_coordinates_long'] ? $store['map_coordinates_long'] : 139.5703048}}">
                            @else
                                <span>{{$store['address']}}</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th colspan="2">電話</th>
                        <td colspan="3">
                            <?php
                            $phoneNumbers = explode('-', $store['phone_number']);
                            ?>
                            @if ($storeSetting->phone_number['edit'])
                                <input required class="form-control w80 display-inline number-only" type="text"
                                       maxlength="5"
                                       value="{{$phoneNumbers[0]}}" name="phone_number[0]"> -
                                <input required class="form-control w80 display-inline number-only" type="text"
                                       maxlength="4"
                                       value="{{$phoneNumbers[1] or ''}}" name="phone_number[1]"> -
                                <input required class="form-control w80 display-inline number-only" type="text"
                                       maxlength="4"
                                       value="{{$phoneNumbers[2] or ''}}" name="phone_number[2]">
                            @else
                                <span>{{$store['phone_number']}}</span>
                            @endif
                        </td>
                    </tr>
                    {{--@if ($storeSetting->fax_number['display'])--}}
                    <tr class="{!! $storeSetting->fax_number['display']?'':'color-block' !!}">
                        <th colspan="2">FAX</th>
                        <td colspan="3">
                            <?php
                            $faxNumbers = explode('-', $store['fax_number']);
                            ?>
                            @if ($storeSetting->fax_number['edit'])
                                <input class="form-control w80 display-inline number-only" type="" maxlength="5"
                                       value="{{$faxNumbers[0]}}"
                                       name="fax_number[0]" {{$storeSetting->fax_number['display'] ? '' : 'disabled'}}>
                                -
                                <input class="form-control w80 display-inline number-only" type="" maxlength="4"
                                       value="{{$faxNumbers[1] or ''}}"
                                       name="fax_number[1]" {{$storeSetting->fax_number['display'] ? '' : 'disabled'}}>
                                -
                                <input class="form-control w80 display-inline number-only" type="" maxlength="4"
                                       value="{{$faxNumbers[2] or ''}}"
                                       name="fax_number[2]" {{$storeSetting->fax_number['display'] ? '' : 'disabled'}}>
                            @else
                                <span>{{$store['fax_number']}}</span>
                            @endif
                        </td>
                        <td style="background-color:inherit;border:none !important;" colspan="2" rowspan="4"></td>
                    </tr>
                    {{--@endif--}}
                    {{--@if ($storeSetting->accept_credit_card['display'])--}}
                    <tr class="{!! $storeSetting->accept_credit_card['display']?'':'color-block' !!}">
                        <th colspan="2">クレジットカード利用</th>
                        <td colspan="3">
                            @if ($storeSetting->accept_credit_card['edit'])
                                @if ($storeSetting->accept_credit_card['data']['accept'])
                                    <div class="form-inline">
                                        {!! Form::select('accept_credit_card', array('0' => '不可', '1' => '可能'), $store['accept_credit_card'], array('class'=> 'form-control w100', 'id' => 'accept_credit_card')) !!}
                                        <input class="form-control display-inline " type="text" style="width: 250px"
                                               id="credit_card_type"
                                               value="{{$store['credit_card_type']}}" name="credit_card_type">
                                    </div>
                                @else
                                    不可
                                @endif
                            @else
                                @if ($storeSetting->accept_credit_card['data']['accept'])
                                    <div>{{$store['accept_credit_card']?  '可能' : '不可'}}</div>
                                    <div>{{$store['accept_credit_card'] ?  $store['credit_card_type'] : ''}}</div>
                                @else
                                    不可
                                @endif
                            @endif
                        </td>
                    </tr>
                    {{--@endif--}}
                    {{--                    @if ($storeSetting->park_info['display'])--}}
                    <tr class="{!! $storeSetting->park_info['display']?'':'color-block' !!}">
                        <th colspan="2">駐車場</th>
                        <td colspan="3">
                            @if ($storeSetting->park_info['edit'])
                                <input maxlength="30" class="form-control w60-percent" type="text"
                                       value="{{$store['park_info'] }}" name="park_info"
                                       placeholder="あり（20台）">
                            @else
                                <span>{{$store['park_info']}}</span>
                            @endif

                        </td>
                    </tr>
                    {{--@endif--}}
                    {{--                    @if ($storeSetting->description['display'])--}}
                    <tr class="{!! $storeSetting->description['display']?'':'color-block' !!}">
                        <th colspan="2">店舗からのお知らせ</th>
                        <td colspan="3">
                            @if ($storeSetting->description['edit'])
                                <textarea rows="4" class="form-control w60-percent display-inline" id="description"
                                          maxlength="200" placeholder="江田駅から徒歩10分です。"
                                          name="description">{{$store['description']}}</textarea>
                                <span>※200文字まで</span>
                            @else
                                <span>{{$store['description'] or $storeSetting->description['data']}}</span>
                            @endif
                        </td>
                    </tr>
                    {{--@endif--}}
                    <tr>
                        <th colspan="2">営業時間</th>
                        <td colspan="3" style="vertical-align:top !important">
                            <?php $working_time = json_decode($store['working_time'], true); ?>
                            @if ($storeSetting->working_time['edit'])
                                @foreach (\App\Models\Store::$days as $key => $val)

                                    <div class="clearfix pb5">
                                        {!!
                                        $val . '' . Form::select('times_open[' . $key . ']', \App\Models\Store::$hoursOpen, empty($working_time['data'][$key]['start']) ? null : $working_time['data'][$key]['start'], array('class' => 'form-control select-time', 'id' => 'times_'.$key))
                                        !!}
                                        ～
                                        {!!
                                         Form::select('times_close[' . $key . ']', \App\Models\Store::$hoursClose, empty($working_time['data'][$key]['end']) ? null : $working_time['data'][$key]['end'], array('class' => 'form-control select-time', 'id' => 'times_'.$key.'_close'))
                                         !!}
                                    </div>

                                @endforeach

                            @else

                                @if(!empty($working_time))
                                    @foreach($working_time['data'] as $value)
                                        <div class="clearfix pb5">
                                            {{$value['title'] . ':'}}
                                            {{ (\App\Models\Store::$hoursOpen[$value['start']] !== '休' && \App\Models\Store::$hoursClose[$value['end']] !== '休') ? \App\Models\Store::$hoursOpen[$value['start']] . '～' . \App\Models\Store::$hoursClose[$value['end']] : '休' }}
                                        </div>
                                    @endforeach
                                @endif

                                @foreach (\App\Models\Store::$days as $key => $val)
                                    <div class="clearfix pb5 hide">
                                        {!! $val . '' . Form::select('times_open[' . $key . ']', \App\Models\Store::$hoursOpen, empty($working_time['data'][$key]['start']) ? null : $working_time['data'][$key]['start'], array('class' => 'form-control select-time', 'id' => 'times_'.$key))!!}
                                        ～
                                        {!! Form::select('times_close[' . $key . ']', \App\Models\Store::$hoursClose, empty($working_time['data'][$key]['end']) ? null : $working_time['data'][$key]['end'], array('class' => 'form-control select-time', 'id' => 'times_'.$key.'_close')) !!}
                                    </div>

                                @endforeach
                            @endif

                            @if ($storeSetting->note_working_time['edit'] && $storeSetting->note_working_time['display'])
                                <div class="txtInfo txtInfo-edit">
                                    {!! Form::textarea('note', !empty($working_time['note']) ? $working_time['note'] : '', ['class' => 'form-control', 'rows' => 2, 'id' => 'note', 'maxlength' => 100]) !!}
                                </div>
                            @else
                                <div class="txtInfo {!! $storeSetting->note_working_time['display']?'':'color-block' !!}">
                                    {{!empty($working_time['note']) ? '※'. $working_time['note'] : ''}}
                                    {!! Form::textarea('note', !empty($working_time['note']) ? $working_time['note'] : '', ['class' => 'hidden']) !!}
                                </div>
                            @endif
                        </td>
                    </tr>
                </table>

                <div class="btn-Registration">
                    <button class="btn btn-primary center-block w150" type="submit">登録</button>
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
            if ($('#credit_card_type')) {
                $(this)[0].disabled = ($('#accept_credit_card').val() == 0);
            }


            $('#accept_credit_card').on('change', function () {
                $('#credit_card_type')[0].disabled = (this.value == 0)
            });

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

@endsection