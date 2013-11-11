<?php 
$this->Ttop = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=Ubuntu:regular,bold&subset=Latin">
<style>body { font-family: Ubuntu, sans-serif; }</style>
<title>QOX ExEngine Unit Testing</title>
</head>
<body>
<p><img src="'.$this->ee->miscGetResPath('http').'ee7_full_openlogo_jpg.jpg" /></p>
<p><b>MENU</b><br/> <a href="'.$_SERVER["SCRIPT_NAME"].'">Run Tests</a><br/><a href="'.$_SERVER["SCRIPT_NAME"].'?summary">Run Tests and Show Summary</a></p>
<b>Console Output:</b><br/>';
$this->Tbot = '</body>
</html>';