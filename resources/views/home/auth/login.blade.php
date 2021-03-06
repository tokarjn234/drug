@extends('layouts.login')

@section('content')
    <?php //pr(session()->all()); ?>
    <div class="row">
        <div class="col-md-4 col-md-offset-4">
            <div class="login-panel panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">{{__('PleaseLogin')}}</h3>
                </div>
                <div class="panel-body">
                    <form role="form" method="POST" action="">
                        {{ csrf_field() }}
                        <fieldset>
                            <div class="form-group pb15">
                                <input required value="{{Input::old('username')}}" class="form-control"
                                       placeholder="{{__('StaffID')}}" name="username" type="text" autofocus>
                            </div>
                            <div class="form-group pb15">
                                <input required class="form-control" placeholder="{{__('Password')}}" name="password"
                                       type="password" value="">
                            </div>
                            <div class="form-group pb15">
                                <input required class="form-control" placeholder="{{__('Company ID')}}" name="company_id" type="number" min="0" value="{{Input::old('company_id')}}">
                            </div>
                            <!--<div class="checkbox">
                                <label>
                                    <input name="remember" type="checkbox" value="Remember Me">Remember Me
                                </label>
                            </div>-->


                            @if (count($errors) > 0)
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                            <button type="submit" class="btn btn-lg btn-info btn-block">{{ __('Login') }}</button>
                        </fieldset>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection