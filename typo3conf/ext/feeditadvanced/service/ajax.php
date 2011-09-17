<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 David Slayback <dave@webempoweredchurch.org>
*  (c) 2008 Jeff Segars <jeff@webempoweredchurch.org>
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
 * AJAX controller class for frontend editing.
 *
 * @author	David Slayback <dave@webempoweredchurch.org>
 * @author	Jeff Segars <jeff@webempoweredchurch.org>
 * @package TYPO3
 * @subpackage feeditadvanced
 */
class tx_feeditadvanced_ajax {

	/**
	 * The page ID for editing.
	 *
	 * @var	integer
	 */
	protected $pid;

	/**
	 * The editing command.
	 *
	 * @var	string
	 */ 
	protected $cmd;

	/**
	 * The AJAX object.
	 *
	 * @var	TYPO3AJAX
	 */
	protected $ajaxObj;

	/**
	 * The TSConfig properties for the current page.
	 *
	 * @var	array
	 */
	protected $modTSconfig;

	/**
	 * Constructor to initialize a frontend instance and a backend user.
	 *
	 * @return	void
	 */
	public function __construct() {
		// @todo Change this whole class to use typo3/ajax.php only
		$GLOBALS['TYPO3_AJAX'] = true;

		$this->pid = t3lib_div::_GP('pid');

		tslib_eidtools::connectDB();
		$this->initializeTSFE($this->pid);

			// Setup ajax object
		require_once(PATH_typo3.'classes/class.typo3ajax.php');
		$this->ajaxObj = t3lib_div::makeInstance('TYPO3AJAX', 'feeditadvanced');

		/**
		 * @todo This content format is only applicable for the login error.
		 *		 Need to refactor constructor and processAction to reduce duplicated
		 *		 code and make more use of HTTP_ACCEPT.
		 */
		if (stristr($_SERVER['HTTP_ACCEPT'], 'application/json')) {
			$this->ajaxObj->setContentFormat('jsonbody');
		} else {
			$this->ajaxObj->setContentFormat('javascript');
		}

		if ($this->isFrontendEditActive()) {
				// @todo	Is there a better way to force these values so that we're sure editAction gets called?
			$GLOBALS['BE_USER']->uc['TSFE_adminConfig']['display_edit'] = true;
			$GLOBALS['BE_USER']->extAdminConfig['enable.']['edit'] = true;
			$GLOBALS['TSFE']->displayEditIcons = true;

			$GLOBALS['BE_USER']->frontendEdit->initConfigOptions();
		} else {
				// @todo	Should we send a full error message here or what?  How does the user relogin?
			$this->ajaxObj->addContent('error', 'No login found.');
			$this->ajaxObj->render();
		}
	}
	
	/**
	 * Checks whether frontend editing is active.
	 *
	 * @return		boolean
	 * @todo		Should this be moved to the core?  We do the same check in several other places.
	 */
	public function isFrontendEditActive() {
		return $GLOBALS['TSFE']->beUserLogin && ($GLOBALS['BE_USER']->frontendEdit instanceof t3lib_frontendedit);
	}

