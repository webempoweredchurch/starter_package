<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008-2010 Kraft Bernhard (kraftb@think-open.at)
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
 * Integration of kb_packman into DAM
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
 *   49: class tx_kbpackman_dam extends tx_damindex_index
 *   58:     function indexSessionNew($filesTodo)
 *
 * TOTAL FUNCTIONS: 1
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */


require_once(t3lib_extMgm::extPath('dam_index').'modfunc_index/class.tx_damindex_index.php');

class tx_kbpackman_dam extends tx_damindex_index	{


	/**
	 * Handler for the DAM API to index new files
	 *
	 * @param	string		Serialized array of file to get indexed (which were extracted from an archive)
	 * @return	mixed		Return value of indexing method of DAM
	 */
	function indexSessionNew($filesTodo) {
		$filesTodo = unserialize($GLOBALS['SOBE']->MOD_SETTINGS['tx_kbpackman_dam_filesTodo']);
		return parent::indexSessionNew($filesTodo);
	}


}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kb_packman/class.tx_kbpackman_dam.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kb_packman/class.tx_kbpackman_dam.php']);
}

?>
