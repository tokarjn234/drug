@extends('layouts.sb2admin')

@section('title', '集計')

@section('content')
    <div id="page-wrapper" class="page-statistic head-manager">
                <!-- /.row -->
                <div class="row">
                    <div class="col-lg-12">
                    	<div class="bs-statistic bs-statistic-tabs">
                            <ul class="nav nav-tabs">
                                <li class="{{$dayActive}}" role="presentation"><a data-toggle="tab" role="tab" href="#tabDayView">日別集計</a></li>
                                <li class="{{$monthActive}}" role="presentation"><a data-toggle="tab" role="tab" href="#tabMonthView">月別集計</a></li>
                                <li id="tab-current-view" class="hidden" role="presentation"><a data-toggle="tab" role="tab" href="#tabCurrentView">本日</a></li>
                            </ul>

                            <div class="tab-content">
                                <div id="tabDayView" class="tab-pane fade {{$dayActive}}" aria-labelledby="home-tab" role="tabpanel">
                                    <div class="clearfix top-search">
                                    	<div class="left-search">
                                            <form method="post" action="{{action('Home\StoresController@postIndex')}}">
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
                                            <a class="btn btn-info" href="{{action('Home\StoresController@getDayCsv')}}"><img src="/images/csv_2.png" alt=""> CSVダウンロード</a>
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
                                                    @if($result['yearMonth'] != '合計')
                                                        <th class="year-month">{{ $result['yearMonth'] }}</th>
                                                    @endif
                                                @endforeach
                                                <th colspan="3" rowspan="3" style="height: 111px">合計</th>
                                            </tr>
                                            <tr>
                                                @foreach ($viewByDaysCreatedAt as $result)
                                                    @if($result['day'] != '合計')
                                                        <th class="{{ $result['colour'] }}">{{ $result['day'] }}</th>
                                                    @endif
                                                @endforeach
                                            </tr>
                                            <tr>
                                                @foreach ($viewByDaysCreatedAt as $result)
                                                    @if($result['day'] != '合計')
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
                                            <form method="post" action="{{action('Home\StoresController@postIndex')}}">
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
                                            <a class="btn btn-info" href="{{action('Home\StoresController@getMonthCsv')}}"><img src="/images/csv_2.png" alt=""> CSVダウンロード</a>
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
                                                    @if($result['year'] != '合計')
                                                        <th class="year-month">{{ $result['year'] }}</th>
                                                    @endif
                                                @endforeach
                                                <th colspan="2" rowspan="2" style="height: 74px">合計</th>
                                            </tr>
                                            <tr>
                                                @foreach ($viewByMonthsCreatedAt as $result)
                                                    @if($result['month'] != '合計')
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
                                                {{--<th class="count-store not-min-width" colspan="2">無効件数</th>--}}
                                                @foreach ($viewByMonthsInvalid as $result)
                                                    <td>{{ $result['invalidCount'] }}</td>
                                                @endforeach
                                            </tr>

                                        </table>
                                    </div>
                                <!--/tab2--></div>
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
                        </div>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
            </div>
            <!-- /#page-wrapper -->

    <script>
        $(document).ready(function () {
            $('#menu-current-view').click(function(){
                $('#tab-current-view a').trigger('click');
            });

            $('.statistic-view .year-month').each(function(){
                var colSpan=1;
                while( $(this).text() == $(this).next().text() ){
                    $(this).next().remove();
                    colSpan++;
                }
                $(this).attr('colSpan',colSpan);
            });
            ;
        });
    </script>
@endsection