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
/**
 * Class/Functions used to modify TCA on the fly. Functions are called by hooks or userFunc declarations. This allows us to change the TCA based on the existence of tx_wecsermons_resource_types backend records. These records become record types for tx_wecsermons_resources, changing how resources are edited in the backend.
 *
 * @author	Web Empowered Church Team, Foundation For Evangelism <sermon@webempoweredchurch.org>
 * @package TYPO3
 * @subpackage wec_sermons
 */

#require_once(PATH_t3lib.'class.t3lib_BEfunc.php');

 /**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   51: class tx_wecsermons_resourceTypeTca
 *   61:     function getMainFields_preProcess($table,$row,&$pObj)
 *  122:     function resourceType_items( &$params, &$pObj )
 *  158:     function processAvailableFields( $availFields )
 *  193:     function processDatamap_preProcessIncomingFieldArray()
 *  206:     function processDatamap_preProcessFieldArray(&$fieldArray, $table, $id, &$pObj)
 *  226:     function processDatamap_postProcessFieldArray($status, $table, $id, $fieldArray, $pObj)
 *
 * TOTAL FUNCTIONS: 6
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */
class tx_wecsermons_resourceTypeTca {

	/**
	 * getMainFields_preProcess: Hook implementation that modifies the TCA on the fly, converting backend records tx_wecsermons_resources_type into a 'types' arrays that change our backend forms
	 *
	 * @param	string		$table: The name of the table where the current record is stored
	 * @param	array		$row:	An associative array of the current record
	 * @param	array		&$pObj:	The backreference to the parent object calling this function
	 * @return	void
	 */
	function getMainFields_preProcess($table,$row,&$pObj) {

# Keep this line for future functionality, possibly manipulating pi1's flexform
#		if( $row['list_type'] = 'wec_sermons_pi1' ) {

#		if( !strcmp( $table, 'tx_wecsermons_resource_types' ) ) {
#				debug( $row,1);
#		}

		if( $table == 'tx_wecsermons_resources' ) {

			//	Make sure TCA is loaded for our table
			t3lib_div::loadTCA( 'tx_wecsermons_resources' );

			//	Retreive all tx_wecsermons_resource_types records
			$resource = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
				'*',
				'tx_wecsermons_resource_types',
				' deleted = 0 AND hidden = 0 ',
				'',
				'sorting'
			);

			//	Convert each tx_wecsermons_resource_types record into a 'types' TCA array
			while( $resourceType = $GLOBALS['TYPO3_DB']->sql_fetch_assoc( $resource )  ) {

				$GLOBALS['TCA']['tx_wecsermons_resources']['types'][$resourceType['uid']] = array( 'showitem' => "sys_language_uid;;;;1-1-1, hidden;;1, l18n_parent, l18n_diffsource,type;;;;2-2-2, title, " . rtrim( $this->processAvailableFields( $resourceType['avail_fields'] ) . ($resourceType['type'] == '1' ? 'rendered_record,' : ''), ',') );
			}

#debug( $GLOBALS['TCA']['tx_wecsermons_resources']['types'] ,1);

		}
		
	}

