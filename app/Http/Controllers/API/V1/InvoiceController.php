<?php

namespace App\Http\Controllers\API\V1;

use Illuminate\Http\Request;
use Cartalyst\Sentry\Sentry;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\UserKey;
use App\UserProfile;
use App\Invoice;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use Vinkla\Hashids\Facades\Hashids;
use Validator;
use Carbon\Carbon;

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

        $this->middleware( 'api.auth'  );

    }

    /**
     * Display a index of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {

        //$input = $request->all();
        $input = $request->only( 'api_key' , 'item_desc' , 'order_id' , 'currency' , 'amount'  , 'email' , 'redirect' , 'ipn' , 'reference' , 'lang' );

        $validator = Validator::make( $input  , [
                'api_key' => 'required|alpha_dash|max:40' ,
                'currency' => 'required|alpha|max:5',
                'amount' => 'required|numeric|min:0.01|max:10000000',
                'item_desc' => 'required|max:255',                
                'order_id' => 'alpha_num|max:255',                                
                'redirect' => 'url|max:255', 
                'ipn' => 'url|max:255', 
                'email' => 'email|max:255',       
                'reference' => 'max:255',      
                'lang' => 'alpha|max:2' , 
        ]);

        if( $validator->fails() ) 
        {
            $messages = $validator->messages();
            if( $messages->first('api_key') ) {
                return Response::json ( api_error_handler(  'invalid_api_key' , 'The api_key is invalid.' ) , 400 );
            } elseif ( $messages->first('currency')  )  {
                return Response::json ( api_error_handler(  'invalid_currency' , 'The currency is invalid.' ) , 400 );            
            } elseif ( $messages->first('amount')  )  {
                return Response::json ( api_error_handler(  'invalid_amount' , 'The amount is invalid.' ) , 400 );            
            } elseif ( $messages->first('item_desc')  )  {
                return Response::json ( api_error_handler(  'invalid_item_desc' , 'The item_desc is invalid.' ) , 400 );            
            } else {
                return Response::json ( api_error_handler(  'invalid_request' , 'The Input format is invalid.' ) , 400 );
            }
        }

        // api_key 체크 후 
        // user_id 추출 
        // invoice 테이블에 데이터 삽입.
        // invoice token 생성 
        // 값 리턴
        
        $api_key = e( $input['api_key'] );
        $currency = e( $input['currency'] );        
        $amount = e( $input['amount'] );        
        $item_desc = e( $input['item_desc'] );        
        $order_id = e( $input['order_id'] );        
        $redirect = e( $input['redirect'] );        
        $ipn = e( $input['ipn'] );                                                
        $email = e( $input['email'] ); 
        $reference = e( $input['reference'] ); 

        if ($request->has('lang')) $lang = e($input['lang']);
        else  $lang = 'ko' ;

        $user_id = UserKey::getResourceOwnerId( $api_key );

        DB::beginTransaction();
        try {

                $livemode = UserKey::getLiveMode( $api_key );            
                $inbound_address = Invoice::getNewAddress();
                $rate = Config::get( 'coin.pi.rate' );
                $pi_amount = ( $amount / $rate );
                $expiration_at = date( 'Y-m-d H:i:s' , time() + 86400 );

                $in_data = [
                    'user_id' => $user_id ,
                    'api_key' => $api_key ,
                    'token' => '' ,
                    'status' => 'new' ,
                    'exception_status' => NULL ,
                    'product_id' => NULL , 
                    'product_token' => NULL ,
                    'amount' => $amount , 
                    'amount_received' => NUMBER_ZERO , 
                    'pi_amount' => $pi_amount , 
                    'pi_amount_received' =>  NUMBER_ZERO, 
                    'rate' => $rate , 
                    'currency' => strtoupper($currency) ,
                    'inbound_address' => $inbound_address , 
                    'refund_address' => NULL , 
                    'livemode' => $livemode ,
                    'item_desc' => $item_desc ,
                    'order_id' => $order_id , 
                    'reference' => $reference , 
                    'email' => $email  , 
                    'redirect' => $redirect  , 
                    'ipn' => $ipn  ,                                         
                    'expiration_at' => $expiration_at , 
                    'url' => '' , 
                    'payment_url' => "pi:{$inbound_address}?amount={$pi_amount}" ,
                    'lang' => $lang , 
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

        $data = [ 'invoice_token' => $invoice_token ];

        return Response::json ($data , 200 ) ;
    }

}
