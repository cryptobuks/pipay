<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\UserProfile;
use App\Account;
use App\Transaction;
use App\Invoice;
use App\Payment;
use App\Refund;
use App\ExchangeAPI;
use App\Ouser;
use App\OuserAddress;
use Exception;
use Validator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;

class Refunds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:refunds';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'refund execute';

    protected $exchange_api ;

    /**
     * Create a refunds command instance.
     *
     * @return void
     */
    public function __construct(  ExchangeAPI $exchange_api )
    {
        parent::__construct();
        $this->exchange_api = $exchange_api;        
    }

    /**
     * 환불 신청이 되었을때 처리하는 커맨드 
     *
     * @return mixed
     */
    public function handle()
    {
        // 환불 신청이 들어왔는지 확인한다.
        $refunds = Refund::whereNull( 'refunded_at' )->orderBy('user_id' , 'asc')->get();

        // 환불 신청이 있으면 결제요구 테이블의 상태를 바꾸고 환불 처리를 한다.         
        foreach( $refunds as $refund ) {
            $invoice = Invoice::find( $refund->invoice_id );
            $user_profile = UserProfile::find( $invoice->user_id );
            $account = Account::whereUserId( $invoice->user_id )->whereCurrency( 'PI' )->first();  // 결제 계정 정보            
        
            $validator = Validator::make( ['address' => $refund->address ] , [  'address'  => 'required|email',  ]);
            if( $validator->fails() )   {
                $user_address = OuserAddress::where( 'address' , $refund->address )->first(); 
                $refund_user = Ouser::find( $user_address->user_id );
                $refund_address = $refund_user->email;
            } else {
                $refund_address = $refund->address;                
            }

            // 환불  시작 
            DB::beginTransaction();
            try {

                // 접속 토큰 생성
                $api_token =  $this->exchange_api->getAccessToken();


                if( $invoice->status == 'confirmed' ) {   //  결제가 정산전일때  처리 하는 로직

                    // 환불  API 호출 
                    $refund_row = $this->exchange_api->transfer( $api_token['access_token'] , 
                        [  'to_email' => $refund_address , 'amount' =>  $refund->pi_amount  , 'currency' => 'PI' ]  );

                    // 성공시 결제 계정에서 송금 금액을 빼줌.
                    if( $refund_row['status'] == 'success' ) {  
                       $account->sub_funds( $refund->pi_amount  , NUMBER_ZERO , ACCOUNT_REFUND ,   $refund );
                    } else {
                        $this->info( "[" . Carbon::now() . "] Error :  " . $refund_row['status']  . " "   );
                        throw new  Exception( 'refund failed' );
                    }

                } elseif( $invoice->status == 'settlement_complete') {  // 결제가 정산 후일때  처리 하는 로직 

                    // 환불   API 호출 
                    $refund_row = $this->exchange_api->move( $api_token['access_token'] , 
                        [  'from_address' => $user_profile->email  , 'to_address' => $refund_address  , 'amount' =>  $refund->pi_amount   , 'currency' => 'PI' ]  );

                    // 성공시 결제 계정에서 환불 금액을 빼줌.
                    if( $refund_row['status'] != 'success' ) {
                        $this->info( "[" . Carbon::now() . "] Error :  " . $refund_row['status']  . " "   );
                        throw new  Exception( 'refund failed' );
                    }

                }

                // 거래요구 데이터 환불로 상황 바꿈 

               $invoice->status = 'refunded';

                // 환불 금액 업데이트 
               $invoice->amount_refunded = $invoice->amount_refunded + $refund->amount;
               $invoice->pi_amount_refunded = $invoice->pi_amount_refunded + $refund->pi_amount;                
               $invoice->save();

               // 환불 처리 완료 
               $refund->refunded_at = Carbon::now();
               $refund->save();

               // 전송시   로그  기록 
               $transaction_data = [
                    'user_id' => $invoice->user_id  , 
                    'account_id' => $account->id , 
                    'amount' =>  $refund->pi_amount , 
                    'currency' => 'PI' , 
                    'fee' =>  NUMBER_ZERO , 
                    'fee_id' => NUMBER_ZERO ,                 
                    'net' => $refund->pi_amount  , 
                    'source_id' => $refund->id , 
                    'source_type' => get_class( $refund )  ,                                 
                    'status' => 'available'  ,                                 
                    'type' => 'refund'  , 
                    'url' => url( "refund" ) ,
                    'description' => NULL , 
                ];

               Transaction::create ( $transaction_data ) ;

               DB::commit();

            } catch ( Exception $e) {
                    DB::rollback();
                    $this->info( "[" . Carbon::now() . "] Error : " . $e->getMessage()   );  
                    exit();
            }               

            if( $invoice->status == 'refunded') {
                $this->info( "[" . Carbon::now() . "]  paid to " . $user_profile->email . " at id of " . $invoice->id . " in transfer "   ); 
            } else {
                $this->info( "[" . Carbon::now() . "]  failed to " . $user_profile->email . " at id of " . $invoice->id . " in transfer "   );                 
            }

        }   // 유저별 환불 처리 
  
    } 

}
