@extends('layouts.mediaid')

@section('title', 'メッセージ管理')

@section('content')

	<div id="page-wrapper" class="settings_page">
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-12">
            <div class="mtit"><i class="fa fa-stop"></i> メッセージテンプレート設定機能</div>

                <div class="bs-statistic-tabs " data-example-id="togglable-tabs">
                    <ul id="myTabs" class="nav nav-tabs" role="tablist">
                        <li class="active" role="presentation"><a id="home-tab" aria-expanded="false"  aria-controls="home"  data-toggle="tab" role="tab" href="#tab1">基本テンプレート</a></li>
                        
                        <li id="add" style="margin-bottom:3px" class="last">
                            <button type="button" class="btn btn-primary new-regulations"
                                    onClick="self.location='{!! action('Mediaid\MessageTemplatesController@getAdd') !!}'">+新規作成
                            </button>
                        </li>
                    </ul>

                    <div id="myTabContent" class="tab-content">
                        <div id="tab1" class="tab-pane fade active in" aria-labelledby="home-tab" role="tabpanel">
                            <table class="table table-striped table-center">
                                <tr>
                                    <th>＃</th>
                                    <th style="width: 130px;">メッセージ区分</th>
                                    <th style="width: 180px;">テンプレート名（必須）</th>
                                    <th>タイトル</th>
                                    <th>最終更新日時</th>
                                    <th>ステータス</th>
                                    <th>更新者</th>
                                </tr>

                                @foreach($SettingsTab1 as $index => $settingTab1)

                                    <tr>
                                        <td>{{$index + 1}}</td>
                                        <td>
                                            {{@\App\Models\MessageTemplate::$messageTypes[$settingTab1['message_type']]}}
                                        </td>
                                        <td>
                                            <a href="{!! action('Mediaid\MessageTemplatesController@getEdit',$settingTab1['alias']) !!}">{{ $settingTab1['name'] }}</a>
                                        </td>
                                        <td>{{ str_limit($settingTab1['title'], $limit = 50, $end = '...') }}</td>
                                        <td>{{ date('Y/m/d', strtotime($settingTab1['updated_at'])) }}
                                            <br/>{{date('H:i',strtotime($settingTab1['updated_at']))}}</td>
                                        <td>
                                            {{@\App\Models\MessageTemplate::$messageStatus[$settingTab1['status']]}}

                                        </td>

                                        <td>{{ $settingTab1['first_name'] }} {{ $settingTab1['last_name'] }}</td>

                                    </tr>
                                @endforeach
                            </table>
                            <!--/tab1--></div>
                        
                    </div>
                    <!--/END Tab--></div>
            </div>
            <!-- /.col-lg-12 -->
        </div>
    </div>
    <!-- /#page-wrapper -->



@endsection