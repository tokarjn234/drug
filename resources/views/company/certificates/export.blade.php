<!DOCTYPE html>
<html lang="ja">
<head>
    <title>スマホ処方めーる管理画面
        端末証明書発行通知書（仮）</title>
    <style>
        .table{
            width: 100%;
            max-width: 100%;
            margin-bottom: 20px;
        }
        .table-bordered, .table-bordered th, .table-bordered td {
            border: 1px solid #ddd;
            border-collapse: collapse;

        }

        .table-bordered th {
            background-color: #ECE7E7;
        }


        @media  print
        {
            .no-print, .no-print *
            {
                display: none !important;
            }
        }

        .btn {
            display: inline-block;
            padding: 6px 12px;
            margin-bottom: 0;
            font-size: 14px;
            font-weight: 400;
            line-height: 1.42857143;
            text-align: center;
            white-space: nowrap;
            vertical-align: middle;
            -ms-touch-action: manipulation;
            touch-action: manipulation;
            cursor: pointer;
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
            background-image: none;
            border: 1px solid transparent;
            border-radius: 4px;

        }

    </style>
</head>
<body>
<div id="page-wrapper" >
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-12">
                <div class="dataTable_wrapper" style="width: 70%;margin:0 auto;">
                    <p align="center">
                        スマホ処方めーる管理画面<br>
                        端末証明書発行通知書（仮）

                    </p>
                    @if(!empty($store))
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="txt-center">店舗名</th>
                                <td align="center">{{$store->name}}</td>
                            </tr>
                        </thead>
                        <tbody>
                            <th>店舗コード</th>
                            <td align="center">{{$store->id}}</td>
                        </tbody>
                    </table>
                    @endif

                    @if(!empty($company))
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                            <tr>
                                <th class="txt-center">企業名</th>
                                <td align="center">{{$company->name}}</td>
                            </tr>
                        </thead>
                        <tbody>
                            <th>企業コード</th>
                            <td align="center">{{$company->id}}</td>
                        </tbody>
                    </table>
                    @endif

                    <table class="table table-striped table-bordered table-hover" >
                        <thead>
                        <tr>

                            <th class="txt-center">端末証明書番号</th>
                            <th class="txt-center">パスワード</th>
                            <th class="txt-center">証明書導入端末（店舗メモ）</th>


                        </tr>

                        </thead>
                        <tbody>
                            @foreach( $certificates as $cer)
                                <tr align="center">

                                    <td>{{$cer->ssl_client_s_dn_cn}}</td>
                                    <td>{{$cer->export_password}}</td>
                                    <td>{{$cer->name}}</td>
                                </tr>
                            @endforeach


                        </tbody>
                    </table>
                    <div class="no-print" align="center">
                        <button onclick="location.href = '{{action('Company\CertificatesController@getIndex')}}'" class="btn btn-primary btn-lg" type="button">戻る</button>
                        <button onclick="window.print()" class="btn btn-primary btn-lg" type="button">印刷</button>



                    </div>
                <!-- /.panel -->
            </div>
            <!-- /.col-lg-12 -->
        </div>

        <!--End Modal-->



        </div>
</div>

</body>
</html>