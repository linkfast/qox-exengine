<?php
	include_once( '../../ee.php' );
	$ee = new exengine(array("SilentMode" => true));
	$mvc = new eemvc_index($ee,"start");
	$mvc->SessionMode = true;
	$mvc->start();
?>