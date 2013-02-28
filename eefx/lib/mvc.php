<?php

# ExEngine 7 / Libs / ExEngine's M-V-C

/*
 This file is part of ExEngine.
 Copyright (C) LinkFast Company

 ExEngine is free software; you can redistribute it and/or modify it under the
 terms of the GNU Lesser Gereral Public Licence as published by the Free Software
 Foundation; either version 2 of the Licence, or (at your opinion) any later version.

 ExEngine is distributed in the hope that it will be usefull, but WITHOUT ANY WARRANTY;
 without even the implied warranty of merchantability or fitness for a particular purpose.
 See the GNU Lesser General Public Licence for more details.

 You should have received a copy of the GNU Lesser General Public Licence along with ExEngine;
 if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, Ma 02111-1307 USA.
 */
 
function &eemvc_get_instance()
{
		return eemvc_controller::get_instance();
}

class eemvc_index {
	
	const VERSION = "0.0.1.9";
	
	private $ee;
	private $controllername;
	private $defcontroller=null;
	
	public $viewsFolder = "views/";
	public $modelsFolder = "models/";
	public $controllersFolder = "controllers/" ;
	public $staticFolder = "static/";
	public $indexname = "index.php";
	public $SessionMode=false;
	public $AlwaysSilent=false;
	
	public $errorHandler=false;
	
	public $urlParsedData;
	
	public $staticFolderHTTP;
	public $viewsFolderHTTP;
	public $modelsFolderHTTP;
	public $controllersFolderHTTP;
	private $controllersFolderR=null;
	
	function __construct(&$parent,$defaultcontroller=null) {
		$this->ee = &$parent;		
		$this->debug("MVC Initialized.");			
		if ($defaultcontroller==null) {
			$this->debug("Index: No default controller set.");	
		} else
			$this->defcontroller = $defaultcontroller;
	}
	
	 function start() {
		 if ($this->SessionMode===true) { @session_start(); $this->debug("SessionMode=true"); } else {$this->debug("SessionMode=false");}
		 
		 if (!$this->ee->argsGet("SilentMode")) {
			 print "<h1>MVC-ExEngine can not work with SilentMode argument set to FALSE. Please set it to TRUE.</h1>";
			 exit();
		 }
		 
		if (!$this->ee->strContains($_SERVER['REQUEST_URI'],$this->indexname)) {
			header("Location: ".$_SERVER['REQUEST_URI'].$this->indexname);
			exit();
		}
		
		 $this->debug("Index: MVC Started, waiting to controller name, CONTROLLER_NAME/.");
		 $this->parseURL();				 
		 
		 
		if ( ( ( empty($this->urlParsedData) && (substr($_SERVER['REQUEST_URI'],strlen($_SERVER['REQUEST_URI'])-1,1) != "/") )
		||  
		( count($this->urlParsedData) > 0 && end($this->urlParsedData) != null )  )
		&& 
		!$this->ee->strContains($_SERVER['REQUEST_URI'],"?",false) 
		) {
			header("Location: ". $_SERVER['REQUEST_URI']."/" );
		} else if (!$this->ee->strContains($_SERVER['REQUEST_URI'],"/?",false) && substr($_SERVER['REQUEST_URI'],strlen($_SERVER['REQUEST_URI'])-1,1) != "/") {
			header("Location: ". str_replace("?","/?",$_SERVER['REQUEST_URI']) );
		}
		
		//print "fuq";
		 
		 $this->setStaticFolder();
		 
		 $this->debug(print_r($this->urlParsedData,1));		
		 
		 if (count($this->urlParsedData) > 0 && $this->ee->strContains($this->urlParsedData[count($this->urlParsedData)-1],"?") && !$this->ee->strContains($this->urlParsedData[count($this->urlParsedData)-1],"/?")) {
			 $this->debug("Index: Has GET Query and No '/'");
			 $ne = explode("?",$this->urlParsedData[count($this->urlParsedData)-1]);
			 $this->debug("Index: New Last index (".(count($this->urlParsedData)-1)."): ". $ne[0]);
			 $this->urlParsedData[count($this->urlParsedData)-1] = $ne[0];
			 $this->debug("Index: New url array: " .print_r($this->urlParsedData,1));	
		 }
		 
		 
		 if (isset($this->urlParsedData[0]) && (!empty($this->urlParsedData[0]))) {
			 $this->debug("Index: Loading controller: ".$this->urlParsedData[0]);	
			 $output = $this->load_controller($this->urlParsedData[0]);
		 } else {
			if ($this->defcontroller) {
				$this->debug("Index: Loading default controller: ".$this->defcontroller);	
				$output = $this->load_controller($this->defcontroller);
			}else {
				$this->ee->errorExit("ExEngine MVC","No default controller set.","eemvc");
			}
		 }	 	 
		 
		 if (!$this->AlwaysSilent) {
			 $rpl = "<html>\n"."<!-- Powered by MVC-".$this->ee->miscMessages("Slogan",1)." -->";
			 $output = str_replace("<html>",$rpl,$output);
		 }		 
		 print $output; 
		 
	 }
	 
