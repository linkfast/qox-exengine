<?

# ExEngine 7 / ExEngine 6 Compatibility Layer / ExEngine 6 Core Emulator

/*
	This file is part of ExEngine7.

    ExEngine7 is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    Foobar is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with Foobar.  If not, see <http://www.gnu.org/licenses/>.
*/

#EE6CL Only emulates ExEngine up to 6.4.2.8, 6.4.3.0 is an alias for the UP2. If you want to use updated versions of EE6 you should use ForwardMode.
$exen_config["versionName"] = "ExEngine 7 / EE6CL / SiX Update Pack 2 / Release 0" ;
$exen_config["version"] = "6.4.3.0" ;
$exen_config["versionFull"] = $exen_config["versionName"]."/".$exen_config["version"];
$exen_config["releaseDate"] = "16/03/2010";
//TIME DATE LANGUAGES
global $exen_date_language;
$exen_date_language = array(
// MONTH => "name",
"01" => "Enero",
"02" => "Febrero",
"03" => "Marzo",
"04" => "Abril",
"05" => "Mayo",
"06" => "Junio",
"07" => "Julio",
"08" => "Agosto",
"09" => "Septiembre",
"10" => "Octubre",
"11" => "Noviembre",
"12" => "Dicciembre",
"ofm" => "de",
"ofy" => "del"
);
//EMBEDDED CODER/DECODER/RAND FORMULAS:
global $exen_coder_formulas;
$exen_coder_formulas["uex1"] = 'sha1(hash("sha512",hash("ripemd320",md5(sha1($in)))));' ;
$exen_coder_formulas["md320"] = 'hash("ripemd320",$in);' ;
$exen_coder_formulas["l2inside"] = 'md5(sha1(md5(base64_decode($in))).$in);' ;
$exen_coder_formulas["sha1"] = 'sha1($in);' ;
$exen_coder_formulas["md5"] = 'md5($in);' ;
$exen_coder_formulas["reg"] = '((($in*5)-4)+2)*2;' ;
$exen_coder_formulas["securehtml"] = 'htmlspecialchars($in, ENT_QUOTES);' ;
global $exen_decoder_formulas;
$exen_decoder_formulas["securehtml"] = 'str_replace("'."\\\\".'", "", htmlspecialchars_decode($in,ENT_QUOTES)) ;' ;
$exen_decoder_formulas["reg"] = '((($in/2)-2)+4)/5;' ;
global $exen_rand_formulas;
$exen_rand_formulas["ckeditor"] = 'substr( md5( uniqid( microtime() ).rand().strftime("%H%M%S" ,time()) ),0,7 );';
$exen_rand_formulas["algorhm"] = 'sha1( uniqid( microtime() ).rand().strftime("%pl%df%ms%ya%H%M%Slfsa" ,time()) );';
// EXENGINE ERROR CODES:
global $exen_errorCodes;
$exen_errorCodes["XC01"] = "Invalid configuration file.";
$exen_errorCodes["XC02"] = "MixedEngine Loader Error";
$exen_errorCodes["XC03"] = "ExEngine Version Incorrect, update required.";
$exen_errorCodes["XC04"] = "MixedEngine version Incorrect, update required.";
$exen_errorCodes["XC05"] = "MixedEngine specification incorrect.";
$exen_errorCodes["ME01"] = "MixedEngine loader incorrect.";
$exen_errorCodes["ME02"] = "MixedEngine loader incorrect.";
//
///////////////////////////////////////////////////////////////////////////////////////////////
// INIT :
$exen_config["memoryused"] = filesize(exen_core_fullpath());
////
function exen_charset($pagec){
	global $exen_config;
	if ($exen_config["silentmode"] != 1) {
		print "<!-- EXENGINE HEADER ERROR, TRY LOADING EXENGINE WITH THE SILENT ARGUMENT OR WITHOUT PRINTING ANYTHING BEFORE IT -->";
	} else {
		try {
			@header('Content-type: text/html; charset='.$pagec.'');	
		} catch (Exception $e) {
			print "<!-- EXENGINE HEADER ERROR, TRY LOADING EXENGINE WITH THE SILENT ARGUMENT OR WITHOUT PRINTING ANYTHING BEFORE IT -->";	
		}
	}
}
function exen_checkLatest() {
	global $exen_config;
	exen_load_engine("comups");
	$comupsConn = new comups("update.aldealinkfast.com");
	$x = $comupsConn->versionCompare($exen_config["version"],"exengine","<");
	return $x;
}
function exen_memoryused() {
	global $exen_config;
	return $exen_config["memoryused"];
}
function exen_rdhttp() {
	return exen_fwphttp()."resources/";
}
function exen_configcheck() {
	global $exen_config;
	global $exen_app_key;
	global $exen_extended;
	$error = 0;
	if (!file_exists($exen_config["app_path"]) || !$exen_app_key || !$exen_config["session_name"] || !exen_checkhttpaccess()) {
		if ($exen_extended["errorcontrol"] === true) {
			print '<div align="center">';
			exen_visualerror("XC01","ExEngine Core","Please check configuration file, may not be correct or has missing fields.<br/><br/>Cannot continue with application execution. ExEngine Core halted. Try ex_engine.php?cmd=testConfig command.",1);
			print '</div>';
		} else {
			print "<h1>ExEngine Core Error [XC01]</h1><br/><br/><b>Please check configuration file, may not be correct or has missing fields.</b><br/><br/>Cannot continue with application execution. ExEngine Core halted. Try ex_engine.php?cmd=testConfig command.";
		}
		$error++;
	}
	
	if ($error >= 1) {
		exit();
	}
}
function exen_enableExEn($exenName) {
	global $exen_extended;
	$exen_extended[$exenName] = exen_checkhttpaccess();
}
function exen_checkhttpaccess() {
		global $exen_config;
		$self_xengine = $exen_config["http_path"].$exen_config["ex_path"]."ex_engine_config.php";
		if (exen_rfile_exists($self_xengine)) {
			return true;
		} else {
			return false;	
		}
}
function exen_exEngineLocation() {
	global $exen_config;
	return $exen_config["app_path"].$exen_config["ex_path"]."ex_engine.php";	
}
function exen_rfile_exists($uri) {
		if (@fopen($uri, "r")) {
			return true;
		} else {
			return false;	
		}
}
function exen_checkversion($val) {
	//Legacy function to be deleted.
	global $engine_alias;
	if ($engine_alias) {
		$addMsg = "The call of this functions was made by a MixedEngine v1 (v2 MixedEngines usually does not use this function).<br/>MixedEngine Alias : ".$engine_alias;
	}
	exen_exitMessage("ExEngine Core Error [DF]","exen_checkversion($version) is a deprecated function and will not work with this version of ExEngine, please modify your MixedEngine or Application Code as necessary.<br/><br/>".$addMsg);
}
function exen_compareCoreVersion($rqVer) {
	global $exen_config;
	return version_compare($exen_config["version"],$val,">=");	
}
function exen_rqCoreVersion($rqVer,$mess) {
	global $exen_config;
	$m = version_compare($exen_config["version"],$rqVer,">=");	
	if (!$m) {
		exen_exitMessage("ExEngine Version Error [RQ]",$mess);
	}
}
function exen_exitMessage($title,$mess) {
	print "<h1>".$title."</h1><br/><br/><b>".$mess." ExEngine Core halted.";
	exit();
	
}
function exen_checkengine($engine="null") {
	global $exen_internal_loadedengines;
	return array_key_exists($engine, $exen_internal_loadedengines);
}
function exen_checkvalidurl($url="null") {
	$checkurl = strpos($url, "http://");
	if( $checkurl === false) {
		return false ;
	} else {
		return true ;
	} 
}
function exen_core_fullpath() {
	global $exen_config;
	return __FILE__;
}
function exen_enginespath() {
	global $exen_config;
	return $exen_config["http_path"].$exen_config["ex_path"]."ex_framework/engines/" ;
}
function exen_fwphttp() {
	global $exen_config;
	return $exen_config["http_path"].$exen_config["ex_path"]."ex_framework/" ;
}
function exen_fwpath() {
	global $exen_config;
	return $exen_config["app_path"].$exen_config["ex_path"]."ex_framework/";
}
function exen_coder($type,$in) {
	global $exen_coder_formulas;
	if (array_key_exists($type, $exen_coder_formulas)) {
		$ret = @eval("return ".$exen_coder_formulas[$type]) ;
		if ($ret !== false) {
			return $ret;
		} else {
			return "Coder formula malformed.";
		}
	} else {
		return "Coder algorithm not found." ;
	}
}
function exen_rand($type) {
	global $exen_rand_formulas;
	if (array_key_exists($type, $exen_rand_formulas)) {
		$ret = @eval("return ".$exen_rand_formulas[$type]) ;
		if ($ret !== false) {
			return $ret;
		} else {
			return "Rand formula malformed.";
		}
	} else {
		return "Rand algorithm not found." ;
	}
}
function exen_decoder($type,$in) {
	global $exen_decoder_formulas;
	if (array_key_exists($type, $exen_decoder_formulas)) {
		$ret = @eval("return ".$exen_decoder_formulas[$type]) ;
		if ($ret !== false) {
			return $ret;
		} else {
			return "Deoder formula malformed.";
		}
	} else {
		return "Decoder algorithm not found." ;
	}
}
function exen_stro_replace($search, $replace, $subject)
{
    return strtr( $subject, array_combine($search, $replace) );
}
function exen_linec($input) {
	$order   = array("\r\n", "\n", "\r");
	$replace = array("<br />\n","<br />\n","<br />\n");
	return exen_stro_replace($order, $replace, $input);
}
function exen_nohtml($input) {
	$order   = array("<", ">","&nbsp;");
	$replace = array("&lt;", "&gt;");
	return exen_stro_replace($order, $replace, $input);
}
function exen_dlfile($url,$dloc) {
	$content = file_get_contents($url);
	$dir = dirname($dloc);
	$fp = fopen($dloc, 'w');
	fwrite($fp, $content);
	fclose($fp);
}
function exen_samepageuri() {
	global $exen_config;
	return $exen_config["http_domain"].substr($_SERVER['REQUEST_URI'], 1);	
}
function exen_sps_script() {
	global $exen_config;
	$spage = $exen_config["app_spath"].substr($_SERVER['REQUEST_URI'], 1);
	return filesize($spage);
}
function exen_sps_http_ajaxcp() {
	if ($_GET['EXEN_NOPSIZE'] == "N") {
		return true;	
	} else {
		return false;
	}
}
function exen_sps_http() {
	global $exen_config;
	if (!$_GET['EXEN_NOPSIZE'] == "N") {
		$spage = $exen_config["http_domain"].substr($_SERVER['REQUEST_URI'], 1);
		$tmpfile = exen_temp_file();
		if (strrpos($spage, ".php?") === false) {
				$spage = $spage."?EXEN_NOPSIZE=N";	
		} else {
			$spage = $spage."&EXEN_NOPSIZE=N";
		}
		//print $spage;
		exen_dlfile($spage,$tmpfile)	;
		$str = filesize($tmpfile);
		unlink($tmpfile);
		return $str + strlen($str);
	}
}
function exen_sp_aGetArgs($cmds) {
		global $exen_config;
		
		if (strrpos($cmds, "?") === false) {
			$spage = $exen_config["http_domain"].substr($_SERVER['REQUEST_URI'], 1);
			if (strrpos($spage, $cmds) === false) {
				if (strrpos($spage, "?") === false) {
						$spage = $spage."?".$cmds;	
				} else {
					$spage = $spage."&".$cmds;
				}
			}				
			return $spage;
		} else {
			if (!exen_silentmode()) {
				print "<!-- <!> exen_sp_aGetArgs() Function error <!> ==> First argument must not contain '?' char.
				Use function this way :
				$newFullUrl = exen_sp_aGetArgs('arg1=abcd&arg2=abc32&arg3=mgggg');
				<!> --- <!> -->";	
			}
			return false;
		}
}
function exen_codehl($theData) {
	return exen_no_nbsp(highlight_string($theData,true));
}
function exen_no_nbsp($input) {
	return str_replace("&nbsp;", " ", $input);
}
//LEGACY
if (isset($_GET['cmd'])) {
	if ($_GET['cmd'] == "img.reg.create") {
		print "This is function is no longer supported.";
	}
}
//LEGACY
/* ----- Time and date funtions -----------------------*/
function exen_timedate($typ) {
global $exen_date_language ;
global $exen_config;
	date_default_timezone_set($exen_config["timezone"]);
if ($typ == "time") {
	$date_x = strftime("%I:%M %p" ,time());
} elseif ($typ == "date") {
	$date_x = strftime("%d/%m/%y" ,time());
} elseif ($typ == "timedate") {
	$date_x = strftime("%d/%m/%y %I:%M %p" ,time());
} elseif ($typ == "reg") {
	$date_x = strftime("%H%M%S" ,time());
} elseif ($typ == "filecreation") {
	$date_x = strftime("%d%m%y%H%M%S" ,time());
} elseif ($typ == "date_lang") {
	$temp_day = strftime("%d" ,time());
	$temp_month = strftime("%m" ,time());
	//print $temp_month;
	$temp_year = strftime("%Y" ,time());
	$date_x = $temp_day." ".$exen_date_language["ofm"]." ".$exen_date_language[$temp_month]." ".$exen_date_language["ofy"]." ".$temp_year ; 
} elseif ($typ == "time_seg") {
	$date_x = strftime("%H : %M : %S" ,time());
}
return $date_x ;
}
function exen_get_config($property="null") {
	global $exen_config;
	if ($property != "null") {
		return $exen_config[$property];
	} else {
		return "null";
	}
}
function exen_silentmode() {
	global $exen_config;
	if ($exen_config["silentmode"] == 1) {
		return true ;
	} else {
		return false ;
	}
}
function exen_date($a) {
	if ($a == "day") {
		$b = $date_x = strftime("%d" ,time());
	}
	if ($a == "month") {
		$b = $date_x = strftime("%m" ,time());
	}
	if ($a == "year") {
		$b = $date_x = strftime("%Y" ,time());
	}
	if ($a == "yearm") {
		$b = $date_x = strftime("%y" ,time());
	}
	return $b ;
}
/* --- END OF DATE RETURNER --------------------*/
/* -------- Base 64 Functions --------------------*/
// BASE 64 DECODER
function exen_b64d($string) {
	return base64_decode($string);
}
// BASE 64 ENCODER
function exen_b64e($string) {
	return base64_encode($string);
}
/* ------------- Base 64 Funcionts END ------------*/
/* ------------- LOG FUNCTIONS --------------------------*/
function exen_tolog($app='',$msg='') {
	global $ex_config ;
	exen_database("silent");
	if ((!$app)||(!msg)) {
		if ($ex_config['silentmode'] != "1") {
			print "Ha ocurrido un error en funcion ex_engine.tolog($app,$msg), por favor revise su sintaxis";
		}
	} else {
			$log_fecha = timedate("date")."  ".timedate("time_seg");
			$log_texto = $msg ;
			$query = "insert into log (fechahora,aplicacion,texto) values ('$log_fecha','$app','$log_texto')";
			$result = exen_dbquery($query) or die('Query failed: ' . exen_dberror());	
	}
}
/* ------------ LOG FUNCTIONS END ---------------------- */
// PHPINFO COMMAND
if (isset($_GET['cmd'])) {
	if ($_GET['cmd'] == "phpinfo") {	
		phpinfo();	
	}
}
//
// GD FUNCTIONS :
// CREATE THUMBNAIL (Supports JPG & PNG)
function exen_gd_resize($source,$output,$new_w="150",$new_h="150"){
	global $exen_config ;
	$name = $source;
	$filename = $output;
	$filename_temp = $exen_config["app_path"].$exen_config["ex_path"]."ex_framework/temp/".exen_timedate("filecreation");
	
	$system=explode('.',$name);
	
	if (preg_match('/jpg|jpeg/',$system[1])){
		copy($name, $filename_temp."tmp.jpg");
		$src_img=imagecreatefromjpeg($filename_temp."tmp.jpg");	
		unlink($filename_temp."tmp.jpg");
	}
	if (preg_match('/png/',$system[1])){		
		copy($name, $filename_temp."tmp.png");
		$src_img=imagecreatefrompng($filename_temp."tmp.png");
		unlink($filename_temp."tmp.jpg");
	}
	
	$old_x=imagesx($src_img);
	$old_y=imagesy($src_img);
	if ($old_x > $old_y) {
		$thumb_w=$new_w;
		$thumb_h=$old_y*($new_h/$old_x);
	}
	if ($old_x < $old_y) {
		$thumb_w=$old_x*($new_w/$old_y);
		$thumb_h=$new_h;
	}
	if ($old_x == $old_y) {
		$thumb_w=$new_w;
		$thumb_h=$new_h;
	}
	$dst_img=imagecreatetruecolor($thumb_w,$thumb_h);
	imagecopyresampled($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_x,$old_y); 
	if (preg_match("/png/",$system[1]))
	{
		imagepng($dst_img,$filename); 
	} else {
		imagejpeg($dst_img,$filename); 
	}
	//imagedestroy($dst_img); 
	//imagedestroy($src_img); 
	return $output;
}
function exen_engine_cv($engine,$minver) {
	global $exen_config;
	$exen_loader = 1;
	include_once($exen_config["app_path"].$exen_config["ex_path"]."ex_framework/engines/".$engine.".php");
	if ($engine_version >= $minver) {
		return true ;
	} else {
		return false ;
	}
}
//Only MixedEngine v1 Applicable
function exen_engine_getinfo_val($val){
	global $exen_config;
	$exen_loader = 1;
	include_once($exen_config["app_path"].$exen_config["ex_path"]."ex_framework/engines/".$engine.".php");
	return $engine_info[$val];
}
////
function exen_load_extended($file) {
	global $exen_http_path ;
	global $exen_app_path ;
	global $exen_path ;
	global $exen_database ;
	global $exen_silentmode ;
	global $exen_session ;
	global $exen_usession ;
	global $exen_date_language ;
	global $exen_app_key ;
	global $exen_config ;
	global $exen_loader;
	global $exen_internal_loadedengines;
	include(exen_extendedpath().$file) ;	
	$exen_config["memoryused"] += filesize(exen_extendedpath().$file);
}
// Only MixedEngines v3 Applicable | Part of ME v3 Loader Beta 14
function exen_checkIME($array,$app) {
	global $exen_config;
	global $exen_mixedEngines;
	global $exen_MixedEngine;
	$rqengines = array_keys($array);
	$rqengines_ver = $array;
	$libAlias = $exen_mixedEngines[$app][0];
	$engineVersion = $exen_mixedEngines[$app][1];
	
	if (isset($exen_mixedEngines[$app][2])) {
		$engineName = $exen_mixedEngines[$app][2];
	} else {
		$engineName = $libAlias;
	}	
	$a=0;
	while($a < count ($rqengines)) {
		//INSTALL CHECK
		if (!exen_engine_installed($rqengines[$a]) && $rqengines[$a] != "Core") {
			print "<h1>ExEngine Core Error [XC02]</h1><br/><br/><b>Mixed Engine loader cannot load ".$engineName."[".$libAlias."]"." engine</b>, '".$rqengines[$a]."' is missing in the MixedEngines directory. ExEngine Core halted.";
			exit();
		}		
		//VERSION CHECK
		$versiones[$a] = $array[$rqengines[$a]];
		if ($rqengines[$a] != "Core") {			
			$MEversion = exen_ME_version($rqengines[$a]);
			
			if ($MEversion == 3) {
				exen_load_ME($rqengines[$a]);
				$engineVersion = $exen_mixedEngines[$rqengines[$a]][1];
			} 
			//TO REMOVE ON AJAXIS .START.
			elseif ($MEversion == 2) {
				
				exen_load_engine($rqengines[$a]);
				$engineVersion = $exen_MixedEngine[$rqengines[$a]]["version"] ;
			} 
			//TO REMOVE ON AJAXIS .END.
			else {
				exen_exitMessage("ExEngine Core Error [XC05]","Only MixedEngines v2 and v3 can be checked using the rqEngines property. MixedEngine v1 : ".$rqengines[$a]." cannot be checked. Error when trying to load ".$engineName."[".$libAlias."]");
			}
					
			if (version_compare($engineVersion,$versiones[$a],"<")) {
					exen_exitMessage("ExEngine Core Error [XC04]","Cannot load '".$rqengines[$a]."' version is incorrect. Error occured when trying to load ".$engineName."[".$libAlias."]");
			}
		} else {
			if (!exen_compareCoreVersion($versiones[$a])) {
				$verCheck = exen_checkLatest();
				if ($verCheck) {
					$mess = 'A newer version of ExEngine is available at <a href="http://www.linkfastsa.com/?pid=dgs&psid=exengine" target="_blank">ExEngine Home</a>.';
				}
				exen_exitMessage("ExEngine Core Error [XC03]","A more recent version of ExEngine is required by ".$engineName."[".$libAlias."]");
			}
		}
		$a++;
	}			
}
function exen_ME_version($file) {
	global $exen_config;	
	$iP = $exen_config["app_path"].$exen_config["ex_path"]."ex_framework/engines/".$file.".php";
	$iPH = $exen_config["http_path"].$exen_config["ex_path"]."ex_framework/engines/".$file.".php";	
	if (file_exists($iP)) {
		$me = implode('', file($iP));
	} elseif (file_exists($file)) {
		$me = implode('', file($file));
	} else {
		print "File not Found";
	}
	$mev1 = preg_match("/engine_alias/i", $me);
	//TO REMOVE ON AJAXIS .START.
	$mev2 = preg_match("/exen_MixedEngineLoader/i", $me);
	//TO REMOVE ON AJAXIS .END.
	$mev3 = preg_match("/meProperties/i", $me);	
	if ($mev1) {
		return 1;
	} else {
		//TO REMOVE ON AJAXIS .START.
		if ($mev2) {
			return 2;
		} else {
		//TO REMOVE ON AJAXIS .END.
			if ($mev3) {
				return 3;
			} else {
				if (!$mev1 && !$mev2 && !$mev3) {
					return 0;	
				}
			}
		//TO REMOVE ON AJAXIS .START.
		}
		//TO REMOVE ON AJAXIS .END.
	}
}
// Only MixedEngines v3 Applicable (MixedEngines v3 Loader)
function exen_load_ME($me) {
	//MixedEngines v3 Loader Beta 16
	//CHECK :
	if (exen_ME_version($me) != 3 && exen_ME_version($me) != 0) {
		$eV = exen_ME_version($me);
		exen_exitMessage("ExEngine Core Error [ME01]","MixedEngines Loader v3 requieres a V3 MixedEngine.<br/>If you want to load a MixedEngine v1 or v2, please use exen_load_engine. ".$me." is a MixedEngine V".$eV." file.");
		}
	global $exen_mixedEngines;
	global $exen_config;
	$iP = $exen_config["app_path"].$exen_config["ex_path"]."ex_framework/engines/".$me.".php";
	$iPH = $exen_config["http_path"].$exen_config["ex_path"]."ex_framework/engines/".$me.".php";
	if (file_exists($iP)) {
		include_once($iP);
		$exen_mixedEngines[$me]["appPath"] = $iP;
		$exen_mixedEngines[$me]["httpPath"] = $iPH;
		$exen_mixedEngines[$me] = explode("|",$meProperties);
		if (isset($exen_mixedEngines[$me][2])) {
			$libName = $exen_mixedEngines[$me][2];
			exen_checkIME($meRequirements,$exen_mixedEngines[$me][0]);
		} else {
			$libName = $exen_mixedEngines[$me][0];
			exen_checkIME($meRequirements,"MixedEnginev3 - ".$exen_mixedEngines[$me][0]);
		}
		if (!$exen_config["silentmode"]) {
			print "<!-- ".$libName." Loaded Successfully -->\n";
		}
	} elseif (file_exists($me)) {
		include_once($me);
		$exen_mixedEngines[$me]["appPath"] = $me;
		$exen_mixedEngines[$me] = explode("|",$meProperties);
		if (isset($exen_mixedEngines[$me][2])) {
			$libName = $exen_mixedEngines[$me][2];
			exen_checkIME($meRequirements,$exen_mixedEngines[$me][0]);
		} else {
			$libName = $exen_mixedEngines[$me][0];
			exen_checkIME($meRequirements,"MixedEnginev3 - ".$exen_mixedEngines[$me][0]);
		}
		if (!$exen_config["silentmode"]) {
			print "<!-- ".$libName." Loaded Successfully -->\n";
		}
	} else {
		if (!$exen_config["silentmode"]) {
			print "<!-- ExEngine MixedEngine Loader Error : ".$me." not found -->\n";
		}
	}
}
// Only MixedEngines v2 Applicable
function exen_load_engine($exen_load_engine_loc) {
	
	// MixedEngines v1 / v2 Loader
	//CHECK
	if (exen_ME_version($exen_load_engine_loc) == 3) {
			exen_exitMessage("ExEngine Core Error [ME02]","MixedEngines Loader requieres a V1/V2 MixedEngine.<br/>If you want to load a MixedEngine v3, please use exen_load_ME. Error when trying to load ".$exen_load_engine_loc);
		}
	
	
	global $exen_database ;	
	global $exen_config ;
	global $exen_loader ;
	
	// REQUIRED BY MixedEngines v1
	global $exen_http_path ;
	global $exen_app_path ;
	global $exen_path ;
	global $exen_session ;
	global $exen_usession ;
	global $exen_date_language ;
	global $exen_silentmode ;
	global $exen_app_key ;
	$exen_version = $exen_config["version"];
	
	//ENGINE LOADER VARS:
	global $exen_internal_loadedengines;
	global $exen_load_engine_locx;	
	global $exen_engine_loc;
	
	//REQUIRED BY SOME COMPATIBILITY FUNCTIONS
	global $engine_alias;
	
	$exengine_present = true;
	
	if (array_key_exists($exen_load_engine_loc, $exen_internal_loadedengines)) {
		if ($exen_config['silentmode'] != "1") {
			print "<!-- <!>ExEngine : Engine ".$engine_name." is already loaded. <!> -->\n";
		}
	} else {	
	// INSTALLED EXENGINES
	$engineLocEXP = $exen_app_path.$exen_path."ex_framework/engines/".$exen_load_engine_loc.".php";
	$engineLocEXPH = $exen_config["http_path"].$exen_config["ex_path"]."ex_framework/engines/".$exen_load_engine_loc.".php";
		if (file_exists($engineLocEXP)) {
			$exen_loader = 1;	
			$exen_load_engine_locx = $engineLocEXP;
			include_once($engineLocEXP) ;	
			//Check4Engines ==MIXED ENGINE v2==:
			$engine_Temp_alias = $exen_MixedEngineLoader["alias"];
			if ($engine_Temp_alias) {
				$rqengines = array_keys($exen_MixedEngine[$engine_Temp_alias]["rqEngines"]);
				$exen_MixedEngine[$engine_Temp_alias]["httpLocation"] = $engineLocEXPH;
				$exen_MixedEngine[$engine_Temp_alias]["appLocation"] = $engineLocEXP;
				$rqengines_ver = $exen_MixedEngine[$engine_Temp_alias];
				$c=0;
				while ($c < count($rqengines)) {
						if (!exen_engine_installed($rqengines[$c]) && $rqengines[$c] != "Core") {
										print "<h1>ExEngine Core Error [XC02]</h1><br/><br/><b>Mixed Engine loader cannot load ".$exen_MixedEngine[$engine_Temp_alias]["name"]." engine</b>, '".$rqengines[$c]."' is missing in the Mixed Engines directory. ExEngine Core halted.";
										exit();
						}
						$c++;
				}
				$engine_name = $exen_MixedEngine[$engine_Temp_alias]["name"];
				$engine_author = $exen_MixedEngine[$engine_Temp_alias]["author"];
				$engine_protected = $exen_MixedEngine[$engine_Temp_alias]["protected"];
			}		
			//	
			
			//CheckEnginesVersion
			if ($engine_Temp_alias) {
				$rqengines = array_keys($exen_MixedEngine[$engine_Temp_alias]["rqEngines"]);
				$rqengines_ver = $exen_MixedEngine[$engine_Temp_alias];
				$a=0;
				while($a < count ($rqengines)) {
					$versiones[$a] = $exen_MixedEngine[$engine_Temp_alias]["rqEngines"][$rqengines[$a]];
					if ($rqengines[$a] != "Core") {						
						$vCengineLocEXP = $exen_app_path.$exen_path."ex_framework/engines/".$rqengines[$a].".php";
						print $vCengineLocEXP;
						exen_load_engine($vCengineLocEXP);
						$libAlias = $exen_MixedEngineLoader["alias"];
						$engineVersion = $exen_MixedEngine[$libAlias]["version"];
						if (!version_compare($engineVersion,$versiones[$a],">")) {
								exen_exitMessage("ExEngine Core Error [XC03]","Mixed Engine loader cannot load ".$exen_MixedEngine[$engine_Temp_alias]["name"]." engine</b>, '".$rqengines[$a]."' version is incorrect.");
						}
					} else {
						if (!exen_compareCoreVersion($versiones[$a])) {
							$verCheck = exen_checkLatest();
							if ($verCheck) {
								$mess = 'A newer version of ExEngine is available at <a href="http://www.linkfastsa.com/?pid=dgs&psid=exengine" target="_blank">ExEngine Home</a>.';
							}
							exen_exitMessage("ExEngine Core Error [XC03]","Mixed Engine loader cannot load ".$exen_MixedEngine[$engine_Temp_alias]["name"]." engine</b>, a more recent version of ExEngine is required. ".$mess);
						}
					}
					$a++;
				}				
			}	
			//
			$exen_config["memoryused"] += filesize($engineLocEXP);
			if ($exen_config['silentmode'] != "1") {
				
				print "<!-- <!> ExEngine : Mixed Engine ".$engine_name." was loaded successfully, rights reserved to ".$engine_author.". <!> -->\n";
				
			}
			$exen_internal_loadedengines[$exen_load_engine_loc] = 1;
			//EXTERNAL MIXEDENGINES
		} elseif (file_exists($exen_load_engine_loc)) {
			$exen_loader = 1;	
			$exen_load_engine_locx = $exen_load_engine_loc;
			include_once($exen_load_engine_loc) ;			
			//Check4Engines ==MIXED ENGINE v2==:
			$engine_Temp_alias = $exen_MixedEngineLoader["alias"];
			if ($engine_Temp_alias) {
				
				$exen_MixedEngine[$engine_Temp_alias]["appLocation"] = $exen_load_engine_loc;
				$rqengines = array_keys($exen_MixedEngine[$engine_Temp_alias]["rqEngines"]);
				$rqengines_ver = $exen_MixedEngine[$engine_Temp_alias];
				$c=0;
				while ($c < count($rqengines)) {
						if (!exen_engine_installed($rqengines[$c]) && $rqengines[$c] != "Core") {
										print "<h1>ExEngine Core Error [XC02]</h1><br/><br/><b>Mixed Engine loader cannot load ".$exen_MixedEngine[$engine_Temp_alias]["name"]." engine</b>, '".$rqengines[$c]."' is missing in the Mixed Engines directory. ExEngine Core halted.";
										exit();
						}
						$c++;
				}
				$engine_name = $exen_MixedEngine[$engine_Temp_alias]["name"];
				$engine_author = $exen_MixedEngine[$engine_Temp_alias]["author"];
				$engine_protected = $exen_MixedEngine[$engine_Temp_alias]["protected"];
			}	
			//			
			//CheckEnginesVersion
			if ($engine_Temp_alias) {
				$rqengines = array_keys($exen_MixedEngine[$engine_Temp_alias]["rqEngines"]);
				$rqengines_ver = $exen_MixedEngine[$engine_Temp_alias];
				$a=0;
				while($a < count ($rqengines)) {
					$versiones[$a] = $exen_MixedEngine[$engine_Temp_alias]["rqEngines"][$rqengines[$a]];
					if ($rqengines[$a] != "Core") {						
						$vCengineLocEXP = $exen_app_path.$exen_path."ex_framework/engines/".$rqengines[$a].".php";
						print $vCengineLocEXP;
						exen_load_engine($vCengineLocEXP);
						$libAlias = $exen_MixedEngineLoader["alias"];
						$engineVersion = $exen_MixedEngine[$libAlias]["version"];
						if (!version_compare($engineVersion,$versiones[$a],">")) {
								exen_exitMessage("ExEngine Core Error [XC03]","Mixed Engine loader cannot load ".$exen_MixedEngine[$engine_Temp_alias]["name"]." engine</b>, '".$rqengines[$a]."' version is incorrect.");
						}
					} else {
						if (!exen_compareCoreVersion($versiones[$a])) {
							$verCheck = exen_checkLatest();
							if ($verCheck) {
								$mess = 'A newer version of ExEngine is available at <a href="http://www.linkfastsa.com/?pid=dgs&psid=exengine" target="_blank">ExEngine Home</a>.';
							}
							exen_exitMessage("ExEngine Core Error [XC03]","Mixed Engine loader cannot load ".$exen_MixedEngine[$engine_Temp_alias]["name"]." engine</b>, a more recent version of ExEngine is required. ".$mess);
						}
					}
					$a++;
				}				
			}	
			//
			
			$exen_config["memoryused"] += filesize($exen_load_engine_loc);
			if ($exen_config['silentmode'] != "1") {
				print "<!-- <!> ExEngine : Mixed Engine ".$engine_name." was loaded successfully, rights reserved to ".$engine_author.". <!> -->\n";
			}
			$exen_engine_loc[$engine_name] = $exen_load_engine_loc;
			$exen_internal_loadedengines[$exen_load_engine_loc] = 1;
			
		} else {
			if ($exen_config['silentmode'] != "1") {
				print "<!-- <!> ExEngine : Mixed Engine ".$exen_load_engine_loc." not found.<!> -->\n";
			}
		}
	}	
}
function exen_dbquery($query,$connection="noname") {
	global $ee6_db;
	return $ee6_db->query($var);
}
// Not Implemented
function exen_dbresult() {
	return "Not Implemented";	
}
function exen_dbfetch_column_types($table) {	
	global $ee6_db;
	return $ee6_db->fetch_column_types($var);
}
function exen_dbnum_rows($var) {
	global $ee6_db;
	return $ee6_db->num_rows($var);
}
function exen_dbfetch_array($var) {
	global $ee6_db;
	return $ee6_db->fetch_array($var);
}
function exen_dberror() {
	return "EE6CL ERR [exen_dberror] not supported";
}
function exen_engine_installed($engine) {
	global $exen_config;
	$engineLocEXP = $exen_config["app_path"].$exen_config["ex_path"]."ex_framework/engines/".$engine.".php";
	return file_exists($engineLocEXP);
}
function exen_engine_getVersion($engine) {
	global $exen_config;
	global $exen_internal_loadedengines;
	global $exen_engine_loc;
	global $exen_MixedEngine;
	print $exen_engine_loc[$engine];
	if ($engine != "Core") {
		if (exen_isEngineLoaded($engine)) {
			return $exen_MixedEngine[$engine]["version"];
		} else {
			if (exen_engine_installed($engine)) {
				exen_load_engine($engine);
				$engineVersion = $exen_MixedEngine[$engine]["version"];
				return $engineVersion;
			} else {
				exen_exitMessage("ExEngine Core Error [XC04]","Error in exen_engine_getVersion(".$engine."), engine is not installed.");
			} 
		}
	} else {
		return $exen_config["version"];
	}
}
function exen_isEngineLoaded($engine) {
	global $exen_internal_loadedengines;
	return array_key_exists($engine, $exen_internal_loadedengines);
}
function exen_loc_engine($engine) {
	global $exen_config;
	return $exen_config["http_path"].$exen_config["ex_path"]."ex_framework/engines/".$engine.".php" ;
}
function exen_loc_engine_internal($engine) {
	global $exen_config;
	return $exen_config["app_path"].$exen_config["ex_path"]."ex_framework/engines/".$engine.".php" ;
}
function exen_temp_dir() {
	global $exen_config;
	return $exen_config["app_path"].$exen_config["ex_path"]."ex_framework/temp/" ;
}
function exen_sqlitelocation() {
	global $exen_config;
	global $exen_database;
	if ($exen_config['mu'] == 1) {
		return $exen_config["musqlitedir"].$exen_database["host"] ;
	} else {
		return $exen_config["app_path"].$exen_config["ex_path"]."ex_framework/databases/".$exen_database["host"] ;
	}
}
function exen_temp_file() {
	return exen_temp_dir().exen_timedate("filecreation").".ext";
}
function exen_temp_delete() {
	global $exen_config;
	if ($diropen = opendir(exen_temp_dir())) {
    	while (false !== ($file = readdir($diropen))) {
        	if ($file != "." && $file != "..") {
				unlink( exen_temp_dir().$file );
        	}
    	}
	}
}
function exen_database() {
	global $ee6_db;
	global $ee6cl;
	$ee6_db = new eedbm($ee6cl["ee7obj"]);
	$ee6_db->open();
}
/* NOT SUPPORTED
function exen_onfly_database($dbname,$type,$host,$argument="silent",$user="",$pass="",$db="") {
global $exen_config ;
global $exen_onflydbcnx ;
global $exen_onflydbselect ;
global $exen_silentmode ;
global $exen_onflydb;
global $exen_onflysqlite;
global $exen_database_connected;
if ($type == "mysql") {
	$exen_onflydbcnx[$dbname] = @mysql_connect($exen_database["host"],$exen_database["username"],$exen_database["password"]);
	$exen_onflydbselect[$dbname] = @mysql_select_db($exen_database["database"]);
	if ((!$exen_onflydbcnx[$dbname]) || (!$exen_onflydbselect[$dbname])) { 
	print "<!-- <!> ExEngine ONtheFLY Database Manager | ERROR on database settings, I can not connect to mysql server. -->" ;
	$exen_onfly_database_connected = 0;
	} else { 
			if ($argument != "silent") {
					print "\n<!-- <!> ExEngine : ONtheFLY Database Manager Loaded (Database ".$dbname." connected) -->" ; 
			}
			$exen_onflydb[$dbname] = "mysql";
			$exen_onfly_database_connected = 1;
	}
} elseif ($type == "sqlite") {
	$exen_onflysqlite[$dbname] = sqlite_open($exen_config["app_path"].$exen_config["ex_path"]."ex_framework/databases/".$host) ;
	if ($exen_onflysqlite[$dbname]) {
		$exen_onfly_database_connected = 1;
	}else{
		print "<!-- <!> ExEngine : ONtheFLY Database Manager | ERROR on database settings, I can not open sqlite database. -->" ;
		$exen_onfly_database_connected = 0;
	}
}
if ($argument == "debug") {
	print "<!-- <!> EXENGINE WARNING <!>
 Debug mode is not implemented in ON the FLY database manager because this mode is not recommended, try to use only one database and as many tables as you like.
 <!> EXENGINE WARNING <!> -->" ;
	}
}
*/
function exen_convert_val($typ="null",$val="null") {
	global $exen_config;
	include_once($exen_config["app_path"].$exen_config["ex_path"]."ex_framework/convert_vals.php");
	if (array_key_exists($typ, $exen_convert_manager_values) === true) {
		return $exen_convert_manager_values[$typ];
	} else {
		return "<!> EXENGINE WARNING <!> Convert value not implemented, try to add in the values include file." ;
	}
}
function exen_commenta($msg,$title="EXENGINE ALERT") {
	print "<!--\n<!> ".$title." <!>\n  ".$msg." \n<!> ... <!>\n-->\n";
}
function exen_countdirfiles($dir) {
	$counter = 0 ;
	if ($diropen = opendir($dir)) {
    	while (false !== ($file = readdir($diropen))) {
        	if ($file != "." && $file != "..") {
				$counter++;
       		}
    	}
    	closedir($diropen);
	}
	return $counter ;
}
function exen_autowap() {
	global $exen_config;
	global $exen_arguments;
	if ($exen_arguments["autowap"] == 1) {
		if ($exen_config['mobilebrowser'] > 0) {		
		$waploc = $exen_config["http_path"].$exen_config["wap_index"];
			  if (exen_rfile_exists($waploc)) {
				$exen_config["silentmode"] = 1;
				
				header('Location: '.$waploc);
				exit();
			  } else {
				  print "<h1>ExEngine Core Warning [XW03]</h1><br/><br/><b>Please check configuration file, may not be correct or has missing fields.</b><br/><br/>ExEngine has detected a Mobile Browser but the mobile configuration is not complete, you can desactivate this message removing the ".'$exen_arguments["autowap"]'." = 1;.";
			  }
		} else {
			// NOTHING, CONTINUE.	
		}
	}
}
function exen_ismobile($silent=false) {
	global $exen_config;
	//exen_checkmobile is based in Lightweight Device-Detection by Ronan @ MobiForge.com
	$exen_config['mobilebrowser'] = '0';
 
	if(preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
		$exen_config['mobilebrowser']++;
	}
	 
	if((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml')>0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
		$exen_config['mobilebrowser']++;
	}    
	 
	$mobile_ua = strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4));
	$mobile_agents = array(
		'w3c ','acs-','alav','alca','amoi','audi','avan','benq','bird','blac',
		'blaz','brew','cell','cldc','cmd-','dang','doco','eric','hipt','inno',
		'ipaq','java','jigs','kddi','keji','leno','lg-c','lg-d','lg-g','lge-',
		'maui','maxo','midp','mits','mmef','mobi','mot-','moto','mwbp','nec-',
		'newt','noki','oper','palm','pana','pant','phil','play','port','prox',
		'qwap','sage','sams','sany','sch-','sec-','send','seri','sgh-','shar',
		'sie-','siem','smal','smar','sony','sph-','symb','t-mo','teli','tim-',
		'tosh','tsm-','upg1','upsi','vk-v','voda','wap-','wapa','wapi','wapp',
		'wapr','webc','winw','winw','xda','xda-');
	 
	if(in_array($mobile_ua,$mobile_agents)) {
		$exen_config['mobilebrowser']++;
	}
	 
	if (strpos(strtolower($_SERVER['ALL_HTTP']),'OperaMini')>0) {
		$exen_config['mobilebrowser']++;
	}
	 
	if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'windows')>0) {
		$exen_config['mobilebrowser']=0;
	}
	 
	if($exen_config['mobilebrowser']>0) {
		 
	return true;
	}
	else {
		 
	  return false;
	}   
}
function exen_extendedpath() {
	global $exen_config;
	return $exen_config["app_path"].$exen_config["ex_path"]."ex_framework/extended/";	
}
if (array_key_exists("loadextended",$exen_config)) {
	if ($exen_config["loadextended"] == 1) {
		if (file_exists($exen_config["app_path"].$exen_config["ex_path"]."ex_framework/extended/autoload.php")) {
			include_once($exen_config["app_path"].$exen_config["ex_path"]."ex_framework/extended/autoload.php");
		} else {
			$exen_config["loadextended"] = 0;
		}
	}
}
function exen_errorCodeExplainer($errorCode) {
	global $exen_errorCodes;
	return $exen_errorCodes[$errorCode];
}
function exen_checkOlderVersionLoaded() {
	$exEv4 = function_exists('horafecha');
	$exEv3 = function_exists('podcasturl') && !function_exists('horafecha');
	
	return $exEv4 || $exEv3;
}
function exen_tests_configTest() {
	global $exen_config;
	global $exen_app_key;
	$exenConfigExists = file_exists($exen_config["app_path"]);
	$appKey = $exen_app_key;
	$sessionName = $exen_config["session_name"];
	$httpAccess = exen_checkhttpaccess();
	?>
	<h1>ExEngine <? print $exen_config['version']; ?> Configuration Test</h1><br/>
	<h3>Testing ex_engine_config.php . . .</h3>
	<p><b>Configuration file readable/exists: 	<? if($exenConfigExists===true){?> TRUE <? } else { ?> FALSE <? } ?></b></p>
	<p><b>Application Key set: 	<? if($appKey){?> TRUE <? } else { ?>FALSE<? } ?></b></p>
	<p><b>Session Name set: 	<? if($sessionName){?> TRUE <? } else { ?>FALSE<? } ?></b></p>
	<p><b>HTTP Access: 	<? if($httpAccess===true){?> TRUE <? } else { ?>FALSE<? } ?></b></p>
	<h3>Test Finished.</h3>
	<?
}
if (isset($_GET['cmd'])) {
	if ($_GET['cmd']=="testConfig") {
		$errorCode = $_GET['error'];
		if ($errorCode) {
		?>
		<h1>An error has been found : <? print exen_errorCodeExplainer($errorCode); ?></h1>
		<?
		}
		if ($errorCode == "XC01" || !$errorCode) {
			exen_tests_configTest();
		}
		exit();
	}
}
if (isset($exen_arguments)) {
	if (array_key_exists("autowap",$exen_arguments)) {
		if ($exen_arguments["autowap"] == 1) {
			exen_ismobile();
			exen_autowap();
		}
	}
}
/*
if (!$_GET['inc']) {
$footer = "<!--
	//////////////////////////////////////////////////////////////////
	//             ExEngine is used by this application             //
	// (c) 1999-2009 - DGS // LINKFAST OSS                          //
	// ExEngine is licenced with the GNU/GPL version 3 Licence.     //
	// ExEngine is OpenSource                                       //
	// More info at : http://www.linkfastsa.com/?pid=sp_dgs         //
	// ExEngine's Wiki : http://wiki.aldealinkfast.com/exengine/    //
	//////////////////////////////////////////////////////////////////
--> \n";
	 if ($exen_config['silentmode'] != 1) {
 		print $footer." \n"; 
 	}
	if($exen_arguments['debug'] == 1) {
		exen_commenta("ExEngine Version : ".$exen_config["versionFull"]."\n ExEngine Date : ".$exen_config["releaseDate"],"ExEngine Debug Mode") ;
	}
 }
 */
/*
//EXENGINE INIT//
if (exen_checkOlderVersionLoaded() === true) {
	if (exen_silentmode() === false) {
	print '<!-- 
 <!> EXENGINE WARNING <!>
 An older version of ExEngine  has been detected, is recommended not loading older versions, try using exen_load_engine("legacy"); (if legacy MixedEngine is available).
 Loading it without using the engine loader may cause problems.
 <!> EXENGINE WARNING <!>
	 -->';
	}
}
date_default_timezone_set($exen_config["timezone"]);
$exen_internal_loadedengines = array("ExEngine" => 1);
exen_configcheck();
include($exen_config["app_path"].$exen_config["ex_path"]."ex_framework/autoload.php") ;
if ($exen_config['cloud'] == 1) {
	include_once("../../ee/eefx/ee6clayer/ex_framework/cloud/ee.cloud.php");
}
*/
?>