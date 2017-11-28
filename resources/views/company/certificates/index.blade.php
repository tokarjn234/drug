@extends('layouts.company')

@section('title', __('Summary'))

@section('content')

    <div id="page-wrapper" ng-controller="CertificatesController">
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
                    <form id="searchForm" ng-submit="search($event)" method="post"
                          action="{{action('Company\CertificatesController@postIndex')}}">

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
                                <th>端末証明書番号</th>
                                <td><input name="ssl_client_s_dn_cn" value="{{$search['ssl_client_s_dn_cn'] or ''}}"
                                           class="form-control " type="text"></td>
                                <th class="pl10">店舗名</th>
                                <td>
                                    <input value="{{$search['store_name'] or ''}}" name="store_name"
                                           class="form-control " type="text">
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td>ステータス</td>
                                <td>
                                    <div class="form-group w100">
                                        {!! Form::select('status', \App\Models\Certificate::$statuses, isset ($search['status']) ? $search['status'] : -1, ['class' => 'form-control']) !!}

                                    </div>
                                </td>
                            </tr>

                        </table>
                        <div class="clearfix">
                            <div class="center-block text-center">                                
                                    <button class="btn btn-primary btn-lg" type="submit">検索</button>
                                    <button name="_clear" value="1" class="btn btn-primary btn-lg" type="submit">検索条件をクリア</button>                                   
                            </div>
                        </div>
                    </form>
                </div>
                <div class="clearfix"></div>

                <div class="dataTable_wrapper">
                    <form method="POST"
                              action="{{ action('Company\CertificatesController@postIssueCertificates') }}">
                            {{ csrf_field() }}
                        <div class="cer-info ng-cloak">
                            発行済端末証明書数：@{{totalCerCount}} <br>
                            配布済端末証明書数：@{{dividedToStoreCount}}<br>
                            導入済端末証明書数：@{{dividedToDeviceCount}}<br>
                            無効済端末証明書数：@{{inactiveCount}}<br>
                            <div>
                                <div style="float: left; width: 20%;" >
                            <strong>発行可能証明書数：@{{availableCount}}</strong><br>


                                <input name="item[]" ng-repeat="item in getCheckedCertificate()" type="hidden"
                                       value="@{{item.alias}}">
                            
                                <button ng-disabled="getCheckedCertificate().length == 0" type="submit"
                                        class="btn btn-primary">店舗への新規割当
                                </button>
                                </div>
                                <div style="width: 80%;float: right;"><p style="color: red; text-align: left;">※店舗への新規割当をするときは、ステータスが「未発行」のものにチェックを入れてから「新規割当」ボタ<br/>ンをおしてください。同じ店舗に複数の端末証明書を同時に配布する場合は、割当数分チェックを入れてから<br/>「店舗への新規割当」ボタンを押してください。</p></div>
                            </div>
                        </div>
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                            
                            <tr>
                                <th class="txt-center" width="100px">新規割当チェック</th>
                                <th class="txt-center" style="width: 40px;"></th>
                                <th class="txt-center">証明書番号</th>
                                <th class="txt-center" width="100px">発行日</th>
                                <th class="txt-center">割当先店舗コード</th>
                                <th class="txt-center">割当先店舗</th>
                                <th class="txt-center" width="100px">店舗配布日</th>
                                <th class="txt-center">証明書導入端末</th>
                                <th class="txt-center" width="100px">最終更新 日時</th>
                                <th class="txt-center" width="100px">ステータス</th>

                            </tr>

                            </thead>
                            <tbody>


                            <tr ng-class="{darkgray:cer.status==status.STATUS_INACTIVE}" ng-repeat="cer in certificates"
                                align="center">
                                <td>
                                    <input ng-click="onCertificateChecked(cer, $event)" ng-model="cer.$checked"
                                           ng-disabled="cer.status==status.STATUS_DIVIDED_TO_DEVICE||cer.status==status.STATUS_DIVIDED_TO_STORE"
                                           ng-if="cer.status!=status.STATUS_INACTIVE" type="checkbox" name="item[]" ng-value="cer.alias">
                                </td>
                                <td ng-bind="$index+1 +(paginate-1) *10">

                                </td>
                                <td ng-bind="cer.ssl_client_s_dn_cn"></td>
                                <td ng-bind="cer.created_at"></td>
                                <td ng-bind="cer.store_id">

                                </td>
                                <td ng-bind="cer.store_name">

                                </td>
                                <td ng-bind="cer.issued_to_store_at"></td>
                                <td ng-bind="cer.name"></td>
                                <td ng-bind-html="cer.updated_at | rawHtml"></td>
                                <td>
                                    <span ng-bind="cer.$status"></span><br>
                                    <a ng-click="restoreCertificate(cer)"
                                            ng-if="cer.status==status.STATUS_DIVIDED_TO_STORE"
                                            class="btn btn-default btn-primary">回収
                                    </a>
                                    <a ng-click="disableCertificate(cer)"
                                            ng-if="cer.status==status.STATUS_DIVIDED_TO_DEVICE"
                                            class="btn btn-default btn-danger">無効化
                                    </a>
                                </td>


                            </tr>

                            </tbody>
                        </table>
                    </form>
                    <nav id="pagination">
                        @include('shared.pagination', ['paginator' => $paginate])

                    </nav>
                </div>

                <!-- /.panel -->
            </div>
            <!-- /.col-lg-12 -->
        </div>

    </div>
    <!-- /#page-wrapper -->

@endsection