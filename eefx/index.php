<?php
/**
@file index.php
@author Giancarlo Chiappe <gchiappe@qox-corp.com> <gchiappe@outlook.com.pe> <g@gchiappe.com>
@version 1.0.1 @ 11 March 2016

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

ExEngine Index Page

 */
error_reporting(E_ALL);
include_once("../ee.php");

$ee = new \ExEngine\Core();
$jq = new jquery($ee);

if ($ee->cArray["debug"]) {	
	$ee->debugThis("ExEngine Index","Debug mode should be disabled before publishing!.");
	$version = $ee->miscGetVersion()." ".exengine::RELEASE;
} else $version = exengine::V_MAJOR."-".exengine::RELEASE;

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php
$jq->load();
$jq->load_ui("redmond");
?>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>QOX ExEngine Application Framework</title>
<style type="text/css">
<!--
body,td,th {
	font-family: Verdana, Geneva, sans-serif;
	font-size: 12px;
	color: #000;
}

body {
	background-color: #FFF;
}
-->
</style>
<script language="javascript" type="text/javascript" src="js/eejs.js"></script>
</head>
<body>
	<div align="center">
		<p>&nbsp;</p>
		<p> <a href="https://github.com/QOXCorp/exengine" target="_blank"><img src="common/ee7_full.png" alt="QOXExEngine" border="0" /></a><br />
		 QOX<strong>ExEngine/<?php print $version ; ?>
			</strong>
			<?php if ($ee->cArray["debug"]) { ?>
			<span style="color: #F00; font-weight: bold;">(debug-mode enabled)</span>
			<?php } ?>
		</p>
		<?php if (@$_GET['from'] == "ee.php") { ?>
		<div id="alert-fromee7" align="center"
			class="ui-state-highlight ui-corner-all smallButtons"
			style="margin: auto; width: 85%; margin-top: 20px; padding: 0 .7em;">
			<h2>Redirected from ee.php (ExEngine Core)</h2>
			<br /> <strong>ExEngine</strong> is a collection of libraries and
			should be included instead of being called as a webpage or
			application.<br /> <br />
			<button>ok</button>
		</div>
		<?php
		}
		if (@$_GET['from'] == "ppage") {
		?>
		<div id="alert-from-ppage"
			class="ui-state-highlight ui-corner-all smallButtons"
			style="margin: auto; width: 85%; margin-top: 20px; padding: 0 .7em;">
			<h2>Redirected from a Framework directory</h2>
			<br /> <strong>ExEngine</strong> resources should not be browseables.<br />
			<br />
			<button>ok</button>
		</div>
		<?php
		}
		?>
		<div>
		<?php if ($ee->cArray["debug"]) { ?>
			<p>
				<span style="color: #333; font-weight: bold; font-size: 16px;">DEBUG MODE TOOLS:</span><br><br>
				<a href="common/excommander.php">EXENGINE COMMANDER</a><br>
                <a href="common/eema.php">ExEngine Message Agent (EEMA)</a><br>
				<a href="common/eema.php?page=legacy-client">DEBUGGER (EEMA in legacy mode)</a><br>
                <a href="common/manager.php">EXTENSION MANAGER</a>
			</p>
			<p>Note: Menu will be hidden when debug-mode is disabled.</p>
        <?php } ?>
		</div>
	</div>
</body>
</html>