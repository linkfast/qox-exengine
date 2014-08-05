<?php

# ExEngine 7 / Extended Libs / AjaxCore 2

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


class ajaxcore2
{	
	private $ee;
	private $thisFile;
	
	const APP = "AjaxCore 2";
	const VERSION = "2.0.0.1";
	
	public  $redirectorLoc = "index.php";

	#AjaxCore2 JavaScript Load
	private $acJS;
	
	#ExEngine's JS
	private $eeJS;

	function __construct($parent,$autoServer=1) {
    	$this->ee = $parent;
		$this->acJS[] = "ac2_basic.js";
		
		$this->thisFile = $this->ee->miscPhpSelfNoPath();
		
		$this->eeJS = $this->ee->miscGetJSNames();
		
		if ($autoServer == 1) {
			if ($this->ee->argsGet("SilentMode") || !$this->ee->argsGet("ShowSlogan")) {
				$this->thisFileServer();
			} else {
				$this->ee->errorWarning("AjaxCore AutoServer mode requires SilentMode=true or ShowSlogan=false Arguments.");
			}
		}
    }
	
	function init() {
		$sF = $this->thisFile;		
		#Include ExEngine's JavaScript
		foreach ($this->eeJS as $eeJSName) {
			$src = $sF."?AC2=ee&file=".$eeJSName;
			print '<script type="text/javascript" src="'.$src.'"></script>'."\n";
		}
		#Include AjaxCore2's JavaScript
		foreach ($this->acJS as $acJSName) {
			$src = $sF."?AC2=fS&file=".$acJSName;
			print '<script type="text/javascript" src="'.$src.'"></script>'."\n";
		}
		
	}	
	
	function thisAjaxServer($input=null,$m=1) {		
		switch ($m) {
			case 1:
			if (!isset($input))
				@header("Location: ".$this->redirector);
			break;
		case 2:
			if ($input)
				@header("Location: ".$this->redirector);
			break;
		}
	}
	
	function thisFileServer() {
		if (isset($_GET['AC2'])) {
			$eeJSPath = $this->ee->miscGetFXPath()."js/";
			$ac2Path = $this->ee->eeResPath()."ajaxcore2/";
			
			if ($_GET['AC2'] == "ee" && $_GET['file']) {
				$file = $eeJSPath.$_GET['file'];
			}
			if ($_GET['AC2'] == "fS" && $_GET['file']) {
				$file = $ac2Path.$_GET['file'];
			}		
			
			if (file_exists($file)) {
				if ($this->ee->eeLoad("mime")) {
						$mimeObj = new eemime($this->ee);
						$cType = $mimeObj->getMIMEType($file);
						header('Content-type: '.$cType);
						readfile($file);
				} else
						$this->ee->errorExit(self::APP,"Mime Extended Engine required, install it using ExCommander or manually.");
			} else
				$this->ee->errorWarning("$file : File not found.");		
				
			exit();
		}
	}
	
	#AjaxCore2 VisualWeb
	const VWVERSION = "1.0.0.0";
	private $vwThemeDir;
	
	function visualWeb_init($theme="eeSeven") {
		#VisualWeb Theme CSS
		$rP = $this->ee->eeResPath()."ajaxcore2/vwThemes/".$theme."/";
		if (file_exists($rP."vwTheme.php")) {
			$this->vwThemeDir = $rP;		
			$this->ee->miscMessShow("LinkFast AjaxCore2's VisualWeb Powered");
			print '<style type="text/css">'."\n";
			readfile($this->vwThemeDir.$theme.".css");
			print '</style>'."\n";;
		}
		$jq = new jquery($this->ee);
		$jq->load();
		$jq->load_ui("dark-hive");
		?>
<script language="javascript" type="text/javascript">
$(document).ready(function(){
			$(".vw_Buttons").button();
});
</script>
        <?
	}
	
	function visualWeb_button($text,$link) {
		?>
        <a href="<? print $link; ?>" class="vw_Buttons"><? print $text; ?></a>
		<?
    }
	
	function visualWeb_input($fieldName,$type) {
		
	}
	
	function visualWeb_password($fieldName,$type) {
		
	}

}

?>