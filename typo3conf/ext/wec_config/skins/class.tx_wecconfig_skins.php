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



require_once(PATH_t3lib.'class.t3lib_extobjbase.php');
require_once(PATH_t3lib.'class.t3lib_tcemain.php');
require_once(t3lib_extMgm::extPath('templavoila_framework') . 'class.tx_templavoilaframework_lib.php');


/**
* Module extension (addition to function menu) 'Submodule in Info' for the 'wec_config' extension.
*
* @author Web-Empowered Church <developer@webempoweredchurch.org>
* @package TYPO3
* @subpackage tx_wecconfig
*/
class tx_wecconfig_skins extends t3lib_extobjbase {

	/**
	 * Returns the module menu
	 *
	 * @return Array with menuitems
	 */
	function modMenu() {
		return array();
	}

	/**
	 * Main method of the module
	 *
	 * @return HTML
	 */
	function main() {
		$data = t3lib_div::_GP('data');
		if (isset($data['skin_selector'])) {
			tx_wecconfig_skins::assignTemplate($this->pObj->id);
		}

		if ($this->allowSkinCopy() && t3lib_div::_GP('copySkin')) {
			tx_wecconfig_skins::copyTemplate();
		}

		$theOutput .= $this->pObj->doc->spacer(5);
		$theOutput .= $this->pObj->doc->section('', $this->renderSkinSelector(), 0, 1);
		$theOutput .= $this->pObj->doc->spacer(5);

		return $theOutput;
	}

	/**
	 * Renders the template selector.
	 *
	 * @return string  HTML output containing a table with the skin selector.
	 */
	function renderSkinSelector () {
		$positionPid = $this->pObj->id;
		$page = t3lib_beFunc::getRecord('pages', $positionPid);
		$currentSkin =  tx_templavoilaframework_lib::getCurrentSkin($positionPid);
		$customSkins = tx_templavoilaframework_lib::getCustomSkinKeys();
		$standardSkins = tx_templavoilaframework_lib::getStandardSkinKeys();

		$tmplHTML = array();
		if (!count($customSkins) && !count($standardSkins)) {
			$title = $GLOBALS['LANG']->getLL('noSkinsTitle');
			$message = sprintf($GLOBALS['LANG']->getLL('noSkinsMessage'), tx_templavoilaframework_lib::getCustomSkinPath());
			$severity = t3lib_FlashMessage::WARNING;
			$flashMessage = t3lib_div::makeInstance('t3lib_FlashMessage', $message, $title, $severity);
			$tmplHTML[] = $flashMessage->render();
		} else {
			if ($currentSkin) {
				$key = array_search($currentSkin, $customSkins);
				if($key !== FALSE) {
					unset($customSkins[$key]);
				}
				$key = array_search($currentSkin, $standardSkins);
				if ($key !== FALSE) {
					unset($standardSkins[$key]);
				}
			}

			$tmplHTML[] = '<h2>' . sprintf($GLOBALS['LANG']->getLL('currentSkin'), $this->pObj->pageRecord['title'] . '</h2>');

			$currentSkinHTML = self::drawSkinPreview($currentSkin, $isCurrent = TRUE);
			if($currentSkin && $currentSkinHTML) {
				$tmplHTML[] = '<div class="currentWrapper">';
				$tmplHTML[] = $currentSkinHTML;
				$tmplHTML[] = '</div>';
			} else {
				$tmplHTML[] = '<div class="noSkinSelected">';
				$tmplHTML[] = $GLOBALS['LANG']->getLL('noSkinSelected');
				$tmplHTML[] = '</div>';
			}

			if (count($customSkins) || count($standardSkins)) {
				$tmplHTML[] = '<hr style="clear:both;"/>';
			}

			$this->pObj->doc->inDocStylesArray['ul'] = 'ul { margin-left: 0px; padding-left: 0px; }';
			$this->pObj->doc->inDocStylesArray['li'] = 'li { list-style:none; padding: 8px 15px 8px 15px; clear:both; border-bottom: 2px solid '.$this->pObj->doc->bgColor4.'}';
			$this->pObj->doc->inDocStylesArray['input'] = 'img {margin:0px;padding:0px;}';
			$this->pObj->doc->inDocStylesArray['.iconWrapper'] = '.iconWrapper { display: inline-block; min-width: 130px; text-align: center; }';
			$this->pObj->doc->inDocStylesArray['.infoWrapper'] = '.infoWrapper { display: inline-block; margin-left: 5px; vertical-align: top; }';
			$this->pObj->doc->inDocStylesArray['.infoWrapper h2'] = '.infoWrapper h2 { background: none; }';
			$this->pObj->doc->inDocStylesArray['.buttonWrapper'] = '.buttonWrapper { margin-top: 10px; }';
			$this->pObj->doc->inDocStylesArray['.currentWrapper'] = '.currentWrapper { height: 70px; margin: 10px; padding: 10px; }';
			$this->pObj->doc->inDocStylesArray['.noSkinSelected'] = '.noSkinSelected { margin: 10px; padding: 10px; }';

			// If there are custom skins, show the section.
			if (count($customSkins)) {
				$tmplHTML[] = '<h3>' . $GLOBALS['LANG']->getLL('customSkins') . '</h3>';
				$tmplHTML[] = '<ul>';
				foreach ($customSkins as $skin) {
					if ($skin != $currentSkin) {
						$tmplHTML[] = '<li>';
						$tmplHTML[] = self::drawSkinPreview($skin);
						$tmplHTML[] = '</li>';
					}
				}
				$tmplHTML[] = '</ul>';
			}

			// If there are standard skins, show the section.
			if (count($standardSkins)) {
				$tmplHTML[] = '<h3>' . $GLOBALS['LANG']->getLL('standardSkins') . '</h3>';
				$tmplHTML[] = '<ul>';
				foreach ($standardSkins as $skin) {
					if ($skin != $currentSkin) {
						$tmplHTML[] = '<li>';
						$tmplHTML[] = self::drawSkinPreview($skin);
						$tmplHTML[] = '</li>';
					}
				}
				$tmplHTML[] = '</ul>';
			}

			// Add hidden form fields for skin selection and copying.
			$tmplHTML[] = '<input id="skinSelector" type="hidden" name="data[skin_selector]" value="' . $currentSkin . '" />';
			if ($this->allowSkinCopy()) {
				$tmplHTML[] = '<input id="copySkin" type="hidden" name="copySkin" value="0" />';
			}
		}

		return implode(chr(10), $tmplHTML);
	}

