@extends('layouts.company')

@section('title', '受信履歴管理')

@section('content')
    <div id="page-wrapper">
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-12">
                <div class="panel-body body-search">
                    <form id="searchForm" ng-submit="search($event)" method="post" action="{{action('Company\OrdersController@postOrderHistory')}}">
                        {{ csrf_field() }}
                        <table>
                            <colgroup>
                                <col width="10%">
                                <col width="15%">
                                <col width="12%">
                                <col width="15%">
                                <col width="7%">
                                <col width="3%">
                                <col width="15%">
                                <col width="7%">
                                <col width="10%">
                                <col width="20%">
                            </colgroup>
                            <tr>
                                <th>店舗名</th>
                                <td><input name="store_name" value="{{$search['store_name'] or ''}}" class="form-control" type="text" placeholder=""></td>
                                <th class="pl10">受付番号</th>
                                <td>
                                    <input value="{{$search['order_code'] or ''}}" name="order_code" class="form-control " id="startReceived" type="text" >
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td rowspan="2" style="vertical-align:top;text-align:right;padding:13px 10px 0">ステータス</td>
                                <td>
                                    <div class="form-group w100">
                                        {!! Form::select('status', \App\Models\Order::$statuses, @$search['status'], ['class' => 'form-control']) !!}

                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>送信者名</th>
                                <td><input name="username" value="{{$search['username'] or ''}}" class="form-control" type="text" placeholder=""></td>
                                <th class="pl10">受信日時</th>
                                <td>
                                    <input onkeypress="return false;" value="{{$search['received_date_start'] or ''}}" name="received_date_start" class="form-control datepicker" id="startVisit" type="text" placeholder="----年--月--日">
                                </td>
                                <td align="center">
                                    <input onkeypress="return false;" value="{{$search['received_time_start'] or ''}}" name="received_time_start"  class="form-control time_cmn timepicker" id="startVisitTime" type="text" value="" placeholder="--" disabled>
                                </td>
                                <td align="center">～</td>
                                <td>
                                    <input onkeypress="return false;" value="{{$search['received_date_end'] or ''}}" name="received_date_end" class="form-control datepicker" id="endVisit" type="text" placeholder="----年--月--日">
                                </td>
                                <td>
                                    <input onkeypress="return false;" value="{{$search['received_time_end'] or ''}}" name="received_time_end" class="form-control time_cmn timepicker" id="endVisitTime" type="text" value="" placeholder="--" disabled>
                                </td>
                                <td>
                                    <div class="form-group" style="width: 110px">
                                        <label>{!! Form::checkbox('completed_flag', 'false', @$search['completed_flag'] == 'false') !!}  調剤完了を除く</label>
                                        <label>{!! Form::checkbox('status_invalid', 'false', @$search['status_invalid'] == 'false') !!}  無効を除く</label>
                                    </div>
                                </td>

                            </tr>
                        </table>
                        <div class="clearfix">
                            <div class="center-block text-center">
                                <!-- <input ng-if="clearSearchConditions" type="hidden" name="_clear" value="1"> -->
                                <button class="btn btn-primary btn-lg" type="submit">検索</button>
                                <button name="btn_reset" class="btn btn-primary btn-lg" type="submit" >検索条件をクリア</button>

                            </div>
                        </div>
                    </form>
                </div>
            
            <div class="clearfix"></div>
                <!-- /.panel-body -->
                <table id="dataTables-example" class="table table-striped">
                    <tr>
                        <th></th>
                        <th style="text-align: center;">受付番号</th>
                        <th style="text-align: center;">店舗名</th>
                        <th style="text-align: center;">受付日時</th>
                        <th style="text-align: center;">送信者名</th>
                        <th style="text-align: center;">ステータス</th>                        
                    </tr>
                    @foreach($paginate as $index => $listOrder)
                    <tr>
                        <td align="center">{{ ($index + 1) + ($paginate->currentPage()-1)*10 }}</td>
                        <td><a href="{!! action('Company\OrdersController@getOrderDetail',$listOrder['alias']) !!}">{{ substr(strstr($listOrder['order_code'],"-"), 1) }}</a></td>
                        <td>{{ $listOrder['name']}}</td>
                        <td style="width: 155px;">{{ date('Y/m/d', strtotime($listOrder['created_at'])) }} {{ date('H:i',strtotime($listOrder['created_at'])) }}</td>
                        <td>{{ decrypt_data($listOrder['first_name_kana']) }} {{ decrypt_data($listOrder['last_name_kana']) }}</td>
                        <td align="center">{{ @\App\Models\Order::$statuses[$listOrder['status']] }}</td>
                    </tr>
                    @endforeach
                </table>

                <nav id="pagination">
                        @include('shared.pagination', ['paginator' => $paginate])

                </nav>

            </div>
            <!-- /.col-lg-12 -->
        </div>
    </div>
    <!-- /#page-wrapper -->

@endsection