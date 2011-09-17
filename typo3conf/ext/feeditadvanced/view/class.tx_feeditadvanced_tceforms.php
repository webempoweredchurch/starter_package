<?php
/***************************************************************
*  Copyright notice
*
*  (c) 1999-2006 Kasper Skaarhoj (kasperYYYY@typo3.com)
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
 * Contains TYPO3 Core Form generator - AKA "TCEforms"
 *
 * $Id: class.t3lib_tceforms.php 2232 2007-03-30 09:41:10Z ohader $
 * Revised for TYPO3 3.6 August/2003 by Kasper Skaarhoj
 * XHTML compliant
 *
 * @author	Kasper Skaarhoj <kasperYYYY@typo3.com>
 */

/**
 * 'TCEforms' - Class for creating the backend editing forms.
 *
 * @author	Kasper Skaarhoj <kasperYYYY@typo3.com>
 * @coauthor	Rene Fritz <r.fritz@colorcube.de>
 * @package TYPO3
 * @subpackage t3lib
 */

/*************************** class starts *************************************/

class tx_feeditadvanced_tceforms extends t3lib_TCEforms_fe {
	var $imagePath = ''; // image path for Forms on page frontend editing mode
	var $backPath = '';
	
	public function __construct() {
			// Set the BACK_PATH for the sprite manager and then immediately unset for rtehtmlarea.
			// FIXME should be removed when the sprite manager and RTE are on the same page with backPath vs. BACK_PATH usage.
		$GLOBALS['BACK_PATH'] = TYPO3_mainDir;

			// Call the rendering function and ignore the output. This serves to flush any header data set before the editing form.
		$pageRenderer = $GLOBALS['TSFE']->getPageRenderer();
		$pageRenderer->render();

			// Set IE8 Standards Mode
		$pageRenderer->addMetaTag('<meta http-equiv="X-UA-Compatible" content="IE=8" />');

		parent::__construct();
		
		$GLOBALS['BACK_PATH'] = '';
	}

	/**
	 * adds basic configurations
	 *
	 * @note This is a new method. Is init really the right name and should it be in a constructor intead?
	*/
	function init() {
		if (!$this->imagePath && $this->backPath)
			$this->imagePath = $this->backPath . 'gfx/';
	}
	
	/**
	 * @note	Where did wrapLabels() go?
	 */


	/**
	 * Returns help-text ICON if configured for.
	 *
	 * @param	string		The table name
	 * @param	string		The field name
	 * @param	boolean		Force the return of the help-text icon.
	 * @return	string		HTML, <a>-tag with
	 *
	 * @note	This is a new method, but extends an existing method in t3lib_tceforms.
	 */
	function helpTextIcon($table, $field, $force=false) {
			// @note	init() call was addeded.
		$this->init();

		if ($this->globalShowHelp && $GLOBALS['TCA_DESCR'][$table]['columns'][$field] && (($this->edit_showFieldHelp=='icon' && !$this->doLoadTableDescr($table)) || $force)) {
			$aOnClick = 'vHWin=window.open(\'' . $this->backPath . 'view_help.php?tfID=' . ($table . '.' . $field) . '\',\'viewFieldHelp\',\'height=400,width=600,status=0,menubar=0,scrollbars=1\');vHWin.focus();return false;';
			
			// @note	Missing check for edit_showFieldHelp == icon and assignment of $text. Might just be due to an older TYPO3 version that this was copied from.
			
			return '<a href="#" onclick="' . htmlspecialchars($aOnClick) . '">' .
					'<img' . t3lib_iconWorks::skinImg($this->imagePath, 'helpbubble.gif', 'width="14" height="14"') . ' hspace="2" border="0" class="absmiddle"' . ($GLOBALS['CLIENT']['FORMSTYLE'] ? ' style="cursor:help;"' : '') . ' alt="" />' .
					'</a>';
		} else {
				// Detects fields with no CSH and outputs dummy line to insert into CSH locallang file:
			return '<span class="nbsp">&nbsp;</span>';
		}
	}


