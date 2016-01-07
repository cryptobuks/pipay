<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Nbobtc\Bitcoind\Bitcoind;
use Nbobtc\Bitcoind\Client;
use App\User;
use App\Account;
use App\PiTransaction;
use App\Transaction;
use App\Invoice;
use App\Payment;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Events\PaymentFinishEvent;
use App\Events\InvoiceFinishEvent;


class PiTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:piTransactions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Detecting coin transaction with payment daemon.';

    private $bitcoind = null;
    private $min = 0;
    private $max = 3;
    private $max_confirm = 500;
    private $rate = 10000;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        //

        $count = $this->max_confirm;
        $this->rate = Config::get('coin.pi.rate');        

        try {
            $this->bitcoind = new Bitcoind(new Client(Config::get('coin.pi.rpc')));
        } catch (Exception $ex) {
            $this->info("[" . Carbon::now() . "] Unable to connect to RPC server backend");
            return ;
        }

        // 마지막 트랜젝션 정보를 구한다.
        $last_tx1 = PiTransaction::orderBy('created_at' ,'desc')->take(1)->get();
        $last_tx = $last_tx1->toArray();

        $txs = array();
        if( !empty( $last_tx ) ) {
            $blocktr = $this->bitcoind->gettransaction( $last_tx[0]['txid'] );
            $blocks = $this->bitcoind->listsinceblock( $blocktr->blockhash );
            foreach( $blocks->transactions as $tx ) {
                if( $tx->account == Config::get('coin.pi.instant_name') && $tx->category == 'receive' ) array_push ( $txs ,  $tx );
            }
        } else {
            $block = $this->bitcoind->listTransactions( Config::get('coin.pi.instant_name') , $count , 0 );

            foreach( $block as $tx ) {
                if( $tx->category == 'receive' ) array_push ( $txs ,  $tx );
            }
        }
        
        try {
            DB::beginTransaction();

            // 새로운 트랙젝션을 추가한다.              
            $this->transaction_create ( $txs );

            // 기존의 트랙제션을 체크해 업데이트 한다.
            $this->transaction_update ();

            DB::commit();

            $this->info( "[" . Carbon::now() . "] It has been executed pi transaction. "  );

            } catch( Exception $e) {
                DB::rollback();
                $this->info( "[" . Carbon::now() . "] Error : {$e} "   );
                return ;
            }
    }


    // 트랜젝션 절차 처리
    public function transaction_create( $txs ){
        
        foreach( $txs as $tx ) {
            $tx_pay = PiTransaction::whereRaw('txid = ? ',array( $tx->txid ) )->first();

            // 트랜젝션이 없으면 추가 
            if( empty( $tx_pay ) ) {   

                // 트랜젝션 값 추가
                $pi_transaction = [
                    'txid' => $tx->txid ,
                    'amount' => $tx->amount ,
                    'confirmations' => $tx->confirmations ,
                    'address' => $tx->address ,
                    'state' => STATES_SUBMIT ,
                    'currency' => "PI" ,
                    'txout' => 0 ,  
                    'received_at' => date( 'Y-m-d H:i:s' , $tx->timereceived ) ,
                ];

                $payment_tx = PiTransaction::create($pi_transaction);

                $invoice = Invoice::whereRaw('inbound_address = ? ',array( $payment_tx->address ) )->first();

                if( isset( $invoice ) ) {
                    $invoice->status = 'pending';
                    $invoice->save();

                    \Event::fire( new InvoiceFinishEvent( $invoice ) );

                    $this->info("[" . Carbon::now() . "] add tx : " . $tx->txid    );                               
                } else {
                    $this->info("[" . Carbon::now() . "] no transactions "   );                               
                }
            }
        }
    }

    // 블럭 업데이트 및 계정 업데이트 
    public function transaction_update( ){
        
        $piTransactions = PiTransaction::whereRaw('confirmations < ? ',array( $this->max_confirm ) )->orderBy('id' ,'asc')->take(3000)->get();

        foreach( $piTransactions as $row ) {

            $tx = $this->bitcoind->gettransaction( $row->txid );
            $tx_pay = PiTransaction::whereRaw('txid = ? ',array( $row->txid ) )->first();
            $tx_pay->confirmations = $tx->confirmations;
            $tx_pay->save();

            $invoice = Invoice::where('inbound_address', $row->address )->where('status', 'pending' )->first();

            if( $tx->confirmations >= $this->max && isset( $invoice ) ) {

                // 결제  완료
                $invoice->amount_received = $invoice->amount_received + ( $tx_pay->amount * $this->rate ) ;
                $invoice->pi_amount_received = $invoice->pi_amount_received + $tx_pay->amount;                
                $invoice->status = 'confirmed';  
                $invoice->exception_status = 'false';  
                $invoice->completed_at = Carbon::now();                                
                $invoice->save();

                // 트랜젝션 완료
                $tx_pay->state = STATES_DONE;
                $tx_pay->save();

                $account = Account::whereUserId( $invoice->user_id )->whereCurrency( 'PI' )->first();
                $amount2 = $tx_pay->amount ;
                $fee = NUMBER_ZERO;
                $net = $amount2 - $fee;
                $currency = 'PI' ; 

                // payment 테이블 데이터 추가 
                $payment_data = [
                    'token' => $invoice->id . '_' .  mt_rand( ) ,
                    'user_id' => $invoice->user_id , 
                    'api_key' => $invoice->api_key , 
                    'account_id' => $account->id , 
                    'buyer_id' => NUMBER_ZERO , 
                    'invoice_id' => $invoice->id , 
                    'amount' =>  $amount2 , 
                    'amount_refunded' => NUMBER_ZERO , 
                    'currency' => $currency , 
                ];

                $payment = Payment::create( $payment_data );

                $payment_token = generateToken( 'pay' , $payment->id  );
                $payment->token = $payment_token;
                $payment->save();

                // 계정에 추가 
                $account->plus_funds( $amount2 , $fee ,  ACCOUNT_INVOICE_CONFIRMED ,   $payment );                

                // Transaction 테이블 데이터 추가
                $transaction_data = [
                    'user_id' => $invoice->user_id  , 
                    'account_id' => $account->id , 
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

                // 입금 완료가 되었음을 알린다.
                \Event::fire( new PaymentFinishEvent( $payment ) );

                $this->info( $payment->id . ' : Finished payment.'); 
                $this->info( "[" . Carbon::now() . "] update tx : " . $tx->txid . "\n" );                   
            }

        }
    }

}
