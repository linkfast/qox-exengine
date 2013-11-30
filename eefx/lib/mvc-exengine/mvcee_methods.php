<?php
/**
@file mvcee_methods.php
@author Giancarlo Chiappe <gchiappe@qox-corp.com> <gchiappe@outlook.com.pe>

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

ExEngine 7 / Libs / MVC-ExEngine / Methods Class (Resources)

ExEngine MVC Implementation Library

*/

class eemvc_methods {
		
		var $cparent;	
		var $jQueryObject;
		
		final function sf() {
			return $this->cparent->index->staticFolderHTTP . $this->tra;
		}
		
		final function fsf() {
			return $this->cparent->index->staticFolder . $this->tra ;	
		}
		
		final function c() {
			return $this->cparent->index->controllersFolderHTTP.$this->tra;
		}
		
	/* TODO: REMOVE
	final function scpath() {
		$urldata = $this->cparent->index->unModUrlParsedData;
		$size = count($urldata);
		$str_make = null;
		$urldata = array_slice($urldata,0,($size-3));
		$size = count($urldata);
		for ($i = 0; $i
							< $size ; $i++) {
			$str_make .= $urldata[$i].'/';	
		}
		return $str_make;
	}
	*/
	
	final function home() {
		$x[0] = null;
		if (!$this->cparent->index->rewriteRulesEnabled) {
			$x = $_SERVER['REQUEST_URI'];		
			$x = explode($this->cparent->index->indexname,$x);
		}
		return "//" . $_SERVER['HTTP_HOST']. $x[0];
	}

	final function sc() {		
		return $this->cparent->index->sameControllerFolderHTTP.$this->cparent->index->controllername . $this->tra;	
	}

	final function scfolder() {
		return $this->cparent->index->sameControllerFolderHTTP . $this->tra;
	}
	
	final function scf() {
		return $this->cparent->index->sameControllerFolderHTTP.$this->cparent->index->controllername."/".$this->cparent->functionName. $this->tra;		
	}

	final function scfi() {
		if ($this->cparent->functionName == "index")
		return $this->cparent->index->sameControllerFolderHTTP.$this->cparent->index->controllername . $this->tra;
			else
		return $this->scf();
	}
	
	final function vs() {
		return $this->cparent->index->controllersFolderHTTP.$this->tra."?EEMVC_SPECIAL=VIEWSIMULATOR&VIEW=";	
	}
	
	final function __construct(&$parent) {
		$this->cparent = &$parent;
		$this->jQueryObject = &$this->cparent->index->jQueryObject;
		$this->tra = null;
		if ($this->cparent->index->trailingSlashLegacy) {
			$this->tra = "/";
		}
	}
	
	final function getSession($element) {
		if ($this->cparent->index->SessionMode)			
			return @$_SESSION[$element];
		else {
			$this->cparent->debug("eemvcil.php:". __LINE__ . ": Cannot get a session variable, SessionMode is set to false.");
			return null;	
		}			
	}
	
	final function setSession($element,$value) {
		if ($this->cparent->index->SessionMode)
			$_SESSION[$element] = $value;	
		else {
			$this->cparent->debug("eemvcil.php:". __LINE__ . ": Cannot get a session variable, SessionMode is set to false.");
			return null;	
		}
	}
	
	final function clearSession() {
		if ($this->cparent->index->dgEnabled) {
			$dgSession = $_SESSION["DG_SA"];
		}
		session_unset();	
		if ($this->cparent->index->dgEnabled) {
			$_SESSION["DG_SA"] = $dgSession;
		}
	}
	
	final function remSession($element) {
		unset($_SESSION[$element]);	
	}

	final function get($element) {
		return @$_GET[$element];	
	}
	
	final function post($element) {
		//support for json_post (angularJS)
		$data2 = @json_decode(file_get_contents('php://input'));
		if ($data2 instanceof stdClass) {
			$pd = get_object_vars($data2);
			return @$pd[$element];
		} else
			return @$_POST[$element];	
	}
	
	final function file($pname) {
		return @$_FILES[$pname];	
	}
	
	final function allpost() {
		return @$_POST;	
	}
	
	final function allget() {
		return @$_GET;	
	}
}
?>