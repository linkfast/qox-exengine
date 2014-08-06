<?php
/**
@file devguard.php
@author Giancarlo Chiappe <gch@linkfastsa.com> <gchiappe@gmail.com>
@version 0.0.1.4

@section LICENSE

ExEngine is free software; you can redistribute it and/or modify it under the
terms of the GNU Lesser Gereral Public Licence as published by the Free Software
Foundation; either version 2 of the Licence, or (at your opinion) any later version.
ExEngine is distributed in the hope that it will be usefull, but WITHOUT ANY WARRANTY;
without even the implied warranty of merchantability or fitness for a particular purpose.
See the GNU Lesser General Public Licence for more details.

You should have received a copy of the GNU Lesser General Public Licence along with ExEngine;
if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, Ma 02111-1307 USA.

@section DESCRIPTION

ExEngine 7 / Libs / ExEngine Development Stage Guard (devguard)

 */

class ee_devguard {
	const VERSION = "0.0.1.5";
	private $ee;
	var $password = "default";
	var $lang = "en";
	function __construct() {
		$this->ee =& ee_gi();
		if (!function_exists('mcrypt_encrypt')){
			$this->ee->errorExit("ExEngine DevGuard Error", "php mcrypt library is required, please install before using DevGuard.");
		}
		if (isset($this->ee->cArray["devguard_default_language"])) {
			$this->lang = $this->ee->cArray["devguard_default_language"];
		}
	}

	function guard($Server_Key_Name) {

		if(strlen(session_id()) == 0) {
			if (! eemvc_get_index_instance() ) {
				session_start();
			} else {
				$index_obj = eemvc_get_index_instance();
				if (!$index_obj->SessionMode || strlen(session_id()) == 0) {
					$this->ee->errorExit("ExEngine DevGuard Error", "Session support in MVC-ExEngine is required.");
				}
			}
		}
		if (isset($_SESSION['DG_SA']) && $_SESSION['DG_SA']) {
			$this->guard_end();
		} else
		{
			$this->loadGateway($Server_Key_Name);
		}
	}

	private function loadGateway($Server_Key_Name,$CustomGateway=false) {
		$dgf = $this->ee->libGetResPath("devguard");
		$langFile = $dgf . "lang/" . $this->lang . ".php";
		if (file_exists($langFile)) {
			include_once($langFile);
		} else {
			$DG_L["gateway_title"] = "Application protected by QOX ExEngine DevGuard";
			$DG_L["welcome_text"] = 'This application is in <span style="font-weight: 500;">development stage</span> and its access is restricted to select users, if you need access contact application developers to get a copy of the key file.';
			$DG_L["select_key"] = "Please select the project key to start a session";
			$DG_L["auth_button"] = "AUTHENTICATE";
			$DG_L["wrong_file"] = "Invalid or wrong file provided, please select the correct key file.";
			$DG_L["no_key_sent"] = "No file send, please select a file.";

			$DG_L["click_close"] = "Click here to close QOX ExEngine DevGuard session.";
		}

		$invalid_key = null;
		//print $_FILES["upload_file"]['error'] ;
		if (isset($_FILES["upload_file"]) && $_FILES["upload_file"]['error'] == 0) {
			$val = $this->guard_decrypt($Server_Key_Name);
			if ($val) {
				$_SESSION['DG_SA'] = true;
				$sU = str_replace("close_guard", "", $_SERVER["REQUEST_URI"]);
				if (substr($sU, -1) == "&" || substr($sU, -1) == "?") {
					$sU = substr($sU,0,strlen($sU)-1);
				}
				header("Location: ".$sU);
				exit();
			} else {
				$invalid_key = $DG_L["wrong_file"];
			}
		}
		if (isset($_FILES["upload_file"]) && $_FILES["upload_file"]['error'] == 4) {
			$invalid_key = $DG_L["no_key_sent"];
		}

		if (!$CustomGateway)
			include_once( $dgf . 'gateway.phtml');
		else
			if (file_exists($dgf . $CustomGateway))
				include_once( $dgf . $CustomGateway);
			else
				if (file_exists( $CustomGateway))
					include_once($CustomGateway);
				else
					include_once( $dgf . 'gateway.phtml');
		exit;
	}

	function guard_gen_keys($Server_Key_Name) {
		$string = hash("sha512", rand());
		$key = $this->password;
		$fname = $this->ee->cArray["devguard_keys_path"]."/".$Server_Key_Name.".dgsk";
		$enc_data = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key)));
		$sk_size = file_put_contents($fname, $enc_data, LOCK_EX);
		if (!$sk_size) {
			$this->ee->errorExit("ExEngine DevGuard Error", "Cannot write server Key file. ($fname).");
		}
		$enc_data_client = mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), md5_file($fname), MCRYPT_MODE_CBC, md5(md5($key)));
		#file download
		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename="clientKey_'.$Server_Key_Name.'.dgck"');
		header('Content-Transfer-Encoding: binary');
		header('Connection: Keep-Alive');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . strlen($enc_data_client));
		print $enc_data_client;
		exit();
	}

	function guard_decrypt($Server_Key_Name) {
		if ($_FILES["upload_file"]["error"] > 0) {
			$this->ee->errorExit("ExEngine DevGuard Error", "Upload Error. :(");
		}
		$fname = $this->ee->cArray["devguard_keys_path"]."/".$Server_Key_Name.".dgsk";
		if (file_exists($fname)) {
			clearstatcache();
			$data = file_get_contents($_FILES["upload_file"]["tmp_name"]);
			$key = $this->password;
			$data = mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), $data, MCRYPT_MODE_CBC, md5(md5($key)));
			$sk_size = filesize($fname);
			if (!$sk_size) {
				$this->ee->errorExit("ExEngine DevGuard Error", "Cannot open server Key file. ($fname).");
			}
			if (md5_file($fname) == $data) return true; else return false;
		} else {
			$this->ee->errorExit("ExEngine DevGuard Error", "Cannot open server Key file. ($fname).");
		}
	}

	function guard_end() {
		if (isset($_GET['close_guard'])) {
			unset($_SESSION['DG_SA']);
			header("Location: ".$_SERVER["REQUEST_URI"]);
		}
	}

	function guard_float_menu($return=false) {
		$dgf = $this->ee->libGetResPath("devguard");
		$langFile = $dgf . "lang/" . $this->lang . ".php";
		if (file_exists($langFile)) {
			include_once($langFile);
		} else {
			$DG_L["click_close"] = "Click here to close QOX ExEngine DevGuard session.";
		}
		if (!$return) {
			include_once( $dgf . 'dglock.phtml');
		} else {
			ob_start();
			include_once( $dgf . 'dglock.phtml');
			$R = ob_get_contents();
			ob_clean();
			return $R;
		}
	}

}

?>