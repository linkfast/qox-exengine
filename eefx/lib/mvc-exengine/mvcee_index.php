<?php
/**
@file mvcee_index.php
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

ExEngine / Libs / MVC-ExEngine / Index Class

ExEngine MVC Implementation Library

 */

namespace {
    /// Get Instance Function, connects controllers.
    function &eemvc_get_instance()
    {
        return ExEngine\MVC\Controller::get_instance();
    }

    function &eemvc_get_index_instance() {
        return ExEngine\MVC\Index::get_instance();
    }
}

namespace ExEngine\MVC {

    class Session {
        var $Enabled = false;
        var $Name = 'MVC-EXENGINE_SESSIONID';
        var $Lifetime = 0;
        var $Path = '/';
        var $Domain = null;
    }

    class Index {

        const VERSION = "0.0.3.1"; /// Version of EE MVC Implementation library.

        private $ee; /// This is the connector to the main ExEngine object.
        public $controllername; /// Name of the Controller in use.
        public $defcontroller=null;

        public $showDefaultView=true;

        public $appFolder = "app";
        public $viewsFolder = "views"; /// Name of the views folder, should be relative to the index file.
        public $modelsFolder = "models"; /// Name of the models folder, should be relative to the index file.
        public $controllersFolder = "controllers" ; /// Name of the controllers folder, should be relative to the index file.
        public $staticFolder = "static"; /// Name of the static folder, should be relative to the index file.
        public $indexname = "index.php"; /// Name of the index file, normally this should not be changed.

        /** @var \ExEngine\MVC\DefaultApplicationConfig|null */
        public $AppConfiguration = null; /// Application Configuration Class.

        private $controllerLayout = 'default';
        private $layoutAdditionalData = null;

        public $AlwaysSilent=false; /// Set to true if you do not want to show warnings or slogans to the rendered pages, this is a global variable, you can set silent to a specific controller by setting the $this->imSilent variable to true.

        public $dgEnabled = false; /// Enable EE's DevGuard for the project.
        public $dgKey=null; /// EE's DevGuard ServerKey.

        public $BootstrapEnabled = false; /// Enable Twitter's Bootstrap libraries loading.
        public $jQueryEnabled = false; /// Set to true if you want jQuery Enabled.
        public $jQueryUITheme="base"; /// Default EEMVC JQuery UI Theme.
        public $jQueryVersion = null; /// Default EEMVC JQuery JS Lib Version.
        public $jQueryUIVersion = null; /// Default EEMVC JQuery UI JS Lib Version.
        public $jQueryObject=null;

        public $errorHandler=false; /// Set to the error handler controller name, that controller should be made using the error controller template.

        public $urlParsedData; /// Parsed data from the URL string, please do not modify in runtime.

        public $staticFolderHTTP; /// HTTP path (URL) to the static folder, made for views rendering.
        public $viewsFolderHTTP;  /// HTTP path (URL) to the views folder, made for views rendering.
        public $modelsFolderHTTP;  /// HTTP path (URL) to the models folder, made for views rendering.
        public $controllersFolderHTTP;  /// HTTP path (URL) to the controllers folder, made for views rendering.
        public $controllersFolderR=null;

        public $rewriteRulesEnabled = false; /// Only when using rewrite rules for clean url, Note for NGINX: works only in root directory of domain/subdomain .
        public $rewriteBaseFolder = ''; /// Set if your app is working in a subfolder and rewriteRulesEnabled is true.
        public $trailingSlashLegacy = false;

        public $sameControllerFolderHTTP;
        /* @var $LayoutAssetsLoader LayoutAssets */
        public $LayoutAssetsLoader;

        public $ma; /// EE Message Agent.

        public $actualInputQuery;
        public $unModUrlParsedData;

        private $routes = array();

        private $loadedViews = [];
        private $loadedModels = [];

        private $origControllerFolderName;

        private $SessionEnabled = false;
        private $TracerEnabledInController = true;

        private $ControllerPageTitle = '';

        /* @var $r Methods */
        public $r;

        public function addModelToArray($ModelFile,$ControllerName) {
            $this->loadedModels['Controllers'][$ControllerName][] = $ModelFile;
        }

        public function addViewToArray($ViewFile,$ControllerName) {
            $this->loadedViews['Controllers'][$ControllerName][] = $ViewFile;
        }

        private static $inst;
        /// Connection static function.
        public static function &get_instance()
        {
            if (self::$inst instanceof Index)
                return self::$inst;
            else
                return false;
        }

        final protected function composerAutoLoad() {
            $this->ee->eeLoad('composer');
            $C = new \ExEngine\Extended\Composer();
            $C->autoload();
        }

        final protected function checkDependencies() {
            // eespyc unittest mime composer
            $eTitle = 'MVC-ExEngine : Dependencies not met';
            if (!$this->ee->eeExists('eespyc')) {
                $this->ee->errorExit($eTitle,'SpyC ExEngine wrapper must be installed, check your extended engines folder.');
            }
            if (!$this->ee->eeExists('unittest')) {
                $this->ee->errorExit($eTitle,'ExEngine Unit Testing Suite must be installed, check your extended engines folder.');
            }
            if (!$this->ee->eeExists('mime')) {
                $this->ee->errorExit($eTitle,'ExEngine Mime Type Detector must be installed, check your extended engines folder.');
            }
            if (!$this->ee->eeExists('composer')) {
                $this->ee->errorExit($eTitle,'ExEngine Composer Extended Engine must be installed, check your extended engines folder.');
            }
        }

