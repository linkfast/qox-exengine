<?php
/**
@file mvcee_methods.php
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

ExEngine / Libs / MVC-ExEngine / Methods Class (Resources)

ExEngine MVC Implementation Library

 */

namespace ExEngine\MVC;

/* Accessible from all controllers, models and views in the object $this->r */
class Methods {

    const VERSION = "0.0.1.8";

    /* @var $cparent Controller */
    var $cparent;
    var $jQueryObject;
    var $goTo;

    final function sf() {
        return $this->cparent->index->staticFolderHTTP . $this->tra;
    }

    final function fsf() {
        return $this->cparent->index->staticFolder . $this->tra ;
    }

    /**
     * Gives the Controllers folder path, made for redirectios and link creation.
     * @return string
     */
    final function c() {
        return $this->cparent->index->controllersFolderHTTP.$this->tra;
    }

    /* TODO: REMOVE
    final function scpath() {
        $urldata = $this->cparent->index->unModUrlParsedData;
        $size = count($urldata);
        $str_make = null;
        $urldata = array_slice($urldata,0,($size-3));
        $size = count($urldata);
        for ($i = 0; $i
                            < $size ; $i++) {
            $str_make .= $urldata[$i].'/';
        }
        return $str_make;
    }
    */

    final function home() {
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
    final function getDbConf($databaseFile='default') {
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

    final function sc() {
        return $this->cparent->index->sameControllerFolderHTTP.$this->cparent->index->controllername . $this->tra;
    }

    final function scfolder() {
        return $this->cparent->index->sameControllerFolderHTTP . $this->tra;
    }

    final function scf() {
        return $this->cparent->index->sameControllerFolderHTTP.$this->cparent->index->controllername."/".$this->cparent->functionName. $this->tra;
    }

    final function scfi() {
        if ($this->cparent->functionName == "index")
            return $this->cparent->index->sameControllerFolderHTTP.$this->cparent->index->controllername . $this->tra;
        else
            return $this->scf();
    }

    final function vs() {
        return $this->cparent->index->controllersFolderHTTP.$this->tra."?EEMVC_SPECIAL=VIEWSIMULATOR&VIEW=";
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
        $this->goTo = new Redirect($this);
    }

    final function getAllSession() {
        if ($this->cparent->index->isSessionEnabled())
            return @$_SESSION;
        else {
            $this->ee->errorExit("MVC-ExEngine","Cannot get a session variable, session support is not enabled.");
            return null;
        }
    }

    final function getSession($element) {
        if ($this->cparent->index->isSessionEnabled())
            return @$_SESSION[$element];
        else {
            $this->ee->errorExit("MVC-ExEngine","Cannot get a session variable, session support is not enabled.");
            return null;
        }
    }

    final function setSession($element,$value) {
        if ($this->cparent->index->isSessionEnabled())
            $_SESSION[$element] = $value;
        else {
            $this->ee->errorExit("MVC-ExEngine","Cannot set a session variable, session support is not enabled.");
            return null;
        }
    }

    final function clearSession() {
        if ($this->cparent->index->dgEnabled) {
            $dgSession = $_SESSION["DG_SA"];
        }
        session_unset();
        if ($this->cparent->index->dgEnabled) {
            $_SESSION["DG_SA"] = $dgSession;
        }
    }

    final function remSession($element) {
        unset($_SESSION[$element]);
    }

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
                $this->ee->errorExit('ExEngine MVC Methods', 'r->get() function must have at least one argument.');
            }
        }
    }

    /**
     * @return string|\stdClass
     */
    final function put() {
        $numargs = func_num_args();
        $arg_list = func_get_args();

        parse_str(file_get_contents("php://input"),$pd);

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
                    if ($pd[$ex[$i]]!=null)
                        $return->$ex[$i] = $pd[$ex[$i]];
                }
                return $return;
            } else {
                return @$pd[$arg_list[0]];
            }
        } else {
            $this->ee->errorExit('ExEngine MVC Methods', 'r->put() function must have at least one argument.');
            return null;
        }
    }

    final function post() {
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
            $this->ee->errorExit('ExEngine MVC Methods', 'r->post() function must have at least one argument.');
            return null;
        }
    }

    /**
     * Copy POSTs params to an MVC-ExEngine model or DBO model.
     * @param $Object
     * @param string $PostName
     */
    final function postCopyToModel(&$Object) {
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

        $obj_v = get_object_vars($Object);

        foreach (array_keys($obj_v) as $obj_var) {
            if (isset($return[$obj_var]))
                $Object->$obj_var = $return[$obj_var];
        }
    }

    /**
     * Gets the actual query string.
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
    final function sfu() {
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

    final function allput() {
        parse_str(file_get_contents("php://input"),$pd);
        return $pd;
    }

    /**
     * Gets raw $_GET vaiable from PHP.
     * @return mixed
     */
    final function allget() {
        return @$_GET;
    }
}

