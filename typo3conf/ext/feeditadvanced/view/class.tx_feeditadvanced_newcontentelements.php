<?php
/***************************************************************
*  Copyright notice
*
*  (c) 1999-2009 Kasper Skaarhoj (kasperYYYY@typo3.com)
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
 * Gets a list of all content elements that are usually shown in the new content element wizard in the backend
 * but is returned in an array so Frontend Editing can render the icons and buttons inthe menu.
 * see tx_feeditadvanced_adminpanel for more.
 *
 * @package TYPO3
 * @subpackage feeditadvanced
 */
class tx_feeditadvanced_newcontentelements {

		// Internal, static (from GPvars):
	var $id;					// Page id
	var $sys_language=0;		// Sys language
	var $R_URI='';				// Return URL.
	var $colPos;				// If set, the content is destined for a specific column.
	var $uid_pid;				//

		// Internal, static:
	var $modTSconfig=array();	// Module TSconfig.

	/**
	 * Internal backend template object
	 *
	 * @var mediumDoc
	 */
	var $doc;

		// Internal, dynamic:
	var $include_once = array();	// Includes a list of files to include between init() and main() - see init()
	var $content;					// Used to accumulate the content of the module.
	var $access;					// Access boolean.
	var $config;					// config of the wizard

    var $menuItems;

	/**
	 * Constructor, initializing internal variables.
	 *
	 * @return	void
	 */
	function init()	{


			// Setting class files to include:
		if (is_array($TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']))	{
			$this->include_once = array_merge($this->include_once,$GLOBALS['TBE_MODULES_EXT']['xMOD_db_new_content_el']['addElClasses']);
		}

			// Setting internal vars:
		$this->id = $GLOBALS['TSFE']->id;
		$this->sys_language = $GLOBALS['TSFE']->sys_language_uid;

		$this->modTSconfig = t3lib_BEfunc::getModTSconfig($this->id, 'mod.wizards.newContentElement');

		$config = t3lib_BEfunc::getPagesTSconfig($this->id);
		$this->config = $config['mod.']['wizards.']['newContentElement.'];

	   			// Getting the current page and receiving access information (used in main())
		$perms_clause = $GLOBALS['BE_USER']->getPagePermsClause(1);
		$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$perms_clause);
		$this->access = is_array($this->pageinfo) ? 1 : 0;
	}