	/**
	 * Draws a preview of the skin, reading metadeta from within it.
	 *
	 * @param	string		$skin
	 * @param	boolean		$currentlySelected
	 * @return	string
	 */
	function drawSkinPreview($skin, $currentlySelected = FALSE) {
		require_once(t3lib_extMgm::extPath('templavoila_framework') . 'class.tx_templavoilaframework_lib.php');
		$skinInfo = tx_templavoilaframework_lib::getSkinInfo($skin);
		if ($skinInfo) {
			if ($skinInfo['icon']) {
				$previewIconFilename = $GLOBALS['BACK_PATH'] . $skinInfo['icon'];
			} else {
				$previewIconFilename = $GLOBALS['BACK_PATH'] . '../' . t3lib_extMgm::siteRelPath('templavoila_framework') . '/default_screenshot.jpg';
			}

			$html = array();

			$html[] = '<div class="iconWrapper"><img src="' . $previewIconFilename . '" /></div>';
			$html[] = '<div class="infoWrapper">';
			$html[] = 	'<h2>' . htmlspecialchars($skinInfo['title']) . '</h2>';
			if ($skinInfo['description']) {
				$html[] = '<p>' . $skinInfo['description'] . '</p>';
			}
			// For the current skin, tell the user what type it is and where its located.
			if ($currentlySelected) {
				switch($skinInfo['type']) {
					case 'EXT':
						$html[] = '<p>(' . $GLOBALS['LANG']->getLL('standardSkin') . ')</p>';
						break;
					case 'LOCAL':
						$html[] = '<div>(' . sprintf($GLOBALS['LANG']->getLL('customSkinPath'), $skinInfo['path']) . ')</div>';
						break;
				}
			}
			
			$html[] = '<div class="buttonWrapper">';
			if ($currentlySelected) {
				$html[] = '<input type="submit" value="' . $GLOBALS['LANG']->getLL('unselectSkinButton') . '" onclick="document.getElementById(\'skinSelector\').value = \'\';" /> ';
			} else {
				$html[] = '<input type="submit" value="' . $GLOBALS['LANG']->getLL('selectSkinButton') . '" onclick="document.getElementById(\'skinSelector\').value = \'' . $skin . '\';" /> ';
			}
			if ($this->allowSkinCopy()) {
				$html[] = '<input type="submit" value="' .  $GLOBALS['LANG']->getLL('copySkinButton') . '" onclick="document.getElementById(\'copySkin\').value = \'' . $skin . '\';" />';
			}
			$html[] = '</div>';
			$html[] = '</div>';

			return implode(chr(10), $html);
		} else {
			return FALSE;
		}
	}

