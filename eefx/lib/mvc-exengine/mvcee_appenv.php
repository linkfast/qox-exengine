<?php
/**
@file mvcee_methods.php
@author Giancarlo A. Chiappe Aguilar <gchiappe@qox-corp.com> <gchiappe@outlook.com.pe>

@section LICENSE

See COPYING.txt

@section DESCRIPTION

ExEngine / Libs / MVC-ExEngine / Methods Class (Resources)

ExEngine MVC Implementation Library
 */

namespace ExEngine\MVC;

/* Accessible from all controllers, models and views in the object $this->r */
use ExEngine\Core;

class ApplicationEnvironment {

    const VERSION = "0.0.1.8";

    /* @var $cparent Controller */
    var $cparent;
    var $jQueryObject;
    /* @var $redirectTo Redirect */
    var $redirectTo;

    /* @var $session Session */
    var $session;

    final function staticFolderHTTP() {
        return $this->cparent->index->staticFolderHTTP . $this->tra;
    }

    final function staticFolder() {
        return $this->cparent->index->staticFolder . $this->tra ;
    }

    /**
     * Gives the Controllers folder path, made for redirects and link creation.
     * @return string
     */
    final function controllerFolderHTTP() {
        return $this->cparent->index->controllersFolderHTTP.$this->tra;
    }

    final function homePathHTTP() {
        $x[0] = null;
        if (!$this->cparent->index->rewriteRulesEnabled) {
            $x = $_SERVER['REQUEST_URI'];
            $x = explode($this->cparent->index->indexname,$x);
        }
        return "//" . $_SERVER['HTTP_HOST']. $x[0];
    }

    /**
     * Get database configuration Array (may be compatible with EEDBM if db type is a SQL-based)
     * @param string $databaseFile
     * @return array|bool
     */
    final function getDatabaseConfigurationFromFile($databaseFile='default') {
        $CfFile =  $this->cparent->index->AppConfiguration->ConfigurationFolder . '/database/';
        if ($databaseFile=='default') {
            $CfFile .= $this->cparent->index->AppConfiguration->DefaultDatabase . '.yml';
        } else {
            $CfFile .= $databaseFile . '.yml';
        }
        if (file_exists($CfFile)) {
            $this->ee->eeLoad('eespyc');
            $YW = new \eespyc();
            $YW->load();
            return \ExEngine\Extended\Spyc\Spyc::YAMLLoad($CfFile);
        } else {
            $this->ee->errorExit('MVC-ExEngine','Database configuration file not found ("'.$CfFile.'").');
            return false;
        }
    }

    final function currentControllerHTTP() {
        return $this->cparent->index->sameControllerFolderHTTP.$this->cparent->index->controllername . $this->tra;
    }

    final function currentControllerFolderHTTP() {
        return $this->cparent->index->sameControllerFolderHTTP . $this->tra;
    }

    final function currentControllerWithCurrentMethodHTTP() {
        return $this->cparent->index->sameControllerFolderHTTP.$this->cparent->index->controllername."/".$this->cparent->functionName. $this->tra;
    }

    final function currentControllerWithCurrentMethodOrImplicitIndexHTTP() {
        if ($this->cparent->functionName == "index")
            return $this->cparent->index->sameControllerFolderHTTP.$this->cparent->index->controllername . $this->tra;
        else
            return $this->currentControllerWithCurrentMethodHTTP();
    }

    final function viewSimulatorHTTP($ViewName="") {
        return $this->cparent->index->controllersFolderHTTP.$this->tra."?EEMVC_SPECIAL=VIEWSIMULATOR&VIEW=".$ViewName;
    }

    private $ee;
    final function __construct(&$parent) {
        $this->cparent = &$parent;
        $this->jQueryObject = &$this->cparent->index->jQueryObject;
        $this->tra = null;
        $this->ee = &ee_gi();
        if ($this->cparent->index->trailingSlashLegacy) {
            $this->tra = "/";
        }
        $this->redirectTo = new Redirect($this);
        $this->session = new Session($this);
    }

