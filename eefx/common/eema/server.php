<?php
/**
 * QOX ExEngine Message Agent Server (formerly Debugger)
 *
 * Programa: Giancarlo Chiappe Aguilar
 * Fecha/Hora: 25/04/14 03:55 PM
 * (C) 2014 Todos los derechos reservados.
 */

/**
@file server.php
@author Giancarlo Chiappe <gchiappe@qox-corp.com> <gchiappe@outlook.com.pe>
@version 2.0.0.1
@section LICENSE

ExEngine is free software; you can redistribute it and/or modify it under the
terms of the GNU Lesser General Public Licence as published by the Free Software
Foundation; either version 2 of the Licence, or (at your opinion) any later version.
ExEngine is distributed in the hope that it will be usefull, but WITHOUT ANY WARRANTY;
without even the implied warranty of merchantability or fitness for a particular purpose.
See the GNU Lesser General Public Licence for more details.

You should have received a copy of the GNU Lesser General Public Licence along with ExEngine;
if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, Ma 02111-1307 USA.

@section DESCRIPTION
ExEngine / ExEngine Message Agent Server
*/
//$ma = new eema("maclient-server",'EE Message Agent ajax server.');

if (isset($_POST['cmd'])) {
    $c = $_POST['cmd'];
} else {
	if (isset($_GET['cmd']))
		$c = $_GET['cmd'];
}

/* legacy debugger */

if ($c == "legacyGetApps") {
    if (isset($_SESSION["exengine-debugger-apps"])) {
        $apps = $_SESSION["exengine-debugger-apps"];
        //$apps = implode("(*)",$apps);
        $res = array("result"=>$apps);
        $ee->debugThis("Debug App Server","All debugging applications names served.");
    } else {
        $res = array("result"=>"Empty.");
        $ee->debugThis("Debug App Server","Debugger Applications list empty.");
    }
    print json_encode($res);
}
if ($c == "legacyCleanAll") {
    $cleaned = $ee->debugCleanAll();
    print json_encode(array("result"=>$cleaned));

    $ee->debugThis("Debug App Server","All data cleaned.");
}
if ($c == "legacyGetMessages") {
    $inApp = $_POST['gApp'];
    $mess = $_SESSION[$inApp];
    if (is_array($mess)) {
        $mess = array_reverse($mess);
        $ee->debugThis("Debug App Server","Messages for $inApp served.");
    }
    if ($mess == null) {
        $mess = "No Messages for $inApp.";
        $ee->debugThis("Debug App Server","No Messages for $inApp.");
    }
    for ($l = 0; $l < count($mess) ; $l++) {
        if (isset($mess[$l]["msg"]))
            $mess[$l]["msg"] = @nl2br($mess[$l]["msg"]);
    }
    $res = array("result"=>$mess);
    print json_encode($res);
}
if ($c == "legacyCleanMessages") {
    $a=$_POST['gApp'];
    $cleaned = $ee->debugClean($_POST['gApp']);
    print json_encode(array("result"=>$cleaned));
    $ee->debugThis("Debug App Server","Messages cleaned for $a.");
}

/* ee message agent */

if ($c == "getApps") {
	//$ma->t("Serving apps.");
	$apps = eema::getApps();
	$errorCount = 0;
	$messagesCount=0;
	foreach ($apps as &$eema_app) {
		$messages = array();
		$messages = eema::getMessages($eema_app['appKey']);
		foreach ($messages as $me) {
			$messagesCount++;
			if ($me['level'] == 'error' || $me['level'] == 'fatal')
				$errorCount++;
		}
		$eema_app['errorCount'] = $errorCount;
		$eema_app['msgCount'] = $messagesCount;
		$messagesCount = 0;
		$errorCount=0;
	}
	print json_encode($apps);
}

if ($c == "getMessages") {
	$appKey = $_POST['appKey'];
	$appData = eema::getAppData($appKey);
	//$ma->t("Serving messages for " . $appData['appShortName'] . ".");
	print json_encode(eema::getMessages($appKey,false,true,true));
}

if ($c == "cleanMessages") {
	$appKey = $_POST['appKey'];
	$eemaObj = eema::getObjFromKey($appKey);
	$eemaObj->clearMessages();
	print json_encode(array("result" => "ok"));
}

if ($c == 'clearApps') {
	eema::clearApps();
	print json_encode(array("result" => "ok"));
}

?>