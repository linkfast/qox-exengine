<?php
# ExEngine 7 / Database Manager Linker / PDO

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

class edbl_pdo
{
	#ExEngine Database Linker v1
	const _edbl_c = 'edbl_pdo($this,$this->ee,$this->aDbSettings["pdo"])';
	const _edbl_o = 'open()';
	const _edbl_cl = 'close($this->connObj)';
	
	private $_edbl_cr = 'file_mode';
	
	#EDBL Function List
	private $dbm;
	private $ee;
	
	#PDO Connection String
	private $pdoconn;
	#PDO Object
	private $pdoobj;
	
	function __construct($dbm,$ee,$pdo) {
		$this->dbm = &$dbm;
		$this->ee = &$ee;
		if (isset($pdo["Object"])) {
			$this->pdoobj = &$pdo["Object"];
		} else {
			$this->pdoconn = $pdo["ConnStr"];
			$this->pdoobj = new PDO();
		}
	}
	
	function open() {
		return dbase_open($file,$this->file_mode);
	}
	
	function close($dbLink) {
		return dbase_close($dbLink);
	}
}
?>
