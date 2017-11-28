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
                          action="{{action('Mediaid\CertificatesController@postIndex')}}">

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
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
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
                            <tr align="center">
                                <th rowspan="2">企業コード</th>
                                <th rowspan="2">企業名</th>
                                <th colspan="5">端末証明書</th>
                                <th rowspan="2">詳細情報</th>
                            </tr>
                            <tr align="center">
                                <th>発行済</th>
                                <th>店舗配布済</th>
                                <th>導入済</th>
                                <th>無効済</th>
                                <th>残数</th>
                            </tr>
                        </thead>
                        <tbody>

                            @foreach($certificates as $cert)
                            <tr align="center">
                                <td>{{ $cert['id'] }}</td>
                                <td>{{ $cert['name'] }}</td>
                                <td>{{ $cert['count']['totalCerCount'] }}</td>
                                <td>{{ $cert['count']['dividedToStoreCount'] }}</td>
                                <td>{{ $cert['count']['dividedToDeviceCount'] }}</td>
                                <td>{{ $cert['count']['inactiveCount'] }}</td>
                                <td>{{ $cert['count']['availableCount'] }}</td>
                                <td><a href="{{ action('Mediaid\CertificatesController@getListCertificate') }}?company_id={{ $cert['alias'] }}" class="btn btn-default btn-primary ng-scope">詳細</a></td>
                            </tr>
                            @endforeach

                        </tbody>
                    </table>
                    <nav id="pagination">
                        @include('shared.pagination', ['paginator' => $paginate])

                    </nav>
                    <a href="{{ action('Mediaid\CertificatesController@getListCertificate') }}" class="btn btn-default btn-primary ng-scope">発行済端末証明書一覧</a>
                </div>

                <!-- /.panel -->
            </div>
            <!-- /.col-lg-12 -->
        </div>

    </div>
    <!-- /#page-wrapper -->

@endsection