	/**
	 * Prints the selector box form-field for the db/file/select elements (multiple)
	 *
	 * @param	string		Form element name
	 * @param	string		Mode "db", "file" (internal_type for the "group" type) OR blank (then for the "select" type)
	 * @param	string		Commalist of "allowed"
	 * @param	array		The array of items. For "select" and "group"/"file" this is just a set of value. For "db" its an array of arrays with table/uid pairs.
	 * @param	string		Alternative selector box.
	 * @param	array		An array of additional parameters, eg: "size", "info", "headers" (array with "selector" and "items"), "noBrowser", "thumbnails"
	 * @param	string		On focus attribute string
	 * @param	string		$table: (optional) Table name processing for
	 * @param	string		$field: (optional) Field of table name processing for
	 * @param	string		$uid:	(optional) uid of table record processing for
	 * @return	string		The form fields for the selection.
	 *
	 * @note	This is a new method, but extends an existing method in t3lib_tceforms. At first glance, it looks way too similar to the original method.
	 * @note	Renamed function so that tceforms version is called, fixing issues with multiselect lists.
	*/
	function dbFileIcons_local($fName, $mode, $allowed, $itemArray, $selector='', array $params=array(), $onFocus='', $table='', $field='', $uid='')	{
			// @note	Added call to init()
		$this->init();

		$disabled = '';
		if ($this->renderReadonly || $params['readOnly'])  {
			$disabled = ' disabled="disabled"';
		}

			// Sets a flag which means some JavaScript is included on the page to support this element.
		$this->printNeededJS['dbFileIcons'] = 1;

			// INIT
		$uidList=array();
		$opt=array();
		$itemArrayC=0;

			// Creating <option> elements:
		if (is_array($itemArray))	{
			$itemArrayC=count($itemArray);
			reset($itemArray);
			switch($mode)	{
				case 'db':
					while(list(,$pp) = each($itemArray))	{
						$pRec = t3lib_BEfunc::getRecordWSOL($pp['table'],$pp['id']);
						if (is_array($pRec)) {
							$pTitle = t3lib_BEfunc::getRecordTitle($pp['table'], $pRec, FALSE, TRUE);
							$pUid = $pp['table'] . '_' . $pp['id'];
							$uidList[] = $pUid;
							$opt[] = '<option value="' . htmlspecialchars($pUid) . ' ">' . htmlspecialchars($pTitle) . ' </option>';
						}
					}
				break;
				// @note	Missing case for folder.  Probably just due to older TYPO3 version.
				case 'file':
					while(list(,$pp)=each($itemArray))	{
						$pParts = explode('|',$pp);
						$uidList[] = $pUid = $pTitle = $pParts[0];
						$opt[] = '<option value="' . htmlspecialchars(rawurldecode($pParts[0])) . '">' . htmlspecialchars(rawurldecode($pParts[0])) . '</option>';
					}
				break;
				default:
					while(list(,$pp)=each($itemArray))	{
						$pParts = explode('|',$pp, 2);
						$uidList[] = $pUid = $pParts[0];
						$pTitle = $pParts[1];
						$opt[]='<option value="' . htmlspecialchars(rawurldecode($pUid)) . '">' . htmlspecialchars(rawurldecode($pTitle)) . '</option>';
					}
				break;
			}
		}

			// Create selector box of the options
		$sSize = $params['autoSizeMax'] ? t3lib_div::intInRange($itemArrayC+1, t3lib_div::intInRange($params['size'],1),$params['autoSizeMax']) : $params['size'];
		if (!$selector) {
				// @note	The code here is simpler than in t3lib_tceforms, possibly due to more recent changes in the core.
			$selector = '<select size="' . $sSize . ' "' . $this->insertDefStyle('group') . ' multiple="multiple" name="' . $fName . '_list" ' . $onFocus.$params['style'] . $disabled . '>' . implode('', $opt) . '</select>';
		}

		$icons = array(
			'L' => array(),
			'R' => array(),
		);
			// @note	Inside the if statement, we see $this->imagePath where $this->backPath was previously used.
		if (!$params['readOnly']) {
			if (!$params['noBrowser'])	{
					// check against inline uniqueness
				$inlineParent = $this->inline->getStructureLevel(-1);
				if(is_array($inlineParent) && $inlineParent['uid']) {
					if ($inlineParent['config']['foreign_table'] == $table && $inlineParent['config']['foreign_unique'] == $field) {
						$objectPrefix = $this->inline->inlineNames['object'] . '[' . $table . ']';
						$aOnClickInline = $objectPrefix . '|inline.checkUniqueElement|inline.setUniqueElement';
						$rOnClickInline = 'inline.revertUnique(\'' . $objectPrefix . '\',null,\'' . $uid . '\');';
					}
				}
				$aOnClick='setFormValueOpenBrowser(\'' . $mode . '\',\'' . ($fName . ' |||' . $allowed . '|' . $aOnClickInline) . '\'); return false;';
				$icons['R'][]='<a href="#" onclick="' . htmlspecialchars($aOnClick) . '">' .
						'<img' . t3lib_iconWorks::skinImg($this->imagePath,'insert3.gif','width="14" height="14"') . ' border="0" ' . t3lib_BEfunc::titleAltAttrib($this->getLL('l_browse_' . ($mode=='file' ? 'file' : 'db'))) . ' />' .
						'</a>';
			}
				// @note	Isn't this dead code?
			elseif (!$params['noBrowser'])	{
				$aOnClick = 'setFormValueOpenBrowser(\'' . $mode . '\',\'' . ($fName . ' |||' . $allowed . '|') . '\'); return false;';
				$icons['R'][] = '<a href="#" onclick="' . htmlspecialchars($aOnClick) . ' ">' .
						'<img' . t3lib_iconWorks::skinImg($this->imagePath,'insert3.gif','width="14" height="14"') . ' border="0" ' . t3lib_BEfunc::titleAltAttrib($this->getLL('l_browse_' . ($mode=='file' ? 'file' : 'db'))) . ' />' .
						'</a>';
			}

			if (!$params['dontShowMoveIcons'])	{
				if ($sSize >= 5) {
					$icons['L'][]='<a href="#" onclick="setFormValueManipulate(\'' . $fName . '\',\'Top\'); return false;">' .
							'<img' . t3lib_iconWorks::skinImg($this->imagePath,'group_totop.gif','') . ' border="0" ' . t3lib_BEfunc::titleAltAttrib($this->getLL('l_move_to_top')) . ' />' .
							'</a>';
				}
				$icons['L'][]='<a href="#" onclick="setFormValueManipulate(\'' . $fName . ' \',\'Up\'); return false;">' .
						'<img' . t3lib_iconWorks::skinImg($this->imagePath,'up.gif','') . ' border="0" ' . t3lib_BEfunc::titleAltAttrib($this->getLL('l_move_up')) . '  />' .
						'</a>';
				$icons['L'][]='<a href="#" onclick="setFormValueManipulate(\'' . $fName . ' \',\'Down\'); return false;">' .
						'<img' . t3lib_iconWorks::skinImg($this->imagePath,'down.gif','') . ' border="0" ' . t3lib_BEfunc::titleAltAttrib($this->getLL('l_move_down')) . '  />' .
						'</a>';
				if ($sSize>=5)	{
					$icons['L'][]='<a href="#" onclick="setFormValueManipulate(\'' . $fName . ' \',\'Bottom\'); return false;">' .
							'<img' . t3lib_iconWorks::skinImg($this->imagePath,'group_tobottom.gif','') . '  border="0" ' . t3lib_BEfunc::titleAltAttrib($this->getLL('l_move_to_bottom')) . '  />' .
							'</a>';
				}
			}

			$clipElements = $this->getClipboardElements($allowed,$mode);
			if (count($clipElements))	{
				$aOnClick = '';
	#			$counter = 0;
				foreach($clipElements as $elValue)	{
					if ($mode=='file')	{
						$itemTitle = 'unescape(\'' . rawurlencode(basename($elValue)) . ' \')';
					} else {	// 'db' mode assumed
						list($itemTable,$itemUid) = explode('|', $elValue);
						$itemTitle = $GLOBALS['LANG']->JScharCode(t3lib_BEfunc::getRecordTitle($itemTable, t3lib_BEfunc::getRecordWSOL($itemTable,$itemUid)));
						$elValue = $itemTable . ' _' . $itemUid;
					}
					$aOnClick.= 'setFormValueFromBrowseWin(\'' . $fName . ' \',unescape(\'' . rawurlencode(str_replace('%20',' ',$elValue)) . ' \'),' . $itemTitle . ' );';

	#				$counter++;
	#				if ($params['maxitems'] && $counter >= $params['maxitems'])	{	break;	}	// Makes sure that no more than the max items are inserted... for convenience.
				}
				$aOnClick.= 'return false;';
				$icons['R'][]='<a href="#" onclick="' . htmlspecialchars($aOnClick) . ' ">' .
						'<img' . t3lib_iconWorks::skinImg($this->imagePath,'insert5.png','') . '  border="0" ' . t3lib_BEfunc::titleAltAttrib(sprintf($this->getLL('l_clipInsert_' . ($mode=='file' ? 'file' : 'db')),count($clipElements))) . '  />' .
						'</a>';
			}
			$rOnClick = $rOnClickInline . ' setFormValueManipulate(\'' . $fName . ' \',\'Remove\'); return false';
			$icons['L'][]='<a href="#" onclick="' . htmlspecialchars($rOnClick) . ' ">' .
					'<img' . t3lib_iconWorks::skinImg($this->imagePath,'group_clear.gif','') . '  border="0" ' . t3lib_BEfunc::titleAltAttrib($this->getLL('l_remove_selected')) . '  />' .
					'</a>';

		}
		
		// @note	Thumbnail code here is new.
		// path for finding thumbnails is wrong used in frontend editing mode 'Forms on page'
		#$params['thumbnails']=str_replace("../",t3lib_div::getIndpEnv('TYPO3_SITE_URL'), $params['thumbnails']);
		$params['thumbnails']=str_replace("../","", $params['thumbnails']); // this must reset in class.ux_tslib_cobj.php
		#$params['thumbnails']=str_replace("..%2F","", $params['thumbnails']); // this is ok

		$info=($params['thumbnails'] ? $this->wrapLabels($params['headers']['items']) : '');
		
		// @note	Minor formatting changes here.
		$str='<table border="0" cellpadding="0" cellspacing="0" width="1">
			' . ($params['headers'] ? '
				<tr>
					<td>' . $this->wrapLabels($params['headers']['selector']) . ' </td>
					<td></td>
					<td></td>
					<td></td>
					<td>' . $info . ' </td>
				</tr>' : '').
			'
			<tr>
				<td valign="top">' .
					$selector . ' <br />' .
					$this->wrapLabels($params['info']).
				'</td>
				<td valign="top">' .
					implode('<br />',$icons['L']) . ' </td>
				<td valign="top">' .
					implode('<br />',$icons['R']) . ' </td>
				<td style="padding-left:5px"></td>
				<td valign="top">' .
					$this->wrapLabels($params['thumbnails']).
				'</td>
			</tr>
		</table>';

			// Creating the hidden field which contains the actual value as a comma list.
		$str.='<input type="hidden" name="' . $fName . ' " value="' . htmlspecialchars(implode(',',$uidList)) . ' " />';

		return $str;
	}

	/**
	 * Creates HTML output for a palette
	 *
	 * @param	array		The palette array to print
	 * @return	string		HTML output
	 *
	 * @note	This is an update of an existing method. It looks like the main diferences are colors and help icons.
	 */
	function printPalette($paletteArray) {
		global $BE_USER;

			// @note Color scheme attributes are new.
			// Init color/class attributes:
		$ccAttr2 = $this->colorScheme[2] ? ' bgcolor="' . $this->colorScheme[2] . ' "' : '';
		$ccAttr2.= $this->classScheme[2] ? ' class="' . $this->classScheme[2] . ' "' : '';
		$ccAttr4 = $this->colorScheme[4] ? ' style="color:' . $this->colorScheme[4] . ' "' : '';
		$ccAttr4.= $this->classScheme[4] ? ' class="' . $this->classScheme[4] . ' "' : '';

			// Traverse palette fields and render them into table rows:
		foreach ($paletteArray as $content)	{
			$hRow[] = '<td' . $ccAttr2 . ' >&nbsp;</td><td nowrap="nowrap"' . $ccAttr2 . ' >' . '<span' . $ccAttr4 . ' >' . $content['NAME'] . '</span></td>';
			
			// @note  This is a new config option.
			if ($GLOBALS['BE_USER']->uc['TSFE_adminConfig']['forceFormsOnPage']) {
				$helpIcon = $content['HELP_ICON'];
			}
			
			$iRow[] = '<td valign="top" class="space"> </td><td nowrap="nowrap" valign="top">' . $content['ITEM'] . $helpIcon . ' </td>';
		}

			// Final wrapping into the table:
		$out = '<table border="0" cellpadding="0" cellspacing="0" class="typo3-TCEforms-palette"><tr><td class="palettePadding"></td>' .
				implode('', $hRow) . ' </tr><tr><td class="palettePadding"></td>' .
				implode('', $iRow) . ' </tr></table>';

		return $out;
	}


