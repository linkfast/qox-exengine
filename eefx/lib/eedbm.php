<?php

# ExEngine 7 / Libs / Database Manager

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

class eedbm {
	
	const VERSION = "0.0.1.12";
	
	public $utf8Mode = false;
	
	public $type;
	public $host;
	public $db;
	public $user;
	public $passwd;
	public $port;
	
	public $InsertedID=0;
	
	public $aDbSettings;
	
	public $connObj;
	public $dbselObj;
	private $connected = false;
	private $ee;
	private $dbgMode = false;
	
	private $edbl_enabled=false;
	private $edbl_driver;
	private $edbl_obj;
	
	private $autoquery;
	
	private $edblPath;
	
	function __construct($parent,$dbObj="default") {
		$this->ee = &$parent;
		$this->settingsParse($dbObj);
		
		$this->edblPath = $this->ee->eePath."eefx/lib/edbl/";
	}
	
	function isConnected() {
		return $this->connected;	
	}
	
	final function setDebugMode($DbgMode) {
		$this->dbgMode = $DbgMode;	
	}
	
	final function open($DebugMode=false,$EDBL_Special=null) {
		$dbg = $DebugMode;
		if (!$this->connected) {
			switch ($this->type) {
				
				#MySQL MODE (LEGACY)
				case "mysql" :					
					$this->connObj  = mysql_connect($this->host,$this->user,$this->passwd);
					$this->dbselObj = mysql_select_db($this->db,$this->connObj);			
					if ( ($this->connObj) && ($this->dbselObj) ) { 			
						$this->connected = true;						
					} else {
						$this->ee->errorWarning("ExEngine 7 : Database Manager 2 : Can not connect to server.");
						$this->ee->debugThis("eedbm","Can not connect to server. Error: ". mysql_errno($this->connObj) . " " . mysql_error($this->connObj));
						$this->connected = false;
					}				
					$r = "<!> ExEngine : Database Manager 2 | Debug mode Enabled, remember to disable this mode in the final revision.\n<br/>";
					$r .= "Status\t\t\t: ".print_r($this->connected,true)." (1=connected, 0=disconnected)\n<br/>";
					$r .= "Database type\t\t: ".$this->type."\n<br/>";
					$r .= "Host\t\t\t: ".$this->host."\n<br/>";
					$r .= "User\t\t\t: ".$this->user."\n<br/>";
					$r .= "Password\t\t: ".$this->passwd."\n<br/>";
					$r .= "Database\t\t: ".$this->db."\n<br/>";
					$r .= "Port\t\t\t: ".$this->port."\n<br/>";							
					$this->ee->debugThis("eedbm","Debug data: ".$r);
					if ($dbg) {
						$this->dbgMode = true ;						
						print "<!-- ". $r . "-->\n";		
					}
					break;
				# LEGACY
				
				case "mysqli":
					$this->connObj = mysqli_connect($this->host,$this->user,$this->passwd);
					$this->dbselObj = mysqli_select_db($this->connObj,$this->db);
					if ($this->connObj && $this->dbselObj) $this->connected = true;
					else {
						$this->ee->errorWarning("ExEngine : Database Manager : Can not connect to database server.");
						$this->ee->debugThis("eedbm","Can not connect to server. Error: ". mysqli_errno($this->connObj) . " " . mysqli_error($this->connObj));
						$this->connected = false;
					}
				break;				
				case "pgsql":
					#PostgreSQL Mode -> TODO
					/*
					$this->connObj  = mysql_connect($this->host,$this->user,$this->passwd);
					$this->dbselObj = mysql_select_db($this->db,$this->connObj);			
					if ( ($this->connObj) && ($this->dbselObj) ) { 			
						$this->connected = true;						
					} else {
						$this->ee->errorWarning("ExEngine 7 : Database Manager 2 : Can not connect to server.");
						$this->connected = false;
					}					
					if ($dbg) {
						$this->dbgMode = true ;
						print "<!-- <!> ExEngine : Database Manager 2 | Debug mode Enabled, remember to disable this mode in the final revision.\n";
						print "Status\t\t\t: ".$this->connected." (1=connected, 0=disconnected)\n";
						print "Database type\t\t: ".$this->type."\n";
						print "Host\t\t\t: ".$this->host."\n";
						print "User\t\t\t: ".$this->user."\n";
						print "Password\t\t: ".$this->passwd."\n";
						print "Database\t\t: ".$this->db."\n";
						print "Port\t\t\t: ".$this->port."\n-->\n";
					}
					*/
					$this->ee->errorExit("ExEngine : Database Manager : PostgreSQL not supported.");
				break;
				default :
					$this->ee->debugThis("eedbm","EDBL Driver Search");
					$edbltype = $this->type;
					if (file_exists($this->edblPath.$edbltype.".edbl.php")) {
						if ($EDBL_Special==null)
								for ($l = 0; $l < 100; $l++)
									$EDBL_Special[$l] = null;	
						
						$this->ee->debugThis("eedbm","Loading: ".$edbltype);
						include_once($this->edblPath.$edbltype.".edbl.php");
						$edbl_classname = "edbl_".$edbltype;
						$edbl_con = eval("return $edbl_classname::_edbl_c;");	
						
						$this->ee->debugThis("eedbm","EDBL Connection Statement: " . $edbl_con);
						
						$this->edbl_obj = eval("return new $edbl_con;");
						$this->connObj = $this->edbl_obj->open();
						$this->edbl_driver = $edbltype;
						$this->edbl_enabled = true;
						if ($this->connObj) {
							$this->connected = true;	
						} else {
							$this->connected = false;
						}
					} else {
						$this->ee->debugThis("eedbm","EDBL Driver not found");
						$this->ee->errorExit("eedbm","Database type not valid, EEDBL driver missing?");	
					}
				break;
			}
		}
	}
	
