<?php

/***************************************************************
*  Copyright notice
*
*  (c) 2009 Jeff Segars <jeff@webempoweredchurch.org>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Hook class for adding info when frontend editing is active.
 *
 * @author	Jeff Segars <jeff@webempoweredchurch.org>
 * @package TYPO3
 * @subpackage feeditadvanced
 */

class tx_templavoila_renderElement {

	/**
	 * Post process the flexform data and values to include hidden fields with flexform pointers, etc.
	 *
	 * @param	array	The data structure.
	 * @param	array	The data values.
	 * @param	array	The original data values.
	 * @param	array	Flexform data.
	 * @return	void
	 */
	public function renderElement_postProcessDataValues($dataStructure, &$dataValues, $originalDataValues, $flexformData) {
		if (is_object($GLOBALS['BE_USER']) && $GLOBALS['BE_USER']->isFrontendEditingActive()) {
				// Calculate flexformPointers. Can we do this via API instead?.
			foreach ($dataValues as $key => &$value) {
				$flexformPointer = array();
				$flexformPointer['table'] = $flexformData['table'];
				$flexformPointer['uid']   = $flexformData['row']['uid'];
				$flexformPointer['sheet'] = $flexformData['sheet'];
				$flexformPointer['sLang'] = $flexformData['sLang'];
				$flexformPointer['field'] = $key;
				$flexformPointer['vLang'] = $flexformData['vLang'];

					// Add a hidden field at the end of each container that provides destination pointer and ID, 
					// but only to elements that are not attributes.
				if ((!isset($dataStructure['ROOT']['el'][$key]['type']) || $dataStructure['ROOT']['el'][$key]['type'] != 'attr') && $dataStructure['ROOT']['el'][$key]['tx_templavoila']['eType'] == 'ce') {
					$vKey = $flexformData['vLang'];
					$value[$vKey] .=  '<input type="hidden" class="feEditAdvanced-flexformPointers" title="' . implode(':', $flexformPointer) . '" value="' . $originalDataValues[$key][$vKey] . '" />';
				
						// Add some content to identify the container at the very beginning
					$value[$vKey] = '<div class="feEditAdvanced-firstWrapper" id="feEditAdvanced-firstWrapper-field-' . $flexformPointer['field'] . '-pages-' . $GLOBALS['TSFE']->id . '"></div>' . $value[$vKey];
				}
			}
		}
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feeditadvanced/templavoila/class.tx_templavoila_renderelement.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feeditadvanced/templavoila/class.tx_templavoila_renderelement.php']);
}

?>