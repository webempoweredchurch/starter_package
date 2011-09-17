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

/**
 * General purpose class for providing a list of translations.
 *
 * @author	Web-Empowered Church Team <ebible@webempoweredchurch.org>
 * @package TYPO3
 * @subpackage tx_wecebible
 */
class tx_wecebible_itemsProcFunc {
	
	/**
	 * Gets the items array of all available translations.
	 * @param	array		The current config array.
	 * @return	array
	 */
	function getBibleTranslations($config=null) {
		if(!isset($config)) {
			$config = array();
		}
		
		$config['items'][] = Array('English Standard Version', 'ESV');
		$config['items'][] = Array('Holman Christian Standard Bible', 'HCSB');
		$config['items'][] = Array('King James Version', 'KJV');
		$config['items'][] = Array('New American Standard Bible', 'NASB');
		$config['items'][] = Array('New Century Version', 'NCV');
		$config['items'][] = Array('New King James Version', 'NKJV');
		$config['items'][] = Array('New International Version', 'NIV');
		$config['items'][] = Array('The Message', 'MSG');
		$config['items'][] = Array('Today\'s New International Version', 'TNIV');
		$config['items'][] = Array('------------------------------', '--div--');
		$config['items'][] = Array('1909 Spanish Reina-Valera Antigua', 'SpaRV');
		$config['items'][] = Array('Italian Riveduta (1927)', 'ItalRV');
		
		return $config;
	}
	
	/**
	 * Draws a selection list for use in the Constants Editor.  Not currently
	 * used until supported by the TYPO3 core.
	 *
	 * @param	array	Array of parameters.  Contains the field name and value.
	 * @return	string
	 */
	function getSelectForConstants($params) {
		$arr = array();
		$fN = $params['fieldName'];
		$fV = $params['fieldValue'];
		
		$translations = tx_wecebible_itemsProcFunc::getBibleTranslations($arr);
		$translations = $translations['items'];
		
		$c = array();
		$c[] = '<select name="'. $fN .'">';
		foreach( $translations as $key => $item ) {
			if ($item[1] == $fV) {
				$c[] = '<option value="'. $item[1] .'" selected="selected">'.$item[0].'</option>';
			} else {
				$c[] = '<option value="'. $item[1] .'">'.$item[0].'</option>';
			}
		}
		$c[] = '</select>';
		return implode(chr(10), $c);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wec_ebible/class.tx_wecebible_itemsProcFunc.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wec_ebible/class.tx_wecebible_itemsProcFunc.php']);
}
?>