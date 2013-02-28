<?php
# ExEngine 7 / Database Manager Linker / Oracle

# NOT 100% IMPLEMENTED

/*
	This file is part of ExEngine7.

    ExEngine7 is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    ExEngine7 is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with ExEngine7.  If not, see <http://www.gnu.org/licenses/>.
*/

class edbl_oracle
{
	#ExEngine Database Linker v1
	const  _edbl_c = 'dblr_dbase($this,$this->ee,$this->aDbSettings["file_mode"])';
	const _edbl_o = 'open()';
	const _edbl_cl = 'close($this->connObj)';
	
	const _edbl_cr = 'file_mode';
	
	#EDBL Function List
	private $dbm;
	private $ee;
	
	private $file_mode;
	
	function __construct($dbm,$ee,$fM=2) {
		$this->dbm = &$dbm;
		$this->ee = &$ee;
		$this->file_mode = $fM;
	}
	
	function open() {
		return dbase_open($file,$this->file_mode);
	}
	
	function close($dbLink) {
		return dbase_close($dbLink);
	}
}
?>
