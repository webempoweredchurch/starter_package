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

/**
 * Main API class for eBible scripture parsing.
 *
 * @author	Web-Empowered Church Team <ebible@webempoweredchurch.org>
 * @package TYPO3
 * @subpackage tx_wecebible
 */
class tx_wecebible_api {

	const VERSELINK_URL = 'http://ebible.com/assets/verselink/ebible.verselink.js';

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

		$argString = implode("&", $argArray);

		/**
		 * @fixme This is a workaround for multiple issues. Hopefully one of these wll be solved in the future.
		 * 1. VerseLink and MooTools both define window.addEvent and conflict. In light of this, we're better off including
		 *    VerseLink first and letting it be overwritten by MooTools. This means Verselink will not work on a page with
		 *    MooTools but MooTools will continue to work.
		 * 2. The TYPO3 page renderer doesn't support USER_INT plugins, so we can't rely on addJsFooter().
		 * 3. TSFE->additionalFooterData doesn't support USER_INT either, so we're stuck adding it in the header.
		 *
		 * As a result of these issues our best bet is to unshift the VerseLink JS onto the front of TSFE->additionalHeaderData
		 * so that it included as early in the header as possible and is overwritten by conflicting libraries.
		 */
		array_unshift($GLOBALS['TSFE']->additionalHeaderData, '<script type="text/javascript" src="' . self::VERSELINK_URL . '?' . $argString . '"></script>');
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