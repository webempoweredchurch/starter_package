<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2004-2010 Kraft Bernhard (kraftb@think-open.at)
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
/**
 * Addition of an item to the clickmenu
 *
 * $Id$
 *
 * @author	Kraft Bernhard <kraftb@think-open.at>
 * @package TYPO3
 * @subpackage tx_kbpackman
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   53: class tx_kbpackman_cm1
 *   61:     function init()
 *   83:     function main(&$backRef,$menuItems,$table,$uid)
 *  136:     function menuItemsDir($table, $uid, $menuItems, &$backRef)
 *  163:     function menuItemsFile($table, $uid, $menuItems, &$backRef)
 *  186:     function includeLL()
 *
 * TOTAL FUNCTIONS: 5
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */


require_once(PATH_t3lib.'class.t3lib_div.php');

class tx_kbpackman_cm1 {


	/**
	 * Initalizing the clickmenu hook
	 *
	 * @return	void
	 */
	function init() {
			// Adds the regular item:
		$this->LL = $this->includeLL();
		$this->filefunc = t3lib_div::makeInstance('t3lib_basicFileFunctions');
		$this->filefunc->init($GLOBALS['FILEMOUNTS'], $GLOBALS['TYPO3_CONF_VARS']['BE']['fileExtensions']);
		$this->dirTarget = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['kb_packman']['dirTarget'];
		$this->fileTarget = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['kb_packman']['fileTarget'];
		$this->pacman = t3lib_div::makeInstance('tx_kbpackman');
	}




	/**
	 * Returns the list of menuitems in the clickmenu with the new fields appended
	 *
	 * @param	object		back reference
	 * @param	array		menu items
	 * @param	string		the table or file on which was clicked
	 * @param	uid		the uid on which was clicked
	 * @return	array		menu items
	 */
	function main(&$backRef,$menuItems,$table,$uid) {
		global $BE_USER,$TCA,$LANG;

		$localItems = array();
		if (!$backRef->cmLevel)	{

			$this->useBackPath = '###BACK_PATH###';

			if (@is_dir($table))	{
				return $this->menuItemsDir($table, $uid, $menuItems, $backRef);
			} elseif (!@is_file($table)) {
				return $menuItems;
			}
			$this->init();
			if (!$this->pacman->isCompressed($table)) {
				return $this->menuItemsFile($table, $uid, $menuItems, $backRef);
			}
			$dir = dirname($table).'/';
			$pathOK = $this->filefunc->checkPathAgainstMounts($dir);
			if (is_writeable($dir)&&(($pathOK&&($BE_USER->user['fileoper_perms']&0x1)&&($BE_USER->user['fileoper_perms']&0x2)) || intval($BE_USER->isAdmin()))) {
				$enc = urlencode($table);
				$url = t3lib_extMgm::extRelPath('kb_packman').'cm1/index.php?id='.$uid.'&file='.$enc.'&returnURL='.rawurlencode(t3lib_div::linkThisScript());
				$localItems['unpack'] = $backRef->linkItem(
					$GLOBALS['LANG']->getLLL('cm1_title',$this->LL),
					$backRef->excludeIcon('<img src="'.$this->useBackPath.t3lib_extMgm::extRelPath('kb_packman').'cm1/cm_icon_unpack.gif" width="15" height="12" border=0 align=top>'),
					$backRef->urlRefForCM($url),
					0	// Disables the item in the top-bar. Set this to zero if you with the item to appear in the top bar!
				);

				$url = t3lib_extMgm::extRelPath('kb_packman').'cm1/index.php?id='.$uid.'&file='.$enc.'&overwrite=1&returnURL='.rawurlencode(t3lib_div::linkThisScript());
				$localItems['unpack_overwrite'] = $backRef->linkItem(
					$GLOBALS['LANG']->getLLL('cm1_title_overwrite',$this->LL),
					$backRef->excludeIcon('<img src="'.$this->useBackPath.t3lib_extMgm::extRelPath('kb_packman').'cm1/cm_icon_unpack_overwrite.gif" width="15" height="12" border=0 align=top>'),
					$backRef->urlRefForCM($url),
					0	// Disables the item in the top-bar. Set this to zero if you with the item to appear in the top bar!
				);
				$menuItems=array_merge($menuItems,$localItems);
			}
		}


		return $menuItems;
	}

