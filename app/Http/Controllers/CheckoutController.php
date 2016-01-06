<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cartalyst\Sentry\Sentry;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Contracts\Encryption\DecryptException;
use App\UserKey;
use App\UserProfile;
use App\Invoice;
use Exception;
use Crypt;
use Vinkla\Hashids\Facades\Hashids;
use Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;


class CheckoutController extends Controller
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
        $input = $request->only( 'lang' , 'livemode' , 'reference' , 'order_id' , 'test' );

        $validator = Validator::make( $input  , [
                'lang' => 'alpha|max:2' , 
                'livemode' => 'boolean',
                'reference' => 'alpha_dash|max:255',                
                'order_id' => 'alpha_dash|max:255',                                
                'test' => 'boolean',                
        ]);

        if( $validator->fails() ) 
        {
            $messages = $validator->messages();
            if( $messages->first('lang') ) {
                return Response::json ( api_error_handler(  'invalid_lang' , 'The lang is invalid.' ) , 400 );
            } elseif ( $messages->first('livemode')  )  {
                return Response::json ( api_error_handler(  'invalid_livemode' , 'The livemode is invalid.' ) , 400 );            
            } elseif ( $messages->first('reference')  )  {
                return Response::json ( api_error_handler(  'invalid_reference' , 'The reference is invalid.' ) , 400 );            
            } elseif ( $messages->first('order_id')  )  {
                return Response::json ( api_error_handler(  'invalid_order_id' , 'The order_id is invalid.' ) , 400 );            

            } else {
                return Response::json ( api_error_handler(  'invalid_request' , 'The Input format is invalid.' ) , 400 );
            }
        }

        try {
            $decrypted = Crypt::decrypt( $token );
            $param = json_decode( $decrypted , true );
        } catch ( DecryptException $e) {
            return 'Server encryption error!';
        }

        $user_id = UserKey::getResourceOwnerId( $param['api_key'] );

        DB::beginTransaction();
        try {

                $currency = !empty( $param['currency'] ) ? strtoupper( $param['currency'] ) : 'KRW';
                $livemode = UserKey::getLiveMode( $param['api_key'] );            
                $inbound_address = Invoice::getNewAddress();
                $rate = Config::get( 'coin.pi.rate' );

                if( $currency == 'KRW') {
                    $pi_amount = ( $param['amount'] / $rate );
                } else {
                    $pi_amount = $param['amount'] ;                    
                    $amount = $param['amount'] * $rate ; 
                }

                $expiration_at = date( 'Y-m-d H:i:s' , time() + 86400 );
                $fee = NUMBER_ZERO;

                $in_data = [
                    'user_id' => $user_id ,
                    'api_key' => $param['api_key']  ,
                    'token' => $user_id . '_' . mt_rand( )  ,
                    'status' => 'new' ,
                    'exception_status' => NULL ,
                    'product_id' => NULL , 
                    'product_token' => NULL ,
                    'amount' => $param['amount'] , 
                    'amount_received' => NUMBER_ZERO , 
                    'pi_amount' => $pi_amount , 
                    'pi_amount_received' =>  NUMBER_ZERO, 
                    'rate' => $rate , 
                    'currency' => $currency ,
                    'inbound_address' => $inbound_address , 
                    'refund_address' => NULL , 
                    'livemode' => $request->has('livemode') ? $input['livemode'] : $livemode ,
                    'item_desc' => $param['item_desc'] ,
                    'order_id' => $request->has('order_id') ? $input['order_id'] : ''  ,
                    'reference' => $request->has('reference') ? $input['reference'] : ''  , 
                    'email' => !empty( $param['email'] ) ? $param['email'] : '' , 
                    'redirect' => !empty( $param['redirect'] ) ? $param['redirect'] : '' , 
                    'ipn' => !empty( $param['ipn'] ) ? $param['ipn'] : '' ,                                          
                    'expiration_at' => $expiration_at , 
                    'url' => '' , 
                    'fee' => $fee ,                    
                    'payment_url' => "pi:{$inbound_address}?amount={$pi_amount}" ,
                    'lang' => $request->has('lang') ? $input['lang']  : 'ko', 
                 ];

                $invoice = Invoice::create( $in_data );

                $invoice_token = generateToken( 'in' , $invoice->id  );

                $invoice->token = $invoice_token;
                $invoice->url = url( 'invoice/' . $invoice_token ) ;
                $invoice->save();

                DB::commit();

        } catch ( Exception $e) {
                DB::rollback();
                return Response::json ( api_error_handler(  'save_failure' , '' ) , 501 );
        }

        if( $input['test'] == '1') {
            return redirect( url( 'invoice/test/' . $invoice_token )  );                         
        }else{
            return redirect( $invoice->url  ); 
        }

    }

}
