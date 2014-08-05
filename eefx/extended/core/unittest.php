<?php
/**
@file unittest.php
@author Giancarlo Chiappe <gchiappe@qox-corp.com> <gchiappe@outlook.com.pe>
@version 1.0.0.1

@section LICENSE

ExEngine is free software; you can redistribute it and/or modify it under the
terms of the GNU Lesser Gereral Public Licence as published by the Free Software
Foundation; either version 2 of the Licence, or (at your opinion) any later version.
ExEngine is distributed in the hope that it will be usefull, but WITHOUT ANY WARRANTY;
without even the implied warranty of merchantability or fitness for a particular purpose.
See the GNU Lesser General Public Licence for more details.

You should have received a copy of the GNU Lesser General Public Licence along with ExEngine;
if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, Ma 02111-1307 USA.

@section DESCRIPTION

ExEngine Unit Testing Suite Classes

@section TODO



*/

function &eeunit_get_instance() {
	return EEUnitTest_Suite::get_instance();
}

function &eeunit_get_case_instance() {
	return EEUnitTest_Case::get_instance();	
}

class EEUnitTest_ResultReader {
	var $ee;
	var $Results;
	var $RenderMode="html";
	var $fromCli=false;
	var $Ttop;
	var $Tbot;
	var $utils;

	function __construct($Data) {
		global $argv;
		$this->ee = &ee_gi();
		$this->ee->eeLoad("utils");
		$this->utils = new \ExEngine\Extended\Utils\Utilities();
		$this->utils->TextRenderer->Load("clihtml");	
		if(defined('STDIN')) {
			$this->fromCli = true;
			$this->RenderMode = "cli";
			$this->write("<b>Sorry No Console Support Yet :(</b>");
			exit();
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
				echo "ExEngine 7 UTS Result Reader\tYour terminal support ANSI colors? (y/n): ";
				$handle = fopen ("php://stdin","r");
				$line = fgets($handle);
				if(strtolower(trim($line)) == 'y'){
					$this->RenderMode = "colorcli";
				}
			}
		} else {
			$this->fromCli = false;
			include_once ( $this->ee->eeResPath().'unittest/template.php' );
			print $this->Ttop;
		}
		$this->write("<b>ExEngine 7 Unit Testing Suite Reader : Initialized</b>");
		$this->Results = unserialize(base64_decode($Data));
		$this->write("<b>ExEngine 7 Unit Testing Suite Reader : Data Loaded: <green>OK</green></b><br/>");
		if (!$this->fromCli) {
			$RR = $this->Results;
			include_once ( $this->ee->eeResPath().'unittest/summary.phtml' );
			print $this->Tbot;
		}
	}
	public final function write($Text) {
		echo $this->parseFormat($Text."<br/>");		
	}
	
	public final function parseFormat($Text) {				
		return	$this->utils->TextRenderer->RenderText("clihtml",$this->RenderMode,$Text);		
	}
}

class EEUnitTest_Suite {
	var $ee;
	var $fromCli=false;
	var $RenderMode="html";
	var $TestCases;
	var $TestCasesInst;
	var $CliShowReportData=false;
	
	var $Asserts=0;
	var $Failures=0;
	var $Total=0;
	
	var $Ttop;
	var $Tbot;

	var $Results;
	var $ActualMethod;
	var $ActualCase;

	const VERSION = "0.0.0.4";
	
	private $noAsk=false;
	private $utils;
	