	/**
	 * Sets the fancy front-end design of the editor  - originally uses fixed color definitions but now CSS defined by the function setCSSForFormsOnPage()
	 * Frontend
	 *
	 * @return	void
	 *
	 * @note	This is an update to an existing method. The method signature has changed significantly and there are lots of hooks that were not in place before.
	 */
	function setFancyDesign(&$totalWrap, &$fieldTemplate, &$palFieldTemplateHeader, &$palFieldTemplate, &$sectionWrap,& $formOnPageConf, $table='tt_content', $CType='text', $listType='0', $mode='editPanel') {

			// set template variables as empty in order to totally reset them
		$totalWrap = '';
		$fieldTemplate = '';
		$palFieldTemplateHeader = '';
		$palFieldTemplate = '';
		$sectionWrap = '';

			// use hooks to redefine template variables
			// set named key for possible hooks
		if ($table == 'tt_content') { // take account content types
			if ($CType == 'list') { // CType 'list' has many subtypes
				$hookName = 'tt_content_' . $listType;
			} else {
				$hookName = 'tt_content_' . $CType;
				$listType = '0';
			}
		} else {
			$hookName = $table;
		}
		
		if ($hookRef = $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tceforms.php']['frontendForms'][$hookName]) {
			$hookObj= &t3lib_div::getUserObj($hookRef);
			if (is_object($hookObj)) {
				if (method_exists($hookObj, 'setTemplates')) {
					$hookObj->setTemplates($totalWrap, $fieldTemplate, $palFieldTemplateHeader, $palFieldTemplate, $sectionWrap, $formOnPageConf, $table, $CType, $listType, $mode);
				}
				if (method_exists($hookObj, 'setTotalWrap')) {
					$totalWrap = $hookObj->setTotalWrap($totalWrap, $formOnPageConf, $table, $CType, $listType, $mode);
				}
				if (method_exists($hookObj, 'setFieldTemplate')) {
					$fieldTemplate = $hookObj->setFieldTemplate($fieldTemplate, $formOnPageConf, $table, $CType, $listType, $mode);
				}
				if (method_exists($hookObj, 'setPalFieldTemplateHeader')) {
					$palFieldTemplateHeader = $hookObj->setPalFieldTemplateHeader($palFieldTemplateHeader, $formOnPageConf, $table, $CType, $listType, $mode);
				}
				if (method_exists($hookObj, 'setPalFieldTemplate')) {
					$palFieldTemplate = $hookObj->setPalFieldTemplate($palFieldTemplate, $formOnPageConf, $table, $CType, $listType, $mode);
				}
				if (method_exists($hookObj, 'setSectionWrap')) {
					$sectionWrap = $hookObj->setPalFieldTemplate($sectionWrap, $formOnPageConf, $table, $CType, $listType, $mode);
				}
			}
		}

		// if no hooks found user default settings for 'Forms on page' mode or use configurations in TypoScript templates

			// layout for limited field list and full form
		$elementConfig = $formsOnPageConf['formsOnPage.'][$CType . '.'][$listType . '.'];
		if (!$totalWrap) {
			if (!strlen($totalWrap = $this->getTSFieldConf($elementConfig['totalWrap'], $elementConfig['totalWrap.']))) {
				$totalWrap ='<table id="formsOnPage" class="formsOnPage typo3-TCEforms" border="0" cellpadding="0" cellspacing="0">|</table>';
			}
		}
		if (!$fieldTemplate) {
			if (!strlen($fieldTemplate = $this->getTSFieldConf($elementConfig['fieldTemplate'], $elementConfig['fieldTemplate.']))) {
				$fieldTemplate = '<tr class="class-main21 fieldHeader"><td nowrap="nowrap" class="class-main21">###FIELD_HELP_ICON###<b>###FIELD_NAME###</b>###FIELD_HELP_TEXT###</td></tr><tr class="class-main23 field"><td nowrap="nowrap" class="class-main23"><img name="req_###FIELD_TABLE###_###FIELD_ID###_###FIELD_FIELD###" src="clear.gif" width="10" height="10" alt="" /><img name="cm_###FIELD_TABLE###_###FIELD_ID###_###FIELD_FIELD###" src="clear.gif" width="7" height="10" alt="" />###FIELD_ITEM######FIELD_PAL_LINK_ICON###</td></tr>';
			}
		}

			// these works only for full form
		if (!$sectionWrap) {
			if (!strlen($sectionWrap = $this->getTSFieldConf($elementConfig['sectionWrap'], $elementConfig['sectionWrap.']))) {
				$sectionWrap = '<tr class="spaceBefore"><td colspan="2" class="spaceBefore">&nbsp;</td></tr><tr><td colspan="2"><table ###TABLE_ATTRIBS###>###CONTENT###</table></td></tr>';
			}
		}
		if (!$palFieldTemplateHeader) {
			if (!strlen($palFieldTemplateHeader = $this->getTSFieldConf($elementConfig['palFieldTemplateHeader'], $elementConfig['palFieldTemplateHeader.']))) {
				$palFieldTemplateHeader = '<tr class="class-main23 palFieldHeader"><td nowrap="nowrap" class="class-main23"><b>###FIELD_HEADER###</b></td></tr>';
			}
		}
		if (!$palFieldTemplate) {
			if (!strlen($palFieldTemplate = $this->getTSFieldConf($elementConfig['palFieldTemplate'], $elementConfig['palFieldTemplate.']))) {
				$palFieldTemplate = '<tr class="class-main25 palField"><td nowrap="nowrap" class="class-main25">###FIELD_PALETTE###</td></tr>';
			}
		}
	}


	/**
	 * Finds the TypoScript values for a given 'field'. Looks in the main and also in the [file]
	 *
	 * @note	New helper function. Looks like the logic can be cleaned up and we may be able to use some standard TS processing functions.
	*/
	function getTSFieldConf($key, $value) {
		$tsConfig = '';
		if ($table == 'tt_content' && ($key || ($value && $value['file']))) {
			if ($value['file'] && is_file(PATH_site . $value['file'])) {
				$tsConfig =  t3lib_div::getURL(t3lib_div::getIndpEnv('TYPO3_SITE_URL') . $value['file']);
			} else {
				$tsConfig = $key;
			}
		} elseif ($value && $value['file'] && is_file(PATH_site . $value['file'])) {
			$tsConfig =  t3lib_div::getURL(t3lib_div::getIndpEnv('TYPO3_SITE_URL') . $value['file']);
		} elseif ($key) {
			$tsConfig = $key;
		}
		
		return $tsConfig;
	}

