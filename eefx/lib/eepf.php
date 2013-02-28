<?php

# ExEngine 7 / Libs / ExEngine Portal Framework

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

/* EEPF uses portions of LinkFast AjaxObjects Lite, Some code based on AjaxCore2 */

#README: EEPF Development is halted undefinetly (12/04/2010).

class eepf
{
	const VERSION = "2.0.0.2";
	const APP = "ExEngine Portal Framework";
	
	const NL = "\n";
	
	private $ee;
	private $eepfArray;
	private $eepfDir;	
	private $themesDir;
	
	private $errors=0;
	
	private $logObj;
	private $dbObj;
	private $dbPrefix;
	private $serverSecurity=false;
	
	#EEPF Dir Includes
	private $eepf_JS;
	private $eepf_CSS;
	
	
	# SELECTED THEME
	private $selThemeName;
	private $selThemeDir;
	private $selThemeImgDir;
	private $selThemeCSS;
	private $selThemeJS;
	
	# PROFILE
	private $selProfile;
	private $appHome;
	private $eepfServer;
	private $portalIndex;
	private $portalTitle;
	
	#GUI Control
	private $htmlBoxes=0;
	
	#Args HTTP
	public $httpArgs;
	
	#Language
	public $twoLetterLang;
	private $eepfLang;
	
	function __construct($ee,$profile="default",$logEnabled=true) {
		$this->ee = $ee;

		if ($this->logEnabled)
			$this->logObj = new eelog($this->ee,self::APP);
		
		$this->eepfDir = $this->ee->eePath."eefx/eepf/";
		
		if (!isset($profile["themeDir"]))
			$this->themesDir = $this->ee->eePath."eefx/eepf/themes/" ;			
		
		if (file_exists($this->eepfDir."eepfDirDetails.php")) {
			include_once($this->eepfDir."eepfDirDetails.php");
			if (isset($eepfDirA) && is_array($eepfDirA)) {
					if ($eepfDirA["eepfVersion"] == self::VERSION) {
						$this->eepfArray = $eepfDirA;
						$this->eepf_JS = $eepfDirJS;
						$this->eepf_CSS = $eepfDirCSS;
					} else {
						$this->ee->errorExit(self::APP,"EEPF Resource version is not same as Library require version, please install ".self::VERSION." version from http://www.aldealinkfast.com/oss/exengine/download.php?g=eepfd&ver=".self::VERSION." .");
						$this->errors++;
					}
			} else {
				$this->ee->errorExit(self::APP,"EEPF array is not valid or is not set, please reinstall EEPF Resource.");
				$this->errors++;
			}
		} else {
			$this->ee->errorExit(self::APP,"EEPF Resource is not installed, you need it to enable ExEngine Portal Framework, download from http://www.aldealinkfast.com/oss/exengine/download.php?g=eepfd&ver=".self::VERSION." .");
			$this->errors++;
		}
		if ($this->errors > 0) {
			$this->ee->miscMessShow("HaltOnError should be disabled, ExEngine Portal Framework object is created but errors occoured in creation.");
			$this->ee->logAuto("HaltOnError should be disabled, ExEngine Portal Framework object is created but errors occoured in creation.",$this->logObj);
		}
		
		if ($profile == "default") {
				$this->selProfile = $this->ee->cArray;
				$this->setTheme($this->selProfile["EEPF_Theme"]);
				$this->dbArray = $this->ee->dbArray;
				$this->appHome = $this->ee->cArray["http_path"];
				$this->dbPrefix = $this->selProfile["EEPF_dbPrefix"];
				$this->eepfServer = $this->selProfile["EEPF_DefServer"];
				$this->serverSecurity = $profile["EEPF_ServerSecurity"];
				$this->portalTitle = $profile["EEPF_portalTitle"];
				$this->portalIndex = $this->appHome.$profile["EEPF_portalIndex"];
				$this->twoLetterLang = $profile["EEPF_themeDefLang"];
		} else {
			if (isset($profile) && is_array($profile)) {
				$this->dbArray = $profile["dbArray"];
				$this->dbPrefix = $profile["dbPrefix"];
				$this->themesDir = $profile["themeDir"];
				$this->appHome = $profile["appHTTPHome"];
				$this->eepfServer = $profile["Server"];
				$this->serverSecurity = $profile["ServerSecurity"];
				$this->setTheme($profile["theme"]);
				$this->portalIndex = $profile["appHTTPHome"].$profile["portalIndex"];
				$this->portalTitle = $profile["portalTitle"];
				if (isset($profile["themeDefLang"]))
					$this->twoLetterLang = $profile["themeDefLang"];
				else
					$this->twoLetterLang = "en";				
			} else {
				$this->ee->errorExit(self::APP,"Invalid or empty EEPF Profile Array.");
				$this->errors++;
			}
		}
		
		$this->setLang($this->twoLetterLang);
		$this->dbObj = new eedbm($this->ee,$this->dbArray);
		
		# Load Theme's Array
	}
	/*
	final function createDatabase($part="all",$adminPass,$webAdmin=false) {
		switch($part) {
			"all" :
			break;
			"config":
			break;
			"news" :
			break;
			"forum" :
			break;
			"faq" :
			break;
			"menu" :
			break;
			"pages" :
			break;
			"users" :
			break;
			"blog" :
			break;
		}
		if ($webAdmin) {
			$this->dbObj->open();
			$this->dbObj->query("UPDATE");
			$this->dbObj->close();
		}
	}
	*/
	
