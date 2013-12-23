<?php
/**
@file mv_dbo_mongodb.php
@author Giancarlo Chiappe <gchiappe@qox-corp.com> <gchiappe@outlook.com.pe>
@version 0.0.0.2 alpha

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

ExEngine 7 / Libs / MVC-ExEngine / Model Class Variants / DBO MongoDB

ExEngine MVC Implementation Library

*/

class eemvc_model_dbo_mongodb extends eemvc_model {
	
	function __construct() {
		parent::__construct();
		if (!$this->checkMongo())
			$this->ee->errorExit("MVC-ExEngine",
				"MongoDB PHP driver is not installed, cannot continue using MongoDB DBO.","ExEngine_MVC_Implementation_Library");

	}

	private function checkMongo() {
		return class_exists("MongoClient");
	}

	private function getProperties($DeleteNulls=false,$DeleteDefaults=false) {
		$vars = get_object_vars($this);		
		unset($vars["db"]);
		unset($vars["r"]);
		unset($vars["MONGODB"]);
		unset($vars["TABLEID"]);
		unset($vars["INDEXKEY"]);		
		if (isset ($this->EXCLUDEVARS) ) {
			unset($vars["EXCLUDEVARS"]);
			for ($c = 0; $c < count($this->EXCLUDEVARS); $c++) {
				unset($vars[$this->EXCLUDEVARS[$c]]);	
			}
		}		
		if ($DeleteNulls) {
			foreach (array_keys($vars) as $key) {
				if (!isset($vars[$key]))
					unset($vars[$key]);
			}
		}
		if ($DeleteDefaults) {
			$cn = get_class($this);
			$Com = new $cn();
			$com_vars = get_object_vars($Com);		
			unset($com_vars["db"]);
			unset($com_vars["r"]);
			unset($com_vars["MONGODB"]);
			unset($com_vars["TABLEID"]);
			unset($com_vars["INDEXKEY"]);		
			foreach (array_keys($vars) as $key) {
				if (isset($vars[$key])) {
					if ($vars[$key] == $com_vars[$key]) {
						unset($vars[$key]);
					}
				}
			}
		}
		return $vars;
	}

	public function __toArray() {
		$obj = clone $this;
		unset($obj->db);
		unset($obj->r);
		unset($obj->MONGODB);
		unset($obj->TABLEID);
		unset($obj->INDEXKEY);
		if (isset($obj->EXCLUDEVARS))
			unset($obj->EXCLUDEVARS);
		return get_object_vars($obj);
	}

	public function __toString() {
		$obj = clone $this;
		unset($obj->db);
		unset($obj->r);
		unset($obj->MONGODB);
		unset($obj->TABLEID);
		unset($obj->INDEXKEY);
		if (isset($obj->EXCLUDEVARS))
			unset($obj->EXCLUDEVARS);
		return print_r($obj,true);
	}
	
	final function load($SafeMode=true) {	
		if (!isset($this->MONGODB)) return false;	

		if (!isset($this->INDEXKEY)) $this->INDEXKEY = "_mongo_id";				
		if (!isset($this->TABLEID)) $this->TABLEID = get_class($this);
		$ik = $this->INDEXKEY;
		if (isset($this->$ik)) {			
			if (method_exists($this,'__befload')) {
				$this->__befload();	
			}
			try {
				$m = new MongoClient();
				$db = $m->selectCollection($this->MONGODB,$this->TABLEID);
				if ($this->INDEXKEY == '_mongo_id') {
					$find_array = array( 
						"_id" => new MongoId($this->$ik) 
					);
				} else {
					$find_array = array(
						$this->INDEXKEY => url_encode($this->$ik)
					);
				}
				$data = $db->findOne($find_array);
				if ($data != null) {
					if ($this->INDEXKEY == '_mongo_id') {
						$this->$ik = @$data["_id"]->__toString();				
					}
					unset($data["_id"]);
					$keys = @array_keys($data);
					for ($c = 0; $c	< count($keys); $c++) {
						$this->$keys[$c] = $data[$keys[$c]];	
					}
					if (method_exists($this,'__aftload')) {
						return $this->__aftload();	
					} else
					return true;
				} else return false;
			} catch (Exception $e) {
				return false;
			}		
		} else return false;
	}
	/*
	function search($SearchArray=null,$SafeMode=true) {
		if (!isset($this->TABLEID)) $this->TABLEID = get_class($this);
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
*/
	function load_all($WhereArray=null,$SafeMode=true) {
		if (!isset($this->MONGODB)) return false;
		if (!isset($this->INDEXKEY)) $this->INDEXKEY = "_mongo_id";				
		if (!isset($this->TABLEID)) $this->TABLEID = get_class($this);
		$ClassName = get_class($this);	
		$m = new MongoClient();
		$db = $m->selectCollection($this->MONGODB,$this->TABLEID);
		if ($WhereArray!=null && is_array($WhereArray)) {
			if (isset($WhereArray['_id']) && !(get_class($WhereArray['_id']) == "MongoId" )) {
				$WhereArray['_id'] = new MongoId($WhereArray['_id']);
			}
			$mongoResult = $db->find($WhereArray);	
		} else {
			$mongoResult = $db->find();
		}
		$RC = 0;
		$Results = null;
		foreach ($mongoResult as $key => $obj) {
		    $obj['_mongo_id'] = @$obj['_id']->__toString();
		    unset($obj['_id']);
		    $TObj = new $ClassName();
		    if (method_exists($TObj,'__befload')) {
				$TObj->__befload();
			}
			$ObjKeys = @array_keys($obj);
			for ($c = 0; $c< count($ObjKeys); $c++) {
				$TObj->$ObjKeys[$c] = $obj[$ObjKeys[$c]];
			}
			if (method_exists($TObj,'__aftload')) {
				$TObj->__aftload();
			}
			$Results[$RC] = clone $TObj;
			$RC++;
		}
		if ($RC > 0)
			return $Results;
		else
			return false;
		/*


		if (!isset($this->TABLEID)) $this->TABLEID = get_class($this);
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
			while ($row = $this->db->fetchArray($q,$SafeMode,MYSQLI_ASSOC)) {
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
		*/
	}
	
