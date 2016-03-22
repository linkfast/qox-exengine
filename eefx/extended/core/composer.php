<?php

Namespace ExEngine\Extended;

/**
 * Class Composer
 * @package ExEngine\Extended
 */
class Composer {

	static $Loaded=false;

    /**
     * @var string|null
     */
    var $ComposerDir=null;

	/**
	 * @var bool|\ExEngine\Core
	 */
	var $ee;

	/**
	 * Composer constructor.
	 * This library provides support for Composer packages in ExEngine.
	 * Note: ExEngine does not relies on Composer but is compatible with it.
	 */
    function __construct() {
		$this->ee = &ee_gi();
		//print $this->ee->appPath.'vendor';
        if (file_exists($this->ee->appPath.'vendor') && is_dir($this->ee->appPath.'vendor'))
            $this->ComposerDir = $this->ee->appPath.'vendor';
    }

	/**
	 * Check if package is installed, format is: vendor/product, example: isPackageInstalled('twbs/bootstrap') .
	 * @param $packageName
	 * @return bool
	 */
	function isPackageInstalled($packageName) {
		$pkgDiscoveryName = $this->ComposerDir . '/' . $packageName . '/composer.json';
		if (file_exists($pkgDiscoveryName))
			return true;
		return false;
	}

	/**
	 * Check if composer is detected.
	 * @return bool
	 */
	function composerDetected() {
		return $this->ComposerDir!=null;
	}

	/**
	 * File proxy for hidden vendor folder.
	 * @param $Package
	 * @param $FilePath
	 * @param bool $Return
	 * @return bool|string
	 */
	function proxyFile($Package, $FilePath, $Return=false) {
		$File = $this->ComposerDir . '/' . $Package . '/' . $FilePath;
		if (file_exists($File)) {
			if ($Return) {
				return file_get_contents($File);
			} else {
				readfile($File);
			}
		} else return false;
	}

	/**
	 * Activates composer. Will return true if included successfully.
	 * @return bool
	 */
    function autoload() {
		if (!self::$Loaded) {
			if (file_exists($this->ComposerDir . '/autoload.php')) {
				include_once($this->ComposerDir . '/autoload.php');
				self::$Loaded = true;
				return true;
			} else return false;
		} else return true;
    }

    /**
	 * Returns full path to the product folder, returns false if package is not installed or not found.
     * @param string $packageName
     * @return string|bool
     */
    function getPackageDir($packageName) {
		//print $this->ee->httpGetUrlFromPath($this->ComposerDir . '/' . $Product);
        if (file_exists($this->ComposerDir . '/' . $packageName) && is_dir($this->ComposerDir . '/' . $packageName))
            return $this->ComposerDir . '/' . $packageName;
        else
            return false;
    }

}

?>