	#Server Section
	final function thisEEPFServer($config=null) {
		$this->httpArgsParser();	
		#SECURITY MODE
		if ($this->serverSecurity) {
			if ( $this->eepfServerKeyCheck($this->httpArgs["EEPF"]) ) {
				$auth = true;
			} else {
				header("Location: ".$this->portalIndex);
				$auth = false;
			}
		} else 
			$auth=true;
		#SECURITY MODE		
		
			if ($auth===true) {
			if ($this->httpArgs["file"]) {			
				if ($this->ee->eeLoad("mime")) {
					$mimeObj = new eemime($this->ee);
					$cType = $mimeObj->getMIMEType($this->eepfDir."eepf.js");
					header('Content-type: '.$cType);
					include_once($this->eepfDir."eepf.js");
				} else {
					$this->ee->errorExit(self::APP,"Mime Extended Engine required, install it using ExCommander or manually.");	
				}
			}
			//print $this->httpArgs["themeFile"];
			if ($this->httpArgs["themeFile"]) {			
					$this->serverGetFileFromTheme($this->httpArgs["themeFile"]);
			}
			
			if ($this->httpArgs["prueba"] == "A") {
				print $this->httpArgs["prueba"];
			}		
			if ($this->httpArgs["info"] == "php") {
				phpinfo();
			}
		}
	}
	
	final function serverGetFileFromTheme($file) {
		if (file_exists($this->selThemeDir.$file)) {
			if ($this->ee->eeLoad("mime")) {
					$mimeObj = new eemime($this->ee);
					$cType = $mimeObj->getMIMEType($this->selThemeDir.$file);
					header('Content-type: '.$cType);
					readfile($this->selThemeDir.$file);
			} else
					$this->ee->errorExit(self::APP,"Mime Extended Engine required, install it using ExCommander or manually.");
		} else
			$this->ee->errorWarning("$file : Theme File not found.");
	}
	
	final function eepfServerKeyCreate() {
		$table = $this->dbPrefix . "ajaxKeys";
		$key = substr( md5( uniqid( microtime() ).rand().strftime("%H%M%S" ,time()) ),0,15 );
		$ip = $_SERVER['REMOTE_ADDR'];	
		$this->dbObj->open();			
		$this->dbObj->query("INSERT INTO `$table` (`$table`.`sekey`,`$table`.`seip`) VALUES ('$key','$ip')");
		$this->dbObj->close();
		return $key;
	}
	
	final function eepfServerKeyCheck($key) {
		$table = $this->dbPrefix . "ajaxKeys";
		$ip = $_SERVER['REMOTE_ADDR'];
		$this->dbObj->open();			
		$aK = $this->dbObj->oneRowArray("SELECT * FROM `$table` WHERE `$table`.`sekey` = '$key' AND `$table`.`seip` = '$ip' ");
		$this->dbObj->close();		
		if ($aK["sekey"] == $key) {
			$this->dbObj->open();			
			$aK = $this->dbObj->query("DELETE FROM `$table` WHERE `$table`.`sekey` = '$key' LIMIT 1");
			$this->dbObj->close();
			return true;
		} else {
			return false;
		}
	}
	
