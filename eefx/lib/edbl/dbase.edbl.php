<?php
/**
@file dbase.edbl.php
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

ExEngine 7 / Database Manager Linker / DBASE

-- NOT FINISHED / NOT WORKING --

*/

class edbl_dbase
{
	#EDBL Standard Version
	const _edbl_sv = "1.0.0.0";
	
	#This driver version
	const _edbl_dv = "0.0.1.0";
	
	#Constructor eval code
	const _edbl_c = 'edbl_dbase($this,$this->ee,$this->aDbSettings["file_mode"])';
	
	#Database mode, socket or file_mode
	const _edbl_cr = 'file_mode';
	
	# EDBL DRIVER
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
	
	function close() {
		return dbase_close($this->edb->close());
	}
}
?>
