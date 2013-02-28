<?php
include_once("../../ee.php");
$ee = new exengine();


$locee_http = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$locee_http = str_replace("eefx/common/setup.php","",$locee_http);

$loc_http = "http://".$locee_http;
$loc_https = "https://".$locee_http;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>ExEngine 7 Setup</title>
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
.optionEE {
	font-size: 16px;
	font-weight: bold;
	text-transform: uppercase;
}
.descrOpt {
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
</style></head>

<body>
<div align="center">
  <p><img src="ee7_th.png" width="223" height="159" alt="ExEngine 7 Logo" /><br/>
  </p>
  <table width="70%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td><h1><strong>Welcome</strong>! </h1>
        <p>This little script will help you to easy configure ExEngine and will give to you an init script for your PHP files.</p>
        <h2>ExEngine 7 Mode</h2>
        <div align="center">
        <table width="70%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="100%" height="45" align="left" valign="middle">
              <p>
                <input name="radio" type="radio" id="radio" value="radio" checked="checked" />
              <span class="optionEE">Real Mode</span><span class="descrOpt"> Select this if your are planning to create only EE7, MEv3.5/4 applications, you can also use <strong>ForwardMode</strong> with this mode, <strong>recommended</strong>.</span></p></td>
          </tr>
          <tr>
            <td height="45" align="left" valign="middle" bgcolor="#BFF1C1">
              <p>
                <input type="radio" name="radio" id="radio2" value="radio" disabled="disabled" />
              <span class="optionEE">Compatibility Mode</span> <span class="descrOpt">You may select this option ONLY if you NEED IT (Please read documentation first), you <strong>cannot use ForwardMode</strong> with this enabled.</span> <strong class="descrOpt">This wil enable EE6CL by default.</strong></p></td>
            </tr>
        </table>
        </div>
        <h2>Core</h2>
        <div align="center">
          <table width="70%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td width="28%" height="45" align="left" valign="middle" class="optionEE">http PATH TO EXENGINE DIRECTORY</td>
              <td width="72%" height="45" align="left" valign="middle"><p>
                <input name="textfield" type="text" id="textfield" value="<? print $loc_http; ?>" /> 
              <span class="descrOpt">i.e. http://www.myapp.com/</span></p></td>
            </tr>
            <tr>
              <td width="28%" height="45" align="left" valign="middle" bgcolor="#BFF1C1" class="optionEE">HTTPS PATH TO EXENGINE DIRECTORY</td>
              <td width="72%" height="45" align="left" valign="middle" bgcolor="#BFF1C1"><p>
                <input name="textfield" type="text" id="textfield" value="<? print $loc_https; ?>" /> 
              <span class="descrOpt">i.e. https://www.myapp.com/ (optional)</span></p></td>
            </tr>
            <tr>
              <td height="45" align="left" valign="middle" bgcolor="#FFFFFF" class="optionEE">pear path</td>
              <td height="45" align="left" valign="middle" bgcolor="#FFFFFF"><p><input type="text" name="textfield9" id="textfield4" /> 
                <span class="descrOpt">(optional) Only when PEAR's path is not in the include path of php.</span></p></td>
            </tr>
            <tr>
              <td height="45" align="left" valign="middle" bgcolor="#BFF1C1" class="optionEE">ee7 commander password</td>
              <td height="45" align="left" valign="middle" bgcolor="#BFF1C1"><input type="text" name="textfield10" id="textfield10" /> 
                <span class="descrOpt">required to use advanced exengine features</span></td>
            </tr>
            <tr>
              <td height="45" align="left" valign="middle" bgcolor="#FFFFFF" class="optionEE">forwardmode controller enabled</td>
              <td height="45" align="left" valign="middle" bgcolor="#FFFFFF"><select name="select2" class="optionEE" id="select2">
                <option value="true">enabled</option>
                <option value="false" selected="selected">disabled</option>
              </select> 
                <span class="descrOpt">this will enable the ForwardMode libraries, cannot be used in COMPATIBILITY MODE (EE6CL comflicts with it).</span></td>
            </tr>
            <tr>
              <td height="45" align="left" valign="middle" bgcolor="#BFF1C1" class="optionEE">debug mode</td>
              <td height="45" align="left" valign="middle" bgcolor="#BFF1C1"><select name="select3" class="optionEE" id="select3">
                <option value="true">enabled</option>
                <option value="false" selected="selected">disabled</option>
              </select>
                <span class="descrOpt">this will enable the debugging mode for ExEngine and Libraries, this not affect the debug functions for applications. Also will show more information in the Commander Menu page. Shoud set to disabled (or false) when publishing application.</span></td>
            </tr>
            <tr>
              <td height="45" align="left" valign="middle" bgcolor="#FFFFFF" class="optionEE">MONITOR MODE</td>
              <td height="45" align="left" valign="middle" bgcolor="#FFFFFF"><select name="select4" class="optionEE" id="select4">
                <option value="true">enabled</option>
                <option value="false" selected="selected">disabled</option>
              </select> 
                <span class="descrOpt">this will enable remote debugging, this will affect the debug functions making them use a database instead of SESSION variables to work, this will ensure that you can debug applications that are running in another computer, this mode should not be used when application is published because uses a lot of database querys to work. Also requires a database (or a table prefix) to work, a EEDBM config array should be provided for its use. </span></td>
            </tr>
            <tr>
              <td height="45" align="left" valign="middle" bgcolor="#BFF1C1" class="optionEE">Time Zone</td>
              <td height="45" align="left" valign="middle" bgcolor="#BFF1C1"><p>
                <input type="text" name="textfield2" id="textfield2" /> 
                <span class="descrOpt">              i.e. America/Lima (<a href="http://php.net/manual/en/timezones.php" target="_blank">PHP Timezones</a>)</span></p></td>
            </tr>
          </table>
        </div>
        <h2>Database Configuration Array Creator</h2>
        <div align="center">
          <table width="70%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td width="28%" height="45" align="left" valign="middle" class="optionEE">TYPE</td>
              <td width="72%" height="45" align="left" valign="middle"><p>
                <input type="text" name="textfield3" id="textfield3" />
              <span class="descrOpt">Embedded: mysql, pgsql and sqlite. Read documentation for other types using EDBL. </span></p></td>
            </tr>
            <tr>
              <td height="45" align="left" valign="middle" bgcolor="#BFF1C1" class="optionEE">HOST</td>
              <td height="45" align="left" valign="middle" bgcolor="#BFF1C1"><input type="text" name="textfield8" id="textfield9" /> 
                <span class="descrOpt">i.e. localhost, mysql.mydomain.com</span></td>
            </tr>
            <tr>
              <td height="45" align="left" valign="middle" class="optionEE">USER</td>
              <td height="45" align="left" valign="middle"><input type="text" name="textfield7" id="textfield8" /> 
                <span class="descrOpt">File-based database don't need this.</span></td>
            </tr>
            <tr>
              <td height="45" align="left" valign="middle" bgcolor="#BFF1C1" class="optionEE">password</td>
              <td height="45" align="left" valign="middle" bgcolor="#BFF1C1"><input type="text" name="textfield6" id="textfield7" />
              <span class="descrOpt">File-based database don't need this.</span></td>
            </tr>
            <tr>
              <td height="45" align="left" valign="middle" class="optionEE">database</td>
              <td height="45" align="left" valign="middle"><input type="text" name="textfield5" id="textfield6" />
              <span class="descrOpt">File-based database don't need this.</span></td>
            </tr>
            <tr>
              <td height="45" align="left" valign="middle" bgcolor="#BFF1C1" class="optionEE">port</td>
              <td height="45" align="left" valign="middle" bgcolor="#BFF1C1"><input type="text" name="textfield4" id="textfield5" />
                <span class="descrOpt">Temporarily only works with PostgreSQL and EDBLs that has enabled this.</span></td>
            </tr>
            <tr>
              <td height="45" align="left" valign="middle" class="optionEE">utf8 mode</td>
              <td height="45" align="left" valign="middle"><p>
                <select name="select" class="optionEE" id="select">
                  <option value="true">enabled</option>
                  <option value="false" selected="selected">disabled</option>
                </select>
                <span class="descrOpt">This only works with MySQL and EDBLs that has enabled this.</span> <span class="descrOpt"><strong>Recommended enableing when using MySQL</strong>, repairs some codification problems.</span></p></td>
            </tr>
          </table>
          <p><strong>NOTE:</strong><br />
          To make this the<strong> DEFAULT DATABASE</strong> copy the generated code to the cfg.php file and <strong>set the variable to $ee_ddb</strong>.</p>
        </div>
        <p>&nbsp;</p>
      <p>&nbsp;</p>
      <p>&nbsp;</p></td>
    </tr>
    <tr>
      <td align="right"><input type="button" name="createConfig" id="createConfig" value="Create Config" />
      <input type="button" name="button" id="button" value="Create INIT Script" /></td>
    </tr>
  </table>
  <p><strong>ExEngine/<? print $ee->miscGetVersion()." ".exengine::RELEASE; ?> </strong></p>
</div>
</body>
</html>