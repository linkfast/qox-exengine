<?php
include_once("../../ee.php");
$ee=new exengine();
if (isset($_POST['send']) && $_POST['send']  == "1") {		
		$ee->eeLoad("servertalk");
		$st = new servertalk_client($ee,$_POST['sIP'],81,$_POST['sMe']);
		$ans = $st->Talk($_POST['sQS']);
}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>ServerTalk! Utility</title>
</head>

<body>
<form action="servertalk.php" method="post">
<p><img src="<? print $ee->miscGetResPath("http"); ?>ee7_full.png" alt="ExEngine 7 Logo" /></p>
<p><strong>LinkFast ServerTalk Utility</strong></p>
<p>ServerTalk Address : 
  <input name="sIP" type="text" id="sIP" value="<?php if (isset($_POST['sIP'])) { print $_POST['sIP']; } ?>" /> 
  Method: 
  <select name="sMe" id="sMe">
    <option value="POST" selected="selected">POST</option>
    <option value="GET">GET</option>
  </select>
</p>
<p>ServerTalk Query String : 
  <input name="sQS" type="text" id="sQS" value="<?php if (isset($_POST['sQS'])) { print $_POST['sQS']; } ?>" />
</p>
<p>ServerTalk Answer:</p>
<p>
  <textarea name="textarea" id="textarea" cols="45" rows="5"><? if (isset($ans)) { print $ans; } ?></textarea>
  <input name="send" type="hidden" id="send" value="1" />
</p>
<p>
  <input type="submit" name="button" id="button" value="Enviar" />
</p>
</form>
</body>
</html>