	# Query Function ($EDBL_Special is an array)	
	function query($Query=null,$AutoQuery=0,$EDBL_Special=null) {
		$q = $Query;
		$aQ = $AutoQuery;
		//return mysql_query($q,$this->connObj);
		
		if ($this->connected) {
			if (isset($q)) {
				$this->ee->debugThis("eedbm","Query: " . $q);
				switch ($this->type) {
					#LEGACY
					case "mysql":
					#UTF8 Compat Mode
					if($this->utf8Mode) 
						mysql_query("SET NAMES 'utf8'",$this->connObj);
					##
					$ret = mysql_query($q,$this->connObj);	
					if (!$ret) {
						return $ret;
					} else {
						if ($aQ == 0)
							return $ret;
						else
							$this->autoquery = $ret;
					}
					break;
					#END LEGACY
					case "mysqli":
						#UTF8 Compat Mode
						if($this->utf8Mode) 
							mysqli_query($this->connObj,"SET NAMES 'utf8'");
						##
						$ret = mysqli_query($this->connObj,$Query);
						if ($aQ==1) $this->autoquery = $ret; else return $ret;					
					break;
					default:
						if ($this->edbl_enabled) {							
							if ($EDBL_Special==null)
								for ($l = 0; $l < 100; $l++)
									$EDBL_Special[$l] = null;							
							$ret = $this->edbl_obj->query($q,$EDBL_Special);
							if ($aQ==1) $this->autoquery = $ret; else return $ret;	
						}
					break;					
				}
			} else {
				$this->ee->errorWarning("ExEngine 7 : Database Manager : Query text should be provided.");	
			}
		} else {
			$this->ee->errorWarning("ExEngine 7 : Database Manager : Database must be connected before using query.<br/><code>". '$eedbmObject->open();</code>');	
		}
		
	}
	
	# Fetching Functions ($EDBL_Special is an array)
	final function fetchArray($QueryObject=null,$SafeMode=true,$ResultType=null,$EDBL_Special=null) {
		$qObj = $QueryObject;
		$rt = $ResultType;
		$rObj;		
		
		if ($this->connected) {
			#autoquery
			if (!isset($qObj)) {
				if (isset($this->autoquery)) {
					$qObj = $this->autoquery;
				} else {
					$this->ee->errorWarning("ExEngine 7 : Database Manager : Query object must be provided,<br/><code>". '$result = $dbObject->fetchArray($queryObject);</code><br/>More help : <a href="http://wiki.aldealinkfast.com/exengine/index.php?title=ExEngine_7:Documentaci%C3%B3n:ExEngine_Database_Manager_(English)" target="_blank">ExEngine Wiki : EE7 : Database Manager</a>');	
				}
			}
			#autoquery end
			
			switch ($this->type) {
				
				#LEGACY
				case "mysql":		
					if (true) {
						if (isset($qObj)) {
							if (!isset($rt)) {
								$rObj =  mysql_fetch_array($qObj);	
							} else {
								$rObj = mysql_fetch_array($qObj,$rt);
							}
						}
					}			
				break;
				#END LEGACY
				
				case "mysqli":
					if (true) {
						if (isset($qObj)) {
							if (!isset($rt)) {
								$rObj =  mysqli_fetch_array($qObj);	
							} else {
								$rObj = mysqli_fetch_array($qObj,$rt);
							}
						}
					}			
				break;
				default:
					if ($this->edbl_enabled) {
						if ($EDBL_Special==null)
								for ($l = 0; $l < 100; $l++)
									$EDBL_Special[$l] = null;
						$rObj = $this->edbl_obj->fetchArray($qObj,$EDBL_Special);
					}
				break;
			}		
					
			if ($SafeMode) {
				if (is_array($rObj)) {
					$aKeys = array_keys($rObj);					
					foreach ($aKeys as $Key) {
						$rObj[$Key] = urldecode($rObj[$Key]);
					}	
					return $rObj;		
				}
			}
			else
			{
				return $rObj;
			}
		} else {
				$this->ee->errorWarning("ExEngine 7 : Database Manager : Database must be connected before using fetchArray,<br/><code>". '$dbObject->open();</code>');
		}
	}
	
