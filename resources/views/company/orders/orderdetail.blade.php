@extends('layouts.company')

@section('title', '受信履歴管理')

@section('content')
    <div id="page-wrapper">
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-12">
                <div class="panel-body body-search">
                    <table id="dataTables-example" class="table table-striped">
                        <colgroup>
                            <col width="11%">
                            <col width="14%">
                            <col width="11%">
                            <col width="14%">
                            <col width="11%">
                            <col width="14%">
                        </colgroup>
                        <tr class="inline">
                            <th>受付番号</th>
                            <td style="text-align: center;">{{ isset($orderDetail) ? substr(strstr($orderDetail['order_code'],"-"), 1) : '' }}</td>
                            <th >店舗名</th>
                            <td style="text-align: center;">{{ isset($orderDetail) ? $orderDetail['name'] : '' }}</td>
                            <th >ステータス</th>
                            <td style="text-align: center;">{{ isset($orderDetail) ? @\App\Models\Order::$statuses[$orderDetail['status']] : '' }}</td>
                        </tr>
                        <tr class="online">
                            <th>受付日時</th>
                            <td class="txt-center">{{ isset($orderDetail) ? date('Y/m/d', strtotime($orderDetail['created_at'])) : '' }} {{ isset($orderDetail) ? date('H:i',strtotime($orderDetail['created_at'])) : '' }}</td>
                            <th >送信者名</th>
                            <td class="txt-center">{{ isset($orderDetail) ? decrypt_data($orderDetail['first_name_kana']) : '' }}{{ isset($orderDetail) ? decrypt_data($orderDetail['last_name_kana']) : '' }}</td>
                            <th>処方せん画像</th>
                            <td class="txt-center">
                            @if( isset($settingDeleteImage) && $settingDeleteImage >= 0 && empty($orderDetail['delete_reason']) )
                                削除済
                            @else
                                <a href="{!! action('Company\OrdersController@getPhotos',$orderDetail['alias']) !!}" target="_blank"><i class="fa fa-file-o f-30"></i></a>
                            @endif
                            </td>
                        </tr>
                    </table>


                </div>
                <!-- /.panel-body -->
                <table id="dataTables-example" class="table table-striped">
                    <tr>
                        <th></th>
                        <th>対応日時</th>
                        <th>スタッフ名</th>
                        <th>対応内容</th>
                        <th>メッセージ内容</th>
                        <th>ステータス</th>
                    </tr>
                    <tr>
                        <td align="center">1</td>
                        <td>2015/12/1　8:50</td>
                        <td align="center">-</td>
                        <td align="center">処方めーる受信</td>
                        <td align="center">-</td>
                        <td align="center">メール受信</td>
                    </tr>
                    @foreach($paginate as $index => $message)
                    <tr>
                        <td align="center">{{$index + 2}}</td>
                        <td style="width: 155px;">{{ date('Y/m/d', strtotime($message['created_at'])) }} {{ date('H:i',strtotime($message['created_at'])) }}</td>
                        <td align="center">
                            @if($message['created_staff_id'] == null)
                                -
                            @else
                                {{ $message['staffFirstName'] }}{{ $message['staffLastName'] }}
                            @endif
                        </td>
                        <td>
                            @if($message['target'] == 0)
                                送信者からのメッセージ受信
                            @elseif($message['target'] == 1)
                                メッセージ送信
                            @endif
                            <br>
                            @if($message['type'] == 0)
                                (受付通知)
                            @elseif($message['type'] == 1)
                                (調剤完了)
                            @else
                                (その他)
                            @endif
                        </td>
                        <td>
                            <div style="max-width:70%;vertical-align: middle;" class="pull-left">
                                {{ decrypt_data($message['title']) }}
                            </div>
                            <div style="display:table-cell; vertical-align: middle;" class="text-center pull-right">
                                <button class="btn btn-primary" data-toggle="modal" data-target="#message{{ $message['id'] }}" >本文を読む</button>
                            </div>
                        </td>
                        <td align="center">
                            @if($message['orderStatus'] == 0 && $message['type'] !=2)
                                受信
                            @elseif($message['orderStatus'] == 2 && $message['type'] == 0)
                                受付通知
                            @elseif($message['orderStatus'] == 1 && $message['type'] == 0)
                                受付通知
                            @elseif($message['orderStatus'] == 2 && $message['type'] == 1)
                                調剤完了
                            @elseif($message['orderStatus'] == 1 && $message['type'] == 1)
                                調剤完了
                            @elseif($message['orderStatus'] == 3 && $message['type'] !=2)
                                無効
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    <div class="modal fade popup-cmn" id="message{{ $message['id'] }}" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document" style="width: 150px;">
                            <div class="modal-content">
                                <div class="modal-body" >

                                    <div class="title-popup" style="white-space: pre-wrap;word-wrap: break-word;">{{ decrypt_data($message['title']) }}</div>
                                    <div class="panel panel-info cont-temp" style="margin-bottom: -10px;">
                                        <div style="word-wrap: break-word;" class="panel-heading" ></div>
                                        <div style="white-space: pre-wrap;word-wrap: break-word;" class="panel-body" >{{ decrypt_data($message['content']) }}</div>
                                    </div>

                                </div>
                                <div class="modal-footer">
                                    <div>
                                        <button type="button" class="btn btn-info" data-dismiss="modal">閉じる</button>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
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