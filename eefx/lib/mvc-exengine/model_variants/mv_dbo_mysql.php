<?php
/**
@file mv_dbo_mysql.php
@author Giancarlo Chiappe <gchiappe@qox-corp.com> <gchiappe@outlook.com.pe>
@version 0.0.1.1

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

ExEngine 7 / Libs / MVC-ExEngine / Model Class Variants / DBO MySQL

ExEngine MVC Implementation Library

*/

namespace ExEngine\MVC\DBO {

	class MySQL extends \ExEngine\MVC\Model {

		const VERSION = "0.0.1.1";

		function __construct() {
			parent::__construct();
			if (!$this->checkMySQL())
				$this->ee->errorExit("MVC-ExEngine",
					"This DBO is designed to work with MySQL (or MariaDB).","ExEngine_MVC_Implementation_Library");
		}

		private function checkMySQL() {
			$this->loadDb();
			$dbType = $this->db->type;
			if ($dbType != "mysqli")
				return false;
			else
				return true;
		}

		private function getProperties() {
			$vars = get_object_vars($this);
			unset($vars[$this->INDEXKEY]);
			unset($vars["db"]);
			unset($vars["r"]);
			unset($vars["TABLEID"]);
			unset($vars["INDEXKEY"]);
			unset($vars["SQLAUTOMODE"]);
			unset($vars["ee"]);
			if (isset ($this->EXCLUDEVARS) ) {
				unset($vars["EXCLUDEVARS"]);
				for ($c = 0; $c < count($this->EXCLUDEVARS); $c++) {
					unset($vars[$this->EXCLUDEVARS[$c]]);
				}
			}
			# print_r($vars);
			return $vars;
		}

		public function __toArray() {
			$obj = clone $this;
			unset($obj->db);
			unset($obj->r);
			unset($obj->TABLEID);
			unset($obj->INDEXKEY);
			unset($obj->SQLAUTOMODE);
			if (isset($obj->EXCLUDEVARS))
				unset($obj->EXCLUDEVARS);
			return get_object_vars($obj);
		}

		public function __toString() {
			$obj = clone $this;
			unset($obj->db);
			unset($obj->r);
			unset($obj->TABLEID);
			unset($obj->INDEXKEY);
			unset($obj->SQLAUTOMODE);
			if (isset($obj->EXCLUDEVARS))
				unset($obj->EXCLUDEVARS);
			return print_r($obj,true);
		}

		private function sql_automode() {
			/* mysql edition */

			$this->debug("SQLAUTOMODE RUN!");

			if (!isset($this->TABLEID)) $this->TABLEID = get_class($this);
			$this->loadDb();
			$this->db->open();
			$q = $this->db->query("SHOW COLUMNS FROM " . $this->TABLEID);
			$pkc = 0;
			while ($row = $this->db->fetchArray($q,true,MYSQLI_ASSOC)) {
				$fieldName = $row["Field"];
				$this->$fieldName = null;
				$this->debug($fieldName);
				if ($row["Key"] == 'PRI' && $pkc == 0) {
					if (!isset($this->INDEXKEY)) $this->INDEXKEY = $fieldName;
					$this->debug("ik: " . $fieldName);
					$pkc++;
				}
			}
			if ($pkc==0) {
				if (!isset($this->INDEXKEY)) $this->INDEXKEY = 'id';
			}
			$this->debug($this->__toString());
		}

		final function load($SafeMode=true) {

			if (isset($this->SQLAUTOMODE) && $this->SQLAUTOMODE==true)
				$this->sql_automode();
			else {
			  if (!isset($this->TABLEID)) $this->TABLEID = get_class($this);
			}
			$ik = $this->INDEXKEY;

			if (isset($this->$ik)) {

				if (method_exists($this,'__befload')) {
					$this->__befload();
				}

				$this->loadDb();
				$this->db->open();
				$q = $this->db->query("SELECT * FROM ".$this->TABLEID." WHERE ".$this->INDEXKEY." = '".urlencode($this->$ik)."' LIMIT 1");
				if (!$q) return false;
				if ($this->db->rowCount($q) == 0) return false;
				$data = $this->db->fetchArray($q,$SafeMode,MYSQLI_ASSOC);
				unset($data[$this->INDEXKEY]);
				$keys = @array_keys($data);
				for ($c = 0; $c < count($keys); $c++) {
					$this->$keys[$c] = $data[$keys[$c]];
				}

				if (method_exists($this,'__aftload')) {
					return $this->__aftload();
				} else
				return true;

			} else return false;
		}

