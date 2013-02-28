<?php
/*
	This file is part of ExEngine.
	Copyright � LinkFast Company
	
	ExEngine is free software; you can redistribute it and/or modify it under the 
	terms of the GNU Lesser Gereral Public Licence as published by the Free Software 
	Foundation; either version 2 of the Licence, or (at your opinion) any later version.
	
	ExEngine is distributed in the hope that it will be usefull, but WITHOUT ANY WARRANTY; 
	without even the implied warranty of merchantability or fitness for a particular purpose. 
	See the GNU Lesser General Public Licence for more details.
	
	You should have received a copy of the GNU Lesser General Public Licence along with ExEngine;
	if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, Ma 02111-1307 USA.
*/

#Default config array :
$ee_config = array(
					   "http_path" => "/ee/", # Full path to ExEngine (http://myhost/myapp/exengine/) (you can use /myapp/exengine/ also)
					   "https_path" => "same", # Full path to ExEngine (SSL) (https://myhost/myapp/exengine/) (you can use /myapp/exengine/ also) (Set to "same" to use the http_path value)					   
					   "pear_path" => "auto", # Set to "auto" for not changing the default include directory.				   
					   
					   "php_timezone" => 'America/Lima', # Supported timezones: http://www.php.net/manual/en/timezones.php
					   
					   #ExEngine Commander Password
					   "eeCPassw" => "prueba1234",
					   
					   #ForwardMode (enables ExEngine 6 compatibility, requires ExEngine 6 also installed)
					   "forwardmode" => false,
					   
					   #Debug mode:
					   "debug" => true,
					   
					   #Debug/Monitor mode | Intensive database usage mode. (Not implemented)
					   "monitor-mode"=>false, #This Enabled all traffic debugging (read manual for more info.).
					   "monitor-prefix"=>"ee7mon_", #Prefix for database tables in Monitor Mode.
					   "monitor-db"=>"default", #Can be a database configuration array, or "default" to use def. database.
					   
					   #EEPF Default Configuration (Don't edit if your are not planning to use ExEngine Portal Framework) (Not fully implemented and flagged to be deleted)
					   "EEPF_Theme" => "eeSeven",
					   "EEPF_dbPrefix" => "eepf_",
					   "EEPF_portalIndex" => null,
					   "EEPF_ServerSecurity" => false,
					   "EEPF_portalTitle" => "EEPF Powered Portal | %%LOC%%",
					   "EEPF_DefServer" => "eepfServer.php",
					   "EEPF_themeDefLang" => "en",
					   #Note: EEPF by default will use $ee_ddb (ExEngine default database) as database. / More info about EEPF at ExEngine wiki.
					   
					   # GetImagesFromDir Supported Images Array (in mime format)
						"GIFD_ValidImages" => array("image/jpeg", "image/gif", "image/png")
					   );

#Default database array :
$ee_ddb = array(
					"type" => "mysql", #mysql/pgsql/sqlite/edbl:driver/pdo (for pdo support, uncomment pdo_url in array) (only mysql is supported)
					#"pdo_url" => "", #If you use PDO the following array keys will be omitted (host,user,passwd,etc)
					"host" => "localhost", #sqlite: path/to/file/file.db
					"user" => "myuser", #only mysql and pgsql
					"passwd" => "mypassword", #only mysql and pgsql
					"db" => "mydb", #only mysql and pgsql
					# "port" => "", #only pgsql
					"utf8mode" => true #Enables UTF8 Compatibility Mode
				);

?>