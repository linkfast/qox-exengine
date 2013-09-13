<?php
class Testmodel extends eemvc_model_dbo {

	// Column names as variables (var $ColumnName;...)
	var $id;
	var $name;

	# EEMVC DBO MODEL VARS
	var $TABLEID = "Names"; //Table Name
	var $INDEXKEY = "id"; //Index Column Name
	var $EXCLUDEVARS = array( "unitTest", "unitTestCase" );
	
	# UNIT TESTING VARS
	var $unitTest;	
	var $unitTestCase;
	function utStartup() {
		$this->unitTestCase = new EEUnitTest_Case("TestModel");
	}
	
	function utFinish() {
		$this->unitTestCase->Finish();	
	}
	
	function testModel() {
		$tF = new EEUnitTest_Function($this->unitTestCase);		
		$tF->assertEquals("Equal","Equal");
		$tF->Finish();
	}	
}

?>