<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cartalyst\Sentry\Sentry;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Response;
use App\Invoice;
use App\Oaccount;

class PaymentController extends Controller
{

    protected $sentry;
    
    /**
     * Create a new payment controller instance.
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

        if ($request->ajax()) {
            $user = $this->sentry->getUser();
            $user_id  = $user->id;
            
            if( !empty($request['filter'] ) ) {
                $invoices = Invoice::where('user_id','=',$user_id)->where('status','=',$request['filter'] )->orderBy( 'id' , 'desc' )->paginate($pagePer);
            } else {
                $invoices = Invoice::where('user_id','=',$user_id)->orderBy( 'id' , 'desc' )->paginate($pagePer);
            }

            $jsonTable = [];
                
            foreach ( $invoices as $invoice){
                $jsonTable[] = array(
                    'id' => $invoice->id,
                    'created_at' => $invoice->created_at->format('Y-m-d H:i:s'),
                    'item_desc' => $invoice->item_desc,
                    'amount' => $invoice->amount,
                    'status' => $invoice->status,
                    'pi_amount_received' => $invoice->pi_amount_received,
                    'pi_amount' => $invoice->pi_amount
                );
            }

            return $jsonTable;
        }

        return view('payments.index', compact('pagePer'));
    }

    /**
     * Display a show of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show( $id )
    {
        $user = $this->sentry->getUser();
        $user_id  = $user->id;

        $invoice = Invoice::select('id', 'token' ,'amount','pi_amount','pi_amount_received','pi_amount_refunded','customer_email','customer_name','customer_custom','status','currency','created_at','completed_at')->find( $id )->toJson();
        $accounts = Oaccount::where('user_id', '=', $user_id)->get();

        foreach ( $accounts as $account) {
            if( 1 == $account->currency_id ){
                $data = json_decode($invoice, true);
                $data['balance'] = $account->amount();
                $invoice = json_encode($data);
            } 
        }        
        
        return $invoice;
    }

    /**
     * Display a receipt of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function receipt( $id )
    {
        $invoice = Invoice::where('token', '=',  $id )->first();

        return view('payments.receipt', compact('invoice') );
    }
    
}
