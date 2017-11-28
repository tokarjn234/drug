@extends('layouts.account')
@section('title', 'パスワードを変更してください')
@section('content')

    <div class="row">


        <div class="col-lg-12">
            <p class="txtNote clearfix"></p>

            <div class="mtit"><i class="fa fa-stop"></i> 内容を確認し、間違いがなければ「登録」を押してください。</div>
            <br/>

            <form method="POST" action="{{ action('Company\CompanyStaffsController@postUpdateData') }}">
                {{csrf_field()}}
                <table class="table table-striped table-striped-show">
                    <colgroup>
                        <col width="30%">
                        <col width="70%">
                    </colgroup>

                    <tbody>
                    <tr>
                        <th class="txt-center">アカウントID</th>
                        <td class="txt-center">{{ $input['username'] }}</td>
                        <input name="name" type="hidden" value="{{ $input['username'] }}"/>
                    </tr>
                    <tr>
                        <th class="txt-center">氏名（漢字)</th>
                        <td>
                            <div class="txt-center">{{ $input['first_name'] }} {{ $input['last_name'] }}</div>
                            <input name="firstName" type="hidden" value="{{ $input['first_name'] }}"/>
                            <input name="lastName" type="hidden" value="{{ $input['last_name'] }}"/>
                        </td>

                    </tr>
                    <tr>
                        <th class="txt-center">氏名（カナ）</th>
                        <td>
                            <div class="txt-center">
                                <div class="txt-center">{{ $input['first_name_kana'] }} {{ $input['last_name_kana'] }}</div>
                                <input type="hidden" name="first_name_kana" value="{{ $input['first_name_kana'] }}"/>
                                <input type="hidden" name="last_name_kana" value="{{ $input['last_name_kana'] }}"/>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th class="txt-center">部署</th>
                        <td>
                            <div class="txt-center">{{ @\App\Models\Staff::$jobCategory[$input['job_category']] }}</div>
                            <input type="hidden" name="job_category" value="{{ $input['job_category'] }}"/>
                        </td>
                    </tr>

                    </tbody>

                </table>

                <div style="text-align: center;" class="btn-Registration">
                    <a href="{{ action('Company\CompanyStaffsController@getCreate',['_'=>time()]) }}"
                       class="btn btn-primary w150">キャンセル</a>
                    <button type="submit" class="btn btn-primary w150">登録</button>
                </div>
            </form>
        </div>
    </div>


@endsection