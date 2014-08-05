<?php
/**
@file me.php
@author Giancarlo Chiappe <gchiappe@qox-corp.com> <gchiappe@outlook.com.pe>
@version 1.0.0.8 alpha

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

ExEngine / Libs / Mixed Engines Loader (me)

 */

namespace ExEngine;

class MixedEngineLoader {
	
	public $meName;
	public $meVersion;
	public $meFile;
	public $meRQVersion="0.0.0.0";
	
	private $meRealName;

	/* @var $ee Core */
	private $ee;
	
	const VERSION = "1.0.0";
	const REV = 9;
	const RELEASE = "alpha";
	
	
	function __construct(Core $parent,$inME=null,$rqV="0.0.0.0") {
		$this->meFile = $inME;
		$this->ee = &$parent;
		
		$this->meRQVersion = $rqV;
	}
	
	final function load() {
		$this->ee->debugThis("me-loader","Loader: Starting load of: ".$this->meFile);
		$ex = 0;
		#Check File Existance.
		if ($this->checkIfExistsInLib()) {
			$this->ee->debugThis("me-loader","Loader: ".$this->meFile."-Exists in engines path.");
			$this->meFile = $this->ee->eePath."eefx/engines/core/".$this->meFile.".php";
			$ex = 1;
		} else if (file_exists($this->meFile)) {
			$this->ee->debugThis("me-loader","Loader: ".$this->meFile."-Exists.");
			$ex = 1;
		} else {
			$this->ee->debugThis("me-loader","Loader: File not found.");
			$this->ee->errorExit("ExEngine7 Mixed Engine Load Error [ME01]","File not found.");				
		}
		#Get Class Name First, to check if loaded.
		if ($ex==1) {
			$this->meName = $this->getClassName($this->meFile);
		}
		if (!$this->checkIfLoaded($this->meFile)) {
			#Load File now.
			if ($ex == 1) {
				$versionME = $this->getMixedEngineVersion($this->meFile);
				if ($versionME == "4") {	
					$this->ee->debugThis("me-loader","Loader: Loading...");
					include_once($this->meFile);					
					if ($this->checkMEv4standard()) {	
						$this->ee->debugThis("me-loader","Loader: V4 Standard: Passed.");
						
						eval('$meName = '.$this->meName.'::NAME ;');
						eval('$meVersion = '.$this->meName.'::VERSION ;');
						eval('$merqEE = '.$this->meName.'::RQEE7 ;');
						
						if (defined($this->meName.'::RQME'))
							eval('$merqME = '.$this->meName.'::RQME ;');	
						if (defined($this->meName.'::RQEE'))
							eval('$merqExE = '.$this->meName.'::RQEE ;');
						
						$merqFM = defined($this->meName.'::FMRQ');						
						if ($merqFM) {
							eval('$merqFMTF = '.$this->meName.'::FMRQ ;');
							if ($merqFMTF) {
								$this->ee->debugThis("me-loader","Loader: ForwardMode Required.");	
								if (!$this->ee->forwardMode) {		
								$this->ee->debugThis("me-loader","Loader: ForwardMode Required: Not available.");						
									$this->ee->errorExit("ExEngine7 Mixed Engine Load Error [ME06]","ForwardMode enabled and active is required to load this MixedEngine.","ForwardMode#MixedEngines");		
								} else {	
									eval('$merqEE6 = '.$this->meName.'::RQEE6 ;');
									if (version_compare($merqEE6,$this->ee->ee6version,">")) {
										$this->ee->debugThis("me-loader","Loader: ForwardMode Required: Old version of EE6.");	
										$this->ee->errorExit("ExEngine7 Mixed Engine Load Error [ME07]","A newer version of ExEngine 6 (ForwardMode) is required for this Engine.","ForwardMode#MixedEngines");		
									}
									
								}
							}							
						}
							
						$versionCheckEE = version_compare($this->ee->miscGetVersion(),$merqEE,"<");
						$this->ee->debugThis("me-loader","Loader: Checking if newer EE7 version is req.: ".var_export($versionCheckEE,true));
						if ($versionCheckEE) {
							$this->ee->debugThis("me-loader","Loader: Checking EE Version: Failure, ME Requires a newer EE7 version.");
							$this->ee->errorExit("ExEngine7 Mixed Engine Load Error [ME05]","A newer version/build of ExEngine 7 is required to load this MixedEngine.");
						} else {
							
							if (method_exists($this->meName,"meRQEngines")) {
								$this->ee->debugThis("me-loader","Loader: Checking EE7 Version: Failure, ME Requires a newer EE7 version.");
								eval('$resu= '.$this->meName."::meRQEngines() ;");
								$this->ee->debugThis("me-loader","Loader: Checking Dependencies...");
								//if (isset($resu) && !is_array($resu)) $resux[0] = true; else $resux[0] = $resu[0];
								
								if (isset($resu)) {
									if (!is_array($resu) && !empty($resu)) {									
										$this->checkAndLoad($resu,"me");									
									}
									
									/*
									
									if (isset($resu) && (!$resu || !$resux[0])) {
										if (is_array($resu))
											$a_det = "<br/><br/>\n".'<b>More Information:</b><br/>'.$resu[1]."<br/>\n";
										else $a_det=" ";
										
										$this->ee->errorExit("ExEngine7 Mixed Engine Load Error [ME08]","RequiredEngines are not found or not working correctly.".$a_det);	
									}
									*/
								}
							}
							
							if ($this->meRQVersion != "0.0.0.0") {
								$v_cm = version_compare($this->meRQVersion,$meVersion,">");
								if (!$v_cm) $this->ee->errorExit("ExEngine7 Mixed Engine Load Error [ME09]","A newer version of this MixedEngine is required.");	
							}							
							$this->ee->loadedME[$this->meFile] = true;
							$this->ee->loadedMEVersion[$this->meFile] = $meVersion;
							$this->ee->debugThis("me-loader","Loader: Done.");
							$this->ee->miscMessShow("ExEngine7 : MixedEngine Loader : " . $meName . " Loaded Successfully.");
							return true;
						}
					} else {
						# ME03
						$this->ee->errorExit("ExEngine7 Mixed Engine Load Error [ME03]","File was recognized as v4 MixedEngine but an error occoured checking the MixedEngine v4 standard specification, check your class MEv4 Constants.");
					}
				} else if ($versionME == "3.1") {
					$this->ee->errorExit("ExEngine7 Mixed Engine Load Error [ME04]","MixedEngines v3.1 are not supported at the time. MixedEngine not loaded.<br/>To load V3.1 MixedEngines please use EE6CL or ExEngine6 in ForwardMode.");			
				} else if ($versionME == "0") {
					$this->ee->errorExit("ExEngine7 Mixed Engine Load Error [ME02]","This is not a MixedEngine, normal PHP library files should be loaded with standard include, include_once or require, to create a MixedEngine please read MixedEngine v4 Manual. Note: Only MixedEngines v4 are supported. File not loaded.");	
				} else {
					$this->ee->errorExit("ExEngine7 Mixed Engine Load Error [ME04]","Only MixedEngines v4 are supported. MixedEngine not loaded.<br/>To load V2,V3,V3.1 MixedEngines please use EE6CL or ExEngine6 in ForwardMode.");
				}
			}			
		} else {
			$meName=null;
			eval('$meName = '.$this->meName.'::NAME ;');
			$this->ee->miscMessShow("MixedEngine ".$meName." is already loaded.");
		}		
	}
	