	var $tc_timer;
	
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
				if (in_array("--help",array_map('strtolower', $argv))) {
					echo "\nExEngine 7 Unit Testing Suite Version ".self::VERSION. "\n";
					echo "Arguments: \n";
					echo "\t-y\tNo questions before testing.\n\n";
					echo "If -y is enabled, you can use these arguments too:\n";
					echo "\t-ct\tEnables Console Coloring (only if you have a color capable terminal).\n";
					echo "\t-srd\tShows the Report Result Data after all tests are done.\n\n";
					exit();
				}	
				if (in_array("-y",array_map('strtolower', $argv))) {
					$this->noAsk = true;
				}				
				if ($this->noAsk) {
					if (in_array("-ct",array_map('strtolower', $argv))) {
						$this->RenderMode = "colorcli";
					}
					if (in_array("-srd",array_map('strtolower', $argv))) {
						$this->CliShowReportData =true;
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
				echo "ExEngine 7 UTS\tDo you want to get the Report Result Data after all tests are finished? (y/n): ";
				$handle = fopen ("php://stdin","r");
				$line = fgets($handle);
				if(strtolower(trim($line)) == 'y') {
					$this->CliShowReportData = true;
				}
			}
			
		}
		$this->ee->eeLoad("scripttimer");
		$st = new scripttimer();
		$st->load();
		$this->ee->eeLoad("utils");
		$this->utils = new \ExEngine\Extended\Utils\Utilities();
		$this->utils->TextRenderer->Load("clihtml");
		$this->Results = new EEUnitTest_Result("SUITE","Suite");
		$this->Results->SubResults = array();
		$this->write("<b>ExEngine 7 Unit Testing Suite : Initialized</b>");
	}	
	
	public final function addPackage($testCase) {				
			$found=0;	
			foreach($this->TestCases as $tc) {
				if ($tc == $testCase) {
					$found = 1;	
					$this->write("<b>ExEngine 7 UTS</b><tab><yellow><b>WARNING</b></yellow><tab>Package (Class ".get_class($testCase).") is already added, skipping.");
				}
			}
			$m = get_class_methods($testCase);
			$fc = 0;
			foreach ($m as $item)
			{
				$pos = strpos($item, "test");
			   	if ($pos !== false)
			    {
			    	if ($pos == 0)
			       		$fc++;			    	
			    }
			}
			if ($fc == 0)
				$this->write("<b>ExEngine 7 UTS</b><tab><yellow><b>WARNING</b></yellow><tab>Package (Class ".get_class($testCase).") is not a valid package for Unit Testing Suite, test methods missing? (Package Skipped)");
			else
				if ($found==0) {
					$this->TestCases[] = $testCase;
					$this->write("<b>ExEngine 7 UTS</b><tab>Added Package (Class ".get_class($testCase).")");
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
			if (count($this->TestCases) != 0)
				$this->write("<tab>Tests Started (".count($this->TestCases)." Packages)");
			else
				$this->write("<tab>No packages found, testing skipped.");
			$TCCount=1;
			
			$global_timer = new CA_Timer();
			$global_timer->start();
				
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
				$this->ActualCase = get_class($tc) ;
				$this->tc_timer = new CA_Timer();
				$this->tc_timer->start();
				new EEUnitTest_Case();	
				$this->TestCasesInst[] =& eeunit_get_case_instance();
				$tci =& eeunit_get_case_instance();
				foreach ($funcs as $f) {
					if ($this->ee->strContains($f,"test")) {
						
						$this->write("<tab><b>Testing: ".str_replace("test","",$f)."</b> (Method ".($c+1)."/".$methods.")");			
						$this->ActualMethod = str_replace("test","",$f);

						try {
						    $tc->$f();
						} catch (Exception $e) {
						    $this->write("<red><b>Exception occurred in ".$this->ActualMethod."</b></red>: " .  $e->getMessage());
						}
						
						$c++;
						
					}
				}
				$this->tc_timer->stop();
				$tci->Results->UsedTime = $this->tc_timer->get(CA_Timer::MILLISECONDS);
				$this->Results->PassedMethods += $tci->Results->PassedMethods;
				$this->Results->FailedMethods += $tci->Results->FailedMethods;
				$this->Results->PassedAsserts += $tci->Results->PassedAsserts;
				$this->Results->FailedAsserts += $tci->Results->FailedAsserts;
				$tci->Finish();
				$this->Results->SubResults[] = $tci->Results;
				$TCCount++;
			}
			$global_timer->stop();
			$this->Finish($global_timer);
		}
		
	}
	
	public final function Finish(&$gt) {
		$time = $gt->get(CA_Timer::MILLISECONDS);
		
		$this->Results->UsedTime = $time;
		$this->Asserts = $this->Results->PassedPackages;
		$this->Failures = $this->Results->FailedPackages;

		$this->Total = $this->Results->PassedPackages + $this->Results->FailedPackages;
		$this->write("<br/><b>GLOBAL SUMMARY:</b>");
		if ($this->Total == 0) {
			$this->write("<b><green>NO TESTS MADE</green></b>");
		} else {
			if ($this->Total == $this->Asserts) {			
				$this->write("<b><green>PASSED</green></b>");
				$this->write("ASSERTS: ".$this->Results->PassedPackages." FAILURES: ".$this->Results->FailedPackages. " TOTAL: ".$this->Total." <b>ASSERTION RATE: ".number_format(($this->Asserts/$this->Total*100),2)."%</b>");
				$this->write("TOTAL TIME: ".$time.'ms<br/>');
				$this->Results->ResultInfo = "PASSED";
			} else if ($this->Failures>0) {
				$this->write("<b>Result: <red>FAILED</red></b>");
				$this->write("ASSERTS: ".$this->Results->PassedPackages." FAILURES: ".$this->Results->FailedPackages. " TOTAL: ".$this->Total." <b>ASSERTION RATE: ".number_format(($this->Asserts/$this->Total*100),2)."%</b>");
				$this->write("TOTAL TIME: ".$time.'ms<br/>');
				$this->Results->ResultInfo = "FAILED";
			}
			if (!$this->fromCli) {
				if (isset($_GET["summary"])) {
					$RR = $this->Results;
					include_once ( $this->ee->eeResPath().'unittest/summary.phtml' );
				}
				print $this->Tbot;
			} else {
				if ($this->CliShowReportData) {
					$this->write("<b>Result Report Data:</b><br/>Data is serialized and base64 encoded, can be loaded creating a EEUnitTest_ResultReader object and loading this data.");
					$this->write(">>>>> DATA START >>>>>");
					$this->write(base64_encode(serialize($this->Results)));
					$this->write("<<<<<  DATA END  <<<<<");
				}
			}
		}
	}
	
	
	public final function write($Text) {
		echo $this->parseFormat($Text."<br/>");		
	}
	
	public final function parseFormat($Text) {				
		return	$this->utils->TextRenderer->RenderText("clihtml",$this->RenderMode,$Text);		
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
	var $ActualMethod=null;
	var $CaseName;
	var $Results;
	//var $Asserts=0;
	//var $Failures=0;
	var $Total=0;

	private static $instance;
	public static function &get_instance()
	{
		return self::$instance;
	}
	
	public final function Finish() {
		$this->Total = $this->Results->PassedMethods + $this->Results->FailedMethods;
		$tc_timer = &$this->eeu->tc_timer;
		$time = $tc_timer->get(CA_Timer::MILLISECONDS);
		
		if ($this->Total == $this->Results->PassedMethods) {	
			$this->eeu->write("<tab><b>PACKAGE SUMMARY <green>PASSED</green></b>");	
			$this->eeu->write("<tab>".$this->CaseName);			
			$this->eeu->write("<tab>ASSERTS: ".$this->Results->PassedMethods." FAILURES: ".$this->Results->FailedMethods. " TOTAL: ".$this->Total.' TIME: '.$time.'ms');
			$this->Results->ResultInfo = "PASSED";
			$this->eeu->Results->PassedPackages += 1;
		} else if ($this->Results->FailedMethods > 0) {
			$this->eeu->write("<tab><b>PACKAGE SUMMARY <red>FAILED</red></b>");
			$this->eeu->write("<tab>".$this->CaseName);
			$this->eeu->write("<tab>ASSERTS: ".$this->Results->PassedMethods." FAILURES: ".$this->Results->FailedMethods. " TOTAL: ".$this->Total.' TIME: '.$time.'ms');
			$this->Results->ResultInfo = "FAILED";
			$this->eeu->Results->FailedPackages += 1;
		}
	}
	
	public final function write($Text) {
		$this->eeu->write("<tab><tab><b>MESSAGE: </b>".$Text);
	}
	
	public final function __construct() {
		$this->eeu = &eeunit_get_instance();
		self::$instance =& $this;
		$this->CaseName = $this->eeu->ActualCase;
		$this->Results= new EEUnitTest_Result("PACKAGE",$this->CaseName);
		$this->Results->SubResults = array();	
	}	
}

