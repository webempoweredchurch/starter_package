<?php
	/***************************************************************
	*  Copyright notice
	*
	*  (c) 2006 Web-Empowered Church <developer@webempoweredchurch.org>
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
	*
	*  This script is distributed in the hope that it will be useful,
	*  but WITHOUT ANY WARRANTY; without even the implied warranty of
	*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	*  GNU General Public License for more details.
	*
	*  This copyright notice MUST APPEAR in all copies of the script!
	***************************************************************/
	 
	 
	require_once(PATH_t3lib . 'class.t3lib_extobjbase.php');
	 
	require_once(PATH_t3lib . 'class.t3lib_page.php');
	require_once(PATH_t3lib . 'class.t3lib_tstemplate.php');
	require_once(PATH_t3lib . 'class.t3lib_tsparser_ext.php');
	 
	/**
	* Module extension (addition to function menu) 'Submodule in Info' for the 'wec_config' extension.
	*
	* @author Web-Empowered Church <developer@webempoweredchurch.org>
	* @package TYPO3
	* @subpackage tx_wecconfig
	*/
	class tx_wecconfig_constants extends t3lib_extobjbase {
		 
		/**
		* Returns the module menu
		*
		* @return Array with menuitems
		*/
		function modMenu() {
			global $LANG;
			 
			return Array ();
		}
		 
		 
		function initialize_editor($pageId, $template_uid = 0) {
			// Initializes the module. Done in this function because we may need to re-initialize if data is submitted!
			global $tmpl, $tplRow, $theConstants;

			$tmpl = t3lib_div::makeInstance('t3lib_tsparser_ext'); // Defined global here!

			// Loading module configuration:
			$this->modTSconfig = t3lib_BEfunc::getModTSconfig($this->pObj->id, 'mod.'.$this->pObj->MCONF['name'].'.tx_wecconfig_constants');

			/**
			 * Hook for manipulating the constants display after the TS partner and TS config have been initialized.
			 * This could be useful for overriding $this->modTSconfig with different default values or overwriting
			 * $GLOBALS['tmpl'] with different rendering for the constants editor (ie. tabs)
			 */
			if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wec_config']['afterInitializeTSParserAndTSConfig'])) {
				foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wec_config']['afterInitializeTSParserAndTSConfig'] as $userFunc) {
					$params = array();
					t3lib_div::callUserFunction($userFunc, $params, $this);
				}
			}

			if (isset($this->modTSconfig['properties']['enableRevertToDefault']) && $this->modTSconfig['properties']['enableRevertToDefault']) {
				$tmpl->ext_dontCheckIssetValues = 0;
			} else {
				$tmpl->ext_dontCheckIssetValues = 1;
			}
			$tmpl->tt_track = 0; // Do not log time-performance information
			$tmpl->init();
			 
			$tmpl->ext_localGfxPrefix = t3lib_extMgm::extPath('tstemplate_ceditor');
			$tmpl->ext_localWebGfxPrefix = $GLOBALS['BACK_PATH'].t3lib_extMgm::extRelPath('tstemplate_ceditor');
			 
			$tplRow = $tmpl->ext_getFirstTemplate($pageId, $template_uid); // Get the row of the first VISIBLE template of the page. whereclause like the frontend.
			if (is_array($tplRow)) {
				// IF there was a template...
				// Gets the rootLine
				$sys_page = t3lib_div::makeInstance('t3lib_pageSelect');
				$rootLine = $sys_page->getRootLine($pageId);
				$tmpl->runThroughTemplates($rootLine, $template_uid); // This generates the constants/config + hierarchy info for the template.
				$theConstants = $tmpl->generateConfig_constants(); // The editable constants are returned in an array.
				$tmpl->ext_categorizeEditableConstants($theConstants); // The returned constants are sorted in categories, that goes into the $tmpl->categories array
				$tmpl->ext_regObjectPositions($tplRow['constants']);
				// This array will contain key=[expanded constantname], value=linenumber in template. (after edit_divider, if any)
				return 1;
			}
		}
		 
		function main() {
			global $SOBE, $BE_USER, $LANG, $BACK_PATH, $TCA_DESCR, $TCA, $CLIENT, $TYPO3_CONF_VARS;
			global $tmpl, $tplRow, $theConstants;

			$page_uid = $this->pObj->id;

			$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['wec_config']);
			if ($confArr['constantCategory'] && $confArr['constantPrefix']) {
				$constantCategory = strtolower($confArr['constantCategory']);
				$constantPrefix = $confArr['constantPrefix'];
			} else {
				// Fallbacks if not custom category is defined.
				if (t3lib_extMgm::isLoaded('templavoila') && t3lib_extMgm::isLoaded('templavoila_framework')) {
					$constantCategory = strtolower('Site Constants');
					$constantPrefix = '';
				} else {
					$constantCategory = 'constants.wec';
					$constantPrefix = 'constants.wec.';
				}
			}
			
			// **************************
			// Main
			// **************************
			 
			// BUGBUG: Should we check if the uset may at all read and write template-records???
			$existTemplate = $this->initialize_editor($page_uid, $template_uid);
			// initialize
			 
			if ($existTemplate) {
				$saveId = $tplRow['_ORIG_uid'] ? $tplRow['_ORIG_uid'] :
				 $tplRow['uid'];


				if (isset($this->modTSconfig['properties']['showConstantName']) && ($this->modTSconfig['properties']['showConstantName'] == 0)) {
					$this->pObj->doc->inDocStylesArray[] = '.typo3-tstemplate-ceditor-constant .typo3-dimmed { display:none; }';
				}

				// Update template ?
				if (t3lib_div::_POST('submit') || (t3lib_div::testInt(t3lib_div::_POST('submit_x')) && t3lib_div::testInt(t3lib_div::_POST('submit_y')))) {
					require_once (PATH_t3lib.'class.t3lib_tcemain.php');
					$tmpl->changed = 0;
					$tmpl->ext_procesInput(t3lib_div::_POST(), $_FILES, $theConstants, $tplRow);
					 
					if ($tmpl->changed) {
						$postData = t3lib_div::_POST();
						if ($constantCategory == 'constants.wec') {
							$title = $postData['data'][$constantPrefix . 'siteName'];
						} elseif ($constantCategory == strtolower('Site Constants')){
							$title = $postData['data']['siteTitle'];
						}
						$uid = $this->pObj->id;
						
						if (tx_wecconfig_constants::updatePageTitle($uid, $title)) {
							$this->pObj->doc->JScodeArray['tx_wecconfig_refresh'] = 'top.content.nav_frame.refresh_nav()';
						}
						 
						// Set the data to be saved
						$recData = array();
						$recData['sys_template'][$saveId]['constants'] = implode($tmpl->raw, chr(10));
						 
						// Create new  tce-object
						$tce = t3lib_div::makeInstance('t3lib_TCEmain');
						$tce->stripslashes_values = 0;
						// Initialize
						$tce->start($recData, Array());
						// Saved the stuff
						$tce->process_datamap();
						// Clear the cache (note: currently only admin-users can clear the cache in tce_main.php)
						$tce->clear_cacheCmd('all');
						 
						// re-read the template ...
						$this->initialize_editor($page_uid, $template_uid);
					}
				}
				 
				// Output edit form
				$tmpl->ext_readDirResources($TYPO3_CONF_VARS['MODS']['web_ts']['onlineResourceDir']);
				$tmpl->ext_resourceDims();
				 
				// Resetting the menu (start). I wonder if this in any way is a violation of the menu-system. Haven't checked. But need to do it here, because the menu is dependent on the categories available.
				$this->pObj->MOD_MENU['constant_editor_cat'] = $tmpl->ext_getCategoryLabelArray();
				$this->pObj->MOD_SETTINGS = t3lib_BEfunc::getModuleData($this->pObj->MOD_MENU, t3lib_div::_GP('SET'), $this->pObj->MCONF['name']);

				$theOutput .= $this->pObj->doc->header($LANG->getLL('editSiteConstants') . ' ' . $this->pObj->pageRecord['title']);
				$theOutput .= $this->pObj->doc->spacer(5);
				 
				// Category and constant editor config:
				$tmpl->ext_getTSCE_config($constantCategory);
				$printFields = trim($tmpl->ext_printFields($theConstants, $constantCategory));
				if ($printFields) {
					$theOutput .= $this->pObj->doc->spacer(20);
					$theOutput .= $this->pObj->doc->section('', $printFields);
				}

				// Hook for adding content after Constants Editor
				if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wec_config']['postProcessConstantsContent'])) {
					foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['wec_config']['postProcessConstantsContent'] as $userFunc) {
						$hookParams = array(
							'content' => $theOutput,
							'constantCategory' => $constantCategory,
							'constantPrefix' => $constantPrefix,
							'constants' => $theConstants
						);
						$theOutput = t3lib_div::callUserFunction($userFunc, $hookParams, $this);
					}
				}

			} else {
				$theOutput = '<h2>'.$LANG->getLL('notemplate').'</h2>';
			}

			return $theOutput;
		}
		 
		function updatePageTitle($pageID, $title) {
			$page = t3lib_befunc::getRecord('pages', $pageID, 'title');
			 
			if ($page['title'] != $title && $title != '') {
				$dataArr = array();
				$dataArr['pages'][$pageID]['title'] = $title;
				$tce = t3lib_div::makeInstance('t3lib_TCEmain');
				 
				// set default TCA values specific for the user
				$TCAdefaultOverride = $GLOBALS['BE_USER']->getTSConfigProp('TCAdefaults');
				if (is_array($TCAdefaultOverride)) {
					$tce->setDefaultsFromUserTS($TCAdefaultOverride);
				}
				 
				$tce->stripslashes_values = 0;
				$tce->start($dataArr, array());
				$tce->process_datamap();
				 
				return true;
			} else {
				return false;
			}
		}
		 
	}
	 
	 
	 
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wec_config/constants/class.tx_wecconfig_constants.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wec_config/constants/class.tx_wecconfig_constants.php']);
	}
	 
?>