		function search($SearchArray=null,$SafeMode=true) {
			if (isset($this->SQLAUTOMODE) && $this->SQLAUTOMODE==true)
				$this->sql_automode();
			else {
			  if (!isset($this->TABLEID)) $this->TABLEID = get_class($this);
			}
			$ik = $this->INDEXKEY;

			$cn = get_class($this);
			$this->loadDb();
			$this->db->open();
			$re = null;
			$o=0;
			if ($SearchArray!=null && is_array($SearchArray))
				$w = $this->db->searchArrayToSQL($SearchArray);
			else return false;
			$q = $this->db->query("SELECT * FROM ".$this->TABLEID. " " . $w);
			if ($q) {
				while ($row = $this->db->fetchArray($q,$SafeMode,MYSQLI_ASSOC)) {
					unset($v);
					$v = new $cn();
					if (method_exists($v,'__befload')) {
						$v->__befload();
					}
					$keys = @array_keys($row);
					for ($c = 0; $c
												< count($keys); $c++) {
						$v->
													$keys[$c] = $row[$keys[$c]];
					}
					if (method_exists($v,'__aftload')) {
						$v->__aftload();
					}
					$re[$o] = &$v;
					$o++;
				}
			} else return false;
			return $re;
		}

		function load_all($WhereArray=null,$SafeMode=true) {
			if (isset($this->SQLAUTOMODE) && $this->SQLAUTOMODE==true)
				$this->sql_automode();
			else {
			  if (!isset($this->TABLEID)) $this->TABLEID = get_class($this);
			}
			$ik = $this->INDEXKEY;

			$cn = get_class($this);
			$this->loadDb();
			$this->db->open();
			$re = null;
			$o=0;
			if ($WhereArray!=null && is_array($WhereArray))
				$w = $this->db->whereArrayToSQL($WhereArray);
			else $w = null;
			$q = $this->db->query("SELECT * FROM ".$this->TABLEID. " " . $w);
			if ($q) {
				while ($row = $this->db->fetchArray($q,$SafeMode,EEDBM_ASSOC)) {
					unset($v);
					$v = new $cn();
					if (method_exists($v,'__befload')) {
						$v->__befload();
					}
					$keys = @array_keys($row);
					for ($c = 0; $c< count($keys); $c++) {
						$v->$keys[$c] = $row[$keys[$c]];
					}
					if (method_exists($v,'__aftload')) {
						$v->__aftload();
					}
					$re[$o] = &$v;
					$o++;
				}
			} else return false;
			return $re;
		}

		function debug($message) {
			$this->ee->debugThis("eemvc-dbo-".get_class($this),$message);
		}

		function load_values($SafeMode=true) {
			if (isset($this->SQLAUTOMODE) && $this->SQLAUTOMODE==true)
				$this->sql_automode();
			else {
			  if (!isset($this->TABLEID)) $this->TABLEID = get_class($this);
			}
			$ik = $this->INDEXKEY;

			$v = $this->getProperties();
			$nnc = 0;
			foreach (array_keys($v) as $ak) {
				if ($v[$ak] == null) unset($v[$ak]); else $nnc++;
			}
			if ($nnc == 0) { $this->debug("eemvcil.php:". __LINE__ . ": load_values() requires at least one property set.");  return false; }


			if (method_exists($this,'__befload')) {
				$this->__befload();
			}

			$this->loadDb();
			$this->db->open();

			$wq = $this->db->whereArrayToSQL($v);

			$q = $this->db->query("SELECT * FROM `".$this->TABLEID."` ".$wq." LIMIT 1");
			if (!$q) return false;
			if ($this->db->rowCount($q) == 0) return false;
			$data = $this->db->fetchArray($q,$SafeMode,MYSQLI_ASSOC);
			unset($data[$this->INDEXKEY]);
			$keys = @array_keys($data);
			for ($c = 0; $c < count($keys); $c++) {
				$this->$keys[$c] = $data[$keys[$c]];
			}
			if (method_exists($this,'__aftload')) {
				return $this->__aftload();
			} else
			return true;
		}

