<?php

namespace ExEngine\MVC\DBO;

class Parse extends \ExEngine\MVC\Model {
    const VERSION = "0.0.0.2";

    var $id;
    var $createdAt;
    var $updatedAt;
    var $ACL;

    protected $APP_ID;
    protected $REST_KEY;
    protected $MASTER_KEY;

    protected $LAST_ERROR;

    function getLastError() {
        return $this->LAST_ERROR;
    }

    function __construct() {
        parent::__construct();
        if (!$this->checkParse())
            $this->ee->errorExit("MVC-ExEngine",
                "Parse PHP SDK is not installed (or loaded), please check your includes.","ExEngine_MVC_Implementation_Library");
        if (!isset($this->DBC))
            $dbCfg = $this->r->getDbConf();
        else
            $dbCfg = $this->r->getDbConf($this->DBC);
        if ($dbCfg) {
            if ($dbCfg['type']!='parse'){
                $this->ee->errorExit('MVC-ExEngine','Specified database configuration is not compatible with Parse DBO, please set a compatible database configuration setting the $DBC reserved variable in the model.');
            } else {
                $this->APP_ID = $dbCfg['appid'];
                $this->REST_KEY = $dbCfg['restkey'];
                $this->MASTER_KEY = $dbCfg['masterkey'];
            }
        }
        \Parse\ParseClient::initialize($this->APP_ID, $this->REST_KEY, $this->MASTER_KEY);
    }
    function log() {
        $ma = new \eema("eemvcdbo-parse-".$this->ee->classGetRealName($this),'MVC-EE DBO (Parse) "'.$this->ee->classGetRealName($this).'".');
        return $ma;
    }
    protected function checkParse() {
        return class_exists("Parse\\ParseClient");
    }
    protected function createParseObject() {
        return \Parse\ParseObject::create($this->TABLEID);
    }
    protected function createParseQuery() {
        return new \Parse\ParseQuery($this->TABLEID);
    }
    protected function getProperties($DeleteNulls=false,$DeleteDefaults=false, $ObjectsToString=true) {
        $vars = get_object_vars($this);
        unset($vars["db"]);
        unset($vars["r"]);
        unset($vars["ee"]);
        unset($vars["APP_ID"]);
        unset($vars["REST_KEY"]);
        unset($vars["MASTER_KEY"]);
        unset($vars["TABLEID"]);
        unset($vars["DBC"]);
        if (isset ($this->EXCLUDEVARS) ) {
            unset($vars["EXCLUDEVARS"]);
            for ($c = 0; $c < count($this->EXCLUDEVARS); $c++) {
                unset($vars[$this->EXCLUDEVARS[$c]]);
            }
        }
        if ($DeleteNulls) {
            foreach (array_keys($vars) as $key) {
                if (!isset($vars[$key]))
                    unset($vars[$key]);
            }
        }
        if ($DeleteDefaults) {
            $cn = get_class($this);
            $Com = new $cn();
            $com_vars = get_object_vars($Com);
            unset($com_vars["db"]);
            unset($com_vars["r"]);
            unset($com_vars["ee"]);
            unset($com_vars["APP_ID"]);
            unset($com_vars["REST_KEY"]);
            unset($com_vars["MASTER_KEY"]);
            unset($com_vars["TABLEID"]);
            unset($com_vars["DBC"]);
            foreach (array_keys($vars) as $key) {
                if (isset($vars[$key])) {
                    if ($vars[$key] == $com_vars[$key]) {
                        unset($vars[$key]);
                    }
                }
            }
        }

        if ($ObjectsToString)
            foreach ($vars as $v_k => &$v_v) {
                if (is_a($v_v,'Parse\\ParseObject')) {
                    /* @var $v_v \Parse\ParseObject */
                    $v_v = $v_v->getObjectId();
                }
            }

        return $vars;
    }

    public function __toArray() {
        return $this->getProperties();
    }

    public function __toString() {
        return print_r($this->getProperties(),true);
    }

    protected function parse_var_recog($obj) {
        if (is_a($obj,'Parse\\ParseFile')) {
            /* @var $obj \Parse\ParseFile */
            $R = $obj->getURL();
        } elseif (is_a($obj,'Parse\\ParseGeoPoint')) {
            /* @var $obj \Parse\ParseGeoPoint */
            $R = ["Lat" => $obj->getLatitude(), "Long" => $obj->getLongitude()];
        } else
            $R = 'other';
        return $R;
    }