class EEUnitTest_Function {
	
	private $eeu;
	private $testCase;
	var $FunctionName;	
	var $Asserts=0;
	var $Failures=0;
	var $Total=0;
	var $c=0;
	var $methodTime;
	var $Results;
	
	private $timer;
	
	public final function __construct($MethodDesc=null) {
		$this->eeu = &eeunit_get_instance();
		$this->testCase = &eeunit_get_case_instance();
		if ($MethodDesc==null) $MethodDesc = "-NOT SET-";
		$this->FunctionName = $MethodDesc;
		$this->Results = new EEUnitTest_Result("METHOD",$MethodDesc);
		$this->Results->MethodName = $this->eeu->ActualMethod;
		$this->timer = new CA_Timer();
		$this->timer->start();
	}
	
	public final function write($Text) {
			$this->eeu->write("<tab><tab><b>MESSAGE: </b>".$Text);
	}
	
	public final function Finish() {
		$this->timer->stop();
		$this->methodTime = $this->timer->get(CA_Timer::MILLISECONDS);
		
		$this->Total = $this->Asserts + $this->Failures;
		$this->testCase->Results->PassedAsserts += $this->Asserts;
		$this->testCase->Results->FailedAsserts += $this->Failures;		
		//$this->testCase->Total += $this->Total;
		
		$this->Results->PassedAsserts = $this->Asserts;
		$this->Results->FailedAsserts = $this->Failures;
		
		if ($this->Total == $this->Asserts) {	
			$this->eeu->write("<tab><tab><b>METHOD SUMMARY <green>PASSED</green></b>");		
			$this->eeu->write("<tab><tab>DESC.: ".$this->FunctionName);
			$this->eeu->write("<tab><tab>ASSERTS: ".$this->Asserts." FAILURES: ".$this->Failures. " TOTAL: ".$this->Total.' TIME: '.$this->methodTime.'ms');
			$this->Results->ResultInfo = "PASSED";
			$this->testCase->Results->PassedMethods += 1;
		} else if ($this->Failures>0) {
			$this->eeu->write("<tab><tab><b>METHOD SUMMARY <red>FAILED</red></b>");
			$this->eeu->write("<tab><tab>DESC.: ".$this->FunctionName);
			$this->eeu->write("<tab><tab>ASSERTS: ".$this->Asserts." FAILURES: ".$this->Failures. " TOTAL: ".$this->Total.' TIME: '.$this->methodTime.'ms');
			$this->Results->ResultInfo = "FAILED";
			$this->testCase->Results->FailedMethods += 1;
		}
		$this->testCase->Results->PassedAsserts += $this->Asserts;
		$this->testCase->Results->FailedAsserts += $this->Failures;
		$this->testCase->Results->SubResults[] = $this->Results;
	}
	
