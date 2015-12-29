<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cartalyst\Sentry\Sentry;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\UserKey;
use App\UserProfile;
use App\Invoice;
use App\Oaccount;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Redis;
use Validator;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;

class InvoiceController extends Controller
{

    protected $sentry;
    
    /**
     * Create a new checkout controller instance.
     *
     * @return void
     */
    public function __construct( Sentry $sentry)
    {

        $this->sentry = $sentry;
        $this->middleware( 'guest'  );

    }

    /**
     * Display a index of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request  , $token )
    {
        $invoice = Invoice::where( 'token' , $token )->first();

        if( $invoice ) {
            return view('invoice.index' , compact ( 'invoice' , 'token') );
        } else {
            return "server error!!!";
        }
    }

    public function getAccessToken( ) {

        $pay_user = Config::get('common.pay_user');   
        $api_token = Redis::get('pay_api_token');

        if( !empty( $api_token ) ) return  json_decode( $api_token ) ;

        // 조건이 맞으면 거래소 출금 API 호출
        try {
            $client = new \GuzzleHttp\Client( [ 'base_uri' =>  $pay_user['base_uri']  ] );

            $res = $client->request( 'POST' , '/oauth/access_token' , [
                'form_params' => [
                    'grant_type' => 'password' , 
                    'client_id' => $pay_user['client_id'] , 
                    'client_secret' => $pay_user['client_secret'] ,                                 
                    'username' => $pay_user['email'] ,                 
                    'password' => $pay_user['password']                               
                ]
             ]);

         $api_token =  $res->getBody() ;

         Redis::set( 'pay_api_token', $api_token );
         Redis::expire( 'pay_api_token' , 7000 );

        } catch ( RequestException $e) {
            return Response::json (   [ 'status' => 'api_unauthorized' ]  , 501 );            
        } catch (ClientException $e) {
            return Response::json (   [ 'status' => 'http_client_error' ]  , 501 );            
        }

        return json_decode( $api_token ) ;
    }

    /**
     * [payment description]
     * @param  Request $request [description]
     * @param  [type]  $token   [description]
     * @return [type]           [description]
     */
    public function payment( Request $request , $token )
    {

        if( !Auth::check() ) {
            return response::json( [ 'status' => 'Unauthorized' ] , 401 );            
        }

        //  결제 필요 데이터 호출 
        $user = Auth::user();
        $account = Oaccount::whereUserId( $user->id )->first();
        $invoice = Invoice::whereToken( $token )->first();

        // 결제 금액 및 현재 잔고 검증 
        if( $invoice->pi_amount > $account->balance  ) {
            return Response::json (   [ 'status' => 'payment_than_balance' ]  , 400 );
        }

        $api_token = $this->getAccessToken();

        // 호출 성공후 인보이스 테이블 업데이트 , payment 테이블에 데이터 추가 , trasnaction , account 테이블에 데이터 추가
        // 결제 처리 완료 후 값 리턴 
        // 실패시 에러 처리 


        return [ 'token' => $token , 'api_token' => $api_token ];            

    }
    
}
