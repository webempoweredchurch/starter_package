<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 David Slayback <dave@webempoweredchurch.org>
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
 * @todo	What does this class do?
 *
 * @author	David Slayback <dave@webempoweredchurch.org>
 * @author	Jeff Segars <jeff@webempoweredchurch.org>
 * @package TYPO3
 * @subpackage feeditadvanced
 */
class tx_feeditadvanced_getMainFields_preProcess {
	
	/**
	 * @todo	What is this?
	 */
	protected $modTSconfig;
	
	
	/**
	 * @todo	Add documentation.
	 *
	 * @param		string
	 * @param		string
	 * @param		???
	 * @return		void
	 */
	public function getMainFields_preProcess($table, $row, $thisVar) {
		$this->modTSconfig = $thisVar->modTSconfig;
		$formOnPageConf = $GLOBALS['TSFE']->tmpl->setup[$table.'.']['stdWrap.']['editPanel.'];

			// these configurations concerns only 'EDITPANEL' objects - edit icons use default system + a funtion to reset some TCA, when used Forms on page editing mode		
		if (t3lib_div::_GP('TSFE_EDIT') && $GLOBALS['BE_USER']->uc['TSFE_adminConfig']['edit_editFormsOnPage']) {

				// disable unwanted palettes
			if ($table == 'tt_content' && isset($formOnPageConf['formsOnPage.']['disablePalettes'])) {
				$disablePalettes = t3lib_div::trimExplode(',',strtolower($formOnPageConf['formsOnPage.']['disablePalettes']), 1);
				if (isset($disablePalettes) && is_array($disablePalettes)) {
					$disablePalettes=array_flip($disablePalettes);
				}
				for ($i=1; $i<16; $i++) {
					if(isset($disablePalettes[$i])) {
						 $GLOBALS['TCA']['tt_content']['palettes'][$i] = NULL;
					}
				}
			}
			
				// reset palettes
			if ($table=='tt_content' && isset($formOnPageConf['formsOnPage.']['palettes.'])) {
				for ($i=1; $i<16; $i++) {
					if ($formOnPageConf['formsOnPage.']['palettes.'][$i . '.']['showitem']) {
						$GLOBALS['TCA']['tt_content']['palettes'][$i]['showitem'] = $formOnPageConf['formsOnPage.']['palettes.'][$i . '.']['showitem'];
					}
				}
			}
			
			// automatic change some wizard properties  - handled in own function and used in function function formsOnPageForm, when works also for edit icons	
			// disable possible generic options 
			if ($this->modTSconfig['formsOnPage.'][$table.'.']['disableGeneralOptions']) {
				$disableGeneralOptions = t3lib_div::trimExplode(',', strtolower($this->modTSconfig['formsOnPage.'][$table.'.']['disableGeneralOptions']), 1);
			} elseif(isset($formOnPageConf['formsOnPage.']['disableGeneralOptions'])) {
				$disableGeneralOptions=t3lib_div::trimExplode(',', strtolower($formOnPageConf['formsOnPage.']['disableGeneralOptions']), 1);
			}
			
			if (isset($disableGeneralOptions) && is_array($disableGeneralOptions)) {
				foreach($disableGeneralOptions as $disabledOption => $value) {
					if (($value == 'starttime') && isset($GLOBALS['TCA'][$table]["columns"]["starttime"])) {
						$GLOBALS['TCA'][$table]["columns"]["starttime"] = NULL;
					}
					if (($value == 'endtime') && isset($GLOBALS['TCA'][$table]["columns"]["endtime"])) {
						$GLOBALS['TCA'][$table]["columns"]["endtime"] = NULL;
					}
					if (($value == 'hidden') && isset($GLOBALS['TCA'][$table]["columns"]["hidden"])) {
						$GLOBALS['TCA'][$table]["columns"]["hidden"] = NULL;
					}
					if (($value == 'fe_group') && isset($GLOBALS['TCA'][$table]["columns"]["fe_group"])) {
						$GLOBALS['TCA'][$table]["columns"]["fe_group"] = NULL;
					}
				}
			}
				
			if(isset($GLOBALS['TCA'][$table]['types'])) {
				$typeNum = $thisVar->getRTypeNum($table,$row);
			}
			
			/* example configurations:
			// TS config for page/users/user groups			
			mod.FE_BE.formsOnPage.tt_content.textpic.0.showitem(	
			bodytext;;9;richtext:rte_transform[flag=rte_enabled|mode=ts_css];3-3-3,
				--palette--;LLL:EXT:cms/locallang_ttc.php:ALT.imgLinks;7,
				--palette--;LLL:EXT:cms/locallang_ttc.php:ALT.imgOptions;11,
				imagecaption;;5,
				altText;;;;1-1-1
			)
			// TS config for TS Templates
			tt_content.stdWrap.editPanel.formsOnPage.textpic.0.showitem(	
			bodytext;;9;richtext:rte_transform[flag=rte_enabled|mode=ts_css];3-3-3,
				--palette--;LLL:EXT:cms/locallang_ttc.php:ALT.imgLinks;7,
				--palette--;LLL:EXT:cms/locallang_ttc.php:ALT.imgOptions;11,
				imagecaption;;5,
				altText;;;;1-1-1
			)			
			*/	
				
			if ($this->modTSconfig['formsOnPage.'][$table.'.']) {
				if ($table == 'tt_content') {
					if (($row['CType'] == 'list') && $this->modTSconfig['formsOnPage.'][$table.'.'][$row['CType'].'.'][$row['list_type'].'.']['showitem']) {
						$GLOBALS['TCA'][$table]['types'][$typeNum]['showitem'] = $this->modTSconfig['formsOnPage.'][$table . '.'][$row['CType'] . '.'][$row['list_type'] . '.']['showitem'];
					} elseif ($this->modTSconfig['formsOnPage.'][$table . '.'][$row['CType'] . '.']['0.']['showitem']) {
						$GLOBALS['TCA'][$table]['types'][$typeNum]['showitem'] = $this->modTSconfig['formsOnPage.'][$table . '.'][$row['CType'] . '.']['0.']['showitem'];
					}
				} elseif (isset($GLOBALS['TCA'][$table]['types'][$typeNum])) {
					$GLOBALS['TCA'][$table]['types'][$typeNum]['showitem'] = $this->modTSconfig['formsOnPage.'][$typeNum . '.']['showitem'];
				}
			} elseif ($formOnPageConf['formsOnPage.']) {
				if ($table == 'tt_content') {
					if (($row['CType']=='list') && $formOnPageConf['formsOnPage.'][$row['CType'] . '.'][$row['list_type'] . '.']['showitem']) {
						$GLOBALS['TCA'][$table]['types'][$typeNum]['showitem'] = $formOnPageConf['formsOnPage.'][$row['CType'] . '.'][$row['list_type'] . '.']['showitem'];
					} elseif ($formOnPageConf['formsOnPage.'][$row['CType'] . '.']['0.']['showitem']) {
						$GLOBALS['TCA'][$table]['types'][$typeNum]['showitem'] = $formOnPageConf['formsOnPage.'][$row['CType'] . '.']['0.']['showitem'];
					}
				} elseif (isset($GLOBALS['TCA'][$table]['types'][$typeNum])) {
					$GLOBALS['TCA'][$table]['types'][$typeNum]['showitem'] = $formOnPageConf['formsOnPage.'][$typeNum . '.']['showitem'];
				}
			}
		}
		
		return;	
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feeditadvanced/view/class.tx_feeditadvanced_getMainFields_preProcess.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feeditadvanced/view/class.tx_feeditadvanced_getMainFields_preProcess.php']);
}

?>