<?php
	error_reporting(E_ALL ^ E_NOTICE); 
	ini_set("display_errors", 1); 

	include_once( "../../ee.php" ); // load exengine.

	$ee = new exengine(array("SilentMode"=>true)); // initiate exengine.

	
	//----

	$ee->eeLoad("unittest");

	$uts = new EEUnitTest_Suite();

	include_once ( 'cars.php' );

	$cars = new Cars();

	$uts->addPackage($cars);

	$resu = array();
	$uts->runTests($resu);

?>