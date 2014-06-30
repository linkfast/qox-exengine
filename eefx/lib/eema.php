<?php
/**
@file eema.php
@author Giancarlo Chiappe <gchiappe@qox-corp.com> <gchiappe@outlook.com.pe>
@version 0.0.1.0

@section LICENSE

ExEngine is free software; you can redistribute it and/or modify it under the
terms of the GNU Lesser Gereral Public Licence as published by the Free Software
Foundation; either version 2 of the Licence, or (at your opinion) any later version.
ExEngine is distributed in the hope that it will be usefull, but WITHOUT ANY WARRANTY;
without even the implied warranty of merchantability or fitness for a particular purpose.
See the GNU Lesser General Public Licence for more details.

You should have received a copy of the GNU Lesser General Public Licence along with ExEngine;
if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, Ma 02111-1307 USA.

@section DESCRIPTION

ExEngine 7 / Libs / ExEngine Message Agent (eema)

 */

/**
 * Class eema
 */
class eema {
    const VERSION = "0.0.1.4";
    private $ee;
    private $appKey = null;
    private $appShortName = null;
    private $appLongName = null;
	private $fBack = false;

    public $dateFormat = "%d/%b/%Y %H:%M:%S.%f";

	public static function strftimeu($format, $microtime=null)
	{
		if (!isset($microtime)) {
			$microtime=microtime(true);
		}
		if (preg_match('/^[0-9]*\\.([0-9]+)$/', $microtime, $reg)) {
			$decimal = substr(str_pad($reg[1], 6, "0"), 0, 6);
		} else {
			$decimal = "000000";
		}
		$format = preg_replace('/(%f)/', $decimal, $format);
		return strftime($format, $microtime);
	}

	/**
	 * @param      $applicationShortName	Shortname should be used in the EEMA client interface.
	 * @param null $applicationLongName		Longname should be used in the EEMA client interface to get a brief description of the app.
	 */
	function __construct($applicationShortName, $applicationLongName=null, $fallBackInLegacy=false) {
        @session_start();
        $this->ee =& ee_gi();

		$applicationShortName = $this->ee->miscURLClean($applicationShortName);

        /* search app if registered */
        $found = false;
        if (isset($_SESSION["exengine-eema-apps"]) && is_array($_SESSION["exengine-eema-apps"])) {
            foreach ($_SESSION["exengine-eema-apps"] as &$appf) {
                if (is_array($appf)) {
                    if ($appf['appShortName'] == $applicationShortName) {
                        $found=true;
                        $this->appKey = $appf['appKey'];
						if (isset($applicationLongName)) {
							$appf['appLongName'] = $applicationLongName;
						}
						$appf['fallBackLegacy'] = $fallBackInLegacy;
                        break;
                    }
                }else{
                    $found=false;
                }
            }
        } else {
            $found = false;
        }

        /* if not found, register it */
        if (!$found) {
            $_SESSION["exengine-eema-apps"][] = array(
                'appShortName' => $applicationShortName,
                'appLongName' => $applicationLongName,
                'appKey' => md5(rand()),
				'fallBackLegacy' => $fallBackInLegacy
            );
        }

        $this->appShortName = $applicationShortName;
        $this->appLongName = $applicationLongName;
		$this->fBack = $fallBackInLegacy;
        /*
        $da = array ( "date" => strftime($dateFormat), "msg"=> $message);
        if (!isset($_SESSION[$app][0])) {
            $_SESSION[$app][0] = $da;
        } else {
            $_SESSION[$app][] = $da;
        }
        */
    }


    function disconnect() {
        $index=0;
        $found = false;
        if (isset($_SESSION["exengine-eema-apps"]) && is_array($_SESSION["exengine-eema-apps"])) {
            foreach ($_SESSION["exengine-eema-apps"] as $appf) {
                if (is_array($appf)) {
                    if ($appf['appShortName'] == $this->appShortName) {
                        $found=true;
                        $index++;
                        break;
                    }
                }else{
                    $found=false;
                }
            }
        } else {
            $found = false;
        }
        if ($_SESSION["exengine-eema-apps"][$index]['appShortName'] == $this->appShortName)
            unset($_SESSION["exengine-eema-apps"][$index]['appShortName']);

		if ($this->fBack) {
			$this->ee->debugDisconnect('eema-'.$this->appShortName);
		}
    }

