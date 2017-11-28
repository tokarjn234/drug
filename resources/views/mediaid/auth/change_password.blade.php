@extends('layouts.mediaid_login')
@section('title', 'パスワードを変更してください')
@section('content')

    <div class="row" ng-controller="AuthChangePasswordController">
        <div class="col-md-4 col-md-offset-4">
            <div class="login-panel panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        @if( !$haveToChangePass && $mediaidCreated['last_change_password'] != null )
                            パスワードの有効期限が切れました。<br/>
                            パスワードを変更してください。
                        @else
                            パスワードを変更してください
                        @endif
                    </h3>
                </div>
                <div class="panel-body">
                    <form id="form" role="form" method="POST"
                          action="{{action('Mediaid\AuthController@changePassword')}}">
                        {{ csrf_field() }}
                        <fieldset>
                            <div class="form-group pb15">
                                <div class="input-group">

                                    <input pattern=".{6,}" title="６文字以上" id="password" required class="form-control"
                                           placeholder="新しいパスワード" name="password" type="password" autofocus value="{{Input::old('password')}}">
                                    <span id="showPassword" title="Show password"
                                          class="input-group-addon cursor-pointer"><i id="eye-icon"
                                                                                      class="fa fa-eye"></i></span>
                                </div>

                                <br>
                                <label id="errorMsg">
                                    ※６文字以上（英字と数字をそれぞれ１文字以上含めてください）

                                </label>
                                @if (count($errors) > 0)
                                    <div class="alert alert-danger">
                                        <ul>
                                            @foreach ($errors->all() as $error)
                                                <li>{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                            </div>


                            <button type="submit" class="btn btn-lg btn-info btn-block">変更</button>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection