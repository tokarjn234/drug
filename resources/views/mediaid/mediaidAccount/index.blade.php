@extends('layouts.mediaid')

@section('title', 'メディエイドアカウント管理')

@section('content')

    <div id="page-wrapper">
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-12 settings-user">
                <p><i class="fa fa-stop"></i> メディエイドアカウント管理</p>


                <table class="table table-striped table table-user">
                    <tr>
                        <th width="50px"></th>
                        <th>カナ氏名</th>
                        <th>氏名</th>
                        <th>アカウントＩＤ</th>
                        <th>最終ログイン日</th>
                        <th>ステータス</th>
                        <th width="100px">権限</th>
                        <th width="100px">パスワードリセット</th>
                        <th width="100px">アカウントロック</th>
                        <th width="100px">アカウント削除</th>
                    </tr>
                    @foreach($staffs as $key => $staff)

                        <tr class="{!! $staff['status'] != \App\Models\Staff::STATUS_DELETED ? '' : 'check-out'!!}">
                            <td>{!! ($staffs->currentPage()-1)*$staffs->perPage()+1+$key !!}</td>
                            <td>{{empty ($staff->first_name_kana) && empty ($staff->last_name_kana) ? '-' : $staff->first_name_kana . ' ' . $staff->last_name_kana}}</td>
                            <td>{{empty ($staff->first_name) && empty ($staff->last_name) ? '-' :  $staff->first_name . ' ' . $staff->last_name}}</td>
                            <td>{{$staff->username}}</td>
                            <td>{!!$staff->last_access_at ? (date('Y/m/d', strtotime($staff->last_access_at)) . '<br>' . date('H:i', strtotime($staff->last_access_at))) : '-'  !!}</td>
                            <td>{{@\App\Models\Staff::$statuses[$staff->status]}}</td>
                            <td>
                                @if($staff->authority == 1)
                                    管理者
                                @else
                                    スタッフ
                                @endif
                            </td>                                            
                            <td>
                                @if ($staff->status !== @\App\Models\Staff::STATUS_DELETED)                                    
                                    <button class="btn btn-primary staff-access1" data="{!! $staff['alias'] !!}">リセット</button>
                                @endif
                            </td>
                            <td>
                                @if ($staff->status !== @\App\Models\Staff::STATUS_DELETED && $staff->status == @\App\Models\Staff::STATUS_ACCOUNT_LOCK)                                    
                                    <button style="background-color: yellow; color: black;" class="btn btn-primary staff-access4" data="{!! $staff['alias'] !!}">解除</button>
                                @endif
                                @if ($staff->status !== @\App\Models\Staff::STATUS_DELETED && $staff->status != @\App\Models\Staff::STATUS_ACCOUNT_LOCK)                                    
                                    <button  class="btn btn-primary staff-access2" data="{!! $staff['alias'] !!}">ロック</button>
                                @endif
                            </td>
                            <td>
                                @if ($staff->status !== @\App\Models\Staff::STATUS_DELETED)                                                 
                                    <button class="btn btn-primary staff-access3" data="{!! $staff['alias'] !!}">削除</button>
                                @endif
                            </td>

                        </tr>
                    @endforeach
                    <tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        
                        <td></td>
                        <td></td>
                        <td>
                            <a href="{{action('Mediaid\MediaidAccountsController@getCreate')}}" class="btn btn-primary">新規作成</a>
                        </td>
                        <td>

                        </td>
                        <td>

                        </td>
                        <td></td>

                    </tr>
                    </tr>
                </table>
                <nav id="pagination">
                    @include('shared.pagination', ['paginator' => $staffs])

                </nav>

            </div>
            <!-- /.col-lg-12 -->
        </div>
    </div>

    <div class="modal fade popup-cmn popview2" id="ConfirmDialog1" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="panel panel-info cont-temp">
                        <div class="panel-body">
                            <p>パスワードをリセットしますか？</p>
                        </div>
                        <!--/cont-temp--></div>
                </div>
                <div class="modal-footer">
                    <div>
                        <button type="button" class="btn btn-default" data-dismiss="modal">キャンセル</button>
                        <button class="btn btn-info btn-update" data-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade popup-cmn popview2" id="ConfirmDialog2" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="panel panel-info cont-temp">
                        <div class="panel-body">
                            <p>アカウントをロックしますか？</p>
                        </div>
                        <!--/cont-temp--></div>
                </div>
                <div class="modal-footer">
                    <div>
                        <button type="button" class="btn btn-default" data-dismiss="modal">キャンセル</button>
                        <button class="btn btn-info btn-update" data-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade popup-cmn popview2" id="ConfirmDialog4" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="panel panel-info cont-temp">
                        <div class="panel-body">
                            <p>アカウントロックを解除しますか？</p>
                        </div>
                        <!--/cont-temp--></div>
                </div>
                <div class="modal-footer">
                    <div>
                        <button type="button" class="btn btn-default" data-dismiss="modal">キャンセル</button>
                        <button class="btn btn-info btn-update" data-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade popup-cmn popview2" id="ConfirmDialog3" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="panel panel-info cont-temp">
                        <div class="panel-body">
                            <p>アカウントを削除しますか？</p>
                        </div>
                        <!--/cont-temp--></div>
                </div>
                <div class="modal-footer">
                    <div>
                        <button type="button" class="btn btn-default" data-dismiss="modal">キャンセル</button>
                        <button class="btn btn-info btn-update" data-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <form action="{!! action('Mediaid\MediaidAccountsController@postDelete') !!}" id="form-update-status" method="post">
        {{csrf_field()}}
        <input id="form-stt" type="hidden" name="stt" value="" />
        <input id="form-id" type="hidden" name="id" value="" />
    </form>

    <script>

        $(document).ready(function () {
            var alias = '';
            var stt = '';
            $(".staff-access1").click(function () {
                $('#ConfirmDialog1').modal('show');
                alias = $(this).attr('data');
                stt = 'changePass';
            });

            $(".staff-access2").click(function () {
                $('#ConfirmDialog2').modal('show');
                alias = $(this).attr('data');
                stt = 'lockAccount';
            });

            $(".staff-access3").click(function () {
                $('#ConfirmDialog3').modal('show');
                alias = $(this).attr('data');
                stt = 'delete';
            });
            $(".staff-access4").click(function () {
                $('#ConfirmDialog4').modal('show');
                alias = $(this).attr('data');
                stt = 'unLockAccount';
            });

            $(".btn-update").click(function () {
                $('#form-id').val(alias);
                $('#form-stt').val(stt);
                $('#form-update-status').submit();

            });
        });

    </script>
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