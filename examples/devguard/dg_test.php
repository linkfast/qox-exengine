<?php
	error_reporting(E_ALL ^ E_NOTICE); 
	ini_set("display_errors", 1); 

	include_once( "../../ee.php" );

	$ee = new \ExEngine\Core(["SilentMode"=>true]);
	$dg = new ee_devguard();

	$dg->guard("myapp");
?>

<html>
<head>
<?php
	#devguard floating close session button.
	$dg->guard_float_menu();
?>
</head>
<body>
	<h1>ExEngine DevGuard Protected Page/Application</h1>
</body>
</html>s