	/**
	 * Main function for handling AJAX-defined actions.
	 *
	 * @return		void
	 */
	public function processAction() {
		if ($this->isFrontendEditActive()) {

				// Setup ajax object
			require_once(PATH_typo3.'classes/class.typo3ajax.php');
			$this->ajaxObj = t3lib_div::makeInstance('TYPO3AJAX', 'feeditadvanced');

				// Get TSConfig options
			$this->modTSconfig = t3lib_BEfunc::getModTSconfig($GLOBALS['TSFE']->id, 'FeEdit');

			$cmd = $GLOBALS['BE_USER']->frontendEdit->TSFE_EDIT['cmd'];
				// Map values from TCEForms submission to editing actions.
			if (!$cmd) {
				if ($GLOBALS['BE_USER']->frontendEdit->TSFE_EDIT['update']) {
					$cmd = 'edit';
				} elseif ($GLOBALS['BE_USER']->frontendEdit->TSFE_EDIT['update_close']) {
					$cmd = 'saveAndClose';
				} elseif ($GLOBALS['BE_USER']->frontendEdit->TSFE_EDIT['close']) {
					$cmd = 'close';
				} elseif (is_array($GLOBALS['BE_USER']->frontendEdit->TSFE_EDIT['data'])) {
						// If there's a data array and no action, assume that we're already showing an editing form and want to show it again.
					$cmd = 'edit';
				}
				$GLOBALS['BE_USER']->frontendEdit->TSFE_EDIT['cmd'] = $cmd;
			}

			if (is_array($GLOBALS['BE_USER']->frontendEdit->TSFE_EDIT['data'])) {
				list($table) = array_keys($GLOBALS['BE_USER']->frontendEdit->TSFE_EDIT['data']);
				list($uid) = array_keys($GLOBALS['BE_USER']->frontendEdit->TSFE_EDIT['data'][$table]);
				
				if ($GLOBALS['BE_USER']->frontendEdit->TSFE_EDIT['newUID']) {
					$uid = $GLOBALS['BE_USER']->frontendEdit->TSFE_EDIT['newUID'];
				}
					// If we're dealing with a new record, get the UID of the previous sibling.
				if (($uid == 'NEW') && ($GLOBALS['BE_USER']->frontendEdit->TSFE_EDIT['data'][$table]['NEW']['pid'] < 0)) {
					$uid = abs($GLOBALS['BE_USER']->frontendEdit->TSFE_EDIT['data'][$table]['NEW']['pid']);
				}
				$GLOBALS['BE_USER']->frontendEdit->TSFE_EDIT['record'] = $table . ':' . $uid;
			}
			list($table, $uid) = explode(':', $GLOBALS['BE_USER']->frontendEdit->TSFE_EDIT['record']);
			$this->setAJAXContentFormat($cmd);
				// Call post processing function, if applicable.
			$cmdFunction = $cmd . 'Item';
			if ($cmd && method_exists($this, $cmdFunction)) {
				$this->$cmdFunction($table, $uid);
			}

			if ($cmd != 'edit' && $cmd !='new' && $cmd !='save') {
				$this->ajaxObj->addContent('cmd', $cmd);
				$this->ajaxObj->addContent('uid', $uid);
			}

				// Return output
			$this->ajaxObj->render();
		}
	}

	/**
	 * Sets the AJAX content format for the current action.
	 *
	 * @param	string	The action being performed.
	 * @return	void
	 */
	protected function setAJAXContentFormat($action) {
		switch ($action) {
			case 'new':
			case 'edit':
			case 'save':
				$format = 'plain';
				break;
			case 'saveAndClose':
			case 'close':
				$format = 'javascript';
				break;
			default:
				$format = 'jsonbody';
		}

		$this->ajaxObj->setContentFormat($format);
	}

	/**
	 * AJAX response to an edit action on a particular record.
	 *
	 * @param	string		Name of the table.
	 * @param	integer		UID of the record.
	 * @return	void
	 */
	protected function editItem($table, $uid) {
		$this->renderContentElement($table, $uid);
	}

	/**
	 * AJAX response to a create action on a particular record.
	 *
	 * @param	string		Name of the table.
	 * @param	integer		UID of the record.
	 * @return	void
	 */
	protected function newItem($table, $uid) {
		$this->renderContentElement($table, $uid);
	}
	
	/**
	 * AJAX response to a save and close action on a particular record.
	 *
	 * @param	string		Name of the table.
	 * @param	integer		UID of the record.
	 * @return	void
	 */
	protected function saveAndCloseItem($table, $uid) {
		$this->saveItem($table, $uid);

		if ($GLOBALS['BE_USER']->frontendEdit->TSFE_EDIT['parentEditPanel']) {
			list($table, $uid) = split(':', $GLOBALS['BE_USER']->frontendEdit->TSFE_EDIT['parentEditPanel']);
			$this->ajaxObj->addContent('id', $table . ':' . $uid);
		}

		if ($table == 'pages') {
			$this->ajaxObj->addContent('url', $this->getPageURL($uid));
		} elseif ($this->modTSconfig['properties']['reloadPageOnContentUpdate']) {
			$this->ajaxObj->addContent('url', $this->getPageURL($GLOBALS['TSFE']->id));
		} else {
			$this->renderContentElement($table, $uid);
		}
	}

