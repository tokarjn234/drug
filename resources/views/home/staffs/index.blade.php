@extends('layouts.sb2admin')

@section('title', __('StaffsManagement'))

@section('content')

   <div id="page-wrapper">
                <!-- /.row -->
                <div class="row">
                    <div class="col-lg-12">
                        <div class="panel-body body-search">
                        <form ng-submit="search($event)" method="post" action="{{action('Home\StaffsController@postIndex')}}">
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
                                    <td><input  name="id" value="{{Request::input('id')}}" class="form-control display-inline" type="text" placeholder=""></td>
                                    <td align="right">氏名（漢字）</td>
                                    <td><input class="form-control" type="text" placeholder=""></td>
                                    <td align="right">氏名（カナ）</td>
                                    <td><input class="form-control display-inline" type="text" placeholder=""></td>
                                    <td align="right">性別 </td>
                                    <td>
                                    	<select class="form-control">
                                            <option>未対応</option>
                                            <option>調剤完了</option>
                                            <option>受付通知</option>
                                            <option>無効</option>
                                        </select>
                                    </td>
                                    
                                </tr>
                                <tr>
                                	<th>生年月日</th>
                                    <td><input id="date_1" class="form-control" type="text" placeholder="----年--月--日"></td>
                                    <td align="center">～</td>
                                    <td><input id="date_1" class="form-control" type="text" placeholder="----年--月--日"></td>
                                    <td align="right">誕生月</td>
                                    <td><input class="form-control display-inline" type="text" placeholder=""></td>
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
                                        <td style="padding-left:0.4%;"><input id="date_1" class="form-control datepicker" type="text" placeholder="----年--月--日"></td>
                                        <td align="center"><input class="form-control time_cmn" type="text" placeholder="--" value=""></td>
                                        <td align="center">～</td>
                                        <td><input id="date_1" class="form-control datepicker" type="text" placeholder="----年--月--日"></td>
                                        <td align="center"><input class="form-control time_cmn" type="text" placeholder="--" value=""></td>
                                        <td></td>
                                        <td align="right">最終受信受付番号</td>
                                        <td><input class="form-control display-inline" type="text" placeholder=""></td>
                                    </tr>
                                    <tr>
                                	<td colspan="9" align="right"><label class="pl10" for="check"> <input id="check" type="checkbox" value=""> 退会を除く</label></td>
                                </tr>
                                </table>
                            <div class="clearfix">
                            	<div class="center-block text-center">
                                	<button class="btn btn-primary btn-lg" type="submit">検索</button>
                                    <button class="btn btn-info fRight" type="button"><img alt="" src="images/csv_2.png">CSVダウンロード</button>
                                </div>
                            </div>
                            </form>
                            
                        </div>
                        <!-- /.panel-body -->
                        <table id="dataTables-example" class="table table-striped">
                        	<tr>
                            	<th></th>
                                <th>{{__('StaffMemberID')}}</th>
                                <th>{{__('StaffMemberName')}}</th>
                                <th>{{__('StaffKanaName')}}</th>
                                <th>{{__('StaffGender')}}</th>
                                <th>{{__('StaffBirthday')}}</th>
                                <th>{{__('StaffAge')}}</th>
                                <th>{{__('StaffRegistrationDate')}}</th>
                                <th>{{__('StaffTransmissionDate')}}</th>
                                <th>{!! __('StaffFinalVisit') !!}</th>
                                <th>{{__('StaffWithdrawal')}}</th>
                            </tr>
                            @foreach ($staffs as $staff)
                            <tr class="check-out">
                            	<td>1</td>
                                <td><a href="#">{{$staff['id']}}</a></td>
                                <td>{{$staff['first_name']}}　{{$staff['last_name']}}</td>
                                <td>{{$staff['first_name_kana']}}　{{$staff['last_name_kana']}}</td>
                                <td>{!! $staff['$gender'] !!}</td>
                                <td>{!! $staff['birthday'] !!}</td>
                                <td>{!! $staff['$age'] !!}</td>
                                <td>2015/12/1　<br>10:00</td>
                                <td>2015/12/1　<br>９:50</td>
                                <td><a href="#">00001</a></td>
                                <td></td>
                            </tr>
                             @endforeach
                           
                        </table>
                        <nav id="pagination">
                        {!! $paginate->render() !!}

                    </nav>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
            </div>
            <!-- /#page-wrapper -->

@endsection