	/**
	 * Gets the current Typoscript template.
	 *
	 * @return array
	 */
	public function getCurrentTemplate() {
		$tmpl = t3lib_div::makeInstance("t3lib_tsparser_ext");
		$tmpl->tt_track = 0;
		$tmpl->init();

		$tmpl->ext_localGfxPrefix = t3lib_extMgm::extPath("wec_config");
		$tmpl->ext_localWebGfxPrefix = $GLOBALS["BACK_PATH"] . t3lib_extMgm::extRelPath("wec_config");
		$templateRow = $tmpl->ext_getFirstTemplate($this->pObj->id);

		return $templateRow;
	}

	/**
	 * Assigns a skin in the current Typoscript template
	 *
	 * @param integer $uid
	 * @return void
	 */
	protected function assignTemplate($uid) {
		// Check if the HTTP_REFERER is valid
		$refInfo = parse_url(t3lib_div::getIndpEnv('HTTP_REFERER'));
		$httpHost = t3lib_div::getIndpEnv('TYPO3_HOST_ONLY');
		if ($httpHost == $refInfo['host'] || t3lib_div::_GP('vC') == $GLOBALS['BE_USER']->veriCode() || $GLOBALS['TYPO3_CONF_VARS']['SYS']['doNotCheckReferer']) {
			$templateArray = t3lib_div::_GP('data');

			$currentTemplate = self::getCurrentTemplate();
			$dataArray = array();
			$dataArray['sys_template'][$currentTemplate['uid']] = $templateArray;

			$tce = t3lib_div::makeInstance('t3lib_TCEmain');
			$tce->stripslashes_values = 0;
			$tce->start($dataArray, array());
			$tce->process_datamap();
			$tce->clear_cacheCmd('all');
		}
	}

	/**
	 * Copys a template to the local path.
	 *
	 * @return void
	 */
	protected function copyTemplate() {
		$copyFrom = tx_templavoilaframework_lib::getSkinPath(t3lib_div::_GP('copySkin'));
		$copyTo = tx_templavoilaframework_lib::getCustomSkinPath();

		$filemounts['1'] = array(
			'name' => $copyTo,
			'path' => PATH_site . $copyTo
		);
		$filemounts['2'] = array(
			'name' => 'typo3conf/ext/',
			'path' => PATH_site . 'typo3conf/ext/'
		);

		$fileCommands = array(
			'copy' => array(
				array(
					'data' => PATH_site . $copyFrom,
					'target' => PATH_site . $copyTo,
					'altName' => 1
				)
			)
		);

		$fileHandler = t3lib_div::makeInstance('t3lib_extFileFunctions');
		$fileHandler->init($filemounts, $TYPO3_CONF_VARS['BE']['fileExtensions']);
		$fileHandler->init_actionPerms($GLOBALS['BE_USER']->getFileoperationPermissions());
		$fileHandler->start($fileCommands);

		$fileHandler->processData();
		$fileHandler->printLogErrorMessages();
	}

	/**
	 * Checks whether skin copying is allowed. This may eventually be a permissions
	 * check but is currently based on OS because TYPO3 file functions do not support
	 * recursive copies in Windows.
	 *
	 * @return boolean
	 */
	protected function allowSkinCopy() {
		if (TYPO3_OS !== 'WIN') {
			$allowSkinCopy = TRUE;
		} else {
			$allowSkinCopy = FALSE;
		}

		return $allowSkinCopy;
	}
}
 
 
 
if (defined('TYPO3_MODE') && $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/wec_config/skins/class.tx_wecconfig_skins.php']) {
	include_once($GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/wec_config/skins/class.tx_wecconfig_skins.php']);
}
 
?>
