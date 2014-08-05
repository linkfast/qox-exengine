<?php

# ExEngine 7 / Libs / Browser Control Library

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

class browser 
{
	public $properties;
	
	private $ee;
	private $mobileBrowserCtrl=0;
	
	function __construct($ee) {
		$this->ee = &$ee;
		$this->properties = $this->detailArray();
	}
	
	function ajaxRequired() {
		if (!$this->ajaxCapable()) {
			$ee = $this->ee;
			include_once($this->ee->miscGetResPath("full")."noajaxsupport.php");
			exit();	
		}
	}
	
	function onlyFirefox() {
		if (!$this->detailArray('firefox', '>= 1.0')) {
			$ee = $this->ee;
			include_once($this->ee->miscGetResPath("full")."onlyff.php");
			exit();
		}
	}
	
	function ajaxCapable() {
		# Ported function from ExEngine 6 SiX ExtendedEngine
		if ($this->detailArray('msie', '>= 5.0') || $this->detailArray('opera', '>= 7.6') || $this->detailArray('netscape', '>= 7.1') || $this->detailArray('firefox', '>= 1.0') || $this->detailArray('safari', '>= 1.2') || $this->detailArray('mozilla', '>= 4.2')) {
			return true;
		} else {
			return false;
		}
	}
	
	function isMobileBrowser() {
		# This script is based in Lightweight Device-Detection by Ronan @ MobiForge.com
		$this->mobileBrowserCtrl = 0;
		if(preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
			$this->mobileBrowserCtrl++;
		}
		if((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml')>0) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))) {
			$this->mobileBrowserCtrl++;
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
			$this->mobileBrowserCtrl++;
		}
	
		if (strpos(strtolower($_SERVER['ALL_HTTP']),'OperaMini')>0) {
			$this->mobileBrowserCtrl++;
		}
		 
		if (strpos(strtolower($_SERVER['HTTP_USER_AGENT']),'windows')>0) {
			$this->mobileBrowserCtrl++;
		}
		if($this->mobileBrowserCtrl++>0) {		 
			return true;
		}
		else {		 
		  return false;
		}   
	}
	
	function detailArray($a_browser = false, $a_version = false, $name = false)
	{
		# Ported function from ExEngine 6 SiX ExtendedEngine
		$browser_list = 'msie firefox konqueror safari netscape navigator opera mosaic lynx amaya omniweb chrome avant camino flock seamonkey aol mozilla gecko';
		$user_browser = strtolower($_SERVER['HTTP_USER_AGENT']);
		$this_version = $this_browser = '';	   
		$browser_limit = strlen($user_browser);
		foreach ($this->w($browser_list) as $row)
		{
			$row = ($a_browser !== false) ? $a_browser : $row;
			$n = stristr($user_browser, $row);
			if (!$n || !empty($this_browser)) continue;		   
			$this_browser = $row;
			$j = strpos($user_browser, $row) + strlen($row) + 1;
			for (; $j <= $browser_limit; $j++)
			{
				$s = trim(substr($user_browser, $j, 1));
				$this_version .= $s;
			   
				if ($s === '') break;
			}
		}	   
		if ($a_browser !== false)
		{
			$ret = false;
			if (strtolower($a_browser) == $this_browser)
			{
				$ret = true;			   
				if ($a_version !== false && !empty($this_version))
				{
					$a_sign = explode(' ', $a_version);
					if (version_compare($this_version, $a_sign[1], $a_sign[0]) === false)
					{
						$ret = false;
					}
				}
			}		   
			return $ret;
		}
		$this_platform = '';
		if (strpos($user_browser, 'linux'))
		{
			$this_platform = 'linux';
		}
		elseif (strpos($user_browser, 'macintosh') || strpos($user_browser, 'mac platform x'))
		{
			$this_platform = 'mac';
		}
		else if (strpos($user_browser, 'windows') || strpos($user_browser, 'win32'))
		{
			$this_platform = 'windows';
		}
	   
		if ($name !== false)
		{
			return $this_browser . ' ' . $this_version;
		}	   
		return array(
			"browser"      => $this_browser,
			"version"      => $this_version,
			"platform"     => $this_platform,
			"useragent"    => $user_browser
		);
	}
	
	function w($a = '')
	{
		if (empty($a)) return array();
		return explode(' ', $a);
	}
}

?>