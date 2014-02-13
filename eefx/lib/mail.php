<?php

# ExEngine 7 / Libs / Internet Mail Class

/*
	This file is part of ExEngine.
	Copyright © LinkFast Company
	
	ExEngine is free software; you can redistribute it and/or modify it under the 
	terms of the GNU Lesser Gereral Public Licence as published by the Free Software 
	Foundation; either version 2 of the Licence, or (at your opinion) any later version.
	
	ExEngine is distributed in the hope that it will be usefull, but WITHOUT ANY WARRANTY; 
	without even the implied warranty of merchantability or fitness for a particular purpose. 
	See the GNU Lesser General Public Licence for more details.
	
	You should have received a copy of the GNU Lesser General Public Licence along with ExEngine;
	if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, Ma 02111-1307 USA.
*/

class eemail {
	
	const APP = "ExEngine Internet Mail Class";
	const VERSION = "0.0.2.3";
	
	private $ee;
	
	public $FromName=null;
	public $FromOnlyAddress;
	public $To;
	public $ToName=null;
	public $Subject;
	public $Message;	
	public $ReplyTo=null;
	public $ReplyToName=null;
	public $MessageTextOnly=null;
	
	public $swiftObj;

	public $CC=null;
	public $BCC=null;
	
	public $SMTP_Cfg_Array;
	
	public $AdditionalHeaders;
	
	private $Attachements;
	private $AttCount=0;
	
	private $Recipients;
	private $RecipientsCount=0;
	
	public $LastError=null;
	
	function __construct($parent=null) {
		if ($parent != null)
			$this->ee = &$parent;	
		else
			$this->ee = &ee_gi();

	}
	
	function addAttachment($Location) {
		$this->Attachements[$this->AttCount]["location"] = $Location;
		$this->AttCount++;
	}
	
	function addRecipient($mail,$name=null) {
		$this->Recipients[$this->RecipientsCount]["mail"] = $mail;
		if (isset($name)) 
			$this->Recipients[$this->RecipientsCount]["name"] = $name;
		else
			$this->Recipients[$this->RecipientsCount]["name"] = null;
		$this->RecipientsCount++;
	}
	
	static public function createSMTPCfgArray($Host,$Port,$User,$Password,$AuthRequired,$Encryption="none",$Debug=false) {
		$a['host'] = $Host;
		$a['port'] = $Port;
		$a['user'] = $User;
		$a['password'] = $Password;
		$a['auth'] = $AuthRequired;
		$a['debug'] = $Debug;
		$a['encryption'] = $Encryption;
		return $a;
	}
	
	private function create_pear_SMTPConfig() {
		$b = $this->SMTP_Cfg_Array;
		
		if ($b['encryption'] == "ssl") {
			$b['host'] = "ssl://".$b['host'];	
		}
		
		unset($b['encryption']);	
		return $b;
	}
	
	function send_pear() {
		
		require_once "Mail.php";
		include 'Mail/mime.php' ;	

        $to = $this->To;
        $subject = $this->Subject;
		
		$headers = array ('To' => $to,
          'Subject' => $subject);	
		
		$html = $this->Message;
		$crlf = $this->compatSetEOL();	
		
		$mime = new Mail_mime($crlf);
		
		if ($this->AttCount >0) {
			for ($c=0;$c < $this->AttCount;$c++) {
				$mime->addAttachment($this->Attachements[$c]["location"]);	
			}
		}
		
		$mime->setHTMLBody($html);
		
		$body = $mime->get();
		$headers = $mime->headers($headers);	  
		  
        $smtp = Mail::factory('mail');

        $mail = $smtp->send($this->To, $headers, $body);
		
		return $mail;
	}
	
	function send_html() {
		$cabeceras  = 'MIME-Version: 1.0' . "\r\n";
		$cabeceras .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		
		$cabeceras .= 'To: '.$this->To . "\r\n";
		if ($this->From) {
			$cabeceras .= 'From: '.$this->From . "\r\n";
		}
		
		return mail($this->To, $this->Subject, $this->Message, $cabeceras);	
	}
	