	/**
	 * Creating the module output.
	 *
	 * @return	void
	 */
	function main()	{
	    $this->init();

	    $wizardItems = $this->getWizardItems();

			// Hook for manipulating wizardItems, wrapper, onClickEvent etc.
		if(is_array($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms']['db_new_content_el']['wizardItemsHook'])) {
			foreach($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms']['db_new_content_el']['wizardItemsHook'] as $classData) {
				$hookObject = &t3lib_div::getUserObj($classData);

				if(!($hookObject instanceof cms_newContentElementWizardsHook)) {
					throw new UnexpectedValueException('$hookObject must implement interface cms_newContentElementWizardItemsHook', 1227834741);
				}

				$hookObject->manipulateWizardItems($wizardItems, $this);
			}
		}


			// Traverse items for the wizard.
			// An item is either a header or an item rendered with a radio button and title/description and icon:
		$cc = $key = 0;
		$this->menuItems = array();
		foreach ($wizardItems as $k => $wInfo)	{
			if ($wInfo['header'])	{
				$this->menuItems[] = array(
						'label'   => htmlspecialchars($wInfo['header'])
				);
				$key = count($this->menuItems) - 1;
			} else {
					// Icon:
				$iInfo = @getimagesize($wInfo['icon']);
					// Finally, put it together in a container:
				$this->menuItems[$key]['ce'][] = $wInfo;
				$cc++;
			}
		}





	}


	/***************************
	 *
	 * OTHER FUNCTIONS:
	 *
	 ***************************/


	/**
	 * Returns the content of wizardArray() function...
	 *
	 * @return	array		Returns the content of wizardArray() function...
	 */
	function getWizardItems()	{
		return $this->wizardArray();
	}

	/**
	 * Returns the array of elements in the wizard display.
	 * For the plugin section there is support for adding elements there from a global variable.
	 *
	 * @return	array
	 */
	function wizardArray()	{
		if (is_array($this->config)) {
			$wizards = $this->config['wizardItems.'];
		}
		$appendWizards = $this->wizard_appendWizards($wizards['elements.']);

		$wizardItems = array();

		if (is_array($wizards)) {
			foreach ($wizards as $groupKey => $wizardGroup) {
				$groupKey = preg_replace('/\.$/', '', $groupKey);
				$showItems = t3lib_div::trimExplode(',', $wizardGroup['show'], true);
				$showAll = (strcmp($wizardGroup['show'], '*') ? false : true);
				$groupItems = array();

				if (is_array($appendWizards[$groupKey . '.']['elements.'])) {
					$wizardElements = array_merge((array) $wizardGroup['elements.'], $appendWizards[$groupKey . '.']['elements.']);
				} else {
					$wizardElements = $wizardGroup['elements.'];
				}

				if (is_array($wizardElements)) {
					foreach ($wizardElements as $itemKey => $itemConf) {
						$itemKey = preg_replace('/\.$/', '', $itemKey);
						if ($showAll || in_array($itemKey, $showItems)) {
							$tmpItem = $this->wizard_getItem($groupKey, $itemKey, $itemConf);
							if ($tmpItem) {
								$groupItems[$groupKey . '_' . $itemKey] = $tmpItem;
			}
		}
					}
				}
				if (count($groupItems)) {
					$wizardItems[$groupKey] = $this->wizard_getGroupHeader($groupKey, $wizardGroup);
					$wizardItems = array_merge($wizardItems, $groupItems);
				}
			}
		}

			// Remove elements where preset values are not allowed:
		$this->removeInvalidElements($wizardItems);

		return $wizardItems;
	}

	function wizard_appendWizards($wizardElements) {
		if (!is_array($wizardElements)) {
			$wizardElements = array();
		}
		if (is_array($GLOBALS['TBE_MODULES_EXT']['xMOD_db_new_content_el']['addElClasses'])) {
			foreach ($GLOBALS['TBE_MODULES_EXT']['xMOD_db_new_content_el']['addElClasses'] as $class => $path) {
				require_once($path);
				$modObj = t3lib_div::makeInstance($class);
				$wizardElements = $modObj->proc($wizardElements);
			}
		}
		$returnElements = array();
		foreach ($wizardElements as $key => $wizardItem) {
			preg_match('/^[a-zA-Z0-9]+_/', $key, $group);
			$wizardGroup =  $group[0] ? substr($group[0], 0, -1) . '.' : $key;
			$returnElements[$wizardGroup]['elements.'][substr($key, strlen($wizardGroup)) . '.'] = $wizardItem;
		}
		return $returnElements;
	}


	function wizard_getItem($groupKey, $itemKey, $itemConf) {
		$itemConf['title'] = $GLOBALS['LANG']->sL($itemConf['title']);
		$itemConf['description'] = $GLOBALS['LANG']->sL($itemConf['description']);
		$itemConf['icon'] = t3lib_iconWorks::skinImg('',$itemConf['icon'],'',1);
		$itemConf['tt_content_defValues'] = $itemConf['tt_content_defValues.'];
		unset($itemConf['tt_content_defValues.']);
		return $itemConf;
	}

	function wizard_getGroupHeader($groupKey, $wizardGroup) {
		return array(
			'header' => $GLOBALS['LANG']->sL($wizardGroup['header'])
		);
	}


	/**
	 * Checks the array for elements which might contain unallowed default values and will unset them!
	 * Looks for the "tt_content_defValues" key in each element and if found it will traverse that array as fieldname / value pairs and check. The values will be added to the "params" key of the array (which should probably be unset or empty by default).
	 *
	 * @param	array		Wizard items, passed by reference
	 * @return	void
	 */
	function removeInvalidElements(&$wizardItems)	{
		global $TCA;

			// Load full table definition:
		t3lib_div::loadTCA('tt_content');

			// Get TCEFORM from TSconfig of current page
		$row = array('pid' => $this->id);
		$TCEFORM_TSconfig = t3lib_BEfunc::getTCEFORM_TSconfig('tt_content', $row);
		$removeItems = t3lib_div::trimExplode(',', $TCEFORM_TSconfig['CType']['removeItems'], 1);
		$keepItems = t3lib_div::trimExplode(',', $TCEFORM_TSconfig['CType']['keepItems'], 1);

		$headersUsed = Array();
			// Traverse wizard items:
		foreach($wizardItems as $key => $cfg)	{

				// Exploding parameter string, if any (old style)
			if ($wizardItems[$key]['params'])	{
					// Explode GET vars recursively
				$tempGetVars = t3lib_div::explodeUrl2Array($wizardItems[$key]['params'],TRUE);
					// If tt_content values are set, merge them into the tt_content_defValues array, unset them from $tempGetVars and re-implode $tempGetVars into the param string (in case remaining parameters are around).
				if (is_array($tempGetVars['defVals']['tt_content']))	{
					$wizardItems[$key]['tt_content_defValues'] = array_merge(is_array($wizardItems[$key]['tt_content_defValues']) ? $wizardItems[$key]['tt_content_defValues'] : array(), $tempGetVars['defVals']['tt_content']);
					unset($tempGetVars['defVals']['tt_content']);
					$wizardItems[$key]['params'] = t3lib_div::implodeArrayForUrl('',$tempGetVars);
				}
			}

				// If tt_content_defValues are defined...:
			if (is_array($wizardItems[$key]['tt_content_defValues']))	{

					// Traverse field values:
				foreach($wizardItems[$key]['tt_content_defValues'] as $fN => $fV)	{
					if (is_array($TCA['tt_content']['columns'][$fN]))	{
							// Get information about if the field value is OK:
						$config = &$TCA['tt_content']['columns'][$fN]['config'];
						$authModeDeny = ($config['type']=='select' && $config['authMode'] && !$GLOBALS['BE_USER']->checkAuthMode('tt_content', $fN, $fV, $config['authMode']));
						$isNotInKeepItems = (count($keepItems) && !in_array($fV, $keepItems));

						if ($authModeDeny || ($fN=='CType' && in_array($fV,$removeItems)) || $isNotInKeepItems) {
								// Remove element all together:
							unset($wizardItems[$key]);
							break;
						} else {
								// Add the parameter:
							$wizardItems[$key]['params'].= '&defVals[tt_content][' . $fN . ']=' . rawurlencode($fV);
							$tmp = explode('_', $key);
							$headersUsed[$tmp[0]] = $tmp[0];
						}
					}
				}
			}
		}
			// remove headers without elements
		foreach ($wizardItems as $key => $cfg)	{
			$tmp = explode('_',$key);
			if ($tmp[0] && !$tmp[1] && !in_array($tmp[0], $headersUsed))	{
				unset($wizardItems[$key]);
			}
		}
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feeditadvanced/view/class.tx_feeditadvanced_newcontentelements.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feeditadvanced/view/class.tx_feeditadvanced_newcontentelements.php']);
}

?>