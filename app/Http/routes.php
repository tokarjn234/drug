<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('api/term', 'Api\OAuthTokensController@getTerm');
Route::get('api/help', 'Api\OAuthTokensController@getHelp');

Route::match(['get', 'post'], 'login', 'Home\AuthController@login');

Route::match(['get', 'post'], 'auth/change-password', 'Home\AuthController@changePassword');
Route::match(['get', 'post'], 'auth/update-profile', 'Home\AuthController@updateProfile');
Route::match(['get', 'post'], 'auth/profile', 'Home\AuthController@profile');
Route::match(['get', 'post'], 'auth/certificates', 'Home\AuthController@certificates');
Route::match(['get', 'post'], 'auth/secured-login', 'Home\AuthController@securedLogin');

Route::get('logout', 'Home\AuthController@logout');
Route::get('mails/confirm-email', 'Home\MailsController@putConfirmEmail');
Route::get('mails/confirm-email-change-profile', 'Home\MailsController@putConfirmEmailChangeProfile');
Route::get('mails/reset-password', 'Home\MailsController@putResetPassword');
Route::post('mails/update-password', 'Home\MailsController@postUpdatePassword');
Route::get('mails/open-app', 'Home\MailsController@getOpenApp');
Route::get('photos/view/{alias}', 'Home\PhotosController@getView');

Route::group(['namespace' => 'Home', 'middleware' => ['StoreAuth']], function () {
    Route::get('/', 'OrdersController@getIndex');

    Route::get('orders/photos/{alias}', 'OrdersController@getPhotos');
    Route::controller('orders', 'OrdersController');


    Route::controller('messages', 'MessagesController');
    Route::controller('stores', 'StoresController');
    Route::controller('settings', 'SettingsController');
    Route::controller('staffs', 'StaffsController');
    Route::controller('users', 'UsersController');
});


Route::match(['get', 'post'], 'company/auth/certificates', 'Company\AuthController@certificates');
Route::match(['get', 'post'], 'company/auth/login', 'Company\AuthController@login');
Route::match(['get', 'post'], 'company/auth/logout', 'Company\AuthController@logout');
Route::match(['get', 'post'], 'company/auth/secured-login', 'Company\AuthController@securedLogin');
Route::match(['get', 'post'], 'company/auth/change-password', 'Company\AuthController@changePassword');
Route::match(['get', 'post'], 'company/auth/profile', 'Company\AuthController@profile');
Route::match(['get', 'post'], 'company/auth/update-profile', 'Company\AuthController@updateProfile');

Route::group(['namespace' => 'Company', 'prefix' => 'company', 'middleware' => ['CompanyAuth']], function () {
    Route::controller('users', 'UsersController');
    Route::controller('companies', 'CompaniesController');
    Route::controller('certificates', 'CertificatesController');
    Route::controller('staffs', 'StaffsController');
    Route::controller('orders', 'OrdersController');

    Route::controller('setting-users', 'SettingUsersController');
    Route::controller('setting-stores', 'SettingStoresController');
    Route::controller('company-staffs', 'CompanyStaffsController');
    Route::controller('stores', 'StoresController');
    Route::post('company/staffs/create-id-staff', 'StaffsController@postCreateIdStaff');

});

Route::match(['get', 'post'], 'mediaid/auth/login', 'Mediaid\AuthController@login');
Route::match(['get', 'post'], 'mediaid/auth/logout', 'Mediaid\AuthController@logout');
Route::match(['get', 'post'], 'mediaid/auth/profile', 'Mediaid\AuthController@profile');
Route::match(['get', 'post'], 'mediaid/auth/update-profile', 'Mediaid\AuthController@updateProfile');
Route::match(['get', 'post'], 'mediaid/auth/change-password', 'Mediaid\AuthController@changePassword');
Route::match(['get', 'post'], 'mediaid/auth/certificates', 'Mediaid\AuthController@certificates');
Route::match(['get', 'post'], 'mediaid/auth/secured-login', 'Mediaid\AuthController@securedLogin');

Route::group(['namespace' => 'Mediaid', 'prefix' => 'mediaid', 'middleware' => ['MediaidAuth']], function () {
    Route::controller('orders', 'OrdersController');
    Route::controller('messageTemplates', 'MessageTemplatesController');
    Route::controller('certificates', 'CertificatesController');
    Route::controller('certificate-mediaid', 'CertificatesMediaidController');
    Route::controller('mediaids', 'MediaidsController');
    Route::controller('companies', 'CompaniesController');
    Route::controller('mediaidAccount', 'MediaidAccountsController');

});

Route::group(['prefix' => 'api', 'middleware' => ['ApiMiddleware'], 'namespace' => 'Api'], function () {
    Route::controller('users', 'UsersController');
    Route::controller('orders', 'OrdersController');
    Route::controller('stores', 'StoresController');
    Route::controller('messages', 'MessagesController');
    Route::controller('system', 'SystemControllers');


    Route::get('stores/search-store', 'StoresController@getSearchStore');
    Route::get('stores/list-address', 'StoresController@getListAddress');

    Route::post('oauth/getAccessToken', [
        'as' => 'OAuthAccessTokenUri',
        'uses' => 'OAuthTokensController@getAccessToken'
    ]);
    Route::post('oauth/logout', 'OAuthTokensController@postLogout');

});

Route::match(['get', 'post'], 'win-api/users/login', 'WinApi\UsersController@postLogin');
Route::match(['get', 'post'], 'win-app/login-with-win-app', 'Home\AuthController@getLoginWithWinApp');
Route::group(['prefix' => 'win-api', 'namespace' => 'WinApi', 'middleware' => 'WinApi'], function () {
    Route::controller('users', 'UsersController');
});