	function send_pear_smtp() {		
	
		#this requires Mail and Mail_Mime installed in PEAR.
		@include_once 'Mail.php';
		@include_once 'Mail/mime.php' ;
		@include_once 'Mail/mail.php' ;
		
        $from = $this->FromOnlyAddress;
        $to = $this->To;
        $subject = $this->Subject;
		
		$headers = array (
		  'From' => $from,
          'To' => $to,
          'Subject' => $subject);	
		
		$text = 'Text version of email';
		$html = $this->Message;
		$crlf = $this->compatSetEOL();	
		
		$mime = new Mail_mime(array('eol' =>$crlf));
		$mime->setHTMLBody($html);
		
		if ($this->AttCount >0) {
			for ($c=0;$c < $this->AttCount;$c++) {
				$mime->addAttachment($this->Attachements[$c]["location"]);	
			}
		}	
		
		$mimeparams=array();		
		$mimeparams['text_encoding']="8bit";
		$mimeparams['text_charset']="UTF-8";
		$mimeparams['html_charset']="UTF-8";
		$mimeparams['head_charset']="UTF-8"; 
		
		$body = $mime->get($mimeparams);
		$headers = $mime->headers($headers);	  
		  
        $smtp = @Mail::factory('smtp',$this->create_pear_SMTPConfig());

        $mail = @$smtp->send($this->To, $headers, $body); 
		
		
		if (@PEAR::isError($mail)) {
			if (isset($this->SMTP_Cfg_Array['debug'])) {
		   		if ($this->SMTP_Cfg_Array['debug']) echo("<p>" . $mail->getMessage() . "</p>");
			}
			return false;
		} else {
		   	return true;
		}	}
	
	private function compatSetEOL() {
		if (strtoupper(substr(PHP_OS,0,3)=='WIN')) {
		  $eol="\r\n";
		} elseif (strtoupper(substr(PHP_OS,0,3)=='MAC')) {
		  $eol="\r";
		} else {
		  $eol="\r\n";
		} 	
		
		return $eol;
	}
	
	function send() {
		return mail($this->To, $this->Subject, $this->Message);
	}

	function prepare_swiftmailer_smtp() {
		if (function_exists('mb_internal_encoding') && ((int) ini_get('mbstring.func_overload')) & 2)
		{
		  $mbEncoding = mb_internal_encoding();
		  mb_internal_encoding('ASCII');
		}
		if (!$this->ee->eeLoad('swiftmailer/5.0.3/lib/swift_required'))
			$this->ee->errorExit("eemail","SwiftMailer is not installed, please install it (in eefx/extended/core/swiftmailer/5.0.3/) before using send_swiftmailer_smtp.","ExEngine_Mailer");
		$transport = Swift_SmtpTransport::newInstance($this->SMTP_Cfg_Array['host'], $this->SMTP_Cfg_Array['port'])
			->setUsername($this->SMTP_Cfg_Array['user'])
			->setPassword($this->SMTP_Cfg_Array['password']);

		if ($this->SMTP_Cfg_Array['encryption'] != 'none')
			$transport->setEncryption($this->SMTP_Cfg_Array['encryption']);
		$transport->setLocalDomain('[127.0.0.1]');
		$this->swiftObj = Swift_Mailer::newInstance($transport);
		return $this->swiftObj;
	}

	function prepare_swiftmailer_message() {
		$message = Swift_Message::newInstance()
		  ->setSubject($this->Subject)		  
		  ->setBody($this->Message,'text/html');		  
		if (isset($this->FromName))
			$message->setFrom(array($this->FromOnlyAddress => $this->FromName));
		else
			$message->setFrom($this->FromOnlyAddress);
		if (isset($this->ToName)) 
			$message->setTo(array($this->To => $this->ToName));
		else
			$message->setTo(array($this->To));
		if (isset($this->CC)) {
			if (is_array($this->CC)) {
				foreach ($this->CC as $cc_mail) {
					$message->AddCC($cc_mail);
				}
			} else
				$message->AddCC($this->CC);
		}
		if (isset($this->BCC)) {
			if (is_array($this->BCC)) {
				foreach ($this->BCC as $bcc_mail) {
					$message->AddBCC($bcc_mail);
				}
			} else
				$message->AddBCC($this->BCC);
		}
		if ($this->AttCount >0) {
			for ($c=0;$c < $this->AttCount;$c++) {
				$message->attach(Swift_Attachment::fromPath($this->Attachements[$c]["location"]));	
			}
		}
		if (isset($this->MessageTextOnly))
			$message->addPart($this->MessageTextOnly);
		
		$this->swiftObjMsg = $message;
		return $message;
	}

	function send_swiftmailer() {
		$failures = array();
		$result = $this->swiftObj->send($this->swiftObjMsg,$failures);

		if (isset($mbEncoding))
		{
		  mb_internal_encoding($mbEncoding);
		}

		if (!$result) {
			$this->LastError = $failures;
			return false;
		} return true;
	}

