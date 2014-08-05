<?php

namespace ExEngine\Extended\Utils;

class Utilities {

	var $TextRenderer;
	var $ConstantsLoader;

	private $ee;
	function __construct() {
		$this->ee = &ee_gi();
		$this->TextRenderer = new TextRenderer($this);
		$this->ConstantsLoader = new ConstantsLoader($this);
	}

	function debug($Message) {
		$this->ee->debugThis("EE_Utilities",$Message);
	}

}

class ConstantsLoader
{
	private $ee;
	private $pa;
	private $resPath;

	function __construct(Utilities $Parent) {
		$this->ee = &ee_gi();
		$this->pa = &$Parent;
		$this->resPath = $this->ee->eeResPath() . 'utils/Constants/';
	}

	function Load($ConstantLib) {
		if (file_exists($this->resPath . $ConstantLib . '.php')) {
			include_once ( $this->resPath . $ConstantLib . '.php' );

			$ClassName = 'EE_Constants_'.strtoupper($ConstantLib);
			$Obj = new $ClassName();

			$this->ArrayToConstant($Obj->constants, $Obj->prefix);

			$this->debug("Constants Lib '$ConstantLib' loaded.");
		} else {
			$this->debug("Constants Lib Not Found.");
		}
	}

	function ArrayToConstant($InputArray,$Prefix=null) {
		if (is_array($InputArray)) {
			foreach ($InputArray as $key => $value) {
				define($Prefix . $key,$value);
			}
		}
	}

	function debug($Message) {
		$this->ee->debugThis("EE_Utilities_ConstantsLoader",$Message);
	}
}

class TextRenderer

{
	private $ee;
	private $pa;
	private $resPath;

	function __construct(Utilities $Parent) {
		$this->ee = &ee_gi();
		$this->pa = &$Parent;
		$this->resPath = $this->ee->eeResPath() . 'utils/Renderer/';
	}

	function RenderText($RendererLib, $Mode, $Text) {
		//print $Mode;
		$or = eval('return \\ExEngine\\Extended\\Utils\\Renderer\\'.ucfirst($RendererLib).'::$ori;');
		$to = eval('return \\ExEngine\\Extended\\Utils\\Renderer\\'.ucfirst($RendererLib).'::$'.$Mode.';');
		$r = str_replace($or,$to,$Text);
		if (method_exists('\\ExEngine\\Extended\\Utils\\Renderer\\'.ucfirst($RendererLib),$Mode)) {

			$r = eval('return \\ExEngine\\Extended\\Utils\\Renderer\\'.ucfirst($RendererLib).'::'.$Mode.'("'.$r.'");');
		}
		return $r;
	}

	function Load($RendererLib) {
		if (file_exists($this->resPath . $RendererLib . '.php')) {
			include_once ( $this->resPath . $RendererLib . '.php' );
			if (method_exists("\\ExEngine\\Extended\\Utils\\Renderer\\".ucfirst($RendererLib),"loadRq")) {
				eval('\\ExEngine\\Extended\\Utils\\Renderer\\'.ucfirst($RendererLib).'::loadRq();');
			}
			$this->debug("Renderer Lib '$RendererLib' loaded.");
		} else {
			$this->debug("Renderer Lib Not Found.");
		}
	}

	function debug($Message) {
		$this->ee->debugThis("EE_Utilities_TextRenderer",$Message);
	}
}
?>