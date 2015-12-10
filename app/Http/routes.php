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
Route::get('dashboard', ['as' => 'home.dashboard', 'uses' => 'HomeController@dashboard'] );

// login routes
$router->get('user/login', ['as' => 'user.login', 'uses' => 'UserController@getLogin']);
$router->post('user/login', ['as' => 'user.login.post', 'uses' => 'UserController@postLogin']);
$router->get('user/logout', ['as' => 'user.logout', 'uses' => 'UserController@getLogout']);

$router->get('user/profile', ['as' => 'user.profile', 'uses' => 'UserController@getProfile']);
$router->post('user/profile', ['as' => 'user.profile.post', 'uses' => 'UserController@postProfile']);

// activation routes
$router->get('user/activate/{id}/{code}', ['as' => 'user.activate', 'uses' => 'UserController@getActivate'])->where('id', '\d+');
$router->get('user/resend', ['as' => 'user.resend', 'uses' => 'UserController@getResend']);
$router->post('user/resend', ['as' => 'user.resend.post', 'uses' => 'UserController@postResend']);

// forgot routes
$router->get('user/forgot', ['as' => 'user.forgot', 'uses' => 'UserController@getForgot']);
$router->post('user/forgot', ['as' => 'user.forgot.post', 'uses' => 'UserController@postForgot']);
$router->get('user/password/{id}/{code}', ['as' => 'user.password', 'uses' => 'UserController@getPassword']);

// product routes
$router->get('product', ['as' => 'product.index', 'uses' => 'ProductController@index']);
$router->get('product/{id}', ['as' => 'product.show', 'uses' => 'ProductController@show']);
$router->get('product/create', ['as' => 'product.create', 'uses' => 'ProductController@create']);
$router->post('product', ['as' => 'product.store', 'uses' => 'ProductController@store']);
$router->get('product/{id}/edit', ['as' => 'product.edit', 'uses' => 'ProductController@edit']);
$router->post('product/{id}', ['as' => 'product.update', 'uses' => 'ProductController@update']);

// payment routes
$router->get('payment', ['as' => 'payment.index', 'uses' => 'PaymentController@index']);
$router->get('payment/{id}', ['as' => 'payment.show', 'uses' => 'PaymentController@show']);
$router->get('refund/{id}', ['as' => 'refund.index', 'uses' => 'RefundController@index']);
$router->post('refund', ['as' => 'refund.store', 'uses' => 'RefundController@store']);


// ledger routes
$router->get('ledger', ['as' => 'ledger.index', 'uses' => 'LedgerController@index']);


// checkout routes
$router->get('checkout', ['as' => 'checkout.index', 'uses' => 'CheckoutController@index']);


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

