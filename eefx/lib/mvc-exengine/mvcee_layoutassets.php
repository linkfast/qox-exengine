<?php

namespace ExEngine\MVC;

use ExEngine\Extended\Composer;

class LayoutAssets {

	const VERSION = '0.0.0.2';

	var $CSS;
	var $JS;
	var $Fonts;

	private $JSConfig;
	private $CSSConfig;
	private $FontConfig;
	private $StaticFolder;
	private $StaticFolderHTTP;
	private $DynamicParserHTTP;
	private $EE_ErrorMgmt_Title = 'MVC-ExEngine LayoutAssets Loader';
	private $EEComposer;
	private $ee;
	private $index;

	function __construct($StaticFolder, $StaticFolderHTTP, $DynamicParserHTTP) {
		$this->ee = &ee_gi();
		$this->index = &eemvc_get_index_instance();
		$this->ee->eeLoad('eespyc,composer');

		/* yaml support required */
		$spyc_instance = new \eespyc();
		$spyc_instance->load();

		$this->EEComposer = new Composer();

		$this->StaticFolder = $StaticFolder;
		$this->StaticFolderHTTP = $StaticFolderHTTP;
		$this->DynamicParserHTTP = $DynamicParserHTTP;
	}

	function loadAssets_String($Type='',$YamlString='') {
		$ValidTypes = ['Fonts','JS','CSS'];
		$RetData = '';
		if (in_array($Type,$ValidTypes)) {
			if (isset($YamlString) and
				strlen($YamlString) > 0) {
				$ParsedYAML = \ExEngine\Extended\Spyc\Spyc::YAMLLoadString($YamlString);
				if (is_array($ParsedYAML) and
					count($ParsedYAML) > 0) {
					foreach ($ParsedYAML as $YAMLObj) {
						switch ($Type) {
							case $ValidTypes[0]:
								$this->Fonts .= $this->LoadFont($YAMLObj);
								break;
							case $ValidTypes[1]:
								$this->JS .= $this->LoadJS($YAMLObj);
								break;
							case $ValidTypes[2]:
								$this->CSS .= $this->LoadCSS($YAMLObj);
								break;
						}
					}
				} else {
					$this->ee->errorExit($this->EE_ErrorMgmt_Title,'loadAssets require a valid YAML string, read the default assets yml file for more information and example ('.$this->index->AppConfiguration->AppFolder.'/assets/*.yml).');
				}
			} else {
				$this->ee->errorExit($this->EE_ErrorMgmt_Title,'loadAssets require a valid YAML string, read the default assets yml file for more information and example ('.$this->index->AppConfiguration->AppFolder.'/assets/*.yml).');
			}
		} else {
			$this->ee->errorExit($this->EE_ErrorMgmt_Title,'loadAssets $Type param must be either: Fonts, JS or CSS.');
		}
	}

    /**
     * @param string $Type
     * @param string $YamlString
     * @param bool $Return
     * @return string
     */
	function loadAssets_ControllerView($Type='',$YamlString='',$Return=false) {
		$ValidTypes = ['Fonts','JS','CSS'];
		$RetData = '';
		if (in_array($Type,$ValidTypes)) {
			if (isset($YamlString) and
				strlen($YamlString) > 0) {
				$ParsedYAML = \ExEngine\Extended\Spyc\Spyc::YAMLLoadString($YamlString);
				if (is_array($ParsedYAML) and
					count($ParsedYAML) > 0) {
					foreach ($ParsedYAML as $YAMLObj) {
						switch ($Type) {
							case $ValidTypes[0]:
									$RetData .= $this->LoadFont($YAMLObj);
								break;
							case $ValidTypes[1]:
									$RetData .= $this->LoadJS($YAMLObj);
								break;
							case $ValidTypes[2]:
									$RetData .= $this->LoadCSS($YAMLObj);
								break;
						}
						if ($Return)
							return $RetData;
						else
							print $RetData;
					}
				} else {
					$this->ee->errorExit($this->EE_ErrorMgmt_Title,'loadAssets require a valid YAML string, read the default assets yml file for more information and example ('.$this->index->AppConfiguration->AppFolder.'/assets/*.yml).');
				}
			} else {
				$this->ee->errorExit($this->EE_ErrorMgmt_Title,'loadAssets require a valid YAML string, read the default assets yml file for more information and example ('.$this->index->AppConfiguration->AppFolder.'/assets/*.yml).');
			}
		} else {
			$this->ee->errorExit($this->EE_ErrorMgmt_Title,'loadAssets $Type param must be either: Fonts, JS or CSS.');
		}
	}