	final function httpArgsParser() {
		if (isset($_POST["EEPF"])) {
			$postArgs = $_POST;	
			$this->httpArgs = $postArgs;
		}
		if (isset($_GET["EEPF"])) {
			$getArgs = $_GET;
			$this->httpArgs = $getArgs;
		}
		
		if (is_array($postArgs) && is_array($getArgs)) {
			$this->httpArgs = array_merge($postArgs,$getArgs);
		}
	}
	
	final function cookieParser() {
		if (isset($_COOKIE)) {
			$this->cookies = $_COOKIE;	
		}
	}
	
	final function serverArg($args) {
		if ($this->serverSecurity) {
			$key = $this->eepfServerKeyCreate();
			return 	$this->eepfServer."?EEPF=$key&".$args;
		} else {
			return $this->eepfServer."?EEPF=SD&".$args;
		}		
	}
	
	## END SERVER SECTION ##
	final function setTheme($themeName) {
		if (file_exists($this->themesDir.$themeName."/theme.eepf.php")) {
			$this->selThemeDir = $this->themesDir.$themeName."/";
			include_once($this->selThemeDir."theme.eepf.php");
			$this->selThemeName = $eepf_theme["name"];
			$this->selThemeImgDir = $this->selThemeDir.$eepf_theme["imgDir"];			
			if (isset($eepf_css) && is_array($eepf_css)) {
				$this->selThemeCSS = $eepf_css;				
			}
			if (isset($eepf_js) && is_array($eepf_js)) {
				$this->selThemeJS = $eepf_js;
			}
		} else {
			$this->ee->errorExit(self::APP,"Theme ".$themeName." not found, EEPF will halt ExEngine now.");
			$this->errors++;
		}
	}
	
	final function setLang($lang) {
		if (file_exists($this->eepfDir."lang/".$lang.".php")) {
			include_once($this->eepfDir."lang/".$lang.".php");
			$this->eepfLang = $eepfLang;
		}
	}
	
	final function userExists($user) {
		$table = $this->dbPrefix . "users";
		$this->dbObj->open();
		$userA = $this->dbObj->oneRowArray("SELECT * FROM `$table` WHERE `user` = '$user'");
		$this->dbObj->close();
		if ($userA["user"] == $user)
			return true;
		else
			return false;
	}
	
	final function themeIncludes() {
		#Load JS
		if (isset($this->selThemeJS) && is_array($this->selThemeJS)) {
			foreach ($this->selThemeJS as $jsFile) {
				$srcLoc = $this->serverArg("themeFile=".$jsFile);
				print '<script type="text/javascript" src="'.$srcLoc.'"></script>'."\n";
			}
		}
		#Load CSS
		if (isset($this->selThemeCSS) && is_array($this->selThemeCSS)) {
			foreach ($this->selThemeCSS as $cssFile) {
				$srcLoc = $this->serverArg("themeFile=".$cssFile);
				print '<link href="'.$srcLoc.'" rel="stylesheet" type="text/css" />'."\n";
			}
		}
	}
	
	final function userLogin($user,$password) {
		$table = $this->dbPrefix . "users";
		$this->dbObj->open();
		$userA = $this->dbObj->oneRowArray("SELECT * FROM `$table` WHERE `user` = '$user' AND `passw` = '$password'");
		$this->dbObj->close();
		if ($userA["user"] == $user && $userA["passw"] == $password) {
			$_SESSION["EEPF"] = true;
			$_SESSION["userID"] = $userA["id"];
			$_SESSION["userRealName"] = $userA["real_name"];
			$_SESSION["userEmail"] = $userA["email"];
			$_SESSION["userPriv"] = $userA["priv"];
			return true;
		}
		else
			return false;
	}
	
	final function getUserDBDetails($userID) {
		$table = $this->dbPrefix . "users";
		$this->dbObj->open();
		$userA = $this->dbObj->oneRowArray("SELECT * FROM `$table` WHERE `$table`.`id` = '$userID'");
		$this->dbObj->close();
		return $userA;
	}
	
