@extends('layouts.sb2admin')

@section('title', __('UsersManagement'))

@section('content')

    <div id="page-wrapper">
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-12">
                <div class="panel-body body-search">
                    <form id="searchForm" ng-submit="search($event)" method="post"
                          action="{{action('Home\UsersController@postIndex')}}">
                        {{ csrf_field() }}
                        <table class="tbl-search">
                            <colgroup>
                                <col width="11%">
                                <col width="14%">
                                <col width="11%">
                                <col width="14%">
                                <col width="11%">
                                <col width="14%">
                                <col width="11%">
                                <col width="12%">
                            </colgroup>
                            <tr>
                                <th>会員ID</th>
                                <td><input name="member_id" value="{{$search['member_id'] or ''}}"
                                           class="form-control display-inline" type="text" placeholder=""></td>
                                <td align="right">氏名（漢字）</td>
                                <td><input name="name" value="{{$search['name'] or ''}}" class="form-control"
                                           type="text" placeholder=""></td>
                                <td align="right">氏名（カナ）</td>
                                <td><input name="name_kana" value="{{$search['name_kana'] or ''}}"
                                           class="form-control display-inline" type="text" placeholder=""></td>
                                <td align="right">性別</td>
                                <td>
                                    {!! Form::select('gender', \App\Models\User::$genders, @$search['gender'], ['class' => 'form-control']) !!}
                                </td>

                            </tr>
                            <tr>
                                <th>生年月日</th>
                                <td><input id="date_1" name="birthday_start" onkeypress="return false;"
                                           value="{{$search['birthday_start'] or ''}}" class="form-control datepicker"
                                           type="text" placeholder="----年--月--日"></td>
                                <td align="center">～</td>
                                <td><input id="date_1" name="birthday_end" onkeypress="return false;"
                                           value="{{$search['birthday_end'] or ''}}" class="form-control datepicker"
                                           type="text" placeholder="----年--月--日"></td>
                                <td align="right">誕生月</td>
                                <td>{!! Form::select('month_birthday', \App\Models\User::$months, @$search['month_birthday'], ['class' => 'form-control']) !!}</td>
                                <td></td>
                                <td></td>
                            </tr>
                        </table>
                        <table>
                            <colgroup>
                                <col width="11.5%">
                                <col width="14%">
                                <col width="9%">
                                <col width="9%">
                                <col width="14%">
                                <col width="9%">
                                <col width="5%">
                                <col width="16%">
                                <col width="14%">
                            </colgroup>
                            <tr>
                                <th>最終送信日時</th>
                                <td style="padding-left:0.4%;"><input name="order_created_date_start"
                                                                      onkeypress="return false;"
                                                                      value="{{$search['order_created_date_start'] or ''}}"
                                                                      id="orderStart" class="form-control datepicker"
                                                                      type="text" placeholder="----年--月--日"></td>
                                <td align="center"><input id="orderStartTime" disabled onkeypress="return false;"
                                                          name="order_created_time_start"
                                                          value="{{$search['order_created_time_start'] or ''}}"
                                                          class="form-control time_cmn timepicker" type="text"
                                                          placeholder="--" value=""></td>
                                <td align="center">～</td>
                                <td><input name="order_created_date_end" onkeypress="return false;"
                                           value="{{$search['order_created_date_end'] or ''}}" id="orderEnd"
                                           class="form-control datepicker" type="text" placeholder="----年--月--日"></td>
                                <td align="center"><input id="orderEndTime" disabled onkeypress="return false;"
                                                          name="order_created_time_end"
                                                          value="{{$search['order_created_time_end'] or ''}}"
                                                          class="form-control time_cmn timepicker" type="text"
                                                          placeholder="--" value=""></td>
                                <td></td>
                                <td align="right">最終受信受付番号</td>
                                <td><input name="order_code" value="{{$search['order_code'] or ''}}"
                                           class="form-control display-inline" type="text" placeholder=""></td>
                            </tr>
                            <tr>
                                <td colspan="9" align="right">
                                    <label class="pl10">{!! Form::checkbox('is_member', 'false', @$search['is_member'] == 'false') !!}
                                        退会を除く</label>
                                </td>
                            </tr>
                        </table>
                        <div class="clearfix">
                            <div class="center-block text-center">
                                <button class="btn btn-primary btn-lg" type="submit">検索</button>
                                <button name="btn_reset" class="btn btn-primary btn-lg" type="submit">検索条件をクリア</button>
                                <a class="btn btn-info fRight"
                                   href="{{action('Home\UsersController@getIndex')}}?csv=true">
                                    <img alt="" src="images/csv_2.png"> CSVダウンロード
                                </a>
                            </div>
                        </div>
                    </form>

                </div>
                <!-- /.panel-body -->
                <table id="dataTables-example" class="table table-user">
                    <tr>
                        <th></th>
                        <th class="min-width-65">{{__('UserMemberID')}}</th>
                        <th>{{__('UserMemberName')}}</th>
                        <th>{{__('UserKanaName')}}</th>
                        <th class="min-width-50">{{__('UserGender')}}</th>
                        <th class="min-width-100">{{__('UserBirthday')}}</th>
                        <th class="min-width-100">{{__('UserRegistrationDate')}}</th>
                        <th class="min-width-100">{{__('UserTransmissionDate')}}</th>
                        <th class="min-width-120">{!!__('UserFinalVisit')!!}</th>
                        <th>{{__('UserWithdrawal')}}</th>
                    </tr>
                    @foreach ($users as $k => $user)
                        <tr
                                @if($user['is_checkout'])
                                class="check-out"
                                @endif
                                >
                            <td>{{ ($k+1) + ($paginate->currentPage()-1)*10 }}</td>
                            <td><a href="{{action('Home\UsersController@getDetail')}}/{{$user['alias']}}">{{$user['id']}}</a> </td>
                            <td>{{$user['name']}}</td>
                            <td>{{$user['name_kana']}}</td>
                            <td>{!! $user['gender_check'] !!}</td>
                            <td>{!! $user['birthday_check'] !!}
                                <br/>
                                {!! $user['age'] !!}
                            </td>
                            <td>{!! $user['created_at_check'] !!}</td>
                            <td>{!! $user['order_created_at'] !!}</td>
                            <td><a href="#">{{$user['short_order_code']}}</a></td>
                            <td>
                                @if($user['is_checkout'])
                                    <span class="fa fa-check"></span>
                                @endif

                            </td>
                        </tr>
                    @endforeach

                </table>

                <nav id="pagination">
                    @include('shared.pagination', ['paginator' => $paginate])
                </nav>

            </div>
            <!-- /.col-lg-12 -->
        </div>
    </div>
    <!-- /#page-wrapper -->

@endsection