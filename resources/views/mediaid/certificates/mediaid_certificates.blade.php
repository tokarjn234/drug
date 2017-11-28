@extends('layouts.mediaid')
@section('title', '証明書管理')
@section('content')

    <div id="page-wrapper" ng-controller="InActiveCertificatesController">
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-12 margin_b20">
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
                <div class="panel-body body-search">
                    <form id="searchForm" ng-submit="search($event)" method="post"
                          action="{{action('Mediaid\CertificatesMediaidController@postIndex')}}">

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
                                <th class="pl10">証明書数を入力してください。</th>
                                <td>
                                    <input value="{{$search['company_name'] or ''}}" name="cert_number"
                                           class="form-control " type="number" min="1" required="required">
                                </td>
                                <td><button class="btn btn-primary btn-lg" value="add_certs" name="add_certs" type="submit">発行する</button></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>

                        </table>

                    </form>
                </div>
                <div class="clearfix"></div>
                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="dataTable_wrapper">
                    <form role="form" id="frm_mediaid_cert" method="POST" action="{{action('Mediaid\CertificatesMediaidController@postIndex')}}">
                        {{csrf_field()}}
                        <input type="hidden" id="disableCertUrl" value="{{action('Mediaid\CertificatesMediaidController@postDisabledCertificate')}}" />
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th class="txt-center">証明書番号</th>
                                    <th class="txt-center">パスワード</th>
                                    <th class="txt-center">使用状況</th>
                                    <th class="txt-center">ステータス</th>
                                </tr>
                            </thead>
                            <tbody>

                            @foreach($certificates as $cert)
                                <tr>
                                    <td class="txt-center">{{$cert->ssl_client_s_dn_cn}}</td>
                                    <td class="txt-center">{{$cert->export_password}}</td>
                                    <td class="txt-center">
                                        @if ($cert->status == \App\Models\Certificate::STATUS_NOT_DIVIDE)
                                            <button name="cert_alias" type="submit" value="{{$cert->alias}}" class="btn btn-primary">未使用</button>
                                        @else
                                            <a class="btn btn-default disabled">使用済</a>
                                        @endif
                                    </td>
                                    <td class="txt-center">
                                        @if ($cert->status == \App\Models\Certificate::STATUS_NOT_DIVIDE)
                                            未割当
                                        @elseif ($cert->status == \App\Models\Certificate::STATUS_DIVIDED_TO_DEVICE)
                                            導入済
                                            <a ng-click="disableMediaidCertificate('{{$cert->alias}}')"
                                                    class="btn btn-default btn-danger">無効化
                                            </a>
                                        @else
                                            無効
                                        @endif
                                    </td>
                                </tr>
                            @endforeach

                            </tbody>
                        </table>
                    </form>
                    <nav id="pagination">
                        @include('shared.pagination', ['paginator' => $certificates])
                    </nav>
                </div>

                <!-- /.panel -->
            </div>
            <!-- /.col-lg-12 -->
        </div>

    </div>
    <!-- /#page-wrapper -->

@endsection