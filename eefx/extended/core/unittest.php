<?php

function &eeunit_get_instance() {
	return EEUnitTest_Suite::get_instance();
}

class EEUnitTest_Suite {
	var $ee;
	var $fromCli=false;
	var $RenderMode="html";
	var $TestCases;
	var $TestCasesInst;
	
	var $Asserts=0;
	var $Failures=0;
	var $Total=0;
	
	var $Ttop;
	var $Tbot;
	
	private $noAsk=false;
	
	private static $instance;
	public static function &get_instance()
	{
		return self::$instance;
	}
	
	public final function __construct() {
		global $argv;
		$this->ee = &ee_gi();
		self::$instance =& $this;
		if(defined('STDIN')) {
			$this->fromCli = true;
			$this->RenderMode = "cli";
		} else {
			$this->fromCli = false;
			include_once ( $this->ee->eeResPath().'unittest/template.php' );
			print $this->Ttop;
		}
		$this->TestCases = array();
		if(defined('STDIN')) {
			if (isset($argv)) {
				if (in_array("-y",array_map('strtolower', $argv))) {
					$this->noAsk = true;
				}
				if ($this->noAsk) {
					if (in_array("-ct",array_map('strtolower', $argv))) {
						$this->RenderMode = "colorcli";
					}
				}
			}
			if (!$this->noAsk) {
				echo "ExEngine 7 UTS\tYour terminal support ANSI colors? (y/n): ";
				$handle = fopen ("php://stdin","r");
				$line = fgets($handle);
				if(strtolower(trim($line)) == 'y'){
					$this->RenderMode = "colorcli";
				}
			}
			
		}
		$this->write("<b>ExEngine 7 Unit Testing Suite : Initialized</b>");
	}	
	
	public final function addPackage($testCase) {
		
		$m = get_class_methods($testCase);
		if (!in_array("utStartup",$m) || !in_array("utFinish",$m)) {
			$this->write("<b>ExEngine 7 UTS</b><tab>Class (".get_class($testCase).") is not a valid package for Unit Testing Suite, some methods missing?.");
		} else {			
			$found=0;	
			foreach($this->TestCases as $tc) {
				if ($tc == $testCase) {
					$found = 1;	
					$this->write("<b>ExEngine 7 UTS</b><tab>Package (Class ".get_class($testCase).") is already added, skipping.");
				}
			}
			if ($found==0) {
					$this->TestCases[] = $testCase;
					$this->write("<b>ExEngine 7 UTS</b><tab>Added Package (Class ".get_class($testCase).")");
			}
		}
	}
	
	public final function runTests(&$Results) {
		if (!is_array($Results)) {
			$this->write("ExEngine 7 UTS -> Results variable must be array. (Test Halted).");
			exit;
		} else {
			if($this->fromCli)
				$this->askFromCli();
			$this->write("<br/><b>ExEngine 7 Unit Testing Suite</b>");
			$this->write("<tab>Tests Started (".count($this->TestCases)." Packages)");
			$TCCount=1;
			foreach ($this->TestCases as $tc) {
				$methods=0;
				$c = 0;
				
				$funcs = get_class_methods($tc);
				foreach ($funcs as $f) {
					if ($this->ee->strContains($f,"test")) {
						$methods++;
					}
				}
				$this->write("<br/><b>RUN UNIT TEST CASE</b> : Package ".get_class($tc)." (".$methods." Tests Methods) (Package #".$TCCount."/".count($this->TestCases).")");	
						
				$tc->utStartup();
				$this->TestCasesInst[] = &$tc->unitTestCase;			
				foreach ($funcs as $f) {
					if ($this->ee->strContains($f,"test")) {
						$this->write("<tab><b>Testing: ".str_replace("test","",$f)."</b> (Method ".($c+1)."/".$methods.")");			
						$tc->$f();
						$c++;
					}
				}
				$Results[] = $tc->unitTestCase->Results;
				$this->Asserts += $tc->unitTestCase->Asserts;
				$this->Failures += $tc->unitTestCase->Failures;
				$tc->utFinish();
				$TCCount++;			
			}
			$this->Finish();
		}
		
	}
	
	public final function Finish() {
		
		$this->Total = $this->Asserts + $this->Failures;
		$this->write("<br/><b>GLOBAL SUMMARY:</b>");
		if ($this->Total == $this->Asserts) {			
			$this->write("<b><green>PASSED</green></b>");
			$this->write("ASSERTS: ".$this->Asserts." FAILURES: ".$this->Failures. " TOTAL: ".$this->Total." <b>ASSERTION RATE: ".number_format(($this->Asserts/$this->Total*100),2)."%</b><br/>");
		} else if ($this->Failures>0) {
			$this->write("<b>Result: <red>FAILED</red></b>");
			$this->write("ASSERTS: ".$this->Asserts." FAILURES: ".$this->Failures. " TOTAL: ".$this->Total." <b>ASSERTION RATE: ".number_format(($this->Asserts/$this->Total*100),2)."%</b><br/>");
		}
		if (!$this->fromCli)
			print $this->Tbot;
	}
	
	
	public final function write($Text) {
		echo $this->parseFormat($Text."<br/>");		
	}
	
