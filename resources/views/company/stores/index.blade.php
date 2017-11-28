@extends('layouts.company')

@section('title', '店舗情報管理')

@section('content')
    <div id="page-wrapper" ng-controller="CompanyStoreIndexController">
        <div class="row">
            <div class="col-lg-12 margin_t20">
                <div class="margin_b10">
                    <strong>■ </strong>店舗情報一括登録
                    <p>CSVファイル取込み</p>
                    {!! Form::open(array('id' => 'form-store', 'name' => 'form', 'action' => (array('Company\StoresController@postImportCsv')), 'method' => 'post', 'enctype' => 'multipart/form-data')) !!}
                    <input data-ng-model="nameCsv" id="uploadFile" placeholder="" disabled="disabled"/>
                    {{--@if($enableCreate)--}}
                    <div class="fileUploadCsv btn btn-primary">

                        <span>参照</span>
                        <input ng-model-instant onchange="angular.element(this).scope().CsvSelect(event)" name="csv"
                               type="file" class="upload"/>
                    </div>

                    <input ng-disabled="!nameCsv" class="btn btn-danger w110" type="submit" name="Submit" value="取り込む"/>

                    {!! Form::close() !!}
                </div>
                <div class="margin_b10 fleft display-inline">
                    <strong>■ </strong>登録店舗一覧
                </div>

                <div class="fRight margin_b10">
                    <div class="center-block text-center">
                        <a href="{{action('Company\StoresController@getCreate')}}" id="staff-create"
                           name="btn_reset" class="btn btn-info">&nbsp;&nbsp;+新規追加&nbsp;&nbsp;</a>
                    </div>
                </div>
                @if (count($errors) > 0)
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <table class="table table-user table-company-stores">
                    <tr>
                        <th class="w110"> {{__('PublicStatus')}} </th>
                        <th> {{__('StoreCode')}} </th>
                        <th> {{__('StoreName')}} </th>
                        <th> {{__('Address')}} </th>
                        <th class="w140"> {{__('StoreImage')}} </th>
                        <th class="w110"> {{__('InformationEditing')}} </th>
                    </tr>

                    <tr ng-repeat="store in stores">
                        <td ng-class="{'is-deleted': store.is_deleted == 1}">
                            <a ng-if="store.is_published != 1 && store.is_deleted != 1" href=""
                               ng-click="changePublic(store, $event, 1)"
                               class="btn btn-info"> 公開する </a>
                            <a ng-if="store.is_published == 1 && store.is_deleted != 1" href=""
                               ng-click="changePublic(store, $event, 0)"
                               class="btn btn-warning"> 非公開に <br> する </a>
                            <span class="color-white" ng-if="store.is_deleted == 1">削除済</span>
                        </td>
                        <td ng-bind="store.is_deleted != 1 ? store.id : '-'"
                            ng-class="{'is-deleted': store.is_deleted == 1}"></td>
                        <td ng-class="{'is-deleted': store.is_deleted == 1}"><a ng-bind="store.name"
                                                                                href="{{action('Company\StoresController@getShow')}}/@{{ store.alias }}"></a>
                        </td>
                        <td ng-class="{'is-deleted': store.is_deleted == 1}"
                            ng-bind-html="(store.postal_code ? ('〒 ' + (store.postal_code)) + '</br>' : '') + (store.province) + (store.city1) + (store.address) | rawHtml"></td>
                        <td ng-class="{'is-deleted': store.is_deleted == 1}">
                            <a href="" ng-if="store.photo_url && store.is_deleted != 1"
                               ng-click="changePhoto(store, $event)"
                               class="photo-store"><img ng-src="../@{{ store.photo_url }}"/></a>
                            <a href="" ng-if="!store.photo_url && store.is_deleted != 1"
                               ng-click="changePhoto(store, $event)"
                               class="btn btn-info"> 画像登録 </a>
                        </td>
                        <td ng-class="{'is-deleted': store.is_deleted == 1}"><a ng-if="store.is_deleted != 1"
                                                                                class="btn btn-info"
                                                                                href="{{action('Company\StoresController@getEdit')}}/@{{ store.alias }}">
                                編集 </a></td>
                    </tr>
                </table>

                <nav id="pagination">
                    @include('shared.pagination', ['paginator' => $paginate])
                </nav>

                <div class="center-block text-center">
                    <a class="btn btn-info" href="{{action('Company\StoresController@getCsv')}}"><img
                                src="/images/csv_2.png" alt=""> 店舗一覧CSVダウンロード</a>
                </div>

                <!--Modal change store to published -->
                <div class="modal fade popup-cmn popview2" id="PublicConfirmDialog" tabindex="-1" role="dialog"
                     aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-body">
                                <div class="panel panel-info cont-temp">
                                    <div class="panel-body">
                                        <p ng-if="changeToPublished == 1">公開してよろしいですか？</p>

                                        <p ng-if="changeToPublished != 1">非公開にてよろしいですか？</p>
                                    </div>
                                    <!--/cont-temp--></div>
                            </div>
                            <div class="modal-footer">
                                <div>
                                    <button type="button" class="btn btn-default" data-dismiss="modal"
                                            ng-click="completedCancel()">キャンセル
                                    </button>
                                    <button type="button" class="btn btn-info"
                                            ng-click="changeIsPublished(currentStore, $event)">OK
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--End Modal-->

                <!--Modal store photo set-->
                <div class="modal fade popup-cmn popview2" id="PhotoConfirmDialog" tabindex="-1" role="dialog"
                     aria-labelledby="myModalLabel">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h4 class="modal-title" ng-bind="currentStore.name + '薬局'"></h4>
                            </div>

                            <div class="modal-body">
                                <div class="panel panel-info cont-temp">
                                    <div class="panel-body margin_b0">

                                        <div class="img-wrap">
                                            <span ng-if="selectImage || currentStore.photo_url" class="close"
                                                  ng-click="deletePhoto(currentStore, $event)">&times;</span>
                                            <img ng-if="!selectImage && currentStore.photo_url"
                                                 ng-src="../@{{currentStore.photo_url}}"/>
                                            <img ng-if="selectImage" ng-src="@{{selectImage}}"/>
                                        </div>

                                        <div class="fileUpload btn btn-default company-store-upload">
                                            <form method="post">
                                                <span>参照</span>
                                                <input class="upload" name="photo_url" type='file' ng-model-instant
                                                       onchange="angular.element(this).scope().imageChange(event)"/>
                                            </form>
                                        </div>

                                        <div ng-if="InvalidTypeImage" class="has-error margin_t10">
                                            インポート可能なファイルは、PNG、JPG、BMPのみとなります。
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <div>
                                    <button type="button" class="btn btn-default" data-dismiss="modal"
                                            ng-click="completedCancel()">キャンセル
                                    </button>
                                    <button ng-disabled="!AcceptSave" type="button" class="btn btn-info"
                                            ng-click="acceptChangePhoto(currentStore, $event)"> 登録
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--End Modal-->
            </div>
        </div>
    </div>
@endsection