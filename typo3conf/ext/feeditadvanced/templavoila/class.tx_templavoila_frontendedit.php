<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Jeff Segars (jeff@webempoweredchurch.org)
*  All rights reserved
*
*  This script is part of the Typo3 project. The Typo3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/


require_once(t3lib_extMgm::extPath('templavoila') . 'class.tx_templavoila_api.php');

class tx_templavoila_frontendedit extends tx_feeditadvanced_frontendedit {
	
	var $templaVoilaObj;
	var $templaVoilaObjTable;
	
	/**
	 * Initializes and saves configuration options and then refreshes TSFE
	 * with these new settings.
	 *
	 * @return	none
	 */
	public function initConfigOptions() {
		parent::initConfigOptions();
		$this->refreshTSFE();
	}
	
	/**
	 * Wrapper function for editAction in parent class.  Once edits are done,
	 * TSFE is refreshed to make sure that TV-specific data is updated.
	 *
	 * @return		none
	 */
	public function editAction() {
		parent::editAction();
		$this->refreshTSFE();
	}

	public function doMoveAfter($table, $uid) {
		if ($table == 'tt_content') {
			$sourcePointerString = $GLOBALS['BE_USER']->frontendEdit->TSFE_EDIT['flexformPointer'];
			$destinationPointerString = $GLOBALS['BE_USER']->frontendEdit->TSFE_EDIT['destinationPointer'];

			$sourcePointer = $this->flexform_getPointerFromString($sourcePointerString);
			$destinationPointer = $this->flexform_getPointerFromString($destinationPointerString);
			$templaVoilaObj = $this->getTemplaVoilaObj($sourcePointer['table']);
			$result = $templaVoilaObj->moveElement($sourcePointer, $destinationPointer);
		} else {
			parent::doMove($table, $uid);
		}
	}

	/**
	 *  Moves records when using TemplaVoila.
	 *
	 * @param		string		The name of the table that the record is within.
	 * @param		integer		The UID of the record to move.
	 * @param		string		The direction that the record should be moved ('up' or 'down')
	 * @param		object		The TCEMain object.
	 * @return		none
	 * @todo 		Jeff:  Need to get rid of this function or integrate it differently due to drag/drop.
	 */
	protected function move($table, $uid, $direction) {
		if ($table == 'tt_content') {
			$sourcePointerString = $GLOBALS['BE_USER']->frontendEdit->TSFE_EDIT['flexformPointer'];
			$sourcePointer = $this->flexform_getPointerFromString($sourcePointerString);

			$destinationPointerString = explode('/', $sourcePointerString, 2);
			$destinationPointer = $this->flexform_getPointerFromString($destinationPointerString[0]);
			if ($direction == 'up') {
				if ($destinationPointer['position'] > 1) {
					$destinationPointer['position'] = $destinationPointer['position'] - 2;
				}
			} else {
				$destinationPointer['position'] = $destinationPointer['position'] + 1;
			}

			$templaVoilaObj = $this->getTemplaVoilaObj($sourcePointer['table']);
			$result = $templaVoilaObj->moveElement($sourcePointer, $destinationPointer);
		} else {
			parent::doMove($table, $uid);
		}
	}	

	/**
	 * Pastes a record using TemplVoila.
	 *
	 * @return		none
	 * @todo 		Jeff: Completely untested!
	 */
	protected function doPaste() {
		// @todo 	Set table properly!
		$templaVoilaObj = $this->getTemplaVoilaObj();
		
		// @todo 	Need to figure how to actually pass the data in a standard way.
		$myPOST=t3lib_div::_POST(); 
		$sourcePointer = $this->flexform_getPointerFromString($myPOST['sourcePointer']);
		$destinationPointer = $this->flexform_getPointerFromString($myPOST['destinationPointer']);
			
		if(!t3lib_div::_GP('setCopyMode')) {
			$templaVoilaObj->moveElement_setElementReferences($sourcePointer, $destinationPointer);
		}
		elseif(intval(t3lib_div::_GP('setCopyMode')) == 1) {
			$templaVoilaObj->insertElement_setElementReferences($destinationPointer, $sourcePointer['uid']);
			$templaVoilaObj->copyElement($sourcePointer, $destinationPointer);
		} else {
			$templaVoilaObj->referenceElement($sourcePointer, $destinationPointer);
		}
	}

