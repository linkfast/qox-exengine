<?php
	error_reporting(E_ALL ^ E_NOTICE); 
	ini_set("display_errors", 1); 
	include_once( '../../ee.php' );
	$ee = new exengine(array("SpecialMode" => "MVCOnly"));

	#to use exengine storage, set the appName property.
	//$ee->appName = "myapp";

	$mvc = new eemvc_index("start");

	#enable session support, by default is false.
	//$mvc->SessionMode = true;

	# devguard mode, uncomment to enable
	/*
	$mvc->dgEnabled = true;
	$mvc->dgKey = "myapp";
	*/

	# rewrite mode enabled, you must use nginx or apache configuration, by default is false.
	//$mvc->rewriteRulesEnabled = true;

	#enable jquery loading, by default is false.
	//$mvc->jQueryEnabled = true;


	$mvc->start();
?>