	/**
	 * Generate a list of menu itesm for a directory
	 *
	 * @param	string		The directory being clicked on (Usually table of record being clicked)
	 * @param	string		Not valid in this context (Usually UID of record being clicked)
	 * @param	array		Current clickmenu items
	 * @param	object		Pointer to the parent object
	 * @return	array		The clickmenu items containing options for the packman extension if applicable
	 */
	function menuItemsDir($table, $uid, $menuItems, &$backRef)	{
		global $BE_USER;
		$this->init();
		$dirTarget = $this->pacman->getTarget($table);
		$pathOK = $this->filefunc->checkPathAgainstMounts($dirTarget);
		if (is_writeable($dirTarget)&&(($pathOK&&($BE_USER->user['fileoper_perms']&0x1)) || intval($BE_USER->isAdmin()))) {
			$enc = urlencode($table);
			$url = t3lib_extMgm::extRelPath('kb_packman').'cm1/index.php?id='.$uid.'&comp='.$enc;
			$menuItems[] = $backRef->linkItem(
				$GLOBALS['LANG']->getLLL('cm1_title_compress', $this->LL),
				$backRef->excludeIcon('<img src="'.$this->useBackPath.t3lib_extMgm::extRelPath('kb_packman').'cm1/cm_icon_compress_dir.gif" width="15" height="12" border=0 align=top>'),
				$backRef->urlRefForCM($url),
				1	// Disables the item in the top-bar. Set this to zero if you with the item to appear in the top bar!
			);
		}
		return $menuItems;
	}

	/**
	 * Generate a list of menu itesm for a directory
	 *
	 * @param	string		The file being clicked on (Usually table of record being clicked)
	 * @param	string		Not valid in this context (Usually UID of record being clicked)
	 * @param	array		Current clickmenu items
	 * @param	object		Pointer to the parent object
	 * @return	array		The clickmenu items containing options for the packman extension if applicable
	 */
	function menuItemsFile($table, $uid, $menuItems, &$backRef)	{
		global $BE_USER;
		$fromDir = dirname($table).'/';
		$dirTarget = $this->pacman->getTarget($fromDir, 1);
		$pathOK = $this->filefunc->checkPathAgainstMounts($dirTarget);
		if (is_writeable($dirTarget)&&(($pathOK&&($BE_USER->user['fileoper_perms']&0x1)) || intval($BE_USER->isAdmin()))) {
			$enc = urlencode($table);
			$url = t3lib_extMgm::extRelPath('kb_packman').'cm1/index.php?id='.$uid.'&comp='.$enc;
			$menuItems[] = $backRef->linkItem(
				$GLOBALS['LANG']->getLLL('cm1_title_compress', $this->LL),
				$backRef->excludeIcon('<img src="'.$this->useBackPath.t3lib_extMgm::extRelPath('kb_packman').'cm1/cm_icon_compress_file.gif" width="15" height="12" border=0 align=top>'),
				$backRef->urlRefForCM($url),
				1	// Disables the item in the top-bar. Set this to zero if you with the item to appear in the top bar!
			);
		}
		return $menuItems;
	}

	/**
	 * Includes the [extDir]/locallang.xml and returns the associated LOCAL_LANG array
	 *
	 * @return	array		Locallang array
	 */
	function includeLL() {
		return $GLOBALS['LANG']->includeLLFile('EXT:kb_packman/locallang.xml', 0);
	}


}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kb_packman/class.tx_kbpackman_cm1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kb_packman/class.tx_kbpackman_cm1.php']);
}

?>