	/**
	 * AJAX response to a save action on a particular record.
	 *
	 * @param	string		Name of the table.
	 * @param	integer		UID of the record.
	 * @return	void
	 */
	protected function saveItem($table, $uid) {
		$this->renderContentElement($table, $uid);
	}

	/**
	 * AJAX response to a close action on a particular record.
	 *
	 * @param	string		Name of the table.
	 * @param	integer		UID of the record.
	 * @return	void
	 */
	protected function closeItem($table ,$uid) {
		if ($GLOBALS['BE_USER']->frontendEdit->TSFE_EDIT['parentEditPanel']) {
			list($table, $uid) = split(':', $GLOBALS['BE_USER']->frontendEdit->TSFE_EDIT['parentEditPanel']);
			$this->ajaxObj->addContent('id', $table . ':' . $uid);
		}

		if ($table == 'pages') {
			$this->ajaxObj->addContent('url', $this->getPageURL($uid));
		} elseif ($this->modTSconfig['properties']['reloadPageOnContentUpdate']) {
			$this->ajaxObj->addContent('url', $this->getPageURL($GLOBALS['TSFE']->id));
		} else {
			$this->renderContentElement($table, $uid);
		}
	}

	/**
	 * AJAX response to a delete action on a particular record.
	 *
	 * @param	string		Name of the table.
	 * @param	integer		UID of the record.
	 * @return	void
	 */
	protected function deleteItem($table, $uid) {
		if ($table == 'pages') {
			$parentPageUID = $GLOBALS['TSFE']->page['pid'];
			if ($parentPageUID) {
 				$this->ajaxObj->addContent('url', $this->getPageURL($parentPageUID));
			}
		}
	}

	/**
	 * AJAX response to a hide action on a particular record.
	 *
	 * @param	string		Name of the table.
	 * @param	integer		UID of the record.
	 * @return	void
	 */
	protected function hideItem($table, $uid) {
		if ($table == 'pages') {
			$parentPageUID = $GLOBALS['TSFE']->page['pid'];
			if ($parentPageUID) {
				$this->ajaxObj->addContent('url', $this->getPageURL($parentPageUID));
			}
		}
	}
			
	/**
	 * AJAX response to a move up action on a particular record.
	 *
	 * @param	string		Name of the table.
	 * @param	integer		UID of the record.
	 * @return	void
	 */
	protected function upItem($table, $uid) {
		$this->moveUpDownItem($table, $uid, -1, $GLOBALS['BE_USER']->frontendEdit->TSFE_EDIT['flexformPointer']);
	}

	/**
	 * AJAX response to a move down action on a particular record.
	 *
	 * @param	string		Name of the table.
	 * @param	integer		UID of the record.
	 * @return	void
	 */
	protected function downItem($table, $uid) {
		$this->moveUpDownItem($table, $uid, 1, $GLOBALS['BE_USER']->frontendEdit->TSFE_EDIT['flexformPointer']);
	}

	/**
	 * AJAX response to a move up/down action on a particular record.
	 *
	 * @param	string		Name of the table.
	 * @param	integer		UID of the record.
	 * @param	integer		Direction to move the record.
	 * @param	string		FlexForm pointer if using Templavoila. Otherwise this will be empty
	 * @return	void
	 * @todo 	Jeff: Make sure moveDir is used consistently. up/down vs. -1/+1
	 * @todo	Extract TV specific code.
	 */
	protected function moveUpDownItem($table, $uid, $moveDir, $flexPtr='') {
			// Special TemplaVoila handling
		if ($flexPtr != '') {
			$origTVFlex = $GLOBALS['TSFE']->page['tx_templavoila_flex'];
				// load the flex array and find values
			$flexArray = t3lib_div::trimExplode(':',$flexPtr);
				// this is the sDEF:lDEF:field_maincontent:vDEF:uid #
			$templavoilaFlex = t3lib_div::xml2array($origTVFlex);
			$contentValues = $templavoilaFlex['data'][$flexArray[2]][$flexArray[3]][$flexArray[4]][$flexArray[5]];
			$this->ajaxObj->addContent('move_array',$contentValues);
		}
		
		$this->ajaxObj->addContent('move_dir', $moveDir);
	}

