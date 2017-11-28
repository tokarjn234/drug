@extends('layouts.sb2adminnosidebar')

@section('content')

    <div id="wrapper">

        <!-- Navigation -->
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
            <div class="navbar-header">
                {{--<a class="navbar-brand bolder" href="javascript:void(0);">クリエイト薬局 青葉荏田西店</a>--}}
                <!-- <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button> -->

                <!-- /.navbar-top-links -->
            </div>
            <!-- /.navbar-header -->

        </nav>

        <div id="page-wrapper" class="default-page">
            <!-- /.row -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="container box-panel">
                        <div class="box-inner">
                            <div class="from-cmn form">
                                <p>変更後のパスワードを入力してください</p>
                                {!! Form::open(array('id' => 'form-reset', 'name' => 'User', 'action' => (array('Home\MailsController@postUpdatePassword')), 'method' => 'post')) !!}
                                <fieldset>
                                    <div class="form-group pb15 change-pass">
                                        <input type="hidden" name="id" value="{!! $id !!}"/>
                                        <input class="form-control" placeholder="新しいパスワード" name="password"
                                               type="password" autofocus id="input-pass">
                                        <button class="btn-show-pass"
                                                type="button"><img src="{{url('/img/ic_hiden_pass.png')}}"
                                                                   id="image-show-pass" width="25px"></button>
                                        <p class="note-pass">※６文字以上（英字と数字をそれぞれ１文字以上含めてください）</p>
                                    </div>
                                    @if (count($errors) > 0)
                                        <div class="alert alert-danger">
                                            <ul>

                                                <?php
                                                $err = $errors->all();
                                                ?>
                                                <li>{!! $err[0] !!}</li>

                                            </ul>
                                        </div>
                                    @endif
                                    @if (!empty(session('errs')))
                                        <div class="alert alert-danger">
                                            <ul>
                                                <li>{!! session('errs') !!}</li>
                                            </ul>
                                        </div>
                                    @endif
                                    <button type="submit" class="btn btn-lg btn-info btn-block w90-percent">変更</button>
                                </fieldset>
                                {!! Form::close() !!}
                            </div>
                        </div>
                        <!--/container--></div>
                </div>
                <!-- /.col-lg-12 -->
            </div>
        </div>
        <!-- /#page-wrapper -->
    </div>
    <!-- /#wrapper -->

    <script>
        $(function () {
            $('#image-show-pass').click(function () {
                var type = $('#input-pass').attr('type');
                if (type === 'password') {
                    $('#input-pass').attr('type', 'text');
                    var url = $('#image-show-pass').attr('src');
                    url = url.replace('ic_hiden_pass', 'icon-show-pass');
                    $('#image-show-pass').attr('src', url);
                }
                if (type === 'text') {
                    $('#input-pass').attr('type', 'password');
                    var url = $('#image-show-pass').attr('src');
                    url = url.replace('icon-show-pass', 'ic_hiden_pass');
                    $('#image-show-pass').attr('src', url);
                }
            });
        });
    </script>

@endsection