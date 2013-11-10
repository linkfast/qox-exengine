<?php
/**
@file eestorage.php
@author Giancarlo Chiappe <gch@linkfastsa.com> <gchiappe@gmail.com>
@version 0.0.1.1

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

ExEngine 7 / Libs / ExEngine Storage (eestorage)

*/

class ee_storage {

	private $ee;
	function __construct($subFolder) {
		$this->ee =& ee_gi();
		$this->storageFolder = $this->ee->cArray["storage_path"];
		$this->subFolder = $subFolder;
		$this->init();
	}

	private $storageFolder;
	private $actualFolder;
	private $subFolder;

	private $phpOwner;
	private $phpGroup;

	private $dirOwner;
	private $dirGroup;
	private $isWritable=false;

	private function init() {
		clearstatcache(true);
		if (self::checkStorageFolder(false)) {
			$path = $this->storageFolder . $this->ee->appName ;
			if ((file_exists($path) && !is_dir($path))) {
				unlink($path);
			}
			if (!file_exists($path)) {
				mkdir($path);
			}
			$path = $this->storageFolder . $this->ee->appName . "/" . $this->subFolder;
			if ((file_exists($path) && !is_dir($path))) {
				unlink($path);
			}
			if (!file_exists($path)) {
				mkdir($path);
			}
		} else return false;
	}

	final function getFolder() {
		return $this->storageFolder . $this->ee->appName . "/" . $this->subFolder . "/";
	}

	public static function checkStorageFolder($exit=true) {
		$ee  =& ee_gi();
		$path = $ee->cArray["storage_path"];

		if (!(file_exists($path) && is_dir($path))) {
			if ($exit) $ee->errorExit("ExEngine Storage","Storage folder does not exists. Check path.");
			return false;
		}
		
		$dirInfo = self::getDirInfo($exit);
		$phpInfo = self::getPhpInfo();

		if (!$dirInfo) return false;

		if ($dirInfo[0] != $phpInfo[0] || $dirInfo[1] != $phpInfo[1]) {
			$ee->errorWarning("ExEngine Storage: Storage folder owner is not same as PHP user.");
		}

		if ($dirInfo[3] != true) {
			if ($exit) $ee->errorExit("ExEngine Storage","Storage folder is not writable, check permissions.");
			return false;
		}

		return true;
	}

	private static function getDirInfo($exit=true) {
		clearstatcache(true);		
		$ee  =& ee_gi();
		$p = $ee->cArray["storage_path"];
		try {
			$iterator = new DirectoryIterator($p);
			$file = $iterator->current();
			while($iterator->valid() && $file->getFilename() != "." ) {
				$file = $iterator->current();
				$iterator->next();
			}
			$userInfo = posix_getpwuid($iterator->getOwner());
			$groupInfo = posix_getgrgid($iterator->getGroup());
			return array($userInfo, $groupInfo, $file->isReadable(), $file->isWritable());		
		} catch(Exception $e) {
			if ($exit) $ee->errorExit("ExEngine Storage","Invalid storage path, please check storage folder requirements. (folder not statable, check owner and permissions).");
			return false;
		}
	}

	private static function getPhpInfo() {
		$userInfo = posix_getpwuid(posix_getuid());
		$groupInfo = posix_getgrgid(posix_getgid());
		return array($userInfo['name'], $groupInfo['name']);
	}

	

	/* Function: download with resume/speed/stream options */ 

	/* 
	    Parametrs: downloadFile(File Location, File Name, 
	    max speed, is streaming   
	    If streaming - movies will show as movies, images as images 
	    instead of download prompt 
	*/ 