    /**
     * Deprecated. Use app->session->ELEMENT
     *
     * @param $element
     * @return bool|null
     */
    final function getSession($element) {
        if ($this->cparent->index->isSessionEnabled())
            if (array_key_exists($element, $_SESSION)) {
                return @$_SESSION[$element];
            } else {
                return false;
            }
        else {
            $this->ee->errorExit("MVC-ExEngine","Cannot get a session variable, session support is not enabled.");
            return null;
        }
    }

    /**
     * Deprecated. Use app->session->ELEMENT = VALUE
     *
     * @param $element
     * @param $value
     * @return null
     */
    final function setSession($element,$value) {
        if ($this->cparent->index->isSessionEnabled())
            $_SESSION[$element] = $value;
        else {
            $this->ee->errorExit("MVC-ExEngine","Cannot set a session variable, session support is not enabled.");
            return null;
        }
    }

    /**
     * Deprecated. Use app->session->clear()
     */
    final function clearSession() {
        if ($this->cparent->index->dgEnabled) {
            $dgSession = $_SESSION["DG_SA"];
        }
        session_unset();
        if ($this->cparent->index->dgEnabled) {
            $_SESSION["DG_SA"] = $dgSession;
        }
    }

    /**
     * Deprecated. Use app->session->ELEMENT = null
     *
     * @param $element
     */
    final function remSession($element) {
        unset($_SESSION[$element]);
    }

    /**
     * This functions provides multiple interfaces to PHP's $_GET variable.
     *
     * One argument:
     *  $this->app->get('GET_VAR_NAME')
     * will return the contents of that variable.
     *
     * Multiple argument:
     *  $this->app->get('GET_VAR_1', 'GET_VAR_2')
     *      or
     *  $this->app->get('GET_VAR_1,GET_VAR_2')
     * will return an stdClass object with the variables names as object properties.
     *
     * @return \stdClass|mixed|null
     */
    final function get() {
        $numargs = func_num_args();
        $arg_list = func_get_args();

        $pd = $_GET;

        if ($numargs >= 2) {
            $return = new \stdClass();
            for ($i = 0; $i < $numargs; $i++) {
                $return->$arg_list[$i] = $pd[$arg_list[$i]];
            }
            return $return;
        }else {
            if ($this->ee->strContains($arg_list[0],",")) {
                $ex = explode(',',$arg_list[0]);
                $return = new \stdClass();
                for ($i = 0; $i < count($ex) ; $i++) {
                    $return->$ex[$i] = $pd[$ex[$i]];
                }
                return $return;
            } elseif ($numargs == 1) {
                return @$pd[$arg_list[0]];
            }else {
                $this->ee->errorExit('ExEngine MVC Methods', 'app->get() function must have at least one argument.');
            }
        }
    }

    /**
     * This functions provides multiple interfaces to PHP's $_POST variable.
     *
     * MVC-ExEngine implementation is compatible with json encoded body post.
     *
     * One argument:
     *  $this->app->post('POST_VAR_NAME')
     * will return the contents of that variable.
     *
     * Multiple argument:
     *  $this->app->post('POST_VAR_1', 'POST_VAR_2')
     *      or
     *  $this->app->post('POST_VAR_1,POST_VAR_2')
     * will return an stdClass object with the variables names as object properties.
     *
     * @return \stdClass|mixed|null
     */
    final function post() {
        $numargs = func_num_args();
        $arg_list = func_get_args();

        /* json-encoded body post */
        $data2 = @json_decode(file_get_contents('php://input'));
        if ($data2 instanceof \stdClass) {
            $pd = get_object_vars($data2);
        } else {
            $pd = $_POST;
        }

        if ($numargs >= 2) {
            $return = new \stdClass();
            for ($i = 0; $i < $numargs; $i++) {
                $return->$arg_list[$i] = $pd[$arg_list[$i]];
            }
            return $return;
        } elseif ($numargs == 1) {
            if ($this->ee->strContains($arg_list[0],",")) {
                $ex = explode(',',$arg_list[0]);
                $return = new \stdClass();
                for ($i = 0; $i < count($ex) ; $i++) {
                    $return->$ex[$i] = $pd[$ex[$i]];
                }
                return $return;
            } else {
                return @$pd[$arg_list[0]];
            }
        } else {
            $this->ee->errorExit('ExEngine MVC Methods', 'app->post() function must have at least one argument.');
            return null;
        }
    }

