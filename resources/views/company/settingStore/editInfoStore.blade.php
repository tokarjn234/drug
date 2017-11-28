@extends('layouts.company')

@section('title', '設定')

@section('content')
    <script>
        $(function() {

           $('#credit_card_type')[0].disabled = ($('#accept_credit_card').val() == 0);

           $('#accept_credit_card').on('change', function() {
               $('#credit_card_type')[0].disabled = (this.value == 0)
           });
        });
    </script>
	<div id="page-wrapper" class="settings_page">
        <div class="row">
            <div class="col-lg-12">
            	<div class="mtit"><i class="fa fa-stop"></i> 店舗検索機能設定</div>
            <form action="{!! action('Company\SettingStoresController@postInfoStore') !!}" method="POST">
            {{ csrf_field() }}
            	<table class="table table-striped">
                	<colgroup>
                    	<col width="30%">
                        <col width="60%">
                        
                    </colgroup>
                    
                	<tr>
                    	<th >クレジットカード利用</th>
                        <td>

                            {!! Form::select('accept_credit_card', [1 => '可能', 0 => '不可'], $setting->accept_credit_card['data']['accept'] ? 1 : 0, ['id' => 'accept_credit_card','style' => 'width: 120px; float: left; margin-right: 50px;', 'class' => 'form-control']) !!}

                            <p style="float:left; margin-top: 5px;">カードの種類  </p>
                            <input id="credit_card_type" type="text" style="width: 400px;" class="form-control"  placeholder="VISA/Master/American Express" name="credit_card_type" value="{{$setting->accept_credit_card['data']['card_type']}}" />
                        </td>                       
                    </tr>
                    <tr>
                    	<th>駐車場</th>
                        <td>
                        	<input  pattern=".{2,40}" title="2文字/40文字"  maxlength="40" type="text" name="park_info" class="form-control" value="{{$setting->park_info['data']}}" />
                        	<p style="float:right;">2文字/40文字</p>
                        </td> 
                                               
                    </tr>
                    <tr>
                    	<th>店舗からのお知らせ</th>
                        <td>
                        	<input  pattern=".{2,40}" title="2文字/40文字" type="text" maxlength="40" name="description" class="form-control" value="{{$setting->description['data']}}" />
                        	<p style="float:right;">2文字/40文字</p>
                        </td>
                        
                    </tr>
                    <tr>
                    	<th>営業時間についてのコメント</th>
                    	<td>
                    		<input  pattern=".{20,70}" title="20文字/70文字" type="text" maxlength="70" name="note_working_time" class="form-control" value="{{$setting->note_working_time['data']}}"/>
                    		<p style="float: right;">20文字/70文字</p>
                    	</td>
                    	
                    </tr>
                    
                </table>
               @include('shared.errors')
                <div style="text-align: center;">
                	<button class="btn btn-primary new-regulations" type="button" onClick="self.location='{!! action('Company\SettingStoresController@getIndex') !!}'">キャンセル</button>
                	<button class="btn btn-primary new-regulations" type="submit" >登録/更新</button>
                </div>
            </form>
            </div>
        </div>
    </div>

@endsection
