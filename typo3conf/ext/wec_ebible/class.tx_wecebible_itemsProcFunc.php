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

		$config['items'][] = array('--- English ---', '--div--');
		$config['items'][] = array('Amplified', 'AMP');
		$config['items'][] = array('ASV', 'ASV');
		$config['items'][] = array('ESV', 'ESV');
		$config['items'][] = array('KJV', 'KJV');
		$config['items'][] = array('The Message', 'MSG');
		$config['items'][] = array('NASB', 'NASB');
		$config['items'][] = array('YLT', 'YLT');

		$config['items'][] = array('--- Arabic ---', '--div--');
		$config['items'][] = array('Smith-Vandyke', 'Smith-Vandyke');

		$config['items'][] = array('--- Czech ---', '--div--');
		$config['items'][] = array('BKR', 'BKR');

		$config['items'][] = array('--- Chinese ---', '--div--');
		$config['items'][] = array('CNV (Simplified)', 'CNVS');
		$config['items'][] = array('CNV (Traditional)', 'CNVT');
		$config['items'][] = array('CUV (Simplified)', 'CUVS');
		$config['items'][] = array('CUV (Traditional)', 'CUVT');

		$config['items'][] = array('--- Danish ---', '--div--');
		$config['items'][] = array('Danish', 'Danish');

		$config['items'][] = array('--- Dutch ---', '--div--');
		$config['items'][] = array('Staten Vertaling', 'StatenVertaling');

		$config['items'][] = array('--- Esperanto ---', '--div--');
		$config['items'][] = array('Esperanto', 'Esperanto');

		$config['items'][] = array('--- Finnish ---', '--div--');
		$config['items'][] = array('Finnish 1776', 'Finnish'); //x
		$config['items'][] = array('Pyha Raamattu 1933', 'PyhaRaamattu1933');

		$config['items'][] = array('--- French ---', '--div--');
		$config['items'][] = array('Darby', 'Darby');
		$config['items'][] = array('LS 1910', 'LS 1910');

		$config['items'][] = array('--- German ---', '--div--');
		$config['items'][] = array('Elberfelder', 'Elberfelder');
		$config['items'][] = array('Elberfelder 1905', 'Elberfelder1905');
		$config['items'][] = array('Luther 1545', 'Luther1545');
		$config['items'][] = array('Luther 1912', 'Luther1912');
		$config['items'][] = array('Schlachter', 'Schlachter');

		$config['items'][] = array('--- Greek ---', '--div--');
		$config['items'][] = array('Modern Greek', 'ModernGreek');

		$config['items'][] = array('--- Hebrew ---', '--div--');
		$config['items'][] = array('Modern Hebrew', 'ModernHebrew');

		$config['items'][] = array('--- Italian ---', '--div--');
		$config['items'][] = array('Giovanni', 'Giovanni');
		$config['items'][] = array('Riveduta', 'Riveduta');

		$config['items'][] = array('--- Korean ---', '--div--');
		$config['items'][] = array('Korean', 'Korean');

		$config['items'][] = array('--- Lithuanian ---', '--div--');
		$config['items'][] = array('Lithuanian', 'Lithuanian');

		$config['items'][] = array('--- Portuguese ---', '--div--');
		$config['items'][] = array('Almeida', 'Almeida');

		$config['items'][] = array('--- Romanian ---', '--div--');
		$config['items'][] = array('Cornilescu', 'Cornilescu');

		$config['items'][] = array('--- Russian ---', '--div--');
		$config['items'][] = array('Synodal', 'Synodal');

		$config['items'][] = array('--- Spanish ---', '--div--');
		$config['items'][] = array('Reina-Valera 1909', 'RV1909');
		$config['items'][] = array('Sagradas', 'Sagradas');

		$config['items'][] = array('--- Tagalog ---', '--div--');
		$config['items'][] = array('Tagalog', 'Tagalog');

		$config['items'][] = array('--- Thai ---', '--div--');
		$config['items'][] = array('Thai', 'Thai');

		$config['items'][] = array('--- Vietnamese ---', '--div--');
		$config['items'][] = array('Vietnamese', 'Vietnamese');

		$config['items'][] = array('--- Xhosa ---', '--div--');
		$config['items'][] = array('Xhosa', 'Xhosa');

		// Workaround sr_feuser_register's issues with duplicate values and inability to build optgroups
		if (TYPO3_MODE == 'FE') {
			foreach ($config['items'] as &$item) {
				if ($item[1] == '--div--') {
					$item[1] = $item[0];
				}
			}
		}

		return $config;
	}

	/**
	 * Draws a selection list for use in the Constants Editor.  Not currently
	 * used until supported by the TYPO3 core.
	 *
	 * @param	array	Array of parameters.  Contains the field name and value.
	 * @param	object	Parent Object.
	 * @return	string
	 */
	function getSelectForConstants($params, $parentObject) {
		$arr = array();
		$fieldName = $params['fieldName'];
		$fieldValue = $params['fieldValue'];

		$translations = tx_wecebible_itemsProcFunc::getBibleTranslations($arr);
		$translations = $translations['items'];

		$content = array();
		$content[] = '<select name="'. $fieldName .'">';
		foreach( $translations as $key => $item ) {
			if ($item[1] == $fieldValue) {
				$content[] = '<option value="'. $item[1] .'" selected="selected">'.$item[0].'</option>';
			} else {
				$content[] = '<option value="'. $item[1] .'">'.$item[0].'</option>';
			}
		}
		$content[] = '</select>';
		return implode(chr(10), $content);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wec_ebible/class.tx_wecebible_itemsProcFunc.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wec_ebible/class.tx_wecebible_itemsProcFunc.php']);
}
?>