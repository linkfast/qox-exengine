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

ExEngine / Libs / MVC-ExEngine / Model Class

ExEngine MVC Implementation Library

*/

namespace ExEngine\MVC {

	class Model {

		/* @var $db \eedbm */
		protected $db;
		/* @var $r ApplicationEnvironment */
		protected $r;
		/* @var $ee \exengine */
		protected $ee;


		function log() {
			$ma = new \eema("eemvc-model-".get_class($this),'MVC-EE MODEL "'.get_class($this).'".');
			return $ma;
		}

		public function __toArray() {
			$obj = clone $this;
			unset($obj->db);
			unset($obj->r);
			return get_object_vars($obj);
		}

		public function __toString() {
			$obj = clone $this;
			unset($obj->db);
			unset($obj->r);
			return print_r($obj,true);
		}

		function __construct() {
			$this->r = new ApplicationEnvironment($this);
			$this->ee = &ee_gi();
		}

		//Database loader, compatible with EEDBM
		final function loadDB($dbObj="default") {
			$this->db = new \eedbm($this->ee,$dbObj);
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
}

namespace {
	include_once('model_variants/mv_dbo_mysql.php');
	include_once('model_variants/mv_dbo_sqlite.php');
	include_once('model_variants/mv_dbo_mongodb.php');
    include_once('model_variants/mv_dbo_parse.php');

	class eemvc_model_dbo_sqlite extends ExEngine\MVC\DBO\SQLite {};
	class eemvc_model_dbo_mongodb extends ExEngine\MVC\DBO\MongoDB {};
	class eemvc_model_dbo_mysql extends ExEngine\MVC\DBO\MySQL {};
	class eemvc_model_dbo extends ExEngine\MVC\DBO\MySQL { }
}
?>