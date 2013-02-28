<?php

# ExEngine 7 / Libs / Internet Files Manipulation

/*
	This file is part of ExEngine.
	Copyright © LinkFast Company
	
	ExEngine is free software; you can redistribute it and/or modify it under the 
	terms of the GNU Lesser Gereral Public Licence as published by the Free Software 
	Foundation; either version 2 of the Licence, or (at your opinion) any later version.
	
	ExEngine is distributed in the hope that it will be usefull, but WITHOUT ANY WARRANTY; 
	without even the implied warranty of merchantability or fitness for a particular purpose. 
	See the GNU Lesser General Public Licence for more details.
	
	You should have received a copy of the GNU Lesser General Public Licence along with ExEngine;
	if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, Ma 02111-1307 USA.
*/

class gd
{		
		private $ee;
		function gd($parentee) {
			$this->ee = &$parentee;
			if (!gd::checkGD()) {
				$this->ee->errorExit("ExEngine:GD Library","PHP's GD extension must be enabled to use this library.","GD_(Library)");
			}
		}
		
		/**
		 * Create a thumbnail image from $inputFileName no taller or wider than 
		 * $maxSize. Returns the new image resource or false on error.
		 * Author: mthorn.net
		 */
		static function thumbnail($inputFileName, $maxSize = 100)
		{
			$info = getimagesize($inputFileName);
		
			$type = isset($info['type']) ? $info['type'] : $info[2];
		
			// Check support of file type
			if ( !(imagetypes() & $type) )
			{
				// Server does not support file type
				return false;
			}
		
			$width  = isset($info['width'])  ? $info['width']  : $info[0];
			$height = isset($info['height']) ? $info['height'] : $info[1];
		
			// Calculate aspect ratio
			$wRatio = $maxSize / $width;
			$hRatio = $maxSize / $height;
		
			// Using imagecreatefromstring will automatically detect the file type
			$sourceImage = imagecreatefromstring(file_get_contents($inputFileName));
		
			// Calculate a proportional width and height no larger than the max size.
			if ( ($width <= $maxSize) && ($height <= $maxSize) )
			{
				// Input is smaller than thumbnail, do nothing
				return $sourceImage;
			}
			elseif ( ($wRatio * $height) < $maxSize )
			{
				// Image is horizontal
				$tHeight = ceil($wRatio * $height);
				$tWidth  = $maxSize;
			}
			else
			{
				// Image is vertical
				$tWidth  = ceil($hRatio * $width);
				$tHeight = $maxSize;
			}
		
			$thumb = imagecreatetruecolor($tWidth, $tHeight);
		
			if ( $sourceImage === false )
			{
				// Could not load image
				return false;
			}
		
			// Copy resampled makes a smooth thumbnail
			imagecopyresampled($thumb, $sourceImage, 0, 0, 0, 0, $tWidth, $tHeight, $width, $height);
			imagedestroy($sourceImage);
		
			return $thumb;
		}
		
		/**
		 * Save the image to a file. Type is determined from the extension.
		 * $quality is only used for jpegs.
		 * Author: mthorn.net
		 */
		static function imageToFile($im, $fileName, $quality = 80)
		{
			if ( !$im || file_exists($fileName) )
			{
			   return false;
			}
		
			$ext = strtolower(substr($fileName, strrpos($fileName, '.')));
		
			switch ( $ext )
			{
				case '.gif':
					imagegif($im, $fileName);
					break;
				case '.jpg':
				case '.jpeg':
					imagejpeg($im, $fileName, $quality);
					break;
				case '.png':
					imagepng($im, $fileName);
					break;
				case '.bmp':
					imagewbmp($im, $fileName);
					break;
				default:
					return false;
			}
		
			return true;
		}
		
		#$im = thumbnail('temp.jpg', 100);
		#imageToFile($im, 'temp-thumbnail.jpg');
		
		static function checkGD() {
			if (!extension_loaded('gd')) {
				return false;
			} else return true;			
		}
	
	
}

?>