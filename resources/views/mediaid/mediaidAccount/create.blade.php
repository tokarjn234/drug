@extends('layouts.mediaid_login')
@section('title', 'メディエイドアカウント管理')
@section('content')

    <div class="row">


        <div class="col-lg-12">
            <p class="txtNote clearfix">■アカウント情報を登録し、「確認」を押してください。</p>
            <form  class="form-inline"  method="POST" action="{{action('Mediaid\MediaidAccountsController@postUpdate')}}">
                {{csrf_field()}}
                <table class="table table-striped table-striped-show">
                    <colgroup>
                        <col width="30%">
                        <col width="70%">


                    </colgroup>

                    <tbody>
                    <tr>
                        <th class="txt-center">アカウントID<br>（ログインID）</th>
                        <td>
                            <div  class="edit-area">
                                <input pattern="^[a-zA-Z0-9]+$" minlength="5" maxlength="10" style="width: 50%" required value="{{ Input::old('username') }}" name="username" type="text" class="form-control alpha-numeric" placeholder="アカウントID">
                            </div>
                            <div class="txt-center" ></div>
                        </td>

                    </tr>
                    <tr>
                        <th class="txt-center">氏名（漢字）</th>
                        <td>
                            <div  class="edit-area">
                                <div  class="form-group">
                                    <label for="">姓&nbsp;&nbsp;&nbsp;</label>
                                    <input maxlength="15" required name="first_name" value="{{ Input::old('first_name') }}" type="text" class="form-control w150" id="" placeholder="姓">
                                </div>
                                <div  class="form-group">
                                    <label for="">名&nbsp;&nbsp;&nbsp;</label>
                                    <input maxlength="15" required name="last_name" value="{{ Input::old('last_name') }}" type="text" class="form-control" id="" placeholder="名">
                                </div>
                            </div>
                            <div class="txt-center" ></div>

                        </td>

                    </tr>
                    <tr>
                        <th class="txt-center">氏名（カナ）</th>
                        <td>
                            <div  class="edit-area">
                                <div  class="form-group">
                                    <label for="">セイ</label>
                                    <input  maxlength="15" required value="{{ Input::old('first_name_kana') }}" name="first_name_kana" type="text" class="form-control katakana" id="" placeholder="姓">
                                </div>
                                <div class="form-group">
                                    <label for="">メイ</label>
                                    <input maxlength="15" required value="{{ Input::old('last_name_kana') }}" name="last_name_kana" type="text" class="form-control katakana" id="" placeholder="名">
                                </div>
                            </div>

                            <div class="txt-center" ></div>
                        </td>
                    </tr>
                    <tr>
                        <th class="txt-center" >権限</th>
                        <td>
                            <label style="font-weight: normal;">
                                <?php echo Form::radio('Authority', 1) ?>
                                管理者</label>
                            <label class="pl10" style="font-weight: normal;">
                                <?php echo Form::radio('Authority', 0) ?>
                                スタッフ</label>
                            <div class="txt-center" ></div>
                        </td>
                    </tr>
                    </tbody>
                </table>
                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="btn-Registration center-block txt-center" >

                    <button type="submit" class="btn btn-primary w150" >確認</button>

                </div>
            </form>
        </div>
    </div>

    <script type="text/javascript">
        $.fn.onlyKana = function(config) {
            var defaults = {
            };
            var options = $.extend(defaults, config);
            return this.each(function(){
                $(this).bind('blur', function(){
                    $(this).val($(this).val().replace(/[^ア-ン゛゜ァ-ォャ-ョーｱ-ﾝﾞﾟｦｧ-ｫｬ-ｮｯｰ]/g, ''));
                });
            });
        };

        $('.katakana').onlyKana();

        $(".alpha-numeric").keydown(function (e) {
                if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110]) !== -1 ||
                        (e.keyCode == 65 && ( e.ctrlKey === true || e.metaKey === true ) ) ||
                        (e.keyCode >= 35 && e.keyCode <= 40)) {
                    return;
                }
                if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.ctrlKey || e.keyCode < 65 || e.keyCode > 90) && (e.keyCode < 96 || e.keyCode > 105)) {
                    e.preventDefault();
                }
        });
        
    </script>

@endsection
