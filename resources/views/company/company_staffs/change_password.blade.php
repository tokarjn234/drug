@extends('layouts.company_login')
@section('title', 'パスワードを変更してください')
@section('content')

    <div class="row" ng-controller="AuthChangePasswordController" >
        <div class="col-md-4 col-md-offset-4">
            <div class="login-panel panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">パスワードを変更してください</h3>
                </div>
                <div class="panel-body">
                    <form id="form" role="form" method="POST" action="{{action('Company\CompanyStaffsController@postChangePassword')}}">
                        {{ csrf_field() }}
                        <input type="hidden" name="alias" value="{{$staff->alias}}">
                        <fieldset>
                            <div class="form-group pb15">
                                <div class="input-group">

                                    <input pattern=".{6,}" title="６文字以上" id="password" required class="form-control" placeholder="新しいパスワード" name="password" type="password" autofocus>
                                    <span id="showPassword" title="Show password" class="input-group-addon cursor-pointer"><i id="eye-icon" class="fa fa-eye"></i></span>
                                </div>

                                <br>
                                <label id="errorMsg">
                                    ※６文字以上（英字と数字をそれぞれ１文字以上含めてください）

                                </label>
                            </div>


                            <button type="submit" class="btn btn-lg btn-info btn-block">変更</button>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection