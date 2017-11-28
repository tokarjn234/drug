@extends('layouts.certificate')
@section('title', 'パスワードを変更してください')
@section('content')

    <div class="row" ng-controller="AuthCertificatesIssueController">


        <div class="col-lg-12">
            <p class="">
                この端末証明書を導入する端末名を記載してください。
                ※ 「スマホ処方めーる管理画面　端末証明書発行通知書」の「証明書導入端末」欄にも同様に記載をしてください。
            </p>
            <form id="issueForm" role="form" ng-submit="downloadCert($event)"  method="POST" action="{{action('Mediaid\AuthController@certificates')}}">
                {{csrf_field()}}

                <input type="hidden" name="next" value="export_cert">
                <input type="hidden" name="cert_alias" value="{{$cert->alias}}">
            <table class="table table-striped no-hover-color">


                <tbody>
                <tr>
                    <th class="txt-center">証明書番号</th>
                    <td class="txt-center">{{$cert->ssl_client_s_dn_cn}}</td>


                </tr>
                <tr>
                    <th class="txt-center">証明書導入端末<br>※必須
                    </th>
                    <td class="txt-center">
                        <input required class="form-control" name="cert_name"><br/>
                        <p style="text-align: left;">（例）受付端末1</p>
                    </td>


                </tr>
            </table>
                <div class="txt-center">


                        <a href="{{action('Mediaid\AuthController@certificates', ['_'=>time()])}}" class="btn btn-primary">キャンセル</a>

                    <button id="submitBtn" class="btn btn-primary" type="submit">端末証明書を導入する</button>
                </div>

            </form>
        </div>

        <div class="modal fade popup-cmn" id="infoDialog" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document" style="width: 840px;">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="title-popup" ></div>
                        <div class="panel panel-info cont-temp" style="margin-bottom: -10px;">

                            <div class="panel-body" >

                                証明書をインストールしてから、以下の「ログイン」ボタンをお押し下さい。
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div>
                            <button ng-click="goToSecuredLogin()" type="button" class="btn btn-info"> ログイン</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection