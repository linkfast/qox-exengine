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

namespace ExEngine\MVC;


/**
 * Class Controller
 * @version 0.0.2.3
 * @package ExEngine\MVC
 */
class Controller {

	/* @var $ee \exengine */
	public $ee; /// Parent EE7 Object.
	/* @var $index Index */
	public $index; /// Parent eemvc_index object.
	/* @var $db \eedbm */
	public $db; /// Default database object, should be loaded first using $this->loadDb.
	public $functionName; /// The name of the in-use function.

	/* @var $r Methods */
	public $r; /// Input data methods

	/* @var $ma \eema */
	public $ma; /// EE Message Agent.
	
	public static $im; /// don't remenber ... :(

	/* @var $inst Controller */
	private static $inst; /// This contoller instance.
	
	public $imSilent = false; /// Set this controller to silent, useful for writing ajax/comet servers.

    public $layout = 'default'; /// Set this to use other layout than the default.
    public $layoutData = []; /// Set this to pass data to the layout view.
	public $tracerEnabled = true;
    public $pageTitle = ''; /// Set this to pass the page title to the Layout.

	public $locale='default'; /// Set to other locale than default to change on load.

	public $I18n;
	
	/// Default constructor, cannot be overriden, private __atconstruct function should be created in the controller to create a custom event.
	/**
	 * @param \exengine $ee
	 * @param Index $parent
	 */
	final function __construct(&$ee,&$parent) {
		$this->ma = new \eema('eemvcc-'.get_class($this), 'MVC-EE Controller "'.get_class($this).'".');

		$this->ee = &$ee;
		$this->index = &$parent;
		self::$inst =& $this;
		
		$this->r = new Methods($this);
		if (method_exists($this,'__atconstruct')) {
			$fn = $this->functionName;
			$this->functionName = "__atconstruct";
			$this->__atconstruct();	
			$this->functionName = $fn;
		}

		$this->I18n = new I18n();
	}
	
	/// Connection static function.
	/**
	 * @return Controller
	 */
	public static function &get_instance()
	{
		return self::$inst;
	}

	/**
	 * Load Assets from the Static folder or Remote using the same format as the app/assets files.
	 * @param string $Type
	 * @param string $YamlString
	 * @param bool $Return
	 * @return string|void
	 */
	final function loadAssets($Type='',$YamlString='',$Return=false) {
		return $this->index->LayoutAssetsLoader->loadAssets_ControllerView($Type,$YamlString,$Return);
	}

	/**
	 * Add more assets to the layout than the stablished in the app/assets files.
	 * @param string $Type
	 * @param string $YamlString
	 */
	final function loadAssetsToLayout($Type='',$YamlString='') {
		if ($this->imSilent) {
			$this->ee->errorExit('Controller : ' . get_class($this),'Cannot set new assets to layout if `$this->imSilent` is set to true.');
		} else {
			$this->index->LayoutAssetsLoader->loadAssets_String($Type,$YamlString);
		}
	}
	
	/// Connects to the default or a connection array specified database (100% compatible with EE DB Manager, depends on its version).
	/**
	 * @param string $dbObj
	 */
	final function loadDB($dbObj="default") {
		if (is_array($dbObj)) {
			$this->ee->errorExit('Controller : ' . get_class($this),'`$this->loadDb()` only accepts the name of the database configuration file (that are inside config/database folder), using EEDBM object is deprecated.');
		} else {
			$this->ee->eeLoad('eespyc');
			$YW = new \eespyc();
			$YW->load();
			$DBArr = \ExEngine\Extended\Spyc\Spyc::YAMLLoad('config/database/' . $this->index->AppConfiguration->DefaultDatabase . '.yml');
			if ($dbObj!='default') {
				if (file_exists('config/database/' . $dbObj . '.yml')) {
					$DBArr = \ExEngine\Extended\Spyc\Spyc::YAMLLoad('config/database/' . $dbObj . '.yml');
				} else {
					$this->ee->errorExit('Controller : ' . get_class($this),'Database configuration file `'.$dbObj.'` not found.');
				}
			}
			if ($DBArr['type']=='mongodb')
				$this->ee->errorExit('Controller : ' . get_class($this),'MongoDB is not supported by ExEngine Database Manager, you can use this database only with DBO Models.');
			else {
				$this->db = new \eedbm($this->ee,$DBArr);
			}
		}
	}
	
	/// Loads a model, by default will create an object with the same name.
	/**
	 * @param string $Model_Path Path to the model class file.
	 * @param string $Object_Name Set to null to create automatically the object based on class name.
	 * @param bool $Create_Object Set to false to avoid creating the object.
	 * @return mixed
	 */
	final function loadModel($Model_Path, $Object_Name=null,$Create_Object=true) {
		$model_name = $Model_Path;
		$obj_name = $Object_Name;
		$create_obj = $Create_Object;
		$this->debug("mvcee_controller.php:". __LINE__ . ": loadModel: Load: ".$model_name);
		if ($this->index->unitTest && defined('STDIN') && !$this->index->utSuite) {
			echo 'MVC-ExEngine -> Loading dependency model '.ucfirst($model_name).'.'."\n";
		} else		
		if ($this->index->utSuite)
			$this->index->utSuite->write("<b>MVC-ExEngine</b><tab>Loading dependency model ".ucfirst($model_name).".");
		$m_file = $this->index->modelsFolder."/".$model_name.".php";
		if (file_exists($m_file)) {
			include_once($m_file);

			$model_name = explode("/",$model_name);
			$model_name = $model_name[(count($model_name)-1)];
			$model_name = ucfirst($model_name);

            $callers=debug_backtrace();
            $this->index->addModelToArray($Model_Path,get_class($this) . '/' . $callers[1]['function']);

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
				$this->index->utSuite->write("<b>MVC-ExEngine</b><tab>Model ".$model_name." not found. (Test Halted).");
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

	/**
	 * @param string $msg
	 */
	final function debug ($msg) {
		//$this->index->debugController($msg);
		$this->ma->d($msg);
		//$this->ee->debugThis("eemvc-".get_class($this),$msg);
	}

	final function log() {
		return $this->ma;
	}

	/**
	 * @param string $filename
	 * @param array $data
	 * @param bool $return
	 * @param bool $dynamic
	 * @param bool $checkmime
	 * @return mixed
	 */
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
			
			$jq = new \jquery($this->ee);
			$jqstr = $jq->load($this->index->jQueryVersion,true);
			$data["EEMVC_JQUERY"]  = $jqstr; 
			$jqstr2 = $jq->load_ui($this->index->jQueryUITheme,$this->index->jQueryUIVersion,true);			
			$data["EEMVC_JQUERYUI"]  = $jqstr2; 
			$jqstr3 = $jq->load_migrate(true);
			$data["EEMVC_JQUERYMIGRATE"] = $jqstr3;
			$data["I18n"] = $this->I18n;

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

            $callers=debug_backtrace();
            $this->index->addViewToArray($filename,get_class($this).'/'.$callers[1]['function']);

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