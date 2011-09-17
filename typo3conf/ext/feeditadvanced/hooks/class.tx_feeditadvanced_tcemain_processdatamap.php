<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Jeff Segars <jeff@webempoweredchurch.org>
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
 * Hooks for TCEMain's processDataMap operations.
 *
 * @author	Jeff Segars <jeff@webempoweredchurch.org>
 * @package TYPO3
 * @subpackage feeditadvanced
 */
class tx_feeditadvanced_tcemain_processdatamap {

	/**
	 * Preprocessing hook for TCEMain. Used to rename POST data and work around the following core bug:
	 * http://bugs.typo3.org/view.php?id=15496
	 *
	 * @param	array
	 * @param	string
	 * @param	integer
	 * @param	t3lib_tcemain
	 * @return	 void
	 */
	public function processDatamap_preProcessFieldArray($fieldArray, $table, $id, $tce) {
		$temp = t3lib_div::_GP('_ACTION_FLEX_FORMTSFE_EDIT');
		$_POST['_ACTION_FLEX_FORMdata'] = $temp['data'];
	}
}

?>