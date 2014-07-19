<?php
class Testmodel extends eemvc_model {

	var $id;
	var $name;

	# UNIT TESTING
	function testModel() {
		$tF = new EEUnitTest_Function($this->unitTestCase);
		$tF->assertEquals("Equal","Equal");
		$tF->Finish();
	}
}

?>