	/**
	 * Creates a palette (collection of secondary options).
	 *
	 * @param	string		The table name
	 * @param	array		The row array
	 * @param	string		The palette number/pointer
	 * @param	string		Header string for the palette (used when in-form). If not set, no header item is made.
	 * @param	string		Optional alternative list of fields for the palette
	 * @param	string		Optional Link text for activating a palette (when palettes does not have another form element to belong to).
	 * @return	string		HTML code.
	 *
	 * @note	This is a new method that overrides t3lib_tceforms and is much more complex than the parent.
	 */
	function getPaletteFields($table, array $row, $palette, $header='', $itemList='', $collapsedHeader='') {

		$this->init();
		$formOnPageConf = $GLOBALS['TSFE']->tmpl->setup[$table . '.']['stdWrap.']['editPanel.']; // configuration for edit panels

		if (isset($row['CType'])) {
			$CType = $row['CType'];
		}
		if (isset($row['list_type'])) {
			$listType=$row['list_type'];
		}
			
			// @note This code all looks familiar.
			// set named key for possible hooks
		if ($table=='tt_content') { // take account content types
			if($CType == 'list') { // CType 'list' has many subtypes
				$hookName = 'tt_content_' . $listType . '_' . $palette;
			} else {
				$hookName = 'tt_content_' . $CType . '_' . $palette;
				$listType = '0';
			}
		} else {
			$hookName = $table . '_' . $palette;
		}
		
		$altPalFieldTemplate = '';
		$altPalFieldTemplateHeader = '';
		if ($hookRef = $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tceforms.php']['frontendForms'][$hookName]) {
			$hookObj= &t3lib_div::getUserObj($hookRef);
			if (is_object($hookObj)) {
				if (method_exists($hookObj, 'setAltPalFieldTemplateHeader')) {
					$altPalFieldTemplateHeader = $hookObj->setAltPalFieldTemplateHeader($palFieldTemplateHeader, $formOnPageConf, $table, $CType, $listType, $palette);
				}
				if (method_exists($hookObj, 'setAltPalFieldTemplate')) {
					$altPalFieldTemplate = $hookObj->setAltPalFieldTemplate($palFieldTemplate, $formOnPageConf, $table, $CType, $listType, $palette);
				}
			}
		}

		$elementConfig = $formOnPageConf['formsOnPage.'][$CType . '.'][$listType . '.'];
		if (!strlen($altPalFieldTemplateHeader)) {
			$altPalFieldTemplateHeader = $this->getTSFieldConf($elementConfig['palFieldTemplateHeader.'][$palette], $elementConfig['palFieldTemplateHeader.'][$palette . '.']);
		}
		if (!strlen($altPalFieldTemplate)) {
			$altPalFieldTemplate = $this->getTSFieldConf($elementConfig['palFieldTemplate.'][$palette], $elementConfig[$palette . '.']);
		}
		$palFieldTemplateHeader = strlen($altPalFieldTemplateHeader) ? $altPalFieldTemplateHeader : $this->palFieldTemplateHeader;
		$palFieldTemplate = strlen($altPalFieldTemplate) ? $altPalFieldTemplate : $this->palFieldTemplate;

		if ($table=='tt_content' && ($formOnPageConf['formsOnPage.'][$row['CType'] . '.']['0.']['advFieldPalettes'] || $formOnPageConf['formsOnPage.'][$row['CType'] . '.'][$row['list_type'] . '.']['advFieldPalettes'])) {
			if($row['CType'] == 'list') {
				$advFields = t3lib_div::trimExplode(',', strtolower($formOnPageConf['formsOnPage.'][$row['CType'] . '.'][$row['list_type'] . '.']['advFieldPalettes']), true);
			} else {
				$advFields = t3lib_div::trimExplode(',', strtolower($formOnPageConf['formsOnPage.'][$row['CType'] . '.']['0.']['advFieldPalettes']), true);
			}
		} else {
			$advFields = t3lib_div::trimExplode(',', $formOnPageConf['formsOnPage.']['advFieldPalettes'], true);
		}
		
		if (isset($advFields) && is_array($advFields)) {
			$advFields = array_flip($advFields);
		}
		
		if (isset($advFields[$palette])) {
			$palFieldTemplateHeader = '<tr name="advField" class="advField" style="display:none"><td class="palettefields"><table class="cellpadding="0" cellspacing="0" border="0" width="100%">' . $palFieldTemplateHeader;
			$palFieldTemplate = $palFieldTemplate . '</table></td></tr>';
		}

		// @todo	Get rid of this return in the middle of the method!
		if (!$this->doPrintPalette) {
			return '';
		}

		$out = '';
		$palParts = array();
		t3lib_div::loadTCA($table);

			// Getting excludeElements, if any.
		if (!is_array($this->excludeElements))	{
			$this->excludeElements = $this->getExcludeElements($table, $row, $this->getRTypeNum($table, $row));
		}

			// Render the palette TCEform elements.
		if ($GLOBALS['TCA'][$table] && (is_array($GLOBALS['TCA'][$table]['palettes'][$palette]) || $itemList))	{
			$itemList = $itemList ? $itemList : $GLOBALS['TCA'][$table]['palettes'][$palette]['showitem'];
			if ($itemList)	{
				$fields = t3lib_div::trimExplode(',',$itemList,1);
				reset($fields);
				while(list(,$fieldInfo)=each($fields))	{
					$parts = t3lib_div::trimExplode(';',$fieldInfo);
					$theField = $parts[0];

					if (!in_array($theField,$this->excludeElements) && $GLOBALS['TCA'][$table]['columns'][$theField])	{
						$this->palFieldArr[$palette][] = $theField;
						if ($this->isPalettesCollapsed($table,$palette)) {
							$this->hiddenFieldListArr[] = $theField;
						}

						$part = $this->getSingleField($table,$theField,$row,$parts[1],1,'',$parts[2]);
						if (is_array($part)) {
							$palParts[] = $part;
						}
					}
				}
			}
		}
		
			// @note	This is basically where the original function starts.
			// Put palette together if there are fields in it:
		if (count($palParts)) {
			if ($header) {
				$out .= $this->intoTemplate(
							array('HEADER' => htmlspecialchars($header)),
							$palFieldTemplateHeader
						);
			}

			$out .= $this->intoTemplate(
						array('PALETTE' => $this->printPalette($palParts)),
						$palFieldTemplate
					);
		}

			// If a palette is collapsed (not shown in form, but in top frame instead) AND a collapse header string is given, then make that string a link to activate the palette.
		if ($this->isPalettesCollapsed($table, $palette) && $collapsedHeader) {
			$pC = $this->intoTemplate(
					array('PALETTE' => $this->wrapOpenPalette('<img' . t3lib_iconWorks::skinImg($this->imagePath, 'options.gif','') . ' border="0" title="' . htmlspecialchars($this->getLL('l_moreOptions')) . '" align="top" alt="" /><strong>' . $collapsedHeader . '</strong>', $table, $row, $palette)),
					$palFieldTemplate
				);
			$out .= $pC;
		}
		
		return $out;
	}