    function load_all($WhereArray=null,$SafeMode=true,$SortArray=null,$AllToArray=false,$preserveParseObj=false,$parseQueryAddons=null) {
        if (!isset($this->TABLEID)) $this->TABLEID = $this->ee->classGetRealName($this);
        $pQuery = $this->createParseQuery();
        if (is_array($WhereArray)) {
            foreach ($WhereArray as $name => $value) {
                if (isset($value)) {
                    $pQuery->equalTo($name, $value);
                }
            }
        }

        if (isset($parseQueryAddons)) {
            $parseQueryAddons($pQuery);
        }

        $results = $pQuery->find();
        if (is_a($results[0], 'Parse\\ParseObject')) {
            $ClassName = get_class($this);
            $R = [];
            $data = $this->getProperties();
            foreach($results as $retObj) {
                /* @var $O \ExEngine\MVC\DBO\Parse */
                $O = new $ClassName();
                foreach ($data as $name => $value) {
                    $obj = $retObj->get($name);
                    if ($preserveParseObj) {
                        $O->parseObject = $retObj;
                    }

                    $A = $this->parse_var_recog($obj);
                    if ($A == 'other') {
                        $O->$name = $retObj->get($name);
                    } else {
                        $O->$name = $A;
                    }
                }
                $O->createdAt = $retObj->getCreatedAt();
                $O->updatedAt = $retObj->getUpdatedAt();
                $O->id = $retObj->getObjectId();
                if ($AllToArray)
                    $R[] = $O->__toArray();
                else
                    $R[] = $O;
            }
            return $R;
        } else {
            return false;
        }
    }

    function load_values($SafeMode=true,$DeleteDefaults=false,$preserveParseObj=false) {
        if (!isset($this->TABLEID)) $this->TABLEID = $this->ee->classGetRealName($this);
        $data = $this->getProperties(false, $DeleteDefaults);
        if (array_key_exists('id',$data)) unset($data['id']);
        if (array_key_exists('createdAt',$data)) unset($data['createdAt']);
        if (array_key_exists('updatedAt',$data)) unset($data['updatedAt']);
        if (array_key_exists('ACL',$data)) unset($data['ACL']);
        $c=0;
        foreach ($data as $name => $value) {
            if (isset($value)) {
                $c++;
            }
        }
        if ($c==0) {
            $this->ee->errorExit('MVC-ExEngine',
                'You must set at least one field to retrieve an object data from Parse.');
        }
        $pQuery = $this->createParseQuery();
        try {
            foreach ($data as $name => $value) {
               if (isset($value)) {
                   $pQuery->equalTo($name, $value);
               }
            }
            $results = $pQuery->find();
            if (is_a($results[0], 'Parse\\ParseObject')) {
                /* @var $retObj \Parse\ParseObject */
                $retObj = $results[0];

                if ($preserveParseObj) {
                    $this->parseObject = $retObj;
                }
                foreach ($data as $name => $value) {
                    $obj = $retObj->get($name);
                    $A = $this->parse_var_recog($obj);
                    if ($A == 'other') {
                        $this->$name = $retObj->get($name);
                    } else {
                        $this->$name = $A;
                    }
                }

                $this->createdAt = $retObj->getCreatedAt();
                $this->updatedAt = $retObj->getUpdatedAt();
                $this->id = $retObj->getObjectId();
                return true;
            } else {
                return false;
            }
        } catch (\Parse\ParseException $ex) {
            $this->LAST_ERROR = $ex->getMessage();
            return false;
        }
    }
    function load($preserveParseObj=false) {
        if (!isset($this->TABLEID)) $this->TABLEID = $this->ee->classGetRealName($this);
        $data = $this->getProperties();
        if (!isset($data["id"])) {
            $this->ee->errorExit('MVC-ExEngine',
                'id is not defined and is required to retrieve object data from Parse.');
        }
        $pQuery = $this->createParseQuery();
        try {
            /* @var $retObj \Parse\ParseObject */
            $retObj = $pQuery->get($data['id']);
            if ($preserveParseObj) {
                $this->parseObject = $retObj;
            }
            if (array_key_exists('id',$data)) unset($data['id']);
            if (array_key_exists('createdAt',$data)) unset($data['createdAt']);
            if (array_key_exists('updatedAt',$data)) unset($data['updatedAt']);
            if (array_key_exists('ACL',$data)) unset($data['ACL']);
            foreach ($data as $name => $value) {
                $obj = $retObj->get($name);
                $A = $this->parse_var_recog($obj);
                if ($A == 'other') {
                    $this->$name = $retObj->get($name);
                } else {
                    $this->$name = $A;
                }
            }

            $this->createdAt = $retObj->getCreatedAt();
            $this->updatedAt = $retObj->getUpdatedAt();
            $this->id = $retObj->getObjectId();
            return $retObj;
        } catch (\Parse\ParseException $ex) {
            $this->LAST_ERROR = $ex->getMessage();
            $this->log()->e("Parse error: ".$this->LAST_ERROR,$this->getProperties());
            return false;
        }
    }
    function delete() {
        if (!isset($this->TABLEID)) $this->TABLEID = $this->ee->classGetRealName($this);
        $data = $this->getProperties();
        if (!isset($data["id"])) {
            $this->ee->errorExit('MVC-ExEngine',
                'id is not defined and is required to retrieve object data from Parse.');
        }
        $pQuery = $this->createParseQuery();
        try {
            $pObj = $pQuery->get($data['id']);
            $pObj->destroy();
        } catch (\Parse\ParseException $ex) {
            $this->log()->e($ex->getMessage());
            return false;
        }
    }
    function update($SafeMode=true) {
        if (!isset($this->TABLEID)) $this->TABLEID = $this->ee->classGetRealName($this);
        $data = $this->getProperties(false,false,false);
        if (!isset($data["id"])) {
            $this->ee->errorExit('MVC-ExEngine',
                'id is not defined and is required to retrieve object data from Parse.');
        }
        $pQuery = $this->createParseQuery();
        try {
            $pObj = $pQuery->get($data['id']);
            if (array_key_exists('id',$data)) unset($data['id']);
            if (array_key_exists('createdAt',$data)) unset($data['createdAt']);
            if (array_key_exists('updatedAt',$data)) unset($data['updatedAt']);
            if (array_key_exists('ACL',$data)) unset($data['ACL']);
            foreach ($data as $name => $value) {
                if (is_array($value)) {
                    $pObj->setArray($name, $value);
                } else {
                    $pObj->set($name, $value);
                }
            }
            $pObj->save();
            $this->id = $pObj->getObjectId();
            $this->createdAt = $pObj->getCreatedAt();
            $this->updatedAt = $pObj->getUpdatedAt();
            return true;
        } catch (\Parse\ParseException $ex) {
            $this->LAST_ERROR  = $ex->getMessage();
            $this->log()->e($this->LAST_ERROR);
            return false;
        }
    }
    function insert($SafeMode=true) {
        if (!isset($this->TABLEID)) $this->TABLEID = $this->ee->classGetRealName($this);
        $pObj = $this->createParseObject();
        $data = $this->getProperties(false,false,false);
        if (array_key_exists('id',$data)) unset($data['id']);
        if (array_key_exists('createdAt',$data)) unset($data['createdAt']);
        if (array_key_exists('updatedAt',$data)) unset($data['updatedAt']);
        if (array_key_exists('ACL',$data)) unset($data['ACL']);
        //print_r($data);
        foreach ($data as $name => $value) {
            if (is_array($value)) {
                $pObj->setArray($name, $value);
            } else {
                $pObj->set($name, $value);
            }
        }
        try {
            $pObj->save();
            $this->id = $pObj->getObjectId();
            $this->createdAt = $pObj->getCreatedAt();
            $this->updatedAt = $pObj->getUpdatedAt();
            return true;
        } catch (\Parse\ParseException $ex) {
            $this->LAST_ERROR = $ex->getMessage();
            $this->log()->e($this->LAST_ERROR);
            return false;
        }
    }
}

