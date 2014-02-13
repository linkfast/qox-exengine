<?php
// unit testing can also be run in the CLI, to run this script cd to this folder and run: php unit_test_mvc.php

error_reporting(E_ALL ^ E_NOTICE); 
ini_set("display_errors", 1); 
include_once( '../../ee/ee.php' );

$ee = new exengine(array("SpecialMode"=> "MVCOnly"));
$ee->eeLoad("unittest");

$ut = new EEUnitTest_Suite();

$mvc = new eemvc_index("start");
$mvc->prepareUnitTesting(); // prepare MVC lib for UnitTesting (no url parsing, etc.)

$ctl = $mvc->prepareController("start");
$model = $mvc->prepareModel($ctl,"testmodel");
//$model2 = $mvc->prepareModel($ctl,"model2");

$ut->addPackage($model);
//$ut->addPackage($model2);

$results = array();
$ut->runTests($results);
?>