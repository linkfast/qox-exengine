<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<style type="text/css">
	#logo {
	    width: 800px;
	    height: 600px;    
	    text-align: left;
	    position: fixed;
	    top: 50%;
	    left: 50%;
	    margin-left:-400px;
	    margin-top:-300px;   
	    background-image: url('<?php print $this->ee->libGetResPath("mvc-ee","http"); ?>images/default.png');
	}

	#copy-text {
		padding-top: 600px;
 		text-align: center;
	}

	body {
		background-color: #000;
		background-repeat: repeat; 
		color: #AEAEAE;
		font-family: arial, verdana;		
		font-size: 14px;
	}

	a {
		color: #62A0D5;
		text-decoration: none;
	}
</style>
<title>QOX MVC-ExEngine</title>
</head>
<body>
<div id="logo">	
	<div id="copy-text">
		This site is powered by <a href="http://github.com/QOXCorp/exengine" target="_blank">QOX ExEngine</a> (MVC), please create a new default controller for your site/controllers subfolder.<br/>
		&copy;2013-2014 <a href="http://qox-corp.com/" target="_blank">QOX Corporation</a>
	</div>
</div>
</body>
</html>