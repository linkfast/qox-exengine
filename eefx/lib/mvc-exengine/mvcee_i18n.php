<?php
namespace ExEngine\MVC {

    /**
     * Class I18n
     * @package ExEngine\MVC
     */
	class I18n {

		const VERSION = '0.0.0.2';

		private $StringsFile = null;
		private $Strings = [];
		private $DefaultStrings = [];

		private $ee;
		/* @var $index Index */
		private $index;
		private $controller = false;
		private $stringsFileLoaded = false;

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
                $this->StringsFile = $this->index->AppConfiguration->DefaultLocale;
			} else {
				$this->ee->errorExit('MVC-ExEngine Locales','Default Locale `' . $this->index->AppConfiguration->DefaultLocale . '` not found in locales folder.');
			}
			if ($this->controller instanceof Controller) {
				if ($this->controller->locale != 'default') {
					$this->changeLocale($this->controller->locale);
				}
			}
		}

        /**
         * Changes the current locale.
         *
         * @param string $StringsFile The name of the language file located in config/locales.
         */
        function changeLocale($StringsFile) {
			if ($StringsFile != $this->index->AppConfiguration->DefaultLocale and
				$this->StringsFile!=$StringsFile) {
				if (file_exists($this->index->AppConfiguration->ConfigurationFolder . '/locales/' . $StringsFile . '.yml')) {
					$this->Strings =  \ExEngine\Extended\Spyc\Spyc::YAMLLoad($this->index->AppConfiguration->ConfigurationFolder . '/locales/' . $StringsFile . '.yml');
					$this->stringsFileLoaded=true;
					$this->StringsFile = $StringsFile;
				} else {
					$this->ee->errorExit('MVC-ExEngine Locales','Locale `' . $StringsFile . '` not found in locales folder.');
				}
			}
		}

        /**
         * Gets the currently loaded locale file name (without path and extension).
         *
         * @return string The current locale file name.
         */
		function getCurrentLocale() {
            return $this->StringsFile;
        }

        /**
         * Gets the current language string from the language file, if not found there,
         * will search in the default language file, otherwise, will return the input text.
         *
         * This will use the "sprintf" function to format the string.
         *
         * @param string $LocaleString The name of the localized string.
         * @param mixed ...$FormatVariables Optional printf variables.
         * @return string Processed result.
         */
		function t($LocaleString) {
            $args = func_get_args();
            if (isset($this->Strings[$LocaleString])) {
                $LS = $this->Strings[$LocaleString];
            } else {
                if (isset($this->DefaultStrings[$LocaleString])) {
                    $LS = $this->DefaultStrings[$LocaleString];
                } else {
                    $LS = $LocaleString;
                }
            }
            if (count($args) < 2) {
                return $LS;
            }
            array_shift($args);
            array_unshift($args, $LS);
            $result = call_user_func_array('sprintf', $args);
            return $result;
		}
	}
}