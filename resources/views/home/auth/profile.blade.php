@extends('layouts.login')
@section('title', 'パスワードを変更してください')
@section('content')

    <div class="row" ng-controller="AuthUpdateProfileController">


        <div class="col-lg-12">
            <p class="txtNote clearfix"></p>

            <table class="table table-striped table-striped-show">
                <colgroup>
                    <col width="30%">
                    <col width="70%">


                </colgroup>

                <tbody>
                <tr>
                    <th class="txt-center">アカウントID</th>
                    <td class="txt-center">{{$staff->username}}</td>

                </tr>
                <tr>
                    <th class="txt-center">氏名（漢字)</th>
                    <td>

                        <div class="txt-center">{{$staff->first_name . ' ' . $staff->last_name}}</div>

                    </td>

                </tr>
                <tr>
                    <th class="txt-center">氏名（カナ）</th>
                    <td>


                        <div class="txt-center">
                            <div class="txt-center">{{$staff->first_name_kana . ' ' . $staff->last_name_kana}}</div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="txt-center">職種</th>
                    <td>

                        <div class="txt-center">
                            {!! @\App\Models\Staff::$jobCategory[$staff['job_category']] !!}
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="txt-center">役職</th>
                    <td>
                        <div class="txt-center">
                            {{$staff->position}}
                        </div>
                    </td>
                </tr>


                </tbody>

            </table>

            <div class="btn-Registration" ng-show="!isConfirmMode">
                <a href="{{action('Home\AuthController@updateProfile', ['prev' => action('Home\AuthController@profile')])}}"
                   class="btn btn-primary center-block w150">編集</a>
            </div>

        </div>
    </div>


@endsection