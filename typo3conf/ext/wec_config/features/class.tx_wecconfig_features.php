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
class tx_wecconfig_features extends t3lib_extobjbase {
	 
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
		 
		if (t3lib_div::_GP('action')) {
			if (t3lib_div::_GP('action') == 'enable') {
				tx_wecconfig_features::enableFeature(t3lib_div::_GP('feature_id'));
			}
			if (t3lib_div::_GP('action') == 'disable') {
				tx_wecconfig_features::disableFeature(t3lib_div::_GP('feature_id'));
			}
			$this->pObj->doc->JScodeArray['tx_wecconfig_refresh'] = 'top.content.nav_frame.refresh_nav()';
		}
		 
		$theOutput .= $this->pObj->doc->spacer(5);
		$theOutput .= $this->pObj->doc->section($LANG->getLL('title'), tx_wecconfig_features::renderFeatureList(), 0, 1);
		$theOutput .= $this->pObj->doc->spacer(5);
		 
		return $theOutput;
	}
	 
	function renderFeatureList() {
		global $TYPO3_DB, $LANG;
		
		$this->pObj->doc->JScodeArray['tx_wecconfig_features_enable'] = '
			function enableFeature(uid) {
				document.editForm.feature_id.value = uid ;
				document.editForm.action.value = "enable";
				document.editForm.submit() ;
			}';
			
		$this->pObj->doc->JScodeArray['tx_wecconfig_features_disable'] = ' 
			function disableFeature(uid) {
				document.editForm.feature_id.value = uid ;
				document.editForm.action.value = "disable";
				document.editForm.submit() ;
			}
		';
		
			$this->pObj->doc->inDocStylesArray['ul'] = 'ul { padding-left: 0px; margin: 0px; }';
			$this->pObj->doc->inDocStylesArray['li'] = 'li { list-style:none; margin-left:0px; padding:15px; clear:both; border-bottom: 2px solid '.$this->pObj->doc->bgColor4.'}';
		 
		$html[] = '<input type="hidden" id="feature_id" name="feature_id" />';
		$html[] = '<input type="hidden" id="action" name="action" />';
		 

		$html[] = $this->pObj->doc->section($LANG->getLL('installed'), $this->listFeatures(0), 1, 2);
		$html[] = $this->pObj->doc->section($LANG->getLL('uninstalled'), $this->listFeatures(1), 1, 2);
		 
		return implode(chr(10), $html);
	}
	
	function listFeatures($disabled) {
		global $TYPO3_DB, $LANG;
		
		$html = array();
		$html[] = '<ul>';
		 
		$table = 'tx_wecconfig_features';
		$res = $TYPO3_DB->exec_SELECTquery ('*', $table, 'disabled='.$disabled.' AND deleted=0', '', 'title' );			 
		//t3lib_befunc::deleteClause($table).t3lib_BEfunc::versioningPlaceholderClause($table)

		$count = 0;
		while ($feature = $TYPO3_DB->sql_fetch_assoc($res)) {
			$html[] = '<li>';
			 
			$html[] = '<h3>'.$feature['title'].'</h3>';
			$html[] = '<p>'.$feature['description'].'</p>';
			 
			$status = $feature['disabled'] ? 'disabled' : 'enabled';
			$html[] = '<p>'.$LANG->getLL('status').': '.$LANG->getLL($status).'</p><br/>';
			 
			if($feature['disabled']) {
				$html[] = '<input type="submit" onclick="enableFeature('.$feature['uid'].')" value="'.$LANG->getLL('install').'" />';
			} else {
				$html[] = '<input type="submit" onclick="disableFeature('.$feature['uid'].')" value="'.$LANG->getLL('uninstall').'" />';
			}
			
			$html[] = '</li>';
			$count++;
		}
		if($count==0) {
			$html[] = '<li style="font-style:italic;">None</li>';
		}
		 
		$html[] = '</ul>';
		
		return implode(chr(10), $html);
	}
	 
	function disableFeature($uid) {
		tx_wecconfig_features::updateFeature($uid, 1);
	}
	 
	function enableFeature($uid) {
		tx_wecconfig_features::updateFeature($uid, 0);
	}
	 
	function updateFeature($uid, $disableValue) {
		$row = t3lib_BEfunc::getRecord('tx_wecconfig_features', $uid);
		 
		$elements = explode(',', $row['elements']);
		$finalArray = array();
		 
		/* Update each element that is part of the feature */
		foreach($elements as $element) {
			/* Split table and ID */
			$element = tx_wecconfig_features::splitTableAndUID($element);
			 
			/* Find hide field */
			t3lib_div::loadTCA($element['table']);
			$hidden_field = $GLOBALS['TCA'][$element['table']]['ctrl']['enablecolumns']['disabled'];
			 
			$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery($element['table'], "uid=".$element['uid'], array($hidden_field => $disableValue));
		}
		 
		/* Update the feature */
		$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_wecconfig_features', "uid=".$uid, array('disabled' => $disableValue));
		
		/* Clear the cache */
		$tce = t3lib_div::makeInstance('t3lib_TCEmain');
		$tce = t3lib_div::makeInstance('t3lib_TCEmain');
		$tce->start(array(), array());
		$tce->clear_cacheCmd('all');
	}
	
	/* Returns a count of the number of features in the entire TYPO3 install.
	 * @return		Number of features.
	 */
	function countFeatures() {
		global $TYPO3_DB;
		if(is_object($TYPO3_DB) && $TYPO3_DB->link) {
			$table = 'tx_wecconfig_features';
			$res = $TYPO3_DB->exec_SELECTquery ('count(uid)', $table, 'deleted=0');
			$row = $TYPO3_DB->sql_fetch_assoc($res);

			$count = $row['count(uid)'];
		} else {
			$count = 0;
		}
		return $count;
	}
	 
	/*
	* Splits a table name and UID in a string
	* @param string  Record and UID string.
	* @return array  Associative array with table and uid split.
	*/
	function splitTableAndUID($record) {
		$break = strrpos($record, '_');
		$uid = substr($record, $break+1);
		$table = substr($record, 0, $break);
		 
		return array('table' => $table, 'uid' => $uid);
	}
}
 
 
 
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wec_config/features/class.tx_wecconfig_features.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wec_config/features/class.tx_wecconfig_features.php']);
}
 
?>
