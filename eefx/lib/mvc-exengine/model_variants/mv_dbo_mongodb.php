<?php
/**
@file mv_dbo_mongodb.php
@author Giancarlo Chiappe <gchiappe@qox-corp.com> <gchiappe@outlook.com.pe>
@version 0.0.0.6 alpha

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

namespace ExEngine\MVC\DBO;

class MongoDB extends \ExEngine\MVC\Model {
    
    const VERSION = "0.0.0.12";
	
	function __construct() {
		parent::__construct();
		if (!$this->checkMongo())
			$this->ee->errorExit("MVC-ExEngine",
				"MongoDB PHP driver is not installed, cannot continue using MongoDB DBO.","ExEngine_MVC_Implementation_Library");
        
        if (defined('EEMVC_DBO_MONGODB_CONSTANT_CHECK')) {
            if (defined('MDB_HOST') && (defined('MDB_USER')  && defined('MDB_PWD') && defined('MDB_AUTHDB'))) {
                $this->MONGODB_HOST = MDB_HOST;
                $this->MONGODB_USER = MDB_USER;
                $this->MONGODB_PWD = MDB_PWD;
                $this->MONGODB_AUTHDB = MDB_AUTHDB;
				$this->log()->t("MongoDB Connection Data",[$this->MONGODB_HOST, $this->MONGODB_USER, $this->MONGODB_PWD, $this->MONGODB_AUTHDB]);
            } elseif (defined('MDB_URL')) {
                $this->MONGODB_URL = MDB_URL;
                $this->log()->t("MongoURL: " . $this->MONGODB_URL);
            }
        } else {
			if (!isset($this->DBC))
				$dbCfg = $this->r->getDbConf();
			else
				$dbCfg = $this->r->getDbConf($this->DBC);
			if ($dbCfg) {
				if ($dbCfg['type']!='mongodb'){
					$this->ee->errorExit('MVC-ExEngine','Specified database configuration is not compatible with MongoDB DBO, please set a compatible database configuration setting the $DBC reserved variable in the model.');
				} else {
					$this->MONGODB_URL = $dbCfg['host'];
                    if (isset($dbCfg['db'])) {
                        $this->MONGODB = $dbCfg['db'];
                    }
				}
			}
		}
	}

	private function checkMongo() {
		return class_exists("MongoClient");
	}

	private function getProperties($DeleteNulls=false,$DeleteDefaults=false) {
		$vars = get_object_vars($this);		
		unset($vars["db"]);
		unset($vars["r"]);
		unset($vars["ee"]);
		unset($vars["MONGODB"]);
        unset($vars["MONGODB_HOST"]);
        unset($vars["MONGODB_USER"]);
        unset($vars["MONGODB_PWD"]);
        unset($vars["MONGODB_AUTHDB"]);
        unset($vars["MONGODB_URL"]);
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
			unset($com_vars["ee"]);
			unset($com_vars["MONGODB"]);
            unset($com_vars["MONGODB_HOST"]);
            unset($com_vars["MONGODB_USER"]);
            unset($com_vars["MONGODB_PWD"]);
            unset($com_vars["MONGODB_AUTHDB"]);
            unset($com_vars["MONGODB_URL"]);
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
    
    private function deleteDefaults() {
        $cn = get_class($this);
        $Com = new $cn();        
        $def_vars = get_object_vars($Com);
        $thi_vars = get_object_vars($this);
        
        unset($def_vars["db"]);
        unset($def_vars["r"]);
		unset($def_vars["ee"]);
        unset($def_vars["MONGODB"]);
        unset($def_vars["MONGODB_HOST"]);
        unset($def_vars["MONGODB_USER"]);
        unset($def_vars["MONGODB_PWD"]);
        unset($def_vars["MONGODB_AUTHDB"]);
        unset($def_vars["MONGODB_URL"]);
        unset($def_vars["TABLEID"]);
        unset($def_vars["INDEXKEY"]);	
        
        foreach (array_keys($def_vars) as $key) {
            if (isset($def_vars[$key])) {
                if ($thi_vars[$key] == $def_vars[$key]) {
                    unset($this->$key);
                }
            }
        }
    }

	public function __toArray() {
		$obj = clone $this;
		unset($obj->db);
		unset($obj->r);
		unset($obj->ee);
		unset($obj->MONGODB);
        unset($obj->MONGODB_HOST);
        unset($obj->MONGODB_USER);
        unset($obj->MONGODB_PWD);
        unset($obj->MONGODB_AUTHDB);
        unset($obj->MONGODB_URL);
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
		unset($obj->ee);
		unset($obj->MONGODB);
        unset($obj->MONGODB_HOST);
        unset($obj->MONGODB_USER);
        unset($obj->MONGODB_PWD);
        unset($obj->MONGODB_AUTHDB);
        unset($obj->MONGODB_URL);
		unset($obj->TABLEID);
		unset($obj->INDEXKEY);
		if (isset($obj->EXCLUDEVARS))
			unset($obj->EXCLUDEVARS);
		return print_r($obj,true);
	}
    
    private function createMongoClient() {
        if (!isset($this->MONGODB_URL) and
            isset($this->MONGODB_HOST) and
            (isset($this->MONGODB_USER) and isset($this->MONGODB_PWD) and isset($this->MONGODB_AUTHDB))
        ) {
            $cfgArr = array('db' => $this->MONGODB_AUTHDB, 'username' => $this->MONGODB_USER, 'password' => $this->MONGODB_PWD);
            $connString = 'mongodb://'.$this->MONGODB_HOST.':'.$this->MONGODB_PWD.'@'.$this->MONGODB_HOST.'/'.$this->MONGODB_AUTHDB;
            $r = new \MongoClient($connString);
        } elseif (isset($this->MONGODB_URL)) {
             $r = new \MongoClient($this->MONGODB_URL);
        } else {
            $r = new \MongoClient();
        }   
        return $r;      
    }
	
	final function load($SafeMode=true, $DeleteDefaults=false) {	
		if (!isset($this->MONGODB)) return false;	

		if (!isset($this->INDEXKEY)) $this->INDEXKEY = "_mongo_id";				
		if (!isset($this->TABLEID)) $this->TABLEID = get_class($this);
		$ik = $this->INDEXKEY;
		if (isset($this->$ik)) {	
            if ($DeleteDefaults) $this->deleteDefaults();
			if (method_exists($this,'__befload')) {
				$this->__befload();	
			}            
			try {                
				$m = $this->createMongoClient();
                
				$db = $m->selectCollection($this->MONGODB,$this->TABLEID);
				if ($this->INDEXKEY == '_mongo_id') {
					$find_array = array( 
						"_id" => new \MongoId($this->$ik)
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
					} else {
						$this->log()->i('load : OK' , $this->__toArray());
						return true;
					}
				} else return false;
			} catch (\Exception $e) {
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
	function count_all($WhereArray=null) {
		if (!isset($this->MONGODB)) return false;
		if (!isset($this->INDEXKEY)) $this->INDEXKEY = "_mongo_id";		
		if (!isset($this->TABLEID)) $this->TABLEID = get_class($this);
		$ClassName = get_class($this);	
		$m = $this->createMongoClient();
		$db = $m->selectCollection($this->MONGODB,$this->TABLEID);
		if ($WhereArray!=null && is_array($WhereArray)) {
			if (isset($WhereArray['_id']) && !(get_class($WhereArray['_id']) == "MongoId" )) {
				$WhereArray['_id'] = new \MongoId($WhereArray['_id']);
			}
			$mongoResult = $db->count($WhereArray);	
		} else {
			$mongoResult = $db->count();
		}
		if ($mongoResult > 0)
			return $mongoResult;
		else
			return 0;
	}

	function load_all($WhereArray=null,$SafeMode=true,$SortArray=null) {
		if (!isset($this->MONGODB)) return false;
		if (!isset($this->INDEXKEY)) $this->INDEXKEY = "_mongo_id";				
		if (!isset($this->TABLEID)) $this->TABLEID = get_class($this);
		$ClassName = get_class($this);	
		$m = $this->createMongoClient();
		$db = $m->selectCollection($this->MONGODB,$this->TABLEID);
		if ($WhereArray!=null && is_array($WhereArray)) {
			if (isset($WhereArray['_id']) && !(get_class($WhereArray['_id']) == "MongoId" )) {
				$WhereArray['_id'] = new \MongoId($WhereArray['_id']);
			}
			if ($SortArray!=null)
				$mongoResult = $db->find($WhereArray)->sort($SortArray);	
			 else 
				$mongoResult = $db->find($WhereArray);	
		} else {
			if ($SortArray!=null)
				$mongoResult = $db->find()->sort($SortArray);
			else
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

	function log() {
		$ma = new \eema("eemvcdbo-mdb-".get_class($this),'MVC-EE DBO (MongoDB) "'.get_class($this).'".');
		return $ma;
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
				$m = $this->createMongoClient();
				$db = $m->selectCollection($this->MONGODB,$this->TABLEID);
				$find_array = $this->getProperties(true,$DeleteDefaults);

				$this->log()->t("load_values : find array",$find_array);

				$data = $db->findOne($find_array);

				if ($data != null) {
					if ($this->INDEXKEY == '_mongo_id') {
						$this->$ik = @$data["_id"]->__toString();	
					}

					//$this->debug()->t("load_values, Loaded Data: " . print_r($data,true));

					unset($data["_id"]);
					$keys = @array_keys($data);
					for ($c = 0; $c	< count($keys); $c++) {
						$this->$keys[$c] = $data[$keys[$c]];	
					}
					if (method_exists($this,'__aftload')) {
						return $this->__aftload();	
					} else {
						$this->log()->i('load_values : OK' , $this->__toArray());
						return true;
					}
				} else return false;
			} catch (\Exception $e) {
				$this->log()->e(print_r($e,true));
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
		//if (!isset($this->$ik)) {
			if (method_exists($this,'__befinsert')) {
				$this->__befinsert();	
			}
			try {
				$m = $this->createMongoClient();
				$db = $m->selectCollection($this->MONGODB,$this->TABLEID);
				$iarr = $this->getProperties(true);
				if($this->INDEXKEY == "_mongo_id") {
                    if (get_class($this->_mongo_id) == "MongoId"){
                        $iarr['_id'] = $this->mongo_id;
                    } else {
                        if (strlen($this->_mongo_id) > 0) {
                            $iarr['_id'] = new \MongoId($this->_mongo_id);
                        } else {
                             unset($iarr[$this->INDEXKEY]);
                        }
                    }
                }
				$result = $db->insert($iarr,array("w" => 1));
				if ($result["ok"] == 1) {
					if($this->INDEXKEY == "_mongo_id")
						$this->$ik = @$iarr["_id"]->__toString();
					if (method_exists($this,'__aftinsert')) {
						$this->__aftinsert();	
					}
					return true;
				} else {
                    $this->log()->e(print_r($result,true));
					return false;
                }
			} catch (\Exception $e) {
                //$this->debug(print_r($m,true));
                $this->log()->e(print_r($e,true));
				return false;
			}
		//} else
		//	return false;
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
				$m = $this->createMongoClient();
				$db = $m->selectCollection($this->MONGODB,$this->TABLEID);
				$iarr = $this->getProperties(true);

				if($this->INDEXKEY == "_mongo_id") {
					$iarr["_id"] = new \MongoId($this->$ik);
					unset($iarr["_mongo_id"]);
				}

				//$this->debug(print_r($iarr,true));
				$result = $db->save($iarr,array("w" => 1));

				if ($result["ok"] == 1) {
					if (method_exists($this,'__aftupdate')) {
						$this->__aftupdate();	
					}
					return true;
				} else
					return false;
			} catch (\Exception $e) {
				$this->log()->e(print_r($e,true));
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
			$m = $this->createMongoClient();
			$db = $m->selectCollection($this->MONGODB,$this->TABLEID);
			if($this->INDEXKEY == "_mongo_id")
				$del_array = array( '_id' => new \MongoId($this->$ik));
			else
				$del_array = array( $this->INDEXKEY => $this->$ik);
			$db->remove($del_array);
			return true;
		} else return false;
	}
}
?>