@extends('layouts.sb2admin')

@section('title', '設定')

@section('content')

            <div id="page-wrapper" class="edit-template" ng-controller="SettingsEditController">
                <!-- /.row -->
                <div class="row">
                
                <form id="settingForm" action="{{action('Home\SettingsController@postEdit')}}" method="POST">
                {{ csrf_field() }}
                    <div class="col-lg-12">
                    	<div class="mtit"><i class="fa fa-stop"></i> メッセージテンプレート作成</div>
                        <table class="table table-striped">
                        	<colgroup>
                            	<col width="20%">
                                <col width="60%">
                                <col width="20%">
                            </colgroup>
                        	<tr>
                            	<th>メッセージ区分</th>
                                <td colspan="2">
                                	<div class="form-group w60-percent">
                                    <?php 
                                        $formOptions = ['class' => 'form-control', 'ng-model' => 'Message.message_type'];
                                        if ($editMessage['type'] == 1 || $editMessage['type'] == 2) {
                                            $formOptions['disabled'] = true;
                                        }
                                    ?>
                                        {!! Form::select('txtSelect', \App\Models\MessageTemplate::$messageTypes,'', $formOptions) !!}
                                    </div>
                                </td>
                            </tr>
                            <tr>
                            	<th>テンプレート名（必須）</th>
                                <td colspan="2">


                        

                                <input title="テンプレート名が入力されていません" required ng-model="Message.name" class="form-control" type="text" @if($editMessage['type'] == 1 || $editMessage['type'] == 2) readonly="true"  @endif placeholder="" name="txtName" value="{!! old('txtName',isset($editMessage) ? $editMessage['name'] : "") !!}">
                                {{--<input id="status" name="status" type="hidden" value="0">--}}
                                <span style="color:red">{{ $errors->has('txtName') ? 'テンプレート名が入力されていません。' : '' }}</span>               
                                </td>
                               
                            </tr>
                            <tr>
                            	<th colspan="3">テンプレート内容　※患者さまアプリに表示される内容です</th>
                            </tr>
                            <tr>
                            	<th>タイトル（必須）<input type="hidden" name="id" value="{{ $editMessage['id'] }}"></th>
                                <td style="border-right:none !important;">

                                    <input   title="本文が入力されていません" class="form-control" required
                                                         ng-model="Message.title" maxlength="50"
                                                         type="text" placeholder=""
                                                         name="txtTitle"
                                                         value="{!! old('txtTitle',isset($editMessage) ? $editMessage['title'] : "") !!}">

                               
                                    <span style="color:red">{{ $errors->has('txtTitle') ? 'タイトルが入力されていません。' : '' }}</span>
                                </td>
                                
                                <td style="border-left:none !important;">※50 文字まで</td>
                            </tr>
                            <tr>
                            	<th>本文（必須）<br>使用可能タグ<br>＜％送信者名％＞<br>＜％会員氏名％＞</th>
                                <td style="border-right:none !important;">
                                	<textarea title="本文が入力されていません" required class="txtArrea" cols="2" rows="2" name="txtContent" ng-model="Message.content">
                                            
                                        {!! old('txtContent',isset($editMessage) ? $editMessage['content'] : "") !!}                               
                                       
                                    </textarea>
                                    <span style="color:red">{{ $errors->has('txtContent') ? '本文が入力されていません。' : '' }}</span>
                                    <td style="border-left:none !important;"></td>
                                </td>
                            </tr>
                        </table>
                        <div class="group-btn">
                            <button id="submitBtn" type="submit" class="hidden"></button>
                            <input type="hidden" id="status" name="status" >
                            @if($editMessage['type'] == 3)
                        	<button class="btn btn-primary" type="button" data-toggle="modal" data-target="#myModal">削除</button>
                            @endif
                            <button ng-click="previewMessage()" class="btn btn-primary" type="button"  data-toggle="modal" data-target="#prev">プレビュー</button>
                            <button class="btn btn-primary" type="button" ng-click="saveMessage(0)">下書き</button>
                            <button   class="btn btn-primary" type="button" ng-click="saveMessage(1)">適用</button>

                            <div class="txt-right">※「適用」を押すとテンプレートとして利用可能となります。「適用」できるテンプレートは、３件までです。</div>
                        </div>
                    </div>

                    <!--Modal members set-->
                    <div class="modal fade popup-cmn popview2" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <div class="panel panel-info cont-temp">
                                        <div class="panel-body">
                                            <p>このテンプレートを削除してよろしいですか？</p>
                                        </div>
                                        <!--/cont-temp--></div>
                                </div>
                                <div class="modal-footer">
                                    <div>
                                        <button type="button" class="btn btn-default" data-dismiss="modal" ng-click="completedCancel()">キャンセル</button>
                                        <a href="{!! action('Home\SettingsController@getDestroy',$editMessage['alias']) !!}" class="btn btn-info">OK</a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>



                    <div class="modal fade popup-cmn" id="prev" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document" style="width: 150px;">
                            <div class="modal-content">
                                <div class="modal-body" >
                                    <div class="view-txt ">
                                        <div class="inner-txt">
                                            <p class="title-txt">{{\App\Models\Store::current('name')}}</p>
                                            <img src="/img/icon-store.jpg" style="width: 10%;">
                                            <div class="frame-txt " style="float: right; width: 90%;">
                                                
                                                <p ng-bind="Message.title"></p><br/>
                                                <p ng-bind="Message.content"></p>
                                                
                                                <span class="icon-bottom"><img src="/images/icon-frame-txt.png" alt=""></span>
                                            </div>
                                            <div class="box-user">
                                                <div class="photo-usser"></div>
                                               
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <div>
                                        <button type="button" class="btn btn-info" data-dismiss="modal">閉じる</button>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="modal fade popup-cmn popview2" id="errorModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-body">
                                    <div class="panel panel-info cont-temp">
                                        <div class="panel-body">
                                            <p>適用できるテンプレートは3件までです。このテンプレ<br/>ートを適用する場合は、一旦下書き保存し、<br/>現在適用中のテンプレートを1件「未使用」の状態にし<br/>てから適用を押してください。</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <div>
                                        <button type="button" class="btn btn-info" data-dismiss="modal" ng-click="completedCancel()">閉じる</button>                                        
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>     

                    </form>
                    <!-- /.col-lg-12 -->
                </div>
            </div>
            <!-- /#page-wrapper -->


@endsection