	/**
	 * AJAX response to a move / drag  action on a particular record.
	 *
	 * @param	string		Name of the table.
	 * @param	integer		UID of the record.
	 * @return	void
	 */
	protected function moveAfterItem($table, $uid) {
		$this->ajaxObj->addContent('moveAfterUID', $GLOBALS['BE_USER']->frontendEdit->TSFE_EDIT['moveAfter']);
		
		// if the item was moved to another column (colPos), then it needs to be saved to the DB as well.
		if ($table == 'tt_content' && isset($GLOBALS['BE_USER']->frontendEdit->TSFE_EDIT['colPos'])) {
			$colPos = $GLOBALS['BE_USER']->frontendEdit->TSFE_EDIT['colPos'];
			$data = array();
			$data[$table][$uid]['colPos'] = $colPos;
			$tce = t3lib_div::makeInstance('t3lib_TCEmain');
			$tce->start($data, array());
			$tce->process_datamap();
			$this->ajaxObj->addContent('colPos', $colPos);
		}
	}
	
	/**
	 * AJAX response to a move action on a particular record.
	 *
	 * @param	string		Name of the table.
	 * @param	integer		UID of the record.
	 * @param	string		FlexForm pointer
	 * @param	string		Cut vs. Copy mode.
	 * @return	void
	 * @todo	Extract TV specific code.
	 */
	protected function cutCopyItem($table, $uid, $sourcePtr, $copyMode) {
			// save item in the clipboard obj of user session...
			// @todo	Can we avoid settings $_GET directly?
		$_GET['id'] = $GLOBALS['TSFE']->id;
		$_GET['sourcePointer'] = $sourcePtr;
		$_GET['setCopyMode'] = $copyMode;
		$thisRec = $table . '|' . $uid;
		$_GET['CB']['el'][$thisRec] = 1;
		$_GET['CB']['setCopyMode'] = $copyMode;

			// Start clipboard
		$this->t3libClipboardObj = t3lib_div::makeInstance('t3lib_clipboard');
			// Initialize - reads the clipboard content from the user session
		$this->t3libClipboardObj->initializeClipboard();
			// Clipboard actions are handled:
			// @todo ??? IS SET???
		$CB = t3lib_div::_GET('CB');	// CB is the clipboard command array
			// @todo 	jeff: $this->cmd = action = ajax[0].  Doesn't seem to ever equal setCB when we're here.
		if ($this->cmd == 'setCB') {
				// CBH is all the fields selected for the clipboard, CBC is the checkbox fields which were checked. By merging we get a full array of checked/unchecked elements
				// This is set to the 'el' array of the CB after being parsed so only the table in question is registered.
				// @todo	Clean up the POSTVAR naming.
			$CB['el'] = $this->t3libClipboardObj->cleanUpCBC(array_merge((array)t3lib_div::_POST('CBH'), (array)t3lib_div::_POST('CBC')), $this->cmd_table);
		}
			// If the clipboard is NOT shown, set the pad to 'normal'.
		$CB['setP'] = 'normal';
			// Execute commands.
		$this->t3libClipboardObj->setCmd($CB);
			// Clean up pad
		$this->t3libClipboardObj->cleanCurrent();
			// Save the clipboard content
		$this->t3libClipboardObj->endClipboard();

			// return the paste and close links
			// @todo	PID is not an argument!
		$data = $this->renderContentElement($table, $uid, $this->pid);

			// now wrap in editPanel
		$cObj = t3lib_div::makeInstance('tslib_cObj');
		$cObj->start($this->contentElementRow, $table);
		if ($sourcePtr) {
			$cObj->setCurrentVal($sourcePtr);
		}
		$dataArr = $cObj->data;
		$lang = $myGET['L'] ? $myGET['L'] : 0;
		$cObj->allow['paste'] = 1;
		$cObj->allow['clear_clipboard'] = 1;

		$dPtr = explode(':', $sourcePtr);
		$cObj->destinationPointer['table'] = $dPtr[0];
		$cObj->destinationPointer['uid']   = $dPtr[1];
		$cObj->destinationPointer['sheet'] = $dPtr[2];
		$cObj->destinationPointer['sLang'] = $dPtr[3];
		$cObj->destinationPointer['field'] = $dPtr[4];
		$cObj->destinationPointer['vLang'] = $dPtr[5];
		$cObj->destinationPointer['position'] = $dPtr[6];
		$cObj->getPasteLink($uid, $dataArr['colPos'], $lang, 'TSFE_FORM_' . $uid);

			// reformat the paste and clear_clipboard links so generic...because have to put in EVERY hovermenu
		$pasteText = $cObj->panelItems['paste'];
		$pattern = '/(onClick="fe_edit_ajax\(.+?,.+?,.+?,)(.+?)(\);)/i';
		$replacement = '$1\'###FLEXPTR###\'$3';
		$pasteText = preg_replace($pattern, $replacement, $pasteText);
		$pattern = '/(' . $table . '\:)(.+?\')/i';
		$pasteText = preg_replace($pattern, '$1###UID###\'', $pasteText);
		$clearClipText = $cObj->panelItems['clear_clipboard'];
		$clearClipText = preg_replace($pattern, '$1###UID###\'', $clearClipText);

		$this->ajaxObj->addContent('paste', $pasteText);
		$this->ajaxObj->addContent('clear_clipboard', $clearClipText);
	}

