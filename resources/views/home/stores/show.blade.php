<?php
$working_time = json_decode($store['working_time'], true);
?>

@extends('layouts.sb2admin')

@section('title', '店舗情報管理')

@section('content')
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places"></script>
    <style type="text/css">
        #map {
            width: 100%;
            height: 300px;
        }
    </style>

    <div id="page-wrapper" class="infor-mannager" ng-controller="StoresShowController">
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-12">
                <p class="txtNote clearfix">アプリの店舗検索画面上に表示されている内容です。
                    <span class="name-company">店舗ID：{{ $store['id'] }}</span>
                </p>

                <table id="dataTables-example" class="table table-striped table-striped-show">
                    <colgroup>
                        <col width="2%">
                        <col width="100%">
                    </colgroup>

                    <tr>
                        <th>項目名</th>
                        <th>登録情報</th>

                    </tr>

                    <tr>
                        <th>店舗ID</th>
                        <td>{{$store['internal_code']}}</td>
                    </tr>

                    <tr>
                        <th>店舗名</th>
                        <td>{{$store['name']}}</td>

                    </tr>
                    {{--@if ($storeSetting->photo_url['display'])--}}
                    <tr class="{{ $storeSetting->photo_url['display'] ? '' : 'color-block'}}">
                        <th>店舗画像</th>
                        <td>
                            <div class="photo-company">
                                <div class="photo-company">{!! !empty($store['photo_url']) ? HTML::image($store['photo_url']) : '' !!}</div>
                            </div>
                        </td>
                    </tr>
                    {{--@endif--}}

                    <tr>
                        <th>郵便番号</th>
                        <td>
                            {{$store['postal_code']}}
                        </td>
                    </tr>

                    <tr>
                        <th>住所</th>
                        <td>
                            {{$store['province'] . ' ' . $store['city1'] . ' ' . $store['address']}}
                            <div id="map"></div>
                            <input type="hidden" name="map_coordinates_lat" id="map_coordinates_lat"
                                   value="{{$store['map_coordinates_lat']}}">
                            <input type="hidden" name="map_coordinates_long" id="map_coordinates_long"
                                   value="{{$store['map_coordinates_long']}}">
                        </td>
                    </tr>
                    <tr>
                        <th>電話</th>
                        <td>{{$store['phone_number']}}</td>
                    </tr>
                    <tr class="{{ $storeSetting->fax_number['display'] ? '' : 'color-block' }}">
                        <th>FAX</th>
                        <td>{{$store['fax_number']}}</td>

                    </tr>
                    <tr class="{!! $storeSetting->accept_credit_card['display']?'':'color-block' !!}">
                        <th>クレジットカード利用</th>
                        <td>
                            @if ($storeSetting->accept_credit_card['data']['accept'])
                                <div>{{$store['accept_credit_card'] == 1 ? '可能' : '不可'}}</div>
                                <div>{{$store['accept_credit_card'] == 1 ? $store['credit_card_type'] : ''}}</div>
                            @else
                                不可
                            @endif
                        </td>
                    </tr>
                    <tr class="{!! $storeSetting->park_info['display']?'':'color-block' !!}">
                        <th>駐車場</th>
                        <td>{{$store['park_info']}}</td>
                    </tr>
                    <tr class="{!! $storeSetting->description['display']?'':'color-block' !!}">
                        <th>店舗からのお知らせ</th>
                        <td>{!! nl2br(e($store['description']))!!} </td>
                    </tr>
                    <tr>
                        <th>営業時間</th>
                        <td style="vertical-align:top !important">
                            <?php $working_time = json_decode($store['working_time'], true); ?>
                            @if(!empty($working_time))
                                @foreach($working_time['data'] as $value)
                                    <div class="clearfix pb5">
                                        {{$value['title'] . ':'}}
                                        {{ (\App\Models\Store::$hoursOpen[$value['start']] !== '休' && \App\Models\Store::$hoursClose[$value['end']] !== '休') ? \App\Models\Store::$hoursOpen[$value['start']] . '～' . \App\Models\Store::$hoursClose[$value['end']] : '休' }}
                                    </div>
                                @endforeach
                            @endif
                            {{--@if ($storeSetting->note_working_time['display'])--}}
                                <div class="txtInfo2 {!! $storeSetting->note_working_time['display']?'':'color-block' !!}">{{!empty($working_time['note']) ? '※'. $working_time['note'] : ''}}</div>
                            {{--@endif--}}
                        </td>
                    </tr>
                </table>
                @if ($isEditable)
                    <div class="btn-Registration">
                        <a href="{!! action('Home\StoresController@getEdit') !!}"
                           class="btn btn-primary center-block w150">編集</a>
                    </div>
                @endif
            </div>
            <!-- /.col-lg-12 -->
        </div>
    </div>
    <!-- /#page-wrapper -->

@endsection