	/**
	 * Returns the form HTML code for a database table field.
	 *
	 * @param	string		The table name
	 * @param	string		The field name
	 * @param	array		The record to edit from the database table.
	 * @param	string		Alternative field name label to show.
	 * @param	boolean		Set this if the field is on a palette (in top frame), otherwise not. (if set, field will render as a hidden field).
	 * @param	string		The "extra" options from "Part 4" of the field configurations found in the "types" "showitem" list. Typically parsed by $this->getSpecConfFromString() in order to get the options as an associative array.
	 * @param	integer		The palette pointer.
	 * @return	mixed		String (normal) or array (palettes)
	 *
	 * @note	New method that extends existing method in t3lib_tceforms.  Looks to be pretty similar.
	 */
	function getSingleField($table,$field,$row,$altName='',$palette=0,$extra='',$pal=0) {
		// @note Start new code.
		global $BE_USER,$myPOST;

		$this->init();
		$formOnPageConf=$GLOBALS['TSFE']->tmpl->setup[$table . '.']['stdWrap.']['editPanel.']; // configuration for edit panels
		if($table=='tt_content' && ($formOnPageConf['formsOnPage.'][$row['CType'] . '.']['0.']['advFields'] || $formOnPageConf['formsOnPage.'][$row['CType'] . '.'][$row['list_type'] . '.']['advFields'])) {
			if($row['CType']=='list')
				$advFields = t3lib_div::trimExplode(',',strtolower($formOnPageConf['formsOnPage.'][$row['CType'] . '.'][$row['list_type'] . '.']['advFields']),1);
			else
				$advFields = t3lib_div::trimExplode(',',strtolower($formOnPageConf['formsOnPage.'][$row['CType'] . '.']['0.']['advFields']),1);
		}
		else
			$advFields = t3lib_div::trimExplode(',',strtolower($formOnPageConf['formsOnPage.']['advFields']),1);

		if(isset($advFields) && is_array($advFields))
			$advFields = array_flip($advFields);

		$out = '';
		$PA = array();
		$PA['altName'] = $altName;
		$PA['palette'] = $palette;
		$PA['extra'] = $extra;
		$PA['pal'] = $pal;

		if(isset($row['CType']))
			$CType=$row['CType'];
		if(isset($row['list_type']))
			$listType=$row['list_type'];

		/* use hooks to redefine template variables */
		// set named key for possible hooks
		if($table=='tt_content') { // take account content types
			if($CType=='list') // CType 'list' has much subtypes
				$hookName='tt_content_' . $listType . '_' . $field;
			else	{
				$hookName='tt_content_' . $CType;
				$listType='0';
				}
			}
		else
			$hookName=$table . '_' . $field;

		if ($hookRef = $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tceforms.php']['frontendForms'][$hookName]) {
			$hookObj= &t3lib_div::getUserObj($hookRef);
			if (is_object($hookObj)) {
				if (method_exists($hookObj, 'setAltFieldTemplate'))
					$altTemplate = $hookObj->setAltFieldTemplate($fieldTemplate,$formOnPageConf,$table,$field,$CType,$listType,$field);
			}
		}

		if(!$altTemplate) {
			if($table=='tt_content' && ($formOnPageConf['formsOnPage.'][$CType . '.'][$listType . '.']['fieldTemplate.'][$field] || $formOnPageConf['formsOnPage.'][$CType . '.'][$listType . '.']['fieldTemplate.'][$field . '.']['file'])) {
				if($formOnPageConf['formsOnPage.'][$CType . '.'][$listType . '.']['fieldTemplate.'][$field . '.']['file'] && is_file(PATH_site .$formOnPageConf['formsOnPage.'][$CType . '.'][$listType . '.']['fieldTemplate.'][$field . '.']['file']))
					$altTemplate =  t3lib_div::getURL(t3lib_div::getIndpEnv('TYPO3_SITE_URL') .$formOnPageConf['formsOnPage.'][$CType . '.'][$listType . '.']['fieldTemplate.'][$field . '.']['file']);
				else
					$altTemplate = $formOnPageConf['formsOnPage.'][$CType . '.'][$listType . '.']['fieldTemplate.'][$field];
				}
			elseif($formOnPageConf['formsOnPage.']['fieldTemplate.'][$field] || $formOnPageConf['formsOnPage.']['fieldTemplate.'][$field . '.']['file']) {
				if($formOnPageConf['formsOnPage.']['fieldTemplate.'][$field . '.']['file'] && is_file(PATH_site .$formOnPageConf['formsOnPage.']['fieldTemplate.'][$field . '.']['file']))
					$altTemplate =  t3lib_div::getURL(t3lib_div::getIndpEnv('TYPO3_SITE_URL') .$formOnPageConf['formsOnPage.']['fieldTemplate.'][$field . '.']['file']);
				else
					$altTemplate = $formOnPageConf['formsOnPage.']['fieldTemplate.'][$field];
				}
			elseif(isset($advFields[strtolower($field)]) && $myPOST['mode']!='editIcons') // not for edit icons - needed field might be hidden!
				$altTemplate = '<tr name="advField" class="advField" style="display:none"><td class="normalfields"><table class="cellpadding="0" cellspacing="0" border="0" width="100%"><tr class="class-main21 fieldHeader"><td nowrap="nowrap" class="class-main21">###FIELD_HELP_ICON###<b>###FIELD_NAME###</b>###FIELD_HELP_TEXT###</td></tr><tr class="class-main23 field"><td nowrap="nowrap" class="class-main23">###FIELD_ITEM######FIELD_PAL_LINK_ICON###</td></tr></table></td></tr>';
			}

			// @note End new code

			// Make sure to load full $GLOBALS['TCA'] array for the table:
		t3lib_div::loadTCA($table);
		
			// Get the TCA configuration for the current field:
		$PA['fieldConf'] = $GLOBALS['TCA'][$table]['columns'][$field];
		$PA['fieldConf']['config']['form_type'] = $PA['fieldConf']['config']['form_type'] ? $PA['fieldConf']['config']['form_type'] : $PA['fieldConf']['config']['type'];	// Using "form_type" locally in this script

		// @note Missing assignment for skipThisField

			// Now, check if this field is configured and editable (according to excludefields + other configuration)
		if (is_array($PA['fieldConf']) &&
				(!$PA['fieldConf']['exclude'] || $BE_USER->check('non_exclude_fields',$table.':'.$field)) &&
				$PA['fieldConf']['config']['form_type']!='passthrough' &&
				($this->RTEenabled || !$PA['fieldConf']['config']['showIfRTE']) &&
				(!$PA['fieldConf']['displayCond'] || $this->isDisplayCondition($PA['fieldConf']['displayCond'],$row)) &&
				(!$GLOBALS['TCA'][$table]['ctrl']['languageField'] || strcmp($PA['fieldConf']['l10n_mode'],'exclude') || $row[$GLOBALS['TCA'][$table]['ctrl']['languageField']]<=0)
			)	{

				// Fetching the TSconfig for the current table/field. This includes the $row which means that
			$PA['fieldTSConfig'] = $this->setTSconfig($table,$row,$field);

				// If the field is NOT disabled from TSconfig (which it could have been) then render it
			if (!$PA['fieldTSConfig']['disabled'])	{

					// Init variables:
				$PA['itemFormElName']=$this->prependFormFieldNames.'['.$table.'][' . $row['uid'].'][' . $field . ']';		// Form field name
				$PA['itemFormElName_file']=$this->prependFormFieldNames_file.'[' . $table.'][' . $row['uid'].'][' . $field . ']';	// Form field name, in case of file uploads
				$PA['itemFormElValue']=$row[$field];		// The value to show in the form field.
				
				// @note	Missing assignment of itemFormElID and read-only default language.
				
				// @note	This should work with frontend editing now.
				/*
				the following doesn't work with 'Forms on page'

					// Create a JavaScript code line which will ask the user to save/update the form due to changing the element. This is used for eg. "type" fields and others configured with "requestUpdate"
				if (
						(($GLOBALS['TCA'][$table]['ctrl']['type'] && !strcmp($field,$GLOBALS['TCA'][$table]['ctrl']['type'])) ||
						($GLOBALS['TCA'][$table]['ctrl']['requestUpdate'] && t3lib_div::inList($GLOBALS['TCA'][$table]['ctrl']['requestUpdate'],$field)))
						&& !$BE_USER->uc['noOnChangeAlertInTypeFields'])	{
					$alertMsgOnChange = 'if (confirm('.$GLOBALS['LANG']->JScharCode($this->getLL('m_onChangeAlert')).') && TBE_EDITOR_checkSubmit(-1)){ TBE_EDITOR_submitForm() };';
				} else {$alertMsgOnChange='';}
				*/

					// @note	New code!
					// Create a JavaScript code line which will ask saving content before changing CType
				if (
						(($GLOBALS['TCA'][$table]['ctrl']['type'] && !strcmp($field,$GLOBALS['TCA'][$table]['ctrl']['type'])) ||
						($GLOBALS['TCA'][$table]['ctrl']['requestUpdate'] && t3lib_div::inList($GLOBALS['TCA'][$table]['ctrl']['requestUpdate'],$field)))
						&& !$BE_USER->uc['noOnChangeAlertInTypeFields'])	{
					$alertMsgOnChange = 'if (confirm(' . $GLOBALS['LANG']->JScharCode($GLOBALS['LANG']->sL('LLL:EXT:feeditadvanced/locallang.xml:alertChange')).') && TBE_EDITOR_checkSubmit(-1)){ TBE_EDITOR.submitForm(); };';
				} else {$alertMsgOnChange='';}

					// Render as a hidden field?
				if (in_array($field,$this->hiddenFieldListArr))	{
					$this->hiddenFieldAccum[]='<input type="hidden" name="' . $PA['itemFormElName'].'" value="' . htmlspecialchars($PA['itemFormElValue']).'" />';
				} else {	// Render as a normal field:

						// If the field is NOT a palette field, then we might create an icon which links to a palette for the field, if one exists.
					if (!$PA['palette'])	{
						if ($PA['pal'] && $this->isPalettesCollapsed($table,$PA['pal']))	{
							list($thePalIcon,$palJSfunc) = $this->wrapOpenPalette('<img' . t3lib_iconWorks::skinImg($this->imagePath,'options.gif','width="18" height="16"').' border="0" title="' . htmlspecialchars($this->getLL('l_moreOptions')).'" alt="" />',$table,$row,$PA['pal'],1);
						} else {
							$thePalIcon = '';
							$palJSfunc = '';
						}
					}
						// onFocus attribute to add to the field:
					$PA['onFocus'] = ($palJSfunc && !$BE_USER->uc['dontShowPalettesOnFocusInAB']) ? ' onfocus="' . htmlspecialchars($palJSfunc).'"' : '';

						// Find item
					$item='';
					$PA['label'] = $PA['altName'] ? $PA['altName'] : $PA['fieldConf']['label'];
					$PA['label'] = $this->sL($PA['label']);
					// @note	Missing come code for TSConfig definition of labels. Was probably added by the core later on.
					
						// JavaScript code for event handlers:
					$PA['fieldChangeFunc']=array();
					$PA['fieldChangeFunc']['TBE_EDITOR_fieldChanged'] = "TBE_EDITOR_fieldChanged('".$table."','".$row['uid']."','".$field."','".$PA['itemFormElName']."');";
					$PA['fieldChangeFunc']['alert']=$alertMsgOnChange;

					// @note	Missing some code for inline records. Probably added later.

						// Based on the type of the item, call a render function:
					$item = $this->getSingleField_SW($table,$field,$row,$PA);
					
					
					// @note	Missing some language specific checks.
					
						// Add language + diff
					$item = $this->renderDefaultLanguageContent($table,$field,$row,$item);
					$item = $this->renderDefaultLanguageDiff($table,$field,$row,$item);

						// If the record has been saved and the "linkTitleToSelf" is set, we make the field name into a link, which will load ONLY this field in alt_doc.php
					$PA['label'] = t3lib_div::deHSCentities(htmlspecialchars($PA['label']));
					if (t3lib_div::testInt($row['uid']) && $PA['fieldTSConfig']['linkTitleToSelf'])	{
							// @note	This URL is constructed differently. Any idea why?
						$lTTS_url = $this->backPath.'alt_doc.php?edit[' . $table.'][' . $row['uid'].']=edit&columnsOnly=' . $field.
									($PA['fieldTSConfig']['linkTitleToSelf.']['returnUrl'] ? '&returnUrl=' . rawurlencode($this->thisReturnUrl()) : '');
						$PA['label'] = '<a href="' . htmlspecialchars($lTTS_url).'">' . $PA['label'].'</a>';
					}

						// Create output value:
					if ($PA['fieldConf']['config']['form_type']=='user' && $PA['fieldConf']['config']['noTableWrapping'])	{
						$out = $item;
					} elseif ($PA['palette'])	{
							// Array:
						$out=array(
							'NAME'=>$PA['label'],
							'ID'=>$row['uid'],
							'FIELD'=>$field,
							'TABLE'=>$table,
							'ITEM'=>$item,
							'HELP_ICON' => $this->helpTextIcon($table,$field,1)
						);
						$out = $this->addUserTemplateMarkers($out,$table,$field,$row,$PA);
					} else {
							// String:
						$out=array(
							'NAME'=>$PA['label'],
							'ITEM'=>$item,
							'TABLE'=>$table,
							'ID'=>$row['uid'],
							'HELP_ICON'=>$this->helpTextIcon($table,$field),
							'HELP_TEXT'=>$this->helpText($table,$field),
							'PAL_LINK_ICON'=>$thePalIcon,
							'FIELD'=>$field
						);
						$out = $this->addUserTemplateMarkers($out,$table,$field,$row,$PA);
							// String:
						$out=$this->intoTemplate($out,$altTemplate);
					}
				}
			} else $this->commentMessages[] = $this->prependFormFieldNames.'[' . $table.'][' . $row['uid'].'][' . $field . ']: Disabled by TSconfig';
		}
			// Return value (string or array)
		return $out;
	}

