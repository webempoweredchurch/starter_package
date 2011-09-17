<?php
/***************************************************************
* Copyright notice
*
* (c) 2006 Foundation for Evangelism
* All rights reserved
*
* This file is part of the Web-Empowered Church (WEC)
* (http://webempoweredchurch.org) ministry of the Foundation for Evangelism
* (http://evangelize.org). The WEC is developing TYPO3-based
* (http://typo3.org) free software for churches around the world. Our desire
* is to use the Internet to help offer new life through Jesus Christ. Please
* see http://WebEmpoweredChurch.org/Jesus.
*
* You can redistribute this file and/or modify it under the terms of the
* GNU General Public License as published by the Free Software Foundation;
* either version 2 of the License, or (at your option) any later version.
*
* The GNU General Public License can be found at
* http://www.gnu.org/copyleft/gpl.html.
*
* This file is distributed in the hope that it will be useful for ministry,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* This copyright notice MUST APPEAR in all copies of the file!
***************************************************************/
/** 
 * Top level class for the 'wec_flashpresentation' extension.
 *
 * @author		Web-Empowered Church Team <flashpresentation@webempoweredchurch.org>
 */


require_once(PATH_tslib.'class.tslib_pibase.php');
require_once(t3lib_extMgm::extPath('wec_flashpresentation').'class.tx_wecflashpresentation_flashobject.php');


/** 
 * Top level class for the 'wec_flashpresentation' extension. Subclasses
 * in pi1 and pi2 are shells that call all functions in parent class.
 *
 * @author		Web-Empowered Church Team <flashpresentation@webempoweredchurch.org>
 * @package		TYPO3
 * @subpackage	tx_wecflashpresentation
 */
class tx_wecflashpresentation extends tslib_pibase {
	var $prefixId = 'tx_wecflashpresentation';		// Same as class name
	var $scriptRelPath = 'class.tx_wecflashpresentation.php';	// Path to this script relative to the extension dir.
	var $extKey = 'wec_flashpresentation';	// The extension key.
	
	/**
	 * Main function for the class that passes handling to the pi1 or pi2 function.
	 * @param	string	Content coming into the extension.
	 * @param	array		Configuration options from Typoscript.
	 * @return	string	HTML output of extension.
	 */
	function main($content,$conf)	{
		/* Standard Initialization */
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		$this->pi_initPIflexform(); // Init and get the flexform data of the plugin
		
		$piFlexForm = $this->cObj->data['pi_flexform']; // Copy the flexform data to a new object
		$flashConf = array();
				
		/* Read all conf variables from Typoscript first */
		foreach($this->conf as $key => $value){
			if ($key{strlen($key)-1} != ".") {
				$flashConf[$key] = $this->cObj->cObjGetSingle($this->conf[$key],
																		 	$this->conf[$key."."],
			            											 	$key);
			}
		}
		
		/* Pull special values out of flashconf and into own variables.  We don't want do pass these along as FlashVars */
		$width = $flashConf['width'];
		$height = $flashConf['height'];
		$bgcolor = $flashConf['bgcolor'];
		$flashPath = $flashConf['flashPath'];
		
		/* Initialize values for FlashObject */
		$jsPath = t3lib_extmgm::siteRelPath($this->extKey).'res/';
		$name = 'wec_flashpresentation_'.$this->cObj->data['uid'];
		$version = '7';

		/* Create FlashObject class */		
		$flashObjectClassName = t3lib_div::makeInstanceClassName('tx_wecflashpresentation_flashobject');
		$flashObject = new $flashObjectClassName($flashPath, $name, $width, $height, $version, $bgcolor, $jsPath);
		
		unset($flashConf['userFunc']);
		unset($flashConf['width']);
		unset($flashConf['height']);
		unset($flashConf['bgcolor']);
		unset($flashConf['flashPath']);
		
		$flashObject->addParameter('wmode', $flashConf['wmode']);
		unset($flashConf['wmode']);
		
		/* Combine FlexForm and TS values */
		if($piFlexForm['data']) {
			foreach($piFlexForm['data'] as $sheet => $data) {
				foreach ($data as $lang => $value) {
					foreach ($value as $key => $val) {
						/* Skip over the slides field.  We'll perform special processing on this later */
						//if ($key != "slides") {
							$val = $this->pi_getFFvalue($piFlexForm, $key, $sheet);
							/* If value exists in Flexform, overwrite existing Typoscript value or create new array entry */
							if ($val != null) {
								$flashConf[$key] = $val;
							
								/* If bandwidth image comes from Flexform, set bwbase to uploads folder */
								if ($key == "bwbase") {
									$flashConf['bwbase'] = "uploads/tx_wecflashpresentation/";
								}
			
							}
						//}	
					}
				}
			}
		}
				
		$flashConf = array_merge($flashConf, $this->splitSlidesAndTimes($flashConf['slides']));
		unset($flashConf['slides']);
		
		if(!$flashConf['baseurl']) {
			$flashConf['baseurl'] = t3lib_div::getIndpEnv('TYPO3_SITE_URL');
		}
		$flashConf['lastloaded'] = "true";

		/* Add each FlashVar to FlashObject */
		foreach($flashConf as $var => $value) {
			$flashObject->addVariable($var, $value);	
		}
		$flashObject->write($name);
		
		/* Create the output */
		$html = '<div id="'.$name.'">You do not have the Flash plugin installed, or your browser does not support Javascript (you should enable it, perhaps?)</div>';
		$javascript = $flashObject->output();	

		return $this->pi_wrapInBaseClass($html.chr(10).$javascript);
		
		
		return $this->pi_wrapInBaseClass($this->outputHTML($flashPath, $width, $height, $bgcolor, $flashVars));
	}
	
