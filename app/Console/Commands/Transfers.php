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
    protected $signature = 'payment:Transfers';

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
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $coin_config = Config::get('coin');   

        // 이전날의 결제 완료된 결제 요구 테이블을 통계를 내서 구한다
        $invoice_users = DB::table('invoices')
            ->select('user_id' , DB::raw(' SUM(amount_received) as total_revenue' ) 
                , DB::raw(' SUM(pi_amount_received) as pi_total_revenue' ) , DB::raw(' count(amount_received) as total_count' ) )
            ->where('status' , 'confirmed' )->groupBy('user_id' )->get();

        $pay_user = Config::get('common.pay_user');   

        foreach( $invoice_users as $invoice_user ) {
            
            // 루프를 돌면서 아이디마다 정산 처리
            $user = User::find( $invoice_user->user_id );
            $user_profile = UserProfile::find( $invoice_user->user_id );
            $account = Account::whereUserId( $user->id )->whereCurrency( $user_profile->settlement_currency )->first();

//            print_r( $invoice_user );
//            exit();

            if( $user_profile->settlement_currency == 'PI') {
                $fee = NUMBER_ZERO;
                $amount2 = $invoice_user->pi_total_revenue;
                $net = $amount2 - $fee;
            } else {
                $fee = $invoice_user->total_revenue * $coin_config['krw']['feerate'];
                $amount2 = $invoice_user->total_revenue;
                $net = $amount2 - $fee;
            }

            // 결제 송금  API 호출 
            $api_token =  $this->exchange_api->getAccessToken();
            $transfer = $this->exchange_api->transfer( $api_token['access_token'] , 
                [ 'from_email' => $pay_user['email'] , 'to_email' => $user->email , 'amount' =>  $net  , 'currency' => $user_profile->settlement_currency ]  );


            if( $transfer['status'] != 'success' ) {
                $this->info( "[" . Carbon::now() . "] Error :  " . $transfer['status']  . " "   );                                    
                return ;
            }

            // DB 입력 
            DB::beginTransaction();
            try {

                Invoice::where( 'user_id' , $user->id )->where( 'status' , 'confirmed' )->update( [ 'status' => 'settlement_complete' ] );

                // 전송 데이터 
                $transfer_data = [
                    'token' => $user->id . '_' . mt_rand(),
                    'user_id' => $user->id , 
                    'account_id' => $account->id , 
                    'status' => 'pending' , 
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
                $transfer_token = generateToken( 'trn' , $invoice->id  );

                $transfer->token = $transfer_token;
                $transfer->save();


                // Transaction 테이블 데이터 추가
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
                    'url' => url( "tranfer" ) ,
                    'description' => NULL , 
                ];

                Transaction::create ( $transaction_data ) ;


                DB::commit();

            } catch ( Exception $e) {
                    DB::rollback();
                    $this->info( "[" . Carbon::now() . "] Error : {$e} "   );                    
                    return ;
            }

        }

        
    }
}
