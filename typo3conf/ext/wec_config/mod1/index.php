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

$LANG->includeLLFile('EXT:wec_config/mod1/locallang.xml');
require_once(PATH_t3lib . 'class.t3lib_scbase.php');
$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
require_once($GLOBALS['BACK_PATH'] . 'template.php');




/**
 * Module 'WEC Config' for the 'wec_config' extension.
 *
 * @author	Web-Empowered Church <developer@webempoweredchurch.org>
 * @package	TYPO3
 * @subpackage	tx_wecconfig
 */
class  tx_wecconfig_module1 extends t3lib_SCbase {
	var $pageinfo;
	var $pageRecord;

	/**
	 * Initializes the Module
	 * @return	void
	 */
	function init()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		parent::init();
	}

	/**
	 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
	 *
	 * @return	void
	 */
	function menuConfig()	{
		global $LANG;
		parent::menuConfig();
	}

	/**
	 * Main function of the module. Write the content to $this->content
	 * If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
	 *
	 * @return	[type]		...
	 */
	function main()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['wec_config']);

		$selectedPid = intval(t3lib_div::_GP('id'));
		$calculatedPid = $this->findRootPid($selectedPid);

		if ($calculatedPid) {
			$this->id = $calculatedPid;
		} else {
			if ($GLOBALS['BE_USER']->user['admin']) {
				// For admin users, get the first real page in the tree.
				$records = t3lib_befunc::getRecordsByField('pages', 'pid', 0, $where='', $groupBy='', $orderBy='', $limit=1);
				$this->id = intval($records[0]['uid']);
			} else {
				// For non-admin users, get the first webmount with a root pid.
				foreach ($GLOBALS['WEBMOUNTS'] as $webmountId)	{
					$calculatedPid = $this->findRootPid($webmountId);

					if($calculatedPid) {
						break;
					}
				}

				$this->id = ($calculatedPid) ? $calculatedPid : intval($GLOBALS['WEBMOUNTS'][0]);
			}
		}

		$this->pageRecord = t3lib_BEfunc::getRecord('pages', $this->id);

		// Access check!
		// The page will show only if there is a valid page and if this page may be viewed by the user
		$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
		$access = is_array($this->pageinfo) ? 1 : 0;
		if (($this->id && $access) || ($BE_USER->user['admin'] && !$this->id))	{
			// Draw the header.
			$this->doc = t3lib_div::makeInstance('template');
			$this->doc->loadJavascriptLib($GLOBALS['BACK_PATH'].'contrib/prototype/prototype.js');
			$this->doc->inDocStylesArray[] = 'body { padding: 0px; margin: 0px; }';
			$this->doc->backPath = $BACK_PATH;
			$this->doc->setModuleTemplate(t3lib_extMgm::extPath('wec_config') . 'mod1/template.html');
			$this->doc->docType = 'xhtml_trans';
			$this->doc->form='<form action="" id="editForm" name="editForm" method="POST" enctype="multipart/form-data">';

			// JavaScript
			$this->doc->JScode = '
				<script language="javascript" type="text/javascript">
					script_ended = 0;
					function jumpToUrl(URL)	{
						document.location = URL;
					}
				</script>
			';

			$this->doc->postCode='
				<script language="javascript" type="text/javascript">
					script_ended = 1;
					if (top.fsMod) top.fsMod.recentIds["web"] = 0;
				</script>
			';

			// Render content:

			$moduleContent = $this->moduleContent();

			//$markerArray['TITLE'] = $LANG->getLL('title');
			$markers = array();
			// Setting up the buttons and markers for docheader
			$docHeaderButtons = $this->getButtons();
			$markers = array(
				'CSH' => $docHeaderButtons['csh'],
				'CONTENT' => $moduleContent
			);

			// If the module menu has more than 1 item, show it.  Otherwise, skip since a menu with 1 item makes no sense.
			if (count($this->MOD_MENU['function']) > 1) {
				$markers['FUNC_MENU'] = t3lib_BEfunc::getFuncMenu($this->id, 'SET[function]', $this->MOD_SETTINGS['function'],$this->MOD_MENU['function']);
			} else {
				$markers['FUNC_MENU'] = '';
			}

			$this->content .= $this->doc->startPage('');
			$this->content .= $this->doc->moduleBody($this->pageinfo, $docHeaderButtons, $markers);
		} else {
				// If no access or if ID == zero

			$this->doc = t3lib_div::makeInstance('mediumDoc');
			$this->doc->backPath = $BACK_PATH;

			$this->content.=$this->doc->startPage($LANG->getLL('title'));
			$this->content.=$this->doc->header($LANG->getLL('title'));
			$this->content.=$this->doc->spacer(5);
			$this->content.=$this->doc->spacer(10);
		}
	}

	/**
	 * Prints out the module HTML
	 *
	 * @return	void
	 */
	function printContent()	{
		$this->content .= $this->doc->endPage();
		echo $this->content;
	}

	/**
	 * Generates the module content
	 *
	 * @return	void
	 */
	function moduleContent()	{
		$this->checkExtObj();
		//$this->extObjContent();

		// Add sys_template to the list of allowed tables.
		$originalModifyTables = $GLOBALS['BE_USER']->groupData['tables_modify'];
		$GLOBALS['BE_USER']->groupData['tables_modify'] .= ',sys_template';
		$originalExcludeFields = $GLOBALS['BE_USER']->groupData['non_exclude_fields'];
		$GLOBALS['BE_USER']->groupData['non_exclude_fields'] .= ',sys_template:skin_selector';
		$originalSysTemplateAdminOnly = $GLOBALS['TCA']['sys_template']['ctrl']['adminOnly'];
		$GLOBALS['TCA']['sys_template']['ctrl']['adminOnly'] = 0;

		$this->extObj->pObj = &$this;
		if (is_callable(array($this->extObj, 'main'))) {
			return $this->extObj->main();
		}

		// Restore original permissions
		$GLOBALS['BE_USER']->groupData['tables_modify'] = $originalModifyTables;
		$GLOBALS['TCA']['sys_template']['ctrl']['adminOnly'] = $originalSysTemplateAdminOnly;
		$GLOBALS['BE_USER']->groupData['non_exclude_fields'] = $originalExcludeFields;
	}

	/**
	 * Create the panel of buttons for submitting the form or otherwise perform operations.
	 *
	 * @return	array	all available buttons as an assoc. array
	 */
	function getButtons()	{
		global $TCA, $LANG, $BACK_PATH, $BE_USER;

		$buttons = array(
			'csh' => '',
			'save' => '',
			'view' => '',
			'record_list' => '',
			'shortcut' => '',
		);

		if(t3lib_div::int_from_ver(TYPO3_version) >= 4004000) {
			$viewButton = t3lib_iconWorks::getSpriteIcon('actions-document-view');
			$saveButton = t3lib_iconWorks::getSpriteIcon('actions-document-save', array(
								'html' => '<input type="image" value = "Update" name="submit" class="c-inputButton" src="clear.gif" title="' . $LANG->sL('LLL:EXT:lang/locallang_core.php:rm.saveDoc', 1) . '" />'
								));
			$listButton = t3lib_iconWorks::getSpriteIcon('actions-system-list-open');

		} else {
			$viewButton = '<img' . t3lib_iconWorks::skinImg($BACK_PATH, 'gfx/zoom.gif') . '/>';
			$saveButton = '<input type="image" class="c-inputButton" name="submit" value="Update"' . t3lib_iconWorks::skinImg($BACK_PATH, 'gfx/savedok.gif','') . ' title="' . $LANG->sL('LLL:EXT:lang/locallang_core.php:rm.saveDoc', 1) . '" />';
			$listButton = '<img' . t3lib_iconWorks::skinImg($BACK_PATH, 'gfx/list.gif') . ' />';
		}

		$buttons['view'] = '<a href="#" onclick="' . htmlspecialchars(t3lib_BEfunc::viewOnClick($this->pageinfo['uid'], $BACK_PATH, t3lib_BEfunc::BEgetRootLine($this->pageinfo['uid']))) . '"  title="' . $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.php:labels.showPage', 1) . '">' .
				$viewButton .
				'</a>';
		switch($this->extClassConf['name']) {
			case 'tx_wecconfig_constants':
				$buttons['save'] = $saveButton;
				$buttons['csh'] = t3lib_BEfunc::cshItem('_MOD_web_wecconfig_constants', '', $GLOBALS['BACK_PATH']);
				break;
			case 'tx_wecconfig_features':
				$buttons['csh'] = t3lib_BEfunc::cshItem('_MOD_web_wecconfig_features', '', $GLOBALS['BACK_PATH']);
				break;
			case 'tx_wecconfig_templates':
				$buttons['csh'] = t3lib_BEfunc::cshItem('_MOD_web_wecconfig_templates', '', $GLOBALS['BACK_PATH']);
				break;
			default:
				$buttons['csh'] = t3lib_BEfunc::cshItem('_MOD_web_wecconfig', '', $GLOBALS['BACK_PATH']);
				break;
		}

			// Shortcut
		if ($BE_USER->mayMakeShortcut())	{
			//$buttons['shortcut'] = $this->doc->makeShortcutIcon('id, edit_record, pointer, new_unique_uid, search_field, search_levels, showLimit', implode(',', array_keys($this->MOD_MENU)), $this->MCONF['name']);
		}

			// If access to Web>List for user, then link to that module.
		if ($BE_USER->check('modules','web_list'))	{
			$href = $BACK_PATH . 'db_list.php?id=' . $this->pageinfo['uid'] . '&returnUrl=' . rawurlencode(t3lib_div::getIndpEnv('REQUEST_URI'));
			$buttons['record_list'] = '<a href="' . htmlspecialchars($href) . '" title="' . $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.php:labels.showList', 1) . '" alt="">' .
					$listButton .
					'</a>';
		}

		return $buttons;
	}

	/**
	 * Starts at the specified page ID and walks up the tree to find the nearest root page id.
	 * This allows other WEC config modules to work relative to the appropriate root page.
	 *
	 * @param	integer		$pageId
	 * @return	integer
	 */
	protected function findRootPid($pageID) {
		$tsTemplate = t3lib_div::makeInstance("t3lib_tsparser_ext");
		$tsTemplate->tt_track = 0;
		$tsTemplate->init();

		// Gets the rootLine
		$sys_page = t3lib_div::makeInstance("t3lib_pageSelect");
		$rootLine = $sys_page->getRootLine($pageID);
		$tsTemplate->runThroughTemplates($rootLine);
		$rootPid = $tsTemplate->rootId;

		return $rootPid;
	}

}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wec_config/mod1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wec_config/mod1/index.php']);
}




// Make instance:
$SOBE = t3lib_div::makeInstance('tx_wecconfig_module1');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE) {
	include_once($INC_FILE);
}

$SOBE->main();
$SOBE->printContent();

?>
