<?php

/***************************************************************
* Copyright notice
*
* (c) 2006-2008 Christian Technology Ministries International Inc.
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
 
 
 
/**
* Module extension (addition to function menu) 'Submodule in Info' for the 'wec_config' extension.
*
* @author Web-Empowered Church <developer@webempoweredchurch.org>
* @package TYPO3
* @subpackage tx_wecconfig
*/
class tx_wecconfig_templates extends t3lib_extobjbase {
	 
	/**
	* Returns the module menu
	*
	* @return Array with menuitems
	*/
	function modMenu() {
		global $LANG;
		 
		return Array ();
	}
	 
	/**
	* Main method of the module
	*
	* @return HTML
	*/
	function main() {
		// Initializes the module. Done in this function because we may need to re-initialize if data is submitted!
		global $SOBE, $BE_USER, $LANG, $BACK_PATH, $TCA_DESCR, $TCA, $CLIENT, $TYPO3_CONF_VARS;
		 
		if (t3lib_div::_GP('data')) {
			tx_wecconfig_templates::assignTemplate($this->pObj->id);
		}
		 
		$theOutput .= $this->pObj->doc->spacer(5);
		$theOutput .= $this->pObj->doc->section('', $this->renderTemplateSelector(), 0, 1);			 
		$theOutput .= $this->pObj->doc->spacer(5);
		 
		return $theOutput;
	}
	 
	/**
	* Renders the template selector.
	*
	* @param integer  Position id. Can be positive and negative depending of where the new page is going: Negative always points to a position AFTER the page having the abs. value of the positionId. Positive numbers means to create as the first subpage to another page.
	* @param string  $templateType: The template type, 'tmplobj' or 't3d'
	* @return string  HTML output containing a table with the template selector
	*/
	function renderTemplateSelector () {
		global $LANG, $TYPO3_DB;
		 
		$positionPid = $this->pObj->id;
					 
		$page = t3lib_beFunc::getRecord('pages', $positionPid);
		$templateObjectID = $page['tx_templavoila_to'];
		$storageFolderPID = $page['storage_pid'];
		 
		$tmplHTML = array();
		 
		 
		$tTO = 'tx_templavoila_tmplobj';
		$tDS = 'tx_templavoila_datastructure';
		$res = $TYPO3_DB->exec_SELECTquery ("$tTO.*", $tTO, "$tTO.uid=$templateObjectID ".t3lib_befunc::deleteClause ($tTO).t3lib_BEfunc::versioningPlaceholderClause($tTO));
		 
		$row = $TYPO3_DB->sql_fetch_assoc($res);
		$current_to = $row['uid'];
		$current_ds = $row['datastructure'];
		
		$tmplHTML [] = '<h2>'.$LANG->getLL('current').'</h2>';
		
		if($current_to != 0 || $current_ds != 0) { 
			$tmplHTML[] = '<div style="height:70px; margin:10px; padding:10px;">';
			$tmplHTML[] = tx_wecconfig_templates::drawTemplatePreview($row);
			$tmplHTML[] = '</div>';
			$tmplHTML[] = '<hr style="clear:both;"/>';
			
			$tTO = 'tx_templavoila_tmplobj';
			$tDS = 'tx_templavoila_datastructure';
			$res = $TYPO3_DB->exec_SELECTquery (
			"$tTO.*",
				"$tTO LEFT JOIN $tDS ON $tTO.datastructure = $tDS.uid",
				"$tTO.pid=".intval($storageFolderPID)." AND $tDS.scope=1 AND $tTO.uid!=$current_to". t3lib_befunc::deleteClause ($tTO).t3lib_befunc::deleteClause ($tDS). t3lib_BEfunc::versioningPlaceholderClause($tTO).t3lib_BEfunc::versioningPlaceholderClause($tDS), '',
				"$tTO.title" );
			
		} else {
			$tmplHTML[] = '<div style="margin:10px; padding:10px;">';
			$tmplHTML[] = $LANG->getLL('notemplate');
			$tmplHTML[] = '</div>';
			$tmplHTML[] = '<hr style="clear:both;"/>';

			
			$tTO = 'tx_templavoila_tmplobj';
			$tDS = 'tx_templavoila_datastructure';
			$res = $TYPO3_DB->exec_SELECTquery (
			"$tTO.*",
				"$tTO LEFT JOIN $tDS ON $tTO.datastructure = $tDS.uid",
				"$tTO.pid=".intval($storageFolderPID)." AND $tDS.scope=1 ". t3lib_befunc::deleteClause ($tTO).t3lib_befunc::deleteClause ($tDS). t3lib_BEfunc::versioningPlaceholderClause($tTO).t3lib_BEfunc::versioningPlaceholderClause($tDS), '',
				"$tTO.title" );
		}
		 
		$tmplHTML[] = '<h2>'.$LANG->getLL('available').'</h2>';

		$this->pObj->doc->inDocStylesArray['ul'] = 'ul { margin-left: 0px; padding-left: 0px; }';
		$this->pObj->doc->inDocStylesArray['li'] = 'li { cursor:pointer; list-style:none; padding: 8px 15px 8px 15px; clear:both; height: 70px; border-bottom: 2px solid '.$this->pObj->doc->bgColor4.'}';
		$this->pObj->doc->inDocStylesArray['li:hover'] = 'li:hover { background-color:'.$this->pObj->doc->bgColor4.'; }';
		$this->pObj->doc->inDocStylesArray['li.compatible'] = 'li.compatible { background:'.$this->pObj->doc->bgColor.';}';
		$this->pObj->doc->inDocStylesArray['input'] = 'img {margin:0px;padding:0px;}';		
				 
		$tmplHTML[] = '<ul>';
		while (false !== ($row = $TYPO3_DB->sql_fetch_assoc($res))) {
			if ($row['datastructure'] == $current_ds) {
				$additionalText = '<p>'.$LANG->getLL('compatible').'</p>';
			} else {
				unset($additionalText);
			}
			$tmplHTML[] = '<li onclick="document.getElementById(\'data_tx_templavoila_to\').value='.$row['uid'].';document.form.submit();">';
			$tmplHTML[] = tx_wecconfig_templates::drawTemplatePreview($row, $additionalText);
			 
			$tmplHTML[] = '</li>';
		}
		$tmplHTML[] = '</ul>';
		 
		$tmplHTML[] = '<input type="hidden" id="data_tx_templavoila_to" name="data[tx_templavoila_to]" value="0" />';
		 
		$content = implode(chr(10), $tmplHTML);
		 
		return $content;
	}
	 
