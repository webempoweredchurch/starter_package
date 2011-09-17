<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2005 Ingo Renner (typo3@ingo-renner.com)
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
/**
 * class.tx_timtab_lib.php
 *
 * contains general purpose functions
 *
 * $Id: class.tx_timtab_lib.php,v 1.1 2005/09/07 19:09:20 ingorenner Exp $
 *
 * @author Ingo Renner <typo3@ingo-renner.com>
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *
 */

require_once(PATH_t3lib.'class.t3lib_tcemain.php');

class tx_timtab_lib {

	/**
	 * explicitly clears cache for the blog page as it is not updating sometimes
	 *
	 * @param	string		comma separated list of page ids
	 * @return	void
	 */
	function clearPageCache($pageIDs) {
		$tce = t3lib_div::makeInstance('t3lib_TCEmain');
		$tce->admin = 1;

		$clearCachePages = split(',', $pageIDs);
		foreach($clearCachePages as $page) {
			$tce->clear_cacheCmd((int) $page);
		}
		$tce->admin = 0;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/timtab/class.tx_timtab_lib.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/timtab/class.tx_timtab_lib.php']);
}

?>