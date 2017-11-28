@extends('layouts.company')

@section('title', 'スタッフアカウント管理')

@section('content')

    <div id="page-wrapper">
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-12">
                <div class="panel-body body-search">
                    <form id="searchForm" method="post" action="{{action('Company\StaffsController@postIndex')}}">
                        {{ csrf_field() }}
                        <table class="tbl-search">
                            <colgroup>
                                <col width="11%">
                                <col width="14%">
                                <col width="11%">
                                <col width="14%">
                                <col width="11%">
                                <col width="14%">
                                <col width="11%">
                                <col width="12%">
                            </colgroup>
                            <tr>
                                <th>店舗名</th>
                                <td><input name="store_name" class="form-control display-inline" type="text"
                                           placeholder=""
                                           value="{!! isset($valueSearch['store_name'])?$valueSearch['store_name']:'' !!}">
                                </td>
                                <td align="right">スタッフ氏名&nbsp;&nbsp;</td>
                                <td><input name="staff_name" class="form-control" type="text" placeholder=""
                                           value="{!! isset($valueSearch['staff_name'])?$valueSearch['staff_name']:'' !!}">
                                </td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>

                            </tr>
                        </table>

                        <div class="clearfix">
                            <div class="center-block text-center">
                                <button class="btn btn-primary btn-lg" type="button" id="submit-search">検索</button>
                                {{--<button name="btn_reset" class="btn btn-primary btn-lg" type="submit">検索条件をクリア</button>--}}
                                <label><input name="deleted_staff" type="checkbox"
                                              id="checkbox-delete" {!! $valueSearch['deleted_staff']=='on'?'checked':'' !!}>
                                    削除済を除く</label>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="staff-list-info">
                    <p>導入店舗数：{!! $number['store_introduction'] !!}</p>

                    <p>利用可能アカウント数：{!! $number['all_staff'] !!}</p>

                    <p>発行済アカウント数：{!! $number['staff_outstanding'] !!}</p>

                    <p>削除済アカウント数：{!! $number['staff_deleted'] !!}</p>

                    <p><b>残アカウント数：{!! $number['staff_available'] !!}</b></p>

                </div>

                <div class="staff-button-create">
                    <div class="center-block text-center">
                        <a href="{!! $number['staff_available']>0? action('Company\StaffsController@getCreate'):'' !!}"
                           id="staff-create"
                           name="btn_reset"
                           class="btn btn-info btn-lg {!! $number['staff_available']<=0?'disabled':'' !!}">
                            新規アカウント発行
                        </a>
                    </div>
                </div>

                <table id="dataTables-example" class="table table-striped table table-user">
                    <tr>
                        <th></th>
                        <th>カナ氏名</th>
                        <th>氏名</th>
                        <th>職種</th>
                        <th>役職</th>
                        <th>アカウントＩＤ</th>
                        <th width="100px">最終ログイン日</th>
                        <th>最終ログイン店舗</th>
                        <th>ステータス</th>
                        <th width="100px">詳細</th>
                    </tr>
                    <?php
                    if (!empty($staff)) {
                    foreach ($staff as $key => $value) {
                    ?>
                    <tr class="{!! $value['status'] != \App\Models\Staff::STATUS_DELETED ? '' : 'check-out'!!}">
                        <td>{!! ($paginate->currentPage()-1)*$paginate->perPage()+1+$key !!}</td>
                        <td>
                            {!! $value['$name_kana'] !!}
                        </td>
                        <td>
                            {!! $value['$name'] !!}
                        </td>
                        <td>{!! empty($value['job_category'])?'-':$value['job_category'] !!}</td>
                        <td>{!! empty($value['position'])?'-':$value['position'] !!}</td>
                        <td>{!! $value['username'] !!}</td>
                        <td>{!! $value['created_at_staff_24h'] !!}</td>
                        <td>{!! $value['last_view_store'] !!}</td>
                        <td>{!! $value['status_string'] !!}</td>
                        <td>
                            <?php
                            if($value['status'] != \App\Models\Staff::STATUS_DELETED){
                            ?>
                            <a href="{!! action('Company\StaffsController@getDetail').'?id='.$value['alias'] !!}"
                               class="btn btn-info">確認</a>
                            <?php
                            }else{
                            ?>
                            <button class="btn btn-info disabled">確認</button>
                            <?php
                            }
                            ?>
                        </td>
                    </tr>
                    <?php
                    }
                    }
                    ?>
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