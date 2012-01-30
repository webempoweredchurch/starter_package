<?php
/***************************************************************
* Copyright notice
*
* (c) 2005-2008 Christian Technology Ministries International Inc.
* All rights reserved
*
* This file is part of the Web-Empowered Church (WEC)
* (http://WebEmpoweredChurch.org) ministry of Christian Technology Ministries 
* International (http://CTMIinc.org). The WEC is developing TYPO3-based
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

require_once(PATH_tslib.'class.tslib_pibase.php');
require_once(t3lib_extMgm::extPath('wec_ebible').'class.tx_wecebible_itemsProcFunc.php');
require_once(t3lib_extMgm::extPath('wec_ebible').'class.tx_wecebible_api.php');
 
/**
 *
 * Plugin for Bible translation selection from eBible.com
 * @author	Web-Empowered Church Team <ebible@webempoweredchurch.org>
 * @package TYPO3
 * @subpackage tx_wecebible
 */
class tx_wecebible_pi2 extends tslib_pibase {
	var $prefixId = 'tx_wecebible_pi2';		// Same as class name
	var $scriptRelPath = 'pi2/class.tx_wecebible_pi2.php';	// Path to this script relative to the extension dir.
	var $extKey = 'wec_ebible';	// The extension key.
	
	/**
	 * Displays the eBible.com Verse of the Day.
	 * @param	array		Content array.
	 * @param	array		Conf array.
	 * @return	string		HTML/Javascript to display verse of the day.
	 */
	function main($content, $conf) {
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();
		
		// don't show the selector if the user can't do anything with it
		if(!$conf['allowUserTranslation'] && $conf['hideIfUserTranslationNotAllowed']) return null;
		
		/* Check GPvars for a users translation preference and saves it */
		tx_wecebible_api::saveDefaultTranslation();
		
		/* Load the template and the main subpart */
		$template = $this->cObj->fileResource($this->conf['templateFile']);
		$content =  $this->cObj->getSubpart($template, "###SELECT_TRANSLATION###");
				
		$markers = array();
		$markers['label'] = $this->pi_getLL('translation');
		$markers['post_url'] = t3lib_div::getIndpEnv('TYPO3_REQUEST_URL');
		
		/* Gets the default translation */
		$defaultTranslation = tx_wecebible_api::getDefaultTranslation();
		
		/* Loop over each translation, building a selection list. */
		$translations = tx_wecebible_itemsProcFunc::getBibleTranslations();
		
		// build dropdown options
		foreach($translations['items'] as $translation) {
			/* Load the translation options subpart */
			$translationOptionsSubpart = $this->cObj->getSubpart($content, "###TRANSLATION_OPTIONS###");
			
			$translationMarkers = array();
			$translationMarkers['translation_label'] = $translation[0];
			$translationMarkers['translation_value'] = $translation[1];
			
			/* Mark the current translation as selected */
			if($translation[1] == $defaultTranslation) {
				$translationMarkers['translation_selected'] = 'selected="selected"';
			} else {
				$translationMarkers['translation_selected'] = '';
			}
			
			/* Replace the markers */
				$translationOptionsOutput .= $this->cObj->substituteMarkerArray($translationOptionsSubpart, $translationMarkers, $wrap='###|###',$uppercase=1);
		}
		
		/* Replace subpart and merge markers. */
		$content = $this->cObj->substituteSubpart($content, '###TRANSLATION_OPTIONS###', $translationOptionsOutput);
		$content = $this->cObj->substituteMarkerArray($content, $markers, $wrap='###|###',$uppercase=1);
		
		return $this->pi_wrapInBaseClass($content);
	}
	
	
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wec_ebible/pi2/class.tx_wecebible_pi2.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wec_ebible/pi2/class.tx_wecebible_pi2.php']);
}

?>