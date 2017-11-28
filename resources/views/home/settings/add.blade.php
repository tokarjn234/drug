@extends('layouts.sb2admin')

@section('title', '設定')

@section('content')

            <div id="page-wrapper" class="edit-template" ng-controller="SettingsAddMessageController">
                <!-- /.row -->
                <div class="row">
                <form id="settingForm" name="settingForm" action="{{action('Home\SettingsController@postAdd')}}"  method="POST">
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
                                        {!! Form::select('txtSelect', \App\Models\MessageTemplate::$messageTypes,'', ['class' => 'form-control']) !!}
                                    </div>
                                </td>
                            </tr>
                            <tr>
                            	<th>テンプレート名 （必須）</th>
                                <td colspan="2"><input title="テンプレート名が入力されていません。" required ng-model="Message.name" class="form-control "  type="text" name="txtName" placeholder="" ng-value="'{{Input::old('txtName')}}'"/>

                                </td>
                                
                            </tr>
                            <tr>
                            	<th colspan="3">テンプレート内容　※患者さまアプリに表示される内容です</th>
                            </tr>
                            <tr>
                            	<th>タイトル（必須）</th>
                                <td style="border-right:none !important;"><input title="タイトルが入力されていません" required ng-model="Message.title" class="form-control" maxlength="50" type="text" placeholder=""  name="txtTitle" ng-value="'{{Input::old('txtTitle')}}'"/>

                                </td>
                                
                                <td style="border-left:none !important;">※50 文字まで</td>
                            </tr>
                            <tr>
                            	<th>本文（必須）<br>使用可能タグ<br>＜％送信者名％＞<br>＜％会員氏名％＞</th>
                                <td style="border-right:none !important;">
                                	<textarea required title="本文が入力されていません" class="txtArrea" cols="2" rows="2" name="txtContent" ng-model="Message.content" >

                                    </textarea>

                                </td>
                                <td style="border-left:none !important;"></td>
                            </tr>
                        </table>
                        <div class="group-btn">

                            <button id="submitBtn" type="submit" class="hidden"></button>
                            <input type="hidden" id="status" name="status" >
                            <button class="btn btn-primary" ng-click="previewMessage()" type="button" data-toggle="modal" data-target="#prev">プレビュー</button>
                            <button class="btn btn-primary" type="button" ng-click="saveMessage(0)" ng-disabled = "checkSave">下書き</button>
                            <button class="btn btn-primary"  type="button"  ng-click="saveMessage(1)" ng-disabled = "checkSave">適用</button>
                            <div class="txt-right">※「適用」を押すとテンプレートとして利用可能となります。「適用」できるテンプレートは、３件までです。</div>
                        </div>
                    </div>

                    <div class="modal fade popup-cmn" id="prev" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                        <div class="modal-dialog" role="document" style="width: 840px;">
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
@endsection