	final function commShow($message) {
		if (!$this->ee->argsGet("silent") && !$this->ee->argsGet("noMeComments")) {
				print "<!-- " . $message . " -->\n";
			}
	}
	
	final function checkIfLoaded($eName) {
		//$this->ee->debugThis("me-loader","Loader: Checking if ME is loaded: ".$eName);
		$isLoaded = isset($this->ee->loadedME[$eName]);	
		$this->ee->debugThis("me-loader","Loader: ME is loaded?: ". var_export($isLoaded,true));
		return $isLoaded;
	}
	
	final function getMixedEngineVersion($file) {
		//$this->ee->debugThis("me-loader","Loader: Getting ME version for: ".$file);
		$meString = implode('', file($file));		
		$v4Pattern = '/#<EEMEv4>#/i';
		$v31Pattern = '/#<EEMEv31>#/i';
		$v3Pattern = '/meProperties/i';
		$v2Pattern = '/exen_MixedEngineLoader/i';
		$v1Pattern = '/engine_alias/i';		
		$r="0";
		if (preg_match($v4Pattern,$meString)) {
			$r = "4";	
		} else 
		if (preg_match($v31Pattern,$meString)) {
			$r = "3.1";	
		} else 
		if (preg_match($v3Pattern,$meString)) {
			$r = "3";	
		} else 
		if (preg_match($v2Pattern,$meString)) {
			$r = "2";	
		} else 
		if (preg_match($v1Pattern,$meString)) {
			$r = "1";	
		} else {
			$r = "0";
		}	
		$this->ee->debugThis("me-loader","Loader: ME version: ".$r);
		return $r;		
	}
	
