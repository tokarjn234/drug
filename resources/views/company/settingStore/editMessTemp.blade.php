@extends('layouts.company')

@section('title', '設定')

@section('content')

            <div id="page-wrapper" class="edit-template" >
                <!-- /.row -->
                <div class="row">
                
                <form action="{{action('Company\SettingStoresController@postEdit')}}" method="POST">
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
                                            $formOptions = ['class' => 'form-control','id' => 'newType'];
                                            if ( ($editMessage['status'] == 1) || ($editMessage['type'] == 2) ) {
                                                $formOptions['disabled'] = true;
                                            }
                                        ?>
                                        {!! Form::select('txtSelect', \App\Models\MessageTemplate::$messageTypes,$editMessage['message_type'], $formOptions) !!}
                                    </div>
                                </td>
                            </tr>
                            <tr>
                            	<th>テンプレート名（必須）</th>
                                <td colspan="2">                          
                                <input title="テンプレート名が入力されていません" required @if(isset($editMessage) && (($editMessage['status'] == 1) || ($editMessage['type'] == 2)) ) readonly="true" @endif class="form-control" type="text"  placeholder="" name="txtName" value="{!! old('txtName',isset($editMessage) ? $editMessage['name'] : "") !!}">             
                                </td>
                               
                            </tr>
                            <tr>
                            	<th colspan="3">テンプレート内容　※患者さまアプリに表示される内容です</th>
                            </tr>
                            <tr>
                            	<th>タイトル（必須）<input type="hidden" name="id" value="{{ $editMessage['id'] }}"></th>
                                <td style="border-right:none !important;">

                                    <input   title="本文が入力されていません" class="form-control" required
                                                         maxlength="50"
                                                         type="text" placeholder="" id="newTitle" 
                                                         name="txtTitle"
                                                         @if(isset($editMessage) && $editMessage['status'] == 1) readonly="true" @endif
                                                         value="{!! old('txtTitle',isset($editMessage) ? $editMessage['title'] : "") !!}">
                                </td>
                                
                                <td style="border-left:none !important;">※50 文字まで</td>
                            </tr>
                            <tr>
                            	<th>本文（必須）<br>使用可能タグ<br>＜％送信者名％＞<br>＜％会員氏名％＞</th>
                                <td style="border-right:none !important;">
                                	<textarea id="newContent" @if(isset($editMessage) && $editMessage['status'] == 1) readonly="true" @endif title="本文が入力されていません" required class="txtArrea" cols="2" rows="2" name="txtContent">{!! old('txtContent',isset($editMessage) ? $editMessage['content'] : "") !!}</textarea>                                    
                                    <td style="border-left:none !important;"></td>
                                </td>
                            </tr>
                        </table>
                        <div class="group-btn">
                            <button id="submitBtn" type="submit" class="hidden"></button>
                            <input type="hidden" id="status" name="status" >

                            @if(isset($editMessage) && $editMessage['type'] == 1)
                        	<button class="btn btn-primary" type="button" data-toggle="modal" data-target="#myModal">削除</button>
                            @endif

                            <button id="showItem" class="btn btn-primary" type="button"  data-toggle="modal" data-target="#prev">プレビュー</button>

                            @if(isset($editMessage) && $editMessage['status'] == 0)
                                <button id="doNotActiveStatus" class="btn btn-primary" type="submit">下書き</button>
                            @endif

                            @if(isset($editMessage) && $editMessage['status'] == 0 && (isset($countEditTemp) && $countEditTemp < 5))

                                <button class="btn btn-primary" type="button" data-toggle="modal" data-target="#errorModal">適用</button>

                            @elseif( (isset($editMessage) && $editMessage['status'] == 0) && (isset($countEditTemp) && $countEditTemp >= 5) )

                                <button disabled class="btn btn-primary">適用</button>

                            @elseif(isset($editMessage) && $editMessage['status'] == 1 && (($editMessage['message_type'] == 0) || ($editMessage['message_type'] == 1)) && $editMessage['type'] == 2)

                                <button class="btn btn-primary" disabled type="submit">未適用に戻す</button>
                                <a style="width: 245px; margin-right: 75px;" id="activeStatus" class="btn btn-primary" href="{!! action('Company\SettingStoresController@getIndex') !!}">メッセージテンプレート一覧に戻る</a>

                            @elseif(isset($editMessage) && $editMessage['status'] == 1 && (($editMessage['message_type'] == 0) || ($editMessage['message_type'] == 1)) && $editMessage['type'] == 1 && (isset($countEditTemp) && $countEditTemp >= 5))

                                <button class="btn btn-primary" type="submit">未適用に戻す</button>
                                <button style="width: 245px; margin-right: 75px;" id="activeStatus" disabled class="btn btn-primary" type="submit">メッセージテンプレート一覧に戻る</button>

                            @elseif(isset($editMessage) && $editMessage['status'] == 1 && (($editMessage['message_type'] == 0) || ($editMessage['message_type'] == 1)) && $editMessage['type'] == 1 && (isset($countEditTemp) && $countEditTemp < 5))

                                <button class="btn btn-primary" type="submit">未適用に戻す</button>
                                <button style="width: 245px; margin-right: 75px;" id="activeStatus" class="btn btn-primary" type="submit">メッセージテンプレート一覧に戻る</button>

                            @else

                                <button id="StatusNotActive" class="btn btn-primary" type="submit">未適用に戻す</button>
                                <button style="width: 245px; margin-right: 75px;" id="activeStatus" class="btn btn-primary" type="submit">メッセージテンプレート一覧に戻る</button>

                            @endif

                            <div class="txt-right"></div>
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
                                        <a href="{!! action('Company\SettingStoresController@getDestroy',$editMessage['alias']) !!}" class="btn btn-info">OK</a>
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
                                            <p class="title-txt">{{\App\Models\Company::current('name')}}</p>
                                            <img src="/img/icon-store.jpg" style="width: 10%;">
                                            <div class="frame-txt " style="float: right; width: 90%;">
                                                
                                                <p class="printTitle"></p><br/>
                                                <p class="printContent"></p>
                                                
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
                                            <p>「適用」すると全店舗のメッセージ</br>テンプレートに反映されます。適用しますか？</p>
                                        </div>
                                        <!--/cont-temp--></div>
                                </div>
                                <div class="modal-footer">
                                    <div>
                                        <button type="button" class="btn btn-default" data-dismiss="modal">キャンセル</button>
                                        <button class="btn btn-info" type="submit" id="updateOtherStatus">OK</button>
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
            <script type="text/javascript" language="javascript">
                $(document).ready(function() {
                    $('#showItem').click(function() {
                        var title = $('#newTitle').val();
                        var content = $('#newContent').val();
                        $('.printTitle').html(title);
                        $('.printContent').html(content);
                    });

                    $('#notActiveStatus').click(function(){
                        $('#status').val(0);
                    });

                    $('#doNotActiveStatus').click(function(){
                        $('#status').val(0);
                    });

                    $('#StatusNotActive').click(function(){
                        $('#status').val(0);
                    });

                    $('#activeStatus').click(function(){
                        $('#status').val(1);
                    });

                    $('#updateOtherStatus').click(function(){
                        $('#status').val(1);
                    });


                    $('#newType').change(function() {    
                        if( ($(this).val() == 0) || ($(this).val() == 1 ) ){   
                            $('#notActiveStatus').attr('disabled',true);
                            $('#StatusNotActive').attr('disabled',true);
                            
                        }else if( ($(this).val() == 2) || ($(this).val() == 3) || ($(this).val() == 4) ){
                            $('#notActiveStatus').attr('disabled',false);
                            $('#StatusNotActive').attr('disabled',false);
                            
                        }
                    });
                });
            </script>

@endsection