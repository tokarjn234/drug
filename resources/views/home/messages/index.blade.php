@extends('layouts.layout_message')

@section('title', '処方せんメッセージ管理')

@section('content')
    <div id="page-wrapper" class="message" ng-controller="MessagesController">
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-12" style="margin-top: 70px;">
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

                <div class="col-lg-8 clearfix">

                    <ul class="navigation">
                        <li class="order-setting ng-cloak" ng-if="!singleMode">
                            <label for="msgOrdering">{{__('MessageOrdering')}}</label>
                            <select ng-cloak ng-change="onMsgOrderChanged(msgOrdering)" ng-model="msgOrdering" id="msgOrdering">
                                <option value="1">{{__('MessageOrderingByLatestOrder')}}</option>
                                <option value="2">{{__('MessageOrderingByLatestMsg')}}</option>
                            </select>
                        </li>
                        <li ng-repeat="order in orders" class="order" ng-class="{active:$index==activeIndex, disabled: order.status==Order.STATUS_INVALID || order.completed_flag==1}">
                            <div  ng-click="showOrderMessages(order, $index, $event)" class="msgBtn" href="" >
                                <span  ng-bind="'受付番号:' + order.order_code"></span> <br><span title="@{{order.full_name }}" ng-bind="(order.full_name ? order.full_name : '--') | truncate:true:15:'...'" class="inner"></span>

                            </div>
                            <span ng-if="$index==activeIndex" class="arrow"></span>
                        </li>


                      </ul>  <!--/navigation-->

                    <div class="direct-chatMessage">
                        <!-- DIRECT CHAT -->
                        <div class="box box-warning direct-chat direct-chat-warning">
                            <div class="box-footer">
                                <form action="#" method="post"  ng-submit="showConfirm(Message, $event)">
                                    <div class="input-group">
                                        <input maxlength="50" required ng-model="Message.title" type="text" name="message" placeholder="件名(50字)"
                                               class="form-control title_message">
                                        <textarea id="messageContent"  maxlength="500" required ng-model="Message.content" style="resize: vertical;max-height: 300px;" class="form-control txt_input"  ></textarea>
                                              <span class="input-group-btn">
                                                <button ng-bind="btnText[Message.type]" type="submit" class="btn btn-info btn-flat" >
                                                </button>
                                              </span>
                                    </div>
                                    <i style="font-size: 10px" ng-bind="'文字数:'+ getMessageLength() + '/500' "></i>
                                </form>
                            </div>
                            <!-- /.box-footer-->
                            <div class="box-body ng-cloak">
                                <!-- Conversations are loaded here -->
                                <div class="direct-chat-messages">
                                    <!-- Message. Default to the left -->
                                    <div class="direct-chat-msg" ng-repeat="message in messages" ng-class="{right: message.target==1}">

                                        <img class="direct-chat-img" ng-src="@{{message.target == 1 ? '/img/icon-store.jpg' : '/img/icon-patient.jpg'}}" alt="message user image">

                                        <div class="direct-chat-info clearfix" ng-class="{'pull-left': message.target==0, 'pull-right': message.target==1}">
                                            <span class="direct-chat-name" ng-class="{'pull-left': message.target==0, 'pull-right': message.target==1}">
                                                <span ng-bind="message.created_at | dateFormatHii" class="direct-chat-timestamp">2:00</span>
                                                <span ng-bind="message.created_at | dateFormatYmd" class="direct-chat-timestamp">2:00</span>
												<span ng-if="message.target==0" title="@{{currentOrder.full_name || '--'}}" ng-bind="(currentOrder.full_name || '--') | truncate:true:6"></span>
												<span ng-if="message.target==1" title="@{{message.full_name || '--'}}" ng-bind="(message.full_name || '--') | truncate:true:6"></span>
                                                <br>
												<span ng-if="message.target==1" ng-bind="getMessageType(message.type)"></span>
                                                

                                            </span>
                                        </div>
                                        <!-- /.direct-chat-info -->
                                        <div class="direct-chat-text">
                                            <p ng-if="message.target==1"  class="txtNote" ng-bind="message.title"></p>
                                            <span ng-bind="message.content" class="msg-text"></span>
                                        </div>
                                        <!-- /.direct-chat-text -->
                                    </div>

                                </div>
                                <!--/.direct-chat-messages-->


                            </div>
                            <!-- /.box-body -->
                        </div>
                        <!--/.direct-chat -->
                        <!--/direct-chat--></div>
                    <!--/col-lg-8--></div>
                <div class="col-lg-4 tab-right">
                    <h3 class="mtit">テンプレート管理</h3>

                    <div class="boxTab">
                        <div class="msg-template" ng-repeat="item in messageTemplates">


                            <h3 class="tit-temp" id="@{{'msgTemplateHeader'+item.id}}" ng-class="getMsgClass(item)" ng-bind="item.name" ng-click="showTemplate(item, $event)"></h3>

                            <div class="panel panel-success cont-temp msg-content" id="@{{'msgTemplate'+item.id}}">
                                <div class="panel-heading" style="word-wrap: break-word;" ng-bind="formatMsg(item.title)"></div>
                                <div class="panel-body">
                                    <p class="msg-body" ng-bind="formatMsg(item.content)"></p>
                                    <button ng-click="selectTemplate(item)" class="btn btn-success center-block" type="button">選択</button>
                                </div>
                             </div>   <!--/cont-temp-->
                        </div>

                    </div>    <!--/boxTab-->
                </div>
            </div>
            <nav id="pagination" ng-show="!singleMode">
                @include('shared.pagination', ['paginator' => $paginate])

            </nav>
            <!-- /.col-lg-12 -->
        </div>

        <!--Modal members set-->
        <div class="modal fade popup-cmn" id="ConfirmDialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document" style="width: 840px;">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="title-popup" ng-bind="Message.header"></div>
                        <div class="panel panel-info cont-temp" style="margin-bottom: -10px;">
                            <div style="word-wrap: break-word;" class="panel-heading" ng-bind="Message.title"></div>
                            <div class="panel-body" >
                                <p style="white-space: pre-wrap;word-wrap: break-word;" ng-bind="Message.content"></p>
                             </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div>
                            <button type="button" class="btn btn-default" data-dismiss="modal">キャンセル</button>
                            <button ng-click="sendMessage(Message, $event)" type="button" id="submitCalendar" class="btn btn-info">送信</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!--End Modal-->
    </div>


@endsection

