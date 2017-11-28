@extends('layouts.company')

@section('title', 'スタッフアカウント管理')

@section('content')

    <div id="page-wrapper">
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-12">
                <div class="staff-create-info staff-create-id text-left">
                    <p>■アカウントID（※）を入力し、「アカウント発行」ボタンを押してください。</p>

                    <p>特定のアカウントIDを指定しない場合は、入力せずに「アカウント発行」ボタンを押してください。自動的に作成します。</p>

                    <p>※アカウントID：薬局画面にログインするときのID</p>
                </div>

                <div class="panel-body body-search create-staff-id">
                    <form id="formCreateId" method="post" action="create-id-staff">
                        {{ csrf_field() }}
                        <table class="tbl-staff-create table-create-id-staff">
                            <tr>
                                <th>アカウントID</th>
                                <td><input name="member_id" class="form-control display-inline number-only" type="text" id="newId"
                                           placeholder="{!! $new_id !!}" onblur="setValueRand('newId')"
                                           value="{!! empty(session('new_id'))?$new_id:session('new_id') !!}" required
                                           maxlength="7"></td>
                            </tr>
                            <tr>

                                @if (count($errors) > 0)
                                    <td colspan="2">
                                        <div class="alert alert-danger">
                                            <ul>
                                                {{--@foreach ($errors->all() as $error)--}}
                                                {{--<li>{{ $errors[0] }}</li>--}}
                                                {{--@endforeach--}}
                                                <?php
                                                $err = $errors;
                                                ?>
                                                <li>{!! $err[0] !!}</li>

                                            </ul>
                                        </div>
                                    </td>
                                @endif
                            </tr>
                        </table>

                        <div class="clearfix">
                            <div class="center-block text-center">

                                <a href="{!! action('Company\StaffsController@getIndex') !!}"
                                   class="btn btn-primary btn-lg">キャンセル</a>
                                <button id="account-issue" class="btn btn-primary btn-lg" type="button">アカウント発行</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /.col-lg-12 -->
        </div>
    </div>
    <!-- /#page-wrapper -->

    <div class="modal fade popup-cmn popview2" id="ConfirmDialog" tabindex="-1" role="dialog"
         aria-labelledby="myModalLabel">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="panel panel-info cont-temp">
                        <div class="panel-body">
                            <p>アカウントを発行しますか？</p>
                        </div>
                        <!--/cont-temp--></div>
                </div>
                <div class="modal-footer">
                    <div>
                        <button class="btn btn-default" data-dismiss="modal">キャンセル</button>
                        <button class="btn btn-info btn-update">OK</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>

        $(document).ready(function () {
            $("#account-issue").click(function () {
                $('#ConfirmDialog').modal('show');
            });

            $(".btn-update").click(function () {
                $('#formCreateId').submit();
            });
        });

        $(".number-only").keydown(function (e) {
            // Allow: backspace, delete, tab, escape, enter and .
            if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110]) !== -1 ||
                        // Allow: Ctrl+A, Command+A
                    (e.keyCode == 65 && ( e.ctrlKey === true || e.metaKey === true ) ) ||
                        // Allow: home, end, left, right, down, up
                    (e.keyCode >= 35 && e.keyCode <= 40)) {
                // let it happen, don't do anything
                return;
            }
            // Ensure that it is a number and stop the keypress

            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });

    </script>

@endsection