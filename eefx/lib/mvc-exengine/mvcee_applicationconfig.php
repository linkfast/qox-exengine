<?php

/**
 * Default Application Config
 * V. 0.0.1.7
 */

Namespace ExEngine\MVC;

abstract class DefaultApplicationConfig {
    var $DefaultController = "start";
    var $RewriteRulesEnabled = false;
    var $RewriteBaseFolder = '';
    var $DevGuard = false;
    var $DevGuardKey = '';
    var $DevGuardExceptions = [];
    var $ComposerAutoload = false;
    var $InitLogSave = true;
    var $SessionCfg = null;
    var $DefaultLocale = 'en';
    var $DefaultLayout = 'default';
    var $DefaultDatabase = 'default';
    var $AppFolder = 'app';
    var $ControllersFolder = 'controllers';
    var $ModelsFolder = 'models';
    var $ViewsFolder = 'views';
    var $StaticFolder = 'static';
    var $StaticUploadFolder = 'usr_upload';
    var $SafeStorageFolder = 'safe_storage';
    var $ConfigurationFolder = 'config';
    var $EnableLog = true;
    var $LogCleaningPolicyEnabled = true;
    var $ExEngineApplicationName = 'MyMVCApplication';
    var $Tracer=false;
    var $ErrorHandler = null;
    var $EEjQueryEnabled = false;
    var $EEjQueryVersion = null;
    var $EEjQueryUITheme = 'base';
    var $EEjQueryUIVersion = null;
    var $UsingFromCLI = false;

    /* @var $ee \ExEngine\Core */
    protected $ee;
    function __construct() {
        $this->ee = &ee_gi();
    }

    /**
     * Override this function to the set the when the log files will be reset.
     * @return bool
     */
    function LogCleaningPolicy() {
        return date('D', time()) === 'Mon';
    }

    function ApplicationInit() { }

}

?>