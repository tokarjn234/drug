@extends('layouts.company')

@section('title', 'スタッフアカウント管理')

@section('content')

    <div id="page-wrapper">
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-12">
                <table id="dataTables-example" class="table table-user" style="width: 60%;margin: 30px auto">
                    <tr>
                        <th width="200px">氏名（漢字）</th>
                        <td>{!! $staff['$name'] !!}</td>
                    </tr>
                    <tr>
                        <th width="200px">氏名（カナ）</th>
                        <td>{!! $staff['$name_kana'] !!}</td>
                    </tr>

                    <tr>
                        <th width="200px">職種</th>
                        <td>{!! @\App\Models\Staff::$jobCategory[$staff['job_category']] !!}</td>
                    </tr>
                    <tr>
                        <th width="200px">役職</th>
                        <td>{!! $staff['position'] !!}</td>
                    </tr>
                    <tr>
                        <th width="200px">アカウントID</th>
                        <td>{!! $staff['username'] !!}</td>
                    </tr>
                    <tr>
                        <th width="200px">ステータス</th>
                        <td>{!! $staff['status_string'] !!}</td>
                    </tr>

                </table>
                <form>
                    {{ csrf_field() }}
                    <div class="clearfix">
                        <div class="center-block text-center">
                            <button id="staff-access1" class="btn btn-info btn-lg" type="submit">アカウント削除</button>
                            <button id="staff-access2" class="btn btn-info btn-lg"
                                    type="submit">{!! $staff['status']==\App\Models\Staff::STATUS_ACCOUNT_LOCK?'アカウントロック解除':'アカウントロック' !!}
                            </button>
                            <button id="staff-access3" class="btn btn-info btn-lg" type="submit">パスワードリセット</button>
                        </div>
                    </div>
                </form>
            </div>
            <!-- /.col-lg-12 -->
        </div>
    </div>
    <!-- /#page-wrapper -->

    <div class="modal fade popup-cmn popview2" id="ConfirmDialog1" tabindex="-1" role="dialog"
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
                        <a href="{!! action('Company\StaffsController@getDelete').'?stt=del' !!}"
                           class="btn btn-info btn-delete">OK</a>
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
                            <p>{!! $staff['status'] == \App\Models\Staff::STATUS_ACCOUNT_LOCK ? 'アカウントロックを解除しますか？' : 'アカウントをロックしますか？' !!}</p>
                        </div>
                        <!--/cont-temp--></div>
                </div>
                <div class="modal-footer">
                    <div>
                        <button type="button" class="btn btn-default" data-dismiss="modal">キャンセル</button>
                        <a href="{!! action('Company\StaffsController@getDelete').'?stt=' !!}{!! $staff['status']==\App\Models\Staff::STATUS_ACCOUNT_LOCK?'unlock':'lock' !!}"
                           type="button" class="btn btn-info btn-delete">OK</a>
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
                            <p>パスワードをリセットしますか？</p>
                        </div>
                        <!--/cont-temp--></div>
                </div>
                <div class="modal-footer">
                    <div>
                        <button type="button" class="btn btn-default" data-dismiss="modal">キャンセル</button>
                        <button type="button" class="btn btn-info btn-update">OK</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>

        $(document).ready(function () {
            $("#staff-access1").click(function () {
                $('#ConfirmDialog1').modal('show');
            });

            $("#staff-access2").click(function () {
                $('#ConfirmDialog2').modal('show');
            });

            $("#staff-access3").click(function () {
                $('#ConfirmDialog3').modal('show');
            });

            $(".btn-update").click(function () {
                window.location.href = "/company/staffs/new-password?id={!! $staff['alias'] !!}";
            });
        });

    </script>

@endsection