//	function pi1LayoutItems( &$params, &$pObj ) {
//
////TODO: Figure out how to grab the current page in the backend.
//debug( $GLOBALS );
//		$tsConfig = t3lib_BEfunc::getPagesTSconfig( $GLOBALS [ '_POST' ][ 'popViewId' ] );
//		$tsConfig = $tsConfig['tx_wecsermons.']['layout.'];
//
//		foreach( $tsConfig as $key => $value ) {
//			$params['items'][] = array (
//				$value,
//				$key,
//				''
//			);
//		}
//
//	}



	/**
	 * resourceType_items: A TCA[config][itemsProcFunc] implementation used from tx_wecsermons_resources
	 *
	 * @param	array		&$params: Reference to the 'config' array from our TCA column
	 * @param	array		&$pObj:	The backreference to the parent object calling this function
	 * @return	void
	 */
	function resourceType_items( &$params, &$pObj ) {

		//	Make sure TCA is loaded for our table
		t3lib_div::loadTCA( 'tx_wecsermons_resources' );
		t3lib_div::loadTCA( 'tx_wecsermons_resource_types' );

		$where .= t3lib_BEfunc::BEenableFields('tx_wecsermons_resource_types');
		$where .= t3lib_BEfunc::deleteClause('tx_wecsermons_resource_types');

		//	Retreive all tx_wecsermons_resource_types records
		$resource = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'*',
			'tx_wecsermons_resource_types',
				' 1=1'.$where,
				'',
				'sorting'
		);

		//	Convert each tx_wecsermons_resource_types record into a selectable 'record type' for tx_wecsermons_resource records
		while( $resourceType = $GLOBALS['TYPO3_DB']->sql_fetch_assoc( $resource )  ) {

			//	TODO: Resize the given image file to 18x16 via ImageMagick
			$params['items'][] = array(
				$resourceType['title'],
				$resourceType['uid'],
				$resourceType['icon'] ? '../../' . $GLOBALS['TCA']['tx_wecsermons_resource_types']['columns']['icon']['config']['uploadfolder'] . '/' . $resourceType['icon'] : ''
			);

		}

	}

	/**
	 * processAvailableFields	Function that performs pre-processing of the "types" portion of TCA, enabling RTE functionality and advanced backend form features.
	 *
	 * @param	string		$$availFields: A csv string containing the possible available fields that can be displayed for a SMS resource
	 * @return	string		Returns a processed csv string, ready to be appended to the "types" portion of TCA
	 */
	 function processAvailableFields( $availFields ) {

	 	$processedFields = '';
	 	$fieldArray = explode( ',' , $availFields );

	 	foreach( $fieldArray as $field ) {

	 		switch( $field ) {

	 			case 'description':
	 				$processedFields .= ' description;;;richtext:rte_transform[mode=ts_css];3-3-3';
	 				break;

	 			case 'itunes_metadata':
	 				$processedFields .= ' subtitle;;;;4-4-4, summary,';
	 				break;

	 			default:
	 				$processedFields .= $field;
	 				break;

	 		}

	 		$processedFields .= ',';
	 	}
	 	//	Return the processed fields csv string if string length > 1
	 	return strlen( $processedFields ) > 1 ? $processedFields : '';
	}

	/**
	 * this function seems to needed for compatibility with TYPO3 3.7.0.
	 * In this TYPO3 version tcemain ckecks the existence of the method "processDatamap_preProcessIncomingFieldArray()" but calls "processDatamap_preProcessFieldArray()"
	 *
	 * @return	void
	 */
	function processDatamap_preProcessIncomingFieldArray() {
	}

	/**
	 * This method is called by a hook in the TYPO3 Core Engine (TCEmain) when a record is saved. We use it to redirect a save_and_preview event to the proper page
	 *
	 * @param	array		$fieldArray: The field names and their values to be processed (passed by reference)
	 * @param	string		$table: The table TCEmain is currently processing
	 * @param	string		$id: The records id (if any)
	 * @param	object		$pObj: Reference to the parent object (TCEmain)
	 * @return	void
	 * @access public
	 */
	function processDatamap_preProcessFieldArray(&$fieldArray, $table, $id, &$pObj) {
		if ($table == 'tx_wecsermons_sermons' || $table == 'tx_wecsermons_series'
			|| $table == 'tx_wecsermons_speakers' || $table == 'tx_wecsermons_resources'
			|| $table == 'tx_wecsermons_topics' || $table == 'tx_wecsermons_seasons'
		) {

				// direct preview
			if (isset($GLOBALS['_POST']['_savedokview_x']) && !$GLOBALS['BE_USER']->workspace)	{
					// if "savedokview" has been pressed and the beUser works in the LIVE workspace open current record in single view
				$pagesTSC = t3lib_BEfunc::getPagesTSconfig($GLOBALS['_POST']['popViewId']); // get page TSconfig
				if ($pagesTSC['tx_wecsermons_pi1.']['singlePid']) {
					$GLOBALS['_POST']['popViewId_addParams'] = ($fieldArray['sys_language_uid']>0?'&L='.$fieldArray['sys_language_uid']:'').'&no_cache=1&tx_wecsermons_pi1%5BrecordType%5D='.$table.'&tx_wecsermons_pi1%5BshowUid%5D='.$id;
					$GLOBALS['_POST']['popViewId'] = $pagesTSC['tx_wecsermons_pi1.']['singlePid'];
				}

			}
		}
		
		if( (! strcmp( $table, 'tx_wecsermons_series') || ! strcmp( $table, 'tx_wecsermons_sermons') ) && $fieldArray['current'] ) {
#$GLOBALS['TYPO3_DB']->debugOutput = 1;
#debug($fieldArray,'Field Array');			
			$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery(
				$table,
				'',
				array( 'current' => '0' )
				
			);
			
		}
		
	}

/*
	function processDatamap_postProcessFieldArray($status, $table, $id, $fieldArray, $pObj) {

		if( !strcmp( $table, 'tx_wecsermons_resource_types' ) && !strcmp( $status, 'update' ) && $fieldArray['typoscript_object_name'] ) {



debug( $status,1);
debug( $table,1);
debug( $id,1);
debug( $fieldArray,1);

		}
	}
*/

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wec_sermons/class.tx_wecsermons_resourceTypeTca.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wec_sermons/class.tx_wecsermons_resourceTypeTca.php']);
}

?>