<?php

# ExEngine 7 / Libs / ExEngine's jQuery

/*
 This file is part of ExEngine.
 Copyright (C) LinkFast Company

 ExEngine is free software; you can redistribute it and/or modify it under the
 terms of the GNU Lesser Gereral Public Licence as published by the Free Software
 Foundation; either version 2 of the Licence, or (at your opinion) any later version.

 ExEngine is distributed in the hope that it will be usefull, but WITHOUT ANY WARRANTY;
 without even the implied warranty of merchantability or fitness for a particular purpose.
 See the GNU Lesser General Public Licence for more details.

 You should have received a copy of the GNU Lesser General Public Licence along with ExEngine;
 if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, Ma 02111-1307 USA.
 */

class jquery {
	private $resPath;
	private $fresPath;
	private $jqUIThemes;
	private $ee;
	private $jqVersion = "1.10.2";
	private $jqUIVersion = "1.10.3";

	public $tagMode = false;
	public $tagData = "";
	public $jqThemes = array ("base","ui-darkness","ui-lightness","start","redmond","black-tie","blitzer","cupertino","dark-hive","dot-luv","eggplant","excite-bike","hot-sneaks","humanity","le-frog","mint-choc","overcast","pepper-grinder","smoothness","south-street","sunny","swanky-purse","trontastic","vader");

	//CDN Servers: 0: google, 1: jquery (mt) [no https], 2: Microsoft, 3: CloudFlare
	private $remoteGET = array("//ajax.googleapis.com/ajax/libs/LIBNAME/LIBVERSION/LIBFILE.js", "http://code.jquery.com/jquery-LIBVERSION.min.js",
	"//ajax.aspnetcdn.com/ajax/jQuery/jquery-LIBVERSION.min.js", "//cdnjs.cloudflare.com/ajax/libs/jquery/LIBVERSION/LIBFILE.js");
	public $CDNServer = 0;

	const VERSION = "1.0.9";

	function __construct($parent) {
		$this->ee = &$parent;
		$this->resPath = $this->ee->libGetResPath("jquery","http") ;
		$this->fresPath = $this->ee->libGetResPath("jquery","full") ;
		$this->jqUIThemes = $this->resPath . "themes/";	
	}
	
	function load_migrate($ret = false) {
			$file = $this->ee->libGetResPath("jquery","http")."jquery-migrate-1.1.0.js";
			$t = '<script type="text/javascript" src="'.$file.'"></script>'."\n";
			if ($ret) return $t ; else print $t;
	}
	
	function load($ver = null, $ret = false) {
		if (!$ver) $ver = $this->jqVersion;
		$file = $this->ee->libGetResPath("jquery","full")."jquery-".$ver.".min.js";

		if (file_exists($file)) {
			$t = '<script type="text/javascript" src="'.$this->resPath."jquery-".$ver.".min.js".'"></script>'."\n";
			if ($this->tagMode)
				$this->tagData .= $t;
			else
			{
				if ($ret)
					return $t; 
				else				
					print $t;
			}
				
		} else {
			//Try to load from CDN
			$uri = str_replace("LIBNAME","jquery",$this->remoteGET[$this->CDNServer]);
			$uri = str_replace("LIBVERSION",$ver,$uri);
			$uri = str_replace("LIBFILE","jquery.min",$uri);
				
			$t = '<script type="text/javascript" src="'.$uri.'"></script>'."\n";
			if ($this->tagMode)
			$this->tagData .= $t;
			else {
				if ($ret)
					return $t; 
				else				
					print $t;
			}
		}
	}
	function load_dev($ver = null, $ret=false) {
		if (!$ver) $ver = $this->jqVersion;
		$file = $this->ee->libGetResPath("jquery","full")."jquery-".$ver.".dev.js";

		if (file_exists($file)) {
			$t= '<script type="text/javascript" src="'.$this->resPath."jquery-".$ver.".dev.js".'"></script>'."\n";
			if ($this->tagMode)
			$this->tagData .= $t;
			else {
				if ($ret)
					return $t; 
				else				
					print $t;
			}
		} else {
			//Try to load from CDN
			$uri = str_replace("LIBNAME","jquery",$this->remoteGET[$this->CDNServer]);
			$uri = str_replace("LIBVERSION",$ver,$uri);
			$uri = str_replace("LIBFILE","jquery",$uri);
			$t= '<script type="text/javascript" src="'.$uri.'"></script>'."\n";
			if ($this->tagMode)
				$this->tagData .= $t;
			else {
				if ($ret)
					return $t; 
				else				
					print $t;
			}
		}
	}
	function load_ui($theme="base",$ver = null,$ret=false) {
		if (!$ver) $ver = $this->jqUIVersion;
		$file = $this->ee->libGetResPath("jquery","full")."jquery-ui-".$ver.".min.js";
		$t = '';
		//THEME LOAD
		if (!file_exists($this->ee->libGetResPath("jquery","full").'/themes/'.$theme.'/jquery.ui.all.css')) {
			if ($_SERVER['SERVER_PORT']=="443") {
				$htp = "https";	
			} else
				$htp = "http";			
			//Try to load from CDN.		
			if ($this->ee->httpCheckURL("$htp://ajax.googleapis.com/ajax/libs/jqueryui/".$this->jqUIVersion."/themes/".$theme."/jquery-ui.css")) {
				$t .= '<link type="text/css" href="'."$htp://ajax.googleapis.com/ajax/libs/jqueryui/".$this->jqUIVersion."/themes/".$theme."/jquery-ui.css".'" rel="stylesheet" />'."\n";
			} else {
				$this->ee->errorWarning("Theme not found locally neither Google's CDN. ("."http://ajax.googleapis.com/ajax/libs/jqueryui/".$this->jqUIVersion."/themes/".$theme."/jquery-ui.css".").");	
			}
		} else {
			$t .= '<link type="text/css" href="'.$this->jqUIThemes.$theme.'/jquery.ui.all.css" rel="stylesheet" />'."\n";
		}
		// JQUERY UI LOAD
		if (file_exists($file)) {
			$t .= '<script type="text/javascript" src="'.$this->resPath."jquery-ui-".$ver.".min.js".'"></script>'."\n";						
		} else {
			//Try to load from CDN
			$uri = str_replace("LIBNAME","jqueryui",$this->remoteGET[$this->CDNServer]);
			$uri = str_replace("LIBVERSION",$ver,$uri);
			$uri = str_replace("LIBFILE","jquery-ui.min",$uri);
			$t .= '<script type="text/javascript" src="'.$uri.'"></script>'."\n";						
		}	
		
		if ($this->tagMode)
			$this->tagData .= $t;
		else{
				if ($ret)
					return $t; 
				else				
					print $t;
			}
	}
	
