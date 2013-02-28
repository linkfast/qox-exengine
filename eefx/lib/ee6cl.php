<?php

# ExEngine 7 / Libs / ExEngine 6 Compatibility Layer & ForwardMode Enabler

/*
	This file is part of ExEngine.
	Copyright © LinkFast Company
	
	ExEngine is free software; you can redistribute it and/or modify it under the 
	terms of the GNU Lesser Gereral Public Licence as published by the Free Software 
	Foundation; either version 2 of the Licence, or (at your opinion) any later version.
	
	ExEngine is distributed in the hope that it will be usefull, but WITHOUT ANY WARRANTY; 
	without even the implied warranty of merchantability or fitness for a particular purpose. 
	See the GNU Lesser General Public Licence for more details.
	
	You should have received a copy of the GNU Lesser General Public Licence along with ExEngine;
	if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, Ma 02111-1307 USA.
*/


class ee6clayer {
	
	private $ee;
	
	# Start emulation layer, translating EE7 configuration file into an EE6 type.
	function __construct($parent) {
		
		$this->ee = &$parent;
		
		if ($this->checkforVeryOld()) {
			$this->ee->errorExit("VERY OLD version of ExEngine is loaded please disable it, EE6CL is not compatible with ExEngine3/4.");	
		}
		
		// =( ... ExEngine 6 Style... xD!
		global $exen_config;
		global $exen_app_key;
		
		if (isset($exen_app_key)) {
			$this->ee->errorExit("ExEngine 7 EE6CL Error [CL01]","ExEngine 6 or it's config file is loaded, cannot continue. If you want to use ForwardMode (if available) read documentation. EE7 Object should be created after loading ExEngine 6.");
		}
		
		
		
		#Config Translation
		$exen_config = $this->configEE6Translator($this->ee->cArray);		
		#Needed Compat. Superglobals
		$exen_app_path = $exen_config["app_path"];		
		
		if (!$this->ee->forwardMode) {
			global $ee6cl;
			$ee6cl["ee7obj"] = &$this->ee ;
			include_once($this->ee->eePath."eefx/ee6clayer/ee6emu.php");
			if (!$this->ee->argsGet("SilentMode")) {
					print "\n<!-- ExEngine 6 Compatibility Layer Enabled -->\n";
			}
		} else {
			global $ee6cl;
			$ee6cl["ee7obj"] = &$this->ee ;
			include_once($this->ee->eePath."eefx/ee6clayer/forwardmode.php");
			if (!$this->ee->argsGet("SilentMode")) {				
				print "\n<!-- ExEngine 6 ForwardMode Enabled -->\n";
			}
		}
	}
	
	#Translates EE7 to EE6
	final function configEE6Translator($ee7ConfigArray) {
		$ret = $ee7ConfigArray;
		$ret["app_path"] = $this->ee->eePath."ee6clayer/";
		$ret["ex_path"] = ".";
		$ret["timezone"] = $this->ee->cArray["php_timezone"];
		$ret["silentmode"] = $this->ee->argsGet("silent");
		return $ret;
	}
	
	#This will check if ExEngine 3 or 4 is loaded.
	final function checkforVeryOld() {
		$exEv4 = function_exists('horafecha');
		$exEv3 = function_exists('podcasturl') && !function_exists('horafecha');
	
		return $exEv4 || $exEv3;	
	}
}
?>