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
<title>ExEngine Setup</title>
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
  <p><img src="ee7_th.png" width="223" height="159" alt="ExEngine 7 Logo" /></p>
  <p><strong>CONCEPT OF EXENGINE 7 SETUP</strong></p>
  <p><strong>== NOT WORKING EXAMPLE ==</strong><br/>
  </p>
  <table width="70%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td><h1><strong>Welcome</strong>! </h1>
        <p>This little script will help you to easy configure ExEngine and will give to you an init script for your PHP files.</p>
        <h2>ExEngine 7 Mode</h2>
        <div align="center">
        <table width="70%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td height="45" colspan="2" align="left" valign="middle">
              <p>
                <input name="radio" type="radio" id="radio" value="radio" checked="checked" />
              <span class="optionEE">Real Mode</span><span class="descrOpt"> Select this if your are planning to create only EE7, MEv3.5/4 applications, you can also use <strong>ForwardMode</strong> with this mode, <strong>recommended</strong>.</span></p></td>
            </tr>
          <tr>
            <td height="45" colspan="2" align="left" valign="middle" bgcolor="#BFF1C1">
              <p>
                <input name="radio" type="radio" disabled="disabled" id="radio2" value="radio" />
              <span class="optionEE">Compatibility Mode</span> <span class="descrOpt">You may select this option ONLY if you NEED IT (Please read documentation first), you <strong>cannot use ForwardMode</strong> with this enabled.</span> <strong class="descrOpt">This wil enable EE6CL by default.</strong></p></td>
            </tr>
          <tr>
            <td height="45" colspan="2" align="left" valign="middle" bgcolor="#FFFFFF" ><input type="checkbox" name="checkbox" id="checkbox" />
              <span class="optionEE">load jquery</span><span class="descrOpt">Check to load jquery.</span> </td>
            </tr>
          <tr>
            <td width="28%" height="45" align="left" valign="middle" bgcolor="#BFF1C1" class="optionEE">silentmode</td>
            <td width="72%" align="left" valign="middle" bgcolor="#BFF1C1" class="descrOpt"><select name="select5" class="optionEE" id="select5">
              <option value="true">enabled</option>
              <option value="false" selected="selected">disabled</option>
            </select>
              Supresses all warnings and slogans written to the code of the rendered html script.</td>
          </tr>
        </table>
        <p>
          <input type="button" name="button" id="button" value="Create INIT Script" />
        </p>
        </div>
        <h2>Core Configuration Array Creator</h2>
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
                <span class="descrOpt">this will enable the ForwardMode libraries, cannot be used in COMPATIBILITY MODE (EE6CL conflicts with it).</span></td>
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
                <span class="descrOpt">this will enable remote debugging, this will affect the debug functions making them use a database instead of SESSION variables to work, this will ensure that you can debug applications that are running in another computer, this mode should not be used when application is published because uses a lot of database querys to work. Also requires a database (or a table prefix) to work, a EEDBM connection array should be provided for its use. </span></td>
            </tr>
            <tr>
              <td height="45" align="left" valign="middle" bgcolor="#BFF1C1" class="optionEE">Time Zone</td>
              <td height="45" align="left" valign="middle" bgcolor="#BFF1C1"><p>
                <input type="text" name="textfield2" id="textfield2" /> 
                <span class="descrOpt">              i.e. America/Lima (<a href="http://php.net/manual/en/timezones.php" target="_blank">PHP Timezones</a>)</span></p></td>
            </tr>
          </table>
          <p><strong>NOTE:</strong><br /> 
          You should copy the generated configuration script into eefx/cfg.php for default usage<strong></strong>.</p>
          <p>
            <input type="button" name="createConfig" id="createConfig" value="Create Config Script" />
          </p>
        </div>
        <h2>Database Connection Array Creator</h2>
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
          <p>
            <input type="button" name="createConfig2" id="createConfig2" value="Create EEDBM Connection Array" />
          </p>
        </div>
        <h2>ExEngine MVC Implementation INIT Creator</h2>
        <div align="center">
          <table width="70%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td height="45" align="left" valign="middle" bgcolor="#BFF1C1" class="optionEE">index file name</td>
              <td height="45" align="left" valign="middle" bgcolor="#BFF1C1"><input name="textfield12" type="text" id="textfield16" value="index.php" />
              <span class="descrOpt">Set the file name of the app starting file, i.e. index.php. (the generated code by this tool will be in &quot;that&quot; file)</span></td>
            </tr>
            <tr>
              <td width="28%" height="45" align="left" valign="middle" class="optionEE">Startup controller name</td>
              <td width="72%" height="45" align="left" valign="middle"><p>
                <input name="textfield11" type="text" id="textfield11" value="startup" />
                <span class="descrOpt">Set the name to the default controller class name.</span></p></td>
            </tr>
            <tr>
              <td height="45" align="left" valign="middle" bgcolor="#BFF1C1" class="optionEE">Views folder</td>
              <td height="45" align="left" valign="middle" bgcolor="#BFF1C1"><input name="textfield11" type="text" id="textfield12" value="views/" />
                <span class="descrOpt">i.e. views, relative to the root of the application.</span></td>
            </tr>
            <tr>
              <td height="45" align="left" valign="middle" class="optionEE">controllers folder</td>
              <td height="45" align="left" valign="middle"><input name="textfield11" type="text" id="textfield13" value="controllers/" />                
                <span class="descrOpt">i.e. controllers, relative to the root of the application.</span></td>
            </tr>
            <tr>
              <td height="45" align="left" valign="middle" bgcolor="#BFF1C1" class="optionEE">models folder</td>
              <td height="45" align="left" valign="middle" bgcolor="#BFF1C1"><input name="textfield11" type="text" id="textfield14" value="models/" />
              <span class="descrOpt">i.e. models, relative to the root of the application.</span></td>
            </tr>
            <tr>
              <td height="45" align="left" valign="middle" class="optionEE">static content folder</td>
              <td height="45" align="left" valign="middle"><input name="textfield11" type="text" id="textfield15" value="static/" />
                <span class="descrOpt">i.e. static, relative to the root of the application.</span></td>
            </tr>
            <tr>
              <td height="45" align="left" valign="middle" bgcolor="#BFF1C1" class="optionEE">session mode</td>
              <td height="45" align="left" valign="middle" bgcolor="#BFF1C1"><select name="select6" class="optionEE" id="select6">
                <option value="true" selected="selected">enabled</option>
                <option value="false">disabled</option>
              </select>                
                <span class="descrOpt">Enable the use of sessions in the MVC application.</span></td>
            </tr>
            <tr>
              <td height="45" align="left" valign="middle" class="optionEE">silent mode</td>
              <td height="45" align="left" valign="middle"><select name="select7" class="optionEE" id="select7">
                <option value="true">enabled</option>
                <option value="false" selected="selected">disabled</option>
              </select>
              <span class="descrOpt">Supresses all warnings and slogans in the rendered html code.</span></td>
            </tr>
            <tr>
              <td height="45" align="left" valign="middle" bgcolor="#BFF1C1" class="optionEE">jquery ui theme</td>
              <td height="45" align="left" valign="middle" bgcolor="#BFF1C1"><input name="textfield13" type="text" id="textfield17" value="base" />
              <span class="descrOpt">Set the JQuery UI theme to use with the embedded jquery of the MVC implementation.</span></td>
            </tr>
            <tr>
              <td height="45" align="left" valign="middle" bgcolor="#FFFFFF" class="optionEE">error handler controller</td>
              <td height="45" align="left" valign="middle" bgcolor="#FFFFFF"><input name="textfield14" type="text" id="textfield18" value="myerrorcontroller" />
              <span class="descrOpt">Set the name of the controller that will handle errors like 404, 403, etc.</span></td>
            </tr>
          </table>
          <p>
            <input type="button" name="button2" id="button2" value="Create MVC INIT Script" />
          </p>
        </div></td>
    </tr>
    <tr>
      <td align="right">&nbsp;</td>
    </tr>
  </table>
  <p>QOX<strong>ExEngine/<? print $ee->miscGetVersion()." ".exengine::RELEASE; ?> </strong></p>
</div>
</body>
</html>