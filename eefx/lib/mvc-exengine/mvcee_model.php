<?php
/**
@file mvcee_model.php
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

ExEngine 7 / Libs / MVC-ExEngine / Model Class

ExEngine MVC Implementation Library

*/

class eemvc_model {
	
	//Default Database Object (used for Code-Hinting compatibility)
	public $db;
	public $r; 

	public function __toString() {
		$obj = $this;
		unset($obj->db);
		unset($obj->r);
		return print_r($obj,true);
	}
	
	function __construct() {
		$this->r = new eemvc_methods($this);		
	}
	
	//Database loader, compatible with EEDBM (used for Code-Hinting compatibility)
	final function loadDB($dbObj="default") {		
		$this->db = new eedbm($this->ee,$dbObj);
	}
	
	//Get all Controller's properties
	function __get($key)
	{
		$Contr =& eemvc_get_instance();
		return $Contr->$key;
	}
	
	//Call Controller's methods
	function __call($name,$args=null) {
		$Contr =& eemvc_get_instance();		
		if (method_exists('eemvc_controller',$name)) {
			if ($args==null) {
				call_user_func(array($Contr,$name));
			} else {
				call_user_func_array(array($Contr,$name), $args); 
			}
		}
	}
}

include_once('model_variants/mv_dbo_mysql.php');
include_once('model_variants/mv_dbo_mongodb.php');
?>