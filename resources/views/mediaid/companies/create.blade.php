@extends('layouts.mediaid')

@section('title', '登録企業管理')

@section('content')
    <?php
    $billable = [
            0 => '0',
            1 => '1',
            2 => 'その他'
    ];
    ?>

    <div id="page-wrapper">
        <form method="post"
              action="{!! action('Mediaid\CompaniesController@postCreate') !!}"
              id="form-all-info-company" data-toggle="validator">
            {{ csrf_field() }}
            <input type="hidden" name="status" value="{!! \App\Models\Company::STATUS_PREPARE !!}"/>
            <!-- /.row -->
            <div class="row" id="form-input-all-info">
                @if (count($errors) > 0)
                    <div class="col-lg-12 col-md-12 col-sm-12 margin_t20 alert alert-danger">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <div class="col-lg-12 infoCompany" id="infoCompany">
                    <div class="center-block text-left margin_t10">
                        <i class="fa fa-stop"></i> 企業情報
                    </div>

                    <table id="dataTables-example" class="table table-striped table table-user">
                        <tr>
                            <th width="20%" colspan="2">項目名</th>
                            <th colspan="3">登録情報</th>
                        </tr>
                        <tr>
                            <th colspan="2">企業名</th>
                            <td colspan="3">
                                <input name="name" type="text" class="form-control required"
                                       maxlength="50"
                                       value="{!! empty(Input::old('name'))?'':Input::old('name')!!}"
                                       data-message="企業名が入力されていません。"
                                       data-toggle="popover" data-placement="top"
                                       placeholder=""/>
                            </td>
                        </tr>
                        <tr>
                            <th colspan="2">代表者名</th>
                            <td colspan="3">
                                <input name="name_manager"
                                       maxlength="30"
                                       value="{!! empty(Input::old('name_manager'))? '': Input::old('name_manager')!!}"
                                       type="text" class="form-control"/>
                            </td>
                        </tr>
                        <tr>
                            <th colspan="2">代表電話番号</th>
                            <td>
                                <input name="phone_number"
                                       value="{!! empty(Input::old('phone_number'))? '': Input::old('phone_number')!!}"
                                       type="text" maxlength="15" class="form-control numeric phone-numbers"/>
                            </td>
                            <th>FAX番号</th>
                            <td>
                                <input name="fax"
                                       value="{!! empty(Input::old('fax'))? '': Input::old('fax')!!}"
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
                                        <input name="postal_code_headquarters[0]" type="text"
                                               class="form-control w100_imp numeric"
                                               value="{!! empty(Input::old('postal_code_headquarters.0'))? @$postal_code_headquarters[0]: Input::old('postal_code_headquarters.0')!!}"
                                               maxlength="3"/>
                                        -
                                        <input name="postal_code_headquarters[1]" type="text"
                                               value="{!! empty(Input::old('postal_code_headquarters.1'))? @$postal_code_headquarters[1]: Input::old('postal_code_headquarters.1')!!}"
                                               class="form-control w100_imp numeric" maxlength="4"/>
                                    </div>
                                </div>
                        </tr>
                        <tr>
                            <th>住所</th>
                            <td colspan="3"><input name="headquarters"
                                                   value="{!! empty(Input::old('headquarters'))? @$company['headquarters']: Input::old('headquarters')!!}"
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
                                        <input name="bill_to_postal_code[0]" type="text"
                                               class="form-control w100_imp numeric"
                                               value="{!! empty(Input::old('bill_to_postal_code.0'))? @$bill_to_postal_code[0]: Input::old('bill_to_postal_code.0')!!}"
                                               maxlength="3"/>
                                        -
                                        <input name="bill_to_postal_code[1]" type="text"
                                               value="{!! empty(Input::old('bill_to_postal_code.1'))? @$bill_to_postal_code[1]: Input::old('bill_to_postal_code.1')!!}"
                                               class="form-control w100_imp numeric" maxlength="4"/>
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>住所</th>
                            <td colspan="3"><input name="bill_to_address"
                                                   value="{!! empty(Input::old('bill_to_address'))? @$company['bill_to_address']: Input::old('bill_to_address')!!}"
                                                   type="text" maxlength="50" class="form-control"/></td>
                        </tr>
                        <tr>
                            <th>宛先</th>
                            <td colspan="3"><input name="bill_to_destination"
                                                   value="{!! empty(Input::old('bill_to_destination'))? @$company['bill_to_destination']: Input::old('bill_to_destination')!!}"
                                                   type="text" maxlength="80" class="form-control"/></td>
                        </tr>

                        <tr>
                            <th rowspan="3">システム担当者</th>
                            <th>部署名</th>
                            <td colspan="3">
                                <input name="meta_company[division]" type="text"
                                       value="{!! empty(Input::old('meta_company.division'))? @$company['meta_company']['division']: Input::old('meta_company.division')!!}"
                                       class="form-control"/>
                            </td>
                        </tr>
                        <tr>
                            <th>氏名</th>
                            <td colspan="3"><input name="meta_company[name_division]"
                                                   value="{!! empty(Input::old('meta_company.name_division'))? @$company['meta_company']['name_division']: Input::old('meta_company.name_division')!!}"
                                                   type="text" class="form-control"/></td>
                        </tr>
                        <tr>
                            <th>電話番号</th>
                            <td><input name="meta_company[phone_number]" type="text"
                                       value="{!! empty(Input::old('meta_company.phone_number'))? @$company['meta_company']['phone_number']: Input::old('meta_company.phone_number')!!}"
                                       maxlength="15" class="form-control numeric phone-numbers"/></td>
                            <th>FAX番号</th>
                            <td><input name="meta_company[fax_division]" type="text"
                                       value="{!! empty(Input::old('meta_company.fax_division'))? '': Input::old('meta_company.fax_division')!!}"
                                       maxlength="15" class="form-control numeric phone-numbers"/></td>
                        </tr>
                    </table>
                    <div class="pull-right">
                        <button type="button" onclick="checkValid('#modalConfirm', '企業情報を登録しますか？')"
                                class="btn btn-info btn-edit">登録
                        </button>
                        {{--<button type="submit" class="btn btn-info btn-edit">登録</button>--}}
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
                            <table id="dataTables-example" class="table table-striped table table-user">
                                <tr>
                                    <th width="20%">店舗数</th>
                                    <td>
                                        <div class="row">
                                            <div class="col-lg-8 col-md-8 col-sm-8"><input name="contract_store"
                                                                                           type="number"
                                                                                           min="0" max="100000"
                                                                                           value="{!! empty(Input::old('contract_store'))? 0: Input::old('contract_store')!!}"
                                                                                           class="form-control numeric"/>
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-4">
                                                <button type="button"
                                                        onclick="checkValid('#modalConfirm', '企業情報を登録しますか？」')"
                                                        class="btn btn-info btn-edit">登録する
                                                </button>
                                                {{--<button class="btn btn-info btn-sm" type="submit"> 登録する</button>--}}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <!-- /.col-lg-12 -->
                <div class="col-lg-12 numberStaff" id="numberStaff">
                    <div class="center-block text-left">
                        <i class="fa fa-stop"></i>
                        スタッフアカウント数登録　：　発行済アカウント数：
                        {{--$staff['basicStaff'] + $company['staff_add']['number'] --}}
                        <br/>
                        ※基本アカウントは、店舗が増えた場合には自動的に追加されます。追加アカウントの発行は、原則として課金対象です。
                    </div>
                    <div class="row">
                        <div class="col-lg-6">

                            <table id="dataTables-example" class="table table-striped table table-user">
                                <tr>
                                    <th width="20%">基本</th>
                                    <td>
                                        <div class="row">
                                            <div class="col-lg-5 col-md-5 col-sm-5"><input name="basicStaffPerStore"
                                                                                           type="number"
                                                                                           min="0"
                                                                                           max=""
                                                                                           value="0"
                                                                                           class="form-control numeric"/>
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-3" style="padding:8px 0;">×契約店舗数
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-4">
                                                <button type="button"
                                                        onclick="checkValid('#modalConfirm', '企業情報を登録しますか？」')"
                                                        class="btn btn-info btn-edit">登録する
                                                </button>
                                                {{--<button class="btn btn-info btn-sm"> 登録する</button>--}}
                                            </div>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <th>追加</th>
                                    <td>
                                        <div class="row">
                                            <div class="col-lg-8 col-md-8 col-sm-8">

                                                <input name="staff_add[number]" type="number"
                                                       id="staff_add_number"
                                                       min="0" max="100000"
                                                       value="{!! empty(Input::old('staff_add.number'))? 0: Input::old('staff_add.number')!!}"
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

                            </table>
                        </div>
                    </div>
                </div>
                <!-- /.col-lg-12 -->

                <!--Certificate Begin-->
                <div class="col-lg-12 numberCertificate" id="numberCertificate">
                    <div>
                        <i class="fa fa-stop"></i> 端末証明書追加 : 発行済アカウント数 : {!! @$cert['availableCount'] !!}<br/>
                        ※端末証明書は、店舗が増えた場合に自動的に追加されます。
                        追加発行は、原則として課金対象です。
                    </div>
                    <div class="row">

                        <div class="col-lg-6">
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
                                                        value="{!! empty(Input::old('numberCertificatePerStore'))? 0: Input::old('numberCertificatePerStore')!!}"
                                                        class="form-control numeric"/>
                                            </div>
                                            <div class="col-lg-3 col-md-3 col-sm-3" style="padding:8px 0;">×契約店舗数
                                            </div>
                                            <div class="col-lg-4 col-md-4 col-sm-4">
                                                <button type="button" onclick="checkValid('#modalConfirm', '企業情報を登録しますか？')"
                                                        class="btn btn-info btn-edit">登録
                                                </button>
                                                {{--<button class="btn btn-info btn-sm" type="submit"> 更新</button>--}}
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th width="20%">追加</th>
                                    <td>
                                        <div class="row">
                                            <div class="col-lg-8 col-md-8 col-sm-8">
                                                <input name="cert_add[number]"
                                                       type="number"
                                                       min="0"
                                                       max=""
                                                       id="cert_add_number"
                                                       value="{!! empty(Input::old('cert_add.number'))? 0: Input::old('cert_add.number')!!}"
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
                                                                <input name="cert_add[text]"
                                                                       class="form-control not-require" type="text"
                                                                       id="text-comment-add-cert"
                                                                       value="{!! empty(Input::old('cert_add.text'))? '': Input::old('cert_add.text')!!}"/>
                                                            </div>
                                                            <div class="text-center"
                                                                 style="border-top: none;margin-bottom: 20px; margin-top: 30px;">
                                                                <button type="button" class="btn btn-primary "
                                                                        data-dismiss="modal"
                                                                        onclick="clearData('#text-comment-add-cert')">
                                                                    キャンセル
                                                                </button>
                                                                <button class="btn  btn-primary" id="btn-submit-1"
                                                                        data-dismiss="modal"
                                                                        type="button">OK
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
                        <i class="fa fa-stop"></i> 処方せん画像保存期間<br/>※{!! @$setting['numberDayDeleteImage'] !!}
                        日を超える場合に課金対象です。

                    </div>
                    <table id="dataTables-example" class="table table-striped table table-user">
                        <tr>
                            <th width="20%">処方せん画像削除</th>
                            <td>
                                <div class="row">
                                    <div class="col-lg-3 col-md-3" style="padding:8px 0;">処方せん受信翌日</div>
                                    <div class="text-left col-lg-3 col-md-3">
                                        <input type="number" min="0" name="numberDayDeleteImage"
                                               class="form-control"
                                               value="{!! empty(Input::old('numberDayDeleteImage'))? 0: Input::old('numberDayDeleteImage')!!}"/>
                                    </div>

                                    <div class="col-lg-3 col-md-3" style="padding:8px 0;">日後に削除する</div>
                                    <div class="col-lg-3 col-md-3">
                                        <button type="button" onclick="checkValid('#modalConfirm', '企業情報を登録しますか？')"
                                                class="btn btn-info btn-edit">登録
                                        </button>
                                        {{--<button class="btn btn-info" type="submit">更新</button>--}}
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

                    <table id="dataTables-example" class="table table-striped table table-user">
                        <tr>
                            <th colspan="2">基本契約</th>
                        </tr>
                        <tr>
                            <th width="20%">課金方法</th>
                            <td>
                                <div class="text-left">
                                    <label>{!! Form::radio('billable[billable]', 2, 2) !!}
                                        その他</label><br/>
                                    <input type="text" name="billable[text]" class="form-control"
                                           value="{!! empty(Input::old('billable.text'))? '': Input::old('billable.text')!!}"
                                           maxlength="200"/>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <table id="dataTables-example" class="table table-striped table table-user">
                        <tr>
                            <th colspan="3">追加オプション</th>
                        </tr>
                        <tr>
                            <th width="50%">患者からの返信機能</th>
                            <td width="200px">
                                <div class="pull-left">
                                    <label>
                                        {!! Form::radio('patientReplySettingMediaid[used]', 1, @$setting['patientReplySettingMediaid']['used']) !!}
                                        利用する
                                    </label>
                                    <label>
                                        {!! Form::radio('patientReplySettingMediaid[used]', 0, @!$setting['patientReplySettingMediaid']['used']) !!}
                                        利用しない
                                    </label>
                                </div>

                                <div class="pull-left" style="margin-left: 20px; padding-top: 3px;">
                                    <div class="text-left">※課金対象</div>
                                    <input type="hidden" name="patientReplySettingMediaid[billable]" value="1"/>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th width="50%">会員向けメッセージ配信機能</th>
                            <td>
                                <div class="pull-left">
                                    <label>
                                        {!! Form::radio('memberForMessageDeliveryMediaid[used]', 1, @$setting['memberForMessageDeliveryMediaid']['used']) !!}
                                        利用する
                                    </label>
                                    <label>
                                        {!! Form::radio('memberForMessageDeliveryMediaid[used]', 0, @!$setting['memberForMessageDeliveryMediaid']['used']) !!}
                                        利用しない
                                    </label>
                                </div>

                                <div class="pull-left" style="margin-left: 20px; padding-top: 3px;">
                                    <div class="text-left">※課金対象</div>
                                    <input type="hidden" name="memberForMessageDeliveryMediaid[billable]" value="1"/>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th width="50%">ほっとラインサービス</th>
                            <td>
                                <div class="pull-left">
                                    <label>
                                        {!! Form::radio('hotlineServiceMediaid[used]', 1, @$setting['hotlineServiceMediaid']['used']) !!}
                                        利用する
                                    </label>
                                    <label>
                                        {!! Form::radio('hotlineServiceMediaid[used]', 0, @!$setting['hotlineServiceMediaid']['used']) !!}
                                        利用しない
                                    </label>
                                </div>

                                <div class="pull-left" style="margin-left: 20px; padding-top: 3px;">
                                    <div class="text-left">※課金対象</div>
                                    <input type="hidden" name="hotlineServiceMediaid[billable]" value="1"/>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th width="50%">ほっとライン24サービス</th>
                            <td>
                                <div class="pull-left">
                                    <label>
                                        {!! Form::radio('hotline24ServiceMediaid[used]', 1, @$setting['hotline24ServiceMediaid']['used']) !!}
                                        利用する
                                    </label>
                                    <label>
                                        {!! Form::radio('hotline24ServiceMediaid[used]', 0, @!$setting['hotline24ServiceMediaid']['used']) !!}
                                        利用しない
                                    </label>
                                </div>

                                <div class="pull-left" style="margin-left: 20px; padding-top: 3px;">
                                    <div class="text-left">※課金対象</div>
                                    <input type="hidden" name="hotline24ServiceMediaid[billable]" value="1"/>
                                </div>
                            </td>
                        </tr>
                    </table>
                    <script>
                        function triggerCustomMsg() {
                            $("input[name=name]").setCustomValidity("This email is already used");
                        }
                    </script>
                    <div class="pull-right">
                        <button type="button" onclick="checkValid('#modalConfirm', '企業情報を登録しますか？')"
                                class="btn btn-info">契約内容更新
                        </button>
                        <div class="clearfix"></div>
                    </div>
                </div>
                <!-- /.col-lg-12 -->
            </div>


            {{--6-2. app cho quản lý #8--}}
            <div class="row"
                 id="infoFirstUser">
                <div class="col-lg-12">
                    <div class="center-block text-left">
                        <i class="fa fa-stop"></i> 本部画面初期アカウント
                    </div>

                    <table id="dataTables-example" class="table table-striped table table-user">
                        <tr>
                            <th width="10%">アカウントID<br/>（ログインID）</th>
                            <td width="40%">
                                <input pattern="^[a-zA-Z0-9]+$" type="text" name="staff[username]"
                                       class="form-control alpha-numeric" maxlength="10" minlength="5"
                                       value="{!! empty(Input::old('staff.username'))? '': Input::old('staff.username')!!}"
                                       style="width:50%;"/>
                            </td>
                            <th width="10%">部署</th>
                            <td width="">
                                <input type="text" name="staff[department]" class="form-control" maxlength="30"
                                       value="{!! empty(Input::old('staff.department'))? '': Input::old('staff.department')!!}"/>
                            </td>
                        </tr>
                        <tr>
                            <th>氏名（漢字）</th>
                            <td>
                                <div class="row text-left">
                                    <div class="col-lg-6 col-md-6 col-sm-12 form-inline"><label>姓</label><input
                                                maxlength="15"
                                                type="text"
                                                name="staff[first_name]"
                                                class="form-control widthcalc"
                                                value="{!! empty(Input::old('staff.first_name'))? '': Input::old('staff.first_name')!!}"
                                                placeholder="調剤"/></div>
                                    <div class="col-lg-6 col-md-6 col-sm-12 form-inline"><label>名</label><input
                                                maxlength="15"
                                                type="text"
                                                name="staff[last_name]"
                                                class="form-control widthcalc"
                                                value="{!! empty(Input::old('staff.last_name'))? '': Input::old('staff.last_name')!!}"
                                                placeholder="太郎"/></div>
                                </div>
                            </td>
                            <th>氏名（カナ）</th>
                            <td>
                                <div class="row text-left">
                                    <div class="col-lg-6 col-md-6 col-sm-12 form-inline"><label>セイ</label><input
                                                maxlength="15"
                                                type="text"
                                                name="staff[first_name_kana]"
                                                class="form-control katakana widthcalc"
                                                value="{!! empty(Input::old('staff.first_name_kana'))? '': Input::old('staff.first_name_kana')!!}"
                                                placeholder="チョウザイ"/>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12 form-inline"><label>メイ</label><input
                                                maxlength="15"
                                                type="text"
                                                name="staff[last_name_kana]"
                                                class="form-control katakana widthcalc"
                                                value="{!! empty(Input::old('staff.last_name_kana'))? '': Input::old('staff.last_name_kana')!!}"
                                                placeholder="タロウ"/></div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- /.col-lg-12 -->

            <div class="row">
                <div class="col-lg-12">
                    <div class="text-center">
                        <button type="button" onclick="checkValid('#modalConfirm', '企業情報を登録しますか？')"
                                class="btn btn-info">この企業アカウントの情報を下書き保存する
                        </button>
                        {{--<button type="submit" class="btn btn-info" id="btn-save-first-staff">この企業アカウントの情報を下書き保存する--}}
                        {{--</button>--}}
                        <button type="button" class="btn btn-danger btn-start-used" data-toggle="modal" disabled
                                data-target="#confirmModal3">この企業アカウントの利用を開始する
                        </button>
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
                            <p class="text-center">株式会社つばめドラッグのスタッフアカウントを<span id="staff_add_number_show">0</span>件追加します。追加理由を記載してください。
                            </p>
                            <input name="staff_add[text]" class="form-control not-require" type="text"
                                   id="text-comment-add-staff"
                                   value="{!! empty(Input::old('staff_add.text'))? '': Input::old('staff_add.text')!!}"/>
                        </div>
                        <div class="text-center" style="border-top: none;margin-bottom: 20px; margin-top: 30px;">
                            <button type="button" class="btn btn-primary " data-dismiss="modal"
                                    onclick="clearData('#text-comment-add-staff')">キャンセル
                            </button>
                            <button class="btn  btn-primary" id="btn-submit-1" data-dismiss="modal">OK</button>
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
                            <button class="btn  btn-primary" id="btn-submit-2">OK</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal3 -->
            <div class="modal fade" id="confirmModal3" role="dialog">
                <div class="modal-dialog modal-sm">
                    <div class="modal-content">
                        <div class="modal-body" style="border: 1px solid #ccc; border-radius: 5px; margin: 15px;">
                            <p class="text-center">株式会社つばめドラッグの利用が可能となります。よろしいですか？</p>
                        </div>
                        <div class="text-center" style="border-top: none;margin-bottom: 20px; margin-top: 30px;">
                            <button type="button" class="btn btn-primary " data-dismiss="modal">キャンセル</button>
                            <button type="button" id="submit-form-with-all-info" class="btn  btn-primary">OK</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
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
                    <button class="btn  btn-primary" onclick="formSubmit('#form-all-info-company')">OK</button>
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
        //----------------------------------------------------------

        $(document).ready(function () {
            var input = $("input").not(".not-require");
            $(input).keyup(function () {
                var $emptyFields = $(input).filter(function () {
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

        //Set message
        document.addEventListener("DOMContentLoaded", function () {
            var elements = $("input[name=name]");
            for (var i = 0; i < elements.length; i++) {
                elements[i].oninvalid = function (e) {
                    e.target.setCustomValidity("");
                    if (!e.target.validity.valid) {
                        e.target.setCustomValidity("企業名が入力されていません。");
                    }
                };
                elements[i].oninput = function (e) {
                    e.target.setCustomValidity("");
                };
            }
        })
        $('#submit-form-with-all-info').click(function () {
            $('input[name=status]').val('{!! \App\Models\Company::STATUS_IN_USE !!}');
            $('#form-all-info-company').submit();

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

                if (key !== 8 && key !== 9) {
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
            if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110]) !== -1 ||
                    (e.keyCode == 65 && ( e.ctrlKey === true || e.metaKey === true ) ) ||
                    (e.keyCode >= 35 && e.keyCode <= 40)) {
                return;
            }
            if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57))) {
                e.preventDefault();
            }
        });
        function clearData(e) {
            $(e).val('');
        }
    </script>

@endsection