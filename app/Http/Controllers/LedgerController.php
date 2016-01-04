<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cartalyst\Sentry\Sentry;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Transaction;
use App\Account;

class LedgerController extends Controller
{

    protected $sentry;
    
    /**
     * Create a new ledger controller instance.
     *
     * @return void
     */
    public function __construct( Sentry $sentry)
    {
        $this->sentry = $sentry;

        $this->middleware( 'auth'  );

    }

    /**
     * Display a index of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( Request $request )
    {

        $pagePer = 10;

        $user = $this->sentry->getUser();
        $user_id  = $user->id;

        if ($request->ajax()) {
            $transactions = Transaction::where('user_id', '=', $user_id)->orderBy( 'id' , 'desc' )->paginate($pagePer);
            
            $jsonTable = [];
                
            foreach ( $transactions as $transaction){
                if( 'payment' == $transaction->type ) {
                    $jsonTable[] = array(
                        'created_at' => $transaction->created_at->format('Y-m-d H:i:s'),
                        'deposit' => $transaction->amount,
                        'withdraw' => 0,
                        'fee' => 0,
                    );
                } else if ( 'refund' == $transaction->type || 'transfer' == $transaction->type ) {
                    $jsonTable[] = array(
                        'created_at' => $transaction->created_at->format('Y-m-d H:i:s'),
                        'deposit' => 0,
                        'withdraw' => $transaction->amount,
                        'fee' => $transaction->fee,
                    );
                }
            }
        

            return $jsonTable;
        }

        $amount = [];

        $accounts = Account::where('user_id', '=', $user_id)->get();
        
        foreach ( $accounts as $account) {
            if( 'PI' == $account->currency ){
                $amount['pi'] = $account->balance - $account->locked;
            } 
            else if ( 'KRW' == $account->currency) {
                $amount['krw'] = $account->balance - $account->locked;
            }
        }        

        $AccountJson[] = (object) array(
            'KRW' => $amount['krw'],
            'PI' => $amount['pi']
         );

        return view('ledgers.index', compact('AccountJson','pagePer'));
    }

}
