@extends('layouts.mediaid')

@section('title', '登録企業管理')

@section('content')

    <div id="page-wrapper">
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-12">
                <div class="margin_b20">
                    <div class="margin_t20">
                        <div><i class="fa fa-stop"></i> 企業検索</div>
                        <form id="searchForm" method="post"
                              action="{{action('Mediaid\CompaniesController@postIndex')}}">
                            {{ csrf_field() }}
                            <table class="" width="100%">
                                <tr>
                                    <th class="text-center" width="10%">企業名</th>
                                    <td width="30%">
                                        <input name="company_name" class="form-control display-inline" type="text"
                                               placeholder=""
                                               value="{!! isset($valueSearch['company_name'])?$valueSearch['company_name']:'' !!}">
                                    </td>
                                    <td></td>
                                    <td>
                                        <label><input name="show_all_cpn" type="checkbox"
                                                    {!! !isset($valueSearch['show_all_cpn'])?'':$valueSearch['show_all_cpn']=='on'?'checked':'' !!}>
                                            全企業を表示</label><br/>
                                    </td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td width="200px">
                                        <div class="center-block text-center">
                                            <button class="btn btn-primary btn-lg button-search" type="button"
                                                    id="submit-search" width="50%">検索
                                            </button>
                                        </div>
                                    </td>
                                    <td width="200px">
                                        <div class="center-block text-center">
                                            <a href="{!! action('Mediaid\CompaniesController@getIndex') !!}"
                                               class="btn btn-primary btn-lg">検索条件をクリア
                                            </a>
                                        </div>
                                    </td>
                                    <td></td>
                                </tr>
                            </table>

                            <div class="clearfix">

                            </div>
                        </form>
                    </div>

                    <div class="staff-list-info">
                        <i class="fa fa-stop"></i> 登録企業一覧
                    </div>

                    <div class="pull-right">
                        <div class="center-block text-center">
                            <a href="{!! action('Mediaid\CompaniesController@getCreate') !!}"
                               id="staff-create"
                               name="btn_reset"
                               class="btn btn-info">
                                +新規登録
                            </a>
                        </div>
                    </div>
                </div>
                <div class="clearfix"></div>

                <table id="dataTables-example" class="table table-striped table table-user margin_t20">
                    <thead>
                    <tr>
                        <th width="6%" rowspan="2">企業コード</th>
                        <th rowspan="2" width="15%">企業名</th>
                        <th colspan="3" width="34%">担当者情報</th>
                        <th rowspan="2">契約店舗数</th>
                        <th rowspan="2">スタッフアカウント数</th>
                        <th rowspan="2" width="10%">ステータス</th>
                        <th rowspan="2" width="10%">ステータス更新日</th>
                        <th rowspan="2" width="5%">詳細情報</th>

                    </tr>
                    <tr>
                        <th>部署名</th>
                        <th>担当者名</th>
                        <th class="min-w102">電話番号</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    if(!empty($companies)){
                    foreach($companies as $k=>$v){
                    ?>
                    <tr class="{!! $v['status']==\App\Models\Company::STATUS_CANCELLATION_COMPLETED?'check-out':'' !!}">
                        <td>{!! $v['id_cpn'] !!}</td>
                        <td>{!! $v['name'] !!}</td>
                        <td>{!! $v['meta_company']['division'] !!}</td>
                        <td>{!! $v['name_manager'] !!}</td>
                        <td width="50px">{!! $v['phone_number'] !!}</td>
                        <td>{!! $v['contract_store'] !!}</td>
                        <td>{!! $v['all_staff'] !!}</td>
                        <td>{!! $v['status_string'] !!}</td>
                        <td>{!! $v['updated_at'] !!}</td>
                        <td>
                            <a href="{!! action('Mediaid\CompaniesController@getDetail') !!}?id={!! $v['alias'] !!}"
                               class="btn btn-info"> 詳細</a>
                        </td>
                    </tr>
                    <?php }} ?>
                    </tbody>
                </table>
                <nav id="pagination">
                    @include('shared.pagination', ['paginator' => $paginate])
                </nav>

            </div>
            <!-- /.col-lg-12 -->
        </div>
    </div>
    <!-- /#page-wrapper -->
    <script>
        $('#submit-search').click(function () {
            $('#searchForm').submit();
        })

        //        $("#staff-create").click(function () {
        //            window.location.href = "/company/staffs/create";
        //        });
    </script>

@endsection