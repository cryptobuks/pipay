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
Route::get('support', ['as' => 'home.support', 'uses' => 'HomeController@support'] );  // 약관 페이지 

// login routes
$router->get('user/login', ['as' => 'user.login', 'uses' => 'UserController@getLogin']);   // 로그인 뷰 
$router->post('user/login', ['as' => 'user.login.post', 'uses' => 'UserController@postLogin']);  // 로그인 액션
$router->get('user/logout', ['as' => 'user.logout', 'uses' => 'UserController@getLogout']);   // 로그아웃 
$router->get('user/agreement', ['as' => 'user.agreement', 'uses' => 'UserController@agreement']);   // 약관 페이지 
$router->post('user/agreement', ['as' => 'user.agreement.post', 'uses' => 'UserController@postAgreement']);   // 약관 페이지  처리 로직


$router->get('user/encrypt', ['as' => 'user.encrypt', 'uses' => 'UserController@getEncrypt']);   // 암호 테스트 뷰
$router->post('user/encrypt', ['as' => 'user.encrypt.post', 'uses' => 'UserController@encrypt']);   // 암호 인코딩 
$router->get('user/decrypt/{crypt}', ['as' => 'user.decrypt.post', 'uses' => 'UserController@decrypt']);  // 암호 디코딩 

$router->get('user/profile', ['as' => 'user.profile', 'uses' => 'UserController@getProfile']);   // 유저 프로파일
$router->post('user/profile/{id}', ['as' => 'user.profile.post', 'uses' => 'UserController@postProfile']);   // 유저 프로파일 처리
$router->post('user/upload/logo' , ['as' => 'user.upload.logo' , 'uses' => 'UserController@postLogo' ]);   // 유저 사진 업로드 


// tool routes
$router->get('tool', ['as' => 'tool.index', 'uses' => 'ToolController@index']);   // 결제도구 메인
$router->get('tool/generate/button', ['as' => 'tool.generate.button', 'uses' => 'ToolController@generateButton']);   // 결제 버튼 생성 폼 페이지 
$router->get('tool/generate/link', ['as' => 'tool.generate.link', 'uses' => 'ToolController@generateLink']);   // 결제 링크 생성 폼 페이지 
$router->post('tool/encrypt', ['as' => 'tool.encrypt', 'uses' => 'ToolController@encrypt']);   // 결제 값들 암호화 

// payment routes
$router->get('payment', ['as' => 'payment.index', 'uses' => 'PaymentController@index']);  // 거래 목록 
$router->get('payment/{id}', ['as' => 'payment.show', 'uses' => 'PaymentController@show']);  // 거래 보기 
$router->get('export/payment', ['as' => 'payment.excelexport', 'uses' => 'PaymentController@excelExport']);  // 엑셀 내보내기
$router->get('receipt/{id}', ['as' => 'receipt', 'uses' => 'PaymentController@receipt']);  // 거래 보기 
$router->get('refund/{id}', ['as' => 'refund.index', 'uses' => 'RefundController@index']);  // 환불하기 페이지 
$router->post('refund', ['as' => 'refund.store', 'uses' => 'RefundController@store']);   // 환불 저장 

// ledger routes
$router->get('ledger', ['as' => 'ledger.index', 'uses' => 'LedgerController@index']);   // 정산 목록 
$router->get('export/ledger', ['as' => 'ledger.excelexport', 'uses' => 'LedgerController@excelExport']);   // 정산 목록 

// 결제 페이지 요청 
$router->get('checkout/{token}', ['as' => 'checkout.index', 'uses' => 'CheckoutController@index']);

// 결제 페이지 실행 
$router->get('invoice/{token}', ['as' => 'invoice.index', 'uses' => 'InvoiceController@index']);
$router->get('invoice/test/{token}', ['as' => 'invoice.index', 'uses' => 'InvoiceController@test']);
$router->post('invoice/payment', ['as' => 'invoice.payment', 'uses' => 'InvoiceController@payment']);

// API
Route::group(array('prefix' => 'api/v1'), function()
{
    Route::post('invoice', 'API\V1\InvoiceController@index');  // 결제 요청 
});

// Access Token
Route::post('oauth/access_token', function() {
    return Response::json(Authorizer::issueAccessToken());
});

// 결제 간단 로그인  
$router->post('oauth/loginOnce', ['as' => 'oauth.loginOnce', 'uses' => 'OauthController@loginOnce']);

// 문자 인증 
$router->post('oauth/smsAuth', ['as' => 'oauth.smsAuth', 'uses' => 'OauthController@smsAuth']); 

// 결제 로그아웃
$router->get('oauth/logout', ['as' => 'oauth.logout', 'uses' => 'OauthController@logout']);

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

