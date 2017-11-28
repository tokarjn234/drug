
<!DOCTYPE html>
<html lang="ja"  ng-app="mainApp">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="author" content="">
    <title>ログイン</title>
    <!-- Bootstrap Core CSS -->
    <script>
        @if(isset ($jsonData))
            var jsonData = JSON.parse('{!!addslashes(json_encode($jsonData))  !!}');
        @endif
    </script>
    {!! \App\Lib\Bundle::styles('main') !!}
    {!!\App\Lib\Bundle::scripts('main') !!}
    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->

</head>

<body style="background-color:#fff;">

<div id="wrapper">
    <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0">
        <div class="navbar-header" style="height:70px">
            <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            <a href="{{action('Home\OrdersController@getIndex')}}"><img class="img-logo" src="{{url('/img/logo.png')}}"  height="50" style="margin-top:10px"></a>
            <a class="navbar-brand bolder" style="margin-right: 240px;" href="">ログイン</a>
        </div>

    </nav>
    <div class="container">
        @yield('content')
    </div>           <!--/container-->
</div>


</body>

</html>