        final protected function cfgCheck(DefaultApplicationConfig $Configuration) {
            /* check mvc folders */
            if (!file_exists($Configuration->AppFolder . '/' . $Configuration->ControllersFolder) and
                !is_dir($Configuration->AppFolder . '/' . $Configuration->ControllersFolder)) {
                $this->ee->errorExit('MVC-ExEngine','Configuration file invalid.<br><br>Controllers folder does not exists.');
            }
            if (!file_exists($Configuration->AppFolder . '/' . $Configuration->ModelsFolder) and
                !is_dir($Configuration->AppFolder . '/' . $Configuration->ModelsFolder)) {
                $this->ee->errorExit('MVC-ExEngine','Configuration file invalid.<br><br>Models folder does not exists.');
            }
            if (!file_exists($Configuration->AppFolder . '/' . $Configuration->ViewsFolder) and
                !is_dir($Configuration->AppFolder . '/' . $Configuration->ViewsFolder)) {
                $this->ee->errorExit('MVC-ExEngine','Configuration file invalid.<br><br>Views folder does not exists.');
            }
            if (!file_exists($Configuration->StaticFolder) and
                !is_dir($Configuration->StaticFolder)) {
                $this->ee->errorExit('MVC-ExEngine','Configuration file invalid.<br><br>Static folder does not exists.');
            }
            if (!file_exists($Configuration->SafeStorageFolder) and
                !is_dir($Configuration->SafeStorageFolder)) {
                $this->ee->errorExit('MVC-ExEngine','Configuration file invalid.<br><br>Safe Storage folder does not exists.');
            }
            if (!file_exists($Configuration->ConfigurationFolder) and
                !is_dir($Configuration->ConfigurationFolder)) {
                $this->ee->errorExit('MVC-ExEngine','Configuration file invalid.<br><br>Configuration folder does not exists.');
            }

            /* check mvc configuration files */
            /* check default database */
            $CfFile =  $Configuration->ConfigurationFolder . '/database/' . $Configuration->DefaultDatabase .'.yml' ;
            if (!file_exists($CfFile)) {
                $this->ee->errorExit('MVC-ExEngine','Configuration file invalid.<br><br>Default database configuration file does not exists, even if you are not going to use a database, the default database file must exist.');
            } else {
                /* check database connection */
                $this->ee->eeLoad('eespyc');
                $YW = new \eespyc();
                $YW->load();
                $dbCfgArr = \ExEngine\Extended\Spyc\Spyc::YAMLLoad($CfFile);



                if ($dbCfgArr['check']) {
                    if ($dbCfgArr['type']=='mongodb'){
                        if (!class_exists("MongoClient")) {
                            $this->ee->errorExit("MVC-ExEngine",
                                "MongoDB PHP driver is not installed, you cannot use a MongoDB database.");
                        } else {
                            try
                            {
                                new \MongoClient($dbCfgArr['host']);
                            }
                            catch ( \MongoConnectionException $e )
                            {
                                $this->ee->errorExit("MVC-ExEngine",
                                    "MongoDB connection string is invalid, cannot connect to database, maybe auth issues?, check log for details.");
                            }
                        }
                    } else {
                        $eed = new \eedbm($this->ee,$dbCfgArr);
                        $eed->open(false);
                        if (!$eed->isConnected() or !$eed->isDbSelected()) {
                            $this->ee->errorExit("MVC-ExEngine",
                                "Cannot connect to " . $dbCfgArr['type'] . " database, error: " . $eed->errorLatest() .
                                ($this->ee->strContains($eed->errorLatest(),'Unknown database') ? '<br><br>To create the schema you can run `php mvctool.php -db default create` in the terminal.' : ''));
                        }
                    }
                }
            }
        }

        /**
         * @param DefaultApplicationConfig $Configuration
         */
        final function __construct(DefaultApplicationConfig &$Configuration) {

            if (ee_gi() instanceof \ExEngine\Core) {
                $this->ee = &ee_gi();
            } else {
                print "MVC-ExEngine Error: Instance of ExEngine is required (7.0.8.41+).";
                exit();
            }

            if (version_compare($this->ee->miscGetVersion(),'7.0.8.41','<')) {
                $this->ee->errorExit('MVC-ExEngine','ExEngine version must be greater or equal to 7.0.8.41, previous versions are not supported.');
            }

            /* Set Application Path (Root) */
            $this->ee->appPath = getcwd() . '/';

            /* check dependencies */
            $this->checkDependencies();

            /* check configuration instance */
            if (!($Configuration instanceof DefaultApplicationConfig)) {
                $this->ee->errorExit('MVC-ExEngine','Application Configuration is required.');
                exit();
            } else {
                $Configuration->ApplicationInit();
            }

            $this->cfgCheck($Configuration);

            /* set ee's app name */
            $this->ee->appName = $Configuration->ExEngineApplicationName;

            /* load composer autoload.php */
            if ($Configuration->ComposerAutoload) { $this->composerAutoLoad(); }

            /* check session and start */
            if ($Configuration->SessionCfg instanceof Session) {
                $SessionMode = $Configuration->SessionCfg;
            } else {
                $SessionMode = new Session();
                $SessionMode->Enabled = false;
            }

            $this->SessionEnabled = $SessionMode->Enabled;

            //print_r($this->SessionEnabled);

            if ($this->SessionEnabled===true) {
                if (!defined('EE_UTS') and !$Configuration->UsingFromCLI) {
                    $this->ee->sessionCreator($SessionMode->Name, $SessionMode->Lifetime, $SessionMode->Path, $SessionMode->Domain);
                }

                $this->ma = new \eema("eemvci","MVC-EE Index.");
                if (!isset($_SESSION['MVC_EXENGINE_TRACER_STATE'])) {
                    $_SESSION['MVC_EXENGINE_TRACER_STATE'] = $Configuration->Tracer;
                }
            }

            if (!defined('EE_UTS') and !$Configuration->UsingFromCLI and strlen($_SERVER['HTTP_HOST'])==0)
                $this->ee->errorExit("MVC-ExEngine","HTTP_HOST is not defined, check your PHP or HTTP Server configuration.");

            $this->debug("eemvcil.php:". __LINE__ . ": MVC Initialized.");
            if ($Configuration->DefaultController==null) {
                $this->debug("eemvcil.php:". __LINE__ . ": Index: No default controller set.");
            } else
                $this->defcontroller = $Configuration->DefaultController;

            if ($Configuration->ErrorHandler != null) {
                $this->errorHandler = $Configuration->ErrorHandler;
            }

            $this->appFolder = $Configuration->AppFolder;
            $this->rewriteRulesEnabled  = $Configuration->RewriteRulesEnabled;
            $this->rewriteBaseFolder = $Configuration->RewriteBaseFolder;

            $this->dgEnabled = $Configuration->DevGuard;
            $this->dgKey = $Configuration->DevGuardKey;

            $this->jQueryEnabled = $Configuration->EEjQueryEnabled;
            $this->jQueryUITheme = $Configuration->EEjQueryUITheme;
            $this->jQueryVersion = $Configuration->EEjQueryVersion;
            $this->jQueryUIVersion = $Configuration->EEjQueryUIVersion;

            $this->controllersFolder = $this->appFolder . '/' . $this->controllersFolder;
            $this->viewsFolder = $this->appFolder . '/' . $this->viewsFolder;
            $this->modelsFolder = $this->appFolder . '/' . $this->modelsFolder;

            /* init views array */
            $this->loadedViews['Layout'] = [];
            $this->loadedViews['Controllers'] = [];

            self::$inst = &$this;
            $this->AppConfiguration = $Configuration;
        }

