<?php

Namespace ExEngine\MVC;

abstract class DefaultApplicationConfig {
    var $DefaultController = "start";
    var $RewriteRulesEnabled = false;
        var $RewriteBaseFolder = '';
    var $DevGuard = false;
        var $DevGuardKey = '';
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
	var $SafeStorageFolder = 'safe_storage';
	var $ConfigurationFolder = 'config';
	var $EnableLog = true;
		var $LogCleaningPolicyEnabled = true;
	var $ExEngineApplicationName = 'MyMVCApplication';

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