	/**
	 * Clears the specified item from the clipboard.
	 *
	 * @param		string		The name of the table.
	 * @param		integer		The UID of the record.
	 * @param		string		FlexForm pointer.
	 * @return		void
	 * @todo		Remove the TemplaVoila specific code.
	 */
	protected function clearClipboard($table, $uid, $sourcePtr = 0) {
			// clear current item in the clipboard obj user session...
			// Start clipboard
		$this->t3libClipboardObj = t3lib_div::makeInstance('t3lib_clipboard');
			// Initialize - reads the clipboard content from the user session
		$this->t3libClipboardObj->initializeClipboard();
		$cData = $this->t3libClipboardObj->clipData;
		$thisUID = $cData[$cData['current']]['el'];
		if (is_array($thisUID)) {
			$thisRec = key($thisUID);
			$_GET['id'] = $GLOBALS['TSFE']->id;
			$_GET['CB']['remove'] = $thisRec;
				// CB is the clipboard command array
			$CB = t3lib_div::_GET('CB');
				// Execute commands.
			$this->t3libClipboardObj->setCmd($CB);
				// Clean up pad
			$this->t3libClipboardObj->cleanCurrent();
					// Save the clipboard content
			$this->t3libClipboardObj->endClipboard();
			
			$this->ajaxObj->addContent('clear_clipboard',$thisRec);
		}
	}

	/**
	 * Handles the paste of a content element.
	 *
	 * @param		string		The name of the table.
	 * @param		integer		The UID of the record.
	 * @param		string		The Flexform pointer to paste from.
	 * @param		string		The Flexform Pointer to paste to.
	 * @param		string		Cut / Copy mode.
	 * @return		void
	 * @todo		Remove TemplaVoila specific code.
	 */
	protected function pasteItemHERE($table, $uid) {
		// @todo	Are these parameters needed?
	//},$sourcePtr,$destPtr,$copyMode) {

		$mySrcPtr = explode(':', $sourcePtr);
		$sourcePointer['table'] = $mySrcPtr[0];
		$sourcePointer['uid'] = $mySrcPtr[1];
		$sourcePointer['sheet'] = $mySrcPtr[2];
		$sourcePointer['sLang'] = $mySrcPtr[3];
		$sourcePointer['field'] = $mySrcPtr[4];
		$sourcePointer['vLang'] = $mySrcPtr[5];
		$sourcePointer['position'] = $mySrcPtr[6];

		$myDestPtr = explode(':',$destPtr);
		$destinationPointer['table'] = $myDestPtr[0];
		$destinationPointer['uid'] = $myDestPtr[1];
		$destinationPointer['sheet'] = $myDestPtr[2];
		$destinationPointer['sLang'] = $myDestPtr[3];
		$destinationPointer['field'] = $myDestPtr[4];
		$destinationPointer['vLang'] = $myDestPtr[5];
		$destinationPointer['position'] = $myDestPtr[6];

		$flexPtr = $GLOBALS['BE_USER']->frontendEdit->TSFE_EDIT['flexformPointer'];
		if ($flexPtr) {
			require_once(t3lib_extMgm::extPath('templavoila') . 'class.tx_templavoila_api.php');
			$TVObj = t3lib_div::makeInstance('tx_templavoila_api', $sourcePointer['table']);
			$srcRec = $TVObj->flexform_getRecordByPointer($sourcePointer);
			$destRec = $TVObj->flexform_getRecordByPointer($destinationPointer);
			$elID = $srcRec['uid'];
				// cut mode
			if(!$copyMode) {
				$ok = $TVObj->moveElement_setElementReferences($sourcePointer, $destinationPointer);
			}
				// copy mode
			elseif(intval($copyMode) == 1) {
				$ok = $TVObj->insertElement_setElementReferences($destinationPointer, $sourcePointer['uid']);
					// set copyMode to new UID
				$elID = $TVObj->copyElement($sourcePointer, $destinationPointer);
			}
				// ???
			else {
				$ok = $TVObj->referenceElement($sourcePointer, $destinationPointer);
			}

				// grab source content (either created or copy version)
			$data = $this->renderContentElement($table, $elID);

			if ($ok) {
					// clear the clipboard
				$this->clearClipboard($table, $src['uid']);

					// send back all info
				$this->ajaxObj->addContent('src_id', $srcRec['uid']);
				$this->ajaxObj->addContent('dest_id', $destRec['uid']);				
				$this->ajaxObj->addContent('paste_content', $data);
				$this->ajaxObj->addContent('copy_mode', $copyMode);
			}
		}
	}

