<?php
namespace ExEngine\MVC {

	class I18n {

		const VERSION = '0.0.0.2';

		private $CurrentLocale='en';
        private $DefaultStringsFile = null;
		private $Strings = [];
		private $DefaultStrings = [];

		private $ee;
		/* @var $index Index */
		private $index;
		private $controller = false;
		private $stringsFileLoaded=false;


        /**
         * I18n constructor.
         * This constructor should only be called from the controller loader.
         * @param bool $auto
         */
		function __construct($auto=true){
			$this->ee = &ee_gi();
			$this->index = &eemvc_get_index_instance();
			if (eemvc_get_instance() instanceof Controller) {
				$this->controller = &eemvc_get_instance();
			}
			$this->ee->eeLoad('eespyc');
			$SW = new \eespyc();
			$SW->load();

			if ($auto) {
                $this->DefaultStrings =  \ExEngine\Extended\Spyc\Spyc::YAMLLoad($this->index->AppConfiguration->AppFolder . '/locales/' . $this->index->AppConfiguration->DefaultLocale . '.yml');
                $this->defStringsFileLoaded=true;
            }

            $this->DefaultStringsFile = $this->index->AppConfiguration->AppFolder . '/locales/' . $this->index->AppConfiguration->DefaultLocale . '.yml';

			if (file_exists($this->DefaultStringsFile)) {
				$this->DefaultStrings =  \ExEngine\Extended\Spyc\Spyc::YAMLLoad($this->DefaultStringsFile);
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

        /**
         * changeLocale
         * This method will load a new locale file, if not new or is default, will not be loaded. No output will be
         * provided, except the case that the new locale file is not found.
         * @param $NewLocale
         */
		function changeLocale($NewLocale) {
			if ($NewLocale != $this->index->AppConfiguration->DefaultLocale and
				$this->CurrentLocale!=$NewLocale) {
				if (file_exists($this->index->AppConfiguration->AppFolder . '/locales/' . $NewLocale . '.yml')) {
					$this->Strings =  \ExEngine\Extended\Spyc\Spyc::YAMLLoad($this->index->AppConfiguration->AppFolder . '/locales/' . $NewLocale . '.yml');
					$this->stringsFileLoaded=true;
                    $this->CurrentLocale = $NewLocale;
				} else {
					$this->ee->errorExit('MVC-ExEngine Locales','Locale `' . $NewLocale . '` not found in locales folder.');
				}
			}
		}

        /**
         * t
         * This function looks for the key-value of the string, this will change the locale if you set the
         * locale var in the controller that is using it. (ex. var $locale = 'en'; or $this->locale = 'en'; in a
         * controller method)
         * @param string $LocaleString
         * @param string $NotFoundValue (optional)
         * @return string
         */
		function t($LocaleString, $NotFoundValue="") {
            if ($this->controller instanceof Controller) {
                $this->changeLocale($this->controller->locale);
            }
			if (isset($this->Strings[$LocaleString])) {
                # If found in the selected locale, get the value.
				return $this->Strings[$LocaleString];
			} else {
                # If not, try to get from the default locale
				if (isset($this->DefaultStrings[$LocaleString])) {
					return $this->DefaultStrings[$LocaleString];
				} else {
                    # If not found in default locale, return the NotFoundValue and if enabled create in the default
                    # locales file and the others in the root of locales.
                    if (strlen($NotFoundValue) > 0) {
                        if ($this->index->AppConfiguration->AutomaticLocalePopulate)
                            $this->addToLocales($LocaleString, $NotFoundValue);
                        return $NotFoundValue;
                    }
                    if ($this->index->AppConfiguration->AutomaticLocalePopulate)
                        $this->addToLocales($LocaleString, $LocaleString);
					return $LocaleString;
				}
			}
		}

        /**
         * addToLocales
         * This method adds to the all root locale files (if enabled) the key-value pair of the searched string.
         * Is recommended to enable this function to quickly generate the locales file and then will be easy to
         * translate.
         * Important: You should chmod the locales files to enable writing, no error will be provided but the file will
         * not be updated.
         * $ cd app/locales/
         * $ chmod a+w *.yml
         * When going to production, remember to remove writing access.
         * $ cd app/locales
         * $ chmod a-w *.yml
         * @param $Key
         * @param $Value
         */
        function addToLocales($Key, $Value) {
            $this->DefaultStrings[$Key] = $Value;
            # Open default localefile:
            $NewItem = $Key . ': "' . $Value . '"' . PHP_EOL; #Yaml format.
            if (file_exists($this->DefaultStringsFile)) { #Only APPEND, this will not create the file.
                file_put_contents($this->DefaultStringsFile, $NewItem, FILE_APPEND);
            }
            if ($this->index->AppConfiguration->PopulateAllLocales) {
                foreach(glob($this->index->AppConfiguration->AppFolder . '/locales/*.yml') as $yFile) {
                    if ($this->ee->strContains($yFile, $this->index->AppConfiguration->DefaultLocale))
                        continue;
                    file_put_contents($yFile, $NewItem, FILE_APPEND);
                }
            }
        }
	}
}