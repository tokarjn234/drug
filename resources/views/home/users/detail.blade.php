@extends('layouts.sb2admin')

@section('title', __('UsersManagement'))

@section('content')

   <div id="page-wrapper" class="member-detail">
                <!-- /.row -->
                <div class="row">
                    <div class="col-lg-12">
                    	<table class="table table-striped tbl-detail-member">
                        	<colgroup>
                                <col width="5%">
                                <col width="3%">
                                <col width="10%">
                                <col width="8%">
                                <col width="8%">
                                <col width="8%">
                                <col width="4%">
                            </colgroup>
                        	<tr>
                                <th colspan="2">会員ID</th>
                                <td><a href="#">{{$user['id']}}</a></td>
                                <th>性別</th>
                                <td colspan="3">{{ $user['gender_check'] }}</td>
                            </tr>

                            <tr>
                                <th colspan="2">カナ氏名</th>
                                <td>{{$user['name_kana']}}</td>
                                <th>生年月日</th>
                                <td colspan="3">{{ $user['birthday_check'] }}</td>
                            </tr>

                            <tr>
                                <th colspan="2">会員氏名</th>
                                <td>{{$user['name']}}</td>
                                <th>歳</th>
                                <td colspan="3">{!! $user['age_check_detail'] !!}</td>
                            </tr>

                            <tr>
                                <th colspan="2">携帯電話番号</th>
                                <td>{{ $user['phone_number'] }}</td>
                                <th>郵便番号</th>
                                <td colspan="3">{{$user['postal_code_check']}}</td>
                            </tr>

                            <tr>
                                <th colspan="2">携帯メールアドレス</th>
                                <td>{{$user['email']}}</td>
                                <th>住所</th>
                                <td colspan="3">{{$user['address_full']}}</td>
                            </tr>

                            <tr>
                                <th colspan="2">最終送信日時</th>
                                <td>{!! $user['detail_order_created_at'] !!}</td>
                                <th>会員登録日時</th>
                                <td colspan="3">{!! $user['detail_created_at'] !!}</td>
                            </tr>

                            <tr>
                                <th colspan="2">最終受信受付番号</th>
                                <td>{{$user['short_order_code']}}</td>
                                <th>退会日時</th>
                                <td colspan="3">{{$user['exited_at']}}</td>
                            </tr>
                        </table>
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
            </div>
            <!-- /#page-wrapper -->

@endsection