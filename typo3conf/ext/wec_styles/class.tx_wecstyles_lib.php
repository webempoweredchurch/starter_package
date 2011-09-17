<?php

/***************************************************************
 * Copyright notice
 *
 * (c) 2010 Christian Technology Ministries International Inc.
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

/**
 * General purpose library for working with styles.
 *
 * @author Web-Empowered Church <developer@webempoweredchurch.org>
 * @package TYPO3
 * @subpackage tx_templavoilaframework
 */
class tx_wecstyles_lib {

	/**
	 * Includes plugin CSS using additionalHeaderData or the page renderer.
	 *
	 * @return void
	 */
	public static function includePluginCSS() {
		if (!isset($GLOBALS['TSFE']->pSetup['includeCSS.']['wec_styles']) &&
		    !isset($GLOBALS['TSFE']->additionalHeaderData['wec_styles'])) {
			$cssFile = t3lib_extMgm::siteRelPath('wec_styles') . 'css/pluginstyles.css';
			$GLOBALS['TSFE']->additionalHeaderData['wec_styles'] = '<link type="text/css" rel="stylesheet" href="' . $cssFile . '" />';
		}
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wec_styles/class.tx_wecstyles_lib.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wec_styles/class.tx_wecstyles_lib.php']);
}

?>