class Redirect {
    private $index;
    /* @var $r Methods */
    private $r;
    function __construct($R) {
        $this->index = &eemvc_get_index_instance();
        $this->r = $R;
    }
    function home($Arguments=null) {
        if ($Arguments != null)
            $Arguments = '/' . $Arguments;
        header('Location: ' . $this->r->home() . $Arguments);
    }
    function func($function_name, $Arguments=null) {
        if ($Arguments != null)
            $Arguments = '/' . $Arguments;
        header('Location: ' . $this->r->sc() . '/' . $function_name . $Arguments);
    }
    function cont($controller, $Arguments=null) {
        if ($Arguments != null)
            $Arguments = '/' . $Arguments;
        header('Location: ' . $this->r->c() . '/' . $controller . $Arguments);
    }
    function index($Arguments=null) {
        if ($Arguments != null)
            $Arguments = '/' . $Arguments;
        header('Location: ' . $this->r->sc() . $Arguments);
    }
}

class FileUpload {

    private $Name;
    private $Extension;
    private $Type;
    private $Size;
    private $Temp_Name;
    private $Error_Code;
    private $FileData;

    private $ee;
    private $mvcee;

    private $StaticUploadFile = '';
    private $StaticRelative = '';

    const VERSION = "0.0.1.0";

    function __construct($PostNameOrB64Data,$b64isDataURI=false)
    {
        $this->ee = &ee_gi();
        $this->mvcee = &eemvc_get_index_instance();

        if (isset($_FILES[$PostNameOrB64Data]) and is_array($_FILES[$PostNameOrB64Data])) {
            $F = $_FILES[$PostNameOrB64Data];
            $this->Name = $F['name'];
            $this->Type = $F['type'];
            $this->Size = $F['size'];
            $this->Temp_Name = $F['tmp_name'];
            $this->Error_Code = $F['error'];
            $this->Extension = pathinfo($this->Name, PATHINFO_EXTENSION);
        } else {
            $uri = $PostNameOrB64Data;
            if ($b64isDataURI)
                $uri = substr($uri,strpos($uri,",")+1);
            $encodedData = str_replace(' ','+',$uri);
            $this->FileData = base64_decode($encodedData);
            $this->Error_Code = 0;
        }
    }

    function getName() {
        return $this->Name;
    }

    function getExtension() {
        return $this->Extension;
    }

    function getErrorCode() {
        return $this->Error_Code;
    }

    function getSize() {
        return $this->Size;
    }

    function getType() {
        return $this->Type;
    }

    function getTempName() {
        return $this->Temp_Name;
    }

    function rename($NewName) {
        $this->Name = $NewName;
    }

    static function getStaticFolderHTTP() {
        $index = &eemvc_get_index_instance();
        return $index->staticFolderHTTP . '/' . $index->AppConfiguration->StaticUploadFolder . '/';
    }

    static function getStaticFolder() {
        $index = &eemvc_get_index_instance();
        return $index->AppConfiguration->StaticFolder . '/'. $index->AppConfiguration->StaticUploadFolder . '/';
    }

    function getStaticHTTPPath() {
        if (file_exists($this->StaticUploadFile)) {
            return $this->mvcee->staticFolderHTTP . '/' . $this->mvcee->AppConfiguration->StaticUploadFolder . '/' . $this->StaticRelative;
        } else {
            return false;
        }
    }

    function moveToStatic($SubFolder='.') {
        if ($this->Error_Code==0) {
            $NLFolder = $this->mvcee->AppConfiguration->StaticFolder . '/' . $this->mvcee->AppConfiguration->StaticUploadFolder . '/' . $SubFolder;
            if (!is_dir($NLFolder))
                mkdir($NLFolder);

            if (isset($this->Name)) {
                $NewLocation = $NLFolder . '/' . $this->Name;
            } else {
                $this->ee->errorExit('MVC-ExEngine File Management','Please set a name before moving file.');
                return null;
            }

            if (!isset($this->FileData))
                $U = move_uploaded_file($this->Temp_Name, $NewLocation);
            else
                $U = file_put_contents($NewLocation,$this->FileData);

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

    function moveToSafeStorage($SubFolder='.') {
        if ($this->Error_Code==0) {
            $NewLocation = $this->mvcee->AppConfiguration->SafeStorageFolder . '/' . $SubFolder . '/' . $this->Name;
            return move_uploaded_file($this->Temp_Name, $NewLocation);
        } else {
            return false;
        }
    }

    function move($Location) {
        if ($this->Error_Code==0) {
            if (is_dir($Location)) {
                if (isset($this->Name)) {
                    $NewLocation = $Location . '/' . $this->Name;
                } else {
                    $this->ee->errorExit('MVC-ExEngine File Management','Please set a name before moving file.');
                    return null;
                }

                if (!isset($this->FileData))
                    $U = move_uploaded_file($this->Temp_Name, $NewLocation);
                else
                    $U = file_put_contents($NewLocation,$this->FileData);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

}
?>