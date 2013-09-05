<?php
//error_reporting(E_ALL); 
//ini_set("display_errors", 1); 
include_once( '../../ee/ee.php' );

$ee = new exengine(array("SilentMode"=> true));
$ee->eeLoad("unittest");

$ut = new EEUnitTest_Suite();

$mvc = new eemvc_index($ee,"start");
$mvc->prepareUnitTesting();

$ctl = $mvc->prepareController("start");
$model = $mvc->prepareModel($ctl,"testmodel");
//$model2 = $mvc->prepareModel($ctl,"model2");

$ut->addPackage($model);
//$ut->addPackage($model2);

$results = array();
$ut->runTests($results);
?>