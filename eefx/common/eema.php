<?php
/**
 * QOX ExEngine Message Agent (formerly Debugger)
 *
 * Programa: Giancarlo Chiappe Aguilar
 * Fecha/Hora: 25/04/14 12:24 PM
 * (C) 2014 Todos los derechos reservados.
 */

/**
@file eema.php
@author Giancarlo Chiappe <gchiappe@qox-corp.com> <gchiappe@outlook.com.pe>
@version 2.0.0.3
@section DESCRIPTION
ExEngine / ExEngine Message Agent
 */

if ($CreateSession && !isset($_GET['NoNewSession'])) {
	session_start();
}

define('EEMA_VERSION','2.0.0.3');

if (!isset($cd)) {
    include_once("../../ee.php");
    $ee = new exengine(array("ShowSlogan"=>false));
} else {
    $ee = &$pee;
}

$jq = new jquery($ee);
$emaPath = $ee->miscGetResPath("full")."eema";
$httpResPath = $ee->libGetResPath('bootstrap','http');
$httpResPathFA = $ee->libGetResPath('fontawesome','http');
$httpResPathUS = $ee->libGetResPath('underscore','http');
$httpResPathFonts = $ee->libGetResPath('fonts','http');
$httpCommonPath = $ee->miscGetResPath("http");

if (isset($_GET['server_mode']) && $_GET['server_mode']=="true") {
    include_once($emaPath."/server.php");
    exit();
}

$pageData = null;

if (isset($_GET['page'])) {
    switch ($_GET['page']) {
        case 'legacy-client':
                $emaInc = $emaPath.'/legacy.php';
            break;
        case 'settings':
            $emaInc = $emaPath.'/settings.php';
            break;
        case 'help':
            $emaInc = $emaPath.'/help.php';
            break;
        default:
            $pageData = '<h4>404 Page not found.</h4>';
            break;
    }
} else {
    if (isset($eemaLegacyMode) && $eemaLegacyMode==true) {
        $emaInc = $emaPath.'/legacy.php';
    } else
        $emaInc = $emaPath.'/maclient.php';
}

include_once($ee->miscGetResPath("full").'eema/frontend.phtml');