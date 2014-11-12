<?php
/**
@file db2.edbl.php
@author Giancarlo Chiappe <gch@linkfastsa.com> <gchiappe@gmail.com>
@version 0.0.1.0

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

ExEngine 7 / Database Manager Linker / DB2

-- WORKING --

*/

class edbl_db2
{
	#EDBL Standard Version
	const _edbl_sv = "1.0.0.0";
	
	#This driver version
	const _edbl_dv = "0.0.1.0";
	
	#Constructor eval code
	const _edbl_c = 'edbl_db2($this,$this->ee,$EDBL_Special)';
	
	#Database mode, socket or file_mode
	const _edbl_cr = 'socket';
	
	
	# EDBL DRIVER
	private $dbm;

	private $ee;
		
	function __construct($dbm,$ee,$EDBL_Special) {
		$this->dbm = &$dbm;
		$this->ee = &$ee;	
		
		$this->ee->debugThis("edbl-db2","Created!");
	}
	
	function query($query,$edbl_options=null) {					
		if ($edbl_options[0]==null)
			$edbl_options[0] = array("cursor" => DB_SCROLLABLE);			
		$r = db2_exec($this->dbm->connObj,$query,$edbl_options[0]);		
		db2_commit($this->dbm->connObj);
		return $r;
	}
	
	function fetchArray($query_obj,$edbl_options=null) {		
		return db2_fetch_array($query_obj,$edbl_options[0]);	
	}
	
	function open() {
		$this->ee->debugThis("edbl-db2","Open (".$this->dbm->db.",".$this->dbm->user.",".$this->dbm->passwd.")");
		return db2_connect($this->dbm->db,$this->dbm->user,$this->dbm->passwd);		
	}
	
	function close() {
		return db2_close($this->dbm->connObj);
	}
}
?>