	public final function assertEquals($Expected, $Result, $Title=null) {
		$this->c++;
		$r = new EEUnitTest_Result("ASSERT","assertEquals " . $Title);
		$r->MethodDesc = $this->FunctionName;
		$r->MethodName = $this->eeu->ActualMethod;
		if ($Expected == $Result) {			
			$this->Asserts++;
			$r->ResultInfo = "PASSED";
		} else {
			$this->Failures++;
			$r->ResultInfo = "FAILURE";
		}
		$this->Results->SubResults[] = $r;
		$this->eeu->write("<tab><tab>Test #".$this->c . "<tab> assertEquals ".$Title." : ".
		($r->ResultInfo == "PASSED" ? "<green>PASSED</green>" : "<red>FAILED</red>") );
	}	
	public final function assertTrue($Condition, $Title = null) {
		$this->c++;
		$r = new EEUnitTest_Result("ASSERT","assertTrue " . $Title);
		$r->MethodDesc = $this->FunctionName;
		$r->MethodName = $this->eeu->ActualMethod;
		if ($Condition) {
			$this->Asserts++;
			$r->ResultInfo = "PASSED";
		} else {
			$this->Failures++;
			$r->ResultInfo = "FAILURE";
		}
		$this->Results->SubResults[] = $r;
		$this->eeu->write("<tab><tab>Test #".$this->c . "<tab> assertTrue: ".$Title." : ".
		($r->ResultInfo == "PASSED" ? "<green>PASSED</green>" : "<red>FAILED</red>") );
	}
	public final function assertArrayHasKey($Array, $Key, $Title = null) {
		$this->c++;
		$r = new EEUnitTest_Result("ASSERT","assertArrayHasKey " . $Title);
		$r->MethodDesc = $this->FunctionName;
		$r->MethodName = $this->eeu->ActualMethod;
		if (!is_array($Array))
			$this->write("<b>assertArrayHasKey</b>: Invalid argument, first argument must be an array.");
		if (is_array($Array) && array_key_exists($Key, $Array)) {
			$this->Asserts++;
			$r->ResultInfo = "PASSED";
		} else {
			$this->Failures++;
			$r->ResultInfo = "FAILURE";
		}
		$this->Results->SubResults[] = $r;
		$this->eeu->write("<tab><tab>Test #".$this->c . "<tab> assertArrayHasKey ".$Title." : ".
		($r->ResultInfo == "PASSED" ? "<green>PASSED</green>" : "<red>FAILED</red>") );
	}
		public final function assertInstanceOf($ClassName, $Object, $Title = null) {
		$this->c++;
		$Title = "assertInstanceOf " . $Title;
		$r = new EEUnitTest_Result("ASSERT", $Title);
		$r->MethodDesc = $this->FunctionName;
		$r->MethodName = $this->eeu->ActualMethod;

		if (!is_object($Object))
			$this->write("<b>assertInstanceOf</b>: Invalid argument, second argument must be a object.");

		if (eval ('return $Object instanceof '.$ClassName.';')) {
			$this->Asserts++;
			$r->ResultInfo = "PASSED";
		} else {
			$this->Failures++;
			$r->ResultInfo = "FAILURE";
		}
		$this->Results->SubResults[] = $r;
		$this->eeu->write("<tab><tab>Test #".$this->c . "<tab>".$Title." : ".
		($r->ResultInfo == "PASSED" ? "<green>PASSED</green>" : "<red>FAILED</red>") );
	}
	
}

class EEUnitTest_Result {
	
	var $Type;

	var $Title;
	var $Message=null;
	var $UsedTime=0;

	var $PassedAsserts=0;
	var $FailedAsserts=0;
	var $PassedPackages=0;
	var $FailedPackages=0;
	var $PassedMethods=0;
	var $FailedMethods=0;

	var $MethodDesc=null;
	var $MethodName=null;

	var $ResultInfo=null;

	var $SubResults;
	
	function __construct($Type,$title=null) {
		$this->Type = $Type;
		$this->Title = $title;	
	}
		
	function renderCli() {
		
	}
	
	function renderHtml() {
		
	}
}

?>