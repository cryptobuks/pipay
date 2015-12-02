<?php namespace App\Events;

class TransactionMailer extends Mailer {

	/**
	 * Outline all the events this class will be listening for. 
	 * @param  [type] $events 
	 * @return void         
	 */
	public function subscribe($events)
	{
		$events->listen('withdraw.activation',	'App\Events\TransactionMailer@withdraw_activation');
	}

	/**
	 * Send a withdraw activation email to a new user.
	 * @param  string $email          
	 * @param  int    $userId         
	 * @param  string $name         
	 * @param  string $activationCode 		
	 * @return bool
	 */
	public function withdraw_activation($email, $userId, $name , $withdraw , $activationCode )
	{
		$subject = '[파이페이먼트] 승인 필요:파이 출금 요청';
		$view = 'emails.withdraw_activation';
		$data['userId'] = $userId;
		$data['email'] = $email;
		$data['name'] = $name;
		$data['activationCode'] = $activationCode;
		$data['withdraw'] = json_decode( $withdraw , true );
		
		return $this->sendTo($email, $subject, $view, $data );
	}


}