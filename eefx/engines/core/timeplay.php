<?
#<EEMEv4>#

class timeplay
{
	private $ee;
	const CNAME = "timeplay";
	const NAME = "TimePlay Class";
	const VERSION = "0.0.1.2";
	const DATE = "29/05/2012";
	const RQEE7 = "7.0.0.7";
	
	private $langSet = "es";
	private $cntSet = "pe";
	
	private $langArray;
	private $resPath;
	
	function __construct(&$ee,$lang=null,$country=null) {	
		
		$this->ee = &$ee;
		if (isset($lang))
			$this->langSet = $lang;
		if (isset($country))
			$this->cntSet = $country;
		
		$this->resPath = $ee->meGetResPath("timeplay");
		include_once($this->resPath."lang.".$this->langSet.".php");
		include_once($this->resPath."reg.".$this->cntSet.".php");
		
		$ee->debugThis("timeplay","Object Created: lang: $this->langSet, country: $this->cntSet");
	}
	
	function getTimeDate($format,$timestamp=false) {
		if (!$timestamp) {
			$timestamp = time();
		}
				
		$dayCrossPlatform = strftime($this->crossPlatformE("%e"),$timestamp);
		$avail = array("{dName}","{dNumber}","{mName}","{mNumber}","{yFull}","{yMin}","{12hour}","{24hour}","{minutes}","{sec}","{ap}");
		$repla = array($this->getDayName(strftime("%w",$timestamp)),$dayCrossPlatform,$this->getMonthName(strftime("%m",$timestamp)),strftime("%m",$timestamp),strftime("%Y",$timestamp),strftime("%y",$timestamp),strftime("%I",$timestamp),strftime("%H",$timestamp),strftime("%M",$timestamp),strftime("%S",$timestamp),strftime("%P",$timestamp));
		
		return str_replace($avail,$repla,$format);		
	}
	
	function getDateRegional($style="full") {
		switch ($style) {
			case "full":
				return $this->getTimeDate(timeplay_reg::DATE_FORMAT_FULL);
				break;
			case "small":
				return $this->getTimeDate(timeplay_reg::DATE_FORMAT_SMALL);
				break;
		}
	}
	
	function getTimeRegional() {
		return $this->getTimeDate(timeplay_reg::TIME_FORMAT);
	}
	
	private function crossPlatformE($format) {
		if (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN') {
			$format = preg_replace('#(?<!%)((?:%%)*)%e#', '\1%#d', $format);
		}	
		return $format;
	}

	private function getMonthName($monthNumber) {
		switch($monthNumber) {
			case 1:
				return timeplay_lang::JAN;
				break;
			case 2:
				return timeplay_lang::FEB;
				break;
			case 3:
				return timeplay_lang::MAR;
				break;
			case 4:
				return timeplay_lang::APR;
				break;
			case 5:
				return timeplay_lang::MAY;
				break;
			case 6:
				return timeplay_lang::JUN;
				break;
			case 7:
				return timeplay_lang::JUL;
				break;
			case 8:
				return timeplay_lang::AUG;
				break;
			case 9:
				return timeplay_lang::SEP;
				break;
			case 10:
				return timeplay_lang::OCT;
				break;
			case 11:
				return timeplay_lang::NOV;
				break;
			case 12:
				return timeplay_lang::DEC;
				break;
		}
	}

	private function getDayName($dayNumber) {
		switch ($dayNumber)	{
			case 1:
				return timeplay_lang::MON;
				break;
			case 2:
				return timeplay_lang::TUE;
				break;
			case 3:
				return timeplay_lang::WED;
				break;	
			case 4:
				return timeplay_lang::THU;
				break;	
			case 5:
				return timeplay_lang::FRI;
				break;	
			case 6:
				return timeplay_lang::SAT;
				break;
			case 7:
				return timeplay_lang::SUN;
				break;	
		}
	}
	
}

?>