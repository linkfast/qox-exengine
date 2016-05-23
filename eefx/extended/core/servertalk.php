<?php

# ExEngine 7 / Extended Libs / LinkFast ServerTalk!

// incomplete, pending final decision.
class servertalk_server {

	private $ee;
	private $eedbm;
	
	const VERSION = "1.0.0.0";	
	private $__cfg;
	
	function __construct($parent,$cfg) {
		$this->ee = &$parent;
		$this->eedbm = &$eedbmObj;
		
		if (is_array($cfg)) {
			if (array_key_exists("CLIENT_KEY",$cfg)) {
				$this->__cfg = $cfg;
			} else
				$this->ee->errorExit("ServerTalk!","Invalid configuration array.");
		} else {
			$this->ee->errorExit("ServerTalk!","Configuration variable is not an array.");
		}
	}
	
	// TO-DO
	function checkDatabase() {
		$db = &$this->eedbm;
		$dbPrefix = $this->__cfg["dbPrefix"];
		
		$db->open();
		$q = $db->query("SELECT * FROM ");
	}
	
	function clientAuth() {
		$args = $this->ee->httpMixedArgs();
		if ($args["CK"] == $this->__cfg["CLIENT_KEY"]) {
			return true;
		}
	}
}

class servertalk_client {
	private $ee;
	private $serverAddress;
	private $serverPort;
	private $serverKey;
	private $mode;
	
	function __construct($parent,$server,$port=81,$inMode="GET",$authkey=0) {
		$parent->debugThis("LinkFast ServerTalk","ST Object Created, Settings: ".$server.":".$port.", Method: ".$inMode.", AuthKey: ".$authkey);
		$this->ee = &$parent;
		$this->serverAddress = $server;	
		$this->serverPort = $port;
		$this->mode = $inMode;
		if ($authkey != 0) {
			$this->serverKey = $authkey; 	
		}
	}
	
	private function CreateST_uri($qStr) {
		//$qStr = urlencode($qStr);
		if ($this->mode != "POST") {
			if ($this->serverKey != 0) {
				return "http://".$this->serverAddress."/?sK=".$this->serverKey."&".$qStr;
			} else {			
				return "http://".$this->serverAddress."/?".$qStr;	
			}
		} else {
			return "http://".$this->serverAddress."/";
		}
	}
	
	function checkServer() {
		$curi="http://".$this->serverAddress."/";
		$port = $this->serverPort;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_PORT , $port);
		curl_setopt($ch, CURLOPT_URL,$curi);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT , 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$r = curl_exec($ch);
		curl_close($ch);
		if ($r) {
			return true;
		} else {
			return false;
		}
	}
	
	function Ping($ping)
	{
		set_time_limit(1);
		if ($this->ee->osCheck() == "linux") {
			$comm = "ping -c1 -W1 ".$ping;
		} else if ($this->ee->osCheck() == "windows") {
			$comm = "ping -n 1 -w 100 ".$ping;
		}
		$output=shell_exec($comm);		
		if ($this->ee->strContains($output,"ttl") || $this->ee->strContains($output,"TTL")) {
			return true;	
		} else {
			return false;
		}		
	}  
	
	function Talk($queryString) {
		//LinkFast UltraProxy
		$curl_uri = $this->CreateST_uri($queryString);
		$ch = curl_init();
		$port = $this->serverPort;
		curl_setopt($ch, CURLOPT_PORT , $port);
		curl_setopt($ch, CURLOPT_URL,$curl_uri);
		if ($this->mode == "POST") {
			curl_setopt($ch, CURLOPT_POST, 1);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $queryString);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT , 300);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$result = curl_exec($ch);
		curl_close($ch);
		$result = str_replace("Server: LinkFast ServerTalk\n","",$result);
		$result = str_replace("Server-Status: OK\n","",$result);
		return $result;
	}
}