<!DOCTYPE html>
<html ng-app="mainApp">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title')</title>
    <!-- Bootstrap Core CSS -->
    <script>
        var msgNotifyCheckUrl = '{{action('Home\MessagesController@postCheckNewMessages')}}';
        var jsonData = {};

        @if (isset ($jsonData))
            jsonData = JSON.parse('{!!addslashes(json_encode($jsonData))  !!}');
        @endif
    </script>
    
        {!!\App\Lib\Bundle::scripts('main') !!}
        {!!\App\Lib\Bundle::styles('main') !!}


            <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <!--<script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>-->
    {{--<script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>--}}
    {{--<![endif]-->--}}
   
</head>
<body>
<div id="wrapper" ng-controller="MainController as Main">
    <!-- Navigation -->
    <!-- Navigation -->
    <nav class="navbar navbar-default navbar-fixed-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header" style="height:70px">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a href="{{action('Home\OrdersController@getIndex')}}"><img class="img-logo" src="{{url('/img/logo.png')}}"   height="50" style="margin-top:10px"></a>
            <a class="navbar-brand bolder" href="">{{\App\Models\Store::current('name')}}</a>
            
            <ul class="nav navbar-top-links navbar-right" >
                <li style="line-height:70px;color:black;">{{ Auth::user()->first_name }}{{ Auth::user()->last_name }}さん</li>
                <li class="dropdown" >
                    <a class="dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="fa fa-user fa-fw"></i> <i class="fa fa-caret-down"></i>
                    </a>
                    <ul class="dropdown-menu dropdown-user">
                        <li><a href="{{action('Home\AuthController@profile')}}"><i class="fa fa-user fa-fw"></i>プロフィール</a></li>
                        <li><a href="{{action('Home\AuthController@logout')}}"><i class="fa fa-sign-out fa-fw"></i> ログアウト</a></li>
                    </ul>
                    <!-- /.dropdown-user -->
                </li>
                <!-- /.dropdown -->
            </ul>
            <!-- /.navbar-top-links -->
        </div>
        <!-- /.navbar-header -->


        <div class="navbar-default sidebar" role="navigation">
            <div class="sidebar-nav navbar-collapse">
                <ul class="nav" id="side-menu">
                    <li>
                        <a href="{!! action('Home\OrdersController@getIndex') !!}"><i class="fa fa-suitcase fa-fw"></i> 受信履歴管理</a>
                    </li>
                    <li>
                        <a  href="{!! action('Home\MessagesController@getIndex') !!}"><i class="glyphicon glyphicon-comment"></i> <span ng-bind="msgMenuText">処方せんメッセージ管理</span></a>
                    </li>
                    <li>
                        <a id="menu-current-view" href="{{url('stores/index')}}"><i class="fa  fa-folder-open fa-fw"></i> 集計</a>
                    </li>
                    <li>
                        <a href="{!! action('Home\StoresController@getShow') !!}"><i class="fa fa-save fa-fw"></i> 店舗情報管理</a>
                    </li>
                    <li>
                        <a href="{!! action('Home\UsersController@getIndex') !!}"><i class="fa fa-file-text fa-fw"></i> 会員一覧</a>
                    </li>
                    <li><a href="{!! action('Home\SettingsController@getIndex') !!}"><i class="fa fa-wrench fa-fw"></i> 設定</a></li>
                </ul>
            </div>
            <!-- /.sidebar-collapse -->
        </div>
        <!-- /.navbar-static-side -->
    </nav>
    @yield('content')
</div>

</body>
</html>