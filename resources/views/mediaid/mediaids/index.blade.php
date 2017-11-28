@extends('layouts.mediaid')

@section('title', __('Summary'))

@section('content')

    <div id="page-wrapper" class="page-statistic head-manager" ng-controller="MediaidsIndexController">
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-12">
                <div class="bs-statistic bs-statistic-tabs">
                    <ul class="nav nav-tabs">
                        <li class="{{$dayActive}}" role="presentation"><a id="home-tab" aria-expanded="false" aria-controls="home" data-toggle="tab" role="tab" href="#tabDayView">日別集計（全店舗合計）</a></li>
                        <li class="{{$monthActive}}" role="presentation"><a id="profile-tab" aria-controls="profile" data-toggle="tab" role="tab" href="#tabMonthView" aria-expanded="true">月別集計（全店舗合計）</a></li>
                        <li class="{{$monthlySummaryActive}}" role="presentation"><a id="profile-tab" aria-controls="profile" data-toggle="tab" role="tab" href="#selectCompanyView" aria-expanded="true">企業別集計</a></li>
                        <li id="tab-current-view" class="hidden" role="presentation"><a id="profile-tab" aria-controls="profile" data-toggle="tab" role="tab" href="#tabCurrentView" aria-expanded="true">本日</a></li>
                    </ul>

                    <div class="tab-content">
                        <div id="tabDayView" class="tab-pane fade {{$dayActive}}" aria-labelledby="home-tab" role="tabpanel">
                            <div class="clearfix top-search">
                                <div class="left-search">
                                    <form method="post" action="{{action('Mediaid\MediaidsController@postIndex')}}">
                                        {{ csrf_field() }}
                                        <table>
                                            <colgroup>
                                                <col width="24%">
                                                <col width="30%">
                                                <col width="5%">
                                                <col width="30%">
                                                <col width="10%">
                                            </colgroup>
                                            <tr>
                                                <th>集計期間：</th>
                                                <td><input name="startDate" onkeypress="return false;" class="form-control datepicker" type="text" placeholder="{{$startDate}}"></td>
                                                <td align="center">～</td>
                                                <td><input name="endDate" onkeypress="return false;" class="form-control datepicker" type="text" placeholder="{{$endDate}}"></td>
                                                <td class="pl10"><button class="btn btn-info" type="submit">表示</button></td>
                                            </tr>
                                        </table>
                                    </form>
                                    <!--/left-search--></div>
                                <div class="right-download">
                                    <a class="btn btn-info" href="{{action('Mediaid\MediaidsController@getDayCsv')}}"><img src="/images/csv_2.png" alt=""> CSVダウンロード</a>
                                </div>
                            </div>

                            <table class="table table-striped statistic-view table-scroll-fixed">
                                <tr>
                                    <th class="not-min-width th-absolute" colspan="2" rowspan="3" style="background-color:inherit;border:none !important; height: 111px"></th>
                                </tr>
                                <tr></tr>
                                <tr></tr>
                                <tr>
                                    <th class="count-store not-min-width th-absolute" colspan="2" style="border-bottom: none !important;">処方せん受信件数</th>
                                </tr>
                                <tr>
                                    <th class="count-store not-min-width th-absolute" style="border-bottom: none !important; border-top: none !important; width: 10px; min-width: 10px"></th>
                                    <th>うち調剤完了件数</th>
                                </tr>
                                <tr>
                                    <th class="th-absolute" style="border-top: none !important; width: 10px; min-width: 10px"></th>
                                    <th class="count-store not-min-width th-absolute">うち無効件数</th>
                                </tr>
                                <tr>
                                    <th class="count-store not-min-width th-absolute" colspan="2">調剤完了件数</th>
                                </tr>
                                <tr>
                                    <th class="count-store not-min-width th-absolute" colspan="2">無効件数</th>
                                </tr>

                                <tr>
                                    <th class="not-min-width no-border-white"></th>
                                </tr>

                                <tr>
                                    <th class="count-store not-min-width th-absolute" colspan="2">登録会員数</th>
                                </tr>

                                <tr>
                                    <th class="count-store not-min-width th-absolute" colspan="2">退会者数</th>
                                </tr>

                                <tr>
                                    <th class="count-store not-min-width th-absolute" colspan="2">累計会員数</th>
                                </tr>
                            </table>

                            <div class="div-statistic-view">
                                <table class="table table-striped statistic-view">
                                    <tr>
                                        @foreach ($viewByDaysCreatedAt as $result)
                                            @if ($result['yearMonth'] != '合計')
                                                <th class="year-month">{{ $result['yearMonth'] }}</th>
                                            @endif
                                        @endforeach
                                        <th colspan="3" rowspan="3" style="height: 111px">合計</th>
                                    </tr>
                                    <tr>
                                        @foreach ($viewByDaysCreatedAt as $result)
                                            @if ($result['day'] != '合計')
                                                <th class="{{ $result['colour'] }}">{{ $result['day'] }}</th>
                                            @endif
                                        @endforeach
                                    </tr>
                                    <tr>
                                        @foreach ($viewByDaysCreatedAt as $result)
                                            @if ($result['day'] != '合計')
                                                <th class="{{ $result['colour'] }}">{{ $result['dateOfWeek'] }}</th>
                                            @endif
                                        @endforeach
                                    </tr>
                                    <tr>
                                        @foreach ($viewByDaysCreatedAt as $result)
                                            <td class="{{ $result['colour'] }}">{{ $result['requestCount'] }}</td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        @foreach ($viewByDaysCreatedAt as $result)
                                            <td class="{{ $result['colour'] }}">{{ $result['completedCount'] }}</td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        @foreach ($viewByDaysCreatedAt as $result)
                                            <td class="{{ $result['colour'] }}">{{ $result['deletedCount'] }}</td>
                                        @endforeach
                                    </tr>

                                    <tr>
                                        @foreach ($viewByDaysPrepared as $result)
                                            <td class="{{ $result['colour'] }}">{{ $result['prepareCount'] }}</td>
                                        @endforeach
                                    </tr>

                                    <tr>
                                        @foreach ($viewByDaysInvalid as $result)
                                            <td class="{{ $result['colour'] }}">{{ $result['invalidCount'] }}</td>
                                        @endforeach
                                    </tr>

                                    <tr>
                                        <td class="no-border-white"></td>
                                    </tr>

                                    <tr>
                                        @foreach ($viewByDaysUserRegister as $result)
                                            <td class="{{ $result['colour'] }}">{{ $result['count'] }}</td>
                                        @endforeach
                                    </tr>

                                    <tr>
                                        @foreach ($viewByDaysUserExited as $result)
                                            <td class="{{ $result['colour'] }}">{{ $result['count'] }}</td>
                                        @endforeach
                                    </tr>

                                    <tr>
                                        @foreach ($viewByDaysUserAll as $result)
                                            <td class="{{ $result['colour'] }}">{{ $result['count'] }}</td>
                                        @endforeach
                                    </tr>

                                </table>
                            </div>
                        </div>

                        <div id="tabMonthView" class="tab-pane fade {{$monthActive}}" aria-labelledby="profile-tab" role="tabpanel">
                            <div class="clearfix top-search">
                                <div class="left-search">
                                    <form method="post" action="{{action('Mediaid\MediaidsController@postIndex')}}">
                                        {{ csrf_field() }}
                                        <table>
                                            <colgroup>
                                                <col width="24%">
                                                <col width="30%">
                                                <col width="5%">
                                                <col width="30%">
                                                <col width="10%">
                                            </colgroup>
                                            <tr>
                                                <th>集計期間：</th>
                                                <td><input name="startMonth" onkeypress="return false;" class="form-control monthpicker" type="text" placeholder="{{$startMonth}}"></td>
                                                <td align="center">～</td>
                                                <td><input name="endMonth" onkeypress="return false;" class="form-control monthpicker" type="text" placeholder="{{$endMonth}}"></td>
                                                <td class="pl10"><button class="btn btn-info" type="submit">表示</button></td>
                                            </tr>
                                        </table>
                                    </form>
                                    <!--/left-search--></div>
                                <div class="right-download">
                                    <a class="btn btn-info" href="{{action('Mediaid\MediaidsController@getMonthCsv')}}"><img src="/images/csv_2.png" alt=""> CSVダウンロード</a>
                                </div>
                            </div>

                            <table class="table table-striped statistic-view table-scroll-fixed">
                                <tr>
                                    <th class="not-min-width" colspan="2" rowspan="2" style="background-color:inherit;border:none !important; height: 74px;"></th>
                                </tr>
                                <tr></tr>
                                <tr>
                                    <th class="count-store not-min-width" colspan="2" style="border-bottom: none !important;">処方せん受信件数</th>
                                </tr>
                                <tr>
                                    <th style="border-bottom: none !important; border-top: none !important; width: 10px; min-width: 10px"></th>
                                    <th class="count-store not-min-width">うち調剤完了件数</th>
                                </tr>
                                <tr>
                                    <th style="border-top: none !important; width: 10px; min-width: 10px"></th>
                                    <th class="count-store not-min-width">うち無効件数</th>
                                </tr>
                                <tr>
                                    <th class="count-store not-min-width" colspan="2">調剤完了件数</th>
                                </tr>
                                <tr>
                                    <th class="count-store not-min-width" colspan="2">無効件数</th>
                                </tr>

                                <tr>
                                    <th class="not-min-width no-border-white"></th>
                                </tr>

                                <tr>
                                    <th class="count-store not-min-width th-absolute" colspan="2">登録会員数</th>
                                </tr>

                                <tr>
                                    <th class="count-store not-min-width th-absolute" colspan="2">退会者数</th>
                                </tr>

                                <tr>
                                    <th class="count-store not-min-width th-absolute" colspan="2">累計会員数</th>
                                </tr>
                            </table>

                            <div class="div-statistic-view">
                                <table class="table table-striped statistic-view">
                                    <tr>
                                        @foreach ($viewByMonthsCreatedAt as $result)
                                            @if ($result['year'] != '合計')
                                                <th class="year-month">{{ $result['year'] }}</th>
                                            @endif
                                        @endforeach
                                        <th colspan="2" rowspan="2" style="height: 74px">合計</th>
                                    </tr>
                                    <tr>
                                        @foreach ($viewByMonthsCreatedAt as $result)
                                            @if ($result['month'] != '合計')
                                                <th class="year-month">{{ $result['month'] }}</th>
                                            @endif
                                        @endforeach
                                    </tr>

                                    <tr>
                                        @foreach ($viewByMonthsCreatedAt as $result)
                                            <td>{{ $result['requestCount'] }}</td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        @foreach ($viewByMonthsCreatedAt as $result)
                                            <td>{{ $result['completedCount'] }}</td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        @foreach ($viewByMonthsCreatedAt as $result)
                                            <td>{{ $result['deletedCount'] }}</td>
                                        @endforeach
                                    </tr>

                                    <tr>
                                        @foreach ($viewByMonthsPrepared as $result)
                                            <td>{{ $result['prepareCount'] }}</td>
                                        @endforeach
                                    </tr>

                                    <tr>
                                        @foreach ($viewByMonthsInvalid as $result)
                                            <td>{{ $result['invalidCount'] }}</td>
                                        @endforeach
                                    </tr>

                                    <tr>
                                        <td class="no-border-white"></td>
                                    </tr>

                                    <tr>
                                        @foreach ($viewByMonthsUserRegister as $result)
                                            <td>{{ $result['count'] }}</td>
                                        @endforeach
                                    </tr>

                                    <tr>
                                        @foreach ($viewByMonthsUserExited as $result)
                                            <td>{{ $result['count'] }}</td>
                                        @endforeach
                                    </tr>

                                    <tr>
                                        @foreach ($viewByMonthsUserAll as $result)
                                            <td>{{ $result['count'] }}</td>
                                        @endforeach
                                    </tr>

                                </table>
                            </div>
                        </div>

                        <div id="selectCompanyView" class="tab-pane in {{$monthlySummaryActive}}" aria-labelledby="profile-tab" role="tabpanel">
                            <p class="mtit clearfix"><strong>■</strong> 集計したい企業、集計期間、集計対象を選択し、ＣＳＶダウンロードボタンを押してください。</p>
                            <div class="clearfix">
                                <div class="col-left">
                                    <div class="mtit"><strong>■</strong> 企業検索</div>
                                    <div class="panel-body body-search">
                                        <form id="searchForm" method="post" action="{{action('Mediaid\MediaidsController@postSearch')}}">

                                            {{ csrf_field() }}

                                            <table>
                                                <colgroup>
                                                    <col width="15%">
                                                    <col width="17%">
                                                    <col width="15%">
                                                    <col width="17%">
                                                    <col width="15%">
                                                    <col width="17%">
                                                </colgroup>
                                                <tr>
                                                    <th>企業名</th>
                                                    <td colspan="2"><input name="company_name" class="form-control" type="text" value = "{{$search['company_name'] or ''}}"></td>
                                                    <td colspan="2"></td>
                                                    <td colspan="2">
                                                        <div class="form-group">
                                                            <label>{!! Form::checkbox('company_all', 'false', @$search['company_all'] == 'false') !!} 全企業を表示</label>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </table>
                                            <div class="clearfix">
                                                <div class="center-block text-center">
                                                    <button class="btn btn-primary btn-lg" type="submit">検索</button>
                                                    <button name="btn_reset" class="btn btn-primary btn-lg" type="submit">検索条件をクリア</button>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <p class="mtit">集計対象の企業にチェックをしてください。</p>

                                    <!--/col-left--></div>

                                <form id="searchForm" name="postSearch" method="post" action="{{action('Mediaid\MediaidsController@postDataCsv')}}">

                                    {{ csrf_field() }}

                                    <div class="col-right">
                                        <div class="mtit"><strong>■</strong> 集計期間</div>
                                        <div class="pl10">
                                            <div class="clearfix"><label><input checked id="optionsDate" class="optionsDateMonth" type="radio" value="0" name="optionsRadios">日次</label></div>
                                            <div class="tbl-filter clearfix">
                                                <div class="pl25 td01 company-td01">集計期間：<br>（最大31日間）</div>
                                                <div class="td02">
                                                    <div class="fleft w150 company-w150">
                                                        <input name="startDate" class="form-control datepicker-companies disabled-input datepicker" type="text" placeholder="----年--月--日">
                                                    </div>
                                                    <div class="fleft from-to">～</div>
                                                    <div class="fleft w150 company-w150">
                                                        <input name="endDate" class="form-control datepicker-companies-end disabled-input datepicker" type="text" placeholder="----年--月--日">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="non-validate-csv day-condition-csv">最大31日間</div>
                                            <div class="clearfix"><label><input id="optionsMonth" class="optionsDateMonth" type="radio" value="1" name="optionsRadios">月次</label></div>
                                            <div class="tbl-filter clearfix">
                                                <div class="pl25 td01 company-td01">集計期間：<br>（最大3ヶ月間）</div>
                                                <div class="td02">
                                                    <div class="fleft w150 company-w150">
                                                        <input name="startMonth" class="form-control monthpicker-companies disabled-input monthpicker" type="text" placeholder="----年--月">
                                                    </div>
                                                    <div class="fleft from-to">～</div>
                                                    <div class="fleft w150 company-w150">
                                                        <input name="endMonth" class="form-control monthpicker-companies-end disabled-input monthpicker" type="text" placeholder="----年--月">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="non-validate-csv month-condition-csv">最大3ヶ月間</div>
                                        </div>
                                        <div class="mtit"><strong>■</strong> 集計対象項目 <label><input class="margin_l20i" id="select-all-checkbox-csv" type="checkbox">請求関連項目</label></div>

                                        <div class="pl10 fleft w350 margin_b10">
                                            <div class="margin_b10">
                                                <div class="form-group">
                                                    <label><input class="select-all-checkbox" id="select-all-orders-type" type="checkbox">処方せん受信</label>
                                                </div>
                                                <div class="form-group margin_l20">
                                                    <label><input class="select-all-checkbox select-orders" id="check" name="viewAllOrderByCreated" type="checkbox" checked> 処方せん受信件数</label>
                                                </div>
                                                <div class="form-group margin_l20">
                                                    <label><input class="select-all-checkbox select-orders" id="check" name="viewOrderCompletedByCreated" type="checkbox"> 処方せん受信件数うち調剤完了件数</label></div>
                                                <div class="form-group margin_l20">
                                                    <label><input class="select-all-checkbox select-orders" id="check" name="viewUserDeletedByCreated" type="checkbox"> 処方せん受信件数うち無効件数</label></div>
                                                <div class="form-group margin_l20">
                                                    <label><input class="select-all-checkbox select-orders" id="check" name="viewOrderByCompleted" type="checkbox"> 調剤完了件数</label></div>
                                                <div class="form-group margin_l20">
                                                    <label><input class="select-all-checkbox select-orders" id="check" name="viewOrderByDelete" type="checkbox" checked> 無効件数</label>
                                                </div>
                                            </div>

                                            <div>
                                                <div class="form-group">
                                                    <label><input class="select-all-checkbox" id="select-all-users-type" type="checkbox">会員数</label>
                                                </div>
                                                <div class="form-group margin_l20">
                                                    <label><input class="select-all-checkbox select-users" id="check" name="viewUserByRegister" type="checkbox">登録会員数</label>
                                                </div>
                                                <div class="form-group margin_l20">
                                                    <label><input class="select-all-checkbox select-users" id="check" name="viewUserByExited" type="checkbox">退会者数</label></div>
                                                <div class="form-group margin_l20">
                                                    <label><input class="select-all-checkbox select-users" id="check" name="viewUserAll" type="checkbox">累計会員数</label></div>
                                            </div>
                                        </div>

                                        <div class="pl10 fleft w250 margin_b10">
                                            <div class="margin_b10">
                                                <div class="form-group">
                                                    <label><input class="select-all-checkbox" id="select-all-settings-type" type="checkbox">契約内容</label>
                                                </div>
                                                <div class="form-group margin_l20">
                                                    <label><input class="select-all-checkbox select-settings" id="check" name="billableText" type="checkbox">基本契約（課金方法）</label>
                                                </div>
                                                <div class="form-group margin_l20">
                                                    <label><input class="select-all-checkbox select-settings" id="check" name="patientReplySettingMediaid" type="checkbox">患者からの返信機能</label></div>
                                                <div class="form-group margin_l20">
                                                    <label><input class="select-all-checkbox select-settings" id="check" name="memberForMessageDeliveryMediaid" type="checkbox">会員向けメッセージ配信機能</label></div>
                                                <div class="form-group margin_l20">
                                                    <label><input class="select-all-checkbox select-settings" id="check" name="hotlineServiceMediaid" type="checkbox">ほっとラインサービス</label></div>
                                                <div class="form-group margin_l20">
                                                    <label><input class="select-all-checkbox select-settings" id="check" name="hotline24ServiceMediaid" type="checkbox">ほっとライン24サービス</label></div>
                                            </div>

                                            <div>
                                                <div class="form-group">
                                                    <label><input class="select-all-checkbox" id="select-all-stores-type" type="checkbox">企業別店舗数</label>
                                                </div>
                                                <div class="form-group margin_l20">
                                                    <label><input class="select-all-checkbox select-stores" id="check" name="countStore" type="checkbox">契約店舗数</label>
                                                </div>
                                                <div class="form-group margin_l20">
                                                    <label><input class="select-all-checkbox select-stores" id="check" name="staffAdd" type="checkbox">スタッフアカウント数 <br>
                                                        （対象期間内追加分）
                                                    </label>
                                                </div>
                                            </div>
                                        </div>


                                        <!--/col-right--></div>

                                    <div class="select-store-table col-left">
                                        <table class="table table-striped tbl-lst-id tbl-csv-condition margin_b0">
                                            <tr>
                                                <th width="70px"></th>
                                                <th width="200px">企業コード</th>
                                                <th>企業名</th>
                                            </tr>
                                        </table>
                                        <div class="table-csv mediaid-table-csv">
                                            <table class="table table-striped tbl-lst-id tbl-csv-condition margin_b0">
                                                @foreach($companies as $c)
                                                    <tr>
                                                        <td width="70px"><div class="form-group"><input name="company_id[]" class="select-company" type="checkbox" value="{{$c->id}}"></div></td>
                                                        <td width="200px">{{$c->id}}</td>
                                                        <td class="text-left">{{$c->name}}</td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                        </div>

                                        <div class="check-all"><div class="form-group"><label><input id="select-all" type="checkbox" value=""> すべてチェック</label></div></div>
                                    </div>

                                    <div class="center-block text-center download-csv-button">
                                        <button id="submit-csv" class="btn btn-info" type="submit"><img src="/images/csv_2.png" alt=""> CSVダウンロード</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div id="tabCurrentView" class="tab-pane fade {{$currentActive}}" aria-labelledby="profile-tab" role="tabpanel">
                            <table>
                                <tr class="border-bottom-white10">
                                    <td class="tab-current-view-td">本日  ：</td>
                                    <td>{{ $viewByCurrent[1]['fullDate'] }} （前日：{{ $viewByCurrent[0]['fullDate'] }}）</td>
                                </tr>
                                <tr>
                                    <td class="tab-current-view-td">受信件数  ：</td>
                                    <td>{{ $viewByCurrent[1]['requestCount'] }}件（{{ $viewByCurrent[0]['requestCount'] }}件）</td>
                                </tr>
                                <tr>
                                    <td class="tab-current-view-td">&nbsp;&nbsp;うち完了件数  ：</td>
                                    <td>{{ $viewByCurrent[1]['completedCount'] }}件（{{ $viewByCurrent[0]['completedCount'] }}件）</td>
                                </tr>
                                <tr class="border-bottom-white10">
                                    <td class="tab-current-view-td">&nbsp;&nbsp;うち無効件数  ：</td>
                                    <td>{{ $viewByCurrent[1]['deletedCount'] }}件（{{ $viewByCurrent[0]['deletedCount'] }}件）</td>
                                </tr>
                                <tr>
                                    <td class="tab-current-view-td">完了件数  ：</td>
                                    <td>{{ $viewByCurrentPrepared[1]['prepareCount'] }}件（{{ $viewByCurrentPrepared[0]['prepareCount'] }}件)</td>
                                </tr>
                                <tr class="border-bottom-white10">
                                    <td class="tab-current-view-td">無効件数  ：</td>
                                    <td>{{ $viewByCurrentInvalid[1]['invalidCount'] }}件（{{ $viewByCurrentInvalid[0]['invalidCount'] }}件）</td>
                                </tr>

                                <tr>
                                    <td class="tab-current-view-td">会員登録数  ：</td>
                                    <td>{{ $viewByUserRegisterCurrent[1]['count'] }}名（{{ $viewByUserRegisterCurrent[0]['count'] }}名)</td>
                                </tr>
                                <tr>
                                    <td class="tab-current-view-td">累計会員数  ：</td>
                                    <td>{{ $viewByUserAllCurrent[1]['count'] }}名（{{ $viewByUserAllCurrent[0]['count'] }}名）</td>
                                </tr>
                            </table>
                        </div>

                    </div>
                    <!--/END Tab--></div>
            </div>
            <!-- /.col-lg-12 -->
        </div>
    </div>
    <!-- /#page-wrapper -->

@endsection