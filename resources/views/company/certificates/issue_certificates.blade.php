@extends('layouts.company')

@section('title', __('Summary'))

@section('content')

    <div id="page-wrapper" ng-controller="IssueCertificatesController" >
        <!-- /.row -->
        <div class="row">
            <div class="col-lg-12">
                <form id="issueForm" method="POST" action="{{ action('Company\CertificatesController@postIssueCertificates') }}">
                    {{ csrf_field() }}
                    <input type="hidden" name="action" value="issue">
                    @foreach ($certificates as $cer)
                        <input name="item[]" value="{{$cer->alias}}" type="hidden">
                        @endforeach
                <div class="dataTable_wrapper" style="width: 70%;margin:0 auto;">
                    <p>
                        配布先店舗を選択し、「割り当てる」を押してください。
                    </p>
                    <table class="table table-striped table-bordered table-hover">
                        <thead>
                        <tr>
                            <th class="txt-center">#</th>
                            <th class="txt-center">端末証明書番号</th>


                        </tr>

                        </thead>
                        <tbody>
                        @foreach( $certificates as $index => $cer)
                            <tr align="center">
                                <td>
                                    {{$index +1}}
                                </td>
                                <td> {{$cer->ssl_client_s_dn_cn}}</td>
                            </tr>
                            @endforeach



                        </tbody>
                    </table>
                    <table class="table table-striped table-bordered table-hover" >
                        <thead>
                        <tr>

                            <th class="txt-center">端末証明書番号</th>


                        </tr>

                        </thead>
                        <tbody>

                        <tr>
                            <td>
                                {!! Form::select('store_alias', $stores, $firstStoreAlias , ['class'=> 'form-control  ', 'id' => 'stores', 'ng-model' => 'currentStore']) !!}
                            </td>
                        </tr>


                        </tbody>
                    </table>
                    <div class="center-block text-center">

                        <a href="{{action('Company\CertificatesController@getIndex')}}" class="btn btn-primary btn-lg" type="submit">戻る</a>
                        <button ng-click="save()" class="btn btn-primary btn-lg" type="button">割り当てる</button>


                    </div>

                </div>
                </form>
                <!-- /.panel -->
            </div>
            <!-- /.col-lg-12 -->
        </div>




    </div>
    <!-- /#page-wrapper -->

@endsection