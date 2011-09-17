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
	
require_once (PATH_tslib . 'class.tslib_pibase.php');
require_once(t3lib_extMgm::extPath('wec_ebible').'class.tx_wecebible_domainmgr.php');

/**
 * Main API class for eBible scripture parsing.
 *
 * @author	Web-Empowered Church Team <ebible@webempoweredchurch.org>
 * @package TYPO3
 * @subpackage tx_wecebible
 */	
class tx_wecebible_api {
	
	/**
	 * Adds eBible scripture parsing in the frontend.
	 * @param	array		Content array.
	 * @param	array		Conf array.
	 * @return	none
	 *
	 */	
	function main($content,$conf) {

		// only load this plugin and parse if it's enabled
		if(!$conf['config.']['enableParsing']) return false;
	
		if($conf['config.']['useExternalCSS']) {
			$includeCSS = '<link href="' . t3lib_extMgm::siteRelPath('wec_ebible') .'res/styles.css" media="screen" rel="stylesheet" type="text/css" />';
			$includeCSS .= '<style type="text/css">.footnote { display: none; !important}</style>';
		}
	
		$argArray = array();
	
		/* Processs cObjects */
		$cObj = t3lib_div::makeInstance('tslib_cObj');			
		foreach($conf['url.'] as $key=>$type) {
			if($key[strlen($key)-1] != ".") {
				$value = $cObj->cObjGetSingle($type, $conf['url.'][$key.'.']);
				if($value) {
					$argArray[$key] = $key.'='.$value;
				}
			}
		}

		/* If the current user has selected a translation and override is allowed */
		$userTranslation = tx_wecebible_api::getDefaultTranslation();
		if(!empty($userTranslation) && $conf['config.']['allowUserTranslation']) {
			$argArray['translation'] = 'translation='.$userTranslation;
		}

		if(empty($argArray['key'])) {
			$domainmgr = t3lib_div::makeInstance('tx_wecebible_domainmgr');
			$key = $domainmgr->getKey();
			if(empty($key)) $key  = 'EBIBLE_DEMO';
			$argArray['key'] = 'key='.$key;
		}
	
		// if no api key is set, don't output any javascript
		if(empty($argArray['key']) || $argArray['key'] == 'key=') return null;
	
		// add version string to arg array
		$argArray['v'] = 'v=1.0';
	
		$argString = implode("&", $argArray);
	
		/* Include javascript header */
		$includeJavascript = '<script type="text/javascript" src="'.t3lib_extMgm::siteRelPath('wec_ebible').'res/ebibleicious.js?'.$argString.'"></script>';
		$GLOBALS['TSFE']->additionalHeaderData[] = $includeCSS.$includeJavascript;
	}
	
	/**
	 * Retrieves the default translation.  This may be from a frontend user
	 * record, session variable for an anonymous user, or a Typoscript or
	 * Flexform default.
	 *
	 * @param		boolean		Optional argument to define whether TS (conf)
	 *							should be used in picking the default.
	 *							Defaults to true.
	 * @return		string
	 */
	function getDefaultTranslation($includeConf=true) {
		$translation = '';
		
		/* If user translation is allowed, check preferences */		
		if(tx_wecebible_api::isUserTranslationAllowed()) {
			/* Check the frontend user for a preference */
			if($GLOBALS['TSFE']->fe_user->user['uid']) {
				$translation = $GLOBALS['TSFE']->fe_user->user['tx_wecebible_translation'];
			} else {
				/* Check the user session for a preference */
				$translation = $GLOBALS["TSFE"]->fe_user->getKey("ses","tx_wecebible_translation");		
			}
		} 
		
		/* If no preference is set, find the Typoscript default */
		if($translation == '' && $includeConf) {
			/* Look in Typoscript for default */
			$conf = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_wecebible_api.'];
			$translation = $conf['url.']['translation.']['value'];
		}
		
		
		return $translation;
	}
	
	/**
	 * Saves the default translation.  This may be saved to a frontend user
	 * record or a session variable.
	 *
	 * @param		string		Optional argument for the translation to save.
	 * 							If no translation is provided, GPvars will be
	 * 							checked.
	 * @return		none
	 */
	function saveDefaultTranslation($translation=null) {
		
		/* If no translation is provided, check GPvars and unset when we're done. */
		if(!isset($translation)) {
			$piVars = t3lib_div::_GP('tx_wecebible_api');
			$translation = strip_tags($piVars['translation']);
			unset($_POST['tx_wecebible_api']['translation']);
			unset($_GET['tx_wecebible_api']['translation']);
		}
		
		if($translation) {
			/* Save preference to frontend user record. */
			if($userID = $GLOBALS['TSFE']->fe_user->user['uid']) {
				/* Write to database */
				$fields = array('tx_wecebible_translation' => $translation);
				$GLOBALS['TYPO3_DB']->exec_UPDATEquery('fe_users','uid='.$userID,$fields);
			} else {
				/* Save preference to user session */
				$GLOBALS["TSFE"]->fe_user->setKey("ses","tx_wecebible_translation", $translation);
				$GLOBALS["TSFE"]->fe_user->sesData_change = true;
				$GLOBALS["TSFE"]->fe_user->storeSessionData();		
			}
		}
		
		
	}
	
	/**
	 * Checks Typoscript to determine whether user translation is allowed.
	 *
	 * @return		boolean
	 */
	function isUserTranslationAllowed() {
		$conf = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_wecebible_api.'];
		if($conf['config.']['allowUserTranslation']) {
			$userTranslationAllowed = true;
		} else {
			$userTranslationAllowed = false;
		}
		
		return $userTranslationAllowed;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wec_ebible/class.tx_wecebible_api.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wec_ebible/class.tx_wecebible_api.php']);
}

?>