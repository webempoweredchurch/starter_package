<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2006 Web Empowered Church Team, Foundation For Evangelism (sermon@webempoweredchurch.org)
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

require_once(PATH_t3lib.'class.t3lib_div.php');

 /**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   48: class tx_wecsermons_xmlView
 *   59:     function preProcessContentRow( $pObj, $row, $tableName )
 *   75:     function preProcessPageArray( $pObj, $dataArray, $pageArray )
 *
 * TOTAL FUNCTIONS: 2
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

/**
 * class 'tx_wecsermons_xmlView' for the 'WEC Sermon Management System' library. This class is used in implementing hooks to wecapi_list. This allows us to extend the row and page array to include a link to sermon records, and the LIST view respectively.
 *
 * @author	Web Empowered Church Team, Foundation For Evangelism <sermon@webempoweredchurch.org>
 * @package TYPO3
 * @subpackage wec_sermons
 */
 class tx_wecsermons_xmlView {

	/**
	 * preProcessContentRow: Extends the $row array, inserting a link to the current SMS record. Implementation for hook ['tx_wecapi_list']['preProcessPageArray']
	 *
	 * @param	object		$pObj: The parent object that calls this function through a hook.
	 * @param	array		$row:	The data row containing an SMS record, which we'll use to generate the link to.
	 * @param	[type]		$tableName: The name of the table
	 * @return	[type]		void
	 * @see	tx_wecapi_list::getListContent()
	 */
	function preProcessContentRow( &$pObj, &$row, $tableName ) {

			//	Make a callback to get the URL to this record. Parent object of tx_wecapi_list must support getUrlToSingle function call!
		$row['item_link'] = $pObj->cObj->getUrlToSingle( true, $tableName, $row['uid'] );

	}

	/**
	 * preProcessPageArray:  Extends the page marker array, inserting a link to the current page.
	 *
	 * @param	object		$pObj: The parent object that calls this function through a hook.
	 * @param	array		$dataArray:	The data row containing a data record. Not used in this implementation
	 * @param	[type]		$pageArray: The array of marker names we will process for this page.
	 * @return	[type]		void
	 * @see	tx_wecapi_list::getListContent()
	 */
	function preProcessPageArray( &$pObj, &$dataArray, &$pageArray ) {

			//	Make a callback to get the URL to the list view. Parent object of tx_wecapi_list must support getUrlToList function call!
		$pageArray['channel_link'] = $pObj->cObj->getUrlToList( true );

	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wec_sermons/class.tx_wecsermons_xmlView.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wec_sermons/class.tx_wecsermons_xmlView.php']);
}
?>