	 function load_controller($name) {
		 		 		 
		 ob_start();
		 
		 //print "fuq3";	
		 
		 if ($name != null)
			 $this->controllername = $name;
		 else
		 	$name = $this->defcontroller;		 
		 
		 //print $this->urlParsedData[1];
		  $ctl_folder = $this->controllersFolder;
		  if ($this->controllersFolderR != null) {			
			$this->controllersFolder = $this->controllersFolderR;
		  }
		  
		  //print $ctl_folder;
		  //print $this->controllersFolder;
		  
		 if (is_dir($this->controllersFolder.$name) && (file_exists($this->controllersFolder.$name."/".$this->urlParsedData[1].".php") || file_exists($this->controllersFolder.$name."/".$this->defcontroller.".php"))) {
			 //print "ISDIR";
			 if (file_exists($this->controllersFolder.$name."/".$this->urlParsedData[1].".php") && isset($this->urlParsedData[1]) && !empty($this->urlParsedData[1])) {
				 //print "FE";
				 $this->controllersFolder = $this->controllersFolder.$name."/";				 
				 $nc = $this->urlParsedData[1];
				 $this->urlParsedData = array_slice($this->urlParsedData, 1);
				 print $this->load_controller($nc);
			 } else {
				 //print "DEFCON";
				 $this->controllersFolder = $this->controllersFolder.$name."/";				 				
				 $nc = $this->defcontroller;				 
				 //print $this->controllersFolder.$nc;
				 //$this->urlParsedData = array_slice($this->urlParsedData, 1);
				 print $this->load_controller($nc);
			 }
		 } else {
			 $namel = $name.".php";	
			 		 
			 if (file_exists($this->controllersFolder.$namel)) {				 
				 //print $this->controllersFolder.$namel;
				 include_once($this->controllersFolder.$namel);
				 
				 $name = ucfirst($name);
				 $ctrl = new $name($this->ee,$this);
				 
				 if (isset($ctrl->Silent)) {
					 if ($ctrl->Silent)
						$this->AlwaysSilent = true; 
				 }
				 
				 if (isset($this->urlParsedData[1]) && !empty($this->urlParsedData[1]) && !isset($this->urlParsedData[2])) { 
					 if (method_exists($name,$this->urlParsedData[1])) {						
						if (method_exists($name,'__startup')) {
							$ctrl->__startup();	
						}						
						
						call_user_func(array($ctrl, $this->urlParsedData[1]));	
											
						if (method_exists($name,'__shutdown')) {
							$ctrl->__shutdown();	
						}
					 } else {
						 $this->raiseError("e404cs",array($name,$this->urlParsedData[1]),$ctl_folder,true);	
					 }					 
				 } elseif (isset($this->urlParsedData[1]) && !empty($this->urlParsedData[1]) && isset($this->urlParsedData[2])) {			
					 
					 if (method_exists($name,$this->urlParsedData[1])) {
						 
						if (method_exists($name,'__startup')) {
							$ctrl->__startup();	
						}
						
						call_user_func_array(array($ctrl, $this->urlParsedData[1]), array_slice($this->urlParsedData, 2)); 
						
						if (method_exists($name,'__shutdown')) {
							$ctrl->__shutdown();	
						}
					 } else {
						 $this->raiseError("e404ca",array($name,print_r(array_slice($this->urlParsedData, 2),true)),$ctl_folder,true);
					 }
					 
				 } else {
					 if (method_exists($name,'__startup')) {
							$ctrl->__startup();	
					 }		
					 $ctrl->index();
					 if (method_exists($name,'__shutdown')) {
							$ctrl->__shutdown();	
					 }
					 
				 }
			 } elseif ($ctl_folder == $this->controllersFolder) {
				 //print $this->controllersFolder.$this->defcontroller.".php";
				 include_once($this->controllersFolder.$this->defcontroller.".php");
				 
				 $name = ucfirst($this->defcontroller);
				 $ctrl = new $name($this->ee,$this);
				 
				 if (isset($ctrl->Silent)) {
					 if ($ctrl->Silent)
						$this->AlwaysSilent = true; 
				 }
				 //print $ctrl;
				 
				 if (isset($this->urlParsedData[0]) && !empty($this->urlParsedData[0]) && !isset($this->urlParsedData[1])) {				 
					 
					 if (method_exists($name,$this->urlParsedData[0])) {
						if (method_exists($name,'__startup')) {
							$ctrl->__startup();	
						}
						call_user_func(array($ctrl, $this->urlParsedData[0]));
						
					 } else {
						 $this->raiseError("e404cs",array($name,$this->urlParsedData[0]),$ctl_folder,true);		
					 }
					 
				 } elseif (isset($this->urlParsedData[0]) && !empty($this->urlParsedData[0]) && isset($this->urlParsedData[1])) {			
					 
					 if (method_exists($name,$this->urlParsedData[0])) {
						if (method_exists($name,'__startup')) {
							$ctrl->__startup();	
						}
						call_user_func_array(array($ctrl, $this->urlParsedData[0]), array_slice($this->urlParsedData, 1)); 
						
					 } else {
						 $this->raiseError("e404ca",array($name,print_r(array_slice($this->urlParsedData, 1),true)),$ctl_folder,true);		
					 }					 
				 } else {
						$this->raiseError("e404",array("Controller"=>$name),$ctl_folder,true);
				 }
			 } else {				 
			 	$this->raiseError("e404",array($name),$ctl_folder,true);		  
			 }
			 
		 }
		 if ($this->controllersFolderR != null) {
			$this->controllersFolderR = $this->controllersFolder;
			$this->controllersFolder = $ctl_folder;
		  }
		  
		  $output = ob_get_contents();
		  ob_end_clean();
		  
		  return $output;		 
	 }
	 
