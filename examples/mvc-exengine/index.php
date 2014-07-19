<?php
	error_reporting(E_ALL ^ E_NOTICE);
	ini_set("display_errors", 1);
	include_once( '../../ee.php' );
	$ee = new exengine(["SpecialMode" => "MVCOnly"]);

	#to use exengine storage, set the appName property.
	//$ee->appName = "myapp";

	#enable session support, by default is not enabled.
	$mvc_session = new eemvc_session();
	$mvc_session->Enabled = true;

	$mvc = new eemvc_index("start", $mvc_session);

	# devguard mode, uncomment to enable (you must create a devguard key before using this)
	/*
	$mvc->dgEnabled = true;
	$mvc->dgKey = "myapp";
	*/

	# rewrite mode enabled (fancy urls), you must use nginx or apache configuration, by default is false.
	//$mvc->rewriteRulesEnabled = true;

	#enable integrated JQuery loading, by default is false.
	//$mvc->jQueryEnabled = true;


	$mvc->start();
?>