        final function isSessionEnabled() {
            return $this->SessionEnabled;
        }

        # ExEngine UnitTesting
        var $unitTest = false;
        /* @var $utSuite \EEUnitTest_Suite */
        public $utSuite;
        final function prepareUnitTesting() {
            $this->ee->eeLoad("unittest");
            $eeunit = &eeunit_get_instance();
            if ($eeunit) {
                $this->utSuite = &$eeunit;
                $this->utSuite->write("<b>MVC-ExEngine</b><tab>ExEngine Unit Testing Suite Detected.");
            }
            $this->debug("eemvcil.php:". __LINE__ . ": Unit Testing Mode");
            if(defined('STDIN') && !$this->utSuite) {
                echo 'MVC-ExEngine 7 -> Unit Testing Mode ENABLED'."\n";
            } else {
                $this->utSuite->write("<b>MVC-ExEngine</b><tab>Unit Testing Mode <green>Enabled</green>");
            }
            $this->unitTest = true;
        }

        final function addRoute($Pattern,$Destination) {
            $this->routes[$Pattern] = $Destination;
        }

        final function prepareController($Controller) {
            $Controller = strtolower($Controller);
            if (file_exists($this->controllersFolder.'/'.$Controller.".php")) {
                if(defined('STDIN') && !$this->utSuite) {
                    echo 'MVC-ExEngine 7 -> Preparing controller '.ucfirst($Controller)." for unit testing.\n";
                } else {
                    $this->utSuite->write("<b>MVC-ExEngine</b><tab>Preparing controller ".ucfirst($Controller)." for unit testing.");
                }
                include_once($this->controllersFolder.'/'.$Controller.".php");
                $Controller = ucfirst($Controller);
                $Controller = new $Controller($this->ee,$this);
                return $Controller;
            } else {
                if(defined('STDIN') && !$this->utSuite) {
                    echo 'MVC-ExEngine 7 -> Controller '.ucfirst($Controller).' Not Found. (Test Halted)'."\n";
                    exit;
                } elseif ($this->utSuite) {
                    $this->utSuite->write("<b>MVC-ExEngine</b><tab>Controller ".ucfirst($Controller)." Not Found. (Test Halted)");
                    exit;
                }
                else
                    $this->ee->errorExit("MVC-ExEngine","Controller ".ucfirst($Controller)." Not Found. (Test Halted)");
            }
        }

        final function prepareModel(Controller $controller, $model_name) {
            $controller->loadModel($model_name,null,false);
            $model_name = explode("/",$model_name);
            $model_name = $model_name[(count($model_name)-1)];
            $model_name = ucfirst($model_name);
            $modelx = new $model_name();
            return $modelx;
        }
        #ExEngine UnitTesting

        final function loadView($filename,$data=null,$return=false,$dynamic=true,$checkmime=false) {
            return $this->specialLoadViewStatic($filename,false,$checkmime,$data,$dynamic,$return);
        }