		function load_page($from,$count,$SafeMode=true) {
			if (isset($this->SQLAUTOMODE) && $this->SQLAUTOMODE==true)
				$this->sql_automode();
			else {
			  if (!isset($this->TABLEID)) $this->TABLEID = get_class($this);
			}
			//$ik = $this->INDEXKEY;

			$cn = get_class($this);
			$this->loadDb();
			$this->db->open();
			$re = null;
			//$c=0;
			$o=0;
			$q = $this->db->query("SELECT * FROM `".$this->TABLEID."` LIMIT ".$from." , ".$count);
			if ($q) {
				while ($row = $this->db->fetchArray($q,$SafeMode,EEDBM_ASSOC)) {
					unset($v);
					$v = new $cn();
					if (method_exists($v,'__befload')) {
						$v->__befload();
					}
					$keys = @array_keys($row);
					for ($c = 0; $c < count($keys); $c++) {
						$v->$keys[$c] = $row[$keys[$c]];
					}
					if (method_exists($v,'__aftload')) {
						$v->__aftload();
					}
					$re[$o] = &$v;
					$o++;
				}
			} else return false;
			return $re;
		}

		final function insert($SafeMode=true) {
			if (isset($this->SQLAUTOMODE) && $this->SQLAUTOMODE==true)
				$this->sql_automode();
			else {
			  if (!isset($this->TABLEID)) $this->TABLEID = get_class($this);
			}
			$ik = $this->INDEXKEY;

			if (isset($ik)) {

				if (method_exists($this,'__befinsert')) {
					$this->__befinsert();
				}
				$iarr = $this->getProperties();
				#print_r($iarr);
				$this->loadDb();
				#$this->db->dbgMode = true;
				$this->db->open();

				$r = $this->db->insertArray($this->TABLEID,$iarr,$SafeMode);

				if ($r) {
					$this->$ik = $this->db->InsertedID;
					if (method_exists($this,'__aftinsert')) {
						$this->__aftinsert($r);
					}
					return true;
				} else
				return false;
			}
		}

		final function update($SafeMode=true) {
			/*
			$ik = $this->INDEXKEY;
			if (!isset($this->TABLEID)) $this->TABLEID = get_class($this);*/

			$r = null;

			if (isset($this->SQLAUTOMODE) && $this->SQLAUTOMODE==true)
				$this->sql_automode();
			else {
			  if (!isset($this->TABLEID)) $this->TABLEID = get_class($this);
			}
			$ik = $this->INDEXKEY;

			if (isset($ik)) {
				if (method_exists($this,'__befupdate')) {
					$this->__befupdate();
				}
				$this->loadDb();
				$this->db->open();
				$uarr = $this->getProperties();
				unset($uarr[$ik]);
				$warr = array( $ik => $this->$ik );
				$res = $this->db->updateArray($this->TABLEID,$uarr,$warr,$SafeMode);
				if (method_exists($this,'__aftupdate')) {
					return $this->__aftupdate($r);
				} else
				return $r;
			} else return false;
		}

		final function delete() {
			/*
			$ik = $this->INDEXKEY;
			if (!isset($this->TABLEID)) $this->TABLEID = get_class($this);
			*/
			if (isset($this->SQLAUTOMODE) && $this->SQLAUTOMODE==true)
				$this->sql_automode();
			else {
			  if (!isset($this->TABLEID)) $this->TABLEID = get_class($this);
			}
			$ik = $this->INDEXKEY;
			if (isset($this->$ik)) {
				$this->loadDb();
				$this->db->open();
				$q = $this->db->query("DELETE FROM `".$this->TABLEID."` WHERE `".$ik."` = '".urlencode($this->$ik)."' LIMIT 1");
				$this->$this->INDEXKEY = null;
				return true;
			} else return false;
		}
	}
}
?>