<?php
	error_reporting(E_ALL ^ E_NOTICE); 
	ini_set("display_errors", 1); 

	include_once( "../../ee.php" ); // load exengine.

	$ee = new \ExEngine\Core(["SilentMode"=>true]); // initiate exengine.

	$ee->appName = "test_storage"; // appname is required to use EE Storage.

	$storage = new ee_storage("myapp"); // set the subfolder name for this app/script.

	$folder = $storage->getFolder(); // get the folder.

	$file = $folder . "myfile.txt"; // concat. filename.

	// start using.

	// -- write file

	file_put_contents($file, "hello World! :)");

	// --- read written file.

	$dataoffile = file_get_contents($file);

	print $dataoffile;	
?>