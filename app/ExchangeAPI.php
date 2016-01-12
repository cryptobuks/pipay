<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Redis;
use Log;

class ExchangeAPI extends Model
{
    /**
     * [getAccessToken API 액세스 토큰을 받아온다.]
     * @return [type] [description]
     */
    public function getAccessToken( ) {

        $pay_user = Config::get('common.pay_user');   
        $api_token = Redis::get('pay_api_token');

        if( !empty( $api_token ) ) return  json_decode( $api_token  , true ) ;

        // 조건이 맞으면 거래소 출금 API 호출
        try {
            $client = new \GuzzleHttp\Client( [ 'base_uri' =>  $pay_user['base_uri']  ] );

            $res = $client->request( 'POST' , '/oauth/access_token' , [
                'form_params' => [
                    'grant_type' => 'password' , 
                    'client_id' => $pay_user['client_id'] , 
                    'client_secret' => $pay_user['client_secret'] ,                                 
                    'username' => $pay_user['email'] ,                 
                    'password' => $pay_user['password']                               
                ]
             ]);

         $api_token =  $res->getBody() ;

         Redis::set( 'pay_api_token', $api_token );
         Redis::expire( 'pay_api_token' , 10 );

        } catch ( RequestException $e) {
            Log::info( json_encode( $e->getMessage() )) ;
            return [ 'status' => 'api_unauthorized' ];            
        } catch (ClientException $e) {
            return  [ 'status' => 'http_client_error' ];
        }

        return json_decode( $api_token , true ) ;
    }

    /**
     * [pay_transfer 간단 결제 전송 ]
     * @param  [type] $access_token [description]
     * @param  [type] $data         [description]
     * @return [type]               [description]
     */
    public function pay_transfer( $access_token , $data ) {
        
        $pay_user = Config::get('common.pay_user');   

        // 조건이 맞으면 거래소 출금 API 호출
        try {

            $client = new \GuzzleHttp\Client( [ 'base_uri' =>  $pay_user['base_uri']  ] );

            $res = $client->request( 'POST' , '/api/v2/funds/coins/transfer' , [
                'headers' => [
                    'User-Agent' => 'PI-Payment/0.1' ,
                    'Authorization' => 'Bearer ' . $access_token , 
                ] ,
                'form_params' => [
                    'from_address' => $data['user_email'] , 
                    'to_address' => $pay_user['email'] , 
                    'amount' => $data['pi_amount']  ,                                 
                    'currency' => 'PI' ,                 
                ] ,
             ]);

            $result =  $res->getBody() ;

        } catch ( RequestException $e) {
            return [ 'status' => 'unauthorized' ]  ;            
        } catch (ClientException $e) {
            return  [ 'status' => 'http_client_error' ]  ;            
        }

        return json_decode( $result  , true ) ;

    }

    /**
     * [transfer  결제 전송 ]
     * @param  [type] $access_token [description]
     * @param  [type] $data         [description]
     * @return [type]               [description]
     */
    public function transfer( $access_token , $data ) {
        
        $pay_user = Config::get('common.pay_user');   

        $url_path = '/api/v2/funds/deposit';

        try {

            $client = new \GuzzleHttp\Client( [ 'base_uri' =>  $pay_user['base_uri']  ] );

            $res = $client->request( 'POST' , $url_path , [
                'headers' => [
                    'User-Agent' => 'PI-Payment/0.1' ,
                    'Authorization' => 'Bearer ' . $access_token , 
                ] ,
                'form_params' => [
                    'address' => $data['to_email'] , 
                    'amount' => $data['amount']  ,                                 
                    'currency' => $data['currency'] ,                 
                ] ,
             ]);

            $result =  $res->getBody() ;

        } catch ( RequestException $e) {
            return [ 'status' => 'unauthorized' ]  ;            
        } catch (ClientException $e) {
            return  [ 'status' => 'http_client_error' ]  ;            
        }

        return json_decode( $result  , true ) ;

    }    
    

    /**
     * [move  계좌 이동 ]
     * @param  [type] $access_token [description]
     * @param  [type] $data         [description]
     * @return [type]               [description]
     */
    public function move( $access_token , $data ) {
        
        $pay_user = Config::get('common.pay_user');   
        $url_path = '/api/v2/funds/coins/transfer';

        try {

            $client = new \GuzzleHttp\Client( [ 'base_uri' =>  $pay_user['base_uri']  ] );

            $res = $client->request( 'POST' , $url_path  , [
                'headers' => [
                    'User-Agent' => 'PI-Payment/0.1' ,
                    'Authorization' => 'Bearer ' . $access_token , 
                ] ,
                'form_params' => [
                    'from_address' => $data['from_address'] , 
                    'to_address' => $data['to_address'] , 
                    'amount' => $data['amount']  ,                                 
                    'currency' => $data['currency']  ,                 
                ] ,
             ]);


            $result =  $res->getBody() ;

        } catch ( RequestException $e) {
            return [ 'status' => 'unauthorized' ]  ;            
        } catch (ClientException $e) {
            return  [ 'status' => 'http_client_error' ]  ;            
        }

        return json_decode( $result  , true ) ;

    }        
}