	/**
	 * Initialize the TYPO3 Frontend for a given page id.
	 * 
	 * @param		integer		The page id.
	 * @return		void
	 * @todo		feUserObj doesn't seem to be used.
	 */
	protected function initializeTSFE($pid, $feUserObj = '') {
		global $TSFE, $TYPO3_CONF_VARS;

			// @todo 	jeff: don't include templavoila directly
		if (t3lib_extMgm::isLoaded('templavoila')) {	
			require_once(t3lib_extMgm::extPath('templavoila').'class.tx_templavoila_api.php');
		}

			// create object instances:
		$TSFE = t3lib_div::makeInstance('tslib_fe', $TYPO3_CONF_VARS, $pid, 0, true);

		$TSFE->sys_page = t3lib_div::makeInstance('t3lib_pageSelect');
		$TSFE->tmpl = t3lib_div::makeInstance('t3lib_tstemplate');
		$TSFE->tmpl->init();

			// fetch rootline and extract ts setup:
		$TSFE->rootLine = $TSFE->sys_page->getRootLine(intval($pid));
		$TSFE->getConfigArray();

			// then initialize fe user
		$TSFE->initFEuser();
		$TSFE->fe_user->fetchGroupData();

			// initialize the backend user
		$this->initializeBackendUser();

			// allow hidden pages and records to be edited.
		$TSFE->showHiddenPage = 1;
		$TSFE->showHiddenRecords = 1;
		
		$TT = new t3lib_timeTrack;
		$TT->start();

			// Include the TCA
		$TSFE->includeTCA();
		if(t3lib_div::int_from_ver(TYPO3_version) >= 4004000) {
				// Load the sprite manager for frontend-editing
			$spriteManager = t3lib_div::makeInstance('t3lib_SpriteManager', TRUE);
			$spriteManager->loadCacheFile();
		}

			// Get the page
		$TSFE->fetch_the_id();
		$TSFE->getPageAndRootline();
		$TSFE->initTemplate();
		$TSFE->tmpl->getFileName_backPath = PATH_site;
		$TSFE->forceTemplateParsing = true;
		$TSFE->getConfigArray();

			// Get the Typoscript as its inherited from parent pages
		$template = t3lib_div::makeInstance('t3lib_tsparser_ext'); // Defined global here!
		$template->tt_track = 0;
		$template->init();
		$sys_page = t3lib_div::makeInstance('t3lib_pageSelect');
		$rootLine = $sys_page->getRootLine($pid);
		$template->runThroughTemplates($rootLine); // This generates the constants/config + hierarchy info for the template.
		$template->generateConfig();

			// Save the setup
		$this->setup = $template->setup;
	}

