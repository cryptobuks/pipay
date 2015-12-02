<?php namespace App\Events;

use App\Events\Mailer;

class UserMailer extends Mailer {

	/**
	 * Outline all the events this class will be listening for. 
	 * @param  [type] $events 
	 * @return void         
	 */
	public function subscribe($events)
	{
		$events->listen('user.signup',  'App\Events\UserMailer@welcome');
		$events->listen('user.resend', 'App\Events\UserMailer@welcome');
		$events->listen('user.forgot', 'App\Events\UserMailer@forgotPassword');
		$events->listen('user.newpassword', 'App\Events\UserMailer@newPassword');
	}

	/**
	 * Send a welcome email to a new user.
	 * @param  string $email          
	 * @param  int    $userId         
	 * @param  string $activationCode 		
	 * @return bool
	 */

	public function welcome($email, $url, $link)
	{
		$subject = '[파이페이먼트] 회원 가입을 환영합니다.';
		$view = 'emails.welcome';
		$data['url'] = $url;
		$data['link'] = $link;
		$data['email'] = $email;

		return $this->sendTo($email, $subject, $view, $data );
	}

	/**
	 * Email Password Reset info to a user.
	 * @param  string $email          
	 * @param  int    $userId         
	 * @param  string $resetCode 		
	 * @return bool
	 */
	public function forgotPassword($email, $userId, $resetCode)
	{
		$subject = '[파이페이먼트] 패스워드 재설정 승인요청';
		$view = 'emails.reset';
		$data['userId'] = $userId;
		$data['resetCode'] = $resetCode;
		$data['email'] = $email;

		return $this->sendTo($email, $subject, $view, $data );
	}

	/**
	 * Email New Password info to user.
	 * @param  string $email          
	 * @param  int    $userId         
	 * @param  string $resetCode 		
	 * @return bool
	 */
	public function newPassword($email, $newPassword)
	{
		$subject = '[파이페이먼트] 새 패스워드 정보';
		$view = 'emails.newpassword';
		$data['newPassword'] = $newPassword;
		$data['email'] = $email;

		return $this->sendTo($email, $subject, $view, $data );
	}



}

