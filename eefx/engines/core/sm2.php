<? 
#<EEMEv4>#

class sm2 {
	#EEMEv4 Control#
	private $ee;
	const CNAME = "sm2";
	const NAME = "SoundManager 2 for ExEngine 7";
	const VERSION = "1.0.0.0";
	const DATE = "04/02/2010";
	const RQEE7 = "7.0.0.4";
	#const RQME = "";
	#const RQEE = "";
	
	final static function meRQEngines() {
		
	}
	#EEMEv4 Control#
	
	private $resPath;
	
	function __construct($ee7)	{
		$this->ee = &$ee7;
		$this->resPath = $this->ee->meGetResPath("sm2","http");
	}
	
	function script() {
	return $this->resPath."script/soundmanager2-nodebug-jsmin.js" ;
	}
	
	function scriptd() {
		return $this->resPath."script/soundmanager2.js" ;
	}
	
	function init() {
		print '<script type="text/javascript">'." soundManager.url = '".$this->resPath."';".'	soundManager.debugMode = false;	</script>';
		}
		
	function initd() {
		print '<script type="text/javascript">'." soundManager.url = '".$this->resPath."';".'</script>';
	}
}

?>