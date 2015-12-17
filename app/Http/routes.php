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

Route::get('/', ['as' => 'home.index', 'uses' => 'HomeController@index'] );
Route::get('dashboard', ['as' => 'home.dashboard', 'uses' => 'HomeController@dashboard'] );   // 대쉬보드 페이지
Route::get('agreement', ['as' => 'home.agreement', 'uses' => 'HomeController@agreement'] );  // 약관 페이지 

// login routes
$router->get('user/login', ['as' => 'user.login', 'uses' => 'UserController@getLogin']);   // 로그인 뷰 
$router->post('user/login', ['as' => 'user.login.post', 'uses' => 'UserController@postLogin']);  // 로그인 액션
$router->get('user/logout', ['as' => 'user.logout', 'uses' => 'UserController@getLogout']);   // 로그아웃 
$router->get('user/agreement', ['as' => 'user.agreement', 'uses' => 'UserController@agreement']);   // 약관 페이지 
$router->post('user/agreement', ['as' => 'user.agreement.post', 'uses' => 'UserController@postAgreement']);   // 약관 페이지  처리 로직

$router->get('user/profile', ['as' => 'user.profile', 'uses' => 'UserController@getProfile']);   // 유저 프로파일
$router->post('user/profile/{id}', ['as' => 'user.profile.post', 'uses' => 'UserController@postProfile']);   // 유저 프로파일 처리
$router->post('user/upload/logo' , ['as' => 'user.upload.logo' , 'uses' => 'UserController@postLogo' ]);   // 유저 사진 업로드 

Route::group(['middleware' => 'auth'], function () {
    Route::get('user/upload', function () {
       return view( 'users.upload' );
    });
});

// product routes
$router->get('product', ['as' => 'product.index', 'uses' => 'ProductController@index']);   // 목록
$router->get('product/create', ['as' => 'product.create', 'uses' => 'ProductController@create']);  // 추가 
$router->post('product', ['as' => 'product.store', 'uses' => 'ProductController@store']);  // 추가 저장 
$router->get('product/{id}', ['as' => 'product.edit', 'uses' => 'ProductController@edit']);  // 수정 
$router->post('product/{id}', ['as' => 'product.update', 'uses' => 'ProductController@update']);  // 수정 저장 

// payment routes
$router->get('payment', ['as' => 'payment.index', 'uses' => 'PaymentController@index']);  // 거래 목록 
$router->get('payment/{id}', ['as' => 'payment.show', 'uses' => 'PaymentController@show']);  // 거래 보기 
$router->get('refund/{id}', ['as' => 'refund.index', 'uses' => 'RefundController@index']);  // 환불하기 페이지 
$router->post('refund', ['as' => 'refund.store', 'uses' => 'RefundController@store']);   // 환불 저장 

// ledger routes
$router->get('ledger', ['as' => 'ledger.index', 'uses' => 'LedgerController@index']);   // 정산 목록 

// 결제 페이지 요청 
$router->get('checkout/{token}', ['as' => 'checkout.index', 'uses' => 'CheckoutController@index']);

// API
Route::group(array('prefix' => 'api/v1'), function()
{
    Route::post('invoice', 'API\V1\InvoiceController@index');  // 결제 요청 
});


// User login event
Event::listen('users.login', function($userId, $email)
{
    Session::put('userId', $userId);
    Session::put('email', $email);
});

// User logout event
Event::listen('users.logout', function()
{
    Session::flush();
});

// Subscribe to User Mailer events
Event::subscribe('App\Events\UserMailer');
Event::subscribe('App\Events\TransactionMailer');

Route::get('locale', [
    'as' => 'global.locale',
    function () {
        $cookie = cookie()->forever('l5locale', Request::input('l5locale'));

        Cookie::queue($cookie);

        return ($return = Request::input('return'))
            ? redirect(urldecode($return))->withCookie($cookie)
            : redirect()->route('home.index')->withCookie($cookie);
    } 
]);

