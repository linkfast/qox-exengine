<?php
/**
@file eendbm.php
@author Giancarlo Chiappe <gch@linkfastsa.com> <gchiappe@outlook.com.pe>
@version 0.0.0.0

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

ExEngine 7 / Libs / ExEngine NoSQL Database Manager

*/

class eendbm {
	private $ee;

	function __contruct() {
		$this->ee = &ee_gi();
	}

	function loadDriver($DriverName) {

	}

	function settingsParse($noSQLDatabaseConfigArray) {

	}

}
?>