	/*
	 * Implodes both array keys and values.  Taken from example code at php.net 
	 *
	 * @param	string	String placed between a key and its corresponding value.
	 *	@param	string	String placed between one key/value pairing and the next.
	 * @param	array		Array to be imploded.
	 * @param	boolean	Do not add empty array elements to imploded text.
	 * @return	string	Imploded string from array.
	 */
	function implode_assoc($inner_glue,$outer_glue,$array,$skip_empty=false){
		$output=array();
		foreach($array as $key=>$item) {
			if(!$skip_empty || isset($item)) {
				$output[] = $key.$inner_glue.$item;
			}
		}
		
		return implode($outer_glue,$output);
	}
	
	/*
	 * Reads a string-based slide image/timing pair and splits the values into two
	 * concatenated strings: one for slide images and one for slide timings.
	 *
	 * @param	string	Slide image and timing pairs.  Images and timing are separated
	 *					"|" and pairs are separated by a newline.
	 *	@return	string	Returns a concatenated string with images and timings separated.
	 */  
	function splitSlidesAndTimes($slides) {
		$slideArray = explode(chr(10), $slides);
		$imageArray = array();
		$timingArray = array();
		$prevTime = 0;
		
		foreach ( $slideArray as $slide ) {
			
			$slide = trim($slide);
			$splitArray = explode(" ", $slide);
			
			/* Either slide images or timings are missing */
			if (count($splitArray)==1) {
				/* Slide image is present but timing is not */
				if (strpos($splitArray[1], '.')) {
					$imageArray[] = trim($splitArray[0]);
					$timingArray[] = $prevTime + 1;
				}
				/* Else do nothing because slide image is not present */
			}
			/* Both slide images and timings are present */
			else {
				$slideStr .= trim($splitArray[0]).',';
				$timeStr .= $this->getSeconds(trim($splitArray[1])).',';
				$prevTime .= trim($splitArray[1]);
			}
		}
		
		/* Trim trailing quotes from the end of the strings */
		$slideStr = trim($slideStr, ',');
		$timeStr = trim($timeStr, ',');
		
		return array('slideImages' => $slideStr, 'slideTimes' => $timeStr);
	}
	
	/*
	 * Converts a string into seconds.  Input values are expected to conform
	 * to HH:MM:SS.
	 *
	 * @param	string	The string to be converted.
	 * @return	integer	The number of seconds represented by the input string.
	 */
	function getSeconds($str) {
		$timeArray = explode(":", $str, 3);
		
		$hour = 0;
		$min  = 0;
		$sec  = 0;
		
		switch(count($timeArray)) {
			case 1:
				$sec = $timeArray[0];
				break;
			case 2:
				$min = $timeArray[0];
				$sec = $timeArray[1];
				break;
			case 3:
				$hour = $timeArray[0];
				$min  = $timeArray[1];
				$sec  = $timeArray[2];
				break;
		}
		
		return ($hour * 3600) + ($min * 60) + $sec;
	}
}



if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/wec_flashpresentation/class.tx_wecflashpresentation.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/wec_flashpresentation/class.tx_wecflashpresentation.php"]);
}

?>