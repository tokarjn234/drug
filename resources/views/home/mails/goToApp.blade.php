@extends('layouts.none')

@section('content')

    <div id="wrapper">
        <div class="page-sentMail text-center">
            <div class="">
                <img class="center-block" src="{{url('/img/logo.png')}}" height="50"
                     style="margin-top:10px; margin-bottom: 20px;" align="middle">
                    <div class="name-company">{!! $cp_n !!}ドラッグ</div>
            </div>
            <div class="clearfix"></div>
            <p class="text-left">{!! $mess !!}</p>
            {{--@if (\App\Models\DebugLog::getOS(1))--}}
            <a href="ominext.drugorder.openapp://ominext.com/?flag={!! $flag !!}{!! isset($rem)?'&email='.$rem:'' !!}{!! isset($rid)?'&rid='.$rid:'' !!}{!! isset($patientReplySetting)?'&patientReplySetting='.$patientReplySetting:'' !!}{!! isset($namest)?'&namest='.urldecode($namest):'' !!}"
               class="btn btn-primary">スマホ処方メールに戻る</a>
            {{--@endif--}}
        </div>
    </div>

@endsection