	/**
	 * Initializes the backend user.
	 *
	 * @return void
	 */
	protected function initializeBackendUser() {
		global $BE_USER, $TYPO3_DB, $TSFE, $LANG;

			// @todo	What's the point here? To prevent looping?
		if ($this->initBE) {
			return;
		}
		$this->initBE = true;

		$GLOBALS['BE_USER'] = '';

			// If the backend cookie is set, we proceed and check if a backend user is logged in.
		if ($_COOKIE['be_typo_user']) {
			require_once (PATH_t3lib.'class.t3lib_befunc.php');
			require_once (PATH_t3lib.'class.t3lib_userauthgroup.php');
			require_once (PATH_t3lib.'class.t3lib_beuserauth.php');
			require_once (PATH_t3lib.'class.t3lib_tsfebeuserauth.php');

			// the value this->formfield_status is set to empty in order to disable login-attempts to the backend account through this script
			// @todo 	Comment says its set to empty, but where does that happen?
				
			$GLOBALS['BE_USER'] = t3lib_div::makeInstance('t3lib_tsfeBeUserAuth');
			$GLOBALS['BE_USER']->OS = TYPO3_OS;
			$GLOBALS['BE_USER']->lockIP = $GLOBALS['TYPO3_CONF_VARS']['BE']['lockIP'];
			$GLOBALS['BE_USER']->start();
			$GLOBALS['BE_USER']->unpack_uc('');
			if ($GLOBALS['BE_USER']->user['uid']) {
				$GLOBALS['BE_USER']->fetchGroupData();
				$GLOBALS['TSFE']->beUserLogin = true;
			}
			if ($GLOBALS['BE_USER']->checkLockToIP() && $GLOBALS['BE_USER']->checkBackendAccessSettingsFromInitPhp() && $GLOBALS['BE_USER']->user['uid']) {
				$GLOBALS['BE_USER']->initializeAdminPanel();
				$GLOBALS['BE_USER']->initializeFrontendEdit();
			} else {
				$GLOBALS['BE_USER'] = '';
				$GLOBALS['TSFE']->beUserLogin = false;
			}
		}

			// @todo	Is the if statement needed here?
		//if ($GLOBALS['TSFE']->beUserLogin && is_object($GLOBALS['BE_USER']->frontendEdit))	{
			require_once(t3lib_extMgm::extPath('lang') . 'lang.php');
			$GLOBALS['LANG'] = t3lib_div::makeInstance('language');
			$GLOBALS['LANG']->init($GLOBALS['BE_USER']->uc['lang']);
		//}

		define('PATH_typo3_mod', '../');
	}

	/**
	 * Gets the database row for a specific content element.
	 *
	 * @param	integer		UID of the content element.
	 * @return	array
	 */
	protected function getRow($table, $uid) {
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', $table, 'uid=' . $uid);
		$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		$GLOBALS['TYPO3_DB']->sql_free_result($res);

		return $row;
	}

