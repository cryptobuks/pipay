<?php

/**
 * Generate password - helper function
 * From http://www.phpscribble.com/i4xzZu/Generate-random-passwords-of-given-length-and-strength
 *
 */
function _generatePassword($length=9, $strength=4) {
    $vowels = 'aeiouy';
    $consonants = 'bcdfghjklmnpqrstvwxz';
    if ($strength & 1) {
        $consonants .= 'BCDFGHJKLMNPQRSTVWXZ';
    }
    if ($strength & 2) {
        $vowels .= "AEIOUY";
    }
    if ($strength & 4) {
        $consonants .= '23456789';
    }
    if ($strength & 8) {
        $consonants .= '@#$%';
    }

    $password = '';
    $alt = time() % 2;
    for ($i = 0; $i < $length; $i++) {
        if ($alt == 1) {
            $password .= $consonants[(rand() % strlen($consonants))];
            $alt = 0;
        } else {
            $password .= $vowels[(rand() % strlen($vowels))];
            $alt = 1;
        }
    }

    return $password;
}    

/**
 * Generate a random string.
 *
 * @return string
 */
function getRandomString($length = 42)
{
    // We'll check if the user has OpenSSL installed with PHP. If they do
    // we'll use a better method of getting a random string. Otherwise, we'll
    // fallback to a reasonably reliable method.
    if (function_exists('openssl_random_pseudo_bytes'))
    {
        // We generate twice as many bytes here because we want to ensure we have
        // enough after we base64 encode it to get the length we need because we
        // take out the "/", "+", and "=" characters.
        $bytes = openssl_random_pseudo_bytes($length * 2);

        // We want to stop execution if the key fails because, well, that is bad.
        if ($bytes === false)
        {
            throw new \RuntimeException('Unable to generate random string.');
        }

        return substr(str_replace(array('/', '+', '='), '', base64_encode($bytes)), 0, $length);
    }

    $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

    return substr(str_shuffle(str_repeat($pool, 5)), 0, $length);
}

function getRandomNumber($length = 6 )
{
    $start_num = (int)str_pad("1" , $length , "0");
    $end_num = (int)str_pad("9" , $length , "9");   
    return mt_rand( $start_num , $end_num );
}

function amount_format($amount ,$limit=8 )
{
    if( $amount != '')
        $result = preg_replace('/[\.]*[0]+$/i', '', number_format($amount, $limit ));
    else
        $result = 0;
  
  return $result;
}

function api_error_handler( $error , $message )
{
    return [
        'error' => $error , 
    ];
    //  'error_description' => $message ,                   
}

// 서비스거래번호를 생성한다.
function generateSvcTxSeqno() {   
    $numbers  = "0123456789";   
    $svcTxSeqno = date("YmdHis");   
    $nmr_loops = 6;   
    while ($nmr_loops--) {   
        $svcTxSeqno .= $numbers[mt_rand(0, strlen($numbers)-1)];   
    }   
    return $svcTxSeqno;   
}   


// API 로그 포맷 
function api_log_format( $log_param , $request , $response )
{

    if (App::environment('local')) {
        $log_desc = 'URL:' . $log_param['URL'] . ', User_email:' . $log_param['email'] . ', IP address:' . $log_param['IP'] . ', header:' . json_encode($log_param['header']) ;
        Log::useDailyFiles( storage_path() . '/logs/api.log' , 1 , 'debug' );   
        Log::info( $log_desc , [ 'Request' => $request , 'Reponse' => $response ] );
    }
}

