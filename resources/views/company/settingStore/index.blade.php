@extends('layouts.company')

@section('title', 'スタッフアカウント管理')

@section('content')

    <div id="page-wrapper" class="settings_page">
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-12 settings-user">
                <p><i class="fa fa-stop"></i> 店舗画面設定<br/>※店舗スタッフアカウントのパスワードの有効期限を設定してください。有効期限になると、ログイン時にパスワードリセット画面が表示されます。</p>

                <form action="{{action('Company\SettingStoresController@postSettingStaff')}}" method="POST">
                    {{ csrf_field() }}
                    <table class="table table-striped table table-user table-settings-user-company-2">
                        <tr>
                            <th width="30%">パスワード有効期限</th>
                            <td>
                                <div class="select-setting-store text-left">
                                    <?php
                                    $arr = [
                                        '30days' => '1か月（30日）',
                                        '60days' => '2か月（60日）',
                                        '90days' => '3ヶ月（90日）',
                                        '120days' => '4か月（120日）',
                                        '150days' => '5ヶ月（150日）',
                                        '180days' => '6か月（180日）'
                                    ];

                                    ?>
                                    {!! Form::select('password_expire', $arr, $staffLoginSetting->password_expire, ['class' => 'form-control','style'=>'width: 200px; margin-bottom: 15px;','id'=>'select-time-setting'])  !!}
                                </div>
                                <div class="text-left">
                                    ※医療情報システムの安全管理に関するガイドライン（厚生労働省）では、医療情報にアクセスするシステムのパスワードは<span
                                            id="time-setting">{!! $arr[$staffLoginSetting->password_expire] !!}</span>以内に変更するよう求められています。
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>1アカウントの2重ログイン</th>
                            <td>
                                <div class="text-left">
                                    <label>
                                        {!! Form::radio('multi_account_login', 1, $staffLoginSetting->multi_account_login) !!}
                                        許可
                                    </label>
                                    <label>
                                        {!! Form::radio('multi_account_login', 0, !$staffLoginSetting->multi_account_login) !!}
                                        不可
                                    </label>

                                </div>
                                <div class="text-left">※一つのスタッフアカウントにて複数のPC端末の管理画面にログインすることを許可する設定です。</div>
                            </td>
                        </tr>
                    </table>
                    <div class="clearfix"></div>
                    <div class="btn-submit-settings">
                        <button class="btn btn-primary" type="submit" id="submit-search">更新</button>
                    </div>
                </form>
            </div>
            <!-- /.col-lg-12 -->

            <div class="col-lg-12 settings-user">
                <div class="mtit"><i class="fa fa-stop"></i> 店舗情報登録設定１</div>
                <div class="mtit">※患者アプリの送信先店舗の情報として表示するものを設定します。</div>
                <form action="{{action('Company\SettingStoresController@postSettingStore')}}" method="POST">
                    {{ csrf_field() }}
                    <table class="table table-striped table table-user table-settings-user-company pull-left">
                        <tr>
                            <th width="1%">#</th>
                            <th>項目名</th>
                            <th>表示/非表示</th>
                            <th>店舗での編集可否</th>
                        </tr>
                        <tr>
                            <td>1</td>
                            <td>店舗名</td>
                            <td>
                                表示
                            </td>
                            <td>
                                <label>
                                    {!! Form::radio('edit[name]', 1, $storeInputSetting->name['edit']) !!}
                                    可能
                                </label>
                                <label>
                                    {!! Form::radio('edit[name]', 0, !$storeInputSetting->name['edit']) !!}
                                    不可
                                </label>

                            </td>
                        </tr>

                        <tr>
                            <td>2</td>
                            <td>店舗画像</td>
                            <td>
                                <label>
                                    {!! Form::radio('display[photo_url]', 1, $storeInputSetting->photo_url['display']) !!}
                                    表示
                                </label>
                                <label>
                                    {!! Form::radio('display[photo_url]', 0, !$storeInputSetting->photo_url['display']) !!}
                                    非表示
                                </label>

                            </td>
                            <td>

                                <label>
                                    {!! Form::radio('edit[photo_url]', 1, $storeInputSetting->photo_url['edit']) !!}
                                    可能
                                </label>
                                <label>
                                    {!! Form::radio('edit[photo_url]', 0, !$storeInputSetting->photo_url['edit']) !!}
                                    不可
                                </label>

                            </td>
                        </tr>

                        <tr>
                            <td>3</td>
                            <td>郵便番号</td>
                            <td>
                                表示
                            </td>
                            <td>
                                <label>
                                    {!! Form::radio('edit[postal_code]', 1, $storeInputSetting->postal_code['edit']) !!}
                                    可能
                                </label>
                                <label>
                                    {!! Form::radio('edit[postal_code]', 0, !$storeInputSetting->postal_code['edit']) !!}
                                    不可
                                </label>

                            </td>
                        </tr>

                        <tr>
                            <td>4</td>
                            <td>住所</td>
                            <td>
                                表示
                            </td>
                            <td>
                                <label>
                                    {!! Form::radio('edit[address]', 1, $storeInputSetting->address['edit']) !!}
                                    可能
                                </label>
                                <label>
                                    {!! Form::radio('edit[address]', 0, !$storeInputSetting->address['edit']) !!}
                                    不可
                                </label>

                            </td>
                        </tr>

                    </table>
                    <table class="table table-striped table table-user table-settings-user-company pull-left">
                        <tr>
                            <th width="1%">#</th>
                            <th>項目名</th>
                            <th>表示/非表示</th>
                            <th>店舗での編集可否</th>
                        </tr>
                        <tr>
                            <td>5</td>
                            <td>電話番号</td>
                            <td>
                                表示
                            </td>
                            <td>
                                <label>
                                    {!! Form::radio('edit[phone_number]', 1, $storeInputSetting->phone_number['edit']) !!}
                                    可能
                                </label>
                                <label>
                                    {!! Form::radio('edit[phone_number]', 0, !$storeInputSetting->phone_number['edit']) !!}
                                    不可
                                </label>

                            </td>
                        </tr>

                        <tr>
                            <td>6</td>
                            <td>FAX番号</td>
                            <td>
                                <label>

                                    {!! Form::radio('display[fax_number]', 1, $storeInputSetting->fax_number['display']) !!}
                                    表示
                                </label>
                                <label>
                                    {!! Form::radio('display[fax_number]', 0, !$storeInputSetting->fax_number['display']) !!}
                                    非表示
                                </label>

                            </td>
                            <td>
                                <label>

                                    {!! Form::radio('edit[fax_number]', 1, $storeInputSetting->fax_number['edit']) !!}
                                    可能
                                </label>
                                <label>
                                    {!! Form::radio('edit[fax_number]', 0, !$storeInputSetting->fax_number['edit']) !!}
                                    不可
                                </label>

                            </td>
                        </tr>

                        <tr>
                            <td>7</td>
                            <td>受付時間</td>
                            <td>
                                表示
                            </td>
                            <td>

                                <label>
                                    {!! Form::radio('edit[working_time]', 1, $storeInputSetting->working_time['edit']) !!}
                                    可能
                                </label>
                                <label>
                                    {!! Form::radio('edit[working_time]', 0, !$storeInputSetting->working_time['edit']) !!}
                                    不可
                                </label>
                            </td>
                        </tr>

                        <tr>
                            <td>8</td>
                            <td>店舗ID</td>
                            <td>
                                表示
                            </td>
                            <td>

                                <label>
                                    {!! Form::radio('edit[internal_code]', 1, $storeInputSetting->internal_code['edit']) !!}
                                    可能
                                </label>
                                <label>
                                    {!! Form::radio('edit[internal_code]', 0, !$storeInputSetting->internal_code['edit']) !!}
                                    不可
                                </label>
                            </td>
                        </tr>

                    </table>
                    <div class="clearfix"></div>
                    <div class="btn-submit-settings">
                        <button class="btn btn-primary" type="submit" id="submit-search">更新</button>
                    </div>
                </form>
            </div>

            <div class="col-lg-12 settings-user">
                <div class="mtit"><i style="float: left;" class="fa fa-stop"></i><p style="float: left; margin-right: 150px;"> 店舗情報登録設定２</p> <p style="color: red; ">※店舗での設定編集を「可能」とした場合は、当画面にて設定したものが初期設定となります。</p></div>
                <form action="{{action('Company\SettingStoresController@postSettingStore')}}" method="POST">
                    {{ csrf_field() }}
                    <table class="table table-striped table table-user table-settings-user-company-2">
                        <tr>
                            <th width="1%">#</th>
                            <th width="40%">項目名</th>
                            <th>内容</th>
                            <th width="145px">表示/非表示</th>
                            <th width="145px">店舗での編集可否</th>
                        </tr>
                        <tr>
                            <td>1</td>
                            <td>
                                クレジットカード利用
                            </td>
                            <td>
                                <div class="text-left">{{$storeInputSetting->accept_credit_card['data']['accept'] ? '可能' : '不可'}} {{$storeInputSetting->accept_credit_card['data']['card_type']}}</div>
                            </td>
                            <td>
                                <label>
                                    {!! Form::radio('display[accept_credit_card]', 1, $storeInputSetting->accept_credit_card['display']) !!}
                                    表示
                                </label>
                                <label>

                                    {!! Form::radio('display[accept_credit_card]', 0, !$storeInputSetting->accept_credit_card['display']) !!}
                                    非表示
                                </label>
                            </td>
                            <td>

                                <label>
                                    {!! Form::radio('edit[accept_credit_card]', 1, $storeInputSetting->accept_credit_card['edit']) !!}
                                    可能
                                </label>
                                <label>

                                    {!! Form::radio('edit[accept_credit_card]', 0,  !$storeInputSetting->accept_credit_card['edit']) !!}
                                    不可
                                </label>
                            </td>
                        </tr>

                        <tr>
                            <td>2</td>
                            <td>
                                駐車場
                            </td>
                            <td>
                                <div class="text-left">{{$storeInputSetting->park_info['data']}}</div>
                            </td>
                            <td>
                                <label>
                                    {!! Form::radio('display[park_info]', 1, $storeInputSetting->park_info['display']) !!}
                                    表示
                                </label>
                                <label>

                                    {!! Form::radio('display[park_info]', 0,  !$storeInputSetting->park_info['display']) !!}
                                    非表示
                                </label>

                            </td>
                            <td>
                                <label>
                                    {!! Form::radio('edit[park_info]', 1, $storeInputSetting->park_info['edit']) !!}
                                    可能
                                </label>
                                <label>

                                    {!! Form::radio('edit[park_info]', 0, !$storeInputSetting->park_info['edit']) !!}
                                    不可
                                </label>

                            </td>
                        </tr>

                        <tr>
                            <td>3</td>
                            <td>
                                店舗からのお知らせ
                            </td>
                            <td>
                                <div class="text-left">{{$storeInputSetting->description['data']}}</div>
                            </td>
                            <td>
                                <label>
                                    {!! Form::radio('display[description]', 1, $storeInputSetting->description['display']) !!}
                                    表示
                                </label>
                                <label>

                                    {!! Form::radio('display[description]', 0, !$storeInputSetting->description['display']) !!}
                                    非表示
                                </label>

                            </td>
                            <td>
                                <label>
                                    {!! Form::radio('edit[description]', 1, $storeInputSetting->description['edit']) !!}
                                    可能
                                </label>
                                <label>

                                    {!! Form::radio('edit[description]', 0, !$storeInputSetting->description['edit']) !!}
                                    不可
                                </label>

                            </td>
                        </tr>

                        <tr>
                            <td>4</td>
                            <td>
                                営業時間についてのコメント
                            </td>
                            <td>
                                <div class="text-left">{{$storeInputSetting->note_working_time['data']}}</div>
                            </td>
                            <td>
                                <label>
                                    {!! Form::radio('display[note_working_time]', 1, $storeInputSetting->note_working_time['display']) !!}
                                    表示
                                </label>
                                <label>

                                    {!! Form::radio('display[note_working_time]', 0, !$storeInputSetting->note_working_time['display']) !!}
                                    非表示
                                </label>


                            </td>
                            <td>
                                <label>
                                    {!! Form::radio('edit[note_working_time]', 1, $storeInputSetting->note_working_time['edit']) !!}
                                    可能
                                </label>
                                <label>

                                    {!! Form::radio('edit[note_working_time]', 0, !$storeInputSetting->note_working_time['edit']) !!}
                                    不可
                                </label>

                            </td>
                        </tr>

                    </table>
                    <div class="btn-submit-settings">
                        <button class="btn btn-primary" type="submit" id="submit-search">更新</button>
                        <button type="button" class="btn btn-primary new-regulations fRight" id="newAdd" onclick="self.location='{{action('Company\SettingStoresController@getInfoStore')}}'">編集</button>
                    </div>
                </form>
            </div>
           
            <div class="col-lg-12">
                <div class="mtit"><i class="fa fa-stop"></i> メッセージテンプレート設定機能</div>
                <p style="color: red; ">※メッセージテンプレートは、ステータスを「適用中」としたものを編集することはできません。編集する場合は、ステータスを「未適用」とした後に、編集を行ってください。<br/>適用中のものは、各店舗で編集をして利用している可能性があります。一度適用中としたもののステータス変更や編集を行うと全店舗へ影響がありますので、事前に各店舗へ連絡をしてから行うようにしてください。</p>
                <div class="bs-statistic-tabs " data-example-id="togglable-tabs">
                    <ul id="myTabs" class="nav nav-tabs" role="tablist">
                        <li class="{{ $settingActive === 2 ? 'active' : '' }}" role="presentation"><a id="home-tab" class="selectTab1" aria-expanded="false" aria-controls="home" data-toggle="tab" role="tab" href="#tab1">基本テンプレート</a></li>
                        <li class="{{ $settingActive === 1 ? 'active' : '' }}" role="presentation"><a id="home-tab" class="selectTab2" aria-expanded="false" aria-controls="home" data-url="/#tab2" data-toggle="tab" role="tab" href="#tab2">本部テンプレート</a></li>
                        <li><a style="background-color: gray;" id="profile-tab" aria-controls="profile" aria-expanded="true">店舗登録テンプレー</a></li>
                        <li id="add" style="#add{margin-bottom:3px;}" class="last">

                            <button type="button" class="btn btn-primary new-regulations" id="newAddd"
                                    onClick="self.location='{!! action('Company\SettingStoresController@getAdd') !!}'">+新規作成
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

                                    <tr>
                                        <td>{{$index + 1}}</td>
                                        <td>
                                            {{@\App\Models\MessageTemplate::$messageTypes[$settingTab1['message_type']]}}
                                        </td>
                                        <td>
                                            <a href="{!! action('Company\SettingStoresController@getEdit',$settingTab1['alias']) !!}">{{ $settingTab1['name'] }}</a>
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

                                    <tr>
                                        <td>{{$index + 1}}</td>
                                        <td>
                                            {{@\App\Models\MessageTemplate::$messageTypes[$settingTab2['message_type']]}}
                                        </td>
                                        <td>
                                            <a href="{!! action('Company\SettingStoresController@getEdit',$settingTab2['alias']) !!}">{{ $settingTab2['name'] }}</a>
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
                    </div>
                    <!--/END Tab--></div>
            </div>

        </div>
    </div>
    <!-- /#page-wrapper -->
    <script>
        $('#select-time-setting').change(function () {
            timeSetting = $(this).find('option:selected').html();
            $('#time-setting').html('').append(timeSetting);
        });

        var input = $("input[name^='display[']");
        input.each(function () {
            var attr = $(this).attr('checked');
            if ($(this).val() == 0 && typeof attr !== typeof undefined && attr !== false) {
                var name = $(this).attr('name').replace('display[', '').replace(']', '');
                $("input[name='edit[" + name + "]']").attr('disabled', 'true');
            }
        })

        input.click(function () {
            if ($(this).val() == 1) {
                var name = $(this).attr('name').replace('display[', '').replace(']', '');
                $("input[name='edit[" + name + "]']").removeAttr('disabled');
            } else {
                var name = $(this).attr('name').replace('display[', '').replace(']', '');
                $("input[name='edit[" + name + "]']").attr('disabled', 'true');
            }
        });

    </script>

    <script type="text/javascript">
        $(document).ready(function(){
            $('.selectTab1').click(function(){
                $('#newAddd').attr('disabled',true);
            });

            $('.selectTab2').click(function(){
                $('#newAddd').attr('disabled',false);
            });
        });
    </script>

@endsection