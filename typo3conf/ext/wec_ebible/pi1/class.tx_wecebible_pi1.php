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
require_once(t3lib_extMgm::extPath('wec_ebible').'class.tx_wecebible_api.php');

/**
 *
 * Plugin for eBible.com Verse of the Day
 * @author	Web-Empowered Church Team <ebible@webempoweredchurch.org>
 * @package TYPO3
 * @subpackage tx_wecebible
 */
class tx_wecebible_pi1 extends tslib_pibase {
	var $prefixId = 'tx_wecebible_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_wecebible_pi1.php';	// Path to this script relative to the extension dir.
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

		/* Check GPvars for a users translation preference and saves it */
		tx_wecebible_api::saveDefaultTranslation();

		/* Initialize the Flexform and pull the data into a new object */
		$this->pi_initPIflexform();
		$piFlexForm = $this->cObj->data['pi_flexform'];
		
		$allowUserTranslation = $this->pi_getFFvalue($piFlexForm, 'allowUserTranslation');
		empty($allowUserTranslation) ? $allowUserTranslation = $conf['allowUserTranslation']:null;

		$translation = $this->pi_getFFvalue($piFlexForm, 'translation');
		empty($translation) ? $translation = $conf['translation']:null;
		
		$userTranslation = tx_wecebible_api::getDefaultTranslation(false);
		
		if($allowUserTranslation && !empty($userTranslation)) {
			$translation = $userTranslation;
		}

				
		$content = '<script type="text/javascript" src="http://ebible.com/api/votd?source='.$translation.'"></script>';

		return $this->pi_wrapInBaseClass($content);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wec_ebible/pi1/class.tx_wecebible_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wec_ebible/pi1/class.tx_wecebible_pi1.php']);
}

?>