@extends('layouts.sb2admin')

@section('title', __('OrderManagement'))

@section('content')

    <div id="page-wrapper" ng-controller="OrdersIndexController">
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-12">

                <div class='uil-default-css hidden'>
                    <div class="loading-io"></div>
                    <div class="loading-io"></div>
                    <div class="loading-io"></div>
                    <div class="loading-io"></div>
                    <div class="loading-io"></div>
                    <div class="loading-io"></div>
                    <div class="loading-io"></div>
                    <div class="loading-io"></div>
                    <div class="loading-io"></div>
                    <div class="loading-io"></div>
                    <div class="loading-io"></div>
                    <div class="loading-io"></div>
                </div>

                <div class="modal-backdrop fade in loading modal-backdrop-loading hidden"></div>

                <div class="panel-body body-search">
                    <form id="searchForm" ng-submit="search($event)" method="post" action="{{action('Home\OrdersController@postIndex')}}">

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
                                <th>{{__('DrugCode')}}</th>
                                <td><input name="order_code" value="{{$search['order_code'] or ''}}" class="form-control" type="text" placeholder=""></td>
                                <th class="pl10">受信日時</th>
                                <td>

                                    <input onkeypress="return false;" value="{{$search['received_date_start'] or ''}}" name="received_date_start" class="form-control datepicker" id="startReceived" type="text" placeholder="----年--月--日">
                                </td>
                                <td align="center">
                                    <input onkeypress="return false;" value="{{$search['received_time_start'] or ''}}" name="received_time_start" class="form-control time_cmn timepicker" id="startReceivedTime" type="text" value="" placeholder="--" disabled>
                                </td>
                                <td align="center">～</td>
                                <td>
                                    <input onkeypress="return false;" value="{{$search['received_date_end'] or ''}}" name="received_date_end" class="form-control datepicker" id="endReceived" type="text" placeholder="----年--月--日">
                                </td>
                                <td>
                                    <input onkeypress="return false;" value="{{$search['received_time_end'] or ''}}" name="received_time_end" class="form-control time_cmn timepicker" id="endReceivedTime" type="text" value="" placeholder="--" disabled>
                                </td>
                                <td rowspan="2" style="vertical-align:top;text-align:right;padding:13px 10px 0">ステータス</td>
                                <td>
                                    <div class="form-group w100">
                                        {!! Form::select('status', \App\Models\Order::$statuses, @$search['status'], ['class' => 'form-control']) !!}

                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>{{__('SenderName')}}</th>
                                <td><input name="username" value="{{$search['username'] or ''}}" class="form-control" type="text" placeholder=""></td>
                                <th class="pl10">受取希望日</th>
                                <td>
                                    <input onkeypress="return false;" value="{{$search['visit_date_start'] or ''}}" name="visit_date_start" class="form-control datepicker" id="startVisit" type="text" placeholder="----年--月--日">
                                </td>
                                <td align="center">
                                    <input onkeypress="return false;" value="{{$search['visit_time_start'] or ''}}" name="visit_time_start"  class="form-control time_cmn timepicker" id="startVisitTime" type="text" value="" placeholder="--" disabled>
                                </td>
                                <td align="center">～</td>
                                <td>
                                    <input onkeypress="return false;" value="{{$search['visit_date_end'] or ''}}" name="visit_date_end" class="form-control datepicker" id="endVisit" type="text" placeholder="----年--月--日">
                                </td>
                                <td>
                                    <input onkeypress="return false;" value="{{$search['visit_time_end'] or ''}}" name="visit_time_end" class="form-control time_cmn timepicker" id="endVisitTime" type="text" value="" placeholder="--" disabled>
                                </td>
                                <td>
                                    <div class="form-group" style="width: 110px">
                                        <label>{!! Form::checkbox('completed_flag', 'false', @$search['completed_flag'] == 'false') !!}   完了を除く</label>
                                        <label>{!! Form::checkbox('status_invalid', 'false', @$search['status_invalid'] == 'false') !!}   無効を除く</label>
                                    </div>
                                </td>

                            </tr>
                        </table>
                        <div class="clearfix">
                            <div class="center-block text-center">
                                <input ng-if="clearSearchConditions" type="hidden" name="_clear" value="1">
                                <button class="btn btn-primary btn-lg" type="submit">検索</button>
                                <button ng-click="clearSearch()" class="btn btn-primary btn-lg" type="button" >検索条件をクリア</button>

                            </div>
                        </div>
                    </form>
                </div>
                <div class="clearfix"></div>

                <div class="dataTable_wrapper">
                    <table class="table table-striped table-bordered table-hover" id="dataTables-example">
                        <thead>
                        <tr>
                            <th class="txt-center" rowspan="2">{{__('DrugCode')}}</th>
                            <th class="txt-center" rowspan="2">{!! __('ReceivedTime') !!}</th>
                            <th class="txt-center" rowspan="2">{!! __('VisitTime') !!}</th>
                            <th class="txt-center" rowspan="2">{{__('SenderNameKana')}}</th>
                            <th class="txt-center" rowspan="2">{!! __('MemberType') !!}</th>
                            <th class="txt-center" colspan="3">{{__('MessageManagement')}}</th>
                            <th class="txt-center" rowspan="2">{!!__('PrescriptionImage')!!}</th>
                            <th class="txt-center" rowspan="2">{{__('OrderStatus')}}</th>
                            <th class="txt-center" rowspan="2">{{__('OrderCompletion')}}</th>
                            <th class="txt-center" rowspan="2">{{__('OrderDelete')}}</th>
                            <th class="txt-center" rowspan="2">{!!__('OrderDeleteComment')!!}</th>
                        </tr>
                        <tr>
                            <th class="txt-center">{{__('ReceivedMessage')}}</th>
                            <th class="txt-center">{{__('DispensedMessage')}}</th>
                            <th class="txt-center">{{__('OtherMessage')}}</th>
                        </tr>
                        </thead>
                        <tbody>

                              <tr ng-repeat="order in orders" ng-class="getRowClass(order)" data-id="@{{order.id}}" ng-cloak>
                                  <td class="txt-center">
                                      <strong ng-bind-html="order.order_code | rawHtml"></strong>

                                  </td>
                                  <td class="txt-center" ng-bind-html="order.created_at | rawHtml"></td>

                                  <td class="txt-center" ng-bind-html="order.visit_at_string | rawHtml"></td>
                                  <td class="txt-center w150" ng-bind="order.first_name + ' '+ order.last_name"></td>
                                  <td class="txt-center" ng-bind="order.$member_type"></td>
                                  <td class="txt-center over" ng-class="getReceivedMsgClass(order)">
                                      <div ng-if="!order.sent_received_msg_at">
                                            <span ng-class="getReceivedMsgBtnClass(order)" ng-click="sendReceivedOrderMsg(order, $event)" class="btn btn-warning">定型文</span><br><span  ng-click="editAndSendMsg(order, 'received', $event)" ng-class="getReceivedMsgBtnClass(order)" class="btn btn-warning">非定型文</span>
                                      </div>
                                      <span ng-if="order.sent_received_msg_at" ng-bind-html="order.sent_received_msg_at | rawHtml"></span>
                                  </td>
                                  <td class="txt-center over" ng-class="getPreparedMsgClass(order)">
                                      <div ng-if="!order.sent_prepared_msg_at">
                                          <span ng-class="getPreparedMsgBtnClass(order)" ng-click="sendPreparedOrderMsg(order, $event)" class="btn btn-warning">定型文</span><br><span ng-click="editAndSendMsg(order, 'prepared', $event)" ng-class="getPreparedMsgBtnClass(order)" class="btn btn-warning">非定型文</span>
                                      </div>
                                      <span ng-if="order.sent_prepared_msg_at" ng-bind-html="order.sent_prepared_msg_at | rawHtml"></span>
                                  </td>
                                  <td class="txt-center" >
                                      <div>
                                          <p ng-if="order.sent_other_msg_at" ng-bind-html="order.sent_other_msg_at | rawHtml"></p>
                                          <a ng-click="editAndSendMsg(order, 'others', $event)" href="" ng-class="{'disabled grey': order.status==Order.STATUS_INVALID}" class="btn btn-info"><i class="fa fa-plus"></i><span ng-if="!order.sent_other_msg_at"><br/></span>追加</a>
                                      </div>
                                  </td>
                                  <td class="txt-center"><i  ng-click="viewPhoto(order)" class="fa fa-file-o f-30 order-photo"></i></td>
                                  <td class="txt-center" ng-bind="order.$status"></td>
                                  <td class="txt-center complete-checkbox" >
                                      <input ng-disabled="(order.status==Order.STATUS_PREPARED_NOTIFIED && order.sent_prepared_msg_at) || order.status==Order.STATUS_INVALID" ng-click="showCompletePopup(order, $event)" type="checkbox" name="completed_flag" ng-checked="order.completed_flag==1">
                                  </td>
                                  <td class="txt-center delete-checkbox">
                                      <input ng-click="showDeletePopup(order, $event)" ng-disabled="order.completed_flag==1 || order.status==Order.STATUS_INVALID" ng-checked="order.status==Order.STATUS_INVALID"  type="checkbox">
                                  </td>
                                  <td class="txt-center w100" >
                                      <p title="@{{ order.delete_reason }}" class="trim-info" ng-bind="order.delete_reason | truncate:true:75:'...'"></p>
                                  </td>
                              </tr>
                        </tbody>
                    </table>
                    <nav id="pagination">
                        @include('shared.pagination', ['paginator' => $paginate])

                    </nav>
                </div>

                <!-- /.panel -->
            </div>
            <!-- /.col-lg-12 -->
        </div>

        <div class="modal fade popup-cmn" id="SendMsgDialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document" style="width: 840px;">
                <div class="modal-content">
                    <div class="modal-body" >
                        <div class="title-popup" ng-bind="messageTemplates[sendMsgType].header"></div>
                        <div class="panel panel-info cont-temp" style="margin-bottom: -10px;">
                            <div style="word-wrap: break-word;" class="panel-heading" ng-bind="formatMsg(messageTemplates[sendMsgType].title)"></div>
                            <div style="white-space: pre-wrap;word-wrap: break-word;" class="panel-body" ng-bind="formatMsg(messageTemplates[sendMsgType].content)">
                             </div>
                            <!--/cont-temp--></div>
                    </div>
                    <div class="modal-footer">
                        <div>
                            <button type="button" class="btn btn-default" data-dismiss="modal">キャンセル</button>
                            <button ng-click="sendMessage(currentOrder, messageTemplates[sendMsgType], $event)" type="button" id="submitCalendar" class="btn btn-info">送信</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!--Modal members set-->
        <div class="modal fade popup-cmn popview2" id="CompletedConfirmDialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="panel panel-info cont-temp">
                            <div class="panel-body">
                                <p>この処方せんの対応を完了<br>してよろしいですか？</p>
                            </div>
                            <!--/cont-temp--></div>
                    </div>
                    <div class="modal-footer">
                        <div>
                            <button type="button" class="btn btn-default" data-dismiss="modal" ng-click="completedCancel()">キャンセル</button>
                            <button type="button" class="btn btn-info" ng-click="setCompleteOrder(currentOrder, $event)">OK</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--End Modal-->

        <!--Modal members set-->
        <div class="modal fade popup-cmn popview2" id="DeletedConfirmDialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">

                <div class="modal-content">
                    <div class="modal-body">
                        <div class="title-popup">この処方せんを削除します。削除理由を記載してください。</div>
                        <div class="panel panel-info">
                            <textarea maxlength="255" ng-model="tmpDeleteReason" style="resize: none; width: 366px; height: 100px;" class="form-control"></textarea>

                        </div>
                        <span ng-show="showDeleteReasonError" style="color:red;margin-top:-5px;">削除理由が入力されていません</span>
                    </div>
                    <div class="modal-footer">
                        <div>
                            <button type="button" class="btn btn-default" data-dismiss="modal">キャンセル</button>
                            <button type="button" class="btn btn-info" ng-click="setDeletedOrder(currentOrder, tmpDeleteReason, $event)">OK</button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <!--End Modal-->



    </div>
    <!-- /#page-wrapper -->

@endsection