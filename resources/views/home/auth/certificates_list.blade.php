@extends('layouts.certificate')
@section('title', 'パスワードを変更してください')
@section('content')

    <div class="row" >


        <div class="col-lg-12">
            <p class="">
                選択された店舗：{{$store->name}}
                ※選択された店舗が該当の店舗と異なる場合は、「戻る」を押して、店舗コードを入力しなおしてください。
                <span>使用状況が「未使用」となっている端末証明書を選択してください。</span>
            </p>
            <form role="form" method="POST" action="{{action('Home\AuthController@certificates')}}">
                {{csrf_field()}}
                <input type="hidden" name="next" value="certificates_issue">
                <input type="hidden" name="store_id" value="{{$storeId}}">
            <table class="table table-striped table-striped-show">


                <tbody>
                <tr>
                    <th class="txt-center">証明書番号</th>
                    <th class="txt-center">証明書導入端末</th>
                    <th class="txt-center">導入日時</th>
                    <th class="txt-center">使用状況</th>

                </tr>
                @foreach($certs as $cert)
                    <tr>
                        <td class="txt-center">{{$cert->ssl_client_s_dn_cn}}</td>
                        <td class="txt-center">{{$cert->status == \App\Models\Certificate::STATUS_DIVIDED_TO_DEVICE ? $cert->name : ''}}</td>
                        <td class="txt-center">{{$cert->status == \App\Models\Certificate::STATUS_DIVIDED_TO_DEVICE  ? date('Y/m/d', strtotime($cert->issued_to_device_at)) : ''}}</td>
                        <td class="txt-center">
                            @if ($cert->status == \App\Models\Certificate::STATUS_DIVIDED_TO_STORE)
                                <button name="cert_alias" type="submit" value="{{$cert->alias}}" class="btn btn-primary">未使用</button>
                            @else
                                <a class="btn btn-default disabled">使用済</a>
                            @endif
                        </td>

                    </tr>
                    @endforeach



                </tbody>

            </table>
                </form>
            <div class="txt-center">
                <a href="{{action('Home\AuthController@certificates', ['_'=>time()])}}" class="btn btn-primary">戻る</a>
            </div>
        </div>
    </div>


@endsection