<?php
session_start();
#ExEngine Facebook JavaScript SDK Auto-ChannelCreator

//Include ExEngine 7 Framework
include_once("../../../../ee.php");
//Retrieve original App EE7 Object
$ee = new exengine($_SESSION['eeArjs'],$_SESSION['eeCfgA']);
//Load FB JS SDK MixedEngine
$ee->meLoad("fbjssdk");
//Create object...
$fbj = new fbjssdk($ee);
//Call ChannelServer
$fbj->serveChannel();
?>