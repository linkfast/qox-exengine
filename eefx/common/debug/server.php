<?
if (isset($_POST['cmd'])) {
	$c = $_POST['cmd'];
}
if ($c == "getApps") {
	if (isset($_SESSION["exengine-debugger-apps"])) {
		$apps = $_SESSION["exengine-debugger-apps"];
		$apps = implode("(*)",$apps);		
		$res = array("result"=>$apps);	
		$ee->debugThis("Debug App Server","All debugging applications names served.");
	} else {
		$res = array("result"=>"Empty.");
		$ee->debugThis("Debug App Server","Debugger Applications list empty.");
	}
	print json_encode($res);	
}
if ($c == "cleanAll") {
	$cleaned = $ee->debugCleanAll();
	print json_encode(array("result"=>$cleaned));
	
	$ee->debugThis("Debug App Server","All data cleaned.");
}
if ($c == "getMessages") {
	$inApp = $_POST['gApp'];
	$mess = $_SESSION[$inApp];
	if (is_array($mess)) {
		$mess = array_reverse($mess);
		$ee->debugThis("Debug App Server","Messages for $inApp served.");
	}
	if ($mess == null) {
		$mess = "No Messages for $inApp.";	
		$ee->debugThis("Debug App Server","No Messages for $inApp.");
	}
    for ($l = 0; $l < count($mess) ; $l++) {        
        if (isset($mess[$l]["msg"]))
            $mess[$l]["msg"] = @nl2br($mess[$l]["msg"]);        
    }
	$res = array("result"=>$mess);
	print json_encode($res);
}
if ($c == "cleanMessages") {
	$a=$_POST['gApp'];
	$cleaned = $ee->debugClean($_POST['gApp']);
	print json_encode(array("result"=>$cleaned));
	$ee->debugThis("Debug App Server","Messages cleaned for $a.");
}
?>