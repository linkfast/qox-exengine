<?
//Mixed Engine v3.1
$meProperties = "testv31|1.0.0.0|NULL|LinkFast/DGS|false|24/11/2009";
$meClassesProvided = "testv3";
$meRequirements = array(
				"Core" => "7.0.0.0",
);

class testv3 {
	
	public $ee;
	
	function __construct($ee) {
		$this->ee = $ee;
		print "HELLO WORLD";
	}
}