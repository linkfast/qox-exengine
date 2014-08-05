<?php
// ExEngine 7 / Debugger 1.1.2 BETA
/*
 This file is part of ExEngine7.

 ExEngine7 is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 ExEngine7 is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with ExEngine7.  If not, see <http://www.gnu.org/licenses/>.
 */
session_start();

if (!isset($cd)) {
	include_once("../../ee.php");
	$ee = new exengine(array("ShowSlogan"=>false));
} else {
	$ee = &$pee;
}

if (isset($_GET['aserver']) && $_GET['aserver']=="true") {
    include_once($ee->miscGetResPath("full")."debug/server.php");
    exit();
}

$jq = new jquery($ee);

if (@!$ee->cArray["debug"]) {
	header("Location: ../");
	exit();
} else {
	$ee->miscMessages("Slogan");

	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?
	$jq->load();
	$jq->load_ui("black-tie");
	?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>ExEngine Debugger</title>
<style type="text/css">
<!--
body,td,th {
	font-family: Verdana, Geneva, sans-serif;
	font-size: 14px;
	color: #CCC;
}

body {
	background-color: #FFF;
	margin-left: 16px;
	margin-top: 16px;
	margin-right: 16px;
	margin-bottom: 16px;
}

a:link {
	color: #D6D6D6;
}

a:visited {
	color: #D6D6D6;
}

a:hover {
	color: #D6D6D6;
}

a:active {
	color: #D6D6D6;
}

.notAvailable {
	color: #666;
}
-->
</style>
<script language="javascript" type="text/javascript">
	var self = '<? print $ee->miscPhpSelfNoPath(); ?>';
	var loc = '<? print $ee->miscGetResPath("http"); ?>';
</script>
<script language="javascript" type="text/javascript"
	src="<? print $ee->miscGetResPath("http"); ?>debug/jq.js"></script>
</head>

<body>
	<p align="center">
		<img src="<? print $ee->miscGetResPath("http")."ee7_full.png"; ?>" />
	</p>
	<div align="center">
		<table width="95%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td colspan="3"><h1>
						<span style="color: #000;">ExEngine Debugger (Beta)</span>
					</h1></td>
			</tr>
			<tr>
				<td colspan="2" bgcolor="#666666">Global settings:</td>
				<td align="center" bgcolor="#666666"><strong>Available applications</strong>
				</td>
			</tr>
			<tr>
				<td colspan="2" align="center" bgcolor="#333333"><p>&nbsp;</p>
					<p>
						<a href="javascript:cleanDebug();">Clean All</a> | <a 
							href="javascript:refreshApps();">Refresh Apps</a> | <span 
							class="notAvailable">Help</span>
					</p>
					<p>&nbsp;</p></td>
				<td width="15%" bgcolor="#333333"><p id="available-apps" align="center">
							<img
								src="<? print $ee->miscGetResPath($mode="http"); ?>debug_loading.gif"
								border="0" align="middle" />
						</p>
				</td>
			</tr>
			<tr>
				<td width="11%" align="center" valign="middle" bgcolor="#3B3B3B"><p>
						<a href="javascript:getMessages();">Refresh</a>
					</p>
					<p>
						<a href="javascript:cleanSelApp();">Clean</a>
					</p>
					<p class="notAvailable">Export</p>
					<p class="notAvailable">Print</p>
					<p>
						<a href="javascript:changeFontSize('messages-app','2');">A</a> / <span 
							style="font-size: 10px"><a 
							href="javascript:changeFontSize('messages-app','-2');">A</a> </span>
					</p></td>
				<td colspan="2" bgcolor="#666666"><div id="messages-app"
						style="font-size: 12px; height: 300px; overflow: auto">
						<p align="center">Please Select an application to see messages.</p>
					</div></td>
			</tr>
			<tr>
				<td colspan="2" align="left" bgcolor="#333333"><span id="status-bar"><img
						src="<? print $ee->miscGetResPath($mode="http"); ?>debug_loading.gif"
						border="0" align="middle" /> Starting application... please
						wait...</span></td>
				<td align="right" bgcolor="#333333">&nbsp;</td>
			</tr>
		</table>
		<p style="color: #000;">
			QOX<strong>ExEngine/<? print $ee->miscGetVersion(); ?> </strong>
		</p>
	</div>
</body>
</html>
<? } ?>