	public final function parseFormat($Text) {
		$this->ee->eeLoad("utils");
		$utils = new EE_Utilities();
		$utils->TextRenderer->Load("clihtml");		
		return	$utils->TextRenderer->RenderText("clihtml",$this->RenderMode,$Text);		
	}
		
	private function askFromCli() {
		if (!$this->noAsk) {
			echo $this->parseFormat("<b>ExEngine 7 UTS</b><tab>Start Unit Tests? (".count($this->TestCases)." Packages) Type 'y' to continue: ");
			$handle = fopen ("php://stdin","r");
			$line = fgets($handle);
			if(strtolower(trim($line)) != 'y'){
				echo $this->parseFormat("<b>ExEngine 7 UTS</b><tab><red>ABORTED</red><br/>");
				exit;
			}	
		}
		return true;
	}	
}

class EEUnitTest_Case {	
	private $eeu;
	var $CaseName;
	var $Results;
	var $Asserts=0;
	var $Failures=0;
	var $Total=0;
	
	public final function Finish() {
		$this->Total = $this->Asserts + $this->Failures;
		
		if ($this->Total == $this->Asserts) {	
			$this->eeu->write("<tab><b>PACKAGE SUMMARY <green>PASSED</green></b>");	
			$this->eeu->write("<tab>".$this->CaseName);
			$this->eeu->write("<tab>ASSERTS: ".$this->Asserts." FAILURES: ".$this->Failures. " TOTAL: ".$this->Total);
		} else if ($this->Failures>0) {
			$this->eeu->write("<tab><b>PACKAGE SUMMARY <red>FAILED</red></b>");
			$this->eeu->write("<tab>".$this->CaseName);
			$this->eeu->write("<tab>ASSERTS: ".$this->Asserts." FAILURES: ".$this->Failures. " TOTAL: ".$this->Total);
		}
	}
	
	public final function __construct($CaseName) {
		$this->eeu = &eeunit_get_instance();
		$this->CaseName = $CaseName;
		$this->Results = array();
	}	
}

class EEUnitTest_Function {
	
	private $eeu;
	var $FunctionName;
	var $testCase;
	var $Asserts=0;
	var $Failures=0;
	var $Total=0;
	var $c=0;
	
	public final function __construct(EEUnitTest_Case $testCase,$MethodDesc=null) {
		$this->testCase = &$testCase;
		$this->eeu = &eeunit_get_instance();
		if ($MethodDesc==null) $MethodDesc = "-NOT SET-";
		$this->FunctionName = $MethodDesc;
	}
	
	public final function write($Text) {
			$this->eeu->write("<tab><tab><b>MESSAGE: </b>".$Text);
	}
	
	public final function Finish() {
		$this->Total = $this->Asserts + $this->Failures;
		$this->testCase->Asserts += $this->Asserts;
		$this->testCase->Failures += $this->Failures;		
		$this->testCase->Total += $this->Total;
		
		
		if ($this->Total == $this->Asserts) {	
			$this->eeu->write("<tab><tab><b>METHOD SUMMARY <green>PASSED</green></b>");		
			$this->eeu->write("<tab><tab>DESC.: ".$this->FunctionName);
			$this->eeu->write("<tab><tab>ASSERTS: ".$this->Asserts." FAILURES: ".$this->Failures. " TOTAL: ".$this->Total);
		} else if ($this->Failures>0) {
			$this->eeu->write("<tab><tab><b>METHOD SUMMARY <red>FAILED</red></b>");
			$this->eeu->write("<tab><tab>DESC.: ".$this->FunctionName);
			$this->eeu->write("<tab><tab>ASSERTS: ".$this->Asserts." FAILURES: ".$this->Failures. " TOTAL: ".$this->Total);
		}
	}
	
	public final function assertEquals($Expected, $Result, $Title="assertEquals") {
		$this->c++;
		$r = new EEUnitTest_Result($Title);
		if ($Expected == $Result) {			
			$this->Asserts++;
			$r->data = "PASSED";
		} else {
			$this->Failures++;
			$r->data = "FAILURE";
		}
		$this->testCase->Results[] = $r;
		$this->eeu->write("<tab><tab>Test #".$this->c . "<tab>".$Title." : ".
		($r->data == "PASSED" ? "<green>PASSED</green>" : "<red>FAILED</red>") );
	}	
	public final function assertTrue($Condition, $Title = "assertTrue") {
		$this->c++;
		$r = new EEUnitTest_Result($Title);
		if ($Condition) {
			$this->Asserts++;
			$r->data = "PASSED";
		} else {
			$this->Failures++;
			$r->data = "FAILURE";
		}
		$this->testCase->Results[] = $r;
		$this->eeu->write("<tab><tab>Test #".$this->c . "<tab>".$Title." : ".
		($r->data == "PASSED" ? "<green>PASSED</green>" : "<red>FAILED</red>") );
	}
	
}

class EEUnitTest_Result {
	
	var $data;
	var $Title;
	
	function __construct($title=null) {
		$this->Title = $title;	
	}
		
	function renderCli() {
		
	}
	
	function renderHtml() {
		
	}
}

?>