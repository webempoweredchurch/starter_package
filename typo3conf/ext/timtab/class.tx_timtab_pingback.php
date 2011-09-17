<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2005 Ingo Renner (typo3@ingo-renner.com)
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
 * Pingback class for the timtab extension, the majority of the code
 * is taken from wordpress
 *
 * $Id$
 *
 * @author    Ingo Renner <typo3@ingo-renner.com>
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   45: class tx_timtabb_trackback
 *   47:     function pingSent()
 *   56:     function discoverPingbackServerURI()
 *
 * TOTAL FUNCTIONS: 2
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */
class tx_timtab_pingback {
	var $prefixId = 'tx_timtab_pingback';        // Same as class name
    var $scriptRelPath = 'class.tx_timtab_pingback.php';    // Path to this script relative to the extension dir.
    var $extKey = 'timtab';    // The extension key.

	function pingSent() {

	}

	/**
	 * [Describe function...]
	 *
	 * @return	[type]		...
	 */
	function discoverPingbackServerURI() {

	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/timtab/class.tx_timtab_pingback.php'])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/timtab/class.tx_timtab_pingback.php']);
}

?>
