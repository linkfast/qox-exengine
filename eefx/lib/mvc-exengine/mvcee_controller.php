<?php
/**
@file mvcee_controller.php
@author Giancarlo Chiappe <gchiappe@qox-corp.com> <gchiappe@outlook.com.pe>

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

ExEngine / Libs / MVC-ExEngine / Controller Class

ExEngine MVC Implementation Library

*/

class eemvc_controller {

	/* @var $ee exengine */
	public $ee; /// Parent EE7 Object.
	/* @var $index eemvc_index */
	public $index; /// Parent eemvc_index object.
	/* @var $db eedbm */
	public $db; /// Default database object, should be loaded first using $this->loadDb.
	public $functionName; /// The name of the in-use function.

	/* @var $r eemvc_methods */
	public $r; /// Input data methods

	/* @var $ma eema */
	public $ma; /// EE Message Agent.
	
	public static $im; /// don't remenber ... :(

	/* @var $inst eemvc_controller */
	private static $inst; /// This contoller instance.
	
	public $imSilent = false; /// Set this controller to silent, useful for writing ajax/comet servers.
	
	/// Default constructor, cannot be overriden, private __atconstruct function should be created in the controller to create a custom event.
	final function __construct(&$ee,&$parent) {
		$this->ma = new eema('eemvcc-'.get_class($this), 'MVC-EE Controller "'.get_class($this).'".');

		$this->ee = &$ee;
		$this->index = &$parent;
		self::$inst =& $this;
		
		$this->r = new eemvc_methods($this);
		if (method_exists($this,'__atconstruct')) {
			$fn = $this->functionName;
			$this->functionName = "__atconstruct";
			$this->__atconstruct();	
			$this->functionName = $fn;
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
		$this->debug("mvcee_controller.php:". __LINE__ . ": loadModel: Load: ".$model_name);
		if ($this->index->unitTest && defined('STDIN') && !$this->index->utSuite) {
			echo 'MVC-ExEngine 7 -> Preparing model '.ucfirst($model_name).' for unit testing.'."\n";
		} else		
		if ($this->index->utSuite)
			$this->index->utSuite->write("
								<b>MVC-ExEngine</b>
								<tab>
									Preparing model ".ucfirst($model_name)." for unit testing.");
		
		$m_file = $this->index->modelsFolder."/".$model_name.".php";
		
		if (file_exists($m_file)) {
			include_once($m_file);		
			
			$model_name = explode("/",$model_name);
			$model_name = $model_name[(count($model_name)-1)];
			$model_name = ucfirst($model_name);

			if ($obj_name==null)
				$obj_name = $model_name;
			
			if ($create_obj) {
				$this->$obj_name = new $model_name();			
				$this->debug("mvcee_controller.php:". __LINE__ . ": loadModel: ".$model_name.' ('.$m_file.') Done. ($this->'.$obj_name.')');
			}
			else
				$this->debug("mvcee_controller.php:". __LINE__ . ": loadModel: ".$model_name.' ('.$m_file.') Done.');

		} else {
			$this->debug("mvcee_controller.php:". __LINE__ . ": loadModel: ".$model_name.'-Not found');
			if ($this->index->unitTest && defined('STDIN') && !$this->index->utSuite) {
				echo 'MVC-ExEngine 7 -> Model '.$model_name.' not found. (Test Halted)'."\n";
				exit;
			} else
			if ($this->index->utSuite) {
				$this->index->utSuite->write("
									<b>MVC-ExEngine</b>
									<tab>
										Model ".$model_name." not found. (Test Halted).");
				exit;
			}
			else
				$this->ee->errorExit("MVC-ExEngine","Model not found. ( ".$model_name." )
									</br>
									<b>Trace:</b>
									<br/>
									Controller: ".get_class($this)."
									<br/>
									Function: ".$this->functionName,"ExEngine_MVC_Implementation_Library");
		}		
	}
	
	final function debug ($msg) {
		//$this->index->debugController($msg);
		$this->ma->d($msg);
		//$this->ee->debugThis("eemvc-".get_class($this),$msg);
	}
	
	final function loadView($filename,$data=null,$return=false,$dynamic=true,$checkmime=false) {	
		
		$view_fileo = $this->index->viewsFolder."/".$filename;
		//print $view_fileo;	
		
		$view_file = $view_fileo;	
		
		if (!file_exists($view_file)) {
			$view_file = $view_fileo.".php";
		}
		
		if (!file_exists($view_file)) {
			$view_file = $view_fileo.".html";
		}

		if (!file_exists($view_file)) {
			$view_file = $view_fileo.".phtml";
		}
		
		if ($checkmime) {
			$this->ee->eeLoad("mime");
			$eemime = new eemime($this->ee);
			$mime_type = $eemime->getMIMEType($view_file);				
			$this->debug("mvcee_controller.php:". __LINE__ . ": specialLoadViewStatic: File Mime Type: ".$mime_type);
		}
		
		if (file_exists($view_file) && !is_dir($view_file)) {
			
			$this->debug("mvcee_controller.php:". __LINE__ . ": loadView: Loading: ".$view_file);

			$tra = null;
			if ($this->index->trailingSlashLegacy) {
				$tra = "/";
			}

			$data["EEMVC_SF"] = $this->index->staticFolderHTTP.$tra;
			$data["EEMVC_SFTAGGED"] =  $this->index->controllersFolderHTTP.$tra."?EEMVC_SPECIAL=STATICTAGGED&FILE=";
			
			$x[0] = null;
			if (!$this->index->rewriteRulesEnabled) {
				$x = $_SERVER['REQUEST_URI'];		
				$x = explode($this->index->indexname,$x);
			}

			$data["EEMVC_HOME"] = "//" . $_SERVER['HTTP_HOST'] . $x[0];
			$data["EEMVC_C"] = $this->index->controllersFolderHTTP.$tra;
			$data["EEMVC_SCFOLDER"] = $this->index->sameControllerFolderHTTP.$tra;
			$data["EEMVC_SC"] = $this->index->sameControllerFolderHTTP.$this->index->controllername.$tra;
			$data["EEMVC_SCF"] = $this->index->sameControllerFolderHTTP.$this->functionName.$tra;
			
			$data["EEMVC_VS"] = $this->index->controllersFolderHTTP.$tra."?EEMVC_SPECIAL=VIEWSIMULATOR&VIEW=";
			
			$jq = new jquery($this->ee);
			$jqstr = $jq->load($this->index->jQueryVersion,true);
			$data["EEMVC_JQUERY"]  = $jqstr; 
			$jqstr2 = $jq->load_ui($this->index->jQueryUITheme,$this->index->jQueryUIVersion,true);			
			$data["EEMVC_JQUERYUI"]  = $jqstr2; 
			$jqstr3 = $jq->load_migrate(true);
			$data["EEMVC_JQUERYMIGRATE"] = $jqstr3;

			extract($data);	
			
			ob_start();	
			
			if ($dynamic) {
				if ((bool) @ini_get('short_open_tag') === FALSE)
				{
					$this->debug("mvcee_controller.php:". __LINE__ . ": loadView: Mode: ShortTags_Rewriter");
					echo eval('?>'.preg_replace("/;*\s*\?>/", "; ?>", str_replace('
									<?=', '<?php echo ', file_get_contents($view_file))));
				}
				else
				{		
					$this->debug("mvcee_controller.php:". __LINE__ . ": loadView: Mode: Include");	

					include($view_file);
				}
			}
			else
			{
				$this->debug("mvcee_controller.php:". __LINE__ . ": loadView: Mode: ReadFile");
				readfile($view_file);
			}
			
			$this->debug("mvcee_controller.php:". __LINE__ . ": loadView: Mode: View loaded: ".$view_file);
			
			$output = ob_get_contents();
			ob_end_clean();
			
			//$this->index->debug($output);
			
			if ($return)
			{
				return $output;
			} else {
				if ($checkmime)
					header('Content-type: '.$mime_type);
				echo $output;
			}				
		} else {
			$this->ee->errorExit("MVC-ExEngine","View (".$view_file.") not found.","eemvcil");
		}
	}
}
?>