	 private $ctl_folder;
	 
	 final private function raiseError($error,$data,$controllersfolder=null,$noexit=false) {
		if ($controllersfolder == null )
		 	$controllersfolder = $this->controllersFolder;
			
		if ($this->errorHandler) {
			if (file_exists($controllersfolder.$this->errorHandler.".php")) {
				include_once($controllersfolder.$this->errorHandler.".php");
				$name = ucfirst($this->errorHandler);
				$ctrl = new $name($this->ee,$this);
				
				if (method_exists($name,$error)) {
					call_user_func_array(array($ctrl, $error), $data);
				} else {
					$this->ee->errorExit("Error ".$error,print_r($data,true),null,$noexit);
				}				
			}
		 } else
			$this->ee->errorExit("Error ".$error,print_r($data,true),null,$noexit);
	 }
	 
	 final private function parseURL() {
		$ru = $_SERVER['REQUEST_URI'];
		$sn = $_SERVER['SCRIPT_NAME'];
		$data = str_replace($sn,"",$ru);
		
		$this->debug("Input Query: ".$data);
		
		$x = explode("/",$data);
		
		for ($i=0 ; $i<count($x) ; $i++) {
			$x[$i] = urldecode($x[$i]);
		}
		
		$this->urlParsedData = array_slice($x,1);
	 }
	 
	 final function setStaticFolder() {
		$str = "//" . $_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'].$this->staticFolder;		
		$str = str_replace($this->indexname,"",$str);		
		$this->staticFolderHTTP = $str;
		
		$str = "//" . $_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME']."/";		
		$this->controllersFolderHTTP = $str;
		/*
		$str = "//" . $_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'].$this->viewsFolder;		
		$str = str_replace($this->indexname,"",$str);		
		$this->viewsFolderHTTP = $str;
		
		$str = "//" . $_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'].$this->modelsFolder;		
		$str = str_replace($this->indexname,"",$str);		
		$this->modelsFolderHTTP = $str;
		*/
	 }
	 
