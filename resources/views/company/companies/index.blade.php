@extends('layouts.company')

@section('title', __('Summary'))

@section('content')

    <div id="page-wrapper" class="page-statistic head-manager" ng-controller="CompaniesIndexController">
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-12">
                <div class="bs-statistic bs-statistic-tabs">
                    <ul class="nav nav-tabs">
                        <li class="{{$dayActive}}" role="presentation"><a id="home-tab" aria-expanded="false" aria-controls="home" data-toggle="tab" role="tab" href="#tabDayView">日別集計（全店舗）</a></li>
                        <li class="{{$monthActive}}" role="presentation"><a id="profile-tab" aria-controls="profile" data-toggle="tab" role="tab" href="#tabMonthView" aria-expanded="true">月別集計（全店舗）</a></li>
                        <li class="{{$monthlySummaryActive}}" role="presentation"><a id="profile-tab" aria-controls="profile" data-toggle="tab" role="tab" href="#selectStoreView" aria-expanded="true">店舗別集計</a></li>
                        <li id="tab-current-view" class="hidden" role="presentation"><a id="profile-tab" aria-controls="profile" data-toggle="tab" role="tab" href="#tabCurrentView" aria-expanded="true">本日</a></li>
                    </ul>

                    <div class="tab-content">
                        <div id="tabDayView" class="tab-pane fade {{$dayActive}}" aria-labelledby="home-tab" role="tabpanel">
                            <div class="clearfix top-search">
                                <div class="left-search">
                                    <form method="post" action="{{action('Company\CompaniesController@postIndex')}}">
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
                                    <a class="btn btn-info" href="{{action('Company\CompaniesController@getDayCsv')}}"><img src="/images/csv_2.png" alt=""> CSVダウンロード</a>
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

                                </table>
                            </div>
                        </div>

                        <div id="tabMonthView" class="tab-pane fade {{$monthActive}}" aria-labelledby="profile-tab" role="tabpanel">
                            <div class="clearfix top-search">
                                <div class="left-search">
                                    <form method="post" action="{{action('Company\CompaniesController@postIndex')}}">
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
                                    <a class="btn btn-info" href="{{action('Company\CompaniesController@getMonthCsv')}}"><img src="/images/csv_2.png" alt=""> CSVダウンロード</a>
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

                                </table>
                            </div>
                        </div>

                        <div id="selectStoreView" class="tab-pane in {{$monthlySummaryActive}}" aria-labelledby="profile-tab" role="tabpanel">
                            <p class="mtit clearfix"><strong>■</strong> 集計したい店舗、集計期間、集計対象を選択し、ＣＳＶダウンロードボタンを押してください。</p>
                            <div class="clearfix">
                                <div class="col-left">
                                    <div class="mtit"><strong>■</strong> 店舗検索</div>
                                    <div class="panel-body body-search">
                                        <form id="searchForm" method="post" action="{{action('Company\CompaniesController@postSearch')}}">

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
                                                    <th>都道府県</th>
                                                    <td>
                                                        {!! Form::select('province', $provinces, @(int)$search['province'], ['class' => 'form-control', 'id' => 'province_list']) !!}
                                                    </td>
                                                    <td></td>
                                                    <th class="pl10">市区町</th>
                                                    <td colspan="2">
                                                        <select class="form-control" name="city1" id="city1_list"></select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>店舗名</th>
                                                    <td colspan="2"><input name="store_name" class="form-control" type="text" value = "{{$search['store_name'] or ''}}"></td>
                                                    <td colspan="2"></td>
                                                    <td colspan="2">
                                                        <div class="form-group">
                                                            <label>{!! Form::checkbox('store_all', 'false', @$search['store_all'] == 'false') !!}   全店舗を表示 </label>
                                                            {{--<label for="check"><input name="store_all" id="check" type="checkbox" value="1"> 全店舗を表示</label>--}}
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
                                    <p class="mtit">集計対象の店舗にチェックをしてください。</p>

                                    <!--/col-left--></div>

                                <form id="searchForm" name="postSearch" method="post" action="{{action('Company\CompaniesController@postDataCsv')}}">

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
                                        <div class="mtit"><strong>■</strong> 集計対象項目</div>
                                        <div class="pl10">
                                            <div class="form-group">
                                                <label><input id="check" name="checkCreated" type="checkbox" value="1" checked>処方せん受信件数</label>
                                            </div>
                                            <div class="form-group">
                                                <label><input id="check" name="checkCreatedCompleted" type="checkbox" value="1">処方せん受信件数うち調剤完了件数</label></div>
                                            <div class="form-group">
                                                <label><input id="check" name="checkCreatedDeleted" type="checkbox" value="1">処方せん受信件数うち無効件数</label></div>
                                            <div class="form-group">
                                                <label><input id="check" name="checkCompleted" type="checkbox" value="1">調剤完了件数</label></div>
                                            <div class="form-group">
                                                <label><input id="check" name="checkDeleted" type="checkbox" value="1" checked> 無効件数</label>
                                            </div>
                                        </div>
                                        <!--/col-right--></div>

                                    <div class="select-store-table col-left">
                                        <table class="table table-striped tbl-lst-id tbl-csv-condition margin_b0">
                                            <tr>
                                                <th width="70px"></th>
                                                <th width="200px">店舗コード</th>
                                                <th>店舗名</th>
                                            </tr>
                                        </table>

                                        <div class="table-csv company-table-csv">
                                            <table class="table table-striped tbl-lst-id tbl-csv-condition margin_b0">
                                                @foreach($stores as $s)
                                                    <tr>
                                                        <td width="70px"><div class="form-group"><input name="store_id[]" class="select-store" type="checkbox" value="{{$s->id}}"></div></td>
                                                        <td width="200px">{{$s->id}}</td>
                                                        <td class="text-left">{{$s->name}}</td>
                                                    </tr>
                                                @endforeach
                                            </table>
                                        </div>

                                        <div class="check-all"><div class="form-group"><label><input id="select-all" type="checkbox" value=""> すべてチェック</label></div></div>
                                        <div id="list-cites" class="hidden">{!! json_encode($cityListRelation) !!}</div>
                                        <div id="search-city1" class="hidden">{!! @(int)$search['city1'] !!}</div>
                                    </div>

                                    <div class="center-block text-center download-csv-button">
                                        <button id="submit-csv" class="btn btn-info" type="submit"><img src="/images/csv_2.png" alt=""> CSVダウンロード</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        <div id="tabCurrentView" class="tab-pane fade {{$currentActive}}" aria-labelledby="profile-tab" role="tabpanel">
                            <table>
                                <tr>
                                    <td class="tab-current-view-td">本日  ：</td>
                                    <td>{{ $viewByCurrent[1]['fullDate'] }} （前営業日：{{ $viewByCurrent[0]['fullDate'] }}）</td>
                                </tr>
                                <tr>
                                    <td class="tab-current-view-td">処方せん受信件数  ：</td>
                                    <td>{{ $viewByCurrent[1]['requestCount'] }}件（{{ $viewByCurrent[0]['requestCount'] }}件）</td>
                                </tr>
                                <tr>
                                    <td class="tab-current-view-td">&nbsp;&nbsp;うち調剤完了件数  ：</td>
                                    <td>{{ $viewByCurrent[1]['completedCount'] }}件（{{ $viewByCurrent[0]['completedCount'] }}件）</td>
                                </tr>
                                <tr>
                                    <td class="tab-current-view-td">&nbsp;&nbsp;うち無効件数  ：</td>
                                    <td>{{ $viewByCurrent[1]['deletedCount'] }}件（{{ $viewByCurrent[0]['deletedCount'] }}件）</td>
                                </tr>
                                <tr>
                                    <td class="tab-current-view-td">調剤完了件数  ：</td>
                                    <td>{{ $viewByCurrentPrepared[1]['prepareCount'] }}件（{{ $viewByCurrentPrepared[0]['prepareCount'] }}件)</td>
                                </tr>
                                <tr>
                                    <td class="tab-current-view-td">無効件数  ：</td>
                                    <td>{{ $viewByCurrentInvalid[1]['invalidCount'] }}件（{{ $viewByCurrentInvalid[0]['invalidCount'] }}件）</td>
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