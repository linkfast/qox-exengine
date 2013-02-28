<?php

# ExEngine 7 / Libs / Internet Files Manipulation

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

class iFile {
	function Post($url, $data){
			$c = curl_init();
			curl_setopt($c, CURLOPT_URL, $url);
			curl_setopt($c, CURLOPT_POST, true);
			curl_setopt($c, CURLOPT_POSTFIELDS, $data);
			curl_setopt($c, CURLOPT_FOLLOWLOCATION  ,1);
			curl_setopt($c, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
			curl_setopt($c, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL	
			return curl_exec ($c);
			curl_close ($c); 
	}
	
	function Get($url, $data){
		
		
	}
}
?>