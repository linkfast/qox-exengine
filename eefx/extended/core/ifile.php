<?

# ExEngine 7 / Extended Libs / iFile

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

// REWRITE CHECKED

ini_set('memory_limit', '1024M');
set_time_limit(0);

function ifile_download($url,$destination) {
	//ALIAS FOR A ExEngine SiX Function.
	return exen_dlfile($url,$destination);
}

function ifile_post($url, $data){
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

function ifile_get($url, $data) {
	$get = file_get_contents($url."?".$data);
	return $get;
}

function ifile_url_to_temp($url,$tempfilename) {
	if (exen_checkvalidurl($url)) {
		$d_file = file_get_contents($url);
  		file_put_contents(exen_temp_dir().$tempfilename, $d_file);
	} else {
		return "iFile Error => URL no valida.";
	}
}

function ifile_gettitle($url) {
	if (exen_checkvalidurl($url)) {
		$file = @ fopen(($url),"r") or die ("No me puedo conectar...");
		$text = fread($file,16384);
		if (preg_match('/<title>(.*?)<\/title>/is',$text,$found)) {
        	return $found[1];
		} else {
    	    return " -- Sin Titulo -- ";
		}
	} else {
		return "iFile Error => URL no valida.";
	}
}
?>