	function drawTemplatePreview($row, $additionalText = '') {
		global $LANG, $TYPO3_DB;
		 
		// Check if preview icon exists, otherwise use default icon:
		$tmpFilename = 'uploads/tx_templavoila/'.$row['previewicon'];
		$previewIconFilename = (@is_file(PATH_site.$tmpFilename)) ? ($GLOBALS['BACK_PATH'].'../'.$tmpFilename) :
		 ($GLOBALS['BACK_PATH'].'../'.t3lib_extMgm::siteRelPath('templavoila').'res1/default_previewicon.gif');
		 
		// Note: we cannot use value of image input element because MSIE replaces this value with mouse coordinates! Thus on click we set value to a hidden field. See http://bugs.typo3.org/view.php?id=3376
		$previewIcon = '<input style="float:left; margin-right: 10px;" type="image" class="c-inputButton" name="i' .$row['uid'] . '" src="'.$previewIconFilename.'" title="" />';
		 
		$html = $previewIcon.'<div style="display:inline-block; margin:5px;"><h2 style="background:none;">'.htmlspecialchars($row['title']).'</h2>'.$additionalText.'</div>';
		return $html;
	}
	 
	function assignTemplate($uid) {
		global $LANG, $BE_USER, $TYPO3_CONF_VARS;
		 
		// Check if the HTTP_REFERER is valid
		$refInfo = parse_url(t3lib_div::getIndpEnv('HTTP_REFERER'));
		$httpHost = t3lib_div::getIndpEnv('TYPO3_HOST_ONLY');
		if ($httpHost == $refInfo['host'] || t3lib_div::_GP('vC') == $BE_USER->veriCode() || $TYPO3_CONF_VARS['SYS']['doNotCheckReferer']) {
			 
			$pageArray = t3lib_div::_GP('data');
			 
			$dataArr = array();
			$dataArr['pages'][$uid] = $pageArray;
			 
			// If no data structure is set, try to find one by using the template object
			if ($dataArr['pages'][$uid]['tx_templavoila_to'] && !$dataArr['pages'][$uid]['tx_templavoila_ds']) {
				$templateObjectRow = t3lib_BEfunc::getRecordWSOL('tx_templavoila_tmplobj', $dataArr['pages'][$uid]['tx_templavoila_to'], 'uid,pid,datastructure');
				$dataArr['pages'][$uid]['tx_templavoila_ds'] = $templateObjectRow['datastructure'];
			}
			 
			$tce = t3lib_div::makeInstance('t3lib_TCEmain');
			 
			// set default TCA values specific for the user
			$TCAdefaultOverride = $GLOBALS['BE_USER']->getTSConfigProp('TCAdefaults');
			if (is_array($TCAdefaultOverride)) {
				$tce->setDefaultsFromUserTS($TCAdefaultOverride);
			}
			 
			$tce->stripslashes_values = 0;
			$tce->start($dataArr, array());
			$tce->process_datamap();
			 
			$tce->clear_cacheCmd('all');
			 
		}
	}
}
 
 
 
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wec_config/templates/class.tx_wecconfig_templates.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wec_config/templates/class.tx_wecconfig_templates.php']);
}
 
?>
