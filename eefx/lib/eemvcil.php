<?php
/**
@file eemvcil.php
@author Giancarlo Chiappe <gch@linkfastsa.com> <gchiappe@gmail.com>
@version 0.0.1.13

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

ExEngine 7 / Libs / ExEngine's Model View Controller Implementation Library (eemvcil)

ExEngine MVC Implementation Library

*/
 
/// Get Instance Funciont, connects controllers.
function &eemvc_get_instance()
{
		return eemvc_controller::get_instance();
}


/// eemvc_index Class, used to create the initial object in index.php at the root folder.
class eemvc_index {
	
	const VERSION = "0.0.1.13"; /// Version of EE MVC Implementation library.
	
	private $ee; /// This is the connector to the main ExEngine object.
	public $controllername; /// Name of the Controller in use.
	private $defcontroller=null;
	
	public $viewsFolder = "views/"; /// Name of the views folder, should be relative to the index file.
	public $modelsFolder = "models/"; /// Name of the models folder, should be relative to the index file.
	public $controllersFolder = "controllers/" ; /// Name of the controllers folder, should be relative to the index file.
	public $staticFolder = "static/"; /// Name of the static folder, should be relative to the index file.
	public $indexname = "index.php"; /// Name of the index file, normally this should not be changed.
	public $SessionMode=false; /// Set to true if you are going to use sessions, remember that "session_start()" does not work with EEMVC.
	public $AlwaysSilent=false; /// Set to true if you do not want to show warnings or slogans to the rendered pages, this is a global variable, you can set silent to a specific controller by setting the $this->imSilent variable to true.
	public $jQueryUITheme="base"; /// Default EEMVC JQuery UI Theme.
	public $jQueryVersion = null; /// Default EEMVC JQuery JS Lib Version.
	public $jQueryUIVersion = null; /// Default EEMVC JQuery UI JS Lib Version.
	
	public $errorHandler=false; /// Set to the error handler controller name, that controller should be made using the error controller template.
	
	public $urlParsedData; /// Parsed data from the URL string, please do not modify in runtime.
	
	public $staticFolderHTTP; /// HTTP path (URL) to the static folder, made for views rendering.
	public $viewsFolderHTTP;  /// HTTP path (URL) to the views folder, made for views rendering.
	public $modelsFolderHTTP;  /// HTTP path (URL) to the models folder, made for views rendering.
	public $controllersFolderHTTP;  /// HTTP path (URL) to the controllers folder, made for views rendering.
	private $controllersFolderR=null;
	
	/// Default constructor for the index listener.
	final function __construct(&$parent,$defaultcontroller=null) {
		$this->ee = &$parent;		
		$this->debug("MVC Initialized.");			
		if ($defaultcontroller==null) {
			$this->debug("Index: No default controller set.");	
		} else
			$this->defcontroller = $defaultcontroller;
	}
	
	/// Loads a view for the View Simulator, useful for designers that want to test the basic functionality of their pages.
	final function specialLoadViewStatic($filename,$fullpath=false,$checkmime=false) {
		
		if ($fullpath) {
			$view_fileo = $filename;
		}
		else
			$view_fileo = $this->viewsFolder.$filename;	
			
		$view_file = $view_fileo;	
		
		if (!file_exists($view_file)) {
			$view_file = $view_fileo.".php";
		}
		
		if (!file_exists($view_file)) {
			$view_file = $view_fileo.".html";
		}

		if (file_exists($view_file)) {
			
			$this->debug("specialLoadViewStatic: Loading: ".$view_file);
			
			if ($checkmime) {
				$this->ee->eeLoad("mime");
				$eemime = new eemime($this->ee);
				$mime_type = $eemime->getMIMEType($view_file);				
				$this->debug("specialLoadViewStatic: File Mime Type: ".$mime_type);
			}

			$data["EEMVC_SF"] = $this->staticFolderHTTP;
			$data["EEMVC_SFTAGGED"] =  $this->controllersFolderHTTP."?EEMVC_SPECIAL=STATICTAGGED&FILE=";
			$data["EEMVC_C"] = $this->controllersFolderHTTP;
			$data["EEMVC_SC"] = $this->controllersFolderHTTP."?EEMVC_SPECIAL=VIEWSIMULATOR&VIEW=".$view_file."&ERROR=NODYNAMIC&";
			$data["EEMVC_SCF"] = $this->controllersFolderHTTP."?EEMVC_SPECIAL=VIEWSIMULATOR&VIEW=".$view_file."&ERROR=NODYNAMIC&";
			
			$data["EEMVC_VS"] = $this->controllersFolderHTTP."?EEMVC_SPECIAL=VIEWSIMULATOR&VIEW=";
			
			$jq = new jquery($this->ee);
			$jqstr = $jq->load($this->jQueryVersion,true);
			$data["EEMVC_JQUERY"]  = $jqstr; 
			$jqstr2 = $jq->load_ui($this->jQueryUITheme,$this->jQueryUIVersion,true);			
			$data["EEMVC_JQUERYUI"]  = $jqstr2; 

			extract($data);	
			
			ob_start();				

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
			
			$this->debug("specialLoadViewStatic: Mode: View loaded: ".$view_file);
			
			$output = ob_get_contents();
			ob_end_clean();		
				
			if ($checkmime)
				header('Content-type: '.$mime_type);
			
			echo $output;			
		} else {
			$this->ee->errorExit("ExEngine MVC","View not found.","eemvc");
		}	
	}
	
