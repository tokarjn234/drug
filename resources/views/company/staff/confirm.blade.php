@extends('layouts.company')

@section('title', 'スタッフアカウント管理')

@section('content')

    <div id="page-wrapper">
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-12">
                <div class="staff-create-info" style="border: none">
                    <p>{!! isset($mess)?$mess:session('mess') !!}</p>
                </div>

                <div class="panel-body body-search create-staff-id">
                    <form>
                        {{ csrf_field() }}
                        <div>
                            <table class="tbl-staff-create" width="100%">
                                <tr>
                                    <th>アカウントID</th>
                                    <td><input name="member_id" class="form-control display-inline" readonly type="text"
                                               placeholder=""
                                               value="{!! empty($idStaff)? session('idStaff'):$idStaff !!}"></td>
                                </tr>

                                <tr>
                                    <th>初期パスワード</th>
                                    <td><input name="member_id" class="form-control display-inline" readonly type="text"
                                               placeholder=""
                                               value="{!! empty($pass)?session('password'):$pass !!}"></td>
                                </tr>

                                <tr>
                                    <th>本部ID</th>
                                    <td><input name="company_id" class="form-control display-inline" readonly
                                               type="text"
                                               placeholder=""
                                               value="{!! empty($company_id)?session('company_id'):$company_id !!}">
                                    </td>
                                </tr>
                            </table>
                        </div>

                        <div class="clearfix">
                            <div class="center-block text-center">
                                <a href="{{ action('Company\StaffsController@getIndex') }}"
                                   class="btn btn-primary btn-lg">戻る</a>
                                <button id="staff-detail" class="btn btn-primary btn-lg" type="button"
                                        onclick="PrintElem('#mydiv')">印刷する
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!-- /.col-lg-12 -->
        </div>
    </div>
    <!-- /#page-wrapper -->

    <div id="mydiv" class="hidden">
        <table>
            <tr>
                <th width="40%" style="background-color: white;">アカウントID</th>
                <td>{!! empty($idStaff)? session('idStaff'):$idStaff !!}</td>
            </tr>
            <tr>
                <th>初期パスワード</th>
                <td>{!! empty($pass)?session('password'):$pass !!}</td>
            </tr>
            <tr>
                <th>本部ID</th>
                <td>{!! empty($company_id)?session('company_id'):$company_id !!}</td>
            </tr>
        </table>
    </div>

    <script type="text/javascript">

        function PrintElem(elem) {
            Popup($(elem).html());
        }

        function Popup(data) {
            var mywindow = window.open('', 'Detail Staff', 'height=600,width=800');
            mywindow.document.write('<html><head><title></title>');
            /*optional stylesheet*/
            {{--mywindow.document.write({!!HTML::style('style.css')!!});--}}
            mywindow.document.write('<style>*{padding: 0;margin: auto;} body{margin: 0 25px;} table{border-collapse: collapse;width: 90%;margin-top: 20px; background-color: white !important;} table, th, td {border: 1px solid #000;vertical-align: middle;text-align: center;padding: 15px;} table input{border: none; margin-left: 10%} .time-print{margin-bottom: 30px;margin-right: 10px;}</style>')
            mywindow.document.write('</head><body >');
            mywindow.document.write('<p align="right" class="time-print">発行日：{!! date('Y/m/d', time()) !!}</p>');
            mywindow.document.write('<p align="center">スマホ処方めーる<br />「初期パスワード」通知書</p>');
            mywindow.document.write('<p>スマホ処方メール管理画面をご利用いただくには、利用者登録をしていただく必要があります。以下の初期パスワードにて「スマホ処方めーる管理画面」にアクセスをし、登録を行ってください。<br />※「スマホ処方めーる管理画面」は、店舗内の端末証明書が導入されているPCからしかアクセスすることはできません。</p>');
            mywindow.document.write(data);
            mywindow.document.write('</body></html>');

            mywindow.print();
            mywindow.close();

            return true;
        }

    </script>
@endsection