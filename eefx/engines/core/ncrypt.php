<?
#<EEMEv4>#

class ncrypt
{
	private $ee;
	const CNAME = "ncrypt";
	const NAME = "ExEngine NCrypt Class";
	const VERSION = "0.0.1.0";
	const DATE = "17/01/2011";
	const RQEE7 = "7.0.0.7";
	
	private $cryptMethod;
	private $iv = "1234567890";
	
	public $ekey;
	
	function __construct($ee,$encryptionMethod="CBC") {
		$this->ee = &$ee;
		$this->cryptMethod = $encryptionMethod;
		switch ($encryptionMethod) {
			case "CBC" :
			//$this->iv = mcrypt_create_iv (mcrypt_get_block_size (MCRYPT_TripleDES, MCRYPT_MODE_CBC), MCRYPT_DEV_RANDOM);
			break;
		}
	}
	
	function encrypt($data) {
		if ($this->ekey != null) {
			$enc=mcrypt_cbc (MCRYPT_TripleDES, $this->ekey, $data, MCRYPT_ENCRYPT, $this->iv);		
		  	return base64_encode($enc);
		} else {
			$this->ee->errorExit("ExEngine NCrypt Class","En/decryption key must be provided before calling en/decrypting functions.".' Change $ekey value.',"Ncrypt");
			return null;
		}
	}
	
	function decrypt($data) {
		if ($this->ekey != null) {
			$data = base64_decode($data);
			$dec = mcrypt_cbc (MCRYPT_TripleDES, $this->ekey, $data, MCRYPT_DECRYPT, $this->iv);
			return $dec;
		} else {
			$this->ee->errorExit("ExEngine NCrypt Class","En/decryption key must be provided before calling en/decrypting functions.".' Change $ekey value.',"Ncrypt");
			return null;
		}
	}
}