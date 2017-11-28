@extends('layouts.mediaid')

@section('title', '端末証明書管理')

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
                          action="{{action('Mediaid\CertificatesController@postListCertificate')}}">

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
                                <th class="pl10">企業名</th>
                                <td>
                                    <input value="{{$search['company_name'] or ''}}" name="company_name"
                                           class="form-control " type="text">
                                </td>
                                <th>端末証明書番号</th>
                                <td><input name="ssl_client_s_dn_cn" value="{{$search['ssl_client_s_dn_cn'] or ''}}"
                                           class="form-control " type="text"></td>
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
                                <button name="_clear" value="1" class="btn btn-primary btn-lg" type="submit">検索条件をクリア
                                </button>

                            </div>
                        </div>
                    </form>
                </div>
                <div class="clearfix"></div>

                <div class="dataTable_wrapper">
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                        <tr>
                            <th class="txt-center" style="width: 40px;"></th>
                            <th class="txt-center">証明書番号</th>
                            <th class="txt-center" width="100px">発行日</th>
                            <th class="txt-center">発行先企業コード</th>
                            <th class="txt-center">発行先企業名</th>
                            <th class="txt-center">配布先店舗名</th>
                            <th class="txt-center" width="100px">店舗配布日</th>
                            <th class="txt-center">証明書導入端末</th>
                            <th class="txt-center" width="100px">最終更新 日時</th>
                            <th class="txt-center" width="100px">ステータス</th>
                        </tr>
                        </thead>
                        <tbody>

                        <tr ng-class="{darkgray:cer.status==status.STATUS_INACTIVE}" ng-repeat="cer in certificates"
                            align="center">
                            <td ng-bind="$index+1 +(paginate-1) *10"></td>
                            <td ng-bind="cer.ssl_client_s_dn_cn"></td>
                            <td ng-bind="cer.created_at"></td>
                            <td ng-bind="cer.company_id?cer.company_id:'-'"></td>
                            <td ng-bind="cer.company_name?cer.company_name:'-'"></td>
                            <td ng-bind="cer.store_name?cer.store_name:'-'"></td>
                            <td ng-bind="cer.issued_to_store_at?cer.issued_to_store_at:'-'"></td>
                            <td ng-bind="cer.name"></td>
                            <td ng-bind-html="cer.updated_at | rawHtml"></td>
                            <td>
                                <span ng-bind="cer.$status"></span><br>
                                <button ng-click="restoreCertificate(cer)"
                                        ng-if="cer.status==status.STATUS_DIVIDED_TO_STORE"
                                        class="btn btn-default btn-primary">回収
                                </button>
                                <button ng-click="disableCertificate(cer)"
                                        ng-if="cer.status==status.STATUS_DIVIDED_TO_DEVICE"
                                        class="btn btn-default btn-danger">無効化
                                </button>
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

    </div>
    <!-- /#page-wrapper -->

@endsection