        /// Loads a view for the View Simulator, useful for designers that want to test the basic functionality of their pages.
        final function specialLoadViewStatic($filename, $fullpath=false, $checkmime=false, $data=null, $dynamic=true, $Return = false) {

            if ($fullpath) {
                $view_fileo = $filename;
            }
            else
                $view_fileo = $this->viewsFolder."/".$filename;

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

            if (file_exists($view_file)) {

                $this->debug("eemvcil.php:". __LINE__ . ": specialLoadViewStatic: Loading: ".$view_file);

                if ($checkmime) {
                    $this->ee->eeLoad("mime");
                    $eemime = new \eemime($this->ee);
                    $mime_type = $eemime->getMIMEType($view_file);
                    $this->debug("eemvcil.php:". __LINE__ . ": specialLoadViewStatic: File Mime Type: ".$mime_type);
                }

                $tra = null;
                if ($this->trailingSlashLegacy) {
                    $tra = "/";
                }

                $data["EEMVC_SF"] = $this->staticFolderHTTP . $tra;
                $data["EEMVC_SFTAGGED"] =  $this->controllersFolderHTTP."?EEMVC_SPECIAL=STATICTAGGED&FILE=";
                $data["EEMVC_C"] = $this->controllersFolderHTTP . $tra;
                $x[0] = null;
                if (!$this->rewriteRulesEnabled) {
                    $x = $_SERVER['REQUEST_URI'];
                    $x = explode($this->indexname,$x);
                }
                $data["EEMVC_HOME"] = "//" . $_SERVER['HTTP_HOST']. $x[0];
                $data["EEMVC_SC"] = $this->controllersFolderHTTP .$tra."?EEMVC_SPECIAL=VIEWSIMULATOR&VIEW=".$view_file."&ERROR=NODYNAMIC&";
                $data["EEMVC_SCF"] = $this->controllersFolderHTTP.$tra."?EEMVC_SPECIAL=VIEWSIMULATOR&VIEW=".$view_file."&ERROR=NODYNAMIC&";
                $data["EEMVC_SCFOLDER"] = $this->controllersFolderHTTP .$tra."?EEMVC_SPECIAL=VIEWSIMULATOR&VIEW=".$view_file."&ERROR=NODYNAMIC&";

                $data["EEMVC_VS"] = $this->controllersFolderHTTP.$tra."?EEMVC_SPECIAL=VIEWSIMULATOR&VIEW=";

                if ($this->jQueryEnabled) {
                    if (!$this->jQueryObject)
                        $this->jQueryObject = new \jquery($this->ee);
                    $jqstr = $this->jQueryObject->load($this->jQueryVersion,true);
                    $jqstr2 = $this->jQueryObject->load_ui($this->jQueryUITheme,$this->jQueryUIVersion,true);
                    $jqstr3 = $this->jQueryObject->load_migrate(true);
                } else {
                    $jqstr = $jqstr2 = $jqstr3 = '
						<!-- MVC-EXENGINE: jQuery is not enabled. -->
						';

                }
                $data["EEMVC_JQUERY"]  = $jqstr;
                $data["EEMVC_JQUERYUI"]  = $jqstr2;
                $data["EEMVC_JQUERYMIGRATE"] = $jqstr3;

                $data["I18n"] = new I18n();

                extract($data);

                ob_start();

                if ($dynamic) {
                    if ((bool) @ini_get('short_open_tag') === FALSE)
                    {
                        $this->debug("eemvcil.php:". __LINE__ . ": loadView: Mode: ShortTags_Rewriter");
                        echo eval('?>'.preg_replace("/;*\s*\?>/", "; ?>", str_replace('
						<?=', '<?php echo ', file_get_contents($view_file))));
                    }
                    else
                    {
                        $this->
                        debug("eemvcil.php:". __LINE__ . ": specialLoadViewStatic: Mode: Include");
                        include($view_file);
                    }
                }
                else
                {
                    $this->debug("eemvcil.php:". __LINE__ . ": specialLoadViewStatic: Mode: ReadFile");
                    readfile($view_file);
                }

                $this->debug("eemvcil.php:". __LINE__ . ": specialLoadViewStatic: View loaded: ".$view_file);

                $output = ob_get_contents();
                ob_end_clean();

                if ($checkmime)
                    header('Content-type: '.$mime_type);

                if ($Return)
                    return $output;
                else
                    print $output;

            } else {
                $this->ee->errorExit("MVC-ExEngine","View (".$view_file.") not found.","eemvcil");
            }
        }

        /// This function will start the MVC listener, should be called in the index file.
        final function start() {

            if (isset($this->SessionMode) && $this->SessionMode) {
                $this->ee->errorExit('MVC-ExEngine','mvcee_index::SessionMode property is deprecated, please set the session options at construct time.');
            }

            $this->parseURL();

            //print "<h1>" . $this->urlParsedData[1] . "</h1>";

            if ($this->dgEnabled and !in_array($this->urlParsedData[0],$this->AppConfiguration->DevGuardExceptions)) {
                $dg = new \ee_devguard();
                $dg->guard($this->dgKey);
            }

            if (!$this->jQueryObject && $this->jQueryEnabled)
                $this->jQueryObject = new \jquery($this->ee);

            if (!$this->ee->argsGet("SilentMode")) {
                print "
						<h1>
							MVC-ExEngine can not work with SilentMode argument set to FALSE. Please set it to TRUE.
						</h1>
						";
                exit();
            }

            $this->setStaticFolder();
            $this->origControllerFolderName = $this->controllersFolder;

            if (isset($_GET['EEMVC_SPECIAL'])) {

                switch ($_GET['EEMVC_SPECIAL']) {
                    case 'VIEWSIMULATOR':
                        if ($this->ee->cArray["debug"]) {
                            if (isset($_GET['ERROR'])) if ($_GET['ERROR'] == "NODYNAMIC") $this->ee->errorExit("EEMVCIL","EEMVC_SPECIAL: EEMVC_SC and EEMVC_SCF special tags does no work in the Views Simulator.",null,true);
                            $this->specialLoadViewStatic($_GET['VIEW']);
                        } else {
                            $this->ee->errorExit("MVC-ExEngine","VIEWSIMULATOR doesn´t work in production mode. (Enable debug mode first)","eemvcil");
                        }
                        break;
                    case 'TRACER_ON':
                        if ($this->SessionEnabled) {
                            $_SESSION['MVC_EXENGINE_TRACER_STATE'] = true;
                            $this->ee->errorExit("MVC-ExEngine","This is not an error, MVC-ExEngine Tracer is enabled, reload this page without the ?EEMVC_SPECIAL=TRACER_ON to rerun your controller.");
                        } else {
                            $this->ee->errorExit("MVC-ExEngine","Enabling or disabling tracer at runtime requires session support, if you want to enable Tracer set " .'´$this->Tracer = true;´ in the application init method in config/eemvc.php.');
                        }
                        break;
                    case 'TRACER_OFF':
                        if ($this->SessionEnabled) {
                            $_SESSION['MVC_EXENGINE_TRACER_STATE'] = false;
                            $this->ee->errorExit("MVC-ExEngine","This is not an error, MVC-ExEngine Tracer is disabled, reload this page without the ?EEMVC_SPECIAL=TRACER_OFF to rerun your controller normally.");
                        } else {
                            $this->ee->errorExit("MVC-ExEngine","Enabling or disabling tracer at runtime requires session support, if you want to enable Tracer set " .'´$this->Tracer = true;´ in the application init method in config/eemvc.php.');
                        }
                        break;
                    case 'STATICTAGGED':
                        $file = $this->staticFolder.$_GET['FILE'];
                        $base_file = pathinfo($file, PATHINFO_FILENAME);
                        $base_path = pathinfo($file, PATHINFO_DIRNAME);
                        $res_php_file = $base_path . '/' . $base_file . '.php';
                        $data = null;
                        if (file_exists($res_php_file)) {
                            $this->debug('mvcee_index: Loading PHP preprocessor for a dynamic resource.');
                            include_once $res_php_file;
                        }
                        $this->specialLoadViewStatic($file, true, true, $data);
                        break;
                    default:
                        $this->ee->errorExit("EEMVCIL","EEMVC_SPECIAL: Mode Not Found.");
                        break;
                }

            } else {
                // !!
                if (!$this->rewriteRulesEnabled) {
                    /*
                    if (!$this->ee->strContains($_SERVER['REQUEST_URI'],$this->indexname)) {
                        header("Location: ".$_SERVER['REQUEST_URI'].$this->indexname);
                        exit();
                    }
                    */
                }

                $this->debug("eemvcil.php:". __LINE__ . ": Index: MVC Started, waiting to controller name, CONTROLLER_NAME.");


                /*

                    if ( ( ( empty($this->urlParsedData) && (substr($_SERVER['REQUEST_URI'],strlen($_SERVER['REQUEST_URI'])-1,1) != "/") )
                            ||
                            ( count($this->urlParsedData) > 0 && end($this->urlParsedData) != null )  )
                            &&
                            !$this->ee->strContains($_SERVER['REQUEST_URI'],"?",false)
                            ) {
                            //header("Location: ". $_SERVER['REQUEST_URI']."/" );
                            //exit();
                    } else if (!$this->ee->strContains($_SERVER['REQUEST_URI'],"/?",false) && substr($_SERVER['REQUEST_URI'],strlen($_SERVER['REQUEST_URI'])-1,1) != "/") {
                        //header("Location: ". str_replace("?","/?",$_SERVER['REQUEST_URI']) );
                        //exit();
                    }


                print $_SERVER['REQUEST_URI'];

        */
                //$this->debug(print_r($this->urlParsedData,1));
                // !!
                /*
                if (count($this->urlParsedData) > 0 && $this->ee->strContains($this->urlParsedData[count($this->urlParsedData)-1],"?") && !$this->ee->strContains($this->urlParsedData[count($this->urlParsedData)-1],"/?")) {
                    $this->debug("Index: Has GET Query and No '/'");
                    $ne = explode("?",$this->urlParsedData[count($this->urlParsedData)-1]);
                    $this->debug("Index: New Last index (".(count($this->urlParsedData)-1)."): ". $ne[0]);
                    $this->urlParsedData[count($this->urlParsedData)-1] = $ne[0];
                    $this->debug("Index: New url array: " .print_r($this->urlParsedData,1));
                }
                */

                /*
                if (isset($this->urlParsedData[0]) && (!empty($this->urlParsedData[0]))) {

                    $output = $this->load_controller($this->urlParsedData[0],$this->urlParsedData[1]);
                } else {
                    if ($this->defcontroller) {
                        $this->debug("Index: Loading default controller: ".$this->defcontroller);
                        $output = $this->load_controller($this->defcontroller,null);
                    }else {
                        $this->ee->errorExit("MVC-ExEngine","No default controller set.","eemvcil");
                    }
                }
                */

                if (!$this->AlwaysSilent) {
                    $this->LayoutAssetsLoader = new LayoutAssets($this->staticFolder,$this->staticFolderHTTP,$this->controllersFolderHTTP."/?EEMVC_SPECIAL=STATICTAGGED&FILE=");
                    $this->LayoutAssetsLoader->loadAssets();
                }

                $output = $this->load_controller($this->urlParsedData[0],$this->urlParsedData[1]);

                if (!$this->AlwaysSilent) {

                    $LayoutLoad = $this->AppConfiguration->DefaultLayout;
                    if (isset($this->controllerLayout) and $this->controllerLayout != 'default') {
                        $LayoutLoad = $this->controllerLayout;
                    }

                    if ($this->controllerLayout=='default'){
                        $this->loadedViews['Layout']['default'] = $LayoutLoad;
                    } else {
                        $this->loadedViews['Layout'][$LayoutLoad] = $LayoutLoad;
                    }

                    $TracerEnabledBySession = true;
                    if ($this->SessionEnabled) {
                        $TracerEnabledBySession = $_SESSION['MVC_EXENGINE_TRACER_STATE'];
                    }

                    /* load tracer */
                    if ($this->AppConfiguration->Tracer and
                        $TracerEnabledBySession and
                        $this->TracerEnabledInController) {
                        ob_start();
                        include_once ( $this->ee->libGetResPath('mvc-ee') . 'tracer.phtml' );
                        $output = ob_get_contents() . $output;
                        ob_end_clean();
                    }



                    $LayoutData = ["Content" => $output, "LayoutAssets" => $this->LayoutAssetsLoader,
                        "Title" => $this->ControllerPageTitle, "R" => $this->r];
                    if (is_array($this->layoutAdditionalData)) {
                        $LayoutData = array_merge($LayoutData,$this->layoutAdditionalData);
                    }

                    $output = $this->loadView('layouts/' . $LayoutLoad, $LayoutData,true);

                    $rpl = "<head>\n"."\t<!-- ".$this->ee->miscMessages("Slogan",1)." (MVC-ExEngine) -->";
                    if ($this->dgEnabled)
                        $rpl .= "\n\t" .
                            str_replace("\n", "\n\t", $dg->guard_float_menu(true));

                    $output = str_replace("<head>",$rpl,$output);
                }

                print $output;
            }
        }

        /// This function will call the controller, parse variables, session and render, the use of this function is totally automatic.
        private final function load_controller($name,$next) {
            if ($name != null)
                $this->controllername = $name;
            else
                $this->controllername = $this->defcontroller;
            $ctl_folder = $this->controllersFolder;
            if ($this->controllersFolderR != null) {
                $this->controllersFolder = $this->controllersFolderR;
            }
            if (substr($this->controllersFolder, -1) != "/") $this->controllersFolder = $this->controllersFolder. "/";
            if (!$this->ee->strContains($name,"/?")) {
                $name = str_replace("/?","?",$name);
            }
            if (!$this->ee->strContains($next,"/?")) {
                $next = str_replace("/?","?",$next);
            }
            $mystring = $name;
            $parts = explode("?", $mystring);
            $name = $parts[0];
            $mystring = $next;
            $parts = explode("?", $mystring);
            $next = $parts[0];
            //print "1". "<br/>";	print "XF: " .$this->controllersFolder."&nbsp;&nbsp;&nbsp;"; print "NAME: ". $name ."&nbsp;&nbsp;&nbsp;";	print "NE: ". $next . "<br/>";
            //print "TE: " . $this->controllersFolder.$name.".php" . "<br/>";
            $proceed = false;
            if (is_dir($this->controllersFolder.$name) && strlen($next)==0) {
                //print "IS DIR && SEARCH FOR DEFCONTROLLER" . "<br/>";
                $this->controllersFolder = $this->controllersFolder.$name;
                $this->urlParsedData = array_slice($this->urlParsedData, 1);
                //print $this->controllersFolder . $this->defcontroller . " " . $this->urlParsedData[1] .  "<br/>";
                $this->load_controller($this->defcontroller, $this->urlParsedData[1]);
            } elseif (is_dir($this->controllersFolder.$name) && strlen($next)>0) {
                //print "IS DIR && REPEAT FOR '$next'" . "<br/>";
                $this->controllersFolder = $this->controllersFolder.$name;
                $this->urlParsedData = array_slice($this->urlParsedData, 1);
                $this->load_controller($next, $this->urlParsedData[1]);
            } elseif (file_exists($this->controllersFolder.$name.".php")) {
                //print "CONTROLLER FOUND!";
                $proceed = true;
            } elseif (!file_exists($this->controllersFolder.$name."php")) {
                //print "FILE NOT EXISTS && CHECK IF METHOD EXISTS IN DEFCONTROLLER". "<br/>";;
                $proceed = true;
                $checkifmethodexistsindefcontroller=true;
            } else {
                //print "WHAT?";
                $this->ee->errorExit("MVC-ExEngine","Unexpected Error.");
                exit;
            }
            if ($proceed) {
                ob_start();
                $namel = $name.".php";
                /*
                if ($this->rewriteRulesEnabled) {
                    $strx = "//" . $_SERVER['HTTP_HOST'];
                } else
                    $strx = "//" . $_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];
                */
                if ($this->rewriteRulesEnabled) {
                    if (substr($_SERVER['HTTP_HOST'], -1) == "/")
                        $srv = substr($_SERVER['HTTP_HOST'].(strlen($this->rewriteBaseFolder)>0 ? '/'.$this->rewriteBaseFolder: ''), 0, -1);
                    else
                        $srv = $_SERVER['HTTP_HOST'].(strlen($this->rewriteBaseFolder)>0 ? '/'.$this->rewriteBaseFolder: '');

                    $strx = "//" . $srv;
                }
                else
                    $strx = "//" . $_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'];

                //print $strx;
                $this->controllersFolderHTTP = $strx;

                $this->sameControllerFolderHTTP = $strx.str_replace($this->origControllerFolderName,"",$this->controllersFolder);
                if (file_exists($this->controllersFolder.$namel)) {
                    $this->debug("eemvcil.php:". __LINE__ . ": Index: Loading controller: ".$this->controllersFolder.$name);
                    $this->debug("eemvcil.php:". __LINE__ . ": SCFH: ".$this->sameControllerFolderHTTP);

                    include_once($this->controllersFolder.$namel);
                    $no = $name;
                    $name = ucfirst($name);
                    /* @var $ctrl Controller */
                    $ctrl = new $name($this->ee,$this);
                    if (strlen($next) == 0) {
                        $next = "index";
                    }
                    $this->controllername = $no;
                    if(method_exists($name,$next)) {
                        $nslice = array_slice($this->urlParsedData, 1);

                        if (strlen($nslice[1]) == 0) {
                            if (method_exists($name,'__startup')) {
                                $ctrl->functionName = "__startup";
                                $ctrl->__startup();
                            }
                            $ctrl->functionName = $next;
                            call_user_func(array($ctrl, $next));
                            if (method_exists($name,'__atdestroy')) {
                                $ctrl->functionName = "__atdestroy";
                                $ctrl->__atdestroy();
                            }
                        } else {
                            if (method_exists($name,'__startup')) {
                                $ctrl->functionName = "__startup";
                                $ctrl->__startup();
                            }
                            $ctrl->functionName = $next;
                            call_user_func_array(array($ctrl, $next), array_slice($this->urlParsedData, 2));
                            if (method_exists($name,'__atdestroy')) {
                                $ctrl->functionName = "__atdestroy";
                                $ctrl->__atdestroy();
                            }
                        }
                    } else {
                        $refl = new \ReflectionMethod(get_class($ctrl), 'index');
                        $numParams = $refl->getNumberOfParameters();
                        if ($numParams > 0) {
                            if (method_exists($name,'__startup')) {
                                $ctrl->functionName = "__startup";
                                $ctrl->__startup();
                            }
                            $ctrl->functionName = 'index';
                            call_user_func_array(array($ctrl, 'index'), array_slice($this->urlParsedData, 1));
                            if (method_exists($name,'__atdestroy')) {
                                $ctrl->functionName = "__atdestroy";
                                $ctrl->__atdestroy();
                            }
                        } else {
                            $this->raiseError("e404mnf", [
                                "Error1_Type" => "Method not found",
                                "Error1_Msg" => "Method \"" . ucfirst($next) . "\" not found in \"" . $this->controllersFolder . ucfirst($name) . "\".",
                                "Error2_Type" => "Index function does not accept arguments."
                            ], $ctl_folder, true, __LINE__, __FILE__);
                        }
                    }
                    if ($ctrl->r instanceof Methods) {
                        $this->r = &$ctrl->r;
                    }
                    if (isset($ctrl->imSilent)) {
                        if ($ctrl->imSilent)
                            $this->AlwaysSilent = true;
                    }
                    if (isset($ctrl->pageTitle)) {
                        if (strlen($ctrl->pageTitle) > 0)
                            $this->ControllerPageTitle = $ctrl->pageTitle;
                    }
                    if (isset($ctrl->layout)) {
                        $this->controllerLayout = $ctrl->layout;
                    }
                    if (isset($ctrl->layoutData)) {
                        if (is_array($ctrl->layoutData))
                            $this->layoutAdditionalData = $ctrl->layoutData;
                    }
                    $this->TracerEnabledInController = $ctrl->tracerEnabled;
                }
                if (!file_exists($this->controllersFolder.$namel) && file_exists($this->controllersFolder.$this->defcontroller.".php")) {
                    include_once($this->controllersFolder.$this->defcontroller.".php");
                    $name2 = ucfirst($this->defcontroller);
                    $ctrl = new $name2($this->ee,$this);
                    if (isset($ctrl->imSilent)) {
                        if ($ctrl->imSilent)
                            $this->AlwaysSilent = true;
                    }
                    if ($checkifmethodexistsindefcontroller) {

                        if(method_exists($name2,$name)) {
                            $this->controllername = $this->defcontroller;
                            if (strlen($next) == 0) {
                                if (method_exists($name2,'__startup')) {
                                    $ctrl->functionName = "__startup";
                                    $ctrl->__startup();
                                }
                                $ctrl->functionName = $name;
                                call_user_func(array($ctrl, $name));
                                if (method_exists($name2,'__atdestroy')) {
                                    $ctrl->functionName = "__atdestroy";
                                    $ctrl->__atdestroy();
                                }
                            } else {
                                if (method_exists($name2,'__startup')) {
                                    $ctrl->functionName = "__startup";
                                    $ctrl->__startup();
                                }
                                $ctrl->functionName = $name;
                                //print_r(array_slice($this->urlParsedData, 1));
                                //print 'index: ' . 1;
                                call_user_func_array(array($ctrl, $name), array_slice($this->urlParsedData, 1));
                                if (method_exists($name2,'__atdestroy')) {
                                    $ctrl->functionName = "__atdestroy";
                                    $ctrl->__atdestroy();
                                }
                            }
                            if ($ctrl->r instanceof Methods) {
                                $this->r = &$ctrl->r;
                            }
                            if (isset($ctrl->imSilent)) {
                                if ($ctrl->imSilent)
                                    $this->AlwaysSilent = true;
                            }
                            if (isset($ctrl->pageTitle)) {
                                if (strlen($ctrl->pageTitle) > 0)
                                    $this->ControllerPageTitle = $ctrl->pageTitle;
                            }
                            if (isset($ctrl->layout)) {
                                $this->controllerLayout = $ctrl->layout;
                            }
                            if (isset($ctrl->layoutData)) {
                                if (is_array($ctrl->layoutData))
                                    $this->layoutAdditionalData = $ctrl->layoutData;
                            }
                            $this->TracerEnabledInController = $ctrl->tracerEnabled;
                        } else {
                            $this->raiseError("e404mnf",array("Error1_Type"=> "Controller not found", "Error1_Msg" => "Controller \"".ucfirst($this->urlParsedData[0]). "\" not found in \"".$this->controllersFolder."\". ", "Error2_Type" => "Method in default controller not found", "Error2_Msg"=>"Method \"".ucfirst($this->urlParsedData[0]). "\" not found in \"".$this->controllersFolder.ucfirst($this->defcontroller)."\"."),$ctl_folder,true,__LINE__,__FILE__);
                        }
                    } else {

                    }
                } elseif (!file_exists($this->controllersFolder.$namel) && !file_exists($this->controllersFolder.$this->defcontroller.".php")) {
                    if (!$this->showDefaultView)
                        $this->raiseError("e404mnf",array("Error1_Type"=> "Default controller not found", "Error1_Msg" => "Controller \"".ucfirst($this->defcontroller). "\" not found in \"".$this->controllersFolder."\". ", "Error2_Type" => "Method in default controller not found", "Error2_Msg"=>"Method \"".ucfirst("index"). "\" not found in \"".$this->controllersFolder.ucfirst($this->defcontroller)."\"."),$ctl_folder,true,__LINE__,__FILE__);
                    else
                        include_once($this->ee->libGetResPath("mvc-ee","full")."default_view.php");
                }

                $this->output = ob_get_contents();
                ob_end_clean();
            }
            return $this->output;
        }
        private $ctl_folder; /// System Variable for the controllers folder.
        /// This function will raise an error to the user, if is defined by the developer, it will call the error controller, if not it will raise a default exengine 7 errorExit.
        final private function raiseError($error,$data,$controllersfolder=null,$noexit=false,$linenumber=__LINE__,$file=__FILE__) {
            if ($controllersfolder == null )
                $controllersfolder = $this->controllersFolder;
            if ($this->errorHandler) {
                if (file_exists($controllersfolder."/".$this->errorHandler.".php")) {
                    include_once($controllersfolder."/".$this->errorHandler.".php");
                    $name = ucfirst($this->errorHandler);
                    $ctrl = new $name($this->ee,$this);
                    if (method_exists($name,$error)) {
                        $allData = array(
                            "data" => $data,
                            "linenumber" => $linenumber,
                            "file" => $file);
                        call_user_func_array(array($ctrl, $error), $allData);
                    } else {
                        if ($this->ee->cArray["debug"])
                            $this->ee->errorExit("MVC-ExEngine: Error ".$error,print_r($data,true)."
					<br/>
					"."Line Number: ".$linenumber."
					<br/>
					"."File: ".$file."<br/><br/>
					<strong>Note: Error handler is set & found but no $error function is set</strong>.",null,$noexit);
                        else {
                            $this->ee->errorExit("Application Error #".$error,"Powered by MVC-ExEngine",null,$noexit);
                        }
                    }
                }
            } else {
                $this->AlwaysSilent=true; // disable the layout loading
                if ($this->ee->cArray["debug"]) {



                    $this->ee->errorExit("MVC-ExEngine: Error ".$error,print_r($data,true)."
					<br/>
					"."Line Number: ".$linenumber."
					<br/>
					"."File: ".$file
                        ,null,$noexit);
                }
                else {
                    $this->ee->errorExit("Application Error #".$error,"Powered by MVC-ExEngine",null,$noexit);
                }
            }
        }

        /// This function will parse the URL.
        final private function parseURL() {
            $ru = $_SERVER['REQUEST_URI'];
            $sn = $_SERVER['SCRIPT_NAME'];
            //print $ru ."<br/>";
            if (!$this->rewriteRulesEnabled) {
                if (!$this->ee->strContains($ru,$this->indexname)) {
                    $ru = $ru.$this->indexname;
                }
            } else {
                if (strlen($this->rewriteBaseFolder) > 0) {
                    $ru = str_replace($this->rewriteBaseFolder,'', $ru);
                }
            }
            //print $ru . "<br/>";
            //print $sn . "<br/>";
            $data = str_replace($sn,"",$ru);
            //print $data . "<br/>";
            if (isset($data[strlen($data)-1]) && $data[strlen($data)-1] == "/") {
                $data = substr($data, 0, -1);
            }
            $x = explode("/",$data);
            for ($i=0 ; $i<count($x) ; $i++) {
                $x[$i] = urldecode($x[$i]);
            }
            $actualInputQuery = $data;
            $urlParsedData = array_slice($x,1);
            if (isset($urlParsedData[count($urlParsedData)-1]) && strlen($urlParsedData[count($urlParsedData)-1]) == 0) {
                unset ($urlParsedData[count($urlParsedData)-1]);
            }
            $this->actualInputQuery = $actualInputQuery;

            //print $urlParsedData[count($urlParsedData)-1] . '<br/>';

            if (isset($urlParsedData[count($urlParsedData)-1]) && strpos( $urlParsedData[count($urlParsedData)-1], '?') !== false)
                $urlParsedData[count($urlParsedData)-1] = substr($urlParsedData[count($urlParsedData)-1], 0, strpos( $urlParsedData[count($urlParsedData)-1], '?'));

            //print $urlParsedData[count($urlParsedData)-1];

            $this->urlParsedData = $urlParsedData;
            $this->unModUrlParsedData = $urlParsedData;
            $this->debug("eemvcil.php:". __LINE__ . ": Parsed Data: " . print_r($this->urlParsedData,true));
        }

        /// This function sets the static folder path.
        final function setStaticFolder() {
            $str = "//" . $_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'].$this->staticFolder;
            $str = str_replace($this->indexname,"",$str);
            $this->staticFolderHTTP = $str;
            if ($this->rewriteRulesEnabled) {
                if (substr($_SERVER['HTTP_HOST'], -1) == "/")
                    $srv = substr($_SERVER['HTTP_HOST'].(strlen($this->rewriteBaseFolder)>0 ? '/'.$this->rewriteBaseFolder: ''), 0, -1);
                else
                    $srv = $_SERVER['HTTP_HOST'].(strlen($this->rewriteBaseFolder)>0 ? '/'.$this->rewriteBaseFolder: '');

                $str = "//" . $srv;
            }
            else {
                $str = "//" . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];
            }
            $this->controllersFolderHTTP = $str;
        }

        /// Shortcut to the ExEngine Debugger (Session or remote) for the index class.
        final function debug($message) {
            if ($this->SessionEnabled)
                $this->ma->d($message);
        }

        /// Shortcut to the ExEngine Debugger for the actual controller.
        /*final function debugController($message) {
             $this->ee->debugThis("eemvc-".$this->controllername,$message);
            }*/
    }
}
?>