	/**
	 * Based on the $table and $row of content, this displays the complete TCEform for the record.
	 * The input-$row is required to be preprocessed if necessary by eg. the t3lib_transferdata class. For instance the RTE content should be transformed through this class first.
	 *
	 * @param	string		The table name
	 * @param	array		The record from the table for which to render a field.
	 * @param	integer		Depth level
	 * @return	string		HTML output
	 * @see getSoloField()
	 *
	 * New function that extends existing method in t3lib_tceforms.  Looks to be pretty similar.
	 */
	function getMainFields($table, $row, $depth=0) {
			// @note	Forcing enableTabMenu is new.
		$this->enableTabMenu=true;
		$this->renderDepth=$depth;

			// Init vars:
		$out_array = array(array());
		$out_array_meta = array(array(
			'title' => $this->getLL('l_generalTab')
		));

		$out_pointer=0;
		$out_sheet=0;
		$this->palettesRendered=array();
		$this->palettesRendered[$this->renderDepth][$table]=array();

			// Hook: getMainFields_preProcess (requested by Thomas Hempel for use with the "dynaflex" extension)
		foreach ($this->hookObjectsMainFields as $hookObj)	{
			if (method_exists($hookObj,'getMainFields_preProcess'))	{
				$hookObj->getMainFields_preProcess($table,$row,$this);
			}
		}
		if ($GLOBALS['TCA'][$table])	{
				// Load the full TCA for the table.
			t3lib_div::loadTCA($table);
			
			// @note No check for dividers2tabs
			
				// Load the description content for the table.
			if ($this->edit_showFieldHelp || $this->doLoadTableDescr($table))	{
				$GLOBALS['LANG']->loadSingleTableDescription($table);
			}
				// Get the current "type" value for the record.
			$typeNum = $this->getRTypeNum($table,$row);

				// Find the list of fields to display:
			if ($GLOBALS['TCA'][$table]['types'][$typeNum])	{
				$itemList = $GLOBALS['TCA'][$table]['types'][$typeNum]['showitem'];
				if ($itemList)	{	// If such a list existed...
					// Explode the field list and possibly rearrange the order of the fields, if configured for
					$fields = t3lib_div::trimExplode(',', $itemList, 1);
					if ($this->fieldOrder)	{
						$fields = $this->rearrange($fields);
					}

					// Get excluded fields, added fiels and put it together:
					$excludeElements = $this->excludeElements = $this->getExcludeElements($table, $row, $typeNum);
					$fields = $this->mergeFieldsWithAddedFields($fields, $this->getFieldsToAdd($table, $row, $typeNum));

					// If TCEforms will render a tab menu in the next step, push the name to the tab stack:
					$tabIdentString = '';
					$tabIdentStringMD5 = '';
					if (strstr($itemList, '--div--') !== false && $this->enableTabMenu && $GLOBALS['TCA'][$table]['ctrl']['dividers2tabs']) {
						$tabIdentString = 'TCEforms:' . $table . ':' . $row['uid'];
						$tabIdentStringMD5 = $this->getDynTabMenuId($tabIdentString);
							// Remember that were currently working on the general tab:
						if (isset($fields[0]) && strpos($fields[0], '--div--') !== 0) {
							$this->pushToDynNestedStack('tab', $tabIdentStringMD5 . '-1');
						}
					}

					// Traverse the fields to render:
					$cc=0;
					foreach($fields as $fieldInfo)	{
						// Exploding subparts of the field configuration:
						$parts = explode(';',$fieldInfo);
						
						
						// @note	Removed color_style_parts code
						// Getting the style information out:
						//$color_style_parts = t3lib_div::trimExplode('-',$parts[4]);
													// Render the field:
						$theField = $parts[0];
						if (!in_array($theField,$excludeElements))	{
							if ($GLOBALS['TCA'][$table]['columns'][$theField])	{
								$sFieldPal='';

								if ($parts[2] && !isset($this->palettesRendered[$this->renderDepth][$table][$parts[2]]))	{
									$sFieldPal=$this->getPaletteFields($table,$row,$parts[2]);
									$this->palettesRendered[$this->renderDepth][$table][$parts[2]] = 1;
								}
								$sField = $this->getSingleField($table,$theField,$row,$parts[1],0,$parts[3],$parts[2]);
								if ($sField)	{ $sField.= $sFieldPal; }

								$out_array[$out_sheet][$out_pointer].= $sField;
							} elseif ($theField=='--div--')	{
								if ($cc>0)	{
									$out_array[$out_sheet][$out_pointer].=$this->getDivider();

									if ($this->enableTabMenu && $GLOBALS['TCA'][$table]['ctrl']['dividers2tabs'])	{
										$this->wrapBorder($out_array[$out_sheet],$out_pointer);
										// Remove last tab entry from the dynNestedStack:
										$out_sheet++;
										// Remove the previous sheet from stack (if any):																							// Remove the previous sheet from stack (if any):
										$this->popFromDynNestedStack('tab', $tabIdentStringMD5 . '-' . ($out_sheet));
										// Remember on which sheet we're currently working:
										$this->pushToDynNestedStack('tab', $tabIdentStringMD5 . '-' . ($out_sheet+1));
										$out_array[$out_sheet] = array();
										$out_array_meta[$out_sheet]['title'] = $this->sL($parts[1]);
									 	// Register newline for Tab
 										$out_array_meta[$out_sheet]['newline'] = ($parts[2] == "newline");
									}
								} else {	// Setting alternative title for "General" tab if "--div--" is the very first element.
									$out_array_meta[$out_sheet]['title'] = $this->sL($parts[1]);
										// Only add the first tab to the dynNestedStack if there are more tabs:
									if ($tabIdentString && strpos($itemList, '--div--', strlen($fieldInfo))) {
										$this->pushToDynNestedStack('tab', $tabIdentStringMD5 . '-1');
									}
								}
							} elseif($theField=='--palette--') {
								if ($parts[2] && !isset($this->palettesRendered[$this->renderDepth][$table][$parts[2]]))	{
										// render a 'header' if not collapsed
									if ($GLOBALS['TCA'][$table]['palettes'][$parts[2]]['canNotCollapse'] AND $parts[1]) {
										$out_array[$out_sheet][$out_pointer].=$this->getPaletteFields($table,$row,$parts[2],$this->sL($parts[1]));
									} else {
										$out_array[$out_sheet][$out_pointer].=$this->getPaletteFields($table,$row,$parts[2],'','',$this->sL($parts[1]));
									}
									$this->palettesRendered[$this->renderDepth][$table][$parts[2]] = 1;
								}
							}
						}

						$cc++;
					}
				}
			}
		}

			// Hook: getMainFields_postProcess (requested by Thomas Hempel for use with the "dynaflex" extension)
		foreach ($this->hookObjectsMainFields as $hookObj)	{
			if (method_exists($hookObj,'getMainFields_postProcess'))	{
				$hookObj->getMainFields_postProcess($table,$row,$this);
			}
		}

			// Wrapping a border around it all:
		$this->wrapBorder($out_array[$out_sheet],$out_pointer);

			// Resetting styles:
		$this->resetSchemes();

			// Rendering Main palettes, if any
		$mParr = t3lib_div::trimExplode(',',$GLOBALS['TCA'][$table]['ctrl']['mainpalette']);
		$i = 0;
		if (count($mParr))	{
			foreach ($mParr as $mP)	{
				if (!isset($this->palettesRendered[$this->renderDepth][$table][$mP]))	{
					$temp_palettesCollapsed=$this->palettesCollapsed;
					$this->palettesCollapsed=0;
					$label = ($i==0 ? $this->getLL('l_generalOptions') : $this->getLL('l_generalOptions_more'));
					$out_array[$out_sheet][$out_pointer].=$this->getPaletteFields($table,$row,$mP,$label);
					$this->palettesCollapsed=$temp_palettesCollapsed;
					$this->palettesRendered[$this->renderDepth][$table][$mP] = 1;
				}
				$this->wrapBorder($out_array[$out_sheet],$out_pointer);
				$i++;
				if ($this->renderDepth)	{
					$this->renderDepth--;
				}
			}
		}
		// Return the imploded $out_array:
		if ($out_sheet>0)	{	// There were --div-- dividers around...

			// Create parts array for the tab menu:
			$parts = array();
			foreach ($out_array as $idx => $sheetContent)	{
				$sContent = implode('', $sheetContent);
				if ($sContent) {
					// Wrap content (row) with table-tag, otherwise tab/sheet will be disabled (see getdynTabMenu() )
					$sContent = '<table border="0" cellspacing="0" cellpadding="0" width="100%">' . $sContent . '</table>';
 					}
				$parts[$idx] = array(
					'label' => $out_array_meta[$idx]['title'],
					'content' => $sContent,
					'newline' => $out_array_meta[$idx]['newline'], 	// Newline for this tab/sheet
				);
				}
			if (count($parts) > 1) {
					// Unset the current level of tab menus:
				$this->popFromDynNestedStack('tab', $tabIdentStringMD5 . '-' . ($out_sheet+1));
				
					// @note	Additional parameters on getDynTabMenu!
				$output = $this->getDynTabMenu($parts, $tabIdentString,0,FALSE,50,1,FALSE);
			} else {
					// If there is only one tab/part there is no need to wrap it into the dynTab code
				$output = isset($parts[0]) ? trim($parts[0]['content']) : '';
			}

			$output = '
				<tr>
					<td colspan="2">
					' . $output . '
					</td>
				</tr>';

		} else {
				// Only one, so just implode:
			$output = implode('',$out_array[$out_sheet]);
		}
		return $output;
	}


