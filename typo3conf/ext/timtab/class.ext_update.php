<?php
/***************************************************************
*  Copyright notice
*
*  (c)   2005 Ingo Renner (typo3@ingo-renner.com)
*  All   rights reserved
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
/**
 * class.ext_update.php
 * 
 * Class for importing from other blogs.
 *
 * $Id: class.ext_update.php,v 1.1 2005/09/21 19:04:11 ingorenner Exp $
 *
 * @author  Ingo Renner
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   48: class ext_update
 *   55:     function main()
 *  173:     function access()
 *
 * TOTAL FUNCTIONS: 2
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */


class ext_update {

	/**
	 * Main function, returning the HTML content of the module
	 *
	 * @return	string		HTML
	 */
	function main() {
		
		$onclick = 'document.forms[\'pageform\'].action = \''.t3lib_div::linkThisScript(array()).'\';document.forms[\'pageform\'].submit();return false;';
		$content = '';
		
		if(t3lib_div::_GP('importer_key')) {
			$importer_key = t3lib_div::_GP('importer_key');
			$content .= 'Selected Importer: <strong>'.$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['timtab']['importers'][$importer_key]['name'].'</strong> ('.$importer_key.')<br /><br />'.chr(10);
		}
		
		if (t3lib_div::_GP('importer_selected')) {
			// 2: importer selected, config importer
									
			$content .= '<fieldset style="border: 1px #9ba1a8 solid;"><legend><strong>Database Connection</strong></legend>'.chr(10);
			
			$content .= '<input type="text" name="host" /> Host<br /><br />'.chr(10);
			$content .= '<input type="text" name="db" /> Database<br /><br />'.chr(10);
			$content .= '<input type="text" name="user" /> Username<br /><br />'.chr(10);
			$content .= '<input type="text" name="pass" /> Password<br /><br /><br />'.chr(10);
			$content .= '<input type="text" name="prefix" /> Table Prefix (only if required)'.chr(10);
			
			$content .= '</fieldset><br /><br />'.chr(10);
			
			$content .= '<fieldset style="border: 1px #9ba1a8 solid;"><legend><strong>Target PIDs</strong></legend>'.chr(10);
			$content .= 'Insert the PIDs where you want the items to be stored.<br /><br />'.chr(10);
			
			$content .= '<input type="text" name="pid_posts" /> Posts<br /><br />'.chr(10);
			$content .= '<input type="text" name="pid_categories" /> Categories<br /><br />'.chr(10);
			$content .= '<input type="text" name="pid_comments" /> Comments'.chr(10);
			
			$content .= '</fieldset><br /><br />'.chr(10);
			
			$content .= '<input type="hidden" name="importer_key" value="'.$importer_key.'"/>'.chr(10);
			$content .= '<input type="hidden" name="importer_configured" value="1"/>'.chr(10);
			$content .= '<input type="button" value="Configure Importer" onclick="'.$onclick.'" />'.chr(10);
				
		} else if (t3lib_div::_GP('importer_configured')) {
			// 3: importer configured, display stats
			$PATH_importer = t3lib_extMgm::extPath($importer_key);
			require_once($PATH_importer.'class.'.$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['timtab']['importers'][$importer_key]['class'].'.php');
			
			$importer = t3lib_div::makeInstance($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['timtab']['importers'][$importer_key]['class']);
			$importer->init(
				t3lib_div::_GP('host'),
				t3lib_div::_GP('db'),
				t3lib_div::_GP('user'),
				t3lib_div::_GP('pass'),
				
				t3lib_div::_GP('pid_posts'),
				t3lib_div::_GP('pid_categories'),
				t3lib_div::_GP('pid_comments'),
				
				t3lib_div::_GP('prefix')
			);
			$content .= $importer->query().chr(10);
			
			$content .= '<input type="hidden" name="host" value="'.t3lib_div::_GP('host').'" />'.chr(10);
			$content .= '<input type="hidden" name="db" value="'.t3lib_div::_GP('db').'" />'.chr(10);
			$content .= '<input type="hidden" name="user" value="'.t3lib_div::_GP('user').'" />'.chr(10);
			$content .= '<input type="hidden" name="pass" value="'.t3lib_div::_GP('pass').'" />'.chr(10);
			$content .= '<input type="hidden" name="prefix" value="'.t3lib_div::_GP('prefix').'" />'.chr(10);
			$content .= '<input type="hidden" name="pid_posts" value="'.t3lib_div::_GP('pid_posts').'" />'.chr(10);
			$content .= '<input type="hidden" name="pid_categories" value="'.t3lib_div::_GP('pid_categories').'" />'.chr(10);
			$content .= '<input type="hidden" name="pid_comments" value="'.t3lib_div::_GP('pid_comments').'" />'.chr(10);
			
			$content .= '<input type="hidden" name="importer_key" value="'.$importer_key.'"/>'.chr(10);
			$content .= '<input type="hidden" name="start_import" value="1"/>'.chr(10);
			$content .= '<input type="button" value="IMPORT!" onclick="'.$onclick.'" />'.chr(10);
		
		} else if (t3lib_div::_GP('start_import')) {
			// 4: importer configured, IMPORT
			$PATH_importer = t3lib_extMgm::extPath($importer_key);
			require_once($PATH_importer.'class.'.$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['timtab']['importers'][$importer_key]['class'].'.php');
			
			$importer = t3lib_div::makeInstance($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['timtab']['importers'][$importer_key]['class']);
			$importer->init(
				t3lib_div::_GP('host'),
				t3lib_div::_GP('db'),
				t3lib_div::_GP('user'),
				t3lib_div::_GP('pass'),
				
				t3lib_div::_GP('pid_posts'),
				t3lib_div::_GP('pid_categories'),
				t3lib_div::_GP('pid_comments'),
				
				t3lib_div::_GP('prefix')
			);
			$res = false;
			$res = $importer->import(); //do the import!
			
			if($res) {
				$content .= '<strong>Import complete.</strong>';
			} else {
				$content .= '<strong>Import had <span style="color: #f00;">errors</span>.</strong>';
			}
			
		} else {
			// 1: start, select importer
			
			$content .= 'Available Importers: <select name="importer_key">'.chr(10);
			foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['timtab']['importers'] as $key => $val) {
				$content .= '<option value="'.$key.'">'.$val['name'].'</option>'.chr(10);
			}
			$content .= '</select> ';
			$content .= '<input type="hidden" name="importer_selected" value="1"/>';
			$content .= '<input type="button" value="Select Importer" onclick="'.$onclick.'" />'.chr(10);
								
		} 
		
		return $content;
	}

	/**
	 * Checks whether at least one importer is available
	 * (this function is called from the extension manager)
	 *
	 * @return	boolean
	 */
	function access() {
		
		$access = false;
		
		if($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['timtab']['importers'])
		{
			$access = true;
		}
		
		return $access;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/timtab/class.ext_update.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/timtab/class.ext_update.php']);
}
?>
