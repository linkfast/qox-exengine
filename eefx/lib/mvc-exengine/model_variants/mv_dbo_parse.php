<?php

namespace ExEngine\MVC\DBO;

class Parse extends \ExEngine\MVC\Model {
    const VERSION = "0.0.0.1";

    var $objectId;
    var $createdAt;
    var $updatedAt;
    var $ACL;

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
    private function checkParse() {
        return class_exists("Parse\\ParseClient");
    }
    private function createParseObject() {
        return \Parse\ParseObject::create($this->TABLEID);
    }
    private function createParseQuery() {
        return new \Parse\ParseQuery($this->TABLEID);
    }
    private function setParseVars() {

    }
    private function getProperties($DeleteNulls=false,$DeleteDefaults=false) {
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
        return $vars;
    }

    public function __toArray() {
        return $this->getProperties();
    }

    public function __toString() {
        return print_r($this->getProperties(),true);
    }

    function load_all($WhereArray=null,$SafeMode=true,$SortArray=null) {
        if (!isset($this->TABLEID)) $this->TABLEID = $this->ee->classGetRealName($this);
        $pQuery = $this->createParseQuery();
        if (is_array($WhereArray)) {
            foreach ($WhereArray as $name => $value) {
                if (isset($value)) {
                    $pQuery->equalTo($name, $value);
                }
            }
        }
        $results = $pQuery->find();
        if (is_a($results[0], 'Parse\\ParseObject')) {
            $ClassName = get_class($this);
            $R = [];
            $data = $this->getProperties();
            foreach($results as $retObj) {
                $O = new $ClassName();
                foreach ($data as $name => $value) {
                    $O->$name = $retObj->get($name);
                    $O->createdAt = $retObj->getCreatedAt();
                    $O->updatedAt = $retObj->getUpdatedAt();
                    $O->objectId = $retObj->getObjectId();
                }
                $R[] = $O;
            }
            return $R;
        } else {
            return false;
        }
    }

    function load_values($SafeMode=true,$DeleteDefaults=false) {
        if (!isset($this->TABLEID)) $this->TABLEID = $this->ee->classGetRealName($this);
        $data = $this->getProperties(true, $DeleteDefaults);
        if (array_key_exists('objectId',$data)) unset($data['objectId']);
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
        foreach ($data as $name => $value) {
           if (isset($value)) {
               $pQuery->equalTo($name, $value);
           }
        }
        $results = $pQuery->find();
        if (is_a($results[0], 'Parse\\ParseObject')) {
            $retObj = $results[0];
            foreach ($data as $name => $value) {
                $this->$name = $retObj->get($name);
            }
            $this->createdAt = $retObj->getCreatedAt();
            $this->updatedAt = $retObj->getUpdatedAt();
            $this->objectId = $retObj->getObjectId();
            return true;
        } else {
            return false;
        }
    }
    function load() {
        if (!isset($this->TABLEID)) $this->TABLEID = $this->ee->classGetRealName($this);
        $data = $this->getProperties();
        if (!isset($data["objectId"])) {
            $this->ee->errorExit('MVC-ExEngine',
                'objectId is not defined and is required to retrieve object data from Parse.');
        }
        $pQuery = $this->createParseQuery();
        try {
            $retObj = $pQuery->get($data['objectId']);
            if (array_key_exists('objectId',$data)) unset($data['objectId']);
            if (array_key_exists('createdAt',$data)) unset($data['createdAt']);
            if (array_key_exists('updatedAt',$data)) unset($data['updatedAt']);
            if (array_key_exists('ACL',$data)) unset($data['ACL']);
            foreach ($data as $name => $value) {
                $this->$name = $retObj->get($name);
            }
            $this->createdAt = $retObj->getCreatedAt();
            $this->updatedAt = $retObj->getUpdatedAt();
            $this->objectId = $retObj->getObjectId();
            return $retObj;
        } catch (ParseException $ex) {
            $this->log()->e($ex->getMessage());
            return false;
        }
    }
    function delete() {
        if (!isset($this->TABLEID)) $this->TABLEID = $this->ee->classGetRealName($this);
        $data = $this->getProperties();
        if (!isset($data["objectId"])) {
            $this->ee->errorExit('MVC-ExEngine',
                'objectId is not defined and is required to retrieve object data from Parse.');
        }
        $pQuery = $this->createParseQuery();
        try {
            $pObj = $pQuery->get($data['objectId']);
            $pObj->destroy();
        } catch (ParseException $ex) {
            $this->log()->e($ex->getMessage());
            return false;
        }
    }
    function update($SafeMode=true) {
        if (!isset($this->TABLEID)) $this->TABLEID = $this->ee->classGetRealName($this);
        $data = $this->getProperties();
        if (!isset($data["objectId"])) {
            $this->ee->errorExit('MVC-ExEngine',
                'objectId is not defined and is required to retrieve object data from Parse.');
        }
        $pQuery = $this->createParseQuery();
        try {
            $pObj = $pQuery->get($data['objectId']);
            if (array_key_exists('objectId',$data)) unset($data['objectId']);
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
            $this->objectId = $pObj->getObjectId();
            $this->createdAt = $pObj->getCreatedAt();
            $this->updatedAt = $pObj->getUpdatedAt();
            return true;
        } catch (ParseException $ex) {
            $this->log()->e($ex->getMessage());
            return false;
        }
    }
    function insert($SafeMode=true) {
        if (!isset($this->TABLEID)) $this->TABLEID = $this->ee->classGetRealName($this);
        $pObj = $this->createParseObject();
        $data = $this->getProperties();
        if (array_key_exists('objectId',$data)) unset($data['objectId']);
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
            $this->objectId = $pObj->getObjectId();
            $this->createdAt = $pObj->getCreatedAt();
            $this->updatedAt = $pObj->getUpdatedAt();
            return true;
        } catch (ParseException $ex) {
            $this->log()->e($ex->getMessage());
            return false;
        }
    }
}

?>