	function send_swiftmailer_smtp() {
		if (function_exists('mb_internal_encoding') && ((int) ini_get('mbstring.func_overload')) & 2)
		{
		  $mbEncoding = mb_internal_encoding();
		  mb_internal_encoding('ASCII');
		}

		if (!$this->ee->eeLoad('swiftmailer/5.0.3/lib/swift_required'))
			$this->ee->errorExit("eemail","SwiftMailer is not installed, please install it (in eefx/extended/core/swiftmailer/5.0.3/) before using send_swiftmailer_smtp.","ExEngine_Mailer");

		$transport = Swift_SmtpTransport::newInstance($this->SMTP_Cfg_Array['host'], $this->SMTP_Cfg_Array['port'])
			->setUsername($this->SMTP_Cfg_Array['user'])
			->setPassword($this->SMTP_Cfg_Array['password']);

		if ($this->SMTP_Cfg_Array['encryption'] != 'none')
			$transport->setEncryption($this->SMTP_Cfg_Array['encryption']);

		$mailer = Swift_Mailer::newInstance($transport);
		$this->swiftObj = &$mailer;

		$message = Swift_Message::newInstance()
		  ->setSubject($this->Subject)		  
		  ->setBody($this->Message,'text/html');

		  $this->swiftObjMsg = &$message;
		if (isset($this->FromName))
			$message->setFrom(array($this->FromOnlyAddress => $this->FromName));
		else
			$message->setFrom($this->FromOnlyAddress);
		if (isset($this->ToName)) 
			$message->setTo(array($this->To => $this->ToName));
		else
			$message->setTo(array($this->To));
		if (isset($this->CC)) {
			if (is_array($this->CC)) {
				foreach ($this->CC as $cc_mail) {
					$message->AddCC($cc_mail);
				}
			} else
				$message->AddCC($this->CC);
		}
		if (isset($this->BCC)) {
			if (is_array($this->BCC)) {
				foreach ($this->BCC as $bcc_mail) {
					$message->AddBCC($bcc_mail);
				}
			} else
				$message->AddBCC($this->BCC);
		}
		if ($this->AttCount >0) {
			for ($c=0;$c < $this->AttCount;$c++) {
				$message->attach(Swift_Attachment::fromPath($this->Attachements[$c]["location"]));	
			}
		}
		if (isset($this->MessageTextOnly))
			$message->addPart($this->MessageTextOnly);
		//$message->setPriority(2);

		$failures = array();
		$result = $mailer->send($message,$failures);

		if (isset($mbEncoding))
		{
		  mb_internal_encoding($mbEncoding);
		}

		if (!$result) {
			$this->LastError = $failures;
			return false;
		} return true;
	}
	
	function send_phpmailer_smtp() {
		
		#this requires PHPMailer installed as an extended engine.
		$this->ee->eeLoad('phpmailer/class.phpmailer');
		
		$mail = new PHPMailer;
		
		$si = $this->SMTP_Cfg_Array;
		$mail->IsSMTP();        
		                            // Set mailer to use SMTP
		$mail->Host = $si['host'];  // Specify main and backup server
		$mail->Port = $si['port'];
		$mail->SMTPAuth = $si['auth'];                               // Enable SMTP authentication
		$mail->Username = $si['user'];                            // SMTP username
		$mail->Password =  $si['password'];  
		if ($si['encryption'] != "none") {                         // SMTP password
			$mail->SMTPSecure = $si['encryption'];                            // Enable encryption, 'ssl' also accepted
		}
		
		$mail->From = $this->FromOnlyAddress;
		$mail->FromName = $this->FromName;
		$mail->AddAddress($this->To, $this->ToName);  // Add a recipient
		//$mail->AddAddress('ellen@example.com');               // Name is optional
		if (isset($this->ReplyTo))
			$mail->AddReplyTo($this->ReplyTo, $this->ReplyToName);
			
		if (isset($this->CC)) {
			//print "CC IS DEFINED";
			$mail->AddCC($this->CC);
		}
		
		if (isset($this->BCC))
			$mail->AddBCC($this->BCC);
		
		$mail->WordWrap = 50;   
		
		if ($this->AttCount >0) {
			for ($c=0;$c < $this->AttCount;$c++) {
				$mail->AddAttachment($this->Attachements[$c]["location"]);	
			}
		} 

		$mail->IsHTML(true);
		
		$mail->Subject = $this->Subject;
		$mail->Body    = $this->Message;
		
		if (isset($this->MessageTextOnly))
			$mail->AltBody = $this->MessageTextOnly;
		
		if(!$mail->Send()) {
			$this->LastError = $mail->ErrorInfo;
		   return false;
		} else {
			return true;
		}
					
	}
}
	
?>