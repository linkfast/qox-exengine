<?php

class EE_Utilities {

	var $TextRenderer;
	var $ConstantsLoader;
	
	private $ee;
	function __construct() {
		$this->ee = &ee_gi();
		$this->TextRenderer = new EE_Util_TextRenderer($this);
		$this->ConstantsLoader = new EE_Util_ConstantsLoader($this);
	}
	
	function debug($Message) {
		$this->ee->debugThis("EE_Utilities",$Message);	
	}
	
}

class EE_Util_ConstantsLoader
{
	private $ee;
	private $pa;
	private $resPath;

	function __construct(EE_Utilities $Parent) {
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
		foreach ($InputArray as $key => $value) {
			define($Prefix . $key,$value);
		}
	}

	function debug($Message) {
		$this->ee->debugThis("EE_Utilities_ConstantsLoader",$Message);	
	}
}

class EE_Util_TextRenderer

{
	private $ee;
	private $pa;
	private $resPath;	
	
	function __construct(EE_Utilities $Parent) {
		$this->ee = &ee_gi();
		$this->pa = &$Parent;
		$this->resPath = $this->ee->eeResPath() . 'utils/Renderer/';
	}
	
	function RenderText($RendererLib, $Mode, $Text) {
		$or = eval('return Renderer_'.strtolower($RendererLib).'::$ori;');
		$to = eval('return Renderer_'.strtolower($RendererLib).'::$'.$Mode.';');
		$r = str_replace($or,$to,$Text);
		if (method_exists("Renderer_".$RendererLib,$Mode)) {
				$r = eval('return Renderer_'.strtolower($RendererLib).'::'.$Mode.'("'.$r.'");');
		}	
		return $r;
	}
	
	function Load($RendererLib) {
		if (file_exists($this->resPath . $RendererLib . '.php')) {
			include_once ( $this->resPath . $RendererLib . '.php' );
			if (method_exists("Renderer_".$RendererLib,"loadRq")) {
				eval('Renderer_'.strtolower($RendererLib).'::loadRq();');
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