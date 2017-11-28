@extends('layouts.company')

@section('title', 'スタッフアカウント管理')

@section('content')

    <div id="page-wrapper">
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-12 settings-user">
                <p><i class="fa fa-stop"></i> 患者アプリ利用可能機能設定</p>

                <form action="{{action('Company\SettingUsersController@postSetting')}}" method="POST">
                    {{ csrf_field() }}
                    <table class="table table-striped">
                        <colgroup>
                            <col width="20%">
                            <col width="60%">
                            <col width="20%">
                        </colgroup>
                        <tr>
                            <th>設定項目</th>
                            <th>本部設定</th>
                            <th>各店舗での設定変更可否</th>
                        </tr>
                        <tr>
                            <th>営業時間外の処方せん画像送信可否</th>
                            <td>
                                <label style="font-weight: normal;">
                                    <?php echo Form::radio('acceptOrderOnNonBusinessHour', 1, $acceptOrderOnNonBusinessHour == 1) ?>
                                    送信可</label>
                                <label class="pl10" style="font-weight: normal;">
                                    <?php echo Form::radio('acceptOrderOnNonBusinessHour', 2, $acceptOrderOnNonBusinessHour == 2) ?>
                                    送信不可</label>

                                <p class="txtInfo" style="color: #f0ad4e">※患者アプリにて営業時間外に処方せん画像を当店舗宛てに送信しようとした場合に、拒否することのできる機能です。送信可とした場合は、対応が翌営業日となることを患者さまにお知らせします。</p>
                            </td>
                            <td align="center">
                                <label style="font-weight: normal;">
                                    <?php echo Form::radio('settingChangeOnStoreHour', 1, $settingChangeOnStoreHour == 1) ?>
                                    変更可</label>
                                <label class="pl10" style="font-weight: normal;">
                                    <?php echo Form::radio('settingChangeOnStoreHour', 2, $settingChangeOnStoreHour == 2) ?>
                                    変更不可</label>
                            </td>
                        </tr>
                        <tr>
                            <th>受取時間が夜間休日等加算対象時刻の場合のアラート表示</th>
                            <td>
                                <label style="font-weight: normal;">
                                    <?php echo Form::radio('showAlertAtNight', 1, $showAlertAtNight == 1) ?>
                                    表示する</label>
                                <label class="pl10" style="font-weight: normal;">
                                    <?php echo Form::radio('showAlertAtNight', 2, $showAlertAtNight == 2) ?>
                                    表示しない</label>

                                <p class="txtInfo" style="color: #f0ad4e">※患者アプリにて受取希望時刻が夜間休日等加算対象時刻の場合に、手数料がかかる旨の表示をする機能です。</p></td>
                            <td align="center">
                                <label style="font-weight: normal;">
                                    <?php echo Form::radio('settingChangeOnStoreAtNight', 1, $settingChangeOnStoreAtNight == 1) ?>
                                    変更可</label>
                                <label class="pl10" style="font-weight: normal;">
                                    <?php echo Form::radio('settingChangeOnStoreAtNight', 2, $settingChangeOnStoreAtNight == 2) ?>
                                    変更不可</label>
                            </td>
                        </tr>
                        <tr class="{!! $patientReplySettingMediaid['used']==0?'color-block':'' !!}">
                            <th>薬局からのメッセージ送信時に患者からの返答可否</th>
                            <td>
                                <label style="font-weight: normal;">
                                    <?php echo Form::radio('patientReplySetting', 1, $patientReplySetting == 1, [$patientReplySettingMediaid['used']==0?'disabled':'']) ?>
                                    返答可</label>
                                <label class="pl10" style="font-weight: normal;">
                                    <?php echo Form::radio('patientReplySetting', 2, $patientReplySetting == 2, [$patientReplySettingMediaid['used']==0?'disabled':'']) ?>
                                    1回のみ返答可</label>
                                <label class="pl10" style="font-weight: normal;">
                                    <?php echo Form::radio('patientReplySetting', 3, $patientReplySetting == 3, [$patientReplySettingMediaid['used']==0?'disabled':'']) ?>
                                    返答不可</label>

                                <p class="txtInfo" style="color: #f0ad4e">※薬局から患者アプリにメッセージを送信した際に、患者さまがアプリから返信することのできる機能です。</p>
                            </td>
                            <td align="center">
                                <label style="font-weight: normal;">
                                    <?php echo Form::radio('settingChangeOnStorePatientReply', 1, $settingChangeOnStorePatientReply == 1, [$patientReplySettingMediaid['used']==0?'disabled':'']) ?>
                                    変更可</label>
                                <label class="pl10" style="font-weight: normal;">
                                    <?php echo Form::radio('settingChangeOnStorePatientReply', 2, $settingChangeOnStorePatientReply == 2, [$patientReplySettingMediaid['used']==0?'disabled':'']) ?>
                                    変更不可</label>
                            </td>
                        </tr>
                        <tr>
                            <th>自動ログイン</th>
                            <td>
                                <label style="font-weight: normal;">
                                    <?php echo Form::radio('settingAutoLogin', 1,  $settingAutoLogin == true) ?>
                                    可</label>
                                <label class="pl10" style="font-weight: normal;">
                                    <?php echo Form::radio('settingAutoLogin', 2, $settingAutoLogin !== true) ?>
                                    不可</label>

                                <p class="txtInfo" style="color: #f0ad4e">※会員登録をしている方が次回アプリ起動時に自動ログインをし、毎回ログインID/PWを入力することを不要とする機能です。</p>
                            </td>
                            <td align="center">変更不可</td>
                        </tr>

                    </table>
                    <div class="btn-submit-settings">
                        <button class="btn btn-primary new-regulations" type="submit" >更新</button>
                        <p style="color: red; float: right; line-height: 38px;">※店舗での設定を「変更可」とした場合は、「本部設定」が初期設定となります。</p>
                    </div>
                </form> 
                <br/>

                <div class="mtit"><i class="fa fa-stop"></i> 店舗検索機能設定</div>
                <div class="mtit">※ 患者アプリで、送信先店舗を一覧表示された店舗から選択するか、都道府県名から検索するかを設定します。</div>

                <form action="{{action('Company\SettingUsersController@postSettingStoreLocal')}}" method="POST">
                    {{ csrf_field() }}
                    <table class="table table-striped">
                        <colgroup>
                            <col width="30%">
                            <col width="70%">
                        </colgroup>
                        <tr>
                            <td style="background-color: #d9edf7;border-color:#95dceb;text-align: center;color: #31708f">
                                店舗検索画面表示
                            </td>
                            <td>
                                <label style="font-weight: normal;">
                                    <?php echo Form::radio('settingStoreLocalScreenDisplay', 1, $settingStoreLocalScreenDisplay == 1) ?>
                                    店舗名一覧表示（店舗数20店舗程度までは本設定を推奨）</label>
                                <label class="pl10" style="font-weight: normal;">
                                    <?php echo Form::radio('settingStoreLocalScreenDisplay', 0, $settingStoreLocalScreenDisplay == 0) ?>
                                    都道府県から検索</label>
                            </td>
                        </tr>
                    </table>
                    <div class="btn-submit-settings">
                        <button class="btn btn-primary new-regulations" type="submit">更新</button>
                    </div>
                </form>
            </div>


            <div class="col-lg-12 settings-user">
                <div class="mtit"><i class="fa fa-stop"></i> 会員基本情報登録項目設定</div>

                <form action="{{action('Company\SettingUsersController@postSettingRegisterUser')}}" method="POST">
                    {{ csrf_field() }}
                    <table class="table table-striped table table-user table-settings-user-company pull-left">
                        <tr>
                            <th>#</th>
                            <th>項目名</th>
                            <th>表示/非表示</th>
                            <th>必須/任意</th>
                        </tr>
                        <tr>
                            <td>1</td>
                            <td>氏名（漢字）</td>
                            <td>
                                <label>
                                    {!! Form::radio('display[first_name]', 1, $userRegisterSetting->first_name['display']) !!}
                                    表示
                                </label>

                                <label>
                                    {!! Form::radio('display[first_name]', 0, !$userRegisterSetting->first_name['display']) !!}
                                    非表示
                                </label>

                            </td>
                            <td>
                                <label>
                                    {!! Form::radio('required[first_name]', 1, $userRegisterSetting->first_name['required']) !!}
                                    必須
                                </label>

                                <label>
                                    {!! Form::radio('required[first_name]', 0, !$userRegisterSetting->first_name['required']) !!}
                                    任意
                                </label>

                            </td>
                        </tr>

                        <tr>
                            <td>2</td>
                            <td>氏名（カナ）</td>
                            <td>
                                表示
                            </td>
                            <td>
                                必須
                            </td>
                        </tr>

                        <tr>
                            <td>3</td>
                            <td>性別</td>
                            <td>
                                <label>
                                    {!! Form::radio('display[gender]', 1, $userRegisterSetting->gender['display']) !!}
                                    表示
                                </label>

                                <label>
                                    {!! Form::radio('display[gender]', 0, !$userRegisterSetting->gender['display']) !!}
                                    非表示
                                </label>

                            </td>
                            <td>
                                <label>
                                    {!! Form::radio('required[gender]', 1, $userRegisterSetting->gender['required'],['class'=>'hidden']) !!}
                                    <span>{!! $userRegisterSetting->gender['display']?'必須':'任意' !!}</span>
                                </label>
                                {!! Form::radio('required[gender]', 0, !$userRegisterSetting->gender['required'],['class' => 'hidden']) !!}
                            </td>
                        </tr>

                        <tr>
                            <td>4</td>
                            <td>生年月日</td>
                            <td>
                                <label>
                                    {!! Form::radio('display[birthday]', 1, $userRegisterSetting->birthday['display']) !!}
                                    表示
                                </label>

                                <label>
                                    {!! Form::radio('display[birthday]', 0, !$userRegisterSetting->birthday['display']) !!}
                                    非表示
                                </label>

                            </td>
                            <td>
                                <label>
                                    {!! Form::radio('required[birthday]', 1, $userRegisterSetting->birthday['required']) !!}
                                    必須
                                </label>

                                <label>
                                    {!! Form::radio('required[birthday]', 0, !$userRegisterSetting->birthday['required']) !!}
                                    任意
                                </label>

                            </td>
                        </tr>

                        <tr>
                            <td>5</td>
                            <td>携帯電話番号</td>
                            <td>
                                <label>
                                    {!! Form::radio('display[phone_number]', 1, $userRegisterSetting->phone_number['display']) !!}
                                    表示
                                </label>

                                <label>
                                    {!! Form::radio('display[phone_number]', 0, !$userRegisterSetting->phone_number['display']) !!}
                                    非表示
                                </label>

                            </td>
                            <td>
                                <label>
                                    {!! Form::radio('required[phone_number]', 1, $userRegisterSetting->phone_number['required']) !!}
                                    必須
                                </label>

                                <label>
                                    {!! Form::radio('required[phone_number]', 0, !$userRegisterSetting->phone_number['required']) !!}
                                    任意
                                </label>

                            </td>
                        </tr>
                    </table>
                    <table class="table table-striped table table-user table-settings-user-company pull-left">
                        <tr>
                            <th>#</th>
                            <th>項目名</th>
                            <th>表示/非表示</th>
                            <th>必須/任意</th>
                        </tr>
                        <tr>
                            <td>6</td>
                            <td>携帯メールアドレス</td>
                            <td>
                                表示
                            </td>
                            <td>
                                必須
                            </td>
                        </tr>

                        <tr>
                            <td>7</td>
                            <td>郵便番号</td>
                            <td>
                                <label>
                                    {!! Form::radio('display[postal_code]', 1, $userRegisterSetting->postal_code['display']) !!}
                                    表示
                                </label>

                                <label>
                                    {!! Form::radio('display[postal_code]', 0, !$userRegisterSetting->postal_code['display']) !!}
                                    非表示
                                </label>

                            </td>
                            <td>
                                <label>
                                    {!! Form::radio('required[postal_code]', 1, $userRegisterSetting->postal_code['required']) !!}
                                    必須
                                </label>

                                <label>
                                    {!! Form::radio('required[postal_code]', 0, !$userRegisterSetting->postal_code['required']) !!}
                                    任意
                                </label>

                            </td>
                        </tr>

                        <tr>
                            <td>8</td>
                            <td>住所</td>
                            <td>
                                <label>
                                    {!! Form::radio('display[address]', 1, $userRegisterSetting->address['display']) !!}
                                    表示
                                </label>

                                <label>
                                    {!! Form::radio('display[address]', 0, !$userRegisterSetting->address['display']) !!}
                                    非表示
                                </label>

                            </td>
                            <td>
                                <label>
                                    {!! Form::radio('required[address]', 1, $userRegisterSetting->address['required']) !!}
                                    必須
                                </label>

                                <label>
                                    {!! Form::radio('required[address]', 0, !$userRegisterSetting->address['required']) !!}
                                    任意
                                </label>

                            </td>
                        </tr>

                        <tr>
                            <td>9</td>
                            <td>お薬手帳シール希望</td>
                            <td>
                                <label>
                                    {!! Form::radio('display[drugbook_use]', 1, $userRegisterSetting->drugbook_use['display']) !!}
                                    表示
                                </label>

                                <label>
                                    {!! Form::radio('display[drugbook_use]', 0, !$userRegisterSetting->drugbook_use['display']) !!}
                                    非表示
                                </label>

                            </td>
                            <td>
                                <label>
                                    {!! Form::radio('required[drugbook_use]', 1, $userRegisterSetting->drugbook_use['required'],['class'=>'hidden']) !!}
                                    <span>{!! $userRegisterSetting->drugbook_use['display']?'必須':'任意' !!}</span>
                                </label>
                                {!! Form::radio('required[drugbook_use]', 0, !$userRegisterSetting->drugbook_use['required'],['class' => 'hidden']) !!}
                            </td>
                        </tr>

                        <tr>
                            <td>10</td>
                            <td>後発医薬品希望</td>
                            <td>
                                <label>
                                    {!! Form::radio('display[drugbrand_change]', 1, $userRegisterSetting->drugbrand_change['display']) !!}
                                    表示
                                </label>

                                <label>
                                    {!! Form::radio('display[drugbrand_change]', 0, !$userRegisterSetting->drugbrand_change['display']) !!}
                                    非表示
                                </label>

                            </td>
                            <td>
                                <label>
                                    {!! Form::radio('required[drugbrand_change]', 1, $userRegisterSetting->drugbrand_change['required'],['class'=>'hidden']) !!}
                                    <span>{!! $userRegisterSetting->drugbrand_change['display']?'必須':'任意' !!}</span>
                                </label>
                                {!! Form::radio('required[drugbrand_change]', 0, !$userRegisterSetting->drugbrand_change['required'],['class' => 'hidden']) !!}
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
                <div class="mtit"><i class="fa fa-stop"></i> アンケート登録項目設定</div>
                <div class="mtit">※会員登録画面にアンケートを表示するかを設定することができます。</div>
                <form action="{{action('Company\SettingUsersController@postSettingRegisterUser')}}" method="POST">
                    {{ csrf_field() }}
                    <table class="table table-striped table table-user table-settings-user-company-2">
                        <tr>
                            <th>#</th>
                            <th>質問内容</th>
                            <th>選択肢</th>
                            <th>表示/非表示</th>
                            <th>必須/任意</th>
                        </tr>
                        <tr>
                            <td rowspan="2">1</td>
                            <td rowspan="2">お得な情報をメッセージでの受信を希望しますか？</td>
                            <td>はい</td>
                            <td rowspan="2">
                                <label>
                                    {!! Form::radio('display[accept_saleinfo]', 1, $userRegisterSetting->accept_saleinfo['display']) !!}
                                    表示
                                </label>

                                <label>
                                    {!! Form::radio('display[accept_saleinfo]', 0, !$userRegisterSetting->accept_saleinfo['display']) !!}
                                    非表示
                                </label>

                            </td>
                            <td rowspan="2">
                                <label>
                                    {!! Form::radio('required[accept_saleinfo]', 1, $userRegisterSetting->accept_saleinfo['required'],['class'=>'hidden']) !!}
                                    <span>{!! $userRegisterSetting->accept_saleinfo['display']?'必須':'任意' !!}</span>
                                </label>
                                {!! Form::radio('required[accept_saleinfo]', 0, !$userRegisterSetting->accept_saleinfo['required'],['class' => 'hidden']) !!}
                            </td>
                        </tr>
                        <tr>
                            <td>いいえ</td>
                        </tr>

                        <tr>
                            <td rowspan="2">2</td>
                            <td rowspan="2">お得な情報の郵送での送付を希望しますか？</td>
                            <td>希望する</td>
                            <td rowspan="2">
                                <label>
                                    {!! Form::radio('display[accept_saleinfo_dm]', 1, $userRegisterSetting->accept_saleinfo_dm['display']) !!}
                                    表示
                                </label>

                                <label>
                                    {!! Form::radio('display[accept_saleinfo_dm]', 0, !$userRegisterSetting->accept_saleinfo_dm['display']) !!}
                                    非表示
                                </label>

                            </td>
                            <td rowspan="2">
                                <label>
                                    {!! Form::radio('required[accept_saleinfo_dm]', 1, $userRegisterSetting->accept_saleinfo_dm['required'],['class'=>'hidden']) !!}
                                    <span>{!! $userRegisterSetting->accept_saleinfo_dm['display']?'必須':'任意' !!}</span>
                                </label>
                                {!! Form::radio('required[accept_saleinfo_dm]', 0, !$userRegisterSetting->accept_saleinfo_dm['required'],['class' => 'hidden']) !!}
                            </td>
                        </tr>
                        <tr>
                            <td>希望しない</td>
                        </tr>
                    </table>
                    <div class="btn-submit-settings">
                        <button class="btn btn-primary" type="submit" id="submit-search">更新</button>
                    </div>
                </form>
            </div>
            <!-- /.col-lg-12 -->
        </div>
    </div>
    <!-- /#page-wrapper -->
    <script>
        var input = $("input[name^='display[']");
        input.each(function () {
            var attr = $(this).attr('checked');
            if ($(this).val() == 0 && typeof attr !== typeof undefined && attr !== false) {
                var name = $(this).attr('name').replace('display[', '').replace(']', '');
                $("input[name='required[" + name + "]']").attr('disabled', 'true');
            }
        })

        input.click(function () {
            if ($(this).val() == 1) {
                var name = $(this).attr('name').replace('display[', '').replace(']', '');
                $("input[name='required[" + name + "]']").removeAttr('disabled');
                $("input[name='required[" + name + "]']").next().html('必須');
            } else {
                var name = $(this).attr('name').replace('display[', '').replace(']', '');
                $("input[name='required[" + name + "]']").attr('disabled', 'true');
                $("input[name='required[" + name + "]']").next().html('任意');
            }
        });


    </script>

@endsection