    /**
     * Copy POSTs params to an MVC-ExEngine model or DBO model.
     *
     * Example:
     *
     * class MyModel {
     *  var $name;
     *  var $age;
     * }
     *
     * # POST: name=Giancarlo&age=26
     *
     * $myObj = new MyModel();
     * $this->app->postCopyToModel($myObj, 'name,age');
     * echo $myObj->name . ', ' . $myObj->age;
     * # Prints: Giancarlo, 26
     *
     * @param Model $ModelObject The Model extended object.
     * @param string ...$PropertiesToCopy The names of the properties to copy. Postvars must have the same names. You can also define only one comma separated string.
     */
    final function postCopyToModel(&$ModelObject) {
        $numargs = func_num_args();
        $arg_list = func_get_args();
        /* added compatibility for AngularJS ajax */
        $data2 = @json_decode(file_get_contents('php://input'));
        if ($data2 instanceof \stdClass) {
            $pd = get_object_vars($data2);
        } else {
            $pd = $_POST;
        }

        if ($numargs >= 2) {
            if ($numargs >= 3) {
                $return = new \stdClass();
                for ($i = 1; $i < $numargs; $i++) {
                    $return[$arg_list[$i]] = $pd[$arg_list[$i]];
                }
            } else {
                if ($this->ee->strContains($arg_list[1],",")) {
                    $ex = explode(',',$arg_list[1]);
                    for ($i = 0; $i < count($ex) ; $i++) {
                        $return[$ex[$i]] = $pd[$ex[$i]];
                    }
                } else {
                    $return[$arg_list[1]] = $pd[$arg_list[1]];
                }
            }
        } else {
            $return = $pd;
        }

        $obj_v = get_object_vars($ModelObject);

        foreach (array_keys($obj_v) as $obj_var) {
            if (isset($return[$obj_var]))
                $ModelObject->$obj_var = $return[$obj_var];
        }
    }

    /**
     * Gets the actual URL query string.
     * @param bool $EncodeToBase64
     * @return mixed|string
     */
    final function query($EncodeToBase64=false) {
        $b64 = $EncodeToBase64;
        $qs = $_SERVER['QUERY_STRING'];
        if (!$this->cparent->ee->strContains($qs, '?')) {
            $qs = preg_replace('/&/', '?', $qs, 1);
        }
        if ($b64)
            return base64_encode($qs);
        else
            return $qs;
    }

    /**
     * Gets StaticUploadFolder HTTP path.
     * @return string
     */
    final function staticUploadFolderHTTP() {
        return $this->cparent->index->staticFolderHTTP . '/' . $this->cparent->index->AppConfiguration->StaticUploadFolder .  $this->tra;
    }

    /**
     * Creates a FileUpload object for handling uploads.
     * @param $PostName
     * @return FileUpload
     */
    final function file($PostName) {
        //return @$_FILES[$pname];
        return new FileUpload($PostName);
    }

    /**
     * Gets raw $_POST variable from PHP.
     * @return mixed
     */
    final function allpost() {
        return @$_POST;
    }

    /**
     * Gets raw $_GET variable from PHP.
     * @return mixed
     */
    final function allget() {
        return @$_GET;
    }
}

/**
 * MVC-ExEngine Application Session Manager
 * This provides an object-like interface to PHP sessions.
 *
 * To clear a session variable just set to null.
 *
 * @package ExEngine\MVC
 */
class Session {

    private $__index;
    private $__app;
    private $__ee;
    private $__MaObj;

    final function __construct(ApplicationEnvironment $AppEnv)
    {
        $this->__app = $AppEnv;
        $this->__ee = &ee_gi();
        $this->__index = &eemvc_get_index_instance();
        $this->__MaObj = new \eema('SessionMgr', "MVC-ExEngine App Session Manager");
    }

    final function __get($name)
    {
        $this->__MaObj->d('Get: ' . $name);
        if (!$this->__index->isSessionEnabled()) return null;
        if (array_key_exists($name, $_SESSION)) {
            return $_SESSION[$name];
        }
        return null;
    }

