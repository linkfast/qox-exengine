<?php

Class Cars {
	var $wheels;
	var $doors;
	var $brand;
	var $model;

	function __construct() {
		$this->setData();
	}

	function setData() {
		$this->wheels = 4;
		$this->doors = 4;
		$this->brand = "Lexus";
		$this->model = "M55";
	}

	// --- UNIT TESTS:
	function testWheelsDoors() {
		$tmethod = new EEUnitTest_Function();

		$tmethod->write(print_r($this,true));
		$tmethod->assertTrue(($this->wheels == $this->doors), "If # of Wheels is equal # of doors, the car is ok.");

		$tmethod->assertEquals($this->brand,"Lexus", "If brand of car is not Lexus, fail. ");


		$tmethod->Finish();
	}

}

?>