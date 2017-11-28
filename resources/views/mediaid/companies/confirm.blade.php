@extends('layouts.mediaid')

@section('title', '登録企業管理')

@section('content')
    <?php
    $company = session('company');
    $staff = session('staff');
    ?>
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
                                <colgroup>
                                    <col width="35%">
                                    <col width="65%">                           
                                </colgroup>
                                <tr>
                                    <th>企業コード</th>
                                    <td><input name="" class="form-control display-inline" readonly type="text"
                                               placeholder=""
                                               value="{!! $company['id'] !!}"></td>
                                </tr>

                                <tr>
                                    <th>企業名</th>
                                    <td><input name="" class="form-control display-inline" readonly type="text"
                                               placeholder=""
                                               value="{!! $company['name'] !!}"></td>
                                </tr>
                            </table>
                        </div>
                        <div>
                            <table class="tbl-staff-create" width="100%">
                                <colgroup>
                                    <col width="35%">
                                    <col width="65%">                           
                                </colgroup>
                                <tr>
                                    <th>本部画面管理者アカウントID</th>
                                    <td><input name="" class="form-control display-inline" readonly type="text"
                                               placeholder=""
                                               value="{!! $staff['username'] !!}"></td>
                                </tr>

                                <tr>
                                    <th>初期パスワード</th>
                                    <td><input name="" class="form-control display-inline" readonly type="text"
                                               placeholder=""
                                               value="{!! $staff['password'] !!}"></td>
                                </tr>
                            </table>
                        </div>

                        <div class="clearfix">
                            <div class="center-block text-center">
                                <a href="{{ action('Mediaid\CompaniesController@getIndex') }}" class="btn btn-primary btn-lg">戻る</a>
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
                <th width="40%" style="background-color: white;">企業コード</th>
                <td>{!! $company['id'] !!}</td>
            </tr>
            <tr>
                <th>本部画面URL</th>
                <td>{{ action('Company\AuthController@login') }}</td>
            </tr>
            <tr>
                <th>店舗画面URL</th>
                <td>{{ action('Home\AuthController@login') }}</td>
            </tr>
            <tr>
                <th>本部画面管理者<br />
                    アカウントID</th>
                <td>{!! $staff['username'] !!}</td>
            </tr>
            <tr>
                <th>初期パスワード</th>
                <td>{!! $staff['password'] !!}</td>
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
            mywindow.document.write('<p align="center">スマホ処方めーる<br />利用開始通知書</p>');
            mywindow.document.write('<p>スマホ処方メールのご利用開始ありがとうございます。<br />アカウントの作成が完了しましたので、以下の通りご連絡いたします。<br />以下の「スマホ処方めーる管理画面」にアクセスいただき、<br />利用開始のための設定を行ってください。</p>');
            mywindow.document.write(data);
            mywindow.document.write('</body></html>');

            mywindow.print();
            mywindow.close();

            return true;
        }

    </script>
@endsection