	final function errorLatest() {
		if ($this->connected) {
			switch ($this->type) {
				#LEGACY
				case "mysql" :
					return mysql_error($this->connObj);
				break;
				#LEGACY
				case "mysqli":
					return mysqli_error($this->connObj);
				break;
				default:
				
				break;
			}
		}
	}
	
	#EE Query Functions
	final function updateArray($Table,$InputArray,$WhereArray,$SafeMode=true,$AdditonalQuery=null) {
		$WhereStatement = $WhereArray;
		$table = $Table;
		$arr = $InputArray;
		$fQ = $AdditonalQuery;
		$c = count($arr);
		$q = '';
		$wQ='';
		$a = array_keys($arr);
		
		if (!is_array($InputArray) || !is_array($WhereStatement)) {
			$this->ee->errorExit("eedbm->updateArray","Invalid arguments, array for InputArray and WhereStatements are required.");
		}
		
		if ($c > 1) {
			$c1 = 0;			
			while ($c1 < ($c-1)) {
				if ($this->dbFunction($arr[$a[$c1]])) {
					$q .= "`".$a[$c1]."` = ".$arr[$a[$c1]].", ";
				} else {
					if ($SafeMode)
						$q .= "`".$a[$c1]."` = '".urlencode($arr[$a[$c1]])."', ";
					else
						$q .= "`".$a[$c1]."` = '".$arr[$a[$c1]]."', ";						
				}
				$c1++;
			}
			if ($c1 == ($c-1)) {
				if ($this->dbFunction($arr[$a[$c1]])) {
					$q .= "`".$a[$c1]."` = ".$arr[$a[$c1]]." ";
				} else {
					if ($SafeMode)
						$q .= "`".$a[$c1]."` = '".urlencode($arr[$a[$c1]])."' ";
					else
						$q .= "`".$a[$c1]."` = '".$arr[$a[$c1]]."' ";
				}
			}
		} elseif ($c == 1) {
			if ($SafeMode)
				$q = "`".$a[0]."` = '".urlencode($arr[$a[0]])."' ";	
			else
				$q = "`".$a[0]."` = '".$arr[$a[0]]."' ";	
		}
		unset($c,$c1,$a);
		
		$c = count($WhereStatement);
		$a = array_keys($WhereStatement);
		
		if ($c > 0) {
			$c1 = 0;			
			while ($c1 < ($c-1)) {
				if ($SafeMode)
					$wQ .= "`".$a[$c1]."` = '".urlencode($WhereStatement[$a[$c1]])."' AND ";
				else
					$wQ .= "`".$a[$c1]."` = '".$WhereStatement[$a[$c1]]."' AND ";
				$c1++;
			}
			if ($c1 == ($c-1)) {
				if ($SafeMode)
					$wQ .= "`".$a[$c1]."` = '".urlencode($WhereStatement[$a[$c1]])."' ";
				else
					$wQ .= "`".$a[$c1]."` = '".$WhereStatement[$a[$c1]]."' ";
			}
		} elseif ($c == 1) {
			if ($SafeMode)
				$wQ .= "`".$a[0]."` = '".urlencode($WhereStatement[$a[0]])."' ";
			else
				$wQ .= "`".$a[0]."` = '".$WhereStatement[$a[0]]."' ";
		}
		
		unset($c,$c1,$a);
		
		if ($this->dbgMode) {
			print "UPDATE `".$this->db."`.`".$table."` SET ".$q." WHERE ".$wQ.$fQ;
		}		
		
		return $this->query("UPDATE `".$this->db."`.`".$table."` SET ".$q." WHERE ".$wQ.$fQ);
	}
	