    final function __set($name, $value)
    {
        $this->__MaObj->d('Set: ' . $name . ': "' . $value . '"');
        if (!$this->__index->isSessionEnabled()) return false;
        if ($value == null) {
            $_SESSION[$name] = null;
            unset($_SESSION[$name]);
        } else
            $_SESSION[$name] = $value;
        if ($_SESSION[$name] == $value) return true;
        return false;
    }

    /**
     * Clears the current session. Similar to session_stop but MVC ExEngine compatible.
     */
    final function clear() {
        $dgSession = null;
        // Prevent stopping devguard session.
        if ($this->__index->dgEnabled) {
            $dgSession = $_SESSION["DG_SA"];
        }
        session_unset();
        if ($this->__index->dgEnabled) {
            $_SESSION["DG_SA"] = $dgSession;
        }
    }

}

/**
 * MVC-ExEngine redirector.
 *
 * Class Redirect
 * @package ExEngine\MVC
 */
class Redirect {
    /* @var $index Index */
    private $index;
    /* @var $app ApplicationEnvironment */
    private $app;
    /* @var $ee Core */
    private $ee;
    function __construct(ApplicationEnvironment $AppEnv) {
        $this->ee = &ee_gi();
        $this->index = &eemvc_get_index_instance();
        $this->app = $AppEnv;
    }

    /**
     * Redirects to the application start controller.
     *
     * @param string $Arguments
     */
    function home($Arguments="") {
        if ($Arguments == "")
            $Arguments = '/' . $Arguments;
        $hP = $this->app->homePathHTTP();
        if (substr($hP, -1) != '/') $hP .= '/';
        header('Location: ' . $hP . $Arguments);
    }

    /**
     * Redirects to a current's controller method.
     * Note: Existence of the method is not checked.
     *
     * @param $Method
     * @param string $Arguments
     */
    function method($Method, $Arguments="") {
        if ($Arguments != "")
            $Arguments = '/' . $Arguments;
        $Method = strtolower($Method);
        header('Location: ' . $this->app->currentControllerHTTP() . '/' . $Method . $Arguments);
    }

    /**
     * Redirects to a controller method. Requires controller relative path.
     * Note: Existence of the controller and its method are not checked.
     *
     * @param $RelativeControllerName
     * @param $Method
     * @param string $Arguments
     */
    function controllerMethod($RelativeControllerName, $Method, $Arguments="") {
        if ($Arguments != "")
            $Arguments = '/' . $Arguments;
        $RelativeControllerName = strtolower($RelativeControllerName);
        $Method = strtolower($Method);
        header('Location: ' . $this->app->controllerFolderHTTP() . '/' . $RelativeControllerName . '/' . $Method . $Arguments);
    }

    /**
     * Redirects to a controller. Requires controller relative path.
     * Note: Existence of the controller is not checked.
     *
     * @param $RelativeControllerName
     * @param string $Arguments
     */
    function controller($RelativeControllerName, $Arguments="") {
        if ($Arguments != "")
            $Arguments = '/' . $Arguments;
        $RelativeControllerName = strtolower($RelativeControllerName);
        header('Location: ' . $this->app->controllerFolderHTTP() . '/' . $RelativeControllerName . $Arguments);
    }

    /**
     * Redirects to the current controller's index.
     *
     * @param string $Arguments
     */
    function index($Arguments="") {
        if ($Arguments != "")
            $Arguments = '/' . $Arguments;
        header('Location: ' . $this->app->currentControllerHTTP() . $Arguments);
    }

    /**
     * Redirects to a new Url.
     *
     * @param string $Url The Url.
     */
    function url($Url) {
        header('Location: ' . $Url);
    }

    /**
     * Redirects to the referer url.
     *
     * @param string $GetArguments
     */
    function referer($GetArguments="") {
        $ref = $_SERVER['HTTP_REFERER'];
        $r = $this->ee->httpGet($GetArguments, $ref);
        header('Location: ' . $r);
    }

    private function checkHttpsCF() {
        return isset($_SERVER['HTTPS']) ||
            ($visitor = json_decode($_SERVER['HTTP_CF_VISITOR'])) &&
            $visitor->scheme == 'https';
    }

    /**
     * Enforce HTTPS access.
     */
    function https() {
        if (!$this->checkHttpsCF()) {
            $redirect = 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
            header('HTTP/1.1 301 Moved Permanently');
            header('Location: ' . $redirect);
        }
    }
}

