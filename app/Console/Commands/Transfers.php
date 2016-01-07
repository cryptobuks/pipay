<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use App\UserProfile;
use App\Account;
use App\Transaction;
use App\Invoice;
use App\Payment;
use App\Transfer;
use App\Fee;
use App\ExchangeAPI;
use Exception;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;


class Transfers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:transfers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Settlement payment';


    protected $exchange_api ;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct( ExchangeAPI $exchange_api )
    {
        parent::__construct();
        $this->exchange_api = $exchange_api;
    }

    /**
     * 결제 완료된 내역을 일정 주기마다 정산 처리를 하고 송금해 주는 프로그램
     *
     * @return mixed
     */
    public function handle()
    {

        // 기본 설정 가져옴 
        $coin_config = Config::get('coin');   
        $pay_user = Config::get('common.pay_user');   

        // 현재까지 결제 완료된 상태의 데이터를 통계를 내서 정산 가능한 판매자를 구한다
        $invoice_users = DB::table('invoices')
            ->select('user_id' , DB::raw(' SUM(amount_received) as total_revenue' ) 
                , DB::raw(' SUM(pi_amount_received) as pi_total_revenue' ) , DB::raw(' count(amount_received) as total_count' ) )
            ->where( 'status' , 'confirmed' )->groupBy('user_id' )->get();


         // 아이디마다 계산을 해서 정산 처리
        foreach( $invoice_users as $invoice_user ) {

            // 각 사용자 데이터 불러오기             
            $user = User::find( $invoice_user->user_id );   // 기본 유저 데이터 
            $user_profile = UserProfile::find( $invoice_user->user_id );   // 유저 회사 프로필 
            $account = Account::whereUserId( $user->id )->whereCurrency( $user_profile->settlement_currency )->first();  // 결제 계정 정보

            // 정산 통화마다 설정 바꿈 
            if( $user_profile->settlement_currency == 'PI') {
                $fee = $invoice_user->pi_total_revenue * $coin_config['pi']['feerate'];
                $amount2 = $invoice_user->pi_total_revenue;
                $net = $amount2 - $fee;
            } else {
                $fee = $invoice_user->total_revenue * $coin_config['krw']['feerate'];
                $amount2 = $invoice_user->total_revenue;
                $net = $amount2 - $fee;
            }

            // 송금 시작 
            DB::beginTransaction();
            try {

                // 송금 데이터 생성 
                $transfer_data = [
                    'token' => $user->id . '_' . mt_rand(),
                    'user_id' => $user->id , 
                    'account_id' => $account->id , 
                    'status' => 'pending' ,     // 대기 상태 
                    'type' => 'pipay_account' ,                     
                    'amount' => $net ,                     
                    'amount_reversed' => NUMBER_ZERO ,                     
                    'currency' => $user_profile->settlement_currency  ,                                                                                 
                    'description' => '' ,                     
                    'destination_type' => NULL ,                     
                    'destination_id' => NULL ,   
                    'livemode' => 1 ,                                                                                                     
                ];

                $transfer = Transfer::create ( $transfer_data );
                $transfer_token = generateToken( 'trn' , $transfer->id  );

                $transfer->token = $transfer_token;
                $transfer->save();

               // 계정에 있는 송금할 전송액 잠금 
               $account->lock_funds( $amount2  ,  ACCOUNT_TRANSFER_LOCK ,   $transfer );

               DB::commit();

               $transfer_id = $transfer->id;

            } catch ( Exception $e) {
               DB::rollback();
               $this->info( "[" . Carbon::now() . "] Error : " . $e->getMessage()   );  

               continue ;
            }

            // 송금 시작 
            DB::beginTransaction();
            try {

                // 접속 토큰 생성
                $api_token =  $this->exchange_api->getAccessToken();

                // 결제 송금  API 호출 
                $transfer_row = $this->exchange_api->transfer( $api_token['access_token'] , 
                    [  'to_email' => $user->email , 'amount' =>  $net  , 'currency' => $user_profile->settlement_currency ]  );

                // 성공시 결제 계정에서 송금 금액을 빼줌.
                if( $transfer_row['status'] == 'success' ) {
                   $account->unlock_and_sub_funds( $amount2  ,  $amount2 , $fee , ACCOUNT_TRANSFER ,   $transfer );
               } else {
                    $this->info( "[" . Carbon::now() . "] Error :  " . $transfer_row['status']  . " "   );
                    throw new  Exception( 'transfer failed' );
                }

                // 전송시   로그  기록 
                $transaction_data = [
                    'user_id' => $user->id  , 
                    'account_id' => $account->id , 
                    'amount' =>  $amount2 , 
                    'currency' => $user_profile->settlement_currency , 
                    'fee' =>  $fee , 
                    'fee_id' => NUMBER_ZERO ,                 
                    'net' => $net  , 
                    'source_id' => $transfer->id , 
                    'source_type' => get_class( $transfer )  ,                                 
                    'status' => 'available'  ,                                 
                    'type' => 'transfer'  , 
                    'url' => url( "transfer" ) ,
                    'description' => NULL , 
                ];

               Transaction::create ( $transaction_data ) ;

                // 결제 요청  송금 완료  업데이트 
               Invoice::where( 'user_id' , $user->id )->where( 'status' , 'confirmed' )->update( [ 'status' => 'settlement_complete' ] );

               // 전송 완료 
               $transfer->status = 'paid';
               $transfer->save();

               DB::commit();

            } catch ( Exception $e) {
                    DB::rollback();
                    $this->info( "[" . Carbon::now() . "] Error : " . $e->getMessage()   );  

                    // 실패 업데이트 
                    $transfer->status = 'failed';
                    $transfer->save();

                   // 계정에 있는 송금할 전송액 잠금  품 
                   $account->unlock_funds( $amount2  ,  ACCOUNT_TRANSFER_UNLOCK ,   $transfer );
            }               

            if( $transfer->status == 'paid') {
                $this->info( "[" . Carbon::now() . "]  paid to " . $user->email . " at id of " . $transfer_id . " in transfer "   ); 
            } else {
                $this->info( "[" . Carbon::now() . "]  failed to " . $user->email . " at id of " . $transfer_id . " in transfer "   );                 
            }


        }   // 유저별 정산 처리 

        
    }
}
