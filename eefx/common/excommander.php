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

include_once("../../ee.php");
$arg["ShowSlogan"] = false;
$ee = new exengine($arg);
$ee->eeLoad("ajaxcore2");
$ac2 = new ajaxcore2($ee);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>ExCommander 7</title>
<?
$ac2->init();
$ac2->visualWeb_init();
?>
<? /*
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
.vw_Buttons {
	font-size: 16px;
	font-weight: bold;
	text-transform: uppercase;
}
.vw_Small {
	font-size: 10px;
	color: #666;
	border-bottom-style: dashed;
	border-bottom-color: #666;
	border-bottom-width: 1px;
}
a:link {
	color: #09C;
}
a:visited {
	color: #09C;
}
a:hover {
	color: #09C;
}
a:active {
	color: #09C;
}
-->
</style>
*/ ?>
<script type="text/javascript">
function comm_genCleanUrl() {
	var val = document.getElementById('cu_input').value;
	ac2_postwith('<? print $ee->miscPhpSelfNoPath() ?>',{act:'getCleanUrl',input:val});
}
</script>
</head>

<body>
<div align="center">
  <p><img src="ee7_th.png" width="223" height="159" alt="ExEngine 7 Logo" /><br/>
  </p>
  <table width="70%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td><h1><strong>Welcome</strong>! </h1>
        <p>ExCommander 7 is a easy to use administration utility, you can install ExtendedEngines, MixedEngines, access to ExEngine 7 Command Line interpreter and some more tools. If you are a ExEngine 6 developer this is something like ExEngine6's Tool-Kit.        </p>
        <h2>Please enter EE7-Commander Password</h2>
        <div align="center">
        <table width="70%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="100%" height="120" align="center" valign="middle" bgcolor="#FBE2DB"><p>
              <input type="password" name="comm_passw" id="textfield4" />
            </p>
            <? $ac2->visualWeb_button("LOGIN","javascript:comm_login();"); ?>              
            </td>
          </tr>
        </table>
        </div>
      <h2>Clean URL Generator</h2>
      <p>This command makes use of exengine::miscURLClean()</p>
      <div align="center">
        <table width="70%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="100%" height="120" align="center" valign="middle" bgcolor="#CCCCCC">
            <p>
            	<input type="text" name="cu_input" id="cu_input" />
            </p>
              <? $ac2->visualWeb_button("Generate","javascript:comm_genCleanUrl();"); ?>
              </td>
          </tr>
        </table>
        <? if ($_POST['act']=="getCleanUrl") { ?>
        <p>&nbsp;</p>
        <table width="70%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="100%" height="120" align="center" valign="middle" bgcolor="#999999">
        <p>Result: '<? print $ee->miscURLClean($_POST['input']); ?>'</p>
        	</td>
          </tr>
        </table>
		<? } ?>
      </div>
      <p>&nbsp;</p>
      <p>&nbsp;</p></td>
    </tr>
    <tr>
      <td align="center" class="vw_Small">ExEngine Commander 7 (1.0.0.0) | AjaxCore2 VisualWeb Powered</td>
    </tr>
  </table>
  <p>LinkFast<strong>ExEngine/<? print exengine::VERSION."-".exengine::RELEASE; ?> </strong></p>
</div>
</body>
</html>