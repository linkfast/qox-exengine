<?php
class Testmodel extends eemvc_model_dbo_mysql {

	// Column names as variables (var $ColumnName;...)
	var $id;
	var $name;

	# EEMVC DBO MODEL VARS
	var $TABLEID = "Names"; //Table Name
	var $INDEXKEY = "id"; //Index Column Name
	var $EXCLUDEVARS = array( );
	
	# UNIT TESTING
	function testModel() {
		$tF = new EEUnitTest_Function($this->unitTestCase);		
		$tF->assertEquals("Equal","Equal");
		$tF->Finish();
	}	
}

?>