	function debug($message) {
		$this->ee->debugThis("eemvc-dbo-mongodb-".get_class($this),$message);
	}

	function load_values($SafeMode=true,$DeleteDefaults=false) {		
		if (!isset($this->MONGODB)) return false;	
		if (!isset($this->INDEXKEY)) $this->INDEXKEY = "_mongo_id";		
		if (!isset($this->TABLEID)) $this->TABLEID = get_class($this);
		$ik = $this->INDEXKEY;
		if (!isset($this->$ik)) {			
			if (method_exists($this,'__befload')) {
				$this->__befload();	
			}
			try {				
				$m = new MongoClient();
				$db = $m->selectCollection($this->MONGODB,$this->TABLEID);
				$find_array = $this->getProperties(true,$DeleteDefaults);
				//$this->debug(print_r($find_array,true));
				$data = $db->findOne($find_array);

				if ($data != null) {
					if ($this->INDEXKEY == '_mongo_id') {
						$this->$ik = @$data["_id"]->__toString();	
					}

					$this->debug(print_r($data,true));

					unset($data["_id"]);
					$keys = @array_keys($data);
					for ($c = 0; $c	< count($keys); $c++) {
						$this->$keys[$c] = $data[$keys[$c]];	
					}
					if (method_exists($this,'__aftload')) {
						return $this->__aftload();	
					} else
					return true;
				} else return false;
			} catch (Exception $e) {
				$this->debug(print_r($e,true));
				return false;
			}		
		} else return false;
	}
	/*
	function load_page($from,$count,$SafeMode=true) {		
		if (!isset($this->TABLEID)) $this->TABLEID = get_class($this);
		$cn = get_class($this);	
		$this->loadDb();
		$this->db->open();	
		$re = null;
		$c=0;
		$q = $this->db->query("SELECT * FROM `".$this->TABLEID."` LIMIT ".$from." , ".$count);
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
	*/
	final function insert($SafeMode=true) {
		if (!isset($this->MONGODB)) return false;
		if (!isset($this->INDEXKEY)) $this->INDEXKEY = "_mongo_id";	
		if (!isset($this->TABLEID)) $this->TABLEID = get_class($this);
		$ik = $this->INDEXKEY;
		if (!isset($this->$ik)) {
			if (method_exists($this,'__befinsert')) {
				$this->__befinsert();	
			}
			try {
				$m = new MongoClient();
				$db = $m->selectCollection($this->MONGODB,$this->TABLEID);
				$iarr = $this->getProperties(true);
				if($this->INDEXKEY == "_mongo_id")
					unset($iarr[$this->INDEXKEY]);
				$result = $db->insert($iarr,array("w" => 1));
				if ($result["ok"] == 1) {
					if($this->INDEXKEY == "_mongo_id")
						$this->$ik = @$iarr["_id"]->__toString();
					if (method_exists($this,'__aftinsert')) {
						$this->__aftinsert();	
					}
					return true;
				} else
					return false;
			} catch (Exception $e) {
				return false;
			}
		} else 
			return false;	
	}
	
	final function update($SafeMode=true) {
		if (!isset($this->MONGODB)) return false;
		if (!isset($this->INDEXKEY)) $this->INDEXKEY = "_mongo_id";	
		if (!isset($this->TABLEID)) $this->TABLEID = get_class($this);
		$ik = $this->INDEXKEY;

		if (isset($ik)) {
			if (method_exists($this,'__befupdate')) {
				$this->__befupdate();	
			}
			try {
				$m = new MongoClient();
				$db = $m->selectCollection($this->MONGODB,$this->TABLEID);
				$iarr = $this->getProperties(true);

				if($this->INDEXKEY == "_mongo_id") {
					$iarr["_id"] = new MongoId($this->$ik);
					unset($iarr["_mongo_id"]);
				}

				//$this->debug(print_r($iarr,true));
				$result = $db->save($iarr,array("w" => 1));

				if ($result["ok"] == 1) {
					if (method_exists($this,'__aftinsert')) {
						$this->__aftupdate();	
					}
					return true;
				} else
					return false;
			} catch (Exception $e) {
				$this->debug(print_r($e,true));
				return false;
			}
		} else return false;
	}
	
	final function delete() {
		if (!isset($this->MONGODB)) return false;
		if (!isset($this->INDEXKEY)) $this->INDEXKEY = "_mongo_id";		
		if (!isset($this->TABLEID)) $this->TABLEID = get_class($this);
		$ik = $this->INDEXKEY;
		if (isset($this->$ik)) {
			$m = new MongoClient();
			$db = $m->selectCollection($this->MONGODB,$this->TABLEID);
			if($this->INDEXKEY == "_mongo_id")
				$del_array = array( '_id' => new MongoId($this->$ik));
			else
				$del_array = array( $this->INDEXKEY => $this->$ik);
			$db->remove($del_array);
			return true;
		} else return false;
	}
}
?>