	/// This function will start the MVC listener, should be called in the index file.
	final function start() {	
	
	$this->setStaticFolder();
	if ($this->SessionMode===true) { @session_start(); $this->debug("SessionMode=true"); } else {$this->debug("SessionMode=false");}	
		
		if (isset($_GET['EEMVC_SPECIAL']) && ($this->ee->cArray["debug"])) {
			
				switch ($_GET['EEMVC_SPECIAL']) {
					case 'VIEWSIMULATOR':
						if (isset($_GET['ERROR'])) if ($_GET['ERROR'] == "NODYNAMIC") $this->ee->errorExit("EEMVCIL","EEMVC_SPECIAL: EEMVC_SC and EEMVC_SCF special tags does no work in the Views Simulator.",null,true);
						$this->specialLoadViewStatic($_GET['VIEW']);
					break;
					case 'STATICTAGGED':
						$file = $this->staticFolder.$_GET['FILE'];
						$this->specialLoadViewStatic($file,true,true);
					break;
					default:
						$this->ee->errorExit("EEMVCIL","EEMVC_SPECIAL: Mode Not Found.");
					break;
				}
			
		} else {
		
		 
		 
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
		 
	 }
	 
	 /// This function will call the controller, parse variables, session and render, the use of this function is totally automatic.
	 private final function load_controller($name) {
		 		 		 
		 ob_start();			
		 
		 if ($name != null)
			 $this->controllername = $name;
		 else
		 	$name = $this->defcontroller;	
			
		  $ctl_folder = $this->controllersFolder;
		  if ($this->controllersFolderR != null) {			
			$this->controllersFolder = $this->controllersFolderR;
		  }
		  
		  
		 if (is_dir($this->controllersFolder.$name) && (file_exists($this->controllersFolder.$name."/".$this->urlParsedData[1].".php") || file_exists($this->controllersFolder.$name."/".$this->defcontroller.".php"))) {
			 
			 if (file_exists($this->controllersFolder.$name."/".$this->urlParsedData[1].".php") && isset($this->urlParsedData[1]) && !empty($this->urlParsedData[1])) {
				 
				 $this->controllersFolder = $this->controllersFolder.$name."/";				 
				 $nc = $this->urlParsedData[1];
				 $this->urlParsedData = array_slice($this->urlParsedData, 1);
				 print $this->load_controller($nc);
			 } else {
				
				 $this->controllersFolder = $this->controllersFolder.$name."/";				 				
				 $nc = $this->defcontroller;				 
				 
				 print $this->load_controller($nc);
			 }
		 } else {
			 $namel = $name.".php";	
			 		 
			 if (file_exists($this->controllersFolder.$namel)) {
				 
				 include_once($this->controllersFolder.$namel);
				 
				 $name = ucfirst($name);
				 $ctrl = new $name($this->ee,$this);
				 
				 if (isset($ctrl->imSilent)) {
					 if ($ctrl->imSilent)
						$this->AlwaysSilent = true; 
				 }
				 
				 if (isset($this->urlParsedData[1]) && !empty($this->urlParsedData[1]) && !isset($this->urlParsedData[2])) { 
					 if (method_exists($name,$this->urlParsedData[1])) {						
						if (method_exists($name,'__startup')) {
							$ctrl->__startup();	
						}						
						
						$ctrl->functionName = $this->urlParsedData[1];
						call_user_func(array($ctrl, $this->urlParsedData[1]));	
						
											
						if (method_exists($name,'__atdestroy')) {
							$ctrl->__atdestroy();	
						}
					 } else {
						 $this->raiseError("e404cs",array($name,$this->urlParsedData[1]),$ctl_folder,true);	
					 }					 
				 } elseif (isset($this->urlParsedData[1]) && !empty($this->urlParsedData[1]) && isset($this->urlParsedData[2])) {			
					 
					 if (method_exists($name,$this->urlParsedData[1])) {
						 
						if (method_exists($name,'__startup')) {
							$ctrl->__startup();	
						}
						
						$ctrl->functionName = $this->urlParsedData[1];
						call_user_func_array(array($ctrl, $this->urlParsedData[1]), array_slice($this->urlParsedData, 2)); 
						
						
						if (method_exists($name,'__atdestroy')) {
							$ctrl->__atdestroy();	
						}
					 } else {
						 $this->raiseError("e404ca",array($name,print_r(array_slice($this->urlParsedData, 2),true)),$ctl_folder,true);
					 }
					 
				 } else {
					 if (method_exists($name,'__startup')) {
							$ctrl->__startup();	
					 }	
					 
					 $ctrl->functionName = "index";	
					 $ctrl->index();
					
					 
					 if (method_exists($name,'__atdestroy')) {
							$ctrl->__atdestroy();	
					 }
					 
				 }
			 } elseif ($ctl_folder == $this->controllersFolder) {
				 
				 include_once($this->controllersFolder.$this->defcontroller.".php");
				 
				 $name = ucfirst($this->defcontroller);
				 $ctrl = new $name($this->ee,$this);
				 
				 if (isset($ctrl->imSilent)) {
					 if ($ctrl->imSilent)
						$this->AlwaysSilent = true; 
				 }
				 
				 
				 if (isset($this->urlParsedData[0]) && !empty($this->urlParsedData[0]) && !isset($this->urlParsedData[1])) {				 
					 
					 if (method_exists($name,$this->urlParsedData[0])) {
						if (method_exists($name,'__startup')) {
							$ctrl->__startup();	
						}
						call_user_func(array($ctrl, $this->urlParsedData[0]));
						
						if (method_exists($name,'__atdestroy')) {
							$ctrl->__atdestroy();	
						}
						
					 } else {
						 $this->raiseError("e404cs",array($name,$this->urlParsedData[0]),$ctl_folder,true);		
					 }
					 
				 } elseif (isset($this->urlParsedData[0]) && !empty($this->urlParsedData[0]) && isset($this->urlParsedData[1])) {			
					 
					 if (method_exists($name,$this->urlParsedData[0])) {
						if (method_exists($name,'__startup')) {
							$ctrl->__startup();	
						}
						call_user_func_array(array($ctrl, $this->urlParsedData[0]), array_slice($this->urlParsedData, 1)); 
						
						if (method_exists($name,'__atdestroy')) {
							$ctrl->__atdestroy();	
						}
						
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
	 
	 private $ctl_folder; /// System Variable for the controllers folder.
	 
	 /// This function will raise an error to the user, if is defined by the developer, it will call the error controller, if not it will raise a default exengine 7 errorExit.
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
	 
	 /// This function will parse the URL.
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
	 
	 /// This function sets the static folder path.
	 final function setStaticFolder() {
		$str = "//" . $_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'].$this->staticFolder;		
		$str = str_replace($this->indexname,"",$str);		
		$this->staticFolderHTTP = $str;
		
		$str = "//" . $_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME']."/";		
		$this->controllersFolderHTTP = $str;
	 }
	 
	 /// Shortcut to the ExEngine Debugger (Session or remote) for the index class.
	 final function debug($message) {
		 $this->ee->debugThis("eemvc",$message);
	 }
	 
	 /// Shortcut to the ExEngine Debugger for the actual controller.
	 final function debugController($message) {
		 $this->ee->debugThis("eemvc-".$this->controllername,$message);
	 }
}

class eemvc_methods {
	
	var $cparent;	
	
	final function sf() {
		return $this->cparent->index->staticFolderHTTP;;
	}
	
	final function fsf() {
		return $this->cparent->index->staticFolder ;	
	}
	
	final function c() {
		return $this->cparent->index->controllersFolderHTTP;;
	}
	
	final function sc() {
		return $this->cparent->index->controllersFolderHTTP.strtolower(get_class($this->cparent))."/";	
	}
	
	final function scf() {
		return $this->cparent->index->controllersFolderHTTP.strtolower(get_class($this->cparent))."/".$this->cparent->functionName."/";
	}
	
	final function vs() {
		return $this->cparent->index->controllersFolderHTTP."?EEMVC_SPECIAL=VIEWSIMULATOR&VIEW=";	
	}
	
	final function __construct(&$parent) {
		$this->cparent = &$parent;
	}
	
	final function getSession($element) {
		if ($this->cparent->index->SessionMode)			
			return $_SESSION[$element];
		else {
			$this->cparent->debug("Cannot get a session variable, SessionMode is set to false.");
			return null;	
		}			
	}
	
	final function setSession($element,$value) {
		if ($this->cparent->index->SessionMode)
			$_SESSION[$element] = $value;	
		else {
			$this->cparent->debug("Cannot get a session variable, SessionMode is set to false.");
			return null;	
		}
	}

	final function get($element) {
		return $_GET[$element];	
	}
	
	final function post($element) {
		return $_POST[$element];	
	}
	
	final function file($pname) {
		return $_FILES[$pname];	
	}
	
	final function allpost() {
		return $_POST;	
	}
	
	final function allget() {
		return $_GET;	
	}
}

class eemvc_controller {
	public $ee; /// Parent EE7 Object.
	public $index; /// Parent eemvc_index object.
	public $db; /// Default database object, should be loaded first using $this->loadDb.
	public $functionName; /// The name of the in-use function.
	
	public $r; /// Input data methods  
	
	public static $im; /// don't remenber ... :(
	
	private static $inst; /// This contoller instance.
	
	public $imSilent = false; /// Set this controller to silent, useful for writing ajax/comet servers.
	
	/// Default constructor, cannot be overriden, private __atconstruct function should be created in the controller to create a custom event.
	final function __construct(&$ee,&$parent) {
		$this->ee = &$ee;
		$this->index = &$parent;		
		
		self::$inst =& $this;
		
		$this->r = new eemvc_methods($this);
		if (method_exists($this,'__atconstruct')) {
			$this->__atconstruct();	
		}
	}	
	
	/// Connection static function.
	public static function &get_instance()
	{
		return self::$inst;
	}
	
	/// Connects to the default or a connection array specified database (100% compatible with EE DB Manager, depends on its version).
	final function loadDB($dbObj="default") {		
		$this->db = new eedbm($this->ee,$dbObj);
	}
	
	/// Loads a model, by default will create an object with the same name.
	final function loadModel($model_name,$obj_name=null,$create_obj=true) {
		$this->debug("loadModel: Load: ".$model_name);
		
		$m_file = $this->index->modelsFolder.$model_name.".php";
		
		if (file_exists($m_file)) {
			include_once($m_file);
			
			if ($obj_name==null)
				$obj_name = $model_name;
				
			$model_name = ucfirst($model_name);
			
			if ($create_obj)
				$this->$obj_name = new $model_name();
				
			$this->debug("loadModel: ".$model_name.'-Done. ($this->'.$obj_name.')');
		} else {
			$this->debug("loadModel: ".$model_name.'-Not found');
			$this->ee->errorExit("ExEngine MVC","Model not found.","eemvc");
		}		
	}
	
	final function debug ($msg) {
		$this->index->debugController($msg);	
	}
	
	final function loadView($filename,$data=null,$return=false,$dynamic=true) {	
		
		$view_fileo = $this->index->viewsFolder.$filename;	
		
		$view_file = $view_fileo;	
		
		if (!file_exists($view_file)) {
			$view_file = $view_fileo.".php";
		}
		
		if (!file_exists($view_file)) {
			$view_file = $view_fileo.".html";
		}
		
		if (file_exists($view_file)) {
			
			$this->debug("loadView: Loading: ".$view_file);

			$data["EEMVC_SF"] = $this->index->staticFolderHTTP;
			$data["EEMVC_SFTAGGED"] =  $this->index->controllersFolderHTTP."?EEMVC_SPECIAL=STATICTAGGED&FILE=";
			
			$data["EEMVC_C"] = $this->index->controllersFolderHTTP;
			$data["EEMVC_SC"] = $this->index->controllersFolderHTTP.strtolower(get_class($this))."/";
			$data["EEMVC_SCF"] = $this->index->controllersFolderHTTP.strtolower(get_class($this))."/".$this->functionName."/";
						
			$data["EEMVC_VS"] = $this->index->controllersFolderHTTP."?EEMVC_SPECIAL=VIEWSIMULATOR&VIEW=";
			
			$jq = new jquery($this->ee);
			$jqstr = $jq->load($this->index->jQueryVersion,true);
			$data["EEMVC_JQUERY"]  = $jqstr; 
			$jqstr2 = $jq->load_ui($this->index->jQueryUITheme,$this->index->jQueryUIVersion,true);			
			$data["EEMVC_JQUERYUI"]  = $jqstr2; 

			extract($data);	
			
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