	public static function getAppData($appKey) {
		/* search app if registered */
		$result = array();
		if (isset($_SESSION["exengine-eema-apps"]) && is_array($_SESSION["exengine-eema-apps"])) {
			foreach ($_SESSION["exengine-eema-apps"] as $appf) {
				if (is_array($appf)) {
					if ($appf['appKey'] == $appKey) {
						$result = $appf;
						break;
					}
				}
			}
		}
		return $result;
	}

	public static function getApps() {
		$returnArr = array();
		if (isset($_SESSION["exengine-eema-apps"]) && is_array($_SESSION["exengine-eema-apps"])) {
			foreach ($_SESSION["exengine-eema-apps"] as $appf) {
				if (is_array($appf)) {
					$returnArr[] = $appf;
				}
			}
		}
		return $returnArr;
	}

    public static function clearApps() {
        @session_start();
        unset($_SESSION["exengine-eema-apps"]);
    }

	public static function getObjFromKey($appKey) {
		$result = null;
		if (isset($_SESSION["exengine-eema-apps"]) && is_array($_SESSION["exengine-eema-apps"])) {
			foreach ($_SESSION["exengine-eema-apps"] as $appf) {
				if (is_array($appf)) {
					if ($appf['appKey'] == $appKey) {
						$result = new eema($appf['appShortName'], $appf['appLongName'],$appf['fallBackLegacy']);
						break;
					}
				}
			}
		}
		return $result;
	}

	public static function getMessages($appKey,$inReverseOrder=false,$nl2br=false, $additionalDataParse=false) {
		if (isset($_SESSION['exengine-eema-messages'][$appKey]) && is_array($_SESSION['exengine-eema-messages'][$appKey])) {
			$data = $_SESSION['exengine-eema-messages'][$appKey];

			if ($nl2br || $additionalDataParse) {
				foreach ($data as $key => $element) {
					if ($nl2br)
						$data[$key]['message'] = str_replace(array("\n","\r"),"",@nl2br($data[$key]['message']));
					if ($additionalDataParse) {
						if (isset($data[$key]['additionalData']) && is_array($data[$key]['additionalData']))
							$data[$key]['additionalData'] = str_replace(array("\n","\r"),"",@nl2br(var_export($data[$key]['additionalData'],true)));
					}
				}
			}

			if ($inReverseOrder)
				return @array_reverse($data);
			else
				return $data;
		}
		else
			return array();
	}

    public function clearMessages() {
        $_SESSION['exengine-eema-messages'][$this->appKey] = array();
		if ($this->fBack) {
			$this->ee->debugClean('eema-'.$this->appShortName);
		}
    }

    private function saveMessage($logLevel, $Date, $Message, $additionalData=null) {
        $messageData = array(
            'level' => $logLevel,
            "date" => $Date,
            "message" => $Message
        );
        if (isset($additionalData) && is_array($additionalData)) {
            $messageData["additionalData"] = $additionalData;
        }
        $_SESSION['exengine-eema-messages'][$this->appKey][] = $messageData;

		if ($this->fBack) {
			$this->ee->debugThis('eema-'.$this->appShortName, $logLevel . ': ' . $Message);
		}
    }

    /* log levels: TRACE < DEBUG < INFO < WARN < ERROR < FATAL */

	public function t($Message=null,$additionalData=null) {
        if (!isset($Message)) {
            $Message = 'Default Trace Message';
        }
        $this->saveMessage('trace', eema::strftimeu($this->dateFormat), $Message, $additionalData);
    }

    public function d($Message=null,$additionalData=null) {
        if (!isset($Message)) {
            $Message = 'Default Debug Message';
        }
        $this->saveMessage('debug', eema::strftimeu($this->dateFormat), $Message, $additionalData);
    }

    public function i($Message=null,$additionalData=null) {
        if (!isset($Message)) {
            $Message = 'Default Info Message';
        }
        $this->saveMessage('info', eema::strftimeu($this->dateFormat), $Message, $additionalData);
    }

    public function w($Message=null,$additionalData=null) {
        if (!isset($Message)) {
            $Message = 'Default Warning Message';
        }
        $this->saveMessage('warning', eema::strftimeu($this->dateFormat), $Message, $additionalData);
    }

    public function e($Message=null,$additionalData=null) {
        if (!isset($Message)) {
            $Message = 'Default Error Message';
        }
        $this->saveMessage('error', eema::strftimeu($this->dateFormat), $Message, $additionalData);
    }

    public function f($Message=null,$additionalData=null) {
        if (!isset($Message)) {
            $Message = 'Default Fatal Message';
        }
        $this->saveMessage('fatal', eema::strftimeu($this->dateFormat), $Message, $additionalData);
    }
}
?>