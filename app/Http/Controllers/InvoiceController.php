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
use App\Ouser;
use App\Account;
use App\Payment;
use App\Transaction;
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
        $invoice = Invoice::where( 'token' , $token )->where( 'status' , 'new' )->first();

        if( $invoice ) {
            return view('invoice.index' , compact ( 'invoice' , 'token') );
        } else {
            return "Payment has already ended or Invalid token.";
        }
    }

    /**
     * [getAccessToken API 액세스 토큰을 받아온다.]
     * @return [type] [description]
     */
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
         Redis::expire( 'pay_api_token' , 10 );

        } catch ( RequestException $e) {
            return [ 'status' => 'api_unauthorized' ];            
        } catch (ClientException $e) {
            return  [ 'status' => 'http_client_error' ];
        }

        return json_decode( $api_token , true ) ;
    }

    private function transfer( $access_token , Invoice $invoice , Ouser $user ) {
        
        $pay_user = Config::get('common.pay_user');   

        // 조건이 맞으면 거래소 출금 API 호출
        try {

            $client = new \GuzzleHttp\Client( [ 'base_uri' =>  $pay_user['base_uri']  ] );

            $res = $client->request( 'POST' , '/api/v2/funds/coins/transfer' , [
                'headers' => [
                    'User-Agent' => 'PI-Payment/0.1' ,
                    'Authorization' => 'Bearer ' . $access_token , 
                ] ,
                'form_params' => [
                    'from_address' => $user->email , 
                    'to_address' => $pay_user['email'] , 
                    'amount' => $invoice->pi_amount ,                                 
                    'currency' => 'PI' ,                 
                ] ,
             ]);

            $result =  $res->getBody() ;

        } catch ( RequestException $e) {
            return [ 'status' => 'unauthorized' ]  ;            
        } catch (ClientException $e) {
            return  [ 'status' => 'http_client_error' ]  ;            
        }

        return json_decode( $result  , true ) ;

    }

    /**
     * [payment 결제 처리 ]
     * @param  Request $request [description]
     * @param  [type]  $token   [description]
     * @return [type]           [description]
     */
    public function payment( Request $request )
    {

        //$input = $request->all();
        $input = $request->only( 'token' );

        $validator = Validator::make( $input  , [
                'token' => 'required|alpha_dash|max:50' ,
        ]);

        if( $validator->fails() ) 
        {
            $messages = $validator->messages();
            if( $messages->first('token') ) {
                return Response::json ( api_error_handler(  'invalid_token' , 'The token is invalid.' ) , 400 );
            } else {
                return Response::json ( api_error_handler(  'invalid_request' , 'The Input format is invalid.' ) , 400 );
            }
        }

        // 인증 확인 
        if( !Auth::check() ) {
            return response::json( [ 'status' => 'unauthorized' ] , 401 );            
        }

        //  결제 필요 데이터 호출 
        $token = $input['token']; 
        $user = Auth::user();
        $buyer_account = Oaccount::whereUserId( $user->id )->whereCurrencyId( CURRENCY_COIN )->first();
        $invoice = Invoice::whereToken( $token )->where( 'status' , 'new' )->first();
        $seller_account = Account::whereUserId( $invoice->user_id )->whereCurrency( 'PI' )->first();


        // 결제 금액 및 현재 잔고 검증 
        if( $invoice->pi_amount > $buyer_account->balance  ) {
            return Response::json (   [ 'status' => 'payment_than_balance' ]  , 500 );
        }

        // API 접속 토큰 생성 
        $api_token = $this->getAccessToken();

        // 결제 송금  API 호출 
        $transfer = $this->transfer( $api_token['access_token'] , $invoice , $user );

        if( $transfer['status'] != 'success' ) {
            return Response::json (   [ 'status' => $transfer['status'] ]  , 500 );
        }

        // 호출 성공후 인보이스 테이블 업데이트 , payment 테이블에 데이터 추가 , trasnaction , 원장  테이블에 데이터 추가
        DB::beginTransaction();
        try {
            
            // invoice table 업데이트         
            $invoice->status = 'confirmed' ;
            $invoice->amount_received = $invoice->amount;
            $invoice->pi_amount_received = $invoice->pi_amount;      
            $invoice->completed_at = Carbon::now();  
            $invoice->save();

            $amount2 = $invoice->pi_amount ;
            $fee = NUMBER_ZERO;
            $net = $amount2 - $fee;
            $currency = 'PI' ; 

            // payment 테이블 데이터 추가 
            $payment_data = [
                'token' => $invoice->id . '_' .  mt_rand( ) ,
                'user_id' => $invoice->user_id , 
                'api_key' => $invoice->api_key , 
                'account_id' => $seller_account->id , 
                'buyer_id' => $user->id , 
                'invoice_id' => $invoice->id , 
                'amount' =>  $amount2 , 
                'amount_refunded' => NUMBER_ZERO , 
                'currency' => $currency , 
            ];

            $payment = Payment::create( $payment_data );

            $payment_token = generateToken( 'pay' , $payment->id  );
            $payment->token = $payment_token;
            $payment->save();

            // 원장  테이블에 추가 
            $seller_account->plus_funds( $amount2 , $fee ,  ACCOUNT_INVOICE_CONFIRMED ,   $payment );

            // Transaction 테이블 데이터 추가
            $transaction_data = [
                'user_id' => $invoice->user_id  , 
                'account_id' => $seller_account->id , 
                'amount' =>  $amount2 , 
                'currency' => $currency , 
                'fee' =>  $fee , 
                'fee_id' => NUMBER_ZERO ,                 
                'net' => $net  , 
                'source_id' => $payment->id , 
                'source_type' => get_class( $payment )  ,                                 
                'status' => 'available'  ,                                 
                'type' => 'payment'  , 
                'url' => url( "invoice/payment" ) ,
                'description' => NULL , 
            ];

            Transaction::create ( $transaction_data ) ;

            DB::commit();            
        }  catch (Exception $e) {
            DB::rollback();
            return Response::json ( [ 'status' => 'save_failure'  ]  , 500 );
        }

        // 결제 처리 완료 후 값 리턴 
        return Response::json ( [ 'status' => 'success' ,  'id' => $payment->id ,  'token' => $token  ] );                    

    }
    
}
