<?php
# ExEngine 7 / Common / ExCommander 7

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
include_once("../../ee.php");
$arg["ShowSlogan"] = false;
$ee = new exengine($arg);
$ee->eeLoad("ajaxcore2");
$ac2 = new ajaxcore2($ee);

if (isset($_GET['logout'])) {
	session_destroy();
	$thisphp = $ee->miscPhpSelfNoPath();
	header("Location: ".$thisphp);
	exit();
}

if (isset($_POST['comm_passw'])) {
	$passw = $_POST['comm_passw'];
	if ($passw == $ee->configGetParam("eeCPassw")) {
		$_SESSION['EE7COMMANDER_AUTH'] = true;
	} else {
		$_SESSION['EE7COMMANDER_AUTH'] = false;
		$showError = true;
	}
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>ExEngine 7 Extension Manager</title>
<?
$ac2->init();
$ac2->visualWeb_init();
?>
<script type="text/javascript">
function comm_postPw() {
	var val = document.getElementById('comm_passw').value;
	ac2_postwith('<? print $ee->miscPhpSelfNoPath() ?>',{comm_passw:val});
}
</script>
</head>

<body>
<div align="center">
  <p><img src="ee7_th.png" width="223" height="159" alt="ExEngine 7 Logo" /><br/>
  </p>
  <table width="70%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td>
      <? if (!isset($_SESSION['EE7COMMANDER_AUTH']) || !$_SESSION['EE7COMMANDER_AUTH']) { ?>
    <h1><strong>Welcome</strong>! </h1>
    <p>ExEngine Extension manager is a MixedEngine and plugin manager for ExEngine 7, please use EE7-Commander Password to enter.</p>
    <h2>Please enter EE7-Commander Password</h2>
    <div align="center">
    <table width="70%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="100%" height="120" align="center" valign="middle" bgcolor="#FBE2DB">
        <p>
          <? if (isset($showError) && $showError) { ?><br /><span style="font-style:italic;"> Incorrect password, please try again. </span><br/> <? } ?>
          <input type="password" name="comm_passw" id="comm_passw" />
        </p>
        <? $ac2->visualWeb_button("LOGIN","javascript:comm_postPw();"); ?>              
        </td>
      </tr>
    </table>
    </div>
    <? } else { ?>
      <h2>ExEngine 7 Extension Manager</h2>
      <p align="center">Home | Manage Extensions | Configuration | <a href="?logout">Logout ExCommander7</a></p>
      <p>&nbsp;</p>
      <p>&nbsp;</p>
      <p>&nbsp;</p>
    <? } ?>
      </td>
    </tr>
    <tr>
      <td align="center" class="vw_Small">ExEngine Commander 7 (1.0.0.0) | AjaxCore2 VisualWeb Powered</td>
    </tr>
  </table>
  <p>LinkFast<strong><strong>ExEngine</strong>/<? print $ee->miscGetVersion(); ?> </strong></p>
</div>
</body>
</html>