	/**
	 * Renders the specified content element as if it appears on the specified page
	 * and adds the content to $this->ajaxObj
	 *
	 * @param	integer		UID of the content element to render.
	 * @param	integer		UID of the page to render the content element on.
	 * @return	string		HTML output for the content element.
	 */
	protected function renderContentElement($table, $uid) {
			// Including pagegen will make sure that extension PHP files are included
		if (($GLOBALS['BE_USER']->frontendEdit->TSFE_EDIT['cmd'] == 'close') || ($GLOBALS['BE_USER']->frontendEdit->TSFE_EDIT['cmd'] == 'saveAndClose')) {
			global $TSFE, $TT;
			require_once(PATH_tslib . 'class.tslib_pagegen.php');
			include(PATH_tslib . 'pagegen.php');
		} else {
			$GLOBALS['TSFE']->newCObj();
		}

		if(intval($uid)) {
			$this->contentElementRow = $this->getRow($table, $uid);
		} else {
			$this->contentElementRow = array();
			$this->contentElementRow['uid'] = $uid;
		}

		$cObj = t3lib_div::makeInstance('tslib_cObj');
		$cObj->start($this->contentElementRow, 'tt_content');

			// @todo	Hack to render editPanel for records other than tt_content.
		if(($table == 'tt_content') && ($uid != 'NEW')) {
			$cObjOutput = $cObj->cObjGetSingle($this->setup['tt_content'], $this->setup['tt_content.']);
		} else {
			$conf = array('allow' => 'edit, new, hide');
			if ($uid == 'NEW') {
				$conf['newRecordFromTable'] = $table;
			}
			if (isset($GLOBALS['BE_USER']->frontendEdit->TSFE_EDIT['newRecordInPid'])) {
				$conf['newRecordInPid'] = $GLOBALS['BE_USER']->frontendEdit->TSFE_EDIT['newRecordInPid'];
			}
			$cObjOutput = $cObj->editPanel('', $conf, $table . ':' . $uid, $this->contentElementRow);
		}

			// Set a simplified template file for use in the AJAX response.  No title, meta tags, etc.
			// @todo Should we account for footer data too?
		$pageRenderer = $GLOBALS['TSFE']->getPageRenderer();
		$pageRenderer->setTemplateFile(t3lib_extMgm::extPath('feeditadvanced') . 'res/template/content_element.tmpl');
		$pageRenderer->setCharSet($GLOBALS['TSFE']->metaCharset);
		$pageRenderer->enableConcatenateFiles();
		
			// Set the BACK_PATH for the pageRenderer concatenation.
			// FIXME should be removed when the sprite manager, RTE, and pageRenderer are on the same path about concatenation.
		$GLOBALS['BACK_PATH'] = TYPO3_mainDir;

		$header = $this->renderHeaderData();
		$content = $cObjOutput;

		if ($GLOBALS['TSFE']->isINTincScript()) {
			$GLOBALS['TSFE']->content = $content;
			$GLOBALS['TSFE']->INTincScript();
			$content = $GLOBALS['TSFE']->content;
		}

		$this->ajaxObj->addContent('header', $header);
		$this->ajaxObj->addContent('content', $content);
	}

	/**
	 * Renders header data from TSFE->additionalHeaderData and t3lib_pageRenderer.
	 *
	 * @return	string
	 */
	protected function renderHeaderData() {
		$headerDataString = $GLOBALS['TSFE']->getPageRenderer()->render(t3lib_pageRenderer::PART_HEADER);

		foreach ($GLOBALS['TSFE']->additionalHeaderData as $headerData) {
			$headerDataString .= $headerData;
		}

		return $headerDataString;
	}

	/**
	 * Renders footer data from t3lib_pageRenderer
	 *
	 * @return	string
	 */
	protected function renderFooterData() {
		return $GLOBALS['TSFE']->getPageRenderer()->render(t3lib_pageRenderer::PART_FOOTER);
	}

	/**
	 * Gets the absolute URL for the specified page ID.
	 *
	 * @param	integer	The page id.
	 * @return	string
	 */
	protected function getPageURL($id) {
		$cObj = t3lib_div::makeInstance('tslib_cObj');
		$cObj->start(array());
		$conf = array (
			'parameter' => $id
		);
		$url = $cObj->typolink_URL($conf);

		if ($this->isRelativeUrl($url)) {
			$url = t3lib_div::getIndpEnv(TYPO3_SITE_URL) . $url;
		}

		return $url;
	}

	/**
	 * Determines wether the URL is relative to the
	 * current TYPO3 installation.
	 *
	 * @note Copied from tx_felogin_pi1 implementation.
	 * @param string $url URL which needs to be checked
	 * @return boolean Whether the URL is considered to be relative
	 */
	protected function isRelativeUrl($url) {
		$parsedUrl = @parse_url($url);
		if ($parsedUrl !== FALSE && !isset($parsedUrl['scheme']) && !isset($parsedUrl['host'])) {
				// If the relative URL starts with a slash, we need to check if it's within the current site path
			return (!t3lib_div::isFirstPartOfStr($parsedUrl['path'], '/') || t3lib_div::isFirstPartOfStr($parsedUrl['path'], t3lib_div::getIndpEnv('TYPO3_SITE_PATH')));
		}
		return FALSE;
	}
}

	// exit, if script is called directly (must be included via eID in index_ts.php)
if (!defined ('PATH_typo3conf')) die ('Could not access this script directly!');

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feeditadvanced/service/ajax.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feeditadvanced/service/ajax.php']);
}

$feEditAjax = t3lib_div::makeInstance('tx_feeditadvanced_ajax');
$feEditAjax->processAction();

?>