@extends('layouts.certificate')
@section('title', 'パスワードを変更してください')
@section('content')

    <div class="row" >


        <div class="col-lg-12">
            <p class="txtNote clearfix">スマホ処方めーる管理画面は、端末証明書が導入されていないパソコンからご利用いただくことはできません。<br />現在お使いいただいている端末でスマホ処方めーる管理画面をお使いいただく場合は、「スマホ処方めーる管理画面　端末証明書発行通知書（仮）」に記載されている「店舗コード」を入力し、「次へ」を押してください。<br />※「スマホ処方めーる管理画面　端末証明書発行通知書」は、本部より配布されます。詳しくは管理者にお問い合わせください。
            </p>
            <div class="col-md-4 col-md-offset-4">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">店舗コード</h3>
                    </div>
                    <div class="panel-body">
                        <form role="form" method="POST" action="{{action('Home\AuthController@certificates')}}">
                            {{ csrf_field() }}
                            <input type="hidden" name="next" value="certificates_list">
                            <fieldset>
                                <div class="form-group pb15">
                                    <input required class="form-control" placeholder="店舗コード" name="store_id" type="text" autofocus>
                                </div>

                                @if (count($errors) > 0)
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                
                                <button type="submit" class="btn btn-lg btn-info btn-block">次へ</button>
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection