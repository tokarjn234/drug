@extends('layouts.mediaid')

@section('title', '登録企業管理')

@section('content')
    <?php
    $billable = [
            0 => '0',
            1 => '1',
            2 => 'その他'
    ];
    $err = [];
    if (count($errors) > 0) {
        $err = $errors->toArray();
    }
    ?>
    <form method="post" action="{!! action('Mediaid\CompaniesController@postUpdateInfoCompany') !!}"
          id="update-all-info-company">
        <input name="alias" type="hidden" value="{!! $company['alias'] !!}"/>
        <input name="tag" type="hidden" value="{!! session('tag') !!}"/>
        {{ csrf_field() }}
        <div id="page-wrapper">
            <!-- /.row -->
            <div class="row" id="form-input-all-info">
                <div class="col-lg-12 infoCompany" id="infoCompany">
                    <div class="margin_t20">
                        <div class="button-status-company-blue pull-right">{!! \App\Models\Company::$status[$company['status']] !!}</div>
                        <div class="pull-right text-header-company">
                            企業コード：{!! str_pad($company['id'], 4, '0', STR_PAD_LEFT) !!}
                            　企業名：{!! $company['name'] !!}</div>
                    </div>
                    <div class="clearfix"></div>


                    <div class="center-block text-left margin_t10">
                        <i class="fa fa-stop"></i> 企業情報
                    </div>

                    <div class="get-infoCompany">
                        <table id="dataTables-example" class="table table-striped table table-user">
                            <tr>
                                <th width="20%" colspan="2">項目名</th>
                                <th colspan="3">登録情報</th>
                            </tr>
                            <tr>
                                <th colspan="2">企業名</th>
                                <td colspan="3">
                                    <input name="infoCpn[name]" type="text" class="form-control required"
                                           maxlength="50"
                                           data-message="企業名が入力されていません。"
                                           data-toggle="popover" data-placement="top"
                                           value="{!! empty(Input::old('name'))? $company['name']: Input::old('name')!!}"/>
                                </td>
                            </tr>
                            <tr>
                                <th colspan="2">代表者名</th>
                                <td colspan="3">
                                    <input name="infoCpn[name_manager]" maxlength="30"
                                           value="{!! empty(Input::old('name_manager'))? $company['name_manager']: Input::old('name_manager')!!}"
                                           type="text" class="form-control"/>
                                </td>
                            </tr>
                            <tr>
                                <th colspan="2">代表電話番号</th>
                                <td>
                                    <input name="infoCpn[phone_number]"
                                           value="{!! empty(Input::old('phone_number'))? $company['phone_number']: Input::old('phone_number')!!}"
                                           type="text" maxlength="15" class="form-control numeric phone-numbers"/>
                                </td>
                                <th>FAX番号</th>
                                <td>
                                    <input name="infoCpn[fax]"
                                           value="{!! empty(Input::old('fax'))? $company['fax']: Input::old('fax')!!}"
                                           type="text" maxlength="15" class="form-control numeric phone-numbers"/>
                                </td>
                            </tr>

                            <tr>
                                <th rowspan="2">本社</th>
                                <th>郵便番号</th>
                                <td colspan="3">
                                    <div class="row">
                                        <div class="col-lg-4 col-md-4 col-sm-4 form-inline text-left">
                                            <?php
                                            if (!empty($company['postal_code_headquarters'])) {
                                                $postal_code_headquarters = explode('-', $company['postal_code_headquarters']);
                                            }
                                            ?>
                                            <input name="infoCpn[postal_code_headquarters][0]"
                                                   type="text"
                                                   class="form-control w100_imp numeric"
                                                   value="{!! empty(Input::old('postal_code_headquarters[0]'))? @$postal_code_headquarters[0]: Input::old('postal_code_headquarters[0]')!!}"
                                                   maxlength="3"/>
                                            -
                                            <input name="infoCpn[postal_code_headquarters][1]"
                                                   type="text"
                                                   value="{!! empty(Input::old('postal_code_headquarters[1]'))? @$postal_code_headquarters[1]: Input::old('postal_code_headquarters[1]')!!}"
                                                   class="form-control w100_imp numeric" maxlength="4"/>
                                        </div>
                                    </div>
                            </tr>
                            <tr>
                                <th>住所</th>
                                <td colspan="3"><input name="infoCpn[headquarters]"
                                                       value="{!! empty(Input::old('headquarters'))? $company['headquarters']: Input::old('headquarters')!!}"
                                                       type="text" maxlength="50" class="form-control"/></td>
                            </tr>

                            <tr>
                                <th rowspan="3">請求書送付先</th>
                                <th>郵便番号</th>
                                <td colspan="3">
                                    <div class="row">
                                        <div class="col-lg-4 col-md-4 col-sm-4 form-inline text-left">
                                            <?php
                                            if (!empty($company['bill_to_postal_code'])) {
                                                $bill_to_postal_code = explode('-', $company['bill_to_postal_code']);
                                            }
                                            ?>
                                            <input name="infoCpn[bill_to_postal_code][0]"
                                                   type="text"
                                                   class="form-control w100_imp numeric"
                                                   value="{!! empty(Input::old('bill_to_postal_code[0]'))? @$bill_to_postal_code[0]: Input::old('bill_to_postal_code[0]')!!}"
                                                   maxlength="3"/>
                                            -
                                            <input name="infoCpn[bill_to_postal_code][1]"
                                                   type="text"
                                                   value="{!! empty(Input::old('bill_to_postal_code[1]'))? @$bill_to_postal_code[1]: Input::old('bill_to_postal_code[1]')!!}"
                                                   class="form-control w100_imp numeric" maxlength="4"/>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>住所</th>
                                <td colspan="3"><input name="infoCpn[bill_to_address]"
                                                       value="{!! empty(Input::old('bill_to_address'))? @$company['bill_to_address']: Input::old('bill_to_address')!!}"
                                                       type="text" maxlength="50" class="form-control"/></td>
                            </tr>
                            <tr>
                                <th>宛先</th>
                                <td colspan="3"><input name="infoCpn[bill_to_destination]"
                                                       value="{!! empty(Input::old('bill_to_destination'))? @$company['bill_to_destination']: Input::old('bill_to_destination')!!}"
                                                       type="text" maxlength="80" class="form-control"/></td>
                            </tr>

                            <tr>
                                <th rowspan="3">システム担当者</th>
                                <th>部署名</th>
                                <td colspan="3">
                                    <input name="meta_company[division]" type="text"
                                           value="{!! empty(Input::old('meta_company[division]'))? @$company['meta_company']['division']: Input::old('meta_company[division]')!!}"
                                           class="form-control"/>
                                </td>
                            </tr>
                            <tr>
                                <th>氏名</th>
                                <td colspan="3"><input name="meta_company[name_division]"
                                                       value="{!! empty(Input::old('meta_company[name_division]'))? @$company['meta_company']['name_division']: Input::old('meta_company[name_division]')!!}"
                                                       type="text" class="form-control"/></td>
                            </tr>
                            <tr>
                                <th>電話番号</th>
                                <td><input name="meta_company[phone_number]" type="text"
                                           value="{!! empty(Input::old('meta_company[phone_number]'))? @$company['meta_company']['phone_number']: Input::old('meta_company[phone_number]')!!}"
                                           maxlength="15" class="form-control numeric phone-numbers"/></td>
                                <th>FAX番号</th>
                                <td><input name="meta_company[fax_division]" type="text"
                                           value="{!! empty(Input::old('meta_company[fax_division]'))? @$company['meta_company']['fax_division']: Input::old('meta_company[fax_division]')!!}"
                                           maxlength="15" class="form-control numeric phone-numbers"/></td>
                            </tr>
                        </table>
                    </div>
                    <div class="pull-right">
                        <button type="button" onclick="checkValid('#modalConfirm', '企業情報を更新しますか？')"
                                class="btn btn-info btn-edit">更新
                        </button>
                    </div>
                    <div class="clearfix"></div>


                </div>
                <!-- /.col-lg-12 -->
                <div class="col-lg-12 numberStore" id="numberStore">
                    <div class="center-block text-left margin_t10">
                        <i class="fa fa-stop"></i> 契約店舗数
                    </div>
                    <div class="row">
                        <div class="col-lg-6">

                            <div class="get-numberStore">
                                <table id="dataTables-example" class="table table-striped table table-user">
                                    <tr>
                                        <th width="20%">店舗数</th>
                                        <td>
                                            <div class="row">
                                                <div class="col-lg-8 col-md-8 col-sm-8"><input
                                                            name="infoCpn[contract_store]"
                                                            type="number"
                                                            min="{!! count($storePublic) !!}"
                                                            max=""
                                                            value="{!! @$company['contract_store'] !!}"
                                                            class="form-control numeric"/>
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-4">
                                                    <button class="btn btn-info btn-sm" type="submit"> 更新</button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>


                    </div>
                </div>
                <!--Number Staff begin-->
                <div class="col-lg-12 numberStaff" id="numberStaff">
                    <div class="center-block text-left">
                        <i class="fa fa-stop"></i>
                        スタッフアカウント数登録　：{!! $staff['basicStaffPerStore']*$company['contract_store'] + $company['staff_add']['number'] !!}
                        　発行済アカウント数：{!! $staff['usedStaff'] !!}
                        <br/>
                        ※基本アカウントは、店舗が増えた場合には自動的に追加されます。追加アカウントの発行は、原則として課金対象です。
                    </div>
                    <div class="row">
                        <div class="col-lg-6">
                            @if (!empty($err['staffError']))
                                <div class="margin_t10 margin_b10 alert alert-danger">
                                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                    <ul>
                                        @foreach ($err['staffError'] as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <table id="dataTables-example" class="table table-striped table table-user">

                                <div class="get-numberStaff">
                                    <tr>
                                        <th width="20%">基本</th>
                                        <td>
                                            <div class="row">
                                                <div class="col-lg-5 col-md-5 col-sm-5"><input name="basicStaffPerStore"
                                                                                               type="number"
                                                                                               min="0"
                                                                                               max=""
                                                                                               value="{!! @$staff['basicStaffPerStore'] !!}"
                                                                                               class="form-control numeric"/>
                                                </div>
                                                <div class="col-lg-3 col-md-3 col-sm-3" style="padding:8px 0;">×契約店舗数
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-4">
                                                    <button class="btn btn-info btn-sm"> 更新</button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </div>

                                <div class="get-numberStaff">
                                    <tr>
                                        <th>追加</th>
                                        <td>
                                            <div class="row">
                                                <div class="col-lg-8 col-md-8 col-sm-8">

                                                    <input name="infoCpn[staff_add][number]" type="number"
                                                           id="staff_add_number"
                                                           min="0" max="100000"
                                                           value="{!! @$company['staff_add']['number'] !!}"
                                                           class="form-control numeric"/>
                                                </div>
                                                <div class="col-lg-4 col-md-4 col-sm-4">
                                                    <button type="button" class="btn btn-info btn-sm"
                                                            data-toggle="modal"
                                                            data-target="#myModal"> 発行する
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </div>

                            </table>
                        </div>
                    </div>
                </div>
                <!--Number Staff End-->

                <!--Certificate Begin-->
                <div class="col-lg-12 numberCertificate" id="numberCertificate">
                    <div>
                        <i class="fa fa-stop"></i> 端末証明書追加 : {!! $cert['allCount'] !!} 発行済アカウント数
                        : {!! @$cert['usedCount'] !!}<br/>
                        ※端末証明書は、店舗が増えた場合に自動的に追加されます。
                        追加発行は、原則として課金対象です。
                    </div>
                    <div class="row">

                        <div class="col-lg-6">

                            @if (!empty($err['certError']))
                                <div class="margin_t10 margin_b10 alert alert-danger">
                                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                    <ul>
                                        @foreach ($err['certError'] as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                            <table id="dataTables-example" class="table table-striped table table-user">
                                <tr>
                                    <th>基本</th>
                                    <td>
                                        <div class="row">
                                            <div class="col-lg-5 col-md-5 col-sm-5">
                                                <input
                                                        name="numberCertificatePerStore"
                                                        type="number"
                                                        min="0"
                                                        max=""
                                                        value="{!! @$setting['numberCertificatePerStore'] !!}"
                                                        class="form-control numeric"/>
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-3" style="padding:8px 0;">×契約店舗数
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-4">
                                                <button class="btn btn-info btn-sm" type="submit"> 更新</button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th width="20%">追加</th>
                                    <td>
                                        <div class="row">
                                            <div class="col-lg-8 col-md-8 col-sm-8">
                                                <input name="infoCpn[cert_add][number]"
                                                       type="number"
                                                       min="0"
                                                       max=""
                                                       id="cert_add_number"
                                                       value="{!! !empty($company['cert_add']['number'])?$company['cert_add']['number']:0 !!}"
                                                       class="form-control numeric"/>

                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-4">
                                                <button type="button" class="btn btn-info btn-sm"
                                                        data-toggle="modal"
                                                        data-target="#modalAddCertText"> 発行する
                                                </button>
                                                <!-- Modal Add Cert Text begin-->
                                                <div class="modal fade" id="modalAddCertText" role="dialog">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-body"
                                                                 style="border: 1px solid #ccc; border-radius: 5px; margin: 15px;">

                                                                <p class="text-center">株式会社つばめドラッグの端末証明書を<span
                                                                            id="cert_add_number_show">{!! empty($company['cert_add']['number'])?0:$company['cert_add']['number'] !!}</span>件追加します。追加理由を記載してください。
                                                                </p>
                                                                <input name="infoCpn[cert_add][text]"
                                                                       class="form-control not-require" type="text"
                                                                       id="text-comment-add-cert"
                                                                       value="{!! @$company['cert_add']['text'] !!}"/>
                                                            </div>
                                                            <div class="text-center"
                                                                 style="border-top: none;margin-bottom: 20px; margin-top: 30px;">
                                                                <button type="button" class="btn btn-primary "
                                                                        data-dismiss="modal">キャンセル
                                                                </button>
                                                                <button class="btn  btn-primary" id="btn-submit-1"
                                                                        type="submit">OK
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Modal Add Cert Text end-->
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <!--Certificate End-->

                <!--Delete Image Prescription Begin-->
                <div class="col-lg-12 deleteImage" id="deleteImage">
                    <div class="center-block text-left">
                        <i class="fa fa-stop"></i> 処方せん画像保存期間<br/>※{!! $setting['numberDayDeleteImage'] !!}
                        日を超える場合に課金対象です。

                    </div>
                    <table id="dataTables-example" class="table table-striped table table-user">
                        <tr>
                            <th width="20%">処方せん画像削除</th>
                            <td>
                                <div class="row">
                                    <div class="col-lg-3 col-md-3" style="padding:8px 0;">処方せん受信翌日</div>
                                    <div class="text-left col-lg-3 col-md-3">
                                        <input type="number" min="0" name="settings[numberDayDeleteImage]"
                                               class="form-control"
                                               value="{!! $setting['numberDayDeleteImage'] !!}"/>
                                    </div>

                                    <div class="col-lg-3 col-md-3" style="padding:8px 0;">日後に削除する</div>
                                    <div class="col-lg-3 col-md-3">
                                        <button class="btn btn-info" type="submit">更新</button>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                <!--Delete Image Prescription End-->

                <div class="col-lg-12 settingCompany" id="settingCompany">
                    <div class="center-block text-left">
                        <i class="fa fa-stop"></i> 契約内容

                    </div>

                    <div class="get-settingCompany">
                        <table id="dataTables-example" class="table table-striped table table-user">
                            <tr>
                                <th colspan="2">基本契約</th>
                            </tr>
                            <tr>
                                <th width="20%">課金方法</th>
                                <td>
                                    <div class="text-left">
                                        <label>{!! Form::radio('infoCpn[billable][billable]', 2, $company['billable']['billable'], ['disabled']) !!}
                                            その他</label><br/>
                                        <input type="text" name="infoCpn[billable][text]" class="form-control"
                                               value="{!! @$company['billable']['text'] !!} " maxlength="200"/>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <table id="dataTables-example" class="table table-striped table table-user">
                            <tr>
                                <th colspan="3">追加オプション</th>
                            </tr>
                            <tr>
                                <th>患者からの返信機能</th>
                                <td width="">
                                    <div class="pull-left">
                                        <label>
                                            {!! Form::radio('settings[patientReplySettingMediaid][used]', 1, @$setting['patientReplySettingMediaid']['used'], ['disabled']) !!}
                                            利用する
                                        </label>
                                        <label>
                                            {!! Form::radio('settings[patientReplySettingMediaid][used]', 0, @!$setting['patientReplySettingMediaid']['used'], ['disabled']) !!}
                                            利用しない
                                        </label>
                                    </div>
                                    <div class="pull-left" style="margin-left: 20px; padding-top: 3px;">
                                        <div class="text-left">※課金対象</div>
                                        <input type="hidden"
                                               name="settings[patientReplySettingMediaid][billable]"
                                               value="1"/>
                                    </div>

                                </td>
                            </tr>
                            <tr>
                                <th>会員向けメッセージ配信機能</th>
                                <td>
                                    <div class="pull-left">
                                        <label>
                                            {!! Form::radio('settings[memberForMessageDeliveryMediaid][used]', 1, @$setting['memberForMessageDeliveryMediaid']['used'], ['disabled']) !!}
                                            利用する
                                        </label>
                                        <label>
                                            {!! Form::radio('settings[memberForMessageDeliveryMediaid][used]', 0, @!$setting['memberForMessageDeliveryMediaid']['used'], ['disabled']) !!}
                                            利用しない
                                        </label>
                                    </div>
                                    <div class="pull-left" style="margin-left: 20px; padding-top: 3px;">
                                        <div class="text-left">※課金対象</div>
                                        <input type="hidden"
                                               name="settings[memberForMessageDeliveryMediaid][billable]"
                                               value="1"/>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>ほっとラインサービス</th>
                                <td>
                                    <div class="pull-left">
                                        <label>
                                            {!! Form::radio('settings[hotlineServiceMediaid][used]', 1, @$setting['hotlineServiceMediaid']['used'], ['disabled']) !!}
                                            利用する
                                        </label>
                                        <label>
                                            {!! Form::radio('settings[hotlineServiceMediaid][used]', 0, @!$setting['hotlineServiceMediaid']['used'], ['disabled']) !!}
                                            利用しない
                                        </label>
                                    </div>
                                    <div class="pull-left" style="margin-left: 20px; padding-top: 3px;">
                                        <div class="text-left">※課金対象</div>
                                        <input type="hidden"
                                               name="settings[hotlineServiceMediaid][billable]"
                                               value="1"/>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>ほっとライン24サービス</th>
                                <td>
                                    <div class="pull-left">
                                        <label>
                                            {!! Form::radio('settings[hotline24ServiceMediaid][used]', 1, @$setting['hotline24ServiceMediaid']['used'], ['disabled']) !!}
                                            利用する
                                        </label>
                                        <label>
                                            {!! Form::radio('settings[hotline24ServiceMediaid][used]', 0, @!$setting['hotline24ServiceMediaid']['used'], ['disabled']) !!}
                                            利用しない
                                        </label>
                                    </div>
                                    <div class="pull-left" style="margin-left: 20px; padding-top: 3px;">
                                        <div class="text-left">※課金対象</div>
                                        <input type="hidden"
                                               name="settings[hotline24ServiceMediaid][billable]"
                                               value="1"/>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="pull-right">
                        <button type="button" class="btn btn-info" data-toggle="modal"
                                data-target="#confirmModal"> 契約内容更新
                        </button>
                        <div class="clearfix"></div>


                    </div>
                </div>
                <!-- /.col-lg-12 -->
            </div>

            <div class="row {!! $company['status'] !== \App\Models\Company::STATUS_PREPARE?'not-edit':'' !!}"
                 id="infoFirstUser">
                <div class="col-lg-12">
                    <div class="center-block text-left">
                        <i class="fa fa-stop"></i> 本部画面初期アカウント
                    </div>

                    <table id="dataTables-example" class="table table-striped table table-user">
                        <tr>
                            <th width="10%">アカウントID<br/>（ログインID）</th>
                            <td width="40%">
                                <input type="text" name="staff[username]" class="form-control alpha-numeric"
                                       value="{!! @$firstStaff['username'] !!}" maxlength="10" minlength="5"
                                       style="width:50%;"/>
                            </td>
                            <th width="10%">部署</th>
                            <td width="">
                                <input type="text" name="staff[department]" class="form-control" maxlength="30"
                                       value="{!! @$firstStaff['department'] !!}"/>
                            </td>
                        </tr>
                        <tr>
                            <th>氏名（漢字）</th>
                            <td>
                                <div class="row text-left">
                                    <div class="col-lg-6 col-md-6 col-sm-12 form-inline"><label>姓</label><input
                                                type="text"
                                                name="staff[first_name]"
                                                maxlength="15"
                                                class="form-control widthcalc"
                                                value="{!! @$firstStaff['first_name'] !!}"
                                                placeholder="調剤"/>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12 form-inline"><label>名</label><input
                                                type="text"
                                                name="staff[last_name]"
                                                maxlength="15"
                                                class="form-control widthcalc"
                                                value="{!! @$firstStaff['last_name'] !!}"
                                                placeholder="太郎"/>
                                    </div>
                                </div>
                            </td>
                            <th>氏名（カナ）</th>
                            <td>
                                <div class="row text-left">
                                    <div class="col-lg-6 col-md-6 colsm-12 form-inline"><label>セイ</label><input
                                                type="text"
                                                name="staff[first_name_kana]"
                                                maxlength="15"
                                                class="form-control katakana widthcalc"
                                                value="{!! @$firstStaff['first_name_kana'] !!}"
                                                placeholder="チョウザイ"/>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12 form-inline"><label>メイ</label><input
                                                type="text"
                                                name="staff[last_name_kana]"
                                                maxlength="15"
                                                class="form-control katakana widthcalc"
                                                value="{!! @$firstStaff['last_name_kana'] !!}"
                                                placeholder="タロウ"/>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                @if($company['status']==\App\Models\Company::STATUS_PREPARE)
                    <div class="col-lg-12 text-right">
                        <button class="btn btn-info btn-edit" type="submit">更新</button>
                    </div>
                @endif
            </div>

            <!-- /.col-lg-12 -->

            <div class="row">
                <div class="col-lg-12">
                    <div class="text-center">
                        @if($company['status']==\App\Models\Company::STATUS_PREPARE)
                            <button type="button" class="btn btn-info" id="btn-save-first-staff" onclick="checkValid('#modalConfirm', '企業情報を更新しますか？')">この企業アカウントの情報を下書き保存する
                            </button>
                        @endif
                        @if($company['status']==\App\Models\Company::STATUS_IN_USE)
                            <button type="button" class="btn btn-danger button-stt" data-toggle="modal"
                                    dataSTT="{!! \App\Models\Company::STATUS_CANCELLATION_COMPLETED !!}"
                                    dataMess="株式会社つばめドラッグの利用を停止してよろしいですか？"
                                    data-target="#confirmModal3">この企業アカウントの利用を停止する
                            </button>
                        @endif
                        @if($company['status']==\App\Models\Company::STATUS_CANCELLATION_COMPLETED || $company['status']==\App\Models\Company::STATUS_PREPARE)
                            <button type="button" class="btn btn-danger button-stt btn-start-used" data-toggle="modal"
                                    disabled
                                    dataSTT="{!! \App\Models\Company::STATUS_IN_USE !!}"
                                    dataMess="株式会社つばめドラッグの利用が可能となります。よろしいですか？"
                                    data-target="#confirmModal3">この企業アカウントの利用を開始する
                            </button>
                        @endif
                        <div class="clearfix"></div>


                    </div>
                </div>
            </div>

            <!-- /#page-wrapper -->

            <!-- Modal -->
            <div class="modal fade" id="myModal" role="dialog">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-body" style="border: 1px solid #ccc; border-radius: 5px; margin: 15px;">
                            <p class="text-center">株式会社つばめドラッグのスタッフアカウントを<span
                                        id="staff_add_number_show">{!! @$company['staff_add']['number'] !!}</span>件追加します。追加理由を記載してください。
                            </p>
                            <input name="infoCpn[staff_add][text]" class="form-control not-require" type="text"
                                   id="text-comment-add-staff"
                                   value="{!! $company['staff_add']['text'] !!}"/>
                        </div>
                        <div class="text-center" style="border-top: none;margin-bottom: 20px; margin-top: 30px;">
                            <button type="button" class="btn btn-primary " data-dismiss="modal">キャンセル</button>
                            <button class="btn  btn-primary" id="btn-submit-1" type="submit">OK
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal2 -->
            <div class="modal fade" id="confirmModal" role="dialog">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                        <div class="modal-body" style="border: 1px solid #ccc; border-radius: 5px; margin: 15px;">
                            <p class="text-center">株式会社つばめドラッグの契約内容を登録します。よろしいですか？</p>
                        </div>
                        <div class="text-center" style="border-top: none;margin-bottom: 20px; margin-top: 30px;">
                            <button type="button" class="btn btn-primary " data-dismiss="modal">キャンセル</button>
                            <button class="btn  btn-primary" id="btn-submit-2" type="submit">OK</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{--Status Company--}}
        <input type="hidden" value="{!! $company['status'] !!}" name="infoCpn[status]"
               id="status_company"/>
    </form>

    <!-- Modal3 -->
    <div class="modal fade" id="confirmModal3" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body" style="border: 1px solid #ccc; border-radius: 5px; margin: 15px;">
                    <p class="text-center text-used-modal">株式会社つばめドラッグの利用が可能となります。よろしいですか？</p>
                </div>
                <div class="text-center" style="border-top: none;margin-bottom: 20px; margin-top: 30px;">
                    <button type="button" class="btn btn-primary " data-dismiss="modal">キャンセル</button>
                    <button class="btn  btn-primary btn-change-status-company">OK</button>
                </div>
            </div>
        </div>
    </div>
    <!-- Modal Confirm info company -->
    <div class="modal fade" id="modalConfirm" role="dialog">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-body" style="border: 1px solid #ccc; border-radius: 5px; margin: 15px;">
                    <p class="text-center content-confirm"></p>
                </div>
                <div class="text-center" style="border-top: none;margin-bottom: 20px; margin-top: 30px;">
                    <button type="button" class="btn btn-primary " data-dismiss="modal">キャンセル</button>
                    <button class="btn  btn-primary" onclick="formSubmit('#update-all-info-company')">OK</button>
                </div>
            </div>
        </div>
    </div>



    <script>
        function checkValid(element, text) {
            var submit = true;
            var inputRequired = $('#page-wrapper').find("input");
            inputRequired.each(function () {
                var input;
                if ($(this).hasClass('required')) {
                    var val = $(this).val();
                    var mess = $(this).attr('data-message');
                    if (val === '') {
                        $(this).popover({
                            html: true,
                            content: '<div class="pull-left" style="padding:0 7px; color: #fff;background-color: #f0ad4e;margin-right: 4px;border-radius:3px;"><i class="fa fa-exclamation"></i></div>' + mess
                        });
                        $(this).popover('show').focus();
                        submit = false;
                    }
                }
            });
            $('.required').on('shown.bs.popover', function () {
                setTimeout(function () {
                    $('.required').popover('destroy');
                }, 2000);
            });
            if (submit) {
                $('.content-confirm').html('').html(text);
                $(element).modal('show');
            }
        }

        function formSubmit(elementForm) {
            $(elementForm).submit();
        }
        //--------------------------------------------------------------------

        $('#staff_add_number').keyup(function () {
            var $value = $('#staff_add_number').val();
            if ($value == '') {
                $('#staff_add_number_show').html('').html(0);
            } else {
                $('#staff_add_number_show').html('').html($value);
            }
        });
        //-------------------------------------------------------
        $('#cert_add_number').keyup(function () {
            var $value = $('#cert_add_number').val();
            if ($value == '') {
                $('#cert_add_number_show').html('').html(0);
            } else {
                $('#cert_add_number_show').html('').html($value);
            }
        });
        //--------------------------------------------------------------------------
        $(document).ready(function () {
            var input = $("input").not(".not-require");
            var $emptyFields = $(input).filter(function () {
                return $.trim(this.value) === "";
            });

            if (!$emptyFields.length) {
                $('.btn-start-used').prop("disabled", false);
            }
            else {
                $('.btn-start-used').prop("disabled", true);
            }
            $(input).keyup(function () {
                $emptyFields = $(input).filter(function () {
                    return $.trim(this.value) === "";
                });

                if (!$emptyFields.length) {
                    $('.btn-start-used').prop("disabled", false);
                }
                else {
                    $('.btn-start-used').prop("disabled", true);
                }
            });
        });
        var tag = '{!! session('tag') !!}';

        $('#form-input-all-info').find('input').attr('readonly', true);
        $('#form-input-all-info').find('tr').addClass('color-block color-block-hover');
        if ($('#infoFirstUser').hasClass('not-edit')) {
            $('#infoFirstUser').find('input[type=radio]').attr('disabled', true);
            $('#infoFirstUser').find('tr').addClass('color-block color-block-hover');
            $('#infoFirstUser').find('input').attr('readonly', true);
        } else {
            $('#infoFirstUser').find('tr').addClass('background-hover-white')
        }
        $('#form-input-all-info').find('input[type=radio]').attr('disabled', true);
        $('#form-input-all-info').find('button').attr('disabled', true);
        if (tag !== '') {
            $('html, body').animate({
                scrollTop: $("#" + tag).offset().top
            }, 500);
            $('.' + tag).find('input').removeAttr('readonly');
            $('.' + tag).find('tr').removeClass('color-block color-block-hover').addClass('background-hover-white');

            $('.' + tag).find('input[type=radio]').removeAttr('disabled');
            $('.' + tag).find('button').removeAttr('disabled');
            $('.' + tag).find('input:visible:enabled:first').focus();
        }

        $('.button-stt').click(function () {
            console.log($('.button-stt').attr('dataMess'));
            $('.text-used-modal').html('').html($('.button-stt').attr('dataMess'))
        });
        //Submit form "form-first-staff-media"
        $('.btn-change-status-company').click(function () {
            $('#status_company').val($('.button-stt').attr('dataSTT'));
            $('#update-all-info-company').submit();
        });

        //Kana
        $.fn.onlyKana = function (config) {
            var defaults = {};
            var options = $.extend(defaults, config);
            return this.each(function () {
                $(this).bind('blur', function () {
                    $(this).val($(this).val().replace(/[^ア-ン゛゜ァ-ォャ-ョーｱ-ﾝﾞﾟｦｧ-ｫｬ-ｮｯｰ]/g, ''));
                });
            });
        };

        $('.katakana').onlyKana();

        $('.phone-numbers') //input phone | fax numbers in one input
                .keydown(function (e) {
                    var key = e.charCode || e.keyCode || 0;
                    $phone = $(this);

                    if (key !== 8 && key !== 9 ) {
                        if ($phone.val().length === 5) {
                            $phone.val($phone.val() + '-');
                        }
                        if ($phone.val().length === 10) {
                            $phone.val($phone.val() + '-');
                        }
                    }

                    return (key == 8 ||
                    key == 9 ||
                    key == 46 ||
                    (key >= 48 && key <= 57) ||
                    (key >= 96 && key <= 105));
                });

        $(".alpha-numeric").keydown(function (e) {
            if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110]) !== -1 ||
                    (e.keyCode == 65 && ( e.ctrlKey === true || e.metaKey === true ) ) ||
                    (e.keyCode >= 35 && e.keyCode <= 40)) {
                return;
            }
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.ctrlKey || e.keyCode < 65 || e.keyCode > 90) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });

        $(".alpha-numeric-2").keydown(function (e) {
            if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110]) !== -1 ||
                    (e.keyCode == 65 && ( e.ctrlKey === true || e.metaKey === true ) ) ||
                    (e.keyCode >= 35 && e.keyCode <= 40) || (e.keyCode == 189)) {
                return;
            }
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.ctrlKey || e.keyCode < 65 || e.keyCode > 90) && (e.keyCode < 96 || e.keyCode > 105)) {
                e.preventDefault();
            }
        });

        $(".numeric").keydown(function (e) {
            if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 68]) !== -1 ||
                    (e.keyCode == 65 && ( e.ctrlKey === true || e.metaKey === true ) ) ||
                    (e.keyCode >= 35 && e.keyCode <= 40)) {
                return;
            }
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57))) {
                e.preventDefault();
            }
        });
    </script>

@endsection