	function load_plugin($name,$opt=null) {
		$findme   = ',';
		$pos = strpos($name, $findme);
		if ($pos === false) {
			$plug=$name;
			$pFile = $this->fresPath."plugins/".$plug."/jqPlugin.php";
			if (file_exists($pFile)) {
				include_once($pFile);				
				$plugpath = $this->resPath."plugins/".$plug."/";
				$plugpathf = $this->fresPath."plugins/".$plug."/";
				if (isset($legal) && is_array($legal)) {
					foreach ($legal as $lFile) {						
						if (file_exists($plugpathf.$lFile)) {
							$t = '<!--'."\n";
							ob_start();
							readfile($plugpathf.$lFile);
								$t .= ob_get_contents();
							ob_end_clean();
							$t .= "\n".'-->'."\n";
						}
						if ($this->tagMode)
							$this->tagData .= $t;
						else
							print $t;
					}
				}			
				if (isset($css) && is_array($css)) {
					foreach ($css as $cssFile) {
						$t = '<link type="text/css" href="'.$plugpath.$cssFile.'" rel="stylesheet" />'."\n";
						if ($this->tagMode)
						$this->tagData .= $t;
						else
						print $t;
					}
				}
				if (isset($js) && is_array($js)) {
					foreach ($js as $jsFile) {
						$t= '<script type="text/javascript" src="'.$plugpath.$jsFile.'"></script>'."\n";
						if ($this->tagMode)
						$this->tagData .= $t;
						else
						print $t;
					}
				}
			} elseif (file_exists($plug."/jqPlugin.php")) {
				include_once($plug."/jqPlugin.php");
				$plugpath=$plug;
				if ($opt != null) {
					$plugpath=$opt;
				}
				if (isset($legal) && is_array($legal)) {
					foreach ($legal as $lFile) {						
						if (file_exists($plugpath.$lFile)) {
							$t = '<!--'."\n";
							ob_start();
							readfile($plugpath.$lFile);
								$t .= ob_get_contents();
							ob_end_clean();
							$t .= "\n".'-->'."\n";
						}
						if ($this->tagMode)
							$this->tagData .= $t;
						else
							print $t;
					}
				}
				if (isset($css) && is_array($css)) {
					foreach ($css as $cssFile) {
						$t= '<link type="text/css" href="'.$plugpath.$cssFile.'" rel="stylesheet" />'."\n";
						if ($this->tagMode)
							$this->tagData .= $t;
						else
							print $t;
					}
				}
				if (isset($js) && is_array($js)) {
					foreach ($js as $jsFile) {
						$t= '<script type="text/javascript" src="'.$plugpath.$jsFile.'"></script>'."\n";
						if ($this->tagMode)
							$this->tagData .= $t;
						else
							print $t;
					}
				}
			} else {
				$t= "<!-- ExEngine's jQuery : Plugin '".$plug."' not found. -->";
				if ($this->tagMode)
				$this->tagData .= $t;
				else
				print $t;
			}
		} else {
			$err = 0;
			$load = 0;
			$ees = explode(",",$name);
			foreach ($ees as $ext) {
				$plugin = $ext;
				$pFile = $this->ee->libGetResPath("jquery","full")."plugins/".$plugin."/jqPlugin.php";
				if (file_exists($pFile)) {
					include_once($pFile);
					$plugpath = $this->resPath."plugins/".$plugin."/";
					$plugpathf = $this->fresPath."plugins/".$plugin."/";
					if (isset($legal) && is_array($legal)) {
						foreach ($legal as $lFile) {						
							if (file_exists($plugpathf.$lFile)) {
								$t = '<!--'."\n";
								ob_start();
								readfile($plugpathf.$lFile);
									$t .= ob_get_contents();
								ob_end_clean();
								$t .= "\n".'-->'."\n";
							}
							if ($this->tagMode)
								$this->tagData .= $t;
							else
								print $t;
							}
					}					
					if (isset($css) && is_array($css)) {
						foreach ($css as $cssFile) {
							$t = '<link type="text/css" href="'.$plugpath.$cssFile.'" rel="stylesheet" />'."\n";
							if ($this->tagMode)
								$this->tagData .= $t;
							else
								print $t;
						}
					}
					if (isset($js) && is_array($js)) {
						foreach ($js as $jsFile) {
							$t = '<script type="text/javascript" src="'.$plugpath.$jsFile.'"></script>'."\n";
							if ($this->tagMode)
								$this->tagData .= $t;
							else
								print $t;
						}
					}
				} else {
					$t = "<!-- ExEngine's jQuery : Plugin '".$plugin."' not found. -->";
					if ($this->tagMode)
					$this->tagData .= $t;
					else
					print $t;
				}
				unset($plugin,$pFile,$plugpath,$js,$css,$legal);
			}
		}
	}
}


?>