	 final function debug($message) {
		 $this->ee->debugThis("eemvc",$message);
	 }
	 
	 final function debugController($message) {
		 $this->ee->debugThis("eemvc-".$this->controllername,$message);
	 }
}

class eemvc_controller {
	public $ee;
	public $index;	
	public $db;
	
	public static $im;
	
	private static $inst;
	
	final function __construct(&$ee,&$parent) {
		$this->ee = &$ee;
		$this->index = &$parent;
		
		self::$inst =& $this;
		
		if (method_exists($this,'__atconstruct')) {
			$this->__atconstruct();	
		}
	}	
	
	public static function &get_instance()
	{
		return self::$inst;
	}
	
	//EEDBM 0.0.1.x
	final function loadDB($dbObj="default") {		
		$this->db = new eedbm($this->ee,$dbObj);
	}
	
	final function loadModel($model_name,$obj_name='') {
		$this->debug("loadModel: ".$model_name);
		
		$m_file = $this->index->modelsFolder.$model_name.".php";
		
		if (file_exists($m_file)) {
			include_once($m_file);
			
			if ($obj_name=='')
				$obj_name = $model_name;
				
			$model_name = ucfirst($model_name);
			
			$this->$obj_name = new $model_name();
			$this->debug("loadModel: ".$model_name.'-Done. ($this->'.$model_name.')');
		} else {
			$this->debug("loadModel: ".$model_name.'-Not found');
			$this->ee->errorExit("ExEngine MVC","Model not found.","eemvc");
		}		
	}
	
	final function debug ($msg) {
		$this->index->debugController($msg);	
	}
	
	final function loadView($filename,$data=null,$return=false,$dynamic=true) {	
		
		$view_file = $this->index->viewsFolder.$filename;	
		
		if (file_exists($view_file)) {
			
			$this->debug("loadView: Loading: ".$view_file);
			
			//if (is_array($data)) {
				$data["EEMVC_StaticHTTP"] = $this->index->staticFolderHTTP;
				$data["EEMVC_ControllersHTTP"] = $this->index->controllersFolderHTTP;
				/*
				$data["EEMVC_ViewsHTTP"] = $this->index->viewsFolderHTTP;
				$data["EEMVC_ModelsHTTP"] = $this->index->modelsFolderHTTP;			
				*/	
				extract($data);	
			//} else {
				
			//}
			
			ob_start();	
			
			if ($dynamic) {
				if ((bool) @ini_get('short_open_tag') === FALSE)
				{
					$this->debug("loadView: Mode: ShortTags_Rewriter");
					echo eval('?>'.preg_replace("/;*\s*\?>/", "; ?>", str_replace('<?=', '<?php echo ', file_get_contents($view_file))));
				}
				else
				{		
					$this->debug("loadView: Mode: Include");	
					include_once($view_file);
				}
			}
			else
			{
				$this->debug("loadView: Mode: ReadFile");
				readfile($view_file);
			}
			
			$this->debug("loadView: Mode: View loaded: ".$view_file);
			
			$output = ob_get_contents();
			ob_end_clean();
			
			//$this->index->debug($output);
			
			if ($return)
			{
				return $output;
			} else {
				echo $output;
			}				
		} else {
			$this->ee->errorExit("ExEngine MVC","View not found.","eemvc");
		}
	}
}

class eemvc_model {
	
	function __construct() {		
	}
	
	//Get all Controller's properties
	function __get($key)
	{
		$Contr =& eemvc_get_instance();
		return $Contr->$key;
	}
	
	//Call Controller's methods
	function __call($name,$args=null) {
		$Contr =& eemvc_get_instance();		
		if (method_exists('eemvc_controller',$name)) {
			if ($args==null) {
				call_user_func(array($Contr,$name));
			} else {
				call_user_func_array(array($Contr,$name), $args); 
			}
		}
	}
}

?>