	/**
	* the purpose is automatic change some TCA, when used the frontend editing mode.
	* made own function to work also both EDITICON and EDITPANEL objects
	*
	* @note		New helper method.
	*/
	function changeTCAforFormsOnPage($TCA,$table) {
		$openParams='height=350,width=580,status=0,menubar=0,scrollbars=1';

		foreach($TCA[$table]['columns'] as $field=>$value) {
			$wizards = $TCA[$table]['columns'][$field]['config']['wizards'];
			if (isset($wizards['RTE'])) {
				$wizards['RTE'] = null;
			}
			if (isset($wizards['add']) && is_array($wizards['add'])) {
				$wizards['add']['type'] = 'popup';
				$wizards['add']['JSopenParams'] = $openParams;
			}
			if (isset($wizards['list']) && is_array($wizards['list'])) {
				$wizards['list']['type'] = 'popup';
				$wizards['list']['JSopenParams'] = $openParams;
			}
			if (isset($wizards['edit']) && is_array($wizards['edit'])) {
				$wizards['edit']['type'] = 'popup';
				$wizards['edit']['JSopenParams'] = $openParams;
			}
		}
	}

/** tabbed menu functions **********************************************/
	/**
	* these functions are not in older versions of typo3
	* added for compatibility reasons only
	*
	* @note		Can probably remove.
	*/
	/**
	 * Creates the id for dynTabMenus.
	 *
	 * @param	string		$identString: Identification string. This should be unique for every instance of a dynamic menu!
	 * @return	string		The id with a short MD5 of $identString and prefixed "DTM-", like "DTM-2e8791854a"
	 *
	 * @note	Copied from typo3/template
	 */
	function getDynTabMenuId($identString) {
		$id = 'DTM-' . t3lib_div::shortMD5($identString);
		return $id;
	}
	/**
	 * Push a new element to the dynNestedStack. Thus, every object know, if it's
	 * nested in a tab or IRRE level and in which order this was processed.
	 *
	 * @param	string		$type: Type of the level, e.g. "tab" or "inline"
	 * @param	string		$ident: Identifier of the level
	 * @return	void
	 *
	 * @note	Copied from t3lib_tceforms
	 */
	function pushToDynNestedStack($type, $ident) {
		$this->dynNestedStack[] = array($type, $ident);
	}
	/**
	 * Remove an element from the dynNestedStack. If $type and $ident
	 * are set, the last element will only be removed, if it matches
	 * what is expected to be removed.
	 *
	 * @param	string		$type: Type of the level, e.g. "tab" or "inline"
	 * @param	string		$ident: Identifier of the level
	 * @return	void
	 *
	 * @note	Copied from t3lib_tceforms
	 */
	function popFromDynNestedStack($type=null, $ident=null) {
		if ($type!=null && $ident!=null) {
			$last = end($this->dynNestedStack);
			if ($type==$last[0] && $ident==$last[1]) {
				array_pop($this->dynNestedStack);
			}
		} else {
			array_pop($this->dynNestedStack);
		}
	}

/*********** function getDynTabMenu starts *************************************/
	 /**
	 * Creates a DYNAMIC tab-menu where the tabs are switched between with DHTML.
	 * Should work in MSIE, Mozilla, & Opera.
	 *
	 * @param	array		Numeric array where each entry is an array in itself with associative keys: "label" contains the label for the TAB, "content" contains the HTML content that goes into the div-layer of the tabs content. "description" contains description text to be shown in the layer. "linkTitle" is short text for the title attribute of the tab-menu link (mouse-over text of tab). "stateIcon" indicates a standard status icon (see ->icon(), values: -1, 1, 2, 3). "icon" is an image tag placed before the text.
	 * @param	string		Identification string. This should be unique for every instance of a dynamic menu!
	 * @param	integer		If "1", then enabling one tab does not hide the others - they simply toggles each sheet on/off. This makes most sense together with the $foldout option. If "-1" then it acts normally where only one tab can be active at a time BUT you can click a tab and it will close so you have no active tabs.
	 * @param	boolean		If set, the tabs are rendered as headers instead over each sheet. Effectively this means there is no tab menu, but rather a foldout/foldin menu. Make sure to set $toggle as well for this option.
	 * @param	integer		Character limit for a new row.
	 * @param	boolean		If set, tab table cells are not allowed to wrap their content
	 * @param	boolean		If set, the tabs will span the full width of their position
	 * @param	integer		Default tab to open (for toggle <=0). Value corresponds to integer-array index + 1 (index zero is "1", index "1" is 2 etc.). A value of zero (or something non-existing) will result in no default tab open.
	 * @return	string		JavaScript section for the HTML header.
	 */
	function getDynTabMenu($menuItems,$identString,$toggle=0,$foldout=FALSE,$newRowCharLimit=50,$noWrap=1,$fullWidth=FALSE,$defaultTabIndex=1)	{
		$content = '';

		if (is_array($menuItems))	{

				// Init:
			$options = array(array());
			$divs = array();
			$JSinit = array();

			$id = $this->getDynTabMenuId($identString);
			
			// @note	Different CSS definition for nowrap.
			$noWrap = $noWrap ? ' style="white-space: nowrap;"' : '';

			// Traverse menu items
			$c=0;
			$tabRows=0;
			$titleLenCount = 0;
			foreach($menuItems as $index => $def) {
				$index+=1;	// Need to add one so checking for first index in JavaScript is different than if it is not set at all.

				// Switch to next tab row if needed
				if (!$foldout && ($titleLenCount>$newRowCharLimit || ($def['newline'] === true && $titleLenCount > 0)))	{	// 50 characters is probably a reasonable count of characters before switching to next row of tabs.
					$titleLenCount=0;
					$tabRows++;
					$options[$tabRows] = array();
				}

// @todo @important Dave -- (old problem) for some reason, toggle=1 is being passed and it does not activate or draw the flexform first tab
$toggle = 0;

				if ($toggle==1)	{
					$onclick = 'this.blur(); DTM_toggle("' . $id . '","' . $index . '"); return false;';
				} else {
					$onclick = 'this.blur(); DTM_activate("' . $id . '","' . $index . '", ' . ($toggle<0 ? 1 : 0) . '); return false;';
				}
				
					// @note	Similar logic, but reversed
				$isNonEmpty = strcmp(trim($def['content']),'');
				// "Removes" empty tabs
				if (!$isNonEmpty && $dividers2tabs == 1) {
					continue;
				}
				
				// @note	New code
				$isActive = strcmp(trim($def['content']),'');
				$startTable='<table class="tabTable" cellspacing="0" cellpadding="0" border="0"><tr><td class="left"><div style="width:5px">&nbsp;</div></td><td class="middle">';
				$endTable = '</td><td class="right"><div style="width:5px">&nbsp;</div></td></tr></table>';

				if (!$foldout)	{
					// Create TAB cell:
					$options[$tabRows][] = '
							<td class="' . ($isActive ? 'tab' : 'disabled') . '" id="' . $id . '-' . $index . '-MENU"' . $noWrap.$mouseOverOut . '>' .
							($isActive ? $startTable . '<a href="#" onclick="' . htmlspecialchars($onclick) . '"' . ($def['linkTitle'] ? ' title="' . htmlspecialchars($def['linkTitle']) . '"':'') . '>' : '').
							$def['icon'].
							($def['label'] ? htmlspecialchars($def['label']) : '<span class="space"></span>');
							#$this->icons($def['stateIcon'],''). - we dont't need icons here - taken off
					$options[$tabRows][] .=
							($isActive ? '</a>' . $endTable :'').
							'</td>'; // off $startTable.'<span class="disabled">' + '</span>' . $endTable).'
					$titleLenCount+= strlen($def['label']);
				}
				else {
					// Create DIV layer for content:
					if(empty($divs)) $extraClass=" firstItem";
					$divs[] = '
						<div class="' . ($isActive ? 'tab' : 'disabled').$extraClass . '" id="' . $id . '-' . $index . '-MENU"' . $mouseOverOut . '>' .
							($isActive ? $startTable . '<a href="#" onclick="' . htmlspecialchars($onclick) . '"' . ($def['linkTitle'] ? ' title="' . htmlspecialchars($def['linkTitle']) . '"':'') . '>' : $startTable . '<span class="disabled">').
							$def['icon'].
							($def['label'] ? htmlspecialchars($def['label']) : '<span class="space"></span>').
							($isActive ? '</a>' . $endTable :'</span>' . $endTable) . '</div>';
				}

				// Create DIV layer for content:
				$divs[] = '
						<div style="display: none;" id="' . $id . '-' . $index . '-DIV" class="c-tablayer' . $extraClass . '">' .
							($def['description'] ? '<p class="c-descr">' . nl2br(htmlspecialchars($def['description'])) . '</p>' : '').
							$def['content'].
							'</div>';

				// Create initialization string:
				$JSinit[] = '
					DTM_array["' . $id . '"][' . $c . '] = "' . $id . '-' . $index . '";
					';
//				if ($toggle==1)	{
					$JSinit[] = '
						if (top.DTM_currentTabs["' . $id . '-' . $index . '"]) { DTM_toggle("' . $id . '","' . $index . '",1); }
					';
//					}
				$c++;
			}

			// Render menu:
			if (count($options))	{
				// Tab menu is compiled:
				if (!$foldout)	{
					$tabContent = '';
					for($a=0;$a<=$tabRows;$a++)	{
						$tabContent.= '
							<!-- Tab menu -->
						<table cellpadding="0" cellspacing="0" border="0"' . ($fullWidth ? ' width="100%"' : '') . ' class="typo3-dyntabmenu">
						<tr>';
						$tabContent .= 	implode('',$options[$a]);
						$tabContent .= '
						</tr>
						</table>';
					}
					$content.= '<div class="typo3-dyntabmenu-tabs">' . $tabContent . '</div>';
				}

				// Div layers are added:
				$countDivs=count($divs);

				$content .= '<div class="typo3-dyntabmenu-divs' . ($foldout ? '-foldout' : '') . '">';

				for($i=0;$i<$countDivs;$i++){
					if($i==0)
						$content .= '<div class="firstOptions">' . $divs[$i] . '</div>';
					else
						$content .= '<div class="otherOptions">' . $divs[$i] . '</div>';
					}
				$content .= '</div>';
					// Java Script section added:
// Initialization JavaScript for dynamic tabbed menu
				$content .='
<script type="text/javascript">
	DTM_array["' . $id . '"] = new Array();
	' . implode('',$JSinit) . '
	' . ($toggle<=0 ? 'DTM_activate("' . $id . '", top.DTM_currentTabs["' . $id . '"]?top.DTM_currentTabs["' . $id . '"]:' . intval($defaultTabIndex) . ', 0);' : '') . '
</script>
				';
			}
		}
		return $content;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feeditadvanced/view/class.tx_feeditadvanced_tceforms.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/feeditadvanced/view/class.tx_feeditadvanced_tceforms.php']);
}

?>