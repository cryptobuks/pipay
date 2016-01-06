<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Cartalyst\Sentry\Sentry;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Invoice;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{

    protected $sentry;
    
    /**
     * Create a new home controller instance.
     *
     * @return void
     */
    public function __construct( Sentry $sentry)
    {
        $this->sentry = $sentry;
        $this->middleware( 'auth' , [ 'only' => [ 'dashboard'  ] ] );   

    }    
   
    /**
     * Display a index of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        if( $this->sentry->check() ) {
            return redirect('dashboard');
        } else {
            return view('home');
        }
    }

    /**
     * Display a index of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function support()
    {
       return 'support';
    }


    /**
     * Display a dashboard of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function dashboard()
    {
        $user = $this->sentry->getUser();
        $user_id  = $user->id;

         // 일간 총 매출
        $month_totalInvoice = Invoice::select(DB::raw('left(created_at, 10) AS date'), 
                                DB::raw('SUM(IF(currency = "KRW",amount_received,0) + IF(currency = "Pi",pi_amount_received * rate, 0)) as total')
                                )
                ->where('status', '=' ,'confirmed')
                ->where('user_id', '=', $user_id)
                ->groupBy('date')
                ->having('date', '>=' , DB::raw('left(NOW() - INTERVAL 1 MONTH ,10)'))
                ->orderBy( 'date' , 'asc' )->get();
        $jsonTable_monthInvoice = $this->createJsonTable($month_totalInvoice);

        // 전날 총 매출
        $day_totalInvoice = Invoice::select(DB::raw('left(created_at, 10) AS date'), 
                                DB::raw('SUM(IF(currency = "KRW", amount_received, 0)) as KRW_amount'), 
                                DB::raw('SUM(IF(currency = "Pi", pi_amount_received,0)) as PI_amount'),
                                DB::raw('SUM(IF(currency = "KRW",amount_received,0) + IF(currency = "Pi",pi_amount_received * rate, 0)) as total')
                                )
                ->where('status', '=' ,'confirmed')
                ->where('user_id', '=', $user_id)
                ->groupBy('date')
                ->having('date', '=' , DB::raw('CURDATE()  - INTERVAL 1 DAY'))->first();

        if( is_null( $day_totalInvoice ) ) {
<<<<<<< HEAD
            $day_totalInvoice=[];
            $day_totalInvoice['KRW_amount'] = '0';
            $day_totalInvoice['PI_amount'] = '0';
            $day_totalInvoice['total'] = '0';
=======
            $day_totalInvoice = (object) array(
                    'KRW_amount' => '0',
                    'PI_amount' => '0',
                    'total' => '0'
                );
>>>>>>> c17a3f6
        }

        return view('dashboard', compact('jsonTable_monthInvoice','day_totalInvoice'));
    }

    public function agreement()
    {
        return view('agreement');
    }


    // google cahrt 구조 테이블 생성
    private function createJsonTable( &$DB )
    {
        $table = array();
        
        $table['cols'] = array(

            array('label' => '날짜', 'type' => 'string'),
            array('label' => '일 매출', 'type' => 'number'),

        );

        if (!is_null($DB)) 
        {
            $rows = array();

            foreach ($DB as $tgl) 
            {
                $temp = array();

                $temp[] = array('v' => (string) $tgl->date);
                $temp[] = array('v' => (float) $tgl->total); 

                $rows[] = array('c' => $temp);
            }
            $table['rows'] = $rows;
        }

        return json_encode($table);
    }
}
