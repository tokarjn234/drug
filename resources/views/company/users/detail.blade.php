@extends('layouts.company')

@section('title', '会員管理')

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
                                <td><a href="#">{{ empty($user['id']) ? '-' : $user['id'] }}</a></td>
                                <th>性別</th>
                                <td colspan="3">{{ empty($user['gender_check']) ? '-' : $user['gender_check'] }}</td>
                            </tr>

                            <tr>
                                <th colspan="2">カナ氏名</th>
                                <td>{{ empty($user['name_kana']) ? '-' : $user['name_kana'] }}</td>
                                <th>生年月日</th>
                                <td colspan="3">{{ empty($user['birthday_check']) ? '-' : $user['birthday_check'] }}</td>
                            </tr>

                            <tr>
                                <th colspan="2">会員氏名</th>
                                <td>{{ empty($user['name']) ? '-' : $user['name'] }}</td>
                                <th>歳</th>
                                <td colspan="3">{!! empty($user['age_check_detail']) ? '-' : $user['age_check_detail'] !!}</td>
                            </tr>

                            <tr>
                                <th colspan="2">携帯電話番号</th>
                                <td>{{ empty($user['phone_number']) ? '-' : $user['phone_number'] }}</td>
                                <th>郵便番号</th>
                                <td colspan="3">{{ empty($user['postal_code_check']) ? '-' : $user['postal_code_check'] }}</td>
                            </tr>

                            <tr>
                                <th colspan="2">携帯メールアドレス</th>
                                <td>{{ empty($user['email']) ? '-' : $user['email'] }}</td>
                                <th>住所</th>
                                <td colspan="3">{{ $user['address_full'] or '-' }}</td>
                            </tr>

                            <tr>
                                <th colspan="2">最終送信日時</th>
                                <td>{!! empty($user['detail_order_created_at']) ? '-' : $user['detail_order_created_at'] !!}</td>
                                <th>会員登録日時</th>
                                <td colspan="3">{!! empty($user['detail_created_at']) ? '-' : $user['detail_created_at'] !!}</td>
                            </tr>

                            <tr>
                                <th colspan="2">最終受信受付番号</th>
                                <td>{{empty($user['short_order_code']) ? '-' : $user['short_order_code'] }}</td>
                                <th>退会日時</th>
                                <td colspan="3">{{empty($user['exited_time']) ? '-' : $user['exited_time'] }}</td>
                            </tr>

                            <tr>
                                <th colspan="2">処方めーる受付店舗</th>
                                <td colspan="5">
                                    @foreach ($userStores as $k => $userStore)
                                        {{$userStore['store_name']}} ({{date('Y/m/d', strtotime($userStore['order_created_at']))}})
                                        @if(isset($userStores[$k + 1]) > 0) 、
                                        @endif
                                    @endforeach
                                </td>
                            </tr>
                        </table>
                        <div class="clearfix">
                            	<div class="center-block text-center">
                                    @if($user['status'] == 3 || $user['status'] == 6)
                                        <button class="btn btn-primary btn-leave" type="button">退会処理</button>
                                        @if($user['status'] == 3)
                                            <button name="btn_reset" class="btn btn-primary btn-log" type="button">アカウントロック</button>
                                        @else
                                            <button name="btn_reset" class="btn btn-primary btn-cancel-log" type="button">ロック解除</button>
                                        @endif
                                    @endif
                                </div>
                            </div>
                    </div>
                     
                    <!-- /.col-lg-12 -->
                </div>
            </div>
            <!-- /#page-wrapper -->
            
            <!--Modal-->
		    <div class="modal fade popup-cmn popview2" id="ConfirmLeaveDialog" tabindex="-1" role="dialog"
		         aria-labelledby="myModalLabel">
		        <div class="modal-dialog" role="document">
		            <div class="modal-content">
		                <div class="modal-body">
		                    <div class="panel panel-info cont-temp">
		                        <div class="panel-body">
		                            <p>このアカウントの退会処理を行いますか？</p>
		                            <p>一度行った退会処理を取り消すことはできません。</p>
		                        </div>
		                        <!--/cont-temp--></div>
		                </div>
		                <div class="modal-footer">
		                    <div>
		                        <button type="button" class="btn btn-default" data-dismiss="modal">いいえ</button>
		                        <a class="btn btn-info btn-update" href="{{action('Company\UsersController@getStatusLeave')}}/{{$user['alias']}}">
		                        	はい
		                        </a>
		                    </div>
		                </div>
		            </div>
		        </div>
		    </div>
		    
		    <div class="modal fade popup-cmn popview2" id="ConfirmLockDialog" tabindex="-1" role="dialog"
		         aria-labelledby="myModalLabel">
		        <div class="modal-dialog" role="document">
		            <div class="modal-content">
		                <div class="modal-body">
		                    <div class="panel panel-info cont-temp">
		                        <div class="panel-body">
		                            <p>のアカウントをロックしますか？</p>
		                            <p>ロックしている間はこのアカウントはログイン<br/>することができなくなります。</p>
		                            
		                        </div>
		                        <!--/cont-temp--></div>
		                </div>
		                <div class="modal-footer">
		                    <div>
		                        <button type="button" class="btn btn-default" data-dismiss="modal">いいえ</button>
		                        <a class="btn btn-info btn-update" href="{{action('Company\UsersController@getStatusLock')}}/{{$user['alias']}}">
		                        	はい
		                        </a>
		                    </div>
		                </div>
		            </div>
		        </div>
		    </div>
		    <!--End Modal-->

           <div class="modal fade popup-cmn popview2" id="ConfirmCancelLockDialog" tabindex="-1" role="dialog"
                aria-labelledby="myModalLabel">
               <div class="modal-dialog" role="document">
                   <div class="modal-content">
                       <div class="modal-body">
                           <div class="panel panel-info cont-temp">
                               <div class="panel-body">
                                   <p>アカウントロックを解除しました。</p>
                                   <p>再びログインできるようになります。</p>

                               </div>
                               <!--/cont-temp--></div>
                       </div>
                       <div class="modal-footer">
                           <div>
                               <button type="button" class="btn btn-default" data-dismiss="modal">いいえ</button>
                               <a class="btn btn-info btn-update" href="{{action('Company\UsersController@getStatusCancelLock')}}/{{$user['alias']}}">
                                   はい
                               </a>
                           </div>
                       </div>
                   </div>
               </div>
           </div>

		    <script type="text/javascript">
		    $(".btn-leave").click(function () {
                $("#ConfirmLeaveDialog").modal();
            });
		    $(".btn-log").click(function () {
                $("#ConfirmLockDialog").modal();
            });
            $(".btn-cancel-log").click(function () {
                $("#ConfirmCancelLockDialog").modal();
            });

            </script>

@endsection