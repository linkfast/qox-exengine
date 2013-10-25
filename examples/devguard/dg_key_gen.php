<?php
	error_reporting(E_ALL ^ E_NOTICE); 
	ini_set("display_errors", 1); 

	include_once( "../../ee.php" );

	$ee = new exengine(array("SilentMode"=>true));
	$dg = new ee_devguard();

	#edit the cfg.php file to set the key store folder.
	$dg->guard_gen_keys("myapp");
?>