/**
 * MVC-ExEngine file upload wrapper.
 *
 * Class FileUpload
 * @package ExEngine\MVC
 */
class FileUpload {

    private $Name;
    private $Extension;
    private $Type;
    private $Size;
    private $Temp_Name;
    private $Error_Code;

    private $ee;
    private $mvcee;

    private $StaticUploadFile = '';
    private $StaticRelative = '';

    const VERSION = "0.0.1.0";

    function __construct($PostName)
    {
        $this->ee = &ee_gi();
        $this->mvcee = &eemvc_get_index_instance();

        if (isset($_FILES[$PostName]) and is_array($_FILES[$PostName])) {
            $F = $_FILES[$PostName];
            $this->Name = $F['name'];
            $this->Type = $F['type'];
            $this->Size = $F['size'];
            $this->Temp_Name = $F['tmp_name'];
            $this->Error_Code = $F['error'];
            $this->Extension = pathinfo($this->Name, PATHINFO_EXTENSION);
        } else {
            $this->ee->errorExit('MVC-ExEngine File Uploads', 'Error getting uploaded file.');
        }
    }

    /**
     * Returns the current uploaded file extension.
     *
     * @return string
     */
    function getExtension() {
        return $this->Extension;
    }

    function getErrorCode() {
        return $this->Error_Code;
    }

    /**
     * Returns the current uploaded file size in bytes.
     *
     * @return integer
     */
    function getSize() {
        return $this->Size;
    }

    function getType() {
        return $this->Type;
    }

    /**
     * Returns the current uploaded file full temporary path.
     *
     * @return string
     */
    function getTempFileName() {
        return $this->Temp_Name;
    }

    /**
     * Renames the file. This takes action when you move the file to the final location.
     * The default name is the source name.
     *
     * @param $NewName
     */
    function rename($NewName) {
        $this->Name = $NewName;
    }

    /**
     * Gets the file full HTTP path after its moved successfully to the app's static folder.
     *
     * @return bool|string
     */
    function getFilePathAfterMoveToStaticUploadFolderHTTP() {
        if (file_exists($this->StaticUploadFile)) {
            return $this->mvcee->staticFolderHTTP . '/' . $this->mvcee->AppConfiguration->StaticUploadFolder . '/' . $this->StaticRelative;
        } else {
            return false;
        }
    }

    /**
     * Sets the app's static files location as the final destination of the uploaded file and moves it.
     * Note: The static files location of the app is public.
     *
     * @param string $SubFolder
     * @return bool
     */
    function moveToStaticUploadFolder($SubFolder='.') {
        if ($this->Error_Code==0) {
            $NLFolder = $this->mvcee->AppConfiguration->StaticFolder . '/' . $this->mvcee->AppConfiguration->StaticUploadFolder . '/' . $SubFolder;
            if (!is_dir($NLFolder))
                mkdir($NLFolder);
            $NewLocation =  $NLFolder . '/' . $this->Name;
            $U = move_uploaded_file($this->Temp_Name, $NewLocation);
            if ($U) {
                $this->StaticRelative = $SubFolder . '/' . $this->Name;
                $this->StaticUploadFile = $NewLocation;
            }
            return $U;
        } else {
            return false;
        }
    }

    /*
     * TODO
    function moveToEEStorage() {

    }
    */

    /**
     * Sets the app's safe storage location as the final destination of the uploaded file and moves it.
     *
     * @param string $SubFolder
     * @return bool
     */
    function moveToSafeStorage($SubFolder='.') {
        if ($this->Error_Code==0) {
            $NewLocation = $this->mvcee->AppConfiguration->SafeStorageFolder . '/' . $SubFolder . '/' . $this->Name;
            return move_uploaded_file($this->Temp_Name, $NewLocation);
        } else {
            return false;
        }
    }

    /**
     * Moves the uploaded file to its final destination.
     *
     * @param $Location
     * @return bool
     */
    function move($Location) {
        if ($this->Error_Code==0) {
            if (is_dir($Location)) {
                $NewLocation = $Location . '/' . $this->Name;
                return move_uploaded_file($this->Temp_Name, $NewLocation);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

}
?>