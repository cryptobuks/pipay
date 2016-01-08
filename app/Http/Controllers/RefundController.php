<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Cartalyst\Sentry\Sentry;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Refund;
use App\Invoice;
use App\Ouser;
use App\OuserAddress;
use Validator;


class RefundController extends Controller
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
     * Display a refund of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( $id )
    {
        return view('payments.refund');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store( Request $request  )
    {
        $input = $request->all();
        
        $this->validate( $request , [
            'address'  => 'required|max:80',   
            'amount'  => 'required|numeric|min:0.01|max:1000000',
        ]);

        $user = Ouser::find( $this->sentry->getUser()->id );
        $userAddress = OuserAddress::where('user_id' , $user->id )->first();

        $address = $input['address'];
        $result['success'] = false;

        // 이메일 확인  (내부 회원)
        $validator = Validator::make( $input, [  'address'  => 'required|email',  ]);
        if( !$validator->fails() )   {

            if( $user->email == $address  ) {

                $receiver_user = Ouser::where( 'activated' , 1 )->where( 'email' , $address )->first();

                if( isset( $receiver_user ) ) {

                    $result['success'] = true;
                } else {

                    $result['message'] = 'not_activated_email';
                    $result['success'] = false;
                    return Response::json ( $result , 400 );    
                } 
            } else {

                $result['message'] = 'invalid_email' ;
                $result['success'] = false;
                return Response::json ( $result , 400 );    
            }
            
        }        

        // 파이 코인 주소 정보 확인 (내부/외부 회원)
        if( !$result['success'] ) {

            $isAddress = invoice::getValidateAddress($address) ;

            if( $isAddress === null || $isAddress->isvalid === false){
                $result['message'] = 'invalid_address' ;
                $result['success'] = false;
                return Response::json ( $result , 400 );    
            } else if( $userAddress->address == $address && $isAddress->ismine === true ) {
              
                if( $user->activated == true ) {

                    $result['success'] = true;

                } else {
                    $result['message'] = 'not_activated_address' ;
                    $result['success'] = false;
                    return Response::json ( $result , 400 );    
                }
            }
        }

        // 검증 성공 후 환불하기 입력 
        if( $result['success'] == true ) {

            $refund_data = [
                        'user_id' => $user->id,
                        'invoice_id' => $input['invoice_id'], 
                        'address' => $input['address'],
                        'pi_amount' =>  $input['amount'], 
                        'amount' =>  $input['amount'] * 10000 , 
                        'currency' => 'PI' , 
                    ];

            DB::beginTransaction();
            try {

                Refund::create($refund_data);
                $result['success'] = true;

                DB::commit();

            }  catch (Exception $e) {

                DB::rollback();
                $result['message'] = 'save_failure' ;
                $result['success'] = false;
                return Response::json ( $result , 400 );   
                
            }  
        }

        if( $result['success'] == true ){
            $status = 200;
        } else {
            $result['message'] = 'save_failure' ;
            $result['success'] = false;
            $status = 400;
        }

        return Response::json( $result , $status );
    }

}