	final function guiLogin($postServer="self",$location="_self",$onsubmit=null,$style1="theme",$style2="theme") {
		
	}
	
	final function guiHorMenu($separator,$menuID=0,$style="theme",$lang=null) {
		$table = $this->dbPrefix . "menuitems";		
		if ($style == "theme") {
			$style = "eepf_hormenu";
		}		
		if (!isset($lang)) {
			$lang = $this->twoLetterLang;	
		}
		$items=0;
		$this->dbObj->open();
		#More strict SQL Code...
		$this->dbObj->query("SELECT * FROM `$table` WHERE `$table`.`parent` = '$menuID' AND `$table`.`lang` = '$lang' ORDER BY `$table`.`pos` ASC",1);
		while($row = @$this->dbObj->fetchArray()) {	
			$menuItems[] = $row["name"];
			$menuLinks[] = $row["link"];
			$items++;
		}
		$this->dbObj->close();
		$ret;
		$c=0;
		foreach ($menuItems as $menuName) {
				$c++;
				$aK = array_keys($menuItems,$menuName);
				if ($c < $items)
					$ret .= '<a href="'.$this->appHome.$menuLinks[$aK[0]].'">'.$menuName.'</a>'.$separator ;
				else
					$ret .= '<a href="'.$this->appHome.$menuLinks[$aK[0]].'">'.$menuName.'</a>' ;
		}
		
		return '<span class="'.$style.'">'.$ret.'</span>'.self::NL;
	}
	
	final function guiVerMenu($menuID=0,$style="theme",$lang=null) {
		$table = $this->dbPrefix . "menuitems";		
		if ($style == "theme") {
			$style = "eepf_vermenu";
		}	
		if (!isset($lang)) {
			$lang = $this->twoLetterLang;	
		}
		$items=0;
		$this->dbObj->open();
		#More strict SQL Code...
		$this->dbObj->query("SELECT * FROM `$table` WHERE `$table`.`parent` = '$menuID' AND `$table`.`lang` = '$lang' ORDER BY `$table`.`pos` ASC",1);
		while($row = @$this->dbObj->fetchArray()) {	
			$menuItems[] = $row["name"];
			$menuLinks[] = $row["link"];
			$items++;
		}
		$this->dbObj->close();
		$ret;
		$c=0;
		foreach ($menuItems as $menuName) {
				$c++;
				$aK = array_keys($menuItems,$menuName);
				if ($c < $items)
					$ret .= '<a href="'.$this->appHome.$menuLinks[$aK[0]].'">'.$menuName.'</a>'.'<br/>' ;
				else
					$ret .= '<a href="'.$this->appHome.$menuLinks[$aK[0]].'">'.$menuName.'</a>' ;
		}
		
		return '<p><span class="'.$style.'">'.$ret.'</span></p>'.self::NL;
	}
	
	final function guiHTMLBox($boxId=null,$id=null,$style="theme",$lang=null) {
		if (!isset($boxId)) {
			$this->ee->errorExit(self::APP,"Malformed eepf::guiHTMLBox(), first argument should not be empty.");	
		}
		if (!isset($lang)) {
			$lang = $this->twoLetterLang;	
		}
		$table = $this->dbPrefix . "htmlBox";
		if ($style == "theme") {
			$style = "eepf_htmlbox";
		}
		
		if (!isset($id)) {
			$id = "eepf_hBox_".$this->htmlBoxes;
			$this->htmlBoxes++;	
		}
		
		$this->dbObj->open();
		$det = $this->dbObj->oneRowArray("SELECT * FROM `$table` WHERE `$table`.`id` = '".$boxId."'  AND `$table`.`lang` = '".$lang."'");
		$this->dbObj->close();
		
		$ret[] = '<div id="'.$id.'" class="'.$style.'">'.$det["innerHTML"].'</div>'.self::NL;		
		$ret[] = $id;
		return $ret;
	}
	
