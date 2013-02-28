<?
# ExEngine 7 / Libs / Loging Classes

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

class eelog {
	
	private $ee;
	private $application;
	
	private $eedbmObj;
	
	private $eedbmConfig;
	private $tableToUse;
	
	function __construct($parent,$application,$eedbmConfig="default") {
		$this->ee = &$parent;
		$this->application = $application;
		
		if ($eedbmConfig == "default")
			$this->eedbmConfig = $this->ee->dbArray;
		else
		$this->eedbmConfig = $eedbmConfig;	
		
		$this->tableToUse = $this->eedbmConfig["logTable"];
		$this->eedbmObj = new eedbm($this->ee, $this->eedbmConfig);
	}
	
	function logThis($message) {
		
	}
	
	function createLogTable() {
		
	}
	
	function clearLog() {
			
	}
	
	function destroyLogTable() {
		
	}
	
	function loadDefault() {
		
	}
}

?>