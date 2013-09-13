<?php
	error_reporting(E_ALL ^ E_NOTICE); 
	ini_set("display_errors", 1); 
	include_once( '../../ee.php' );
	$ee = new exengine(array("SpecialMode" => "MVCOnly"));
	$mvc = new eemvc_index($ee,"start");
	$mvc->SessionMode = true;
	$mvc->start();
?>