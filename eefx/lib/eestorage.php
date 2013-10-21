<?php
/**
@file eestorage.php
@author Giancarlo Chiappe <gch@linkfastsa.com> <gchiappe@gmail.com>
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

}

?>