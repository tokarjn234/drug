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
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-12">
                <div class="margin_t20">
                    <div class="button-status-company-red pull-right">{!! \App\Models\Company::$status[$company['status']] !!}</div>
                    <div class="pull-right text-header-company">
                        企業コード：{!! str_pad($company['id'], 4, '0', STR_PAD_LEFT) !!}　企業名：{!! $company['name'] !!}</div>
                </div>
                <div class="clearfix"></div>


                <div class="center-block text-left margin_t10">
                    <i class="fa fa-stop"></i> 企業情報
                </div>

                <table id="dataTables-example" class="table table-striped table table-user">
                    <tr>
                        <th width="20%" colspan="2">項目名</th>
                        <th>登録情報</th>
                    </tr>
                    <tr>
                        <th colspan="2">企業名</th>
                        <td>
                            <div class="text-left">{!! $company['name'] !!}</div>
                        </td>
                    </tr>
                    <tr>
                        <th colspan="2">代表者名</th>
                        <td>
                            <div class="text-left">{!! $company['name_manager'] !!}</div>
                        </td>
                    </tr>
                    <tr>
                        <th colspan="2">代表電話番号</th>
                        <td>
                            <div class="text-left">{!! $company['phone_number'] !!}</div>
                        </td>
                    </tr>
                    <tr>
                        <th colspan="2">FAX番号</th>
                        <td>
                            <div class="text-left">{!! $company['fax'] !!}</div>
                        </td>
                    </tr>
                    <tr>
                        <th colspan="2">本社住所</th>
                        <td>
                            <div class="text-left">{!! $company['headquarters'] !!}</div>
                        </td>
                    </tr>
                    <tr>
                        <th rowspan="2">請求書送付先</th>
                        <th>住所</th>
                        <td>
                            <div class="text-left">{!! $company['bill_to_address'] !!}</div>
                        </td>
                    </tr>
                    <tr>
                        <th>宛先</th>
                        <td>
                            <div class="text-left">{!! $company['bill_to_destination'] !!}</div>
                        </td>
                    </tr>
                    <tr>
                        <th rowspan="4">システム担当者</th>
                        <th>部署名</th>
                        <td>
                            <div class="text-left">{!! $company['meta_company']['division'] !!}</div>
                        </td>
                    </tr>
                    <tr>
                        <th>氏名</th>
                        <td>
                            <div class="text-left">{!! $company['meta_company']['name_division'] !!}</div>
                        </td>
                    </tr>
                    <tr>
                        <th>電話番号</th>
                        <td>
                            <div class="text-left">{!! $company['meta_company']['phone_number'] !!}</div>
                        </td>
                    </tr>
                    <tr>
                        <th>FAX番号</th>
                        <td>
                            <div class="text-left">{!! $company['meta_company']['fax_division'] !!}</div>
                        </td>
                    </tr>
                </table>
                <div class="pull-right"><a
                            href="{!! action('Mediaid\CompaniesController@getEdit') !!}?id={!! $company['alias'] !!}&tag=infoCompany"
                            class="btn btn-info btn-edit">編集</a></div>
                <div class="clearfix"></div>


            </div>
            <!-- /.col-lg-12 -->
            <div class="col-lg-12">
                <div class="margin_t20">
                    <div class="pull-right">
                        企業コード：{!! str_pad($company['id'], 4, '0', STR_PAD_LEFT) !!}　企業名：{!! $company['name'] !!}</div>
                </div>


                <div class="center-block text-left margin_t10">
                    <i class="fa fa-stop"></i> 利用状況
                </div>
                <div class="row">
                    <div class="col-lg-4">
                        <table id="dataTables-example" class="table table-striped table table-user">
                            <tr>
                                <th colspan="2">店舗数</th>
                            </tr>
                            <tr>
                                <th width="50%">契約店舗数</th>
                                <td>{!! $company['contract_store'] !!}</td>
                            </tr>
                            <tr>
                                <th>公開店舗数</th>
                                <td>{!! count($storePublic) !!}</td>
                            </tr>
                        </table>
                        <div class="pull-right"><a
                                    href="{!! action('Mediaid\CompaniesController@getEdit') !!}?id={!! $company['alias'].'&tag=numberStore' !!}"
                                    class="btn btn-info btn-edit">編集</a></div>
                        <div class="clearfix"></div>
                    </div>
                    <div class="col-lg-7 col-lg-push-1">
                        <table id="dataTables-example" class="table table-striped table table-user">
                            <tr>
                                <th colspan="5">スタッフアカウント数</th>
                            </tr>
                            <tr>
                                <th rowspan="2" width="10%">発行済</th>
                                <th width="10%">基本</th>
                                <td width="30%">{!! $staff['basicStaffPerStore']*$company['contract_store'] !!}</td>
                                <th width="20%">利用中</th>
                                <td>{!! $staff['usedStaff'] !!}</td>
                            </tr>
                            <tr>
                                <th>追加</th>
                                <td>
                                    {!! @$company['staff_add']['number'] !!}
                                </td>
                                <th>削除済</th>
                                <td>{!! $staff['deletedStaff'] !!}</td>
                            </tr>
                            <tr>
                                <th colspan="2">残数</th>
                                <td colspan="3">{!! $staff['freeStaff'] !!}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="pull-right"><a
                            href="{!! action('Mediaid\CompaniesController@getEdit') !!}?id={!! $company['alias'].'&tag=numberStaff' !!}"
                            class="btn btn-info btn-edit">+スタッフアカウント追加</a></div>
                <div class="clearfix"></div>
            </div>
            <!-- /.col-lg-12 -->
            <!-- Info Cert Company Begin -->
            <div class="col-lg-7">
                <table id="dataTables-example" class="table table-striped table table-user">
                    <tr>
                        <th colspan="5">端末証明書数</th>
                    </tr>
                    <tr>
                        <th rowspan="2" width="10%">発行済</th>
                        <th width="10%">基本</th>
                        <td width="30%">{!! $staff['numberCertificatePerStore']*$company['contract_store'] !!}</td>
                        <th width="20%">店舗配布済</th>
                        <td>{!! @$cert['dividedToStoreCount'] !!}</td>
                    </tr>
                    <tr>
                        <th>追加</th>
                        <td>
                            {!! @$company['cert_add']['number'] !!}
                        </td>
                        <th>導入済</th>
                        <td>{!! @$cert['dividedToDeviceCount'] !!}</td>
                    </tr>
                    <tr>
                        <th colspan="2">無効</th>
                        <td>{!! @$cert['inactiveCount'] !!}</td>
                        <th>残数</th>
                        <td>{!! @$cert['availableCount'] !!}</td>
                    </tr>
                </table>
                <div class="pull-right"><a
                            href="{!! action('Mediaid\CompaniesController@getEdit') !!}?id={!! $company['alias'].'&tag=numberCertificate' !!}"
                            class="btn btn-info btn-edit">＋端末証明書追加</a></div>
            </div>
            <div class="clearfix"></div>
            <!-- Info Cert Company End -->
            <!--Delete Image begin-->
            <div class="col-lg-7">
                <div class="center-block text-left margin_t10">
                    <i class="fa fa-stop"></i> 処方せん画像保存期間
                </div>
                <table id="dataTables-example" class="table table-striped table table-user">
                    <tr>
                        <th>処方せん画像削除</th>
                        <td>
                            <div class="text-left">
                                処方せん受信翌日～{!! $staff['numberDayDeleteImage'] !!}日経過後に削除
                            </div>
                        </td>
                    </tr>
                </table>
                <div class="pull-right"><a
                            href="{!! action('Mediaid\CompaniesController@getEdit') !!}?id={!! $company['alias'] !!}&tag=deleteImage"
                            class="btn btn-info btn-edit">編集</a></div>
                <div class="clearfix"></div>
            </div>
            <!--Delete Image end-->
            <div class="col-lg-12">
                <div class="center-block text-left margin_t10">
                    <i class="fa fa-stop"></i> 契約内容
                </div>

                <table id="dataTables-example" class="table table-striped table table-user">
                    <tr>
                        <th colspan="2">基本契約</th>
                    </tr>
                    <tr>
                        <th>課金対象</th>
                        <td>
                            <div class="text-left">
                                <i class="fa fa-circle"></i> {!! $billable[$company['billable']['billable']] !!}<br/>
                                {!! $company['billable']['text'] !!}
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
                        <td>{!! @$setting['patientReplySettingMediaid']['used'] !!}</td>
                        <td>
                            <div class="text-left">{!! @$setting['patientReplySettingMediaid']['billable'] !!}</div>
                        </td>
                    </tr>
                    <tr>
                        <th width="50%">会員向けメッセージ配信機能</th>
                        <td>{!! @$setting['memberForMessageDeliveryMediaid']['used'] !!}</td>
                        <td>
                            <div class="text-left">{!! @$setting['memberForMessageDeliveryMediaid']['billable'] !!}</div>
                        </td>
                    </tr>
                    <tr>
                        <th width="50%">ほっとラインサービス</th>
                        <td>{!! @$setting['hotlineServiceMediaid']['used'] !!}</td>
                        <td>
                            <div class="text-left">{!! @$setting['hotlineServiceMediaid']['billable'] !!}</div>
                        </td>
                    </tr>
                    <tr>
                        <th width="50%">ほっとライン24サービス</th>
                        <td>{!! @$setting['hotline24ServiceMediaid']['used'] !!}</td>
                        <td>
                            <div class="text-left">{!! @$setting['hotline24ServiceMediaid']['billable'] !!}</div>
                        </td>
                    </tr>
                </table>
                <div class="pull-right"><a
                            href="{!! action('Mediaid\CompaniesController@getEdit') !!}?id={!! $company['alias'].'&tag=settingCompany' !!}"
                            class="btn btn-info btn-edit">契約内容修正</a></div>
                <div class="clearfix"></div>


            </div>
            <!-- /.col-lg-12 -->
        </div>
    </div>
    <!-- /#page-wrapper -->
    <script>

    </script>

@endsection