class ParseUser extends Parse {

    var $id;
    var $username;
    var $password;

    function loadById($preserveParseObj=false) {
        return parent::load($preserveParseObj);
    }

    function load($preserveParseObj=false) {
        $data = $this->getProperties();
        if (!isset($data["username"]) && !isset($data["password"])) {
            $this->ee->errorExit('MVC-ExEngine',
                'username and password are not defined and is required to retrieve User object data from Parse.');
        }
        try {
            $retObj = \Parse\ParseUser::logIn($data["username"], $data["password"]);
            if (array_key_exists('id',$data)) unset($data['id']);
            if (array_key_exists('createdAt',$data)) unset($data['createdAt']);
            if (array_key_exists('updatedAt',$data)) unset($data['updatedAt']);
            if (array_key_exists('ACL',$data)) unset($data['ACL']);
            //print_r($data);
            foreach ($data as $name => $value) {
                $this->$name = $retObj->get($name);
            }
            $this->createdAt = $retObj->getCreatedAt();
            $this->updatedAt = $retObj->getUpdatedAt();
            $this->id = $retObj->getObjectId();
            if ($preserveParseObj) {
                $this->parseObject = $retObj;
            }
            return $retObj;
        } catch (\Parse\ParseException $Error) {
            $this->log()->e($Error->getMessage());
            return false;
        }
    }
    protected function createParseObject() {
        return new \Parse\ParseUser();
    }
    function insert($SafeMode=true) {
        if (!isset($this->TABLEID)) $this->TABLEID = $this->ee->classGetRealName($this);
        $pObj = $this->createParseObject();
        $data = $this->getProperties();
        if (array_key_exists('id',$data)) unset($data['id']);
        if (array_key_exists('createdAt',$data)) unset($data['createdAt']);
        if (array_key_exists('updatedAt',$data)) unset($data['updatedAt']);
        if (array_key_exists('ACL',$data)) unset($data['ACL']);
        foreach ($data as $name => $value) {
            if (is_array($value)) {
                $pObj->setArray($name, $value);
            } else {
                $pObj->set($name, $value);
            }
        }
        try {
            $pObj->signUp();
            $this->id = $pObj->getObjectId();
            $this->createdAt = $pObj->getCreatedAt();
            $this->updatedAt = $pObj->getUpdatedAt();
            return true;
        } catch (\Parse\ParseException $ex) {

            $this->LAST_ERROR = $ex->getMessage();
            $this->log()->e($this->LAST_ERROR);
            return false;
        }
    }
}