<?php

# ExEngine 7 / Extended Libs / Common Update System

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

// UPDATE OF THIS LIB IS REQUIRED, COMUPS IS GETTING A BIG UPDATE, THIS LIB WILL WAIT THAT.

# Comups for ExEngine is based in Comups Class for PHP.

class comups
{
	# EE7 Style:
	private $ee;	
	
	const VERSION = "1.0.0.4";
	const serverVERrq = "";

	private $serverHST ;
	private $serverCOM ;
	private $connected=false ;
	private $lastError;
	private $serverInfo;	

	const milicDS = "<@@MILIC_DUMP_START@@>";
	const milicDE = "<@@MILIC_DUMP_END@@>";
	const milicSP = "$$@@MCSVR@@$$";
	const milicIV = "INVALID PACKAGE";
	const secMesg = "<@@SECURE_KEY_NEEDED@@>";
	

	function __construct($parent,$inServerHost) {
		
		$this->ee = $parent;
		
		$this->serverHST = $inServerHost;
		$com = self::comGet($inServerHost);
		if ($com !== false) {
			$this->serverCOM = $com;
			$this->connected = true;
		} else {
			$this->serverCOM = false;
			$this->connected = false;
		}
	}	

	function isConnected() {
		return $this->connected;	
	}

	function getLastError() {
		return $this->lastError;
	}

	function comGet($host) {	
			$milicCom = @file_get_contents("http://" . $host . "/milic.htm");		

			if ($milicCom===false) {
				$this->lastError = "Cannot connect, connection refused.";
				return false; 
			} else {
				$milicCom = str_replace(" ","",$milicCom);
				$milicCom = str_replace("\n","",$milicCom);
				return $milicCom."com.php";					
			}
	}

	
	function getServerInfo() {
		$cmd = "?serverinfo=get";
		$allInfo = @file_get_contents($this->serverCOM.$cmd);
		$lines = explode("\n",$allInfo);
		$l=0;
		while ($l < count($lines)) {
			if ($lines[$l] != self::milicDS && $lines[$l] != self::milicDE && $lines[$l] != self::milicIV) {
					$packInfo = explode(self::milicSP,base64_decode($lines[$l]));
			}
			if ($lines[$l] == self::milicIV) {
				$packInfo = self::milicIV;
			}
			$l++;
		}
		return $packInfo;
	}
	
	function versionCompare($localVersion,$packUID,$operator="<") {
		$command = "?getVersionByKey=".$packUID;
		$versionInfo = file_get_contents($this->serverCOM.$command);		
		$ultimaVer = self::oneResultParser($versionInfo);
		return version_compare($localVersion,$ultimaVer,$operator);
	}
	function checkProtected($input) {
		if ($input == self::secMesg) {
			return true;
		} else {
			return false;
		}
	}

	function getUriPkg($packUID,$secKey="") {
		$cmd = "?getInfoByKey=".$packUID."&secureKey=".$secKey;
		$allInfo = file_get_contents($this->serverCOM.$cmd);
		$lines = explode("\n",$allInfo);
		$l=0;
		while ($l < count($lines)) {
			if (($lines[$l] != self::milicDS) && ($lines[$l] != self::milicDE) && ($lines[$l] != self::milicIV)) {
					$packInfo = explode(self::milicSP,base64_decode($lines[$l]));
					$outPut = $packInfo[5];

			}
			if ($lines[$l] == self::milicIV) {
				$outPut = self::milicIV;
			}
			$l++;
		}
		return $outPut;
	}	

	function getVersion($packUID, $secKey="") {
			$cmd =  "?getVersionByKey=".$packUID."&secureKey=".$secKey;
			$packVerInfo = file_get_contents($this->serverCOM.$cmd);
			return self::oneResultParser($packVerInfo);
	}
	function oneResultParser($input) {
		$strs = explode("\n",$input);
		if (!self::checkProtected($input)) {
			return base64_decode($strs[1]);
		} else {
			return "PROTECTEDPACKAGE";
		}
	}

	// UPDATER 
	/*
	function showUpdater($package,$currVer,$noIframeSupport="Your internet browser does not support iframes.<br/><br/>El Exporador de internet que usted est&aacute; usando no soporta iframes, porfavor actualize.") {
		global $exen_MixedEngines;
			?>
            <iframe id="ComupsUpdater/1.0" height="155" width="300" scrolling="auto" style="border:thin;" src="<? print $exen_mixedEngines["comups"]["httpPath"]."?cmd=showUpdater&pack=".$package; ?>&exLoc=<? print exen_exEngineLocation(); ?>&currV=<? print $currVer; ?>&updHost=<? print $this->serverHST; ?>">
            <? print $noIframeSupport; ?>
            </iframe>
            <?
	}
	*/
}