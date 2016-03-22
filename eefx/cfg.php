<?php
/**
@file cfg.php
@author Giancarlo Chiappe <gchiappe@qox-corp.com> <gchiappe@outlook.com.pe>
@version 0.0.0.6

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

ExEngine 7 Configuration File
Should be edited to match your application/server configuration and paths.
*/

#Default config array :
$ee_config = 
array(
	"http_path" => "/ap/lib/exengine/", # Full path to ExEngine (http://myhost:port/myapp/exengine/) (you can use /myapp/exengine/ also)
	"https_path" => "same", # Full path to ExEngine (SSL) (https://myhost:port/myapp/exengine/) (you can use /myapp/exengine/ also) (Set to "same" to use the http_path value)					   
	"pear_path" => "auto", # Set to "auto" for not changing the default include directory.				   

	"php_timezone" => 'America/Lima', # Supported timezones: http://www.php.net/manual/en/timezones.php

	#ExEngine Commander Password (Not Implemented)
	"eeCPassw" => "prueba1234",

	#ForwardMode (enables ExEngine 6 compatibility, requires ExEngine 6 also installed) (HYBRID APPS ARE NOT RECOMMENDED)
	"forwardmode" => false,

	#Debug mode (start default debugger at http://apphost/exengine_lib_path/eefx/common/debug.php or create a remote debugger):
	"debug" => true,

	#DevGuard (see examples)
	"devguard" => false, #DevGuard enable/disable (enables the lib loading, you must instanciate it for using it).
	"devguard_keys_path" => "", #DevGuard keys files path, full path is required (ex. /var/mykeysfolder/).

	#ExEngine Storage (if using more than one application with the same EE install, please set the $ee->appName variable for each application)
	"storage" => false, #enable/disable support (if enabled, directory check will be done each time storage is used.)
	"storage_path" => "", #Path to the Storage folder, must have full write and read support from script, is not recommended that storage folder is publicly available.
	"storage_check" => true, #Check storage folder at ExEngine construction.

	#Debug/Monitor mode | Intensive database usage mode. (Not implemented)
	"monitor-mode"=>false, #This enables all traffic debugging (read documentation for more info.).
	"monitor-prefix"=>"ee7mon_", #Prefix for database tables in Monitor Mode.
	"monitor-db"=>"default", #Can be a database configuration array, or "default" to use def. database.

	# GetImagesFromDir Supported Images Array (in mime format)
	"GIFD_ValidImages" => array("image/jpeg", "image/gif", "image/png")
);

#Default database array for ExEngine Database Manager:
#If you are using direct connections (driver or PDO) feel free to keep using them, but if you are going to use MVC-ExEngine Database Objects you must set this for MySQL (and PgSQL, SQLite, EDBL in the future).
$ee_ddb = 
array(
	"type" => "mysqli", 
	#     TYPE     |   DESCRIPTION  |    SUPPORT     |                NOTES                 #
	###############|#########################################################################
	# mysql        | MySQL DB.      | PARTIAL/NATIVE | Deprecated, support up to PHP 5.4.9  #
	###############|#########################################################################
	# mysqli       | MySQL DB.      | PARTIAL/NATIVE | Recommended MySQL Driver             #
	#              |                |                | (requires mysqli extension)          #
	###############|#########################################################################
	# pgsql        | PostgreSQL.    | NOT YET/NATIVE |                                      #
	#########################################################################################
	# sqlite       | SQLite (file). | NOT YET/NATIVE | host field specifies the path to db. #
	###############|#########################################################################
	# *edbl_driver | For non-native | PARTIAL/NON-   | EDBL Drivers are stored in           #
	#              | databases.     | NATIVE         | lib/edbl/ name should be provided as #
	#              |                |                | the first part of the filename. (for #
	#              |                |                | db2.edbl.php just write db2 as type) #
	###############|#########################################################################
	# pdo          | For non-native | NOT YET/NON-   | Do not use PDO if there is a NATIVE  #
	#              | PDO database   | NATIVE         | or EDBL driver available.            #
	#              | drivers.       |                |                                      #
	###############|#########################################################################
	#"pdo_url" => "", 
	# NOTE: If you use type => "pdo" the following array keys will be omitted (host,user,passwd,etc) and you must write the pdo url
	# in the "pdo_url" value.
	"host" => "localhost", 
	#NOTE: if type=>"sqlite", set "host" => "path/to/file/file.db" (relative to the exengine core php)
	"user" => "mydbuser", #only mysql/i and pgsql
	"passwd" => "mydbpw", #only mysql/i and pgsql
	"db" => "mydb", #only mysql/i and pgsql
	# "port" => "", #only mysqli and pgsql
	"utf8mode" => true #Enables UTF8 Compatibility Mode				
);
?>
