<?php
#<EEMEv4>#

class fbjssdk
{	
	#EEMEv4 Control#
	private $ee;
	const CNAME = "fbjssdk";
	const NAME = "ExEngine Facebook JS SDK";
	const VERSION = "0.0.0.1";
	const DATE = "17/12/2011";
	const RQEE7 = "7.0.7.13";
	#EEMEv4 Control#
	
	public $AppID;
	public $ParseXFBML = true;		
	public $ChannelFile="auto";
		
	function __construct($ee7Object) {
		$this->ee = &$ee7Object;
	}
	
	function load($return=false) {
		if ($this->ParseXFBML)
			$pXML = "true";
		else
			$pXML = "false";
			
		if ($this->ChannelFile!="auto")
			$channel = $this->ChannelFile;
		else
			$channel = $this->serveAutoChannel();
			
		$data = "<div id=\"fb-root\"></div><script> window.fbAsyncInit = function() {
	FB.init({ appId      : '".$this->AppID."', channelUrl : '//".$channel."', status     : true, cookie     : true, xfbml      : ".$pXML." });};	  
  (function(d){ var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;} js = d.createElement('script'); js.id = id; js.async = true; js.src = \"//connect.facebook.net/en_US/all.js\"; d.getElementsByTagName('head')[0].appendChild(js); }(document)); </script>";	
		
		if ($return)
			return $data;
		else
			print $data;
	}	
	
	private function serveAutoChannel() {
		@session_start();
		$_SESSION['eeArjs']=$this->ee->aArray;
		$_SESSION['eeCfgA']=$this->ee->cArray;
		$mePath = $this->ee->meGetResPath("fbjssdk","http");
		return $mePath."channel.php";
	}
	
	function serveChannel() {
		$cache_expire = 60*60*24*365;
		header("Pragma: public");
		header("Cache-Control: max-age=".$cache_expire);
		header('Expires: ' . gmdate('D, d M Y H:i:s', time()+$cache_expire) . ' GMT');		
		print '<script src="//connect.facebook.net/en_US/all.js"></script>';
	}	
}
?>