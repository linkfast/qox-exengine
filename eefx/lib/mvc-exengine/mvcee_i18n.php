<?php
namespace ExEngine\MVC {

	class I18n {

		const VERSION = '0.0.0.1';

		private $StringsFile=null;
		private $Strings = [];
		private $DefaultStrings = [];

		private $ee;
		/* @var $index Index */
		private $index;
		private $controller = false;
		private $stringsFileLoaded=false;

		function __construct(){
			$this->ee = &ee_gi();
			$this->index = &eemvc_get_index_instance();
			if (eemvc_get_instance() instanceof Controller) {
				$this->controller = &eemvc_get_instance();
			}
			$this->ee->eeLoad('eespyc');
			$SW = new \eespyc();
			$SW->load();
			if (file_exists($this->index->AppConfiguration->ConfigurationFolder . '/locales/' . $this->index->AppConfiguration->DefaultLocale . '.yml')) {
				$this->DefaultStrings =  \ExEngine\Extended\Spyc\Spyc::YAMLLoad($this->index->AppConfiguration->ConfigurationFolder . '/locales/' . $this->index->AppConfiguration->DefaultLocale . '.yml');
				$this->defStringsFileLoaded=true;
			} else {
				$this->ee->errorExit('MVC-ExEngine Locales','Default Locale `' . $this->index->AppConfiguration->DefaultLocale . '` not found in locales folder.');
			}
			if ($this->controller instanceof Controller) {
				if ($this->controller->locale != 'default') {
					$this->changeLocale($this->controller->locale);
				}
			}
		}
		function changeLocale($StringsFile) {
			if ($StringsFile != $this->index->AppConfiguration->DefaultLocale and
				$this->StringsFile!=$StringsFile) {
				if (file_exists($this->index->AppConfiguration->ConfigurationFolder . '/locales/' . $StringsFile . '.yml')) {
					$this->Strings =  \ExEngine\Extended\Spyc\Spyc::YAMLLoad($this->index->AppConfiguration->ConfigurationFolder . '/locales/' . $StringsFile . '.yml');
					$this->stringsFileLoaded=true;
				} else {
					$this->ee->errorExit('MVC-ExEngine Locales','Locale `' . $StringsFile . '` not found in locales folder.');
				}
			}
		}
		function t($LocaleString) {
			if (isset($this->Strings[$LocaleString])) {
				return $this->Strings[$LocaleString];
			} else {
				if (isset($this->DefaultStrings[$LocaleString])) {
					return $this->DefaultStrings[$LocaleString];
				} else {
					return $LocaleString;
				}
			}
		}
	}
}
?>