@extends('layouts.company_login')
@section('title', 'パスワードを変更してください')
@section('content')

    <div class="row" ng-controller="AuthUpdateProfileController">


        <div class="col-lg-12">
            <p class="txtNote clearfix">■スタッフ情報を登録し、「確認する」を押してください</p>

            <form id="profileForm" class="form-inline" ng-submit="confirm($event)" method="POST"
                  action="{{action('Company\AuthController@updateProfile')}}">
                {{csrf_field()}}
                <table class="table table-striped table-striped-show">
                    <colgroup>
                        <col width="30%">
                        <col width="70%">


                    </colgroup>

                    <tbody>
                    <tr>
                        <th class="txt-center">アカウントID（ログインID）</th>
                        <td class="txt-center"><p ng-bind="Staff.username"></p></td>

                    </tr>
                    <tr>
                        <th class="txt-center">氏名（漢字）</th>
                        <td>
                            <div ng-show="!isConfirmMode" class="edit-area">
                                <div class="form-group">
                                    <label for="">姓&nbsp;&nbsp;&nbsp;</label>
                                    <input ng-model="Staff.first_name" required name="first_name" type="text"
                                           class="form-control w150" id="" placeholder="姓">
                                </div>
                                <div class="form-group">
                                    <label for="">名&nbsp;&nbsp;&nbsp;</label>
                                    <input ng-model="Staff.last_name" required name="last_name" type="text"
                                           class="form-control" id="" placeholder="名">
                                </div>
                            </div>
                            <div class="txt-center" ng-show="isConfirmMode"
                                 ng-bind="Staff.first_name + ' ' + Staff.last_name"></div>

                        </td>

                    </tr>
                    <tr>
                        <th class="txt-center">氏名（カナ）</th>
                        <td>
                            <div ng-show="!isConfirmMode" class="edit-area">
                                <div class="form-group">
                                    <label for="">セイ</label>
                                    <input ng-model="Staff.first_name_kana" maxlength="15" required
                                           name="first_name_kana" type="text" class="form-control katakana" id=""
                                           placeholder="姓">
                                </div>
                                <div class="form-group">
                                    <label for="">メイ</label>
                                    <input ng-model="Staff.last_name_kana" maxlength="5" required name="last_name_kana"
                                           type="text" class="form-control katakana" id="" placeholder="名">
                                </div>
                            </div>

                            <div class="txt-center" ng-show="isConfirmMode"
                                 ng-bind="Staff.first_name_kana + ' ' + Staff.last_name_kana"></div>
                        </td>
                    </tr>
                    <tr>
                        <th class="txt-center">部署</th>
                        <td>
                            {!! Form::select('job_category', $jsonData['jobCategory'],  $jsonData['staff']['job_category'], ['class' => 'form-control w350 job-Category', 'ng-show'=>'!isConfirmMode']) !!}
                            <input name="department" ng-model="Staff.department" class="form-control w350" ng-show="!isConfirmMode" />
                            <div class="txt-center text-job-Category" ng-show="isConfirmMode" ng-bind="Staff.department"></div>
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

                @if ($cancelable)
                    <div class="btn-Registration center-block txt-center">

                        <a href="{{action('Company\AuthController@profile')}}" class="btn btn-primary w150">キャンセル</a>
                        <button type="submit" class="btn btn-primary w150">保存</button>

                    </div>

                @else
                    <div class="btn-Registration center-block txt-center" ng-show="!isConfirmMode">


                        <button type="submit" class="btn btn-primary w150">確認</button>

                    </div>
                    <div class="btn-Registration center-block ng-cloak txt-center" ng-show="isConfirmMode">
                        <button ng-click="isConfirmMode=false" type="button" class="btn btn-primary w150">キャンセル</button>
                        <button ng-click="save()" type="button" class="btn btn-primary w150">登録</button>
                    </div>
                @endif


            </form>
        </div>
    </div>
    {{--<script>--}}
        {{--$('.job-Category').change(function () {--}}
            {{--var val = $('.job-Category option:selected').html();--}}
            {{--console.log(val);--}}
            {{--$('.text-job-Category').html('').html(val);--}}
        {{--});--}}
    {{--</script>--}}

@endsection
