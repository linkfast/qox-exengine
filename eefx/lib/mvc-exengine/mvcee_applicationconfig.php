<?php

/**
 * Default Application Config
 * V. 0.0.1.8
 */

Namespace ExEngine\MVC;

abstract class DefaultApplicationConfig {
    # New:
    var $ViewsSandboxing = 'auto';
    var $PopulateAllLocales = true;
    var $AutomaticLocalePopulate = false;

    # Older:
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
        $mvc_session = new Session();
        $mvc_session->Enabled = false; # Disabled by default, set $this->SessionCfg->Enabled = true; to enable.
        $mvc_session->Name = "MVC_EXENGINE_SESSION_ID";
        $mvc_session->Lifetime = 3600 * 24; # One day.
        $mvc_session->Path = "/";
        $this->SessionCfg = $mvc_session;
    }

    /**
     * Override this function to the set the when the log files will be reset.
     * @return bool
     */
    function LogCleaningPolicy() {
        return date('D', time()) === 'Mon';
    }

    /**
     * You must override this function to set the configuration values.
     * Note: Session support is disabled by default, set $this->SessionCfg->Enabled = true; to enable or
     * create a new \ExEngine\MVC\Session object for a custom session configuration.
     */
    function ApplicationInit() { }

}