@extends('layouts.layout_message')

@section('title', '処方せんメッセージ管理')

@section('content')
<div id="page-wrapper" class="message">
        <div class="row" style="margin-top: 70px;">
               <br>
                <div class="alert alert-success">
                        <strong> {{__('ThereIsNoOrder')}}</strong>
                </div>

        </div>
</div>
@endsection
