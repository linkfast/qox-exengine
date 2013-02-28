<?php

# ExEngine 7 / Libs / ExEngine Query Language (Extends ExEngine Database Manager)

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

class eeql extends eedbm {
	
	const VERSION_QL = "0.0.0.1";
	
	function query($eeql_query, $autoQuery=0) {
		#not implemented yet.
		
	}
	
	function queryarr($eeql_query_array, $autoQuery=0) {
		#not implemented yet.
		$input["Command"] = "SELECT";
		$input["What"] = "*";
		$input["From"] = "mytable";		
	}
	
}

?>