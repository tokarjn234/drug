<?php
$working_time = json_decode($store['working_time'], true);
?>

@extends('layouts.company')

@section('title', '店舗情報管理')

@section('content')
    <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false&libraries=places"></script>
    <style type="text/css">
        #map{ width:100%; height: 300px; }
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
                        <td >{{$store['name']}}</td>

                    </tr>
                    <tr>
                        <th>店舗画像</th>
                        <td >
                            <div class="photo-company">{!! !empty($store['photo_url']) ? HTML::image($store['photo_url']) : '' !!}</div>
                        </td>
                    </tr>
                    <tr>
                        <th>郵便番号</th>
                        <td>
                            {{$store['postal_code']}}
                        </td>
                    </tr>
                    <tr>
                        <th>住所</th>
                        <td >
                            {{$store['province'] . ' ' . $store['city1'] . ' ' . $store['address']}}
                            <div id="map"></div>
                            <input type="hidden" name="map_coordinates_lat" id="map_coordinates_lat" value="{{$store['map_coordinates_lat']}}">
                            <input type="hidden" name="map_coordinates_long" id="map_coordinates_long" value="{{$store['map_coordinates_long']}}">
                        </td>
                    </tr>
                    <tr>
                        <th>電話</th>
                        <td>{{$store['phone_number']}}</td>
                    </tr>
                    <tr>
                        <th>FAX</th>
                        <td>{{$store['fax_number']}}</td>

                    </tr>
                    <tr>
                        <th>クレジットカード利用</th>
                        <td >{{$store['accept_credit_card'] == 1 ? '可能' : '不可'}} {{ $store['credit_card_type'] ? ' - '.$store['credit_card_type'] : '' }}</td>
                    </tr>
                    <tr>
                        <th>駐車場</th>
                        <td>{{$store['park_info']}}</td>
                    </tr>
                    <tr>
                        <th>店舗からのお知らせ</th>
                        <td>{!! nl2br(e($store['description']))!!} </td>
                    </tr>
                    <tr>
                        <th>営業時間</th>
                        <td  style="vertical-align:top !important">
                            <?php $working_time = json_decode($store['working_time'], true); ?>
                            @if(!empty($working_time))
                                @foreach($working_time['data'] as $value)
                                    <div class="clearfix pb5">
                                        {{$value['title'] . ':'}}
                                        {{ (\App\Models\Store::$hoursOpen[$value['start']] !== '休' && \App\Models\Store::$hoursClose[$value['end']] !== '休') ? \App\Models\Store::$hoursOpen[$value['start']] . '～' . \App\Models\Store::$hoursClose[$value['end']] : '休' }}
                                    </div>
                                @endforeach
                            @endif
                            <div class="txtInfo">{{$working_time['note'] ? '※'. $working_time['note'] : ''}}</div>
                        </td>
                    </tr>
                    <tr>
                        <th>公開ステータス</th>
                        <td>{{$store['is_published'] == 1 ? '公開' : '非公開'}}</td>
                    </tr>
                </table>
                <div class="btn-Registration {{$store['is_deleted'] == 1 ? 'hidden' : ''}}">
                    <a href="{{ action('Company\StoresController@getEdit') }}/{{$store['alias']}} " class="btn btn-primary center-block w150">編集</a>
                </div>
            </div>
            <!-- /.col-lg-12 -->
        </div>
    </div>
    <!-- /#page-wrapper -->

@endsection