	final function checkAndLoad($enginesString,$EngineType) {
		$loadCounter = 0;
		$enginesPlusVersions = explode($enginesString,",");
		foreach ($enginesPlusVersions as $epv) {
			$tempArray = explode($epv,":");
			$engineName = $tempArray[0];
			$engineVersion = $tempArray[1];	
			
			if ($EngineType == "me") {
				if ($this->ee->loadedME[$engineName]) {
					$checkVersion = version_compare($this->ee->loadedME[$engineName],$engineVersion,"<");
					if ($checkVersion) {
						if ($this->meName!=null) $this->ee->errorExit("MixedEngine Loader","A newer version of ".$engineName." is required to load ".$this->meName); else
						$this->ee->errorExit("MixedEngine Loader","A newer version of ".$engineName." is required.");
					} else {
						$loadCounter++;	
					}
				} else {
					$this->ee->meLoad($engineName,0,$engineVersion);	
					$loadCounter++;
				}
			} elseif ($EngineType == "ee") {
				if (!$this->ee->loadedEE[$engineName]) {
					if ($this->ee->eeLoad($engineName))
						$loadCounter++;
				} else
					$loadCounter++;
			}
			
		}
		if ($loadCounter > 0)
			return true;
		else
			return false;
	}
	
	final function getMEName($file) {
		$meString = implode('', file($file));
		$firstClassPat = '/const NAME = "([A-Za-z0-9._%-]*)";/';
		preg_match($firstClassPat,$meString,$matches);
		# ONLY THE FIRST CLASS!
		return $matches[1];
	}
	
	final function getClassName($file) {
		//$this->ee->debugThis("me-loader","Loader: Getting class name for: ".$file);
		$meString = implode('', file($file));
		$firstClassPat = '/class ([A-Za-z0-9._%-]*)/';
		preg_match($firstClassPat,$meString,$matches);
		# ONLY THE FIRST CLASS!
		$this->ee->debugThis("me-loader","Loader: Class name: ".$matches[1]);
		return $matches[1];
	}
	
	final function checkMEv4standard() {
		if (class_exists($this->meName)) {
			
			$meVersion = $this->meName.'::VERSION';
			$meName = $this->meName.'::NAME';
			$meDate = $this->meName.'::DATE';
			$meRqEE = $this->meName.'::RQEE7';
			
			if (defined($meVersion) && defined($meName) && defined($meDate) && defined($meRqEE)) {
				return true;
			} else {
				return false;
			}			
		} else {
			return false;
		}
	}
	
	final function checkIfExistsInLib() {
		//print $this->ee->eePath."eefx/me/".$this->meName.".php";
		return file_exists($this->ee->eePath."eefx/engines/core/".$this->meFile.".php");
	}
}
?>