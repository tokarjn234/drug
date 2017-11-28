@extends('layouts.sb2admin')

@section('title', '設定')

@section('content')


    <div id="page-wrapper" class="settings_page">
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-12">
                <form action="{{action('Home\SettingsController@postSetting')}}" method="POST">
                    {{ csrf_field() }}
                    <div class="mtit"><i class="fa fa-stop"></i> 患者アプリ利用可能機能設定</div>
                    <table class="table table-striped">
                        <colgroup>
                            <col width="30%">
                            <col width="70%">
                        </colgroup>
                        <tr>
                            <th>営業時間外の処方せん画像送信可否</th>
                            <td>
                                <label>
                                    <?php echo Form::radio('acceptOrderOnNonBusinessHour', 1, $acceptOrderOnNonBusinessHour == 1, array($settingChangeOnStoreHour == 2 ? 'disabled' : '')) ?>
                                    送信可</label>
                                <label class="pl10">
                                    <?php echo Form::radio('acceptOrderOnNonBusinessHour', 2, $acceptOrderOnNonBusinessHour == 2, array($settingChangeOnStoreHour == 2 ? 'disabled' : '')) ?>
                                    送信不可</label>

                                <p class="txtInfo">※患者アプリにて営業時間外に処方せん画像を当店舗宛てに送信しようとした場合に、拒否することのできる機能です。<br>送信可とした場合は、対応が翌営業日となることを患者さまにお知らせします。
                                </p>
                            </td>
                        </tr>
                        <tr>
                            <th>受取時間が夜間休日等加算対象時刻の場合のアラート表示</th>
                            <td>
                                <label>
                                    <?php echo Form::radio('showAlertAtNight', 1, $showAlertAtNight == 1, array($settingChangeOnStoreAtNight == 2 ? 'disabled' : '')) ?>
                                    表示する</label>
                                <label class="pl10">
                                    <?php echo Form::radio('showAlertAtNight', 2, $showAlertAtNight == 2, array($settingChangeOnStoreAtNight == 2 ? 'disabled' : '')) ?>
                                    表示しない</label>

                                <p class="txtInfo">※患者アプリにて受取希望時刻が夜間休日等加算対象時刻の場合に、手数料がかかる旨の表示をする機能です。</p>
                            </td>
                        </tr>
                        <tr class="{!! !$patientReplySettingMediaid?'color-block':'' !!}">
                            <th>薬局からのメッセージ送信時に患者からの返答可否</th>
                            <td>
                                <label>
                                    <?php echo Form::radio('patientReplySetting', 1, $patientReplySetting == 1, array($settingChangeOnStorePatientReply == 2 || !$patientReplySettingMediaid ? 'disabled' : '')) ?>
                                    返答可</label>
                                <label class="pl10">
                                    <?php echo Form::radio('patientReplySetting', 2, $patientReplySetting == 2, array($settingChangeOnStorePatientReply == 2 || !$patientReplySettingMediaid ? 'disabled' : '')) ?>
                                    1回のみ返答可</label>
                                <label class="pl10">
                                    <?php echo Form::radio('patientReplySetting', 3, $patientReplySetting == 3, array($settingChangeOnStorePatientReply == 2 || !$patientReplySettingMediaid ? 'disabled' : '')) ?>
                                    返答不可</label>

                                <p class="txtInfo">※薬局から患者アプリにメッセージを送信した際に、患者さまがアプリから返信することのできる機能です。</p>
                            </td>
                        </tr>

                    </table>
                    <button class="btn btn-primary new-regulations {!! $settingChangeOnStoreHour==2&&$settingChangeOnStorePatientReply==2&&$settingChangeOnStoreAtNight==2?'hidden':'' !!}"
                            type="submit">
                        更新
                    </button>
                </form>

                <div class="mtit"><i class="fa fa-stop"></i> メッセージテンプレート設定機能</div>

                <div class="bs-statistic-tabs " data-example-id="togglable-tabs">
                    <ul id="myTabs" class="nav nav-tabs" role="tablist">
                        <li class="{{ $settingActive === 2 ? 'active' : '' }}" role="presentation"><a id="home-tab"
                                                                                                      aria-expanded="false"
                                                                                                      aria-controls="home"
                                                                                                      data-toggle="tab"
                                                                                                      role="tab"
                                                                                                      href="#tab1">基本テンプレート</a>
                        </li>
                        <li class="{{ $settingActive === 1 ? 'active' : '' }}" role="presentation"><a id="home-tab"
                                                                                                      aria-expanded="false"
                                                                                                      aria-controls="home"
                                                                                                      data-url="/#tab2"
                                                                                                      data-toggle="tab"
                                                                                                      role="tab"
                                                                                                      href="#tab2">本部テンプレート</a>
                        </li>
                        <li class="{{ $settingActive === 3 ? 'active' : '' }}" role="presentation"><a id="profile-tab"
                                                                                                      aria-controls="profile"
                                                                                                      data-toggle="tab"
                                                                                                      data-url="/#tab3"
                                                                                                      role="tab"
                                                                                                      href="#tab3"
                                                                                                      aria-expanded="true">店舗登録テンプレー</a>
                        </li>
                        <li id="add" style="margin-bottom:3px" class="last">
                            <button type="button" class="btn btn-primary new-regulations"
                                    onClick="self.location='{!! action('Home\SettingsController@getAdd') !!}'">+新規作成
                            </button>
                        </li>
                    </ul>

                    <div id="myTabContent" class="tab-content">
                        <div id="tab1" class="tab-pane fade {{ $settingActive === 2 ? 'active in' : '' }}"
                             aria-labelledby="home-tab" role="tabpanel">
                            <table class="table table-striped table-center">
                                <tr>
                                    <th>＃</th>
                                    <th>メッセージ区分</th>
                                    <th>テンプレート名（必須）</th>
                                    <th>タイトル（50 文字まで）</th>
                                    <th>最終更新日時</th>
                                    <th>ステータス</th>
                                    <th>更新者</th>
                                </tr>

                                @foreach($SettingsTab1 as $index => $settingTab1)

                                    <tr data-id="{{$settingTab1['id']}}">
                                        <td>{{$index + 1}}</td>
                                        <td>
                                            {{@\App\Models\MessageTemplate::$messageTypes[$settingTab1['message_type']]}}
                                        </td>
                                        <td>
                                            <a href="{!! action('Home\SettingsController@getEdit',$settingTab1['alias']) !!}">{{ $settingTab1['name'] }}</a>
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
                        <div id="tab2" class="tab-pane fade {{ $settingActive === 1 ? 'active in' : '' }}"
                             aria-labelledby="profile-tab" role="tabpanel">
                            <table class="table table-striped table-center">
                                <tr>
                                    <th>＃</th>
                                    <th>メッセージ区分</th>
                                    <th>テンプレート名（必須）</th>
                                    <th>タイトル（50 文字まで）</th>
                                    <th class="updateTime">最終更新日時</th>
                                    <th>ステータス</th>
                                    <th>更新者</th>
                                </tr>

                                @foreach($SettingsTab2 as $index => $settingTab2)

                                    <tr data-id="{{$settingTab2['id']}}">
                                        <td>{{$index + 1}}</td>
                                        <td>
                                            {{@\App\Models\MessageTemplate::$messageTypes[$settingTab2['message_type']]}}
                                        </td>
                                        <td>
                                            <a href="{!! action('Home\SettingsController@getEdit',$settingTab2['alias']) !!}">{{ $settingTab2['name'] }}</a>
                                        </td>
                                        <td>{{ str_limit($settingTab2['title'], $limit = 50, $end = '...') }}</td>
                                        <td>{{ date('Y/m/d', strtotime($settingTab2['updated_at'])) }}
                                            <br/>{{date('H:i',strtotime($settingTab2['updated_at']))}}</td>
                                        <td>
                                            {{@\App\Models\MessageTemplate::$messageStatus[$settingTab2['status']]}}
                                        </td>

                                        <td>{{ $settingTab2['first_name'] }} {{ $settingTab2['last_name'] }}</td>

                                    </tr>
                                @endforeach
                            </table>
                            <!--/tab2--></div>
                        <div id="tab3" class="tab-pane fade {{ $settingActive === 3 ? 'active in' : '' }}"
                             aria-labelledby="profile-tab" role="tabpanel">
                            <table class="table table-striped table-center">
                                <tr>
                                    <th>＃</th>
                                    <th>メッセージ区分</th>
                                    <th>テンプレート名（必須）</th>
                                    <th>タイトル（50 文字まで）</th>
                                    <th>最終更新日時</th>
                                    <th>ステータス</th>
                                    <th>更新者</th>
                                </tr>

                                @foreach($SettingsTab3 as $index => $setting)

                                    <tr data-id="{{$setting['id']}}">
                                        <td>{{$index + 1}}</td>
                                        <td>
                                            {{@\App\Models\MessageTemplate::$messageTypes[$setting['message_type']]}}
                                        </td>
                                        <td>
                                            <a href="{!! action('Home\SettingsController@getEdit',$setting['alias']) !!}">{{ $setting['name'] }}</a>
                                        </td>
                                        <td>{{ str_limit($setting['title'], $limit = 50, $end = '...') }}</td>
                                        <td>{{ date('Y/m/d', strtotime($setting['updated_at'])) }}
                                            <br/>{{date('H:i',strtotime($setting['updated_at']))}}</td>
                                        <td>
                                            {{@\App\Models\MessageTemplate::$messageStatus[$setting['status']]}}
                                        </td>

                                        <td>{{ $setting['first_name'] }} {{ $setting['last_name'] }}</td>

                                    </tr>
                                @endforeach
                            </table>
                            <!--/tab3--></div>
                    </div>
                    <!--/END Tab--></div>
            </div>
            <!-- /.col-lg-12 -->
        </div>
    </div>
    <!-- /#page-wrapper -->



@endsection