	final function pageRender($pid,$mode="byID",$lang=null) {
		if (!isset($pid)) {
			$this->ee->errorExit(self::APP,"Malformed eepf::renderPage(), first argument should not be empty.");	
		}
		$table = $this->dbPrefix . "pages";
		if (!isset($lang)) {
			$lang = $this->twoLetterLang;	
		}
		
		$this->dbObj->open();
		if ($mode == "byID") {
			$det = $this->dbObj->oneRowArray("SELECT * FROM `$table` WHERE `$table`.`id` = '".$pid."' AND `$table`.`lang` = '".$lang."'");
		} elseif ($mode == "byName") {
			$pid = $this->ee->miscURLClean($pid);
			$det = $this->dbObj->oneRowArray("SELECT * FROM `$table` WHERE `$table`.`cleanTitle` = '".$pid."' AND `$table`.`lang` = '".$lang."'");
		}
		$this->dbObj->close();
		
		print '<p class="eepf_page_title">'.$det["title"].'</p>';
		print '<p class="eepf_page_subtitle">'.$det["subtitle"].'</p>';		
		if ($det["type"] == "PHP") {	
			print '<p class="eepf_page_content">';
			eval("?>".$det["contentMix"]."<?");
			print '</p>';
		} else if ($det["type"] == "HTM")
			print '<p style="eepf_page_content">'.$det["contentMix"].'</p>';		
		print '<p class="eepf_page_tags">'.$det["tags"].'</p>';
	}
	
	
	final function pageGetTitle($pid,$mode="byID") {
		$table = $this->dbPrefix . "pages";
		$this->dbObj->open();
		if ($mode == "byID") {
			$det = $this->dbObj->oneRowArray("SELECT * FROM `$table` WHERE `$table`.`id` = '".$pid."' AND `$table`.`lang` = '".$lang."'");
		} elseif ($mode == "byName") {
			$pid = $this->ee->miscURLClean($pid);
			$det = $this->dbObj->oneRowArray("SELECT * FROM `$table` WHERE `$table`.`cleanTitle` = '".$pid."' AND `$table`.`lang` = '".$lang."'");
		}
		$this->dbObj->close();
		return $det["title"];
	}
	
	function guiAI_PHPLanguage($file) {
		if (isset($this->twoLetterLang)) {
				if (file_exists($this->selThemeDir."php/".$file.".".$this->twoLetterLang.".php")) {
					return 	$this->selThemeDir."php/".$file.".".$this->twoLetterLang.".php";
				} else {
					return $this->selThemeDir."php/".$file.".en.php";
				}
		} else {
			return $this->selThemeDir."php/".$file.".en.php";
		}
	}
	
	final function guiHTMLHeaders($tilt=null) {
		$xhtml1_trans =	'<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" 																		"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>'.$this->getPageTitle().'</title>'."\n";

		$bod = '</head>'."\n".'<body>'."\n";
		
		if (isset($tilt) && $tilt == "head") {
			print $xhtml1_trans;
		} else {
			print $xhtml1_trans.$bod;
		}
	}
	
	final function htmlBott() {
		print "</body>\n</html>";	
	}
	
	final function pageTitleRender() {
		if (!$this->httpArgs["pid"]) {
			return str_replace("%%LOC%%",$this->eepfLang["Home"],$this->portalTitle);
		} else {
			$pageTitle = $this->pageGetTitle($pid);
			return str_replace("%%LOC%%",$this->eepfLang["Home"],$this->portalTitle);
		}
	}
	
	final function themeAutoPortal() {
		
		$this->guiHTMLHeaders("head");
		$this->themeIncludes();
		print "</head>\n<body>";
		#This function will create a theme-based index page.
		if (file_exists($this->guiAI_PHPLanguage("top"))) {
			include_once($this->guiAI_PHPLanguage("top"));
			eepf_theme_top($this,$this->ee);
		}
		if (file_exists($this->guiAI_PHPLanguage("content"))) {
			include_once($this->guiAI_PHPLanguage("content"));
			eepf_theme_content($this,$this->ee);
		}
		if (file_exists($this->guiAI_PHPLanguage("bot"))) {
			include_once($this->guiAI_PHPLanguage("bot"));
			eepf_theme_bot($this,$this->ee);
		}
		$this->htmlBott();
	}
	
}