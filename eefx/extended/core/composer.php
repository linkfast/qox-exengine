<?php

Namespace ExEngine\Extended;

/**
 * Class Composer
 * @package ExEngine\Extended
 */
class Composer {

    /**
     * @var string
     */
    var $ComposerDir=null;

	/**
	 * @var bool|\exengine
	 */
	var $ee;

    /**
     *
     */
    function __construct() {
		$this->ee = &ee_gi();
		//print $this->ee->appPath.'vendor';
        if (file_exists($this->ee->appPath.'vendor') && is_dir($this->ee->appPath.'vendor'))
            $this->ComposerDir = $this->ee->appPath.'vendor';
    }

	/**
	 * Check if composer is detected.
	 * @return bool
	 */
	function composerDetected() {
		return $this->ComposerDir!=null;
	}

	/**
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
     *
     */
    function autoload() {
        if (file_exists( $this->ComposerDir .'/autoload.php')) {
            include_once ( $this->ComposerDir . '/autoload.php' );
			return true;
        } else return false;
    }

    /**
     * @param string $Product
     * @return string|bool
     */
    function getPackageDir($Product) {
		//print $this->ee->httpGetUrlFromPath($this->ComposerDir . '/' . $Product);
        if (file_exists($this->ComposerDir . '/' . $Product) && is_dir($this->ComposerDir . '/' . $Product))
            return $this->ComposerDir . '/' . $Product;
        else
            return false;
    }

}

?>