	final function insertArray($Table,$InputArray,$SafeMode=true,$AdditonalQuery=null) {
		$table = $Table;
		$arr = $InputArray;
		$fQ = $AdditonalQuery;
		$c = count($arr);
		$q1='';$q2='';
		if ($c > 1) {
			$c1 = 0;
			$a = array_keys($arr);
			while ($c1 < ($c-1)) {
				$q1 .= "`".$a[$c1]."`, ";
				if ($this->dbFunction($arr[$a[$c1]])) {
					$q2 .= $arr[$a[$c1]]." , ";
				} else {
					if ($SafeMode)
						$q2 .= "'".urlencode($arr[$a[$c1]])."', ";
						else
						$q2 .= "'".$arr[$a[$c1]]."', ";
				}
				$c1++;
			}
			if ($c1 == ($c-1)) {
				$q1 .= "`".$a[$c1]."`";
				if ($this->dbFunction($arr[$a[$c1]])) {
					$q2 .= $arr[$a[$c1]];
				} else {
					if ($SafeMode)
					$q2 .= "'".urlencode($arr[$a[$c1]])."'";
					else
					$q2 .= "'".$arr[$a[$c1]]."'";
				}
			}
		} elseif ($c == 1) {
				$a = array_keys($arr);
				$q1 .= "`".$a[0]."`";
				if ($SafeMode)
				$q2 .= "'".urlencode($arr[$a[0]])."'";	
				else
				$q2 .= "'".$arr[$a[0]]."'";	
		}
		
		if ($this->dbgMode) {
			print "INSERT INTO `".$this->db."`.`".$table."` (".$q1.") VALUES (".$q2.")".$fQ;
		}
		$q = $this->query("INSERT INTO `".$this->db."`.`".$table."` (".$q1.") VALUES (".$q2.")".$fQ);
		
		switch ($this->type) {
			#LEGACY
			case "mysql":
				$this->InsertedID = mysql_insert_id($this->connObj);
			break;	
			#LEGACY
			case "mysqli":
				$this->InsertedID = mysqli_insert_id($this->connObj);
			break;
			default:
				if ($this->edbl_enabled)
					$this->InsertedID = $this->edbl_obj->Inserted_Id();
			break;
		}
			
		return $q;
	}
	
	private final function dbFunction($input) {
		switch ($input) {
			case 'CURDATE()':
				return true;
			break;
			case 'NOW()':
				return true;
			break;
			default:
				return false;
			break;	
		}
	}
	
	#Special Fetching Functions
	final function oneRowArray($Query,$SafeMode=true,$ResultType=null) {
		$q = $Query;
		if (!preg_match("/LIMIT 1/i", $q)) {
			$q .= " LIMIT 1";	
		}
		if ($this->connected) {
			$qObj = $this->query($q);
			$fA = $this->fetchArray($qObj,$SafeMode,$ResultType);
			return $fA;
		}
	}
	
	#Count Functions
	final function rowCount($q) {
		if ($this->connected) {
			switch ($this->type) {
				#LEGACY
				case "mysql":
					return mysql_num_rows($q);
					break;
				#LEGACY
				case "mysqli":
					return mysqli_num_rows($q);
					break;
				default:
					if ($this->edbl_enabled)
						return $this->edbl_obj->rowCount($q);
				break;
			}
		}
	}
	
	#Database Close
	final function close() {
		unset($this->autoquery);
		$this->connected = false;
		switch ($this->type) {
			#legacy
			case "mysql":
				return mysql_close($this->connObj);
			#legacy
			case "mysqli":
				return mysqli_close($this->connObj);
		}
		
		
	}
	
	# Settings Parser
	final function settingsParse($dbObj) {
		if ($dbObj == "default") {
			if (isset($this->ee->dbArray))
				$dbObj = $this->ee->dbArray;
			else
				$this->ee->errorExit("ExEngine's Default database array is not available, you can't use EEDM without providing a database array.");
		}
		if (is_array($dbObj)) {
			if (array_key_exists("type",$dbObj)) {
				$this->aDbSettings = $dbObj;
				$this->type = $dbObj["type"];
				$this->host = $dbObj["host"];
				if ($dbObj["type"] != "sqlite") {
					$this->db = $dbObj["db"];
					$this->user = $dbObj["user"];
					$this->passwd = $dbObj["passwd"];
					if(isset($dbObj["utf8mode"]))
						$this->utf8Mode = $dbObj["utf8mode"];
					if ($dbObj["type"] == "pgsql")
						$this->port = $dbObj["port"];
				}
			} else {
				$this->ee->errorWarning("Database settings array is not consistent, settings are not loaded.");
			}
		} else {
			$this->ee->errorWarning("Database settings object is not an array, settings are not loaded.");
		}		
	}
	
	# Object Database Changer
	final function dbChange($dbName) {
			$this->ee->errorExit("Function dbChange not implemented.");
	}
	
	#ExEngine Database Manager Linker
	final function edblFunctionParse($function) {
			$this->ee->errorExit("Function not implemented.");
	}
	
	#UpDump, Array of Querys to Database
	final function upDump($array) {
		$this->ee->errorExit("Function upDump not implemented.");
	}
	
	#Dump, Table to Array of Querys
	final function dump() {
		$this->ee->errorExit("Function dump not implemented.");
	}
}
?>