	/**
	 * Deletes a record.
	 *
	 * @return		none
	 */
	public function doDelete($table, $uid) {
		if ($table == 'tt_content') {
			$templaVoilaObj = $this->getTemplaVoilaObj();
			$sourcePointerString = $GLOBALS['BE_USER']->frontendEdit->TSFE_EDIT['flexformPointer'];
			$sourcePointer = $this->flexform_getPointerFromString($sourcePointerString);

				// Unlinking rather than deleting to be consistent with TemplaVoila's backend interface.
			$templaVoilaObj->deleteElement($sourcePointer);
		} else {
			parent::doDelete($table, $uid);
		}
	}

	/**
	 * @todo	We should really use native TV functions here.
	 */
	protected function flexform_getPointerFromString($flexformPointerString) {
			$tmpArr = explode ('/', $flexformPointerString);
			$locationString= $tmpArr[0];
			$targetCheckString = $tmpArr[1];
			$locationArr = explode (':', $locationString);
			$targetCheckArr = explode (':', $targetCheckString);

			if (count($locationString) == 2) {
				$flexformPointer = array (
					'table' => $locationArr[0],
					'uid' => $locationArr[1]
				);
			} else {
				$flexformPointer = array (
					'table' => $locationArr[0],
					'uid' => $locationArr[1],
					'sheet' => $locationArr[2],
					'sLang' => $locationArr[3],
					'field' => $locationArr[4],
					'vLang' => $locationArr[5],
					'position' => $locationArr[6],
					'targetCheckuid' => $targetCheckArr[1],
				);
			}

			return $flexformPointer;
	}

	/**
	 * Returns a string of files for JS includes for front-end editing
	 *
	 * @param		none
	 * @return		string
	 */
	public function getJavascriptIncludes() {
		// @todo move this to TV folder
		$incJS .= '<script type="text/javascript" src="' . t3lib_extMgm::siteRelPath('feeditadvanced') . 'templavoila/feEditTV.js"></script>';

		return $incJS;

	}
	
	/**
	 * Returns an associative array of keys and values that should be used as
	 * hidden form fields within an edit panel.
	 *
	 * @param		array		The data array for the edit panel.
	 * @return		array
	 */
	public function getHiddenFields2($dataArr) {
		$sourcePointerString = explode(':', $dataArr['flexformPointer']);

		// For the parent pointer, strip off the table and UID on the end.
		list($parentPointerString) = explode('/', $dataArr['flexformPointer']);

		$sourcePointerString = explode('/',$dataArr['flexformPointer']);
		$sourcePointerString = $sourcePointerString[0];

		return array (
			'flexformPointer' => $dataArr['flexformPointer'],
			'sourcePointer' => $sourcePointerString,
			'destinationPointer' => $parentPointerString,
			'setCopyMode' => t3lib_div::_GP('setCopyMode')
		);
		
	}

	/**
	 * Returns a TemplaVoila API object or retrieves an existing object if it exists.
	 *
	 * @param		string		The name of the table.
	 * @return		object
	 */
	protected function getTemplaVoilaObj($table='pages') {
		if(!is_object($this->templaVoilaObj) || $table != $this->templaVoilaObjTable) {
			$this->templaVoilaObj = t3lib_div::makeInstance('tx_templavoila_api', $table);
			$this->templaVoilaObjTable = $table;
		}
		
		return $this->templaVoilaObj;
	}

	/**
	 * Refreshes the TSFE to account for the fact that TV content elements are
	 * stored as part of the page record and are not completely standalone records.
	 *
	 * @return		none
	 */
	protected function refreshTSFE() {
		$GLOBALS['TSFE']->checkAlternativeIdMethods();
		$GLOBALS['TSFE']->clear_preview();
		$GLOBALS['TSFE']->determineId();
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feeditadvanced/templavoila/class.tx_templavoila_frontendedit.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feeditadvanced/templavoila/class.tx_templavoila_frontendedit.php']);
}

?>