	function loadAssets() {
		$AssetsFiles = [
			'JSConfig' => $this->index->AppConfiguration->AppFolder . '/assets/javascript.yml',
			'CSSConfig' => $this->index->AppConfiguration->AppFolder .'/assets/css.yml',
			'FontConfig' => $this->index->AppConfiguration->AppFolder .'/assets/fonts.yml'
		];
		foreach ($AssetsFiles as $AKey => $AValue) {
			if (file_exists($AValue)) {
				$this->$AKey = \ExEngine\Extended\Spyc\Spyc::YAMLLoad($AValue);
				switch ($AKey) {
					case 'JSConfig':
						foreach ($this->$AKey as $JSObj) {
							$this->JS .= $this->LoadJS($JSObj);
						}
						break;
					case 'CSSConfig':
						foreach ($this->$AKey as $CSSObj) {
							$this->CSS .= $this->LoadCSS($CSSObj);
						}
						break;
					case 'FontConfig':
						foreach ($this->$AKey as $FontObj) {
							$this->Fonts .= $this->LoadFont($FontObj);
						}
						break;
				}
			} else {
				$this->ee->errorExit($this->EE_ErrorMgmt_Title,'Asset YML not found: ' .$AValue );
			}
		}
		if (strlen($this->Fonts)) {
			$this->Fonts = '<style>'."\n" . $this->Fonts . '</style>' . "\n";
		}
	}

	private function LoadFont($Data) {
		if (isset($Data['package'])) {
			/* load composer package */
			$File = $this->EEComposer->getPackageDir($Data['package']). '/' . $Data['src'];
			if (file_exists($File)) {
				return "\t" . '@font-face { font-family: \''.$Data['name'].'\'; src: url(\''.$File.'\');}' . "\n";
			} else {
				$this->ee->errorExit($this->EE_ErrorMgmt_Title,'Required asset not found (`'.$Data['src'].'`).');
			}
		} else {
			if (isset($Data['remote']) && $Data['remote'] == true) {
				/* load remote data */
				return "\t" . '@font-face { font-family: \''.$Data['name'].'\'; src: url(\''.$Data['src'].'\');}' . "\n";
			} else {
				/* load static folder data */
				$File = $this->StaticFolder . '/fonts/' . $Data['src'] ;
				if (file_exists($File)) {
					$File = '/fonts/' . $Data['src'];
					return "\t" . '@font-face { font-family: \''.$Data['name'].'\'; src: url(\''.$File.'\');}' . "\n";
				} else {
					$this->ee->errorExit($this->EE_ErrorMgmt_Title,'Required asset not found (`'.$Data['src'].'`).');
				}
			}
		}
	}

	private function LoadJS($Data) {
		if (isset($Data['package'])) {
			/* load composer package */
			$File = $this->EEComposer->getPackageDir($Data['package']). '/' . $Data['src'] . '.js';
			if (file_exists($File)) {
				return "\t" . '<script src="'. $this->ee->httpGetUrlFromPath($File) .'"></script>' . "\n";
			} else {
				$this->ee->errorExit($this->EE_ErrorMgmt_Title,'Required asset not found (`'.$Data['src'].'`).');
			}
		} else {
			if (isset($Data['remote']) && $Data['remote'] == true) {
				/* load remote data */
				return "\t" . '<script src="'. $Data['src'] .'"></script>' . "\n";
			} else {
				if (isset($Data['requirejs']) and $Data['requirejs']==true) {
					$File = $Data['src'] . '/' . 'require.js';
					if (file_exists($File)) {
						return "\t" . '<script src="'. $this->ee->httpGetUrlFromPath($this->ee->appPath.$File) .'"></script>' . "\n";
					} else {
						$this->ee->errorExit($this->EE_ErrorMgmt_Title,'Required asset not found (`'.$Data['src'].'`).');
					}
				} else {
					/* load static folder data */
					$File = $this->StaticFolder . '/javascript/' . $Data['src'] . '.js';
					if (file_exists($File)) {
						$File = '/javascript/' . $Data['src'] . '.js';
						if (isset($Data['dynamic']) && $Data['dynamic']==true) {
							$File = $this->DynamicParserHTTP . $File;
						} else {
							$File = $this->StaticFolderHTTP . $File;
						}
						return "\t" . '<script src="'. $File .'"></script>' . "\n";
					} else {
						$this->ee->errorExit($this->EE_ErrorMgmt_Title,'Required asset not found (`'.$Data['src'].'`).');
					}
				}
			}
		}
	}

	private function LoadCSS($Data) {
		if (isset($Data['package'])) {
			/* load composer package */
			$File = $this->EEComposer->getPackageDir($Data['package']). '/' . $Data['src'] . '.css';
			if (file_exists($File)) {
				return "\t" . '<link href="'. $this->ee->httpGetUrlFromPath($File) .'" rel="stylesheet">' . "\n";
			} else {
				$this->ee->errorExit($this->EE_ErrorMgmt_Title,'Required asset not found (`'.$Data['src'].'`).');
			}
		} else {
			if (isset($Data['remote']) && $Data['remote'] == true) {
				/* load remote data */
				return "\t" . '<link href="'. $Data['src'] .'" rel="stylesheet">' . "\n";
			} else {
				/* load static folder data */
				$File = $this->StaticFolder . '/css/' . $Data['src'] . '.css';
				if (file_exists($File)) {
					$File = '/css/' . $Data['src'] . '.css';
					if (isset($Data['dynamic']) && $Data['dynamic']==true) {
						$File = $this->DynamicParserHTTP . $File;
					} else {
						$File = $this->StaticFolderHTTP . $File;
					}
					return "\t" . '<link href="'. $File .'" rel="stylesheet">' . "\n";
				} else {
					$this->ee->errorExit($this->EE_ErrorMgmt_Title,'Required asset not found (`'.$Data['src'].'`).');
				}
			}
		}
	}


}

?>