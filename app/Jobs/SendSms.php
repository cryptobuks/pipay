<?php namespace App\Jobs;

use App\Jobs\Job;
use Illuminate\Support\Facades\Config;
use Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendSms extends Job implements SelfHandling, ShouldQueue  {

	use InteractsWithQueue, SerializesModels;

	public $conf	= array('host' => 'daemon.smstong.co.kr', 'port' => '8888');
	public $sock	= NULL;
	public $packet	= NULL;
	public $from ;
	public $to ;
	public $msg ;	

	/**
	 * Create a new command instance.
	 *
	 * @return void
	 */
	public function __construct( $to, $msg )
	{

		$this->from = Config::get('sms.from');
		$this->to = $to;
		$this->msg = $msg;				

	}

	/**
	 * Handle the command.
	 *
	 * @return void
	 */
	public function handle()
	{
		$result = $this->sendsms_smstong();
		if( $result == 1){
			Log::info("Sms send to {$this->to} for succefully ");

			$this->delete();
		} else {
			Log::error("Sendsms error : {$result}");			

			if($this->attempts() > 3) {
				$this->delete();
			} else {
				$this->release(30);
			}
		}

	}


	public function sendsms_smstong() {

		if ($this->to=='' || $this->msg=='') return false;

		// 이용자 정보 입력
		$PACKETDATA['id'	]	= "payall"; //[필수값]문자통아이디
		$PACKETDATA['pw'	]	= "blcblc2014";//[필수값]문자통패스워드
		$PACKETDATA['stype']	= "PHP";
		$PACKETDATA['msg'	]	= $this->msg;
		$PACKETDATA['to'	]   = str_replace('-', '', $this->to);//-기호없이 전화번호만 , 1명이상일때 ^ (예)01000001111^01012340004^01012111111
		$PACKETDATA['from']	= str_replace('-', '', $this->from);//-기호없이 전화번호만 
		$PACKETDATA['indexkey']	= '1'; 
		$PACKETDATA['send_type']	= '1'; //기본값1 SMS, 2일때 LMS NULL인정


		## MMS 일경우
		if ($PACKETDATA['send_type'] == "3"){
			$FILEPACKET['filename'] = $_FILES['userfile']['name'];
			$FILEPACKET['filedata'] = fread(fopen($_FILES['userfile']['tmp_name'],"r"),filesize($_FILES['userfile']['tmp_name']));
			$FILEPACKET['filetype'] = $_FILES['userfile']['type'];
		}

		# socket connect and sending packet data
		if ($this->socketOpen())
		{


			if($PACKETDATA['send_type'] == "3") $this->getMultiPaket($PACKETDATA,$FILEPACKET);
			else $this->getPaket($PACKETDATA);

			$this->socketPutData();
			$RESINFO	=  $this->socketReade();
			$this->disConnect();

			##result receive
			//echo "<PRE>";
//			Log::info( $this->packet . "_" . json_encode( $RESINFO ) );
			//echo "</PRE>";

	  		return $RESINFO['RESULT']; // 1: 성공, 2: 로그인오류, 3: 포인트부족
		} else {
			return 0;
		}
	}

	public function smstongSocket ($conf=array())
	{
		$confs	= (is_array($conf) == true) ? $conf : $this->conf;
		foreach ($confs as $key => $val)
		{
			$this->conf[$key]	= $val;
		}
	}

	public function setConnect ()
	{
		$this->sock	= @fsockopen($this->conf['host'], $this->conf['port'], $errno, $errstr);
		return (!$this->sock) ? false : true;
	}

	public function getPaket ($data)
	{

		foreach ($data as $key => $val)
		{
			switch ($key)
			{
				case 'id' :
				case 'pw' :
					$packet[$key]	= $key."=".$val;
					break;
				case 'from' :
					$packet[$key]	= $key."=".$val;
					break;
				case 'msg' :
					$packet[$key]	= $key."=".$val;
					break;
				case 'to' :
					$packet[$key]	= $key."=".$val;
					break;
				case 'indexkey' :
					$packet[$key]	= $key."=".$val;
					break;
				case 'send_type' :
					$packet[$key]	= $key."=".$val;
					break;
			}
		}
	
		$datas	= $packet['id'] ."&". $packet['pw']."&".$packet['from']."&". $packet['to'] ."&". $packet['msg']."&". $packet['indexkey']."&". $packet['send_type'];
		$this->packet  = "Host: ".$this->conf['host']."\r\n"; 
		$this->packet .= "Content-Type: application/x-www-form-urlencoded\r\n"; 
		$this->packet .= "Content-Length: ".strlen($datas)."\r\n"; 
		$this->packet .= "Connection: close\r\n\r\n"; 
		$this->packet .= $datas;

	}

	public function getMultiPaket ($data,$filedata)
	{
		srand((double)microtime()*1000000); 
		$boundary = "---------------------------".substr(md5(rand(0,32000)),0,10);
		

		foreach ($data as $key => $val)
		{
			$packet .="--".$boundary."\r\n";
			$packet .= "Content-Disposition: form-data; name=\"".$key."\"\r\n";
			$packet .= "\r\n".$val."\r\n";
		}
		
		
		if($filedata["filename"] && $filedata["filedata"] && $filedata["filetype"]){
			$packet .= "--$boundary\r\n";
			$packet .= "Content-Disposition: form-data; name=\"userfile\"; filename=\"".$filedata["filename"]."\"\r\n";
			$packet .= "Content-Type: ".$filedata["filetype"]."\r\n\r\n";
			$packet .= $filedata["filedata"]."\r\n";
			$packet .="--$boundary--\r\n";		
		}
		
		
		$this->packet  = "Host: ".$this->conf['host']."\r\n"; 
		$this->packet .= "Content-type: multipart/form-data; boundary=".$boundary."\r\n";
		$this->packet .= "Content-Length: ".strlen($packet)."\r\n\r\n"; 
		$this->packet .= $packet;
	}

	public function socketOpen ()
	{
		return ($this->setConnect()) ? true : false;
	}

	public function socketPutData ()
	{
		fputs($this->sock, "POST / HTTP/1.1\r\n");
		fputs($this->sock, $this->packet);
		fputs($this->sock, "\r\n");
	}

	public function socketReade ()
	{
		$sockData	= NULL;
		
		$buffer =  fread($this->sock, 1024*10);
		$buffers = explode("\r\n\r\n",$buffer);
		return unserialize(trim($buffers[1]));
	}

	public function disConnect ()
	{
		if ($this->sock) { fclose($this->sock); unset($this->sock); }
	}
}