	public static function downloadFile($fileLocation,$fileName,$maxSpeed=100,$doStream=false) { 
	    if (connection_status()!=0) return(false); 
	    $ex = explode('.',$fileName);
	    $ex = end($ex);
	    $extension = strtolower($ex); 

	    /* List of File Types */ 
	    $fileTypes['swf'] = 'application/x-shockwave-flash'; 
	    $fileTypes['pdf'] = 'application/pdf'; 
	    $fileTypes['exe'] = 'application/octet-stream'; 
	    $fileTypes['zip'] = 'application/zip'; 
	    $fileTypes['doc'] = 'application/msword'; 
	    $fileTypes['xls'] = 'application/vnd.ms-excel'; 
	    $fileTypes['ppt'] = 'application/vnd.ms-powerpoint'; 
	    $fileTypes['gif'] = 'image/gif'; 
	    $fileTypes['png'] = 'image/png'; 
	    $fileTypes['jpeg'] = 'image/jpg'; 
	    $fileTypes['jpg'] = 'image/jpg'; 
	    $fileTypes['rar'] = 'application/rar';     

	    $fileTypes['ra'] = 'audio/x-pn-realaudio'; 
	    $fileTypes['ram'] = 'audio/x-pn-realaudio'; 
	    $fileTypes['ogg'] = 'audio/x-pn-realaudio'; 

	    $fileTypes['wav'] = 'video/x-msvideo'; 
	    $fileTypes['wmv'] = 'video/x-msvideo'; 
	    $fileTypes['avi'] = 'video/x-msvideo'; 
	    $fileTypes['asf'] = 'video/x-msvideo'; 
	    $fileTypes['divx'] = 'video/x-msvideo'; 

	    $fileTypes['mp3'] = 'audio/mpeg'; 
	    $fileTypes['mp4'] = 'video/mp4'; 
	    $fileTypes['mpeg'] = 'video/mpeg'; 
	    $fileTypes['mpg'] = 'video/mpeg'; 
	    $fileTypes['mpe'] = 'video/mpeg'; 
	    $fileTypes['mov'] = 'video/quicktime'; 
	    $fileTypes['swf'] = 'video/quicktime'; 
	    $fileTypes['3gp'] = 'video/quicktime'; 
	    $fileTypes['m4a'] = 'video/quicktime'; 
	    $fileTypes['m4v'] = 'video/x-m4v';
	    $fileTypes['aac'] = 'video/quicktime'; 
	    $fileTypes['m3u'] = 'video/quicktime'; 

	    $contentType = $fileTypes[$extension]; 


	    header("Cache-Control: public"); 
	    header("Content-Transfer-Encoding: binary\n"); 
	    header("Content-Type: $contentType"); 

	    $contentDisposition = 'attachment'; 

	    if($doStream == true){ 
	        /* extensions to stream */ 
	        $array_listen = array('mp3','m3u','m4a','mid','ogg','ra','ram','wm', 
	        'wav','wma','aac','3gp','avi','mov','mp4','mpeg','mpg','swf','wmv','divx','asf'); 
	        if(in_array($extension,$array_listen)){  
	            $contentDisposition = 'inline'; 
	        } 
	    } 

	    if (strstr($_SERVER['HTTP_USER_AGENT'], "MSIE")) { 
	        $fileName= preg_replace('/\./', '%2e', $fileName, substr_count($fileName,
	'.') - 1); 
	        header("Content-Disposition: $contentDisposition; filename=\"$fileName\""); 
	    } else { 
	        header("Content-Disposition: $contentDisposition; filename=\"$fileName\""); 
	    } 

	    header("Accept-Ranges: bytes");    
	    $range = 0; 
	    $size = filesize($fileLocation); 

	    if(isset($_SERVER['HTTP_RANGE'])) { 
	        list($a, $range)=explode("=",$_SERVER['HTTP_RANGE']); 
	        str_replace($range, "-", $range); 
	        $size2=$size-1; 
	        $new_length=$size-$range; 
	        header("HTTP/1.1 206 Partial Content"); 
	        header("Content-Length: $new_length"); 
	        header("Content-Range: bytes $range$size2/$size"); 
	    } else { 
	        $size2=$size-1; 
	        header("Content-Range: bytes 0-$size2/$size"); 
	        header("Content-Length: ".$size); 
	    } 

	    if ($size == 0 ) { die('Zero byte file! Aborting download');} 
	    if (PHP_VERSION < 6) {
			$magic_quotes = get_magic_quotes_runtime();
			ini_set("magic_quotes_runtime", 0);
		}
	    $fp=fopen("$fileLocation","rb"); 

	    fseek($fp,$range); 

	    while(!feof($fp) and (connection_status()==0)) 
	    { 
	        set_time_limit(0); 
	        print(fread($fp,1024*$maxSpeed)); 
	        flush(); 
	        ob_flush(); 
	        sleep(1); 
	    } 
	    fclose($fp); 

	    return((connection_status()==0) and !connection_aborted()); 
	}
}
?>