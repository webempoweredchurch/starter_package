<?php
/***************************************************************
* Copyright notice
*
* (c) 2005-2008 Christian Technology Ministries International Inc.
* All rights reserved
*
* This file is part of the Web-Empowered Church (WEC)
* (http://WebEmpoweredChurch.org) ministry of Christian Technology Ministries 
* International (http://CTMIinc.org). The WEC is developing TYPO3-based
* (http://typo3.org) free software for churches around the world. Our desire
* is to use the Internet to help offer new life through Jesus Christ. Please
* see http://WebEmpoweredChurch.org/Jesus.
*
* You can redistribute this file and/or modify it under the terms of the
* GNU General Public License as published by the Free Software Foundation;
* either version 2 of the License, or (at your option) any later version.
*
* The GNU General Public License can be found at
* http://www.gnu.org/copyleft/gpl.html.
*
* This file is distributed in the hope that it will be useful for ministry,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* This copyright notice MUST APPEAR in all copies of the file!
***************************************************************/

require_once(PATH_tslib.'class.tslib_pibase.php');

require_once(t3lib_extMgm::extPath('wec_api','class.tx_wecapi_list.php'));
require_once(t3lib_extMgm::extPath('wec_sermons', 'class.ext_update.php'));

/**
* [CLASS/FUNCTION INDEX of SCRIPT]
*
*
*
*   80: class tx_wecsermons_pi1 extends tslib_pibase
*   93:     function init($conf)
*  114:     function main($content,$conf)
*  224:     function xmlView ($content, $lConf)
*  353:     function singleView($content,$lConf)
*  499:     function searchView($content,$lConf)
*  514:     function pi_list_searchbox($lConf)
*  562:     function latestView($content,$lConf)
*  638:     function listView($content,$lConf)
*  732:     function pi_list_makelist($lConf, $template)
*  952:     function pi_list_row($lConf, $markerArray = array(), $rowTemplate, $row ='', $c = 2)
* 1808:     function getMarkerArray( $tableName = '', $templateContent = '')
* 1960:     function formatStr( $str )
* 1974:     function getTemplateKey($tableName)
* 2017:     function getUrlToList ( $absolute )
* 2035:     function getUrlToSingle ( $absolute, $tableName, $uid, $sermonUid = '' )
* 2063:     function getFeAdminList( $tableName = '' )
* 2083:     function getNamedTemplateContent($keyName = 'sermon', $view = 'single')
* 2125:     function getNamedSubpart( $subpartName, $content )
* 2142:     function getMarkerName( $markerName )
* 2155:     function loadTemplate( $view = 'LIST')
* 2181:     function getTemplateFile()
* 2209:     function getGroupResult( $groupTable, $detailTable, $lConf, $getCount = 0 )
* 2313:     function getSermonResources( $sermonUid = '', $resourceUid = '')
* 2382:     function getSeriesResources( $seriesUid = '', $resourceUid = '')
* 2450:     function emptyResourceSubparts( &$subpartArray, $templateContent = '' )
* 2495:     function throwError( $type, $message, $detail = '' )
* 2519:     function getTutorial ( $tutorial )
* 2596:     function uniqueCsv()
* 2611:     function unique_array()
* 2629:     function get_foreign_column( $currentTable, $relatedTable )
* 2655:     function getConfigVal( &$Obj, $ffField, $ffSheet, $TSfieldname, $lConf, $default = '' )
* 2674:     function splitTableAndUID($record)
* 2687:     function array_intersect_key()
*
* TOTAL FUNCTIONS: 33
* (This index is automatically created/updated by the extension "extdeveval")
*
*/

/**
* Plugin 'Sermon Repository' for the 'wec_sermons' extension.
*
* @author	Web Empowered Church Team, CTMI <sermon@webempoweredchurch.org>
* @package TYPO3
* @subpackage wec_sermons
*/
class tx_wecsermons_pi1 extends tslib_pibase {
	var $prefixId = 'tx_wecsermons_pi1';		// Same as class name
	var $scriptRelPath = 'pi1/class.tx_wecsermons_pi1.php';	// Path to this script relative to the extension dir.
	var $extKey = 'wec_sermons';	// The extension key.
	var $pi_checkCHash = TRUE;
	var $template = null;

	/**
	* init: performs some initialization of our class
	*
	* @param	array		$conf: Configuration array from TypoScript
	* @return	void
	*/
	function init($conf)	{
		$this->conf=$conf;		// Setting the TypoScript passed to this function in $this->conf
		$this->pi_initPIflexForm(); // Init FlexForm configuration for plugin
		$this->pi_setPiVarDefaults(); // Set default piVars from TS
		$this->pi_loadLL();		// Loading the LOCAL_LANG values

		# Using $this->pi_isOnlyFields:
		# this holds a comma-separated list of fieldnames which - if they are among the GETvars - will not disable caching for the page with pagebrowser.
		$this->pi_isOnlyFields .= ",recordType";

		// Unserialize SMS extension configuration, and write it to a TSFE register for access throughout the plugin
		$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['wec_sermons']);
		$resourcePath = $extConf['resourceUploadPath'];
		$resourcePath = $resourcePath ? $resourcePath : 'uploads/tx_wecsermons';
		$GLOBALS["TSFE"]->register['wec_sermons_resourceUploadPath'] = $resourcePath;
	}

	/**
	* Main: Primary sermons function.
	* This function determines which view to display, branching to retreive content for each view.
	*
	* @param	string		$content: Any previous content that this class will append itself to.
	* @param	array		$conf: Configuration array from TypoScript
	* @return	string		Complete, processed content generated by the wec_sermons plugin
	*/
	function main($content,$conf)	{
		$this->local_cObj = t3lib_div::makeInstance('tslib_cObj'); // Local cObj.
		$this->init($conf);

		//	First check if extension template was loaded by checking existence of resource_types configuration array
		if( ! is_array( $this->conf['resource_types.'] ) ) {
			return $this->throwError(
			'WEC Sermon Management System Error!',
			'The extension template for WEC SMS was not loaded!',
			'Please edit your template record and add the "WEC SMS" template to the "Include static (from extension)" field.'
			);
		}

		// check our code version vs. our schema version
		$smsUpdater = new ext_update;
		$codeVersion = $smsUpdater->current;
		$schemaVersion = $smsUpdater->getVersion();
		if( $codeVersion != $schemaVersion ) {
			return $this->throwError(
			'WEC Sermon Management System Error!',
			'The extension code and schema definitions appear to be out of sync (' . $codeVersion . ', ' . $schemaVersion . ').',
			'Please run the update tool inside the Extension Manager (for the Sermons extension).'
			);
		}

		//	Check if typoscript config 'tutorial' is an integer, otherwise set to 0
		if( t3lib_div::testInt( $this->conf['tutorial'] ) == false ) $this->conf['tutorial'] = 0;

		$tutorial = $this->getConfigVal( $this, 'tutorial', 'sMisc', 'tutorial', $this->conf, 0 );

		//	If tutorial enabled, return tutorial content
		if( $tutorial )
			return $this->getTutorial( $tutorial );

		//	Get the 'what to display' value from plugin or typoscript, plugin overriding
		$display = $this->getConfigVal( $this, 'display', 'sDEF', 'CMD', $this->conf );

		//	Check codes for 'xml', and if found then we only display the RSS and nothing else.
		//	The XML output can not be displayed along with any other view.
		$display = strpos( $display, 'XML' ) ? 'XML' : $display;

		//	Check codes for 'list', and if showing only a single record, set codes to a single 'list' code only.
		$display = strpos( $display,'LIST') && $this->piVars['showUid'] ? 'LIST' : $display;

		//	Clean whitespaces from the allowedTables config value
		$this->conf['allowedTables'] = str_replace( ' ', '', $this->conf['allowedTables'] );

		//	Recursive setting from plugin overrides typoscript
		$this->conf['recursive'] = $this->getConfigVal( $this, 'recursive', 'sDEF', 'recursive', $this->conf, 0 );

		//	Find the starting point in the page tree to search for WEC SMS records, use current page as default
		$this->conf['pidList'] = $this->getConfigVal($this, 'startingpoint', 'sDEF', 'pidList', $this->conf, $GLOBALS['TSFE']->id );

		//	If configured to use the General Storage Folder of the site, include that in the list of pids
		if( $this->conf['useStoragePid'] ) {

			//	Retrieve the general storage pid for this site
			$rootPids = $GLOBALS['TSFE']->getStorageSiterootPids();
			$storagePid = (string) $rootPids['_STORAGE_PID'];

			//	Merge all lists from typoscript, storagePid, and startingpoint specified at plugin and assign to pidList
			$this->conf['pidList'] .= ','. $storagePid;
		}

		$codes = $this->internal['codes'] = t3lib_div::trimExplode(',',$display,0);

		foreach( $codes as $code ) {
			switch( $code ) {	//	Primary switch for this plugin
			case 'SINGLE':
				$this->internal['currentCode'] = 'SINGLE';
				$content = $this->singleView( $content, $this->conf['singleView.'] );
				break;

			case 'CURRENT':
				$this->internal['currentCode'] = 'CURRENT';
				$content = $this->singleView( $content, $this->conf['singleView.'] );
				break;

			case 'PREVIOUS':
				$this->internal['currentCode'] = 'PREVIOUS';
				$content = $this->singleView( $content, $this->conf['singleView.'] );
				break;

			case 'LIST':
				$this->internal['currentCode'] = 'LIST';
				$content = $this->piVars['showUid'] ? $this->listView( $content, $this->conf['listView.'] ) : $content . $this->listView( $content, $this->conf['listView.'] );
				break;

			case 'XML':
				$this->internal['currentCode'] = 'XML';
				$content .= $this->xmlView($content, $this->conf['xmlView.']);
				break;

/*	Currently unused feature, kept for future use

			case 'ARCHIVE':
				$this->internal['currentCode'] = 'ARCHIVE';
				$content .= '<h1>archive case reached</h1><br/>';
				break;
*/
			case 'SEARCH':
				$this->internal['currentCode'] = 'SEARCH';
				$content .= $this->searchView( $content, $this->conf['searchView.'] );
				break;

			case 'LATEST':
				$this->internal['currentCode'] = 'LATEST';
				$content .= $this->latestView( $content, $this->conf['latestView.'] );
				break;

			default:
				$content .= $this->throwError(
				'Configuration Error',
				'Plugin setting "What to Display" was not specified, or TypoScript Setup property "CMD" was incorrect or not found.',
				'What to Display:' . $this->getConfigVal( $this, 'display', 'sDEF', 'CMD', $this->conf )
				);
				break;
			}	//	End Primary switch

		}	//	End Primary foreach loop

		return $content;
	}

	/**
	* xmlView: This function will output a list view of sermons in XML format.
	*
	* @param	string		$content: Any previous content that this function will append itself to.
	* @param	array		$lConf: Locally scoped configuration array from TypoScript for xmlView
	* @return	string		Complete XML content
	*/
	function xmlView ($content, $lConf) {
		//	Get the related table entries to the group, using 'tx_wecsermons_sermons' if none specified
		$tableToList = $this->piVars['recordType'] ? htmlspecialchars( $this->piVars['recordType'] ) : ($this->conf['detailTable'] ? $this->conf['detailTable'] : 'tx_wecsermons_sermons' );

		if ($tableToList == 'tx_wecsermons_sermons') {
			$tableResourceRel = 'tx_wecsermons_sermons_resources_rel';
			$tableResourceRelKey = 'sermonid';
		} elseif ($tableToList == 'tx_wecsermons_series') {
			$tableResourceRel = 'tx_wecsermons_series_resources_rel';
			$tableResourceRelKey = 'seriesid';
		} else {
			; // we should never arrive in this situation
		}

		//	Retrieve the number we want to limit our items to
		$this->internal['results_at_a_time'] = $lConf['maxdetailResults'];
		$this->internal['orderByList']=$lConf[$tableToList.'.']['orderByList'];
		$this->internal['descFlag']='1'; //	Hardcode descending = 1 for latest view

		//	Retrieve ordering from typoscript, or 'title' as default.
		$orderBy = $this->getConfigVal( $this, 'sermons_order_by', 'slistView', 'orderBy', $lConf, 'title' );
		$this->internal['orderBy'] = $lConf['useCreationDate'] ? 'crdate' : $orderBy;
		//	TODO: Modify code to allow other records to be shown. Right now we're assuming sermons only.

		$this->conf['pidList'] = $this->pi_getPidList($this->conf['pidList'],$this->conf['recursive']);

		// Make listing query (can't use the stock pi_exec_query() method because we're doing fancy joins and just getting enclosures)
		$stmt = "select distinct ".$tableToList.".*
			  from ".$tableToList."
			   inner join ".$tableResourceRel."
			    on ".$tableToList.".uid = ".$tableResourceRel.".".$tableResourceRelKey."
			   inner join tx_wecsermons_resources
			    on ".$tableResourceRel.".resourceid = tx_wecsermons_resources.uid
			   inner join tx_wecsermons_resource_types
			    on tx_wecsermons_resources.type = tx_wecsermons_resource_types.uid
			  where tx_wecsermons_resource_types.typoscript_object_name = '".$lConf['enclosureType']."'
			   and ".$tableToList.".pid in (".$this->conf['pidList'].")
			   ".$this->cObj->enableFields($tableToList).chr(10).
			 ((t3lib_div::inList($this->internal['orderByList'],$this->internal['orderBy'])) ? "order by ".$this->internal['orderBy']." desc" : '').chr(10).
			 "limit ".$this->internal['results_at_a_time'];
		// get our results
		$res = $GLOBALS['TYPO3_DB']->sql_query($stmt);

		$sermons = array();
		$content = '';

		//	Iterate over the matching sermons.
		//	Retrieve related resource information and update the data row.

		//  !! Because only one enclosure tag is allowed per item, per RSS 2.0 spec, we process duplicates and only use the last resource
		//	that is referenced.
		while( $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc( $res ) ) {

			//	Retreive the array of related resources to this sermon record
			$resources = $this->getSermonResources( $row['uid'] );
			foreach( $resources as $resource ) {

				//	If the resource is the type allowed as an enclosure in this view, then calculate the url and size, adding to result row
				if( !strcmp( $resource['typoscript_object_name'], $lConf['enclosureType'] ) ) {

					// Merge resource and sermon rows together, so that all fields are available via TypOScript.
					$row = array_merge( $resource, $row );

					//	Retrieve a typolink conf that tells us how to render the link to the resource attachment. Must be provided by admin!
					$this->local_cObj->start($resource);
					$typolinkConf = $lConf['tx_wecsermons_resources.']['resource_types.'][$resource['typoscript_object_name'].'.']['typolink.'];

					// Instantiate a TypoScript parser for parsing the tx_wecapi_list setup config
					$ts_parser = t3lib_div::makeInstance('t3lib_TSparser');
					list(,$wecapi_list) = $ts_parser->getVal('plugin.tx_wecapi_list',$GLOBALS['TSFE']->tmpl->setup);

					// If setCurrent is not set to a value (case where user does not have wec constants installed and wec_sermons is used out of the box), then set it to the environment variable TYPO3_SITE_URL
					if( !$wecapi_list['tag_rendering.']['item_enclosure.']['setCurrent'] ) {
						$ts_parser->setVal(
							'setCurrent',
							$GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_wecapi_list.']['tag_rendering.']['item_enclosure.'],
							array(
								0 => t3lib_div::getIndpEnv('TYPO3_SITE_URL')
							)
						);
					}

					//	Render the relative and absolute paths to the file
					$relPath = $this->local_cObj->typolink_URL( $typolinkConf );
					$absPath = t3lib_div::getFileAbsFileName($relPath);

					//	Retrieve file info for the file.
					$fileInfo = t3lib_basicFileFunctions::getTotalFileInfo( $absPath );

					$row['size'] = $fileInfo['size'];
					$row['enclosure_url'] = $relPath;
					$row['mime_type'] = $resource['mime_type'];
					$row['summary'] = $resource['summary'];
					$row['subtitle'] = $resource['subtitle'];

					//	Calculate the approximate duration of the file, based on specifed bitrate
					$duration = 0;
					$sec = 0;

					if( t3lib_div::testInt($this->conf['bitrate']) ) {
						$duration = (float) (($fileInfo['size'] * 8) / 1024) / $this->conf['bitrate'];
						$sec = $duration % 60;
						$sec = strlen( $sec ) < 2 ? '0'.$sec : $sec;
						$row['duration'] = (round( $duration / 60)) .':'.$sec;
					}
					else
						$row['duration'] = 0;

					// Check if we should link to the resource single view or the sermon single view
					if( $lConf['itemLinkToResource'] )
						$row['item_link'] = $this->getUrlToSingle( 0, 'tx_wecsermons_resources', $resource['uid'], $row['uid'] );
					else
						$row['item_link'] = $this->getUrlToSingle( 0, $tableToList, $row['uid'] );

					//	Replace brackets and ampersands with % urlencoded or html entities
					$row['item_link'] = preg_replace(array('/\[/', '/\]/', '/&/'), array('%5B', '%5D', '&#38;') , $row['item_link']);

				}

			}

			//	If result row has speakers related to it, retrieve the fullname of the first speaker and add to result row as 'author'
			if( $row['speakers_uid'] ) {

				$this->internal['orderByList'] = $lConf['tx_wecsermons_speakers.']['orderByList'];
				$this->internal['orderBy'] = $lConf['tx_wecsermons_speakers.']['orderBy'];

				//	Query for related speakers (again, since we're using exotic irre-intermediate tables... can't use stock pi_exec_query())
				$stmt = "select distinct tx_wecsermons_speakers.*
					  from tx_wecsermons_speakers
					   inner join tx_wecsermons_sermons_speakers_rel
					    on tx_wecsermons_speakers.uid = tx_wecsermons_sermons_speakers_rel.speakerid
					  where tx_wecsermons_sermons_speakers_rel.sermonid = ".$row['uid'].chr(10).
					   $this->cObj->enableFields('tx_wecsermons_speakers').chr(10).
					  ((t3lib_div::inList($this->internal['orderByList'],$this->internal['orderBy'])) ? "order by ".$this->internal['orderBy']." desc" : '').chr(10).
					  'limit 1';
			   
				$speakerRes = $GLOBALS['TYPO3_DB']->sql_query($stmt);

				//	Retreive only the first speaker
				$speaker = $GLOBALS['TYPO3_DB']->sql_fetch_assoc( $speakerRes );
				$row['author'] = $speaker ? $speaker['fullname'] : '';

			}


			//	Collect each modified row into a container array for passing to tx_wecapi_list::getContent
			//	Only add to sermons array if enclosure is specified and item's link has been set, or if enclosureType is not specified at all.
			if( ($lConf['enclosureType'] && $row['item_link']) || !$lConf['enclosureType'] ) {
				$sermons[] = $row;	// enclosureType not specified, so we always add to the array
			}

		}

		//	Call wecapi_list to retrieve the front-end content of this row of records.
		return tx_wecapi_list::getContent( $this, $sermons, $tableToList );

	}

	/**
	* singleView: Generates the SINGLE view of a sermon record.
	*
	* @param	string		$content: Any previous content that this function will append itself to.
	* @param	array		$lConf: Locally scoped configuration array from TypoScript for a single view
	* @return	string		Complete single view content
	*/
	function singleView($content,$lConf)	{

		//	Set the layout (into internal storage)
		$this->internal['layoutCode'] = $this->getConfigVal( $this, 'layout', 'sDEF', 'layoutCode', $lConf, 'BRIEF' );

		//	Set the current table internal variable from recordType querystring value
		$this->internal['currentTable'] = $this->piVars['recordType'] ? htmlspecialchars( $this->piVars['recordType'] ) : $this->getConfigVal( $this, 'detail_table', 'slistView', 'detailTable', $this->conf, 'tx_wecsermons_sermons' );

		// Check if search words were posted back to this page. If so, then error as pidSearchView needed to be set.
		if( $this->piVars['sword'] ) {

			return $this->throwError(
				'WEC Sermon Management System Error!',
				'A search query was directed to this page which is configured with the SINGLE view.',
				'Please configure the constant "pidSearchView" from the Constant Editor'
			);


		}

		//	Check if table is specified and in allowedTables
		if( ! t3lib_div::inList( $this->conf['allowedTables'], $this->internal['currentTable']  ) ) {

			return $this->throwError(
				'WEC Sermon Management System Error!',
				'Row from requested table was not listed in the "allowedTables" typoscript configuration.',
				'Requested Table: ' . $this->internal['currentTable']  . '. allowedTables: ' . $this->conf['allowedTables']
			);

		}

		//	Check if showUid is an int
		//	(slightly tricky because we don't need showUid if we're doing CURRENT/PREVIOUS not the stock SINGLE code)
		if( ! t3lib_div::testInt( $this->piVars['showUid'] ) && !strcmp( 'SINGLE', $this->internal['currentCode'] ) ) {

			return $this->throwError(
				'WEC Sermon Management System Error!',
				'UID for requested resource was not valid.'.
				'Requested UID: ' . $this->piVars['showUid']
			);

		}


		//	Branch between processing resource records and other records
		if( $this->internal['currentTable'] == 'tx_wecsermons_resources' ) {	// If current table is resources

			//	TODO: allow specification of what record to draw from TypoScript
			$resource = $this->getSermonResources( '' , $this->piVars['showUid'] );

			$this->internal['currentRow'] = $resource[0];

			//	Retrieve the template name from the resource

			$templateName = $this->internal['currentRow']['template_name'] ?
				$this->internal['currentRow']['template_name'] :
				'TEMPLATE_' . trim( $this->internal['currentRow']['marker_name'], '#' );

			if( !strcmp( '0', $this->internal['currentRow']['type'] ) || !strcmp( 'default', $this->internal['currentRow']['type'] ) )
				$templateName = $this->conf['defaultTemplate'];

			$this->loadTemplate();
			$this->template['single'] = $this->getNamedSubpart( $templateName, $this->template['total'] );

			if(! $this->template['single'] ) {

				return $this->throwError(
					'WEC Sermon Management System Error!',
					'Unable to retrieve content for specified template.',
					'Requested Template: ' . $templateName
				);
			}

			//	Report an error if we couldn't load the ###CONTENT### subpart
			if( ! $this->cObj->getSubpart($this->template['single'], '###CONTENT###' ) ) {

				return $this->throwError(
					'WEC Sermon Management System Error!',
					'Unable to retrieve ###CONTENT### subpart from specified template.',
					sprintf (
							'Requested Template: ###TEMPLATE_LIST_%s###

							Template File: %s
							',
							$this->internal['layoutCode'],
							$this->conf['templateFile']
						)
					 );
			}

			//	Process sermon & related markers

			//	Store the current table and row while we switch to another table for a moment
			$this->internal['previousTable'] = $this->internal['currentTable'];
			$this->internal['currentTable'] = 'tx_wecsermons_sermons';
			$this->internal['previousRow'] = $this->internal['currentRow'];

			$this->internal['currentRow'] = $this->pi_getRecord($this->internal['currentTable'],$this->piVars['sermonUid']);
			$this->template['single'] = $this->pi_list_row( $lConf, $this->getMarkerArray('tx_wecsermons_sermons',$this->template['single']), $this->template['single'], $this->internal['currentRow'] );

			//	Restore the previous table and row
			$this->internal['currentTable'] = $this->internal['previousTable'];
			$this->internal['currentRow'] = $this->internal['previousRow'];

		}
		else {	// Process record types other than resources

			//	Retrieve the template key, which is the translation between the real table name and the template naming.
			$templateKey = $this->getTemplateKey( $this->internal['currentTable'] );
			$this->template['single'] = $this->getNamedTemplateContent( $templateKey );

			//	Report an error if we couldn't pull up the template.
			if(! $this->template['single'] ) {

				return $this->throwError(
					'WEC Sermon Management System Error!',
					'Unable to retrieve content for specified template.',
					sprintf (
						'Requested Template: ###TEMPLATE_%s_%s###',
						strtoupper( $templateKey ),
						$this->internal['layoutCode']
					)
				 );
			}

			//	Check if the 'CURRENT' view was requested, and retrieve the record marked as current
			//	TODO: allow specification of what record to draw from TypoScript
			if( !strcmp( 'CURRENT', $this->internal['currentCode'] )
				&& ( !strcmp( 'tx_wecsermons_sermons', $this->internal['currentTable']) || !strcmp( 'tx_wecsermons_series', $this->internal['currentTable']) ) ) {

				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
					'*',
					$this->internal['currentTable'],
					"current='1'".$this->cObj->enableFields($this->internal['currentTable'])
				);

				$row = $this->internal['currentRow'] = $GLOBALS['TYPO3_DB']->sql_fetch_assoc( $res );

				//	Report an error if no row was returned.
				if(! $row['uid'] ) {

					return $this->throwError(
						'WEC Sermon Management System Error!',
						'No record marked "current" was found. Please flag a record as current before using the "CURRENT" display.',
						sprintf (
							'Display Requested: %s',
							$this->internal['currentCode']
						)
					 );
				}
			}
			//	Check if the 'PREVIOUS' view was requested, and retrieve the record previous to record marked as current
			//	TODO: allow specification of what record to draw from TypoScript
			elseif( !strcmp( 'PREVIOUS', $this->internal['currentCode'] )
				&& ( !strcmp( 'tx_wecsermons_sermons', $this->internal['currentTable']) || !strcmp( 'tx_wecsermons_series', $this->internal['currentTable']) ) ) {

				// Retrieve the record marked current
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
					'*',
					$this->internal['currentTable'],
					"current='1'"
				);

				$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc( $res );

				// Accumulate our filtering criteria
				// (key criteria being the occurrence_date/end_date of the current record, so we can get the 'next' one)
				$pidList = $this->pi_getPidList($this->conf['pidList'],$this->conf['recursive']);
				$WHERE = !strcmp( 'tx_wecsermons_sermons', $this->internal['currentTable']) ? ('occurrence_date > 1 and occurrence_date < ' . $row['occurrence_date']) : ('enddate > 1 and enddate < '. $row['enddate']);
				$WHERE .= $this->cObj->enableFields($this->internal['currentTable']);
				$WHERE .= ' AND '.$this->internal['currentTable'].'.pid IN ('.$pidList.')';

				// Retreive the record previous to the current record, determining by occurrence_date of sermons or enddate of series
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
					'*',
					$this->internal['currentTable'],
					$WHERE,
					'',
					!strcmp( 'tx_wecsermons_sermons', $this->internal['currentTable']) ? 'occurrence_date desc' : 'enddate desc'
				);

				$row = $this->internal['currentRow'] = $GLOBALS['TYPO3_DB']->sql_fetch_assoc( $res );

				//	Report an error if no row was returned.
				if(! $row['uid'] ) {

					return $this->throwError(
						'WEC Sermon Management System Error!',
						'No record marked "current" was found. Please flag a record as current before using the "CURRENT" display.',
						sprintf (
							'Display Requested: %s',
							$this->internal['currentCode']
						)
					 );
				}
			}
			else
				$this->internal['currentRow'] = $row = $this->pi_getRecord($this->internal['currentTable'],$this->piVars['showUid']);

		}

		// This appends the title of the record we're viewing to the HTML TITLE tag, for improved searching
		$field = $GLOBALS['TCA'][$this->internal['currentTable']]['ctrl']['label'];
		if ( $field && $this->conf['substitutePageTitle'] && !(strpos( $GLOBALS['TSFE']->page['title'], $row[$field] ) === FALSE) )  {
			$GLOBALS['TSFE']->indexedDocTitle .= ' : ' .$row[$field];
			$GLOBALS['TSFE']->page['title'] .= ' : ' .$row[$field];
		}

		// If description is present, and option is enabled - append descrption to meta description tag.
		if( $row['description'] && $lConf['enableMetaDescription'] )
			$GLOBALS['TSFE']->pSetup['meta.']['description'] = strip_tags($row['description']) . ' - ' . $GLOBALS['TSFE']->pSetup['meta.']['description'];

		$this->template['content'] = $this->cObj->getSubpart( $this->template['single'], '###CONTENT###' );
		$this->template['item'] = $this->cObj->getSubpart( $this->template['single'], '###SERIES_SERMONS###' );

		//	Retrieve the markerArray for the right table
		$markerArray = $this->getMarkerArray( $this->internal['currentTable'], $this->template['content'] );

		//	Process row
		$content .= $this->cObj->substituteSubpart( $this->template['single'], '###CONTENT###', $this->pi_list_row($lConf, $markerArray, $this->template['content'], $this->internal['currentRow'] ) );

		//	Parse for additional markers. Browse results, etc.
		$markerArray = $this->getMarkerArray();

		//	Call pi_list_row to substitute last markers and return results
		return $this->pi_list_row( $lConf, $markerArray, $content );

	}

	/**
	* searchView: Generates the SEARCH view of the SMS
	*
	* @param	string		$content: Any previous content that this function will append itself to.
	* @param	array		$lConf: Locally scoped configuration array from TypoScript for search view
	* @return	string		Complete search view content
	*/
	function searchView($content,$lConf)	{

		//	Set the layout in our internal storage
		$this->internal['layoutCode'] = $this->getConfigVal( $this, 'layout', 'sDEF', 'layoutCode', $lConf, 'BRIEF' );

		return "\n\n".$this->pi_list_searchbox($lConf);

	}

	/**
	* pi_list_searchbox: Generates a searchbox using a marker based template.
	*
	* 		Template subpart should be named ###SEARCHBOX<integer>###, where the integer is the 'Layout' option chosen from the fe plugin.
	*
	* @param	array		$lConf: Locally scoped configuration array from TypoScript for search view
	* @return	string		Complete search view content
	*/
	function pi_list_searchbox($lConf) {

		//	Retrieve searchbox template
		$searchBoxTemplate = $this->getNamedTemplateContent( 'searchbox' );

		//	Report an error if we couldn't pull up the template.
		if(! $searchBoxTemplate ) {

			return $this->throwError(
				'WEC Sermon Management System Error!',
				'Unable to retrieve content for specified template.',
				sprintf (
					'Requested Template: ###TEMPLATE_SEARCHBOX_%s###

					Template File: %s
					',
					$this->internal['layoutCode'],
					$this->conf['templateFile']
				)
			 );

		}
		//	Retrieve the marker array
		$markerArray = $this->getMarkerArray('searchbox');

		$markerArray['###SEARCH_BUTTON_NAME###'] = $this->pi_getLL('pi_list_searchBox_search');

		//	Find the PID that we should post form data to
		$pid = $this->getConfigVal( $this, '', '', 'pidSearchView', $this->conf, $GLOBALS['TSFE']->id );

		$markerArray['###FORM_ACTION###'] = $this->cObj->typolink_URL( array( 'parameter' => $pid ) );


		/*	This commented section will enable us to perform deeper searches in the future by searching through multiple tables.

		$tables = t3lib_div::trimExplode( ',', $lConf['searchTables'], 1 );

		$selectContent ='';
		foreach( $tables as $tableName ) {

			//	Grab the tablename from locallang_db
			$llName = $GLOBALS['TSFE']->sL('LLL:EXT:wec_sermons/locallang_db.php:'.$tableName);
			$option = "\n<option value=\"" . $tableName . '">'. $llName. "</option>";
			$selectContent .= $option;
		}

		$markerArray['###SEARCHBOX_OPTIONS###'] = $this->cObj->stdWrap(
			$selectContent,
			array(
				'wrap' => "\n<select class=\"".$this->pi_getClassName('searchbox-select')."\" name=\"tx_wecsermons_pi1[sword_table]\">|\n</select>"
			)
		);
		*/

		return $this->cObj->substituteMarkerArrayCached( $searchBoxTemplate, $markerArray );

	}

	/**
	* latestView: Generates the LATEST view of the SMS
	*
	* @param	string		$content: Any previous content that this function will append itself to.
	* @param	array		$lConf: Locally scoped configuration array from TypoScript for list view
	* @return	string		Complete list view content
	*/
	function latestView($content,$lConf)	{

		//	Set the layout in our internal storage
		$this->internal['layoutCode'] = $this->getConfigVal( $this, 'layout', 'sDEF', 'layoutCode', $lConf, 'BRIEF' );

		// If a single element should be displayed, jump to single view
		if ($this->piVars['showUid'] && $this->conf['enableSmartDisplay'])	{

			$this->conf['singleView.']['layoutCode'] = $this->internal['layoutCode'];
			return $this->singleView('',$this->conf['singleView.'] );

		}

		//	Intialize query params if not set
		if (!isset($this->piVars['pointer']))	$this->piVars['pointer']=0;
		$this->internal['currentTable'] = $this->getConfigVal( $this, 'detail_table', 'slistView', 'detailTable', $this->conf, 'tx_wecsermons_sermons' );


		if( $lConf['useCreationDate'] ) {
			$this->internal['orderBy'] = 'crdate';
		}
		else {

			//	If listing sermon records, check if order was specified in the FE Plugin and load from there. Otherwise load from typoscript, or 'title' as default.
			$this->internal['orderBy'] = !strcmp( $this->internal['currentTable'], 'tx_wecsermons_sermons' ) ?
			$this->getConfigVal( $this, 'sermons_order_by', 'slistView', 'orderBy', $lConf[$this->internal['currentTable'].'.'], 'occurrence_date' ) :
			$lConf[$this->internal['currentTable'].'.']['orderBy'];

		}

		// Initialize some query parameters, and internal variables
		$this->internal['maxPages']=t3lib_div::intInRange($lConf['maxPages'],0,1000,5);		// The maximum number of "pages" in the browse-box: "Page 1", "Page 2", etc.
		$this->internal['dontLinkActivePage']=$lConf['dontLinkActivePage'];
		$this->internal['showFirstLast']=$lConf['showFirstLast'];
		$this->internal['pagefloat']=$lConf['pagefloat'];
		$this->internal['showRange']=$lConf['showRange'];
		$this->internal['orderByList']=$lConf[$this->internal['currentTable'].'.']['orderByList'];

		//	Hardcode descending = 1 for latest view
		$this->internal['descFlag']='1';

		$this->loadTemplate('latest');

		//	Report an error if we couldn't pull up the template.
		if(! $this->template['list'] ) {

			return $this->throwError(
				'WEC Sermon Management System Error!',
				'Unable to retrieve content for specified template.',
				sprintf(
					'Requested Template: ###TEMPLATE_LATEST_%s###

					Template File: %s
					',
					$this->internal['layoutCode'],
					$this->conf['templateFile']
				)
			 );

		}

		//	Report an error if we couldn't load the ###CONTENT### subpart
		if( ! $this->cObj->getSubpart($this->template['list'], '###CONTENT###' ) ) {

			return $this->throwError(
				'WEC Sermon Management System Error!',
				'Unable to retrieve ###CONTENT### subpart from specified template.',
				sprintf (
					'Requested Template: ###TEMPLATE_LIST_%s###

					Template File: %s
					',
					$this->internal['layoutCode'],
					$this->conf['templateFile']
				)
		 	);
		}

		$content = $this->cObj->substituteSubpart( $this->template['list'], '###CONTENT###', $this->pi_list_makelist($lConf, $this->template['content'] ) );


		//	Parse for additional markers. Browse results, etc.
		$markerArray = $this->getMarkerArray();

		//	Call pi_list_row to substitute last markers and return results
		return $this->pi_list_row( $lConf, $markerArray, $content );

	}

	/**
	* listView: Generates the LIST view of the SMS
	*
	* @param	string		$content: Any previous content that this function will append itself to.
	* @param	array		$lConf: Locally scoped configuration array from TypoScript for list view
	* @return	string		Complete list view content
	*/
	function listView($content,$lConf)	{

		//	Set the layout in our internal storage
		$this->internal['layoutCode'] = $this->getConfigVal( $this, 'layout', 'sDEF', 'layoutCode', $lConf, 'BRIEF' );

		// If a single element should be displayed, jump to single view
		if ($this->piVars['showUid'] && $this->conf['enableSmartDisplay'])	{

			$this->conf['singleView.']['layoutCode'] = $this->internal['layoutCode'];
			return $this->singleView('',$this->conf['singleView.']);

		}

		//	Otherwise continue with list view

		//	Intialize query params if not set
		if (!isset($this->piVars['pointer']))	$this->piVars['pointer']=0;
		$this->internal['currentTable'] = $this->getConfigVal( $this, 'detail_table', 'slistView', 'detailTable', $this->conf, 'tx_wecsermons_sermons' );

		// Initialize some query parameters, and internal variables
		$this->internal['descFlag']=$lConf[$this->internal['currentTable'].'.']['descFlag'];
		$this->internal['maxPages']=t3lib_div::intInRange($lConf['maxPages'],0,1000,5);		// The maximum number of "pages" in the browse-box: "Page 1", "Page 2", etc.
		$this->internal['dontLinkActivePage']=$lConf['dontLinkActivePage'];
		$this->internal['showFirstLast']=$lConf['showFirstLast'];
		$this->internal['pagefloat']=$lConf['pagefloat'];
		$this->internal['showRange']=$lConf['showRange'];

		$this->internal['orderByList']=$lConf[$this->internal['currentTable'].'.']['orderByList'];

		//	If listing sermon records, check if order was specified in the FE Plugin and load from there. Otherwise load from typoscript, or 'title' as default.
		if( !strcmp( $this->internal['currentTable'], 'tx_wecsermons_sermons' ) )
			$this->internal['orderBy'] = $this->getConfigVal( $this, 'sermons_order_by', 'slistView', 'orderBy', $lConf[$this->internal['currentTable'].'.'], 'title' );
		else
			$this->internal['orderBy']=$lConf[$this->internal['currentTable'].'.']['orderBy'];

		//	If request is for latest view
		if( !strcmp( $this->internal['currentCode'], 'LATEST' ) ) {

			// Only use orderBy from typoscript config
			$this->internal['orderBy']=$lConf[$this->internal['currentTable'].'.']['orderBy'];

		}

		/*	This commented section will enable us to search through multiple tables to perform deeper searches in the future

		//	Check if selected table is in list of allowed tables, throw error if necessary
		if($this->piVars['sword_table'] && ! t3lib_div::inList( $this->conf['searchView.']['searchTables'], trim( $this->piVars['sword_table'] ) ) ) {
			return $this->throwError(
				'WEC Sermon Management System Error',
				"The table name '" . $this->piVars['sword_table'] . "' was not in the allowed list of tables: '" . $this->conf['searchView.']['searchTables'],
				"Please check the TypoScript configuration for the setting of 'searchView.searchTables'"
			);
		}
		*/

		$this->internal['sword_table'] = 'tx_wecsermons_sermons';	//	Migrate to $this->piVars['sword_table'] in future
		$this->internal['searchFieldList']=$this->conf['searchView.']['searchFieldArray.'][$this->internal['sword_table'].'.']['searchFieldList'];


		//	Load the template file. By default, this populates the $this->template array with the list template from ###TEMPLATE_LIST{layoutCode}###
		$this->loadTemplate();

		//	Report an error if we couldn't pull up the template.
		if(! $this->template['list'] ) {

			return $this->throwError(
				'WEC Sermon Management System Error!',
				'Unable to retrieve content for specified template.',
				sprintf (
					'Requested Template: ###TEMPLATE_LIST_%s###

					Template File: %s
					',
					$this->internal['layoutCode'],
					$this->conf['templateFile']
				)
			 );

		}

		//	Report an error if we couldn't load the ###CONTENT### subpart
		if( ! $this->cObj->getSubpart($this->template['list'], '###CONTENT###' ) ) {

			return $this->throwError(
				'WEC Sermon Management System Error!',
				'Unable to retrieve ###CONTENT### subpart from specified template.',
				sprintf (
					'Requested Template: ###TEMPLATE_LIST_%s###

					Template File: %s
					',
					$this->internal['layoutCode'],
					$this->conf['templateFile']
				)
			 );
		}

		$content = $this->cObj->substituteSubpart( $this->template['list'], '###CONTENT###', $this->pi_list_makelist($lConf, $this->template['content'] ) );

		//	Parse for additional markers. Browse results, etc.
		$markerArray = $this->getMarkerArray();

		//	Call pi_list_row to substitute last markers and return results
		return $this->pi_list_row( $lConf, $markerArray, $content );

	}

	/**
	* pi_list_makelist: Returns the list of items based on the input SQL result pointer
	* For each result row the internal var, $this->internal['currentRow'], is set with the row returned.
	*
	* @param	pointer		Result pointer to a SQL result which can be traversed.
	* @param	string		Marker based template, which will be processed and returned with populated data using $this->substituteMarkerArrayCached  ()
	* @return	string		Output HTML, wrapped in <div>-tags with a class attribute
	*/
	function pi_list_makelist($lConf, $template)	 {
		//	 Gather all our output into $content
		$content = '';
		$subpartArray = array();
		$groupTable = $this->getConfigVal( $this, 'group_table', 'slistView', 'groupTable', $this->conf );
		$previousTable = '';
		$previousRow = array();

		//	If grouping was specified, branch to process group list
		if( $groupTable ) {

			$detailTable = $this->getConfigVal( $this, 'detail_table', 'slistView', 'detailTable', $this->conf );
			$this->template['group'] = $this->cObj->getSubpart( $template, '###GROUP###' );

			//	Change the orderBy clause to match the group table
			$this->internal['orderByList']=$lConf[$groupTable.'.']['orderByList'];
			$this->internal['orderBy']= $lConf[$groupTable.'.']['orderBy'];
			$this->internal['descFlag']=$lConf[$groupTable.'.']['descFlag'];

			//	Check if rendering LATEST view, making changes to ordering as appropriate.
			if( !strcmp( $this->internal['currentCode'], 'LATEST' ) ) {
				$this->internal['descFlag']='1';
				if( $lConf['useCreationDate'] )  $this->internal['orderBy']='crdate';
			}

			// Run a series of checks before branching to grouping logic, return error if necessary
			if( $groupTable == '' || ! $this->template['group'] ) {

				return $this->throwError(
					'WEC Sermon Management System Error!',
					'"group_table" option was specified, but no ###GROUP### tag was found in the template.',
					'Specified Template file: ' . $this->internal['templateFile']
				);
			}


			//	Check if group_table is in list of allowed tables
			if( ! t3lib_div::inList( $this->conf['allowedTables'], $groupTable ) ) {

				return $this->throwError(
					'WEC Sermon Management System Error!',
					'Table specified in "Group By" option, ['.$groupTable.'], is not in the list of allowed tables specified in TypoScript configuration: plugin.tx_wecsermons_pi1.allowedTables',
					'Allowed tables:'.$this->conf['allowedTables']
				);

			}
			//	Check if detail_table is in list of allowed tables
			if( ! t3lib_div::inList( $this->conf['allowedTables'], $detailTable ) ) {

				return $this->throwError(
					'WEC Sermon Management System Error!',
					'Table specified in "Detail" option, ['.$detailTable.'], is not in the list of allowed tables specified in TypoScript configuration: plugin.tx_wecsermons_pi1.allowedTables',
					'Allowed tables: '.$this->conf['allowedTables']
				);

			}
			//	Search TCA for relation to previous table where columns.[colName].config.foreign_table = $this->internal['groupTable']
			$this->internal['currentTable'] = $this->internal['groupTable'] = $groupTable;

			$groupTemplate = $this->template['group'];
			$markerArray = $this->getMarkerArray( $groupTable, $groupTemplate );

			$groupContent = '';
			$detailContent = '';

			//	Get the total count, and set the # results per page
			list($this->internal['res_count']) = $GLOBALS['TYPO3_DB']->sql_fetch_row( $this->getGroupResult( $groupTable, $detailTable, $lConf, 1 ) );
			$this->internal['results_at_a_time'] = $this->conf['maxGroupResults'];

			// Retreive resultset
			$res = $this->getGroupResult( $groupTable, $detailTable, $lConf );

			//	Retreive marker array and template for the detail table
			$detailTemplate = $this->template['item'] = $this->getNamedSubpart( 'ITEM', $template );
			$detailMarkArray = $this->getMarkerArray( $detailTable, $detailTemplate);

			//	Counter for number of group records, and detail records shown on a 'page'
			$groupCount = 0;
			$detailCount = 0;

			//	Iterate every record in groupTable
			while( $groupCount <= $lConf['maxGroupResults']
				&& $this->internal['currentRow'] = $GLOBALS['TYPO3_DB']->sql_fetch_assoc( $res ) ) {

				//	Check if rendering LATEST view, making changes to ordering as appropriate.
				if( !strcmp( $this->internal['currentCode'], 'LATEST' ) ) {
					$this->internal['descFlag']='1';
					if( $lConf['useCreationDate'] )  $this->internal['orderBy']='crdate';
				}


				//	Process the current row
				$groupContent = $this->pi_list_row( $lConf, $markerArray, $groupTemplate, $this->internal['currentRow'], $groupCount );

				if( $detailTemplate ) {

					//	Store previous row, table and order by as we switch to retreiving detail
					$this->internal['previousRow'] = $this->internal['currentRow'];
					$this->internal['previousTable'] = $this->internal['currentTable'];
					$this->internal['currentTable'] = $detailTable;
					$this->internal['previousOrderByList'] = $this->internal['orderByList'];
					$this->internal['previousOrderBy'] = $this->internal['orderBy'];
					$this->internal['previousdescFlag'] = $this->internal['descFlag'];
					$this->internal['orderByList']=$lConf[$detailTable.'.']['orderByList'];
					$this->internal['orderBy']=$lConf[$detailTable.'.']['orderBy'];
					$this->internal['descFlag']=$lConf[$detailTable.'.']['descFlag'];

					//	Store results_at_a_time and switch to hardcoded value of 1000 for detail. This is because we never want a limited list of detail records
					//	in a grouped view.
					$this->internal['prev_results_at_a_time'] = $this->internal['results_at_a_time'];
					$this->internal['results_at_a_time'] = 1000;

					//	We need to temporarily set pointer to 0, as we never want a second page of detail records.
					$prevPointer = $this->piVars['pointer'];
					$this->piVars['pointer'] = 0;

					//	Exec query on detail table, for every record related to our group record
					#$detailRes = $this->pi_exec_query( $detailTable, 0, ' AND find_in_set('.$this->internal['previousRow']['uid'].','.$this->internal['currentTable'].'.'.$foreign_column . ')' );
					$detailUids = $this->getRelatedRecords($this->internal['previousRow']['uid'],$detailTable,$groupTable);
			
					if ( is_array($detailUids) ) {
						$detailRes = $this->pi_exec_query( $detailTable, 0, ' AND uid in ( ' . implode(",", $detailUids) . ')' );

						//	Resore results_at_a_time and pointer values
						$this->internal['results_at_a_time'] = $this->internal['prev_results_at_a_time'];
						$this->piVars['pointer'] = $prevPointer;

						$detailInnerCount = 0;

						//	Iterate over every related detail record to our group record
						while( $this->internal['currentRow'] = $GLOBALS['TYPO3_DB']->sql_fetch_assoc( $detailRes ) ) {

							$detailCount++;
							$detailInnerCount++;

							$detailContent .= $this->pi_list_row( $lConf, $detailMarkArray, $detailTemplate, $this->internal['currentRow'], $detailInnerCount );
						}
					}
				}

				$subpartArray = array(
					'###GROUP###' => $groupContent,
					'###ITEM###' => $detailContent
				);

				$content .= $this->cObj->substituteMarkerArrayCached( $this->template['content'], array(), $subpartArray );
				$detailContent = '';

				$groupCount++;

				//	Restore row,  table, and orderBy from internal storage
				$this->internal['currentRow'] = $this->internal['previousRow'];
				$this->internal['currentTable'] = $this->internal['previousTable'];
				$this->internal['orderByList'] = $this->internal['previousOrderByList'];
				$this->internal['orderBy'] = $this->internal['previousOrderBy'];
				$this->internal['descFlag'] = $this->internal['previousdescFlag'];

			}

		}	//	End if group
		else {	//	No group found, just provide a straight list

			//	Get tables to list, using 'tx_wecsermons_sermons' if none specified
			$tableToList = $this->getConfigVal( $this, 'detail_table', 'slistView', 'detailTable', $this->conf, 'tx_wecsermons_sermons' );

			//	Load the correct marker array and load the item template
			$itemTemplate = $this->cObj->getSubpart( $template, '###ITEM###' );
			$markerArray = $this->getMarkerArray( $tableToList, $itemTemplate );

			$this->internal['currentTable'] = $tableToList;
			$this->internal['results_at_a_time'] = t3lib_div::intInRange($lConf['maxdetailResults'],1,1000);

			if($this->piVars['listView']['startDate']) {
				$startDate = $this->piVars['listView']['startDate'];
			} else {
				$startDate = $this->getConfigVal( $this, 'startDate', 'slistView', 'startDate', $lConf );
			}

			if($this->piVars['listView']['endDate']) {
				$endDate = $this->piVars['listView']['endDate'];
			} else {
				$endDate = $this->getConfigVal( $this, 'endDate', 'slistView', 'endDate', $lConf );
			}

			// Calculate startdate or end date if specified for sermon or series records
			if( (!strcmp( $tableToList, 'tx_wecsermons_sermons' ) || !strcmp( $tableToList, 'tx_wecsermons_series' ) ) 
				&& ($startDate || $endDate )
			) {
		
				// Check if date() function was specified in startdate, and calculate new date if necessary
				if( strstr($startDate,'date()') ) {
					if( strstr($startDate,'-') ) {

						$formula = explode('-',$startDate);
						if( !strcmp($formula[0],'date()' ) && t3lib_div::testint($formula[1]) )
							$startDate = time() - $formula[1]*86400; // calculate difference in days
						else
							$startDate = $formula[0]*86400 - time(); // calculate difference in days
					}
					elseif( strstr($startDate,'+') ) {

						$formula = explode('+',$startDate);
						if( !strcmp($formula[0],'date()' ) && t3lib_div::testint($formula[1]) )
							$startDate = time() + $formula[1]*86400; // calculate difference in days
						else
							$startDate = $formula[0]*86400 + time(); // calculate difference in days
					}
				}
				elseif( strstr($startDate, '/') || strstr($startDate, '-')) { // If startdate is string that needs to be converted to unixtime
					$startDate = $startDate ? strtotime($startDate) : '';
				}

				// Check if date() function was specified in enddate, and calculate new date if necessary
				if( strstr($endDate,'date()') ) {
					if( strstr($endDate,'-') ) {

						$formula = explode('-',$endDate);
						if( !strcmp($formula[0],'date()' ) && t3lib_div::testint($formula[1]) )
							$endDate = time() - $formula[1]*86400; // calculate difference in days
						else
							$endDate = $formula[0]*86400 - time(); // calculate difference in days
					}
					elseif( strstr($endDate,'+') ) {

						$formula = explode('+',$endDate);
				
						if( !strcmp($formula[0],'date()' ) && t3lib_div::testint($formula[1]) )
							$endDate = time() + $formula[1]*86400; // calculate difference in days
						else
							$endDate = $formula[0]*86400 + time(); // calculate difference in days
					}
				}
				elseif( strstr($endDate, '/') || strstr($endDate, '-')) { // If enddate is string that needs to be converted to unixtime
					$endDate = $endDate ? strtotime($endDate) : '';
				}

				// Add filter to where clause
				$where = '';
				$where .= ($startDate && !strcmp( $tableToList, 'tx_wecsermons_sermons' )) ? ' AND occurrence_date >= ' .  $startDate : '';
				$where .= ($endDate && !strcmp( $tableToList, 'tx_wecsermons_sermons' )) ? ' AND occurrence_date <= ' .  $endDate : '';
				$where .= ($startDate && !strcmp( $tableToList, 'tx_wecsermons_series' )) ? ' AND startdate >= ' .  $startDate : '';
				$where .= ($endDate && !strcmp( $tableToList, 'tx_wecsermons_series' )) ? ' AND enddate <= ' .  $endDate : '';
			
			}
			// Get number of records:
			$res = $this->pi_exec_query($tableToList,1, $where);
			list($this->internal['res_count']) = $GLOBALS['TYPO3_DB']->sql_fetch_row($res);

			// Make listing query, pass query to SQL database:
			$res = $this->pi_exec_query($tableToList,0,$where);

			$count = 1;
			while( $this->internal['currentRow'] = $this->internal['previousRow'] = $GLOBALS['TYPO3_DB']->sql_fetch_assoc( $res ) ) {

				$content .= $this->pi_list_row( $lConf, $markerArray, $itemTemplate, $this->internal['currentRow'], $count );

				$count++;
			}

		}

		return $content;
	}

	/**
	* pi_list_row: This function passes one row of data through a marker based template, making the appropriate substitutions, and returns the finished content.
	* 	This function is the crux of the plugin. Using an array of markers, it performs all the appropriate substitutions, matching up data fields to markers.
	*
	* 	Implements a hook for processing additional markers. tx_wecsermons_pi1->processMarker
	*
	* @param	array		$lConf: Locally scoped TypoScript configuration
	* @param	array		$markerArray: Array of typo3 tag markers as keys, and matching fieldnames as values. I.E. array( '###SERMON_TITLE###' => 'title', ... )
	* @param	string		$rowTemplate: A marker based template that defines the layout of our data on the front end
	* @param	array		$row: An associative array representing a row of data, with fieldnames as array keys and field values as array values. I.E. array( 'title' => 'Jesus Who Performs Miracles', ... )
	* @param	integer		$c: Number of current row, to determine even / odd rows
	* @return	string		A completed template subpart, populated with data from the row
	*/
	function pi_list_row($lConf, $markerArray = array(), $rowTemplate, $row ='', $c = 2)	{

		if (isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tx_wecsermons_pi1']['preProcessListItem']))   {
			$hooks =& $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tx_wecsermons_pi1']['preProcessListItem'];
			$hookReference = null;
			$hookParameters = array(
				"lConf" => &$lConf,
				"markerArray" => &$markerArray,
				"rowTemplate" => &$rowTemplate,
				"row" => &$row,
				"c" => &$c,
				"pObj" => &$this
			);
			foreach ($hooks as $hookFunction)       {
				t3lib_div::callUserFunction($hookFunction, $hookParameters, $hookReference);
			}
		}

		$wrappedSubpartArray = array();
		$subpartArray = array();

		//	Using passed markerArray, process each key and insert field content
		//	The reason we are have this looping structure is for future off-loading of this logic
		foreach( $markerArray as $key => $value ) {

			$fieldName = $value;
			$markerArray[$key] = '';

			switch( $key ) {
				
				case '###SERMON_UID###':
					if( $row[$fieldName] ) {
						$this->local_cObj->start( $row, 'tx_wecsermons_sermons' );
						$markerArray[$key] = $this->local_cObj->cObjGetSingle( $lConf['tx_wecsermons_sermons.']['uid'], $lConf['tx_wecsermons_sermons.']['uid.'] );
					}
					break;

				case '###SERMON_TITLE###':
					if( $row[$fieldName] ) {
						$this->local_cObj->start( $row, 'tx_wecsermons_sermons' );
						$markerArray[$key] = $this->local_cObj->cObjGetSingle( $lConf['tx_wecsermons_sermons.']['title'], $lConf['tx_wecsermons_sermons.']['title.'] );
					}
					break;

				case '###SERMON_SUBTITLE###':
					if( $row[$fieldName] ) {
						$this->local_cObj->start( $row, 'tx_wecsermons_sermons' );
						$markerArray[$key] = $this->local_cObj->cObjGetSingle( $lConf['tx_wecsermons_sermons.']['subtitle'], $lConf['tx_wecsermons_sermons.']['subtitle.'] );
					}
					break;

				case '###SERMON_OCCURRENCE_DATE###':
					if( $row[$fieldName] )
					{
						$fieldConf = $lConf['tx_wecsermons_sermons.']['occurrence_date.'];
						//	Wrap the date, choosing from one of three settings in typoscript
						$dateWrap = $fieldConf['strftime'] ? $fieldConf['strftime'] : $lConf['general_dateWrap.']['strftime'];
						if( ! $dateWrap ) $dateWrap = $this->conf['general_dateWrap.']['strftime'];
						$fieldConf['strftime'] = $dateWrap;

						$this->local_cObj->start( $row, 'tx_wecsermons_sermons' );
						$markerArray[$key] = $this->local_cObj->cObjGetSingle( $lConf['tx_wecsermons_sermons.']['occurrence_date'], $fieldConf);
					}
					break;

				case '###SERMON_DESCRIPTION###':
					if( $row[$fieldName] ) {
						$this->local_cObj->start( $row, 'tx_wecsermons_sermons' );
						$markerArray[$key] = $this->local_cObj->cObjGetSingle( $lConf['tx_wecsermons_sermons.']['description'], $lConf['tx_wecsermons_sermons.']['description.'] );
			}
					break;

				case '###SERMON_SCRIPTURE###':
					if( $row[$fieldName] ) {
						$this->local_cObj->start( $row, 'tx_wecsermons_sermons' );
						if ($this->conf['disableBibleGateway'] ) {
							$markerArray[$key] = $this->local_cObj->cObjGetSingle( $lConf['tx_wecsermons_sermons.']['scripture_nolink'], $lConf['tx_wecsermons_sermons.']['scripture_nolink.'] );
						} else {
							$markerArray[$key] =  $this->local_cObj->cObjGetSingle( $lConf['tx_wecsermons_sermons.']['scripture'], $lConf['tx_wecsermons_sermons.']['scripture.'] );
						}
					}
					break;

				case '###SERMON_GRAPHIC###':
					if( $row[$fieldName] ) {

						$this->local_cObj->start( $row, 'tx_wecsermons_sermons' );
						$markerArray[$key] = $this->local_cObj->cObjGetSingle( $lConf['tx_wecsermons_sermons.']['graphic'], $lConf['tx_wecsermons_sermons.']['graphic.']);
					}
					break;


				case '###SERMON_LINK###':

					if( $row['islinked'] )
						$wrappedSubpartArray[$key] = explode(
							'|',
							$this->pi_list_linkSingle(
								'|',
								$row['uid'],
								$this->conf['allowCaching'],
								array(
								'recordType' => 'tx_wecsermons_sermons',
								),
								FALSE,
								$this->conf['pidSingleView'] ? $this->conf['pidSingleView']:0
								)
						);
					else	// If islinked is false, simply return an empty array
						$wrappedSubpartArray[$key] = array( 0 => '', 1 => '');

					break;

				case '###SERMON_SERIES###':

					$subpartArray[$key] = '';
					if( $row[$fieldName] ) {

						// Store current row and table while we switch context
						$previousRow = $this->internal['currentRow'];
						$previousTable = $this->internal['currentTable'];
						$this->internal['currentTable'] = 'tx_wecsermons_series';

						$seriesTemplate = $this->cObj->getSubpart( $rowTemplate, $key );
						$seriesMarkerArray = $this->getMarkerArray('tx_wecsermons_series',$seriesTemplate);
						$seriesContent = '';

						$where = ' AND tx_wecsermons_sermons_series_rel.sermonid = ' . $row[uid] . ' ';
						$where .= $this->cObj->enableFields('tx_wecsermons_series');

						$query = 'select distinct
						tx_wecsermons_series.uid,
						tx_wecsermons_series.pid,
						tx_wecsermons_series.tstamp,
						tx_wecsermons_series.crdate,
						tx_wecsermons_series.cruser_id,
						tx_wecsermons_series.sys_language_uid,
						tx_wecsermons_series.deleted,
						tx_wecsermons_series.hidden,
						tx_wecsermons_series.starttime,
						tx_wecsermons_series.endtime,
						tx_wecsermons_series.fe_group,
						tx_wecsermons_series.title,
						tx_wecsermons_series.subtitle,
						tx_wecsermons_series.description,
						tx_wecsermons_series.scripture,
						tx_wecsermons_series.startdate,
						tx_wecsermons_series.enddate,
						tx_wecsermons_series.graphic,
						tx_wecsermons_series.alttitle,
						tx_wecsermons_series.seasons,
						tx_wecsermons_series.topics,
						tx_wecsermons_series.keywords,
						tx_wecsermons_series.resources,
						tx_wecsermons_series.islinked,
						tx_wecsermons_series.current

						from tx_wecsermons_series
							inner join tx_wecsermons_sermons_series_rel
								on tx_wecsermons_series.uid = tx_wecsermons_sermons_series_rel.seriesid
						where 1=1 ' . $where;

						$seriesRes = $GLOBALS['TYPO3_DB']->sql_query ($query);

						$count = 0;
						while( $this->internal['currentRow'] = $GLOBALS['TYPO3_DB']->sql_fetch_assoc( $seriesRes ) ) {
							//	Recursive call to $this->pi_list_row() to populate each series marker
							$seriesContent .= $this->pi_list_row( $lConf, $seriesMarkerArray, $seriesTemplate, $this->internal['currentRow'] );
							$count++;
						}
				
						//	Make sure we process any sermon tags within the series content section
						$seriesContent = $this->pi_list_row( $lConf, $this->getMarkerArray('tx_wecsermons_sermons', $seriesContent ), $seriesContent, $previousRow);

						// Restore original row and table
						$this->internal['currentRow'] = $previousRow;
						$this->internal['currentTable'] = $previousTable;
				
						//	Replace marker content with subpart, wrapping stdWrap
						if( $count > 0 )
							$subpartArray[$key] = $this->cObj->stdWrap( $seriesContent, $lConf['tx_wecsermons_sermons.']['series.'] );

					}

					break;

				case '###SERMON_SPEAKERS###':

					$subpartArray[$key] = '';
					if( $row[$fieldName] ) {

						// Store current row and table while we switch context
						$previousRow = $this->internal['currentRow'];
						$previousTable = $this->internal['currentTable'];
						$this->internal['currentTable'] = 'tx_wecsermons_speakers';

						//	Get the speakers subpart
						$speakerTemplate = $this->cObj->getSubpart( $rowTemplate, $key );
						$speakerMarkerArray = $this->getMarkerArray('tx_wecsermons_speakers',$speakerTemplate);
						$speakerContent = '';

						// our query
						$where = ' AND tx_wecsermons_sermons_speakers_rel.sermonid = ' . $row[uid] . ' ';
						$where .= $this->cObj->enableFields('tx_wecsermons_speakers');

						$query = 'select distinct
						tx_wecsermons_speakers.uid,
						tx_wecsermons_speakers.pid,
						tx_wecsermons_speakers.tstamp,
						tx_wecsermons_speakers.crdate,
						tx_wecsermons_speakers.cruser_id,
						tx_wecsermons_speakers.sys_language_uid,
						tx_wecsermons_speakers.deleted,
						tx_wecsermons_speakers.hidden,
						tx_wecsermons_speakers.fullname,
						tx_wecsermons_speakers.firstname,
						tx_wecsermons_speakers.lastname,
						tx_wecsermons_speakers.url,
						tx_wecsermons_speakers.photo,
						tx_wecsermons_speakers.alttitle,
						tx_wecsermons_speakers.email,
						tx_wecsermons_speakers.islinked

						from tx_wecsermons_speakers
							inner join tx_wecsermons_sermons_speakers_rel
								on tx_wecsermons_speakers.uid = tx_wecsermons_sermons_speakers_rel.speakerid
						where 1=1 ' . $where;

						$speakerRes = $GLOBALS['TYPO3_DB']->sql_query ($query);

						$count = 0;
						while( $this->internal['currentRow'] = $GLOBALS['TYPO3_DB']->sql_fetch_assoc( $speakerRes ) ) {

							//	Recursive call to $this->pi_list_row() to populate each speaker marker
							$speakerContent .= $this->pi_list_row( $lConf, $speakerMarkerArray, $speakerTemplate, $this->internal['currentRow'] );
							$count++;
						}

						// Restore original row and table
						$this->internal['currentRow'] = $previousRow;
						$this->internal['currentTable'] = $previousTable;


						//	Replace marker content with subpart, wrapping stdWrap
						if( $count > 0 )
							$subpartArray[$key] = $this->cObj->stdWrap( $speakerContent, $lConf['tx_wecsermons_sermons.']['speakers.'] );

					}

					break;

				case '###SERMON_TOPICS###':

					$subpartArray[$key] = '';

					if( $row[$fieldName] ) {

						// Store current row and table while we switch context
						$previousRow = $this->internal['currentRow'];
						$previousTable = $this->internal['currentTable'];
						$this->internal['currentTable'] = 'tx_wecsermons_topics';

		
						//	Load the topics subpart
						$topicTemplate = $this->cObj->getSubpart( $rowTemplate, $key );
						$topicMarkerArray = $this->getMarkerArray('tx_wecsermons_topics',$topicTemplate);
						$topicContent = '';

						// our query
						$where = ' AND tx_wecsermons_sermons_topics_rel.sermonid = ' . $row[uid] . ' ';
						$where .= $this->cObj->enableFields('tx_wecsermons_topics');

						$query = 'select distinct
						tx_wecsermons_topics.uid,
						tx_wecsermons_topics.pid,
						tx_wecsermons_topics.tstamp,
						tx_wecsermons_topics.crdate,
						tx_wecsermons_topics.cruser_id,
						tx_wecsermons_topics.sys_language_uid,
						tx_wecsermons_topics.deleted,
						tx_wecsermons_topics.hidden,
						tx_wecsermons_topics.description,
						tx_wecsermons_topics.title,
						tx_wecsermons_topics.islinked

						from tx_wecsermons_topics
							inner join tx_wecsermons_sermons_topics_rel
								on tx_wecsermons_topics.uid = tx_wecsermons_sermons_topics_rel.topicid
						where 1=1 ' . $where;

						$topicRes = $GLOBALS['TYPO3_DB']->sql_query ($query);

						$count = 0;
						while( $this->internal['currentRow'] = $GLOBALS['TYPO3_DB']->sql_fetch_assoc( $topicRes ) ) {

							//	Recursive call to $this->pi_list_row() to populate each topic marker
							$topicContent .= $this->pi_list_row( $lConf, $topicMarkerArray, $topicTemplate, $this->internal['currentRow'] );

							$count++;
						}

						// Restore original row and table
						$this->internal['currentRow'] = $previousRow;
						$this->internal['currentTable'] = $previousTable;

						//	Replace marker content with subpart
						if( $count > 0 )
							$subpartArray[$key] = $this->cObj->stdWrap( $topicContent, $lConf['tx_wecsermons_sermons.']['topics.'] );

					}
					break;

				case '###SERMON_RESOURCES###':

					$marker = '';
					$markerArray[$key] = '';

					$this->emptyResourceSubparts( $subpartArray, $rowTemplate );

	
					$previousRow = $this->internal['previousRow'];
					$this->internal['previousRow'] = $row;
					$previousTable = $this->internal['previousTable'];
					$this->internal['previousTable'] = 'tx_wecsermons_sermons';

					$this->internal['currentTable'] = 'tx_wecsermons_resources';

					//	Retrieve related resources to this sermon
					$resources = $this->getSermonResources( $row['uid'] );

					foreach( $resources as $resource ) {
						$this->internal['currentRow'] = $resource;
						$this->local_cObj->start( $this->internal['currentRow'] );

						//	Use the marker name from the resource_type record
						$marker = $this->getMarkerName( $this->internal['currentRow']['marker_name'] );

						//	If this resource is the default resource type, we use the subpart marker name from typoscript config
						if( !strcmp( '0', $this->internal['currentRow']['type'] ) ) {

							$marker = $this->getMarkerName( $this->conf['defaultMarker'] );

							//	Change the 'type' to 'default' to the typoscript setting is more user friendly.
							$this->internal['currentRow']['type'] = 'default';

						}

						//	Retrieve the template subpart used to render this resource
						$resourceTemplate = $this->cObj->getSubpart( $rowTemplate, $marker );
						$resourceMarkerArray = $this->getMarkerArray( 'tx_wecsermons_resources', $resourceTemplate );

						if( $resourceTemplate )

							//	Aggregate rendered row into subpart. This allows multiple resources of the same type to all be output,
							//	rather than the last one processed.
							$subpartArray[$marker] .= $this->pi_list_row( $lConf, $resourceMarkerArray, $resourceTemplate, $this->internal['currentRow'] );

					}

					$this->internal['previousRow'] = $previousRow;
					$this->internal['previousTable'] = $previousTable;

					$this->internal['currentTable'] = 'tx_wecsermons_sermons';
					$this->internal['currentRow'] = $row;


					break;

				case '###RESOURCE_ICON###':

					if( $row[$fieldName] ) {
						$this->local_cObj->start( $row, 'tx_wecsermons_resources' );
						$markerArray[$key] = $this->local_cObj->cObjGetSingle( $lConf['tx_wecsermons_resources.']['icon'], $lConf['tx_wecsermons_resources.']['icon.'] );
					}
					break;

				case '###RESOURCE_TITLE###':
					if( $row[$fieldName] ) {
						$this->local_cObj->start( $row, 'tx_wecsermons_resources' );
						$markerArray[$key] = $this->local_cObj->cObjGetSingle( $lConf['tx_wecsermons_resources.']['title'], $lConf['tx_wecsermons_resources.']['title.'] );
					}

					break;

				case '###RESOURCE_DESCRIPTION###':
					if( $row[$fieldName] ) {
						$this->local_cObj->start( $row, 'tx_wecsermons_resources' );
						$markerArray[$key] = $this->local_cObj->cObjGetSingle( $lConf['tx_wecsermons_resources.']['description'], $lConf['tx_wecsermons_resources.']['description.'] );
					}
					break;

				case '###RESOURCE_GRAPHIC###':
					if( $row[$fieldName] ) {
						$this->local_cObj->start( $row, 'tx_wecsermons_resources' );
						$markerArray[$key] = $this->local_cObj->cObjGetSingle( $lConf['tx_wecsermons_resources.']['graphic'], $lConf['tx_wecsermons_resources.']['graphic.'] );
					}
					break;

				case '###RESOURCE_ALTTITLE###':
					if( $row[$fieldName] ) {
						$this->local_cObj->start( $row, 'tx_wecsermons_resources' );
						$markerArray[$key] = $this->local_cObj->cObjGetSingle( $lConf['tx_wecsermons_resources.']['alttitle'], $lConf['tx_wecsermons_resources.']['alttitle.'] );
					}
					break;

				case '###RESOURCE_WEBADDRESS1###':
					if( $row[$fieldName] ) {
						$this->local_cObj->start( $row, 'tx_wecsermons_resources' );
						$markerArray[$key] = $this->local_cObj->cObjGetSingle( $lConf['tx_wecsermons_resources.']['webaddress1'], $lConf['tx_wecsermons_resources.']['webaddress1.'] );
					}
					break;

				case '###RESOURCE_WEBADDRESS2###':
					if( $row[$fieldName] ) {
						$this->local_cObj->start( $row, 'tx_wecsermons_resources' );
						$markerArray[$key] = $this->local_cObj->cObjGetSingle( $lConf['tx_wecsermons_resources.']['webaddress2'], $lConf['tx_wecsermons_resources.']['webaddress2.'] );
					}
					break;

				case '###RESOURCE_WEBADDRESS3###':
					if( $row[$fieldName] ) {
						$this->local_cObj->start( $row, 'tx_wecsermons_resources' );
						$markerArray[$key] = $this->local_cObj->cObjGetSingle( $lConf['tx_wecsermons_resources.']['webaddress3'], $lConf['tx_wecsermons_resources.']['webaddress3.'] );
					}
					break;

				case '###RESOURCE_FILE###':
					if( $row[$fieldName] ) {
						$this->local_cObj->start( $row, 'tx_wecsermons_resources' );
						$markerArray[$key] = $this->local_cObj->cObjGetSingle( $lConf['tx_wecsermons_resources.']['file'], $lConf['tx_wecsermons_resources.']['file.'] );
					}
					break;

				case '###RESOURCE_CONTENT###':
					$this->local_cObj->start( $row, 'tx_wecsermons_resources' );

					//	If resource type's type = '1' and querystring parameter is not null, inject the parameter into the querystring. Type = 1 means we're processing an extension/plugin
					if( $this->internal['currentRow']['type_type']  == '1' && $this->internal['currentRow']['querystring_param']) {

						//	Parse the table_uid string from record into the value for the querystring_param
						list(,$queryStringVal) = array_values( $this->splitTableAndUID($this->internal['currentRow']['rendered_record'] ) );

						//	Break apart our querystring_param from it's stored form of 'plugin[param]'
						$queryString = split( "\[|\]", $this->internal['currentRow']['querystring_param'] );

						//	Push the custom string onto the querystring.
						t3lib_div::_GETset( t3lib_div::array_merge( $_GET, array( $queryString[0] => array( $queryString[1] => $queryStringVal) ) ) );

					}

					$markerArray[$key] = $this->local_cObj->cObjGetSingle( $this->conf['resource_types'], $this->conf['resource_types.'] );

					break;

				case '###RESOURCE_LINK###':

					if( ! $row['islinked'] )
						$wrappedSubpartArray[$key] = array( 0 => '', 1 => '');
					else {

						$this->local_cObj->start( $row, 'tx_wecsermons_resources' );

						//	Make sure 'type' field is in readable format if set to default, for use in TypoScript
						if( !strcmp( '0', $row['type'] ) )
							$row['type'] = 'default';

						if( !strcmp( 'default', $row['type'] ) && $lConf['tx_wecsermons_resources.']['resource_types.'][$row['type'].'.']['typolink'] ) {
							$wrappedSubpartArray[$key] = $this->local_cObj->typolinkWrap( $lConf['tx_wecsermons_resources.']['resource_types.'][$row['type'].'.']['typolink.'] );
						}
						elseif( $lConf['tx_wecsermons_resources.']['resource_types.'][$row['typoscript_object_name'].'.']['typolink'] ) {
							//	If 'typolink' segment is defined, render a link as defined by 'typolink', otherwise render a link to the resources' single view
							$wrappedSubpartArray[$key] = $this->local_cObj->typolinkWrap( $lConf['tx_wecsermons_resources.']['resource_types.'][$row['typoscript_object_name'].'.']['typolink.'] );

						}
						else {	//	Render a link to single view

							//	If this resource is a plugin/extension,
							//	and a record to render is specified,
							//	and the rendering page is specified, then render the link to the single view of that record
							if( $row['type_type'] > 0 && $row['rendered_record'] && $row['rendering_page'] ) {

								//	Parse the table_uid string from record into the value for the querystring_param
								list(,$queryStringVal) = array_values( $this->splitTableAndUID($row['rendered_record'] ) );

								//	Break apart our querystring_param from it's stored form of 'plugin[param]'
								$queryString = split( "\[|\]", $this->internal['currentRow']['querystring_param'] );

								$wrappedSubpartArray[$key] = explode(
									'|',
									$this->pi_linkToPage(
										'|',
										$row['rendering_page'],
										'',
										array( $queryString[0] => array( $queryString[1] => $queryStringVal) )
									 )
								);

							}
							else {
								$wrappedSubpartArray[$key] = explode(
									'|',
									$this->pi_list_linkSingle(
										'|',
										$row['uid'],
										$this->conf['allowCaching'],
										array(
										'recordType' => 'tx_wecsermons_resources',
										'sermonUid' => $this->internal['previousRow']['uid'],
									),
									FALSE,
									$this->conf['pidSingleView'] ? $this->conf['pidSingleView']:0
									)
								);

							}
						}
					}

					break;

				case '###SERIES_TITLE###':
					if( $row[$fieldName] ) {
						$this->local_cObj->start( $row, 'tx_wecsermons_series' );
						$markerArray[$key] = $this->local_cObj->cObjGetSingle( $lConf['tx_wecsermons_series.']['title'], $lConf['tx_wecsermons_series.']['title.'] );
					}
					break;

				case '###SERIES_SUBTITLE###':
					if( $row[$fieldName] ) {
						$this->local_cObj->start( $row, 'tx_wecsermons_series' );
						$markerArray[$key] = $this->local_cObj->cObjGetSingle( $lConf['tx_wecsermons_series.']['subtitle'], $lConf['tx_wecsermons_series.']['subtitle.'] );
					}
					break;

				case '###SERIES_DESCRIPTION###':
					if( $row[$fieldName] ) {
						$this->local_cObj->start( $row, 'tx_wecsermons_series' );
						$markerArray[$key] = $this->local_cObj->cObjGetSingle( $lConf['tx_wecsermons_series.']['description'], $lConf['tx_wecsermons_series.']['description.'] );
					}
					break;

				case '###SERIES_GRAPHIC###':
					if( $row[$fieldName] ) {
						$this->local_cObj->start( $row, 'tx_wecsermons_series' );
						$markerArray[$key] = $this->local_cObj->cObjGetSingle( $lConf['tx_wecsermons_series.']['graphic'], $lConf['tx_wecsermons_series.']['graphic.']);
					}
					break;

				case '###SERIES_ALTTITLE###':
					if( $row[$fieldName] ) {
						$this->local_cObj->start( $row, 'tx_wecsermons_series' );
						$markerArray[$key] = $this->local_cObj->cObjGetSingle( $lConf['tx_wecsermons_series.']['alttitle'], $lConf['tx_wecsermons_series.']['alttitle.']);
					}
					break;

				case '###SERIES_SCRIPTURE###':
					if( $row[$fieldName] ) {
						$this->local_cObj->start( $row, 'tx_wecsermons_series' );
						if ($this->conf['disableBibleGateway'] ) {
							$markerArray[$key] = $this->local_cObj->cObjGetSingle( $lConf['tx_wecsermons_series.']['scripture_nolink'], $lConf['tx_wecsermons_series.']['scripture_nolink.'] );
						} else {
							$markerArray[$key] = $this->local_cObj->cObjGetSingle( $lConf['tx_wecsermons_series.']['scripture'], $lConf['tx_wecsermons_series.']['scripture.'] );
						}
					}
					break;

				case '###SERIES_STARTDATE###':
					if( $row[$fieldName] ) {
						$fieldConf = $lConf['tx_wecsermons_series.']['startdate.'];
						//	Wrap the date, choosing from one of three settings in typoscript
						$dateWrap = $fieldConf['strftime'] ? $fieldConf['strftime'] : $lConf['general_dateWrap.']['strftime'];
						if( ! $dateWrap ) $dateWrap = $this->conf['general_dateWrap.']['strftime'];
						$fieldConf['strftime'] = $dateWrap;

						$this->local_cObj->start( $row, 'tx_wecsermons_series' );
						$markerArray[$key] = $this->local_cObj->cObjGetSingle( $lConf['tx_wecsermons_series.']['startdate'], $fieldConf );
					}
					break;

				case '###SERIES_ENDDATE###':
					if( $row[$fieldName] ) {

						$fieldConf = $lConf['tx_wecsermons_series.']['enddate.'];
						//	Wrap the date, choosing from one of three settings in typoscript
						$dateWrap = $fieldConf['strftime'] ? $fieldConf['strftime'] : $lConf['general_dateWrap.']['strftime'];
						if( ! $dateWrap ) $dateWrap = $this->conf['general_dateWrap.']['strftime'];
						$fieldConf['strftime'] = $dateWrap;


						$this->local_cObj->start( $row, 'tx_wecsermons_series' );
						$markerArray[$key] = $this->local_cObj->cObjGetSingle( $lConf['tx_wecsermons_series.']['enddate'], $fieldConf );
					}
					break;

				case '###SERIES_SEASON###':

					//	Check for related season and insert season subpart
					$subpartArray[$key] = '';

					if( $row[$fieldName] ) {
						//	Store previous row and table in local storage as we switch to retreiving detail
						$previousRow = $this->internal['previousRow'];
						$this->internal['previousRow'] = $row;
						$previousTable = $this->internal['previousTable'];
						$this->internal['previousTable'] = 'tx_wecsermons_series';
						$this->internal['currentTable'] = 'tx_wecsermons_seasons';

						//	Load the season subpart
						$seasonTemplate = $this->cObj->getSubpart( $rowTemplate, $key );
						$seasonMarkerArray = $this->getMarkerArray('tx_wecsermons_seasons',$seasonTemplate);
						$seasonContent = '';

						// our query
						$where = ' AND tx_wecsermons_series_seasons_rel.seriesid = ' . $row[uid] . ' ';
						$where .= $this->cObj->enableFields('tx_wecsermons_seasons');

						$query = 'select distinct
						tx_wecsermons_seasons.uid,
						tx_wecsermons_seasons.pid,
						tx_wecsermons_seasons.tstamp,
						tx_wecsermons_seasons.crdate,
						tx_wecsermons_seasons.cruser_id,
						tx_wecsermons_seasons.sys_language_uid,
						tx_wecsermons_seasons.deleted,
						tx_wecsermons_seasons.hidden,
						tx_wecsermons_seasons.description,
						tx_wecsermons_seasons.title,
						tx_wecsermons_seasons.islinked

						from tx_wecsermons_seasons
							inner join tx_wecsermons_series_seasons_rel
								on tx_wecsermons_seasons.uid = tx_wecsermons_series_seasons_rel.seasonid
						where 1=1 ' . $where;

						$seasonRes = $GLOBALS['TYPO3_DB']->sql_query ($query);

						$count = 0;
						while( $this->internal['currentRow'] = $GLOBALS['TYPO3_DB']->sql_fetch_assoc( $seasonRes ) ) {

							//	Recursive call to $this->pi_list_row() to populate each speaker marker
							$seasonContent .= $this->pi_list_row( $lConf, $seasonMarkerArray, $seasonTemplate, $this->internal['currentRow'], $count );
							$count++;
						}

						//	Restore row and table from local storage
						$this->internal['previousRow'] = $previousRow;
						$this->internal['previousTable'] = $previousTable;
						$this->internal['currentTable'] = 'tx_wecsermons_series';
						$this->internal['currentRow'] = $row;

						//	Replace marker content with subpart
						if( $count > 0 )
							$subpartArray[$key] = $this->cObj->stdWrap( $seasonContent, $lConf['tx_wecsermons_series.']['season.'] );
					}


					break;

				case '###SERIES_TOPICS###':

					$subpartArray[$key] = '';

					if( $row[$fieldName] ) {
						//	Store previous row and table in local storage as we switch to retreiving detail
						$previousRow = $this->internal['previousRow'];
						$this->internal['previousRow'] = $row;
						$previousTable = $this->internal['previousTable'];
						$this->internal['previousTable'] = 'tx_wecsermons_series';
						$this->internal['currentTable'] = 'tx_wecsermons_topics';

						//	Get the series_topics subpart
						$topicTemplate = $this->cObj->getSubpart( $rowTemplate, $key );
						$topicMarkerArray = $this->getMarkerArray('tx_wecsermons_topics',$topicTemplate);
						$topicContent = '';

						// our query
						$where = ' AND tx_wecsermons_series_topics_rel.sermonid = ' . $row[uid] . ' ';
						$where .= $this->cObj->enableFields('tx_wecsermons_topics');

						$query = 'select distinct
						tx_wecsermons_topics.uid,
						tx_wecsermons_topics.pid,
						tx_wecsermons_topics.tstamp,
						tx_wecsermons_topics.crdate,
						tx_wecsermons_topics.cruser_id,
						tx_wecsermons_topics.sys_language_uid,
						tx_wecsermons_topics.deleted,
						tx_wecsermons_topics.hidden,
						tx_wecsermons_topics.description,
						tx_wecsermons_topics.title,
						tx_wecsermons_topics.islinked

						from tx_wecsermons_topics
							inner join tx_wecsermons_series_topics_rel
								on tx_wecsermons_topics.uid = tx_wecsermons_series_topics_rel.topicid
						where 1=1 ' . $where;

						$topicRes = $GLOBALS['TYPO3_DB']->sql_query ($query);

						$count = 0;
						while( $this->internal['currentRow'] = $GLOBALS['TYPO3_DB']->sql_fetch_assoc( $topicRes ) ) {
							$this->local_cObj->start( $this->internal['currentRow'] );

							//	Recursive call to $this->pi_list_row() to populate each speaker marker
							$topicContent .= $this->pi_list_row( $lConf, $topicMarkerArray, $topicTemplate, $this->internal['currentRow'], $count );
							$count++;
						}

						//	Restore row and table from local storage
						$this->internal['previousRow'] = $previousRow;
						$this->internal['previousTable'] = $previousTable;
						$this->internal['currentTable'] = 'tx_wecsermons_series';
						$this->internal['currentRow'] = $row;

						//	Replace marker content with subpart
						if( $count > 0 )
							$subpartArray[$key] = $this->cObj->stdWrap( $topicContent, $lConf['tx_wecsermons_series.']['topics.'] );
					}

					break;

				case '###SERIES_SERMONS###':

					$subpartArray[$key] = '';

					//	Find all sermons related to this series
					$WHERE = $this->cObj->enableFields('tx_wecsermons_sermons');
					$query = 'SELECT tx_wecsermons_sermons.*
						   FROM tx_wecsermons_sermons
						    INNER JOIN tx_wecsermons_sermons_series_rel
						     ON tx_wecsermons_sermons.uid = tx_wecsermons_sermons_series_rel.sermonid
						   WHERE tx_wecsermons_sermons_series_rel.seriesid = ' . $row['uid'] . ' ' . $WHERE;
					$orderBy = '';

					//	Store previous row and table in local storage as we switch to retreiving detail
					$previousRow = $this->internal['previousRow'];
					$this->internal['previousRow'] = $row;
					$previousTable = $this->internal['previousTable'];
					$this->internal['previousTable'] = 'tx_wecsermons_series';
					$this->internal['currentTable'] = 'tx_wecsermons_sermons';

					//	Build order by clause for query
					if( $this->conf['listView.']['tx_wecsermons_sermons.']['orderBy'] ) {
						$fieldArray  = explode( ',',$this->conf['listView.']['tx_wecsermons_sermons.']['orderBy'] );
						$orderString = '';
						foreach( $fieldArray as $field ) {
							$orderString .= 'tx_wecsermons_sermons.'.$field.",";
						}
						$orderString = rtrim( $orderString, "," );
						$orderBy = ' ORDER BY '.$orderString.( $this->conf['listView.']['tx_wecsermons_sermons.']['descFlag'] ? ' DESC' : '' );
					}

					$query .= $orderBy;
					$sermonContent = '';
					$sermonRes = $GLOBALS['TYPO3_DB']->sql_query( $query );

					$sermonSeriesTemplate = $this->cObj->getSubpart( $rowTemplate, $key );
					$sermonMarkers = $this->getMarkerArray( 'tx_wecsermons_sermons', $sermonSeriesTemplate);

					//	Iterate every sermon record, aggregate content
					while( $this->internal['currentRow'] = $GLOBALS['TYPO3_DB']->sql_fetch_assoc( $sermonRes ) ) {
						$sermonContent .= $this->pi_list_row($this->conf['listView.'], $sermonMarkers, $sermonSeriesTemplate, $this->internal['currentRow'] );
					}

					//	Restore row and table from local storage
					$this->internal['previousRow'] = $previousRow;
					$this->internal['previousTable'] = $previousTable;
					$this->internal['currentTable'] = 'tx_wecsermons_series';
					$this->internal['currentRow'] = $row;

					//	Set subpart array with subpart content
					$subpartArray[$key] = $sermonContent;

					break;

				case '###SERIES_RESOURCES###':


					$markerArray[$key] = '';

					$this->emptyResourceSubparts( $subpartArray, $rowTemplate );

					$previousRow = $this->internal['previousRow'];
					$this->internal['previousRow'] = $row;
					$previousTable = $this->internal['previousTable'];
					$this->internal['previousTable'] = 'tx_wecsermons_sermons';

					$this->internal['currentTable'] = 'tx_wecsermons_resources';

					//	Retrieve related resources to this sermon
					$resources = $this->getSeriesResources( $row['uid'] );

					foreach( $resources as $resource ) {
						$this->internal['currentRow'] = $resource;
						$this->local_cObj->start( $this->internal['currentRow'] );

						//	Use the marker name from the resource_type record
						$marker = $this->getMarkerName( $this->internal['currentRow']['marker_name'] );

						//	If this resource is the default resource type, we use the subpart marker name from typoscript config
						if( !strcmp( '0', $this->internal['currentRow']['type'] ) ) {

							$marker = $this->getMarkerName( $this->conf['defaultMarker'] );

							//	Change the 'type' to 'default' to the friendlier typoscript property name.
							$this->internal['currentRow']['type'] = 'default';

						}

						//	Retrieve the template subpart used to render this resource
						$resourceTemplate = $this->cObj->getSubpart( $rowTemplate, $marker );
						$resourceMarkerArray = $this->getMarkerArray( 'tx_wecsermons_resources', $resourceTemplate );

						if( $resourceTemplate )
							//	Aggregate rendered row into subpart. This allows multiple resources of the same type to all be output,
							//	rather than the last one processed.
							$subpartArray[$marker] .= $this->pi_list_row( $lConf, $resourceMarkerArray, $resourceTemplate, $this->internal['currentRow'] );

					}

					$this->internal['previousRow'] = $previousRow;
					$this->internal['previousTable'] = $previousTable;

					$this->internal['currentTable'] = 'tx_wecsermons_series';
					$this->internal['currentRow'] = $row;

					break;

				case '###SERIES_LINK###':

					// If islinked is false, simply return an empty array
					if( ! $row['islinked'] )
						$wrappedSubpartArray[$key] = array( 0 => '', 1 => '');
					else
						$wrappedSubpartArray[$key] = explode(
							'|',
							$this->pi_list_linkSingle(
								'|',
								$row['uid'],
								$this->conf['allowCaching'],
								array(
									'recordType' => 'tx_wecsermons_series',
								),
								FALSE,
								$this->conf['pidSingleView'] ? $this->conf['pidSingleView']:0
							)
						);

					break;

				case '###TOPIC_TITLE###':
					if( $row[$fieldName] ) {
						$this->local_cObj->start( $row, 'tx_wecsermons_topics' );
						$markerArray[$key] = $this->local_cObj->cObjGetSingle( $lConf['tx_wecsermons_topics.']['title'], $lConf['tx_wecsermons_topics.']['title.'] );
					}

					break;

				case '###TOPIC_DESCRIPTION###':
					if( $row[$fieldName] ) {
						$this->local_cObj->start( $row, 'tx_wecsermons_topics' );
						$markerArray[$key] = $this->local_cObj->cObjGetSingle( $lConf['tx_wecsermons_topics.']['description'], $lConf['tx_wecsermons_topics.']['description.'] );
					}

					break;

				case '###TOPIC_LINK###':

					// If islinked is false, simply return an empty array
					if( ! $row['islinked'] )
						$wrappedSubpartArray[$key] = array( 0 => '', 1 => '');
					else
						$wrappedSubpartArray[$key] = explode(
							'|',
							$this->pi_list_linkSingle(
								'|',
								$row['uid'],
								$this->conf['allowCaching'],
								array(
									'recordType' => 'tx_wecsermons_topics',
								),
								FALSE,
								$this->conf['pidSingleView'] ? $this->conf['pidSingleView']:0
							)
						);


					break;

				case '###TOPIC_SERMONS###':

					$subpartArray[$key] = '';

					//	Find all sermons related to this topic
					$WHERE = $this->cObj->enableFields('tx_wecsermons_sermons');
					$query = 'SELECT tx_wecsermons_sermons.*
					           FROM tx_wecsermons_sermons
					            INNER JOIN tx_wecsermons_sermons_topics_rel
					             ON tx_wecsermons_sermons.uid = tx_wecsermons_sermons_topics_rel.sermonid
					           WHERE tx_wecsermons_sermons_topics_rel.topicid = ' . $row['uid'] . ' ' . $WHERE;
					$orderby = '';

					//	Store previous row and table in local storage as we switch to retrieving detail
					$previousRow = $this->internal['previousRow'];
					$this->internal['previousRow'] = $row;
					$previousTable = $this->internal['previousTable'];
					$this->internal['previousTable'] = 'tx_wecsermons_topics';
					$this->internal['currentTable'] = 'tx_wecsermons_sermons';

					//	Build order by clause for query
					if( $this->conf['listView.']['tx_wecsermons_sermons.']['orderBy'] ) {
						$fieldArray  = explode( ',',$this->conf['listView.']['tx_wecsermons_sermons.']['orderBy'] );
						$orderString = '';
						foreach( $fieldArray as $field ) {
							$orderString .= 'tx_wecsermons_sermons.'.$field.",";
						}
						$orderString = rtrim( $orderString, "," );
						$orderBy = ' ORDER BY '.$orderString.( $this->conf['listView.']['tx_wecsermons_sermons.']['descFlag'] ? ' DESC' : '' );
					}

					$query .= $orderBy;
					$sermonContent = '';
					$sermonRes = $GLOBALS['TYPO3_DB']->sql_query( $query );

					$topicTemplate = $this->cObj->getSubpart( $rowTemplate, $key );
					$sermonMarkers = $this->getMarkerArray( 'tx_wecsermons_sermons', $topicTemplate );

					//	Iterate every sermon record, aggregate content
					while( $this->internal['currentRow'] = $GLOBALS['TYPO3_DB']->sql_fetch_assoc( $sermonRes ) ) {
						$sermonContent .= $this->pi_list_row($this->conf['listView.'], $sermonMarkers, $topicTemplate, $this->internal['currentRow'] );
					}

					//	Restore row and table from local storage
					$this->internal['previousRow'] = $previousRow;
					$this->internal['previousTable'] = $previousTable;
					$this->internal['currentTable'] = 'tx_wecsermons_topics';
					$this->internal['currentRow'] = $row;

					//      Set subpart array with subpart content
					$subpartArray[$key] = $sermonContent;

					break;

				case '###TOPIC_SERIES###':

					$subpartArray[$key] = '';

					//      Find all series related to this topic
					$WHERE = $this->cObj->enableFields('tx_wecsermons_series');
					$query = 'SELECT tx_wecsermons_series.*
					           FROM tx_wecsermons_series
					            INNER JOIN tx_wecsermons_series_topics_rel
					             ON tx_wecsermons_series.uid = tx_wecsermons_series_topics_rel.seriesid
					           WHERE tx_wecsermons_series_topics_rel.topicid = ' . $row['uid'] . ' ' . $WHERE;
					$orderby = '';

					//      Store previous row and table in local storage as we switch to retrieving detail
					$previousRow = $this->internal['previousRow'];
					$this->internal['previousRow'] = $row;
					$previousTable = $this->internal['previousTable'];
					$this->internal['previousTable'] = 'tx_wecsermons_topics';
					$this->internal['currentTable'] = 'tx_wecsermons_series';

					//      Build order by clause for query
					if( $this->conf['listView.']['tx_wecsermons_series.']['orderBy'] ) {
						$fieldArray  = explode( ',',$this->conf['listView.']['tx_wecsermons_series.']['orderBy'] );
						$orderString = '';
						foreach( $fieldArray as $field ) {
							$orderString .= 'tx_wecsermons_series.'.$field.",";
						}
						$orderString = rtrim( $orderString, "," );
						$orderBy = ' ORDER BY '.$orderString.( $this->conf['listView.']['tx_wecsermons_series.']['descFlag'] ? ' DESC' : '' );
					}

					$query .= $orderBy;
					$seriesContent = '';
					$seriesRes = $GLOBALS['TYPO3_DB']->sql_query( $query );

					$topicTemplate = $this->cObj->getSubpart( $rowTemplate, $key );
					$seriesMarkers = $this->getMarkerArray( 'tx_wecsermons_series', $topicTemplate );

					//      Iterate every series record, aggregate content
					while( $this->internal['currentRow'] = $GLOBALS['TYPO3_DB']->sql_fetch_assoc( $seriesRes ) ) {
						$seriesContent .= $this->pi_list_row($this->conf['listView.'], $seriesMarkers, $topicTemplate, $this->internal['currentRow'] );
					}

					//      Restore row and table from local storage
					$this->internal['previousRow'] = $previousRow;
					$this->internal['previousTable'] = $previousTable;
					$this->internal['currentTable'] = 'tx_wecsermons_topics';
					$this->internal['currentRow'] = $row;

					//      Set subpart array with subpart content
					$subpartArray[$key] = $seriesContent;

					break;

				case '###SEASON_TITLE###':
					if( $row[$fieldName] ) {
						$this->local_cObj->start( $row, 'tx_wecsermons_seasons' );
						$markerArray[$key] = $this->local_cObj->cObjGetSingle( $lConf['tx_wecsermons_seasons.']['title'], $lConf['tx_wecsermons_seasons.']['title.'] );

					}

					break;

				case '###SEASON_DESCRIPTION###':
					if( $row[$fieldName] ) {
						$this->local_cObj->start( $row, 'tx_wecsermons_seasons' );
						$markerArray[$key] = $this->local_cObj->cObjGetSingle( $lConf['tx_wecsermons_seasons.']['description'], $lConf['tx_wecsermons_seasons.']['description.'] );

					}

					break;

				case '###SEASON_LINK###':

					// If islinked is false, simply return an empty array
					if( ! $row['islinked'] )
						$wrappedSubpartArray[$key] = array( 0 => '', 1 => '');
					else
						$wrappedSubpartArray[$key] = explode(
							'|',
							$this->pi_list_linkSingle(
								'|',
								$row['uid'],
								$this->conf['allowCaching'],
								array(
									'recordType' => 'tx_wecsermons_seasons',
								),
								FALSE,
								$this->conf['pidSingleView'] ? $this->conf['pidSingleView']:0
							)
						);



					break;

				case '###SEASON_SERIES###':

					$subpartArray[$key] = '';

					//      Find all series related to this season
					$WHERE = $this->cObj->enableFields('tx_wecsermons_series');
					$query = 'SELECT tx_wecsermons_series.*
					           FROM tx_wecsermons_series
					            INNER JOIN tx_wecsermons_series_seasons_rel
					             ON tx_wecsermons_series.uid = tx_wecsermons_series_seasons_rel.seriesid
					           WHERE tx_wecsermons_series_seasons_rel.seasonid = ' . $row['uid'] . ' ' . $WHERE;
					$orderby = '';

					//      Store previous row and table in local storage as we switch to retrieving detail
					$previousRow = $this->internal['previousRow'];
					$this->internal['previousRow'] = $row;
					$previousTable = $this->internal['previousTable'];
					$this->internal['previousTable'] = 'tx_wecsermons_seasons';
					$this->internal['currentTable'] = 'tx_wecsermons_series';

					//      Build order by clause for query
					if( $this->conf['listView.']['tx_wecsermons_series.']['orderBy'] ) {
						$fieldArray  = explode( ',',$this->conf['listView.']['tx_wecsermons_series.']['orderBy'] );
						$orderString = '';
						foreach( $fieldArray as $field ) {
							$orderString .= 'tx_wecsermons_series.'.$field.",";
						}
						$orderString = rtrim( $orderString, "," );
						$orderBy = ' ORDER BY '.$orderString.( $this->conf['listView.']['tx_wecsermons_series.']['descFlag'] ? ' DESC' : '' );
					}

					$query .= $orderBy;
					$seriesContent = '';
					$seriesRes = $GLOBALS['TYPO3_DB']->sql_query( $query );

					$seasonTemplate = $this->cObj->getSubpart( $rowTemplate, $key );
					$seriesMarkers = $this->getMarkerArray( 'tx_wecsermons_series', $seasonTemplate );

					//      Iterate every series record, aggregate content
					while( $this->internal['currentRow'] = $GLOBALS['TYPO3_DB']->sql_fetch_assoc( $seriesRes ) ) {
						$seriesContent .= $this->pi_list_row($this->conf['listView.'], $seriesMarkers, $seasonTemplate, $this->internal['currentRow'] );
					}

					//      Restore row and table from local storage
					$this->internal['previousRow'] = $previousRow;
					$this->internal['previousTable'] = $previousTable;
					$this->internal['currentTable'] = 'tx_wecsermons_seasons';
					$this->internal['currentRow'] = $row;

					//      Set subpart array with subpart content
					$subpartArray[$key] = $seriesContent;

					break;

				case '###SPEAKER_FULLNAME###':
					if( $row[$fieldName] ) {
						$this->local_cObj->start( $row, 'tx_wecsermons_speakers' );
						$markerArray[$key] = $this->local_cObj->cObjGetSingle( $lConf['tx_wecsermons_speakers.']['fullname'], $lConf['tx_wecsermons_speakers.']['fullname.'] );
					}

					break;

				case '###SPEAKER_FIRSTNAME###':
					if( $row[$fieldName] ) {
						$this->local_cObj->start( $row, 'tx_wecsermons_speakers' );
						$markerArray[$key] = $this->local_cObj->cObjGetSingle( $lConf['tx_wecsermons_speakers.']['firstname'], $lConf['tx_wecsermons_speakers.']['firstname.'] );
					}

					break;

				case '###SPEAKER_LASTNAME###':
					if( $row[$fieldName] ) {
						$this->local_cObj->start( $row, 'tx_wecsermons_speakers' );
						$markerArray[$key] = $this->local_cObj->cObjGetSingle( $lConf['tx_wecsermons_speakers.']['lastname'], $lConf['tx_wecsermons_speakers.']['lastname.'] );
					}

					break;

				case '###SPEAKER_URL###':
					if( $row[$fieldName] ) {
						$this->local_cObj->start( $row, 'tx_wecsermons_speakers' );
						$markerArray[$key] = $this->local_cObj->cObjGetSingle( $lConf['tx_wecsermons_speakers.']['url'], $lConf['tx_wecsermons_speakers.']['url.'] );
					}

					break;

				case '###SPEAKER_BLOGURL###':
					if( $row[$fieldName] ) {
						$this->local_cObj->start( $row, 'tx_wecsermons_speakers' );
						$markerArray[$key] = $this->local_cObj->cObjGetSingle( $lConf['tx_wecsermons_speakers.']['blogurl'], $lConf['tx_wecsermons_speakers.']['url.'] );
					}

					break;

				case '###SPEAKER_EMAIL###':

					//	Create link, making sure it is spam protected
					if( $row[$fieldName] ) {
						$this->local_cObj->start( $row, 'tx_wecsermons_speakers' );
						$markerArray[$key] = $this->local_cObj->cObjGetSingle( $lConf['tx_wecsermons_speakers.']['email'], $lConf['tx_wecsermons_speakers.']['email.'] );

					}
					break;

				case '###SPEAKER_PHOTO###':
					if( $row[$fieldName] ) {
						$this->local_cObj->start( $row, 'tx_wecsermons_speakers' );
						$markerArray[$key] = $this->local_cObj->cObjGetSingle( $lConf['tx_wecsermons_speakers.']['photo'], $lConf['tx_wecsermons_speakers.']['photo.'] );
					}

					break;

				case '###SPEAKER_LINK###':

					// If islinked is false, simply return an empty array
					if( ! $row['islinked'] )
						$wrappedSubpartArray[$key] = array( 0 => '', 1 => '');

					else	{

						$this->local_cObj->start( $row, 'tx_wecsermons_speakers' );

						//	If 'typolink' is set, generate a link as defined by the 'typolink' segment, otherwise attempt to link to the speakers single view
						if( $lConf['tx_wecsermons_speakers.']['typolink'] ) {

							//	Generate a link as defined by the 'typolink' segment
							$wrappedSubpartArray[$key] = $this->local_cObj->typolinkWrap( $lConf['tx_wecsermons_speakers.']['typolink.'] );
						}
						else{ // If the islinked field is set, then generate a link to the Speaker Single view
							$wrappedSubpartArray[$key] = explode(
								'|',
								$this->pi_list_linkSingle(
									'|',
									$row['uid'],
									$this->conf['allowCaching'],
									array(
										'recordType' => 'tx_wecsermons_speakers',
									),
									FALSE,
									$this->conf['pidSingleView'] ? $this->conf['pidSingleView']:0
									)
							);
						}
					}

					break;

				case '###SPEAKER_SERMONS###':

					$subpartArray[$key] = '';

					//      Find all sermons related to this speaker
					$WHERE = $this->cObj->enableFields('tx_wecsermons_sermons');
					$query = 'SELECT tx_wecsermons_sermons.*
					           FROM tx_wecsermons_sermons
					            INNER JOIN tx_wecsermons_sermons_speakers_rel
					             ON tx_wecsermons_sermons.uid = tx_wecsermons_sermons_speakers_rel.sermonid
					           WHERE tx_wecsermons_sermons_speakers_rel.speakerid = ' . $row['uid'] . ' ' . $WHERE;
					$orderby = '';

					//      Store previous row and table in local storage as we switch to retrieving detail
					$previousRow = $this->internal['previousRow'];
					$this->internal['previousRow'] = $row;
					$previousTable = $this->internal['previousTable'];
					$this->internal['previousTable'] = 'tx_wecsermons_speakers';
					$this->internal['currentTable'] = 'tx_wecsermons_sermons';

					//      Build order by clause for query
					if( $this->conf['listView.']['tx_wecsermons_sermons.']['orderBy'] ) {
						$fieldArray  = explode( ',',$this->conf['listView.']['tx_wecsermons_sermons.']['orderBy'] );
						$orderString = '';
						foreach( $fieldArray as $field ) {
							$orderString .= 'tx_wecsermons_sermons.'.$field.",";
						}
						$orderString = rtrim( $orderString, "," );
						$orderBy = ' ORDER BY '.$orderString.( $this->conf['listView.']['tx_wecsermons_sermons.']['descFlag'] ? ' DESC' : '' );
					}

					$query .= $orderBy;
					$sermonContent = '';
					$sermonRes = $GLOBALS['TYPO3_DB']->sql_query( $query );

					$speakerTemplate = $this->cObj->getSubpart( $rowTemplate, $key );
					$sermonMarkers = $this->getMarkerArray( 'tx_wecsermons_sermons', $speakerTemplate );

					//      Iterate every sermon record, aggregate content
					while( $this->internal['currentRow'] = $GLOBALS['TYPO3_DB']->sql_fetch_assoc( $sermonRes ) ) {
						$sermonContent .= $this->pi_list_row($this->conf['listView.'], $sermonMarkers, $speakerTemplate, $this->internal['currentRow
'] );
					}

					//      Restore row and table from local storage
					$this->internal['previousRow'] = $previousRow;
					$this->internal['previousTable'] = $previousTable;
					$this->internal['currentTable'] = 'tx_wecsermons_speakers';
					$this->internal['currentRow'] = $row;

					//      Set subpart array with subpart content
					$subpartArray[$key] = $sermonContent;

					break;

				case '###ALTERNATING_CLASS###':
					$markerArray['###ALTERNATING_CLASS###'] = $c % 2 ? $this->pi_getClassName( 'list' ) . ' ' . $lConf['alternatingClass'] : $this->pi_getClassName('list');
					break;

				case '###BROWSE_LINKS###':

					// Only show the browsebox when we have more than one page to display
					if( $this->internal['res_count'] > $this->internal['results_at_a_time'] )
						$markerArray['###BROWSE_LINKS###'] = is_array( $lConf['browseBox_linkWraps.'] ) ? $this->pi_list_browseresults($lConf['showResultCount'], '', $lConf['browseBox_linkWraps.'] ) : '';
					break;

				case '###BACK_LINK###':

					//	Retrieve the posted recordType from piVars. If unset, use tx_wecsermons_sermons as default. This is in case of hard linking to the single view improperly, instead of linking through the list view.
					if( ! isset( $this->piVars['recordType'] ) ) $this->piVars['recordType'] = $this->getConfigVal( $this, 'detail_table', 'slistView', 'detailTable', $this->conf, 'tx_wecsermons_sermons' );

					$wrappedSubpartArray[$key] = explode(
						'|',
						$this->pi_linkTP(
							'|',
							array(),
							$this->conf['allowCaching'],
							$this->conf['pidListView']
						)
					);
					break;

				case '###BACK_TO_LIST###':

					$markerArray[$key] =  $this->cObj->cObjGetSingle( $lConf['back'], $lConf['back.'] );

					break;

			}	// End Switch

			//	Hook  for processing extra markers
			if( is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tx_wecsermons_pi1']['postProcessMarkers'] ) ) {

				foreach( $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tx_wecsermons_pi1']['postProcessMarkers'] as $classRef ) {

					$processObject = &t3lib_div::getUserObj( $classRef, 'tx_' );

					$processObject->processMarker( $this, $lConf, $markerArray, $row, $c, $key );
				}

			}


		}	// End Foreach

		$lContent = $this->cObj->substituteMarkerArrayCached($rowTemplate, $markerArray, $subpartArray, $wrappedSubpartArray );

		//	Only add edit UI if there is a row of data we're processing
		if( $row ) {

			//	Get editIcon using $this->internal['currentRow'] and $this->internal['currentTable']
			$lContent = $this->pi_getEditIcon( $lContent, $this->getFeAdminList() );

			//	Get Editpanel using $this->internal['currentRow'] and $this->internal['currentTable']
			$lContent .= $this->pi_getEditPanel();

		}
		return $lContent;
	}

	/**
	* getMarkerArray: Returns the markerArray for a specific table. If template content is provided as well, then the template is scraped and unused markers are filtered out of the array
	*
	* 	Default returned array is for general page markers. Browse_links, back_link, etc.
	*
	* @param		string		$tableName: Table name to retrieve markers for
	* @param		string		$templateContent: A content stream which we will scan for markers
	* @return	array			Array filled with markers as keys, with empty values
	*/
	function getMarkerArray( $tableName = '', $templateContent = '') {

		$markerArray = array();
		$markers = array();
		$subpartMarkers = array();

		//	If we were passed a template, then scan the template for markers and return a truncated array of markers
		//	truncated marker array will only process those things we need to
		if( $templateContent ) {

			//	Find every marker in the template
			preg_match_all('!(\###[A-Z0-9_-|]*\###)!is', $templateContent, $markers);

			//	Truncate the array, removing duplicates
			$markerArray = array_unique($markers[1]);

			//	Flip the keys and values for compare against our internal arrays
			$markerArray = array_flip($markerArray);

			//	Retreive our internal arrary, which we use in later processing
			$SMSmarkers = $this->getMarkerArray($tableName);

			//	Pull out unused markers from internal array
			$markerArray = $this->array_intersect_key($SMSmarkers, $markerArray);

			//	If table is sermons table, add SERMON_RESOURCES marker back for processing
			if( !strcmp( $tableName,'tx_wecsermons_sermons') )
				$markerArray['###SERMON_RESOURCES###'] = 'resources';

			//	If table is series table, add SERIES_RESOURCES marker back for processing
			if( !strcmp( $tableName,'tx_wecsermons_series') )
				$markerArray['###SERIES_RESOURCES###'] = 'resources';

		}
		else {
			switch ( $tableName ) {
			case 'tx_wecsermons_sermons':
				$markerArray = array (
					'###SERMON_UID###' => 'uid',
					'###SERMON_TITLE###' => 'title',
					'###SERMON_SUBTITLE###' => 'subtitle',
					'###SERMON_OCCURRENCE_DATE###' => 'occurrence_date',
					'###SERMON_DESCRIPTION###' => 'description',
					'###SERMON_SCRIPTURE###' => 'scripture',
					'###SERMON_GRAPHIC###' => 'graphic',
					'###SERMON_ALTTITLE###' => 'alttitle',
					'###SERMON_LINK###' => '',
					'###ALTERNATING_CLASS###' => '',
					'###SERMON_TOPICS###' => 'topics',
					'###SERMON_SERIES###' => 'series',
					'###SERMON_SPEAKERS###' => 'speakers',
					'###SERMON_RESOURCES###' => 'resources', // Only included to kick off the processing of resources. Resource markers are defined in the resource_type records or resource record
				);

				break;

			case 'tx_wecsermons_series':
				$markerArray = array (
					'###SERIES_TITLE###' => 'title',
					'###SERIES_SUBTITLE###' => 'subtitle',
					'###SERIES_STARTDATE###' => 'startdate',
					'###SERIES_ENDDATE###' => 'enddate',
					'###SERIES_DESCRIPTION###' => 'description',
					'###SERIES_SCRIPTURE###' => 'scripture',
					'###SERIES_GRAPHIC###' => 'graphic',
					'###SERIES_ALTTITLE###' => 'alttitle',
					'###SERIES_SEASON###' => 'seasons',
					'###SERIES_TOPICS###' => 'topics',
					'###SERIES_SERMONS###' => '',
					'###SERIES_RESOURCES###' => 'resources',  // Only included to kick off the processing of resources. Resource markers are defined in the resource_type records or resource record
					'###SERIES_LINK###' => '',
					'###ALTERNATING_CLASS###' => '',

				);
				break;

			case 'tx_wecsermons_topics':
				$markerArray = array (
					'###TOPIC_TITLE###' => 'title',
					'###TOPIC_DESCRIPTION###' => 'description',
					'###TOPIC_SERMONS###' => '',
					'###TOPIC_SERIES###' => '',
					'###ALTERNATING_CLASS###' => '',
					'###TOPIC_LINK###' => '',
				);
				break;

			case 'tx_wecsermons_speakers':
				$markerArray = array (
					'###SPEAKER_FULLNAME###' => 'fullname',
					'###SPEAKER_FIRSTNAME###' => 'firstname',
					'###SPEAKER_LASTNAME###' => 'lastname',
					'###SPEAKER_EMAIL###' => 'email',
					'###SPEAKER_URL###' => 'url',
					'###SPEAKER_BLOGURL###' => 'blogurl',
					'###SPEAKER_PHOTO###' => 'photo',
					'###SPEAKER_SERMONS###' => '',
					'###SPEAKER_ALTTITLE###' => 'alttitle',
					'###SPEAKER_LINK###' => '',
					'###ALTERNATING_CLASS###' => '',
				);
				break;

			case 'tx_wecsermons_resources':
				$markerArray = array (
					'###RESOURCE_TITLE###' => 'title',
					'###RESOURCE_DESCRIPTION###' => 'description',
					'###RESOURCE_GRAPHIC###' => 'graphic',
					'###RESOURCE_ALTTITLE###' => 'alttitle',
					'###RESOURCE_FILE###' => 'file',
					'###RESOURCE_WEBADDRESS1###' => 'webaddress1',
					'###RESOURCE_WEBADDRESS2###' => 'webaddress2',
					'###RESOURCE_WEBADDRESS3###' => 'webaddress3',
					'###RESOURCE_CONTENT###' => '',
					'###ALTERNATING_CLASS###' => '',
					'###RESOURCE_LINK###' => '',
					'###RESOURCE_ICON###' => 'icon',
				);
				break;

			case 'tx_wecsermons_seasons':
				$markerArray = array (
					'###SEASON_TITLE###' => 'title',
					'###SEASON_DESCRIPTION###' => 'description',
					'###SEASON_SERIES###' => '',
					'###SEASON_LINK###' => '',
					'###ALTERNATING_CLASS###' => '',
				);
				break;

			case 'searchbox':
				$markerArray = array(
					'###FORM_ACTION###' => '',
					'###SEARCHBOX_OPTIONS###' => '',
					'###SEARCH_BUTTON_NAME###' => '',
				);
				break;

			default:
				$markerArray = array (
					'###BROWSE_LINKS###' => '',
					'###BACK_TO_LIST###' => '',
					'###BACK_LINK###' => '',
				);
				break;

		}
	}

	return $markerArray;
	}

	/**
	* formatStr: Format string with general_stdWrap from configuration
	*
	* @param	string		$str: String to wrap
	* @return	string		wrapped string
	*/
	function formatStr( $str ) {

	if ( is_array( $this->conf['general_stdWrap.'] ) )
		return $this->local_cObj->stdWrap($str, $this->conf['general_stdWrap.']);
	else
		return $str;
	}

	/**
	* getTemplateKey: Retrieves the content for a named template. Used to pull a template subpart from a template file
	*
	* @param	string		$tableName: This is the table name to retrieve the template key name for.
	* @return	string		A string value that is the template key name for an SMS table name.
	*/
	function getTemplateKey($tableName) {

		switch( $tableName ) {
			case 'tx_wecsermons_sermons':
				$key = 'Sermon';
			break;

			case 'tx_wecsermons_resources':
				$key = 'Resource';
			break;

			case 'tx_wecsermons_topics':
				$key = 'Topic';
			break;

			case 'tx_wecsermons_seasons':
				$key = 'Season';
			break;

			case 'tx_wecsermons_series':
				$key = 'Series';
			break;

			case 'tx_wecsermons_speakers':
				$key = 'Speaker';
			break;

		}

		//	TODO: Add hook for custom tables

		return $key;

	}

	/**
	* getUrlToList:	Returns the path to the current page with SMS querystring values intact. Will return absolute path if $absolute is true.
	*
	* @param	boolean		$absolute:	Boolean value indicating whether to return an absolute path.
	* @return	string		The absolute or relative path to the current page.
	*/
	function getUrlToList ( $absolute ) {

		return $absolute ? t3lib_div::getIndpEnv('TYPO3_SITE_URL') .
			$this->pi_linkTP_keepPIvars_url( array(), $this->conf['allowCaching'], 0 ) :
			$this->pi_linkTP_keepPIvars_url( array(), $this->conf['allowCaching'], 0 );

	}


	/**
	* getUrlToSingle:	Returns the path to the single view of a particular SMS record with SMS querystring values intact. Will return absolute path if $absolute is true.
	*
	* @param	boolean		$absolute:	Boolean value indicating whether to return an absolute path.
	* @param	string		$tableName: The table name to retrieve the record from. Should be the full table name, prepended with 'tx_wecsermons_'
	* @param	int				$uid: An integer value that is the UID of the record we wish to get the URL for.
	* @param	int				$sermonUid: The uid of the related sermon record, if we are generating a link to a record related to a sermon.
	* @return	string		Return value is the absolute or relative path to the requested SMS record.
	*/
	function getUrlToSingle ( $absolute, $tableName, $uid, $sermonUid = '' ) {


		$piVar = $sermonUid ?

			array (
				'recordType' => $tableName,
				'showUid' => $uid,
				'sermonUid' => $sermonUid,
			)

			: array (
			'recordType' => $tableName,
			'showUid' => $uid,
			);

		return $absolute ? t3lib_div::getIndpEnv('TYPO3_SITE_URL') .
			$this->pi_linkTP_keepPIvars_url( $piVar, $this->conf['allowCaching'], 0, $this->conf['pidSingleView'] ) :
			$this->pi_linkTP_keepPIvars_url( $piVar, $this->conf['allowCaching'], 0, $this->conf['pidSingleView'] );

	}

	/**
	* getFeAdminList: Retrieves the 'fe_admin_fieldList' for a given data table, used for generating the fe editIcon. If no table name is given, then the table name stored in $this->internal['currentTable'] is used.
	*
	* @param	string		$tableName: The name of the table to retrieve the field list for.
	* @return	string		Return value is a CSV string of fieldnames used in the editIcon fieldlist
	*/
	function getFeAdminList( $tableName = '' ) {

		if( ! $tableName ) $tableName = $this->internal['currentTable'];

		//	Load up the tca for given table
		$GLOBALS['TSFE']->includeTCA($TCAloaded = 1);
		t3lib_div::loadTCA( $tableName );

		return $GLOBALS['TCA'][$tableName]['feInterface']['fe_admin_fieldList'];

	}

	/**
	* getNamedTemplateContent: Retrieves the content for a named template. Used to load a template subpart from a template file. A member variable is used to store the template content, $this->template.
	*
	* @param	string		$keyName: This is the keyname of the type of template to retrieve such as SERMON, SERIES, TOPIC, etc.
	* @param	string		$view: This is the name of the view to retrieve, SINGLE, LIST, etc.
	* @return	string		Return value is the content of a specfic marker-based template
	* @see loadTemplate()
	*/
	function getNamedTemplateContent($keyName = 'sermon', $view = 'single') {

		// Make sure template is loaded into instance of our class
		$this->loadTemplate();

		$keyName = strtoupper( $keyName );
		$view = strtoupper( $view );

		switch( $view ) {

			case 'LIST':
			case 'LATEST':
				$templateContent = $this->cObj->getSubpart(
					$this->template['total'],
					sprintf( '###TEMPLATE_%s_%s###',
						$view,
						$this->internal['layoutCode']
					)
				);
			break;

			default:
				$templateContent = $this->cObj->getSubpart(
				$this->template['total'],
				sprintf( '###TEMPLATE_%s_%s###',
					$keyName,
					$this->internal['layoutCode']
				)
			);

		}

		return $templateContent;
	}

	/**
	 * getNamedSubpart: Retrieves a template subpart given the subparts name, and the content stream to read it from.
	 *
	 * @param	string		$subpartName: The name of the subpart
	 * @param	string		$content: The content stream where the subpart is stored
	 * @return	string	Returns a string value containing on the subpart requested.
	 */
	function getNamedSubpart( $subpartName, $content ) {
		// Make sure template is loaded into instance of our class
		$this->loadTemplate();

		//	Get corrected marker name, appending and prepending ### if necessary
		$subpartName = $this->getMarkerName( $subpartName );

		return $this->cObj->getSubpart( $content, $subpartName );

	}

	/**
	 * getMarkerName:	Fixes marker names with prepended and appended hash marks
	 *
	 * @param	string		$markerName: The marker string to be corrected if necessary
	 * @return	string		The fixed marker string, ready for use
	 */
	function getMarkerName( $markerName ) {

		//	Fix subpart name if TYPO tags were not inserted
		return $markerName = strrpos( $markerName, '###') ? strtoupper( $markerName ) :  '###'.strtoupper( $markerName ).'###';
	}

	/**
	 * loadTemplate: Reads in a template file and populates the $template array member variable with the total content, and various other subparts:
	 * 	list, content, item
	 *
	 * @param	string		$view	The name of the view we are loading, such as LATEST, or LIST
	 * @return	void
	 */
	function loadTemplate( $view = 'LIST') {

		if( ! $this->template ) {
			$this->template = array(
				'total' => '',
				'single' => '',
				'list' => '',
				'item' => '',
				'content' => ''
			);

			//	Get the file location and name of our template file
			$templateFile = $this->getTemplateFile();
			$this->template['total'] = $this->cObj->fileResource( $this->internal['templateFile'] );
			$this->template['list'] =  $this->getNamedTemplateContent(null, $view);
			$this->template['content'] = $this->getNamedSubpart('CONTENT', $this->template['list'] );
			$this->template['item'] = $this->getNamedSubpart('ITEM', $this->template['content'] );

		}
	}

	/**
	 * getTemplateFile: Returns the HTML-template path/location (from FF, TS or defaults in that order)
	 *
	 * @return	string		Path of the template file specified in configuration (or defaults)
	 */
	function getTemplateFile() {

		//	Load the HTML template
		$templateFile = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'templateFile', 'sDEF');

		//	If template loaded from plugin, prepend upload path, otherwise use templateFile from TypoScript configuration
		$templateFile = $templateFile ? 'uploads/tx_wecsermons/'.$templateFile : $this->conf['templateFile'];

		//	If template isn't defined in Typoscript, use our hardcoded defaults (which are different depending on the data source)
		if (strpos($templateFile, 'pi1/wecsermons.tmpl') !== FALSE) {
			$detailTable = $this->getConfigVal( $this, 'detail_table', 'slistView', 'detailTable', $this->conf, 'tx_wecsermons_sermons' );
			switch ($detailTable) {
				case 'tx_wecsermons_sermons':
					$templateFile = 'EXT:wec_sermons/pi1/sermons.tmpl';
					break;
				case 'tx_wecsermons_series':
					$templateFile = 'EXT:wec_sermons/pi1/series.tmpl';
					break;
				case 'tx_wecsermons_topics':
					$templateFile = 'EXT:wec_sermons/pi1/topics.tmpl';
					break;
				case 'tx_wecsermons_seasons':
					$templateFile = 'EXT:wec_sermons/pi1/seasons.tmpl';
					break;
				case 'tx_wecsermons_speakers':
					$templateFile = 'EXT:wec_sermons/pi1/speakers.tmpl';
					break;
				default:
					$templateFile = 'EXT:wec_sermons/pi1/wecsermons.tmpl'; # the original ;-)
					break;
			}
		}

		//	Store the name of the template file, for retrieval later if needed
		$this->internal['templateFile'] = $templateFile;

		return $templateFile;

	}

	/**
	 * getGroupResult: Retrieves the result set for group records, when grouping is enabled.
	 * 'emptyGroups,' defined as a constant in LIST or LATEST views, toggles the option to return group records which currently have no records related to them.
	 * 'orderBy' will determine the ordering of record set.
	 * 'maxGroupResults' will determine how many groups are returned
	 *
	 * @param	string				$groupTable: The table name to group by
	 * @param	string				$detailTable: The table name to display detail records by
	 * @param	array					$lConf: Locally scoped configuration array from TypoScript
	 * @param	boolean				$getCount: A boolean value enabling the return of the row count, rather than the rows themselves.
	 * @return	resource		A sql resource returned from sql_query()
	 */
	 function getGroupResult( $groupTable, $detailTable, $lConf, $getCount = 0 ) {

		$pointer = $this->piVars['pointer'];
		$pointer = intval($pointer);
		$groupUids = '';

		// get the links (tables, columns) between our group and detail tables
		$relatables = $this->getRelatables($detailTable, $groupTable);
		if ( ! is_array($relatables) ) {
			$this->throwError(
				'WEC Sermon Management System Error!',
				'Unable to relate Group and Detail tables.',
				'Please examine your list setup.'
			);
		}

		//	Check if search word was used to filter list.
		if( $this->piVars['sword'] ) {

			$pidList = $this->pi_getPidList($this->conf['pidList'],$this->conf['recursive']);
			$searchFieldList = $lConf[$detailTable.'.']['searchFieldList'];


			//	Retrieve result set of series filtered by matching sermons
			$query = 'select '.$groupTable.'uid,'.$groupTable.'.title'.chr(10)
				.'from '.$detailTable.chr(10)
				.'inner join '.$relatables['intermediateTable'].chr(10)
				.' on '.$detailTable.'.uid = '.$relatables['intermediateTable'].'.'.$relatables['current2intermediate'].chr(10)
				.'inner join '.$groupTable.chr(10)
				.' on '.$groupTable.'.uid = '.$relatables['intermediateTable'].'.'.$relatables['related2intermediate'].char(10);

			$WHERE = "where 1=1 ".$this->cObj->searchWhere($this->piVars['sword'],$searchFieldList,$detailTable).$this->cObj->enableFields($groupTable) . $this->cObj->enableFields( $detailTable ) .' AND '.$groupTable.'.pid IN ('.$pidList.')';

			//	Retrieve start or end date from plugin or typoscript configuration
			$startDate = $this->getConfigVal( $this, 'startDate', 'slistView', 'startDate', $lConf );
			$endDate = $this->getConfigVal( $this, 'endDate', 'slistView', 'endDate', $lConf );

			// If detail table is sermon records, then process start and end date filter
			if( !strcmp( $detailTable, 'tx_wecsermons_sermons' ) && ($startDate || $endDate ) ){

				// Check if date() function was specified in startdate, and calculate new date if necessary
				if( strstr($startDate,'date()') ) {
					if( strstr($startDate,'-') ) {

						$formula = explode('-',$startDate);
						if( !strcmp($formula[0],'date()' ) && t3lib_div::testint($formula[1]) )
							$startDate = time() - $formula[1]*86400; // calculate difference in days
						else
							$startDate = $formula[0]*86400 - time(); // calculate difference in days
					}
					elseif( strstr($startDate,'+') ) {

						$formula = explode('+',$startDate);
						if( !strcmp($formula[0],'date()' ) && t3lib_div::testint($formula[1]) )
							$startDate = time() + $formula[1]*86400; // calculate difference in days
						else
							$startDate = $formula[0]*86400 + time(); // calculate difference in days
					}
				}
				elseif( strstr($startDate, '/') || strstr($startDate, '-')) { // If startdate is string that needs to be converted to unixtime
					$startDate = $startDate ? strtotime($startDate) : '';
				}

				// Check if date() function was specified in enddate, and calculate new date if necessary
				if( strstr($endDate,'date()') ) {
					if( strstr($endDate,'-') ) {

						$formula = explode('-',$endDate);
						if( !strcmp($formula[0],'date()' ) && t3lib_div::testint($formula[1]) )
							$endDate = time() - $formula[1]*86400; // calculate difference in days
						else
							$endDate = $formula[0]*86400 - time(); // calculate difference in days
					}
					elseif( strstr($endDate,'+') ) {

						$formula = explode('+',$endDate);
						
						if( !strcmp($formula[0],'date()' ) && t3lib_div::testint($formula[1]) )
							$endDate = time() + $formula[1]*86400; // calculate difference in days
						else
							$endDate = $formula[0]*86400 + time(); // calculate difference in days
					}
				}
				elseif( strstr($endDate, '/') || strstr($endDate, '-')) { // If enddate is string that needs to be converted to unixtime
					$endDate = $endDate ? strtotime($endDate) : '';
				}

				$WHERE .= ($startDate && !strcmp( $detailTable, 'tx_wecsermons_sermons' )) ? ' AND occurrence_date >= ' .  $startDate : '';
				$WHERE .= ($endDate && !strcmp( $detailTable, 'tx_wecsermons_sermons' )) ? ' AND occurrence_date <= ' .  $endDate : '';

			}

			$query .= $WHERE;
			$res = $GLOBALS['TYPO3_DB']->sql_query( $query );


			//	Aggregate series uids to filter by
			while( $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc( $res ) ) {
				$groupUids .= $row['uid'].',';

			}


			$groupUids = rtrim( $groupUids, ',');

		}

		$this->internal['results_at_a_time'] = t3lib_div::intInRange($lConf['maxGroupResults'],1,1000);
		$orderBy = '';
		$res = '';

		// Order by data:
		if ($this->internal['orderBy'])	{
			if (t3lib_div::inList($this->internal['orderByList'],$this->internal['orderBy']))	{
				$fieldArray  = explode( ',',$this->internal['orderBy'] );
				$orderString = '';
				foreach( $fieldArray as $field ) {
					$orderString .= $groupTable.".".$field.",";
				}
				$orderString = rtrim( $orderString, "," );
				$orderBy = ' ORDER BY '.$orderString.($this->internal['descFlag']?' DESC':'');
			}
		}

		//	If display of empty groups is enabled, then simply query the group table for any visible records
		if( $lConf['emptyGroups'] ) {

			if( $getCount )
				list($res) = $this->pi_exec_query( $groupTable,1 );
			else
				$res = $this->pi_exec_query( $groupTable );
		}
		else {	//	Otherwise filter out those groupTable records which do not have associated records

			$pidList = $this->pi_getPidList($this->conf['pidList'],$this->conf['recursive']);

			$select = $getCount ? 'SELECT count(distinct '.$groupTable.'.uid) ' : "SELECT distinct " . $groupTable . ".* ";
			$from = 'from '.$detailTable.chr(10)
				.'inner join '.$relatables['intermediateTable'].chr(10)
				.' on '.$detailTable.'.uid = '.$relatables['intermediateTable'].'.'.$relatables['current2intermediate'].chr(10)
				.'inner join '.$groupTable.chr(10)
				.' on '.$groupTable.'.uid = '.$relatables['intermediateTable'].'.'.$relatables['related2intermediate'].chr(10);
			$where = 'where 1=1 ' . $this->cObj->enableFields($groupTable)
				.$this->cObj->enableFields( $detailTable ) . ' AND '.$groupTable.'.pid IN ('.$pidList.')'
				. ($groupUids ? ' AND '.$groupTable.'.uid in ('.$groupUids.') ' : '');
			$limit = $getCount ? '' : " LIMIT ".($pointer*$this->internal['results_at_a_time']).",".$this->internal['results_at_a_time'];

			//	If start or end date was set, then add this to the query WHERE clause.
			$startDate = $this->getConfigVal( $this, 'startDate', 'slistView', 'startDate', $lConf );
			$endDate = $this->getConfigVal( $this, 'endDate', 'slistView', 'endDate', $lConf );

			// If detail table is sermon records, then process start and end date filter
			if( !strcmp( $detailTable, 'tx_wecsermons_sermons' ) && ($startDate || $endDate )) {

				// Check if date() function was specified in startdate, and calculate new date if necessary
				if( strstr($startDate,'date()') ) {
					if( strstr($startDate,'-') ) {

						$formula = explode('-',$startDate);
						if( !strcmp($formula[0],'date()' ) && t3lib_div::testint($formula[1]) )
							$startDate = time() - $formula[1]*86400; // calculate difference in days
						else
							$startDate = $formula[0]*86400 - time(); // calculate difference in days
					}
					elseif( strstr($startDate,'+') ) {

						$formula = explode('+',$startDate);
						if( !strcmp($formula[0],'date()' ) && t3lib_div::testint($formula[1]) )
							$startDate = time() + $formula[1]*86400; // calculate difference in days
						else
							$startDate = $formula[0]*86400 + time(); // calculate difference in days
					}
				}
				elseif( strstr($startDate, '/') || strstr($startDate, '-')) { // If startdate is string that needs to be converted to unixtime
					$startDate = $startDate ? strtotime($startDate) : '';
				}

				// Check if date() function was specified in enddate, and calculate new date if necessary
				if( strstr($endDate,'date()') ) {
					if( strstr($endDate,'-') ) {

						$formula = explode('-',$endDate);
						if( !strcmp($formula[0],'date()' ) && t3lib_div::testint($formula[1]) )
							$endDate = time() - $formula[1]*86400; // calculate difference in days
						else
							$endDate = $formula[0]*86400 - time(); // calculate difference in days
					}
					elseif( strstr($endDate,'+') ) {

						$formula = explode('+',$endDate);
					
						if( !strcmp($formula[0],'date()' ) && t3lib_div::testint($formula[1]) )
							$endDate = time() + $formula[1]*86400; // calculate difference in days
						else
							$endDate = $formula[0]*86400 + time(); // calculate difference in days
					}
				}
				elseif( strstr($endDate, '/') || strstr($endDate, '-')) { // If enddate is string that needs to be converted to unixtime
					$endDate = $endDate ? strtotime($endDate) : '';
				}

				$where .= ($startDate && !strcmp( $detailTable, 'tx_wecsermons_sermons' )) ? ' AND occurrence_date >= ' .  $startDate : '';
				$where .= ($endDate && !strcmp( $detailTable, 'tx_wecsermons_sermons' )) ? ' AND occurrence_date <= ' .  $endDate : '';

			}

			$query = $select . $from . $where . $orderBy . $limit;
			$res = $GLOBALS['TYPO3_DB']->sql_query( $query );

		}
		return $res;
	}

	/**
	 * getSermonResources: Returns all the resources associated with a particular sermon uid, or a specific resource uid.
	 * The function runs a SQL query to find all related resources for a particular sermon record, joining together multiple tables. The array returned is populated with fields from multiple tables.
	 *
	 * @param	string		$sermonUid
	 * @param	string		$resourceUid:	The UID of a resource. If specified, only this one resource
	 * @return	array		An array of associative arrays. Each associative array represents all properties of one resource, and all properties of its type.
	 */
	function getSermonResources( $sermonUid = '', $resourceUid = '') {

		//	Build query to select resource attributes along with resource type name
		$WHERE = $sermonUid ? 'AND tx_wecsermons_sermons.uid = ' . $sermonUid . ' ' :'';
		$WHERE = $resourceUid ? 'AND tx_wecsermons_resources.uid = ' . $resourceUid . ' ' : $WHERE;
		$WHERE .= $this->cObj->enableFields('tx_wecsermons_sermons');
		$WHERE .= $this->cObj->enableFields('tx_wecsermons_resources');
		$WHERE .= " AND( tx_wecsermons_resources.type = '0' OR (" . ltrim( $this->cObj->enableFields('tx_wecsermons_resource_types'), ' AND') . '))';

		$query = 'select distinct
		tx_wecsermons_resources.uid,
		tx_wecsermons_resources.type,
		tx_wecsermons_resources.title,
		tx_wecsermons_resources.description,
		tx_wecsermons_resources.graphic,
		tx_wecsermons_resources.alttitle,
		tx_wecsermons_resources.file,
		tx_wecsermons_resources.webaddress1,
		tx_wecsermons_resources.webaddress2,
		tx_wecsermons_resources.webaddress3,
		tx_wecsermons_resources.rendered_record,
		tx_wecsermons_resources.subtitle,
		tx_wecsermons_resources.summary,
		tx_wecsermons_resources.islinked,
		tx_wecsermons_resource_types.type type_type,
		tx_wecsermons_resource_types.description type_description,
		tx_wecsermons_resource_types.icon,
		tx_wecsermons_resource_types.alttitle type_alttitle,
		tx_wecsermons_resource_types.marker_name,
		tx_wecsermons_resource_types.template_name,
		tx_wecsermons_resource_types.mime_type,
		tx_wecsermons_resource_types.querystring_param,
		tx_wecsermons_resource_types.typoscript_object_name,
		tx_wecsermons_resource_types.rendering_page

		from tx_wecsermons_resources
			left join tx_wecsermons_sermons_resources_rel on tx_wecsermons_resources.uid=tx_wecsermons_sermons_resources_rel.resourceid
			left join tx_wecsermons_sermons on tx_wecsermons_sermons.uid=tx_wecsermons_sermons_resources_rel.sermonid
			left join tx_wecsermons_resource_types on tx_wecsermons_resources.type=tx_wecsermons_resource_types.uid
				where 1=1 ' . $WHERE;
		$query .= ' ORDER BY tx_wecsermons_sermons_resources_rel.sorting';

		$res = $GLOBALS['TYPO3_DB']->sql_query( $query );

		$resources = array();

		//	TODO: What if none found?
		//	For each related resource, determine the type and render it
		while( $record = $GLOBALS['TYPO3_DB']->sql_fetch_assoc( $res ) )
			$resources[] = $record;

		//	Store the resources array in internal storage for access later if needed
		$this->internal['resources'] = $resources;

		return $resources;

	}


	/**
	 * getSeriesResources: Returns all the resources associated with a particular series uid, or a specific resource uid.
	 * The function runs a SQL query to find all related resources for a particular series record, joining together multiple tables. The array returned is populated with fields from multiple tables.
	 *
	 * @param	string		$seriesUid
	 * @param	string		$resourceUid:	The UID of a resource. If specified, only this one resource
	 * @return	array		An array of associative arrays. Each associative array represents all properties of one resource, and all properties of its type.
	 */
	function getSeriesResources( $seriesUid = '', $resourceUid = '') {

		//	Build query to select resource attributes along with resource type name
		$WHERE = $seriesUid ? 'AND tx_wecsermons_series.uid = ' . $seriesUid . ' ' :'';
		$WHERE = $resourceUid ? 'AND tx_wecsermons_resources.uid = ' . $resourceUid . ' ' : $WHERE;
		$WHERE .= $this->cObj->enableFields('tx_wecsermons_series');
		$WHERE .= $this->cObj->enableFields('tx_wecsermons_resources');
		$WHERE .= " AND( tx_wecsermons_resources.type = '0' OR (" . ltrim( $this->cObj->enableFields('tx_wecsermons_resource_types'), ' AND') . '))';

		$query = 'select distinct
		tx_wecsermons_resources.uid,
		tx_wecsermons_resources.type,
		tx_wecsermons_resources.title,
		tx_wecsermons_resources.description,
		tx_wecsermons_resources.graphic,
		tx_wecsermons_resources.alttitle,
		tx_wecsermons_resources.file,
		tx_wecsermons_resources.webaddress1,
		tx_wecsermons_resources.webaddress2,
		tx_wecsermons_resources.webaddress3,
		tx_wecsermons_resources.rendered_record,
		tx_wecsermons_resources.subtitle,
		tx_wecsermons_resources.summary,
		tx_wecsermons_resources.islinked,
		tx_wecsermons_resource_types.type type_type,
		tx_wecsermons_resource_types.description type_description,
		tx_wecsermons_resource_types.icon,
		tx_wecsermons_resource_types.alttitle type_alttitle,
		tx_wecsermons_resource_types.marker_name,
		tx_wecsermons_resource_types.template_name,
		tx_wecsermons_resource_types.mime_type,
		tx_wecsermons_resource_types.querystring_param,
		tx_wecsermons_resource_types.typoscript_object_name,
		tx_wecsermons_resource_types.rendering_page

		from tx_wecsermons_resources
			left join tx_wecsermons_series_resources_rel on tx_wecsermons_resources.uid=tx_wecsermons_series_resources_rel.resourceid
			left join tx_wecsermons_series on tx_wecsermons_series.uid=tx_wecsermons_series_resources_rel.seriesid
			left join tx_wecsermons_resource_types on tx_wecsermons_resources.type=tx_wecsermons_resource_types.uid
				where 1=1 ' . $WHERE;
		# TODO: add sorting to our intermediate table
		#$query .= ' ORDER BY tx_wecsermons_series_resources_uid_mm.sorting';

		$res = $GLOBALS['TYPO3_DB']->sql_query( $query );

		$resources = array();

		//	TODO: What if none found?
		//	For each related resource, determine the type and render it
		while( $record = $GLOBALS['TYPO3_DB']->sql_fetch_assoc( $res ) )
			$resources[] = $record;

		//	Store the resources array in internal storage for access later if needed
		$this->internal['resources'] = $resources;

		return $resources;

	}

	/**
	 * emptyResourceSubparts: This function is used to determin all the possible subpart marker names based on custom resource types, and set the marker array value to an empty string.
	 * This initializes the marker array with empty strings before use.
	 *
	 * @param	array		$subpartArray:	 The subpartArray to be initalized
	 * @param	string	$templateContent: A content stream which we will scan for markers
	 * @return	void
	 */
	function emptyResourceSubparts( &$subpartArray, $templateContent = '' ) {

		$subparts = array();

		//	Find every subpart marker in the template
		preg_match_all('!\<\!--[a-zA-Z0-9 ]*(###[A-Z0-9_-|]*\###)[a-zA-Z0-9 ]*-->!is', $templateContent, $subparts);

		//	Truncate the array, removing duplicates
		$usedSubparts = array_unique($subparts[1]);

		//	Flip the keys and values for compare against our internal arrays
		$usedSubparts = array_flip($usedSubparts);

		//	Find the marker names for every defined resource type
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			'distinct marker_name',
			'tx_wecsermons_resource_types',
			  'marker_name != \'\' '.$this->cObj->enableFields( 'tx_wecsermons_resource_types' )
		);

		//	Iterate every marker, setting the associated subpart to an empty string
		while( $marker = $GLOBALS['TYPO3_DB']->sql_fetch_assoc( $res ) )
			$subpartArray[$this->getMarkerName( $marker['marker_name'] )] = '';

		//	Set the default resource type marker subpart to an empty string
		$subpartArray[$this->getMarkerName( $this->conf['defaultMarker'] )] = '';


		//	Pull out unused markers from subpart array
		$subpartArray = $this->array_intersect_key( $subpartArray, $usedSubparts);

		return $subpartArray;
	}

	/**
	 * throwError: A helper function that returns an HTML formatted error message for display on the front-end. ** MUST be user friendly!! **
	 *
	 * @param	string		$type: A given type or category of error message we are displaying
	 * @param	string		$message: The error message to be displayed
	 * @param	string		$detail: Any detail we'd like to include, such as the variable name that caused the error and it's value at the time.
	 * @return	string		An HTML formatted error message
	 */
	function throwError( $type, $message, $detail = '' ) {

		//	TODO: Possibly add logic to fire an e-mail off with detail, or log the error.

		$format =  sprintf(
		'
			<div style="border: 1px solid black; padding: 0 1em 0 1em; margin: 1em 0 1em 0; max-width:400px; background-color: #DDDD66; float: center;">
				<h1>%s</h1>
				<p>%s</p>
				<p>%s</p>
			</div>
		',
		htmlspecialchars( $type ), htmlspecialchars( $message ), nl2br( htmlentities( $detail ) ) );

		return $format;
	}

	/**
	 * getTutorial:	Retrieves tutorial content, depending on the tutorial selected in the plugin configuration.
	 *
	 * @param	int		$tutorial: An integer value determining which tutorial we wish to render.
	 * @return	string		Content of the tutorial
	 */
	function getTutorial ( $tutorial ) {

		$content = '';

		//	Check which tutorial was chosen, and pull in the content from the apporpriate static HTML file
		switch( $tutorial )
		{
			case '1':	//	Ginghamsburg tutorial

				switch($this->piVars['page']) {
					case '2' :
						$content .="<H1>Example page of a view on study material</H1>";
						$content .= t3lib_div::getURL(t3lib_extMgm::extPath('wec_sermons').'tut/ging/study_view.htm');
					break;

					case '3':
						$content .="<H1>Example page of another view on study material</H1>";
						$content .= t3lib_div::getURL(t3lib_extMgm::extPath('wec_sermons') .'tut/ging/study_exp.htm');
					break;

					default:
						$content .="<H1>Example page of a view on a sermon listing</H1>";
						$content .= t3lib_div::getURL(t3lib_extMgm::extPath('wec_sermons') .'tut/ging/list_view.htm');

				}

				//	Replace existing relative paths to files
				$content = str_replace( 'images/', t3lib_extMgm::siteRelPath('wec_sermons').'tut/ging/images/', $content );

			break;

			case '2':	//	Living Water tutorial

				switch($this->piVars['page']) {
					case '2' :
						$content .="<H1>Example page of a view on sermon series</H1>";
						$content .= t3lib_div::getURL(t3lib_extMgm::extPath('wec_sermons').'tut/living_water/series_view.htm');
					break;

					case '3':
						$content .="<H1>Example page of a view on sermons archive</H1>";
						$content .= t3lib_div::getURL(t3lib_extMgm::extPath('wec_sermons').'tut/living_water/archive_view.htm');
					break;

					default:
						$content .="<H1>Example page of a view on a single sermon</H1>";
						$content .= t3lib_div::getURL(t3lib_extMgm::extPath('wec_sermons').'tut/living_water/single_view.htm');

				}


				//	Replace existing relative paths
				$content = str_replace( 'images/', t3lib_extMgm::siteRelPath('wec_sermons').'tut/living_water/images/', $content );
			break;
			default:

		}

		//	set piVar['page'] = 1, or increment to next page
		is_null( $this->piVars['page'] ) ? $this->piVars['page'] = 2 : $this->piVars['page']++;

		//	reset counter to 1 when > 3
		if( $this->piVars['page'] > 3 ) $this->piVars['page'] = 1;

		//	Modify all links in the static HTML file, linking to the next screen
		$content = preg_replace('/href="#"/',  'href="'.$this->pi_linkTP_keepPIvars_url(array() , 1 ).'"', $content);


		return $content;
	}	// End getTutorial


	/**
	 * uniqueCsv:	Given any number of CSV strings, this function combines the strings, returning a CSV string without duplicate values.
	 *
	 * @return	string		A CSV string, with unique values.
	 */
	function uniqueCsv()	{
		$max = func_num_args();
		$ttlString = '';
		for( $i =0; $i < $max; $i++ )  {
			$ttlString .=func_get_arg($i) .  ',';
	
		}
		return implode(',', array_unique( t3lib_div::trimExplode(',', $ttlString, 1) ) );
	}

	/**
	 * unique_array: Given any number of single dimensional arrays, this function combines the arrays, returning an array without duplicate values.
	 *
	 * @return	string	An array, without duplicate values.
	 */
	function unique_array() {
		$max = func_num_args();
		$ttlString = '';
		for( $i =0; $i < $max; $i++ )  {
			$ttlString .=func_get_arg($i) .  ',';

		}
		return array_unique( t3lib_div::trimExplode(',', $ttlString, 1) ) ;

	}


	# Note that the following function is very specific to our Sermons datamodel (TCA setup).  It is *not* generic like, say getRelatedRecords
	/**
	 * getRelatables: Return an associative array mapping the linkage points (table and column names) between a group/detail table combination
	 *
	 * @param	string		$currentTable: The table with details (e.g., tx_wecsermons_sermons)
	 * @param	string		$relatedTable: The table for grouping (e.g., tx_wecsermons_series)
	 * @return	mixed		Mapping for the following keys: (1) intermediateTable (2) current2intermediateColumn (3) related2intermediateColumn
	 *				(false on error)
	 */
	function getRelatables( $currentTable, $relatedTable ) {
		$retMap = array();
		t3lib_div::loadTCA( $currentTable );
		foreach( $GLOBALS['TCA'][$currentTable]['columns'] as $columnName => $value ) {
			$columnConfig = $value['config'];
			if ( $columnConfig['type'] == 'inline'
			     && array_key_exists( 'foreign_table', $columnConfig )
       			     && array_key_exists( 'foreign_field', $columnConfig )
			     && array_key_exists( 'foreign_selector', $columnConfig ) ) {
				t3lib_div::loadTCA( $columnConfig['foreign_table'] );
				// columnEndTable is a variable holding the referenced table (so we move from detail->intermediate->group)
				$columnEndTable = rtrim($GLOBALS['TCA'][$columnConfig['foreign_table']]['columns'][$columnConfig['foreign_selector']]['config']['foreign_table']);
				if ( $columnEndTable == $relatedTable ) {
					$retMap['intermediateTable'] = $columnConfig['foreign_table'];
					$retMap['current2intermediate'] = $columnConfig['foreign_field'];
					$retMap['related2intermediate'] = $columnConfig['foreign_selector'];
					return $retMap;
				}
			}
		}
		return false;
	}

	# The following function is likely incomplete, as I'm only using (and aware of, for that matter) a small subset of TCA relation types.
	# In my view, it could be very useful to flesh this out for other extension authors or even t3 core (low hanging fruit).
	# Note that only the first branch (IRRE/selector/combo) is working at present, the others have things backwards (bleh).
        /**
	 * getRelatedRecords: Return a list of uids from $relatedTable that are tied to $currentTable's provided $uid
	 *
	 * @param	int		$uid: The record from $currentTable with which we'll find associated foreign records from $relatedTable
	 * @param	string		$currentTable: The table (name) for which we're finding foreign associated records
	 * @param	string		$relatedTable: The foreign table (name) in which we're finding records that tie to $currentTable
	 * @return	mixed		List of associated uids in foreign table ($relatedTable) (or false on failure)
	 */
	function getRelatedRecords( $uid, $currentTable, $relatedTable ) {
		$retList = array(); // we'll return this to the caller, a list of matching uids
		t3lib_div::loadTCA( $currentTable );

		foreach( $GLOBALS['TCA'][$currentTable]['columns'] as $columnName => $value ) {
			$columnConfig = $value['config'];
			if ( $columnConfig['type'] == 'inline' 
			     && array_key_exists( 'foreign_table', $columnConfig )
			     && array_key_exists( 'foreign_field', $columnConfig )
			     && array_key_exists( 'foreign_selector', $columnConfig ) ) {
				//
				// it's an IRRE n:m (intermediate with selector) relation
				//
				t3lib_div::loadTCA( $columnConfig['foreign_table'] );

				// columnEndTable is a variable holding the referenced table (so we move from current->intermediate->referenced)
				$columnEndTable = rtrim($GLOBALS['TCA'][$columnConfig['foreign_table']]['columns'][$columnConfig['foreign_selector']]['config']['foreign_table']);

				if ( $columnEndTable == $relatedTable ) {
					$stmt = "select distinct i." . $columnConfig['foreign_field'] . "
					         from " . $columnConfig['foreign_table'] . " i
					          inner join " . $relatedTable . " g
					           on g.uid = i." . $columnConfig['foreign_selector'] . "
					         where g.uid = " . $uid;
					$res = $GLOBALS['TYPO3_DB']->sql_query($stmt);
					while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_row($res) ) {
						$retList[] = $row[0];
					}
					return $retList;
				}
			} elseif ( $columnConfig['type'] == 'inline'
			            && array_key_exists( 'foreign_table', $columnConfig )
			            && array_key_exists( 'foreign_field', $columnConfig )
			            && array_key_exists( 'foreign_label', $columnConfig ) ) {
				//
				// it's an IRRE but without selector, so we'll hinge upon foreign_label (although this is optional iirc)
				//
                                t3lib_div::loadTCA( $columnConfig['foreign_table'] );

                                // columnEndTable is a variable holding the referenced table (so we move from current->intermediate->referenced)
                                $columnEndTable = rtrim($GLOBALS['TCA'][$columnConfig['foreign_table']]['columns'][$columnConfig['foreign_label']]['config']['foreign_table']);

                                if ( $columnEndTable == $relatedTable ) {
                                        $res = $GLOBALS['TYPO3_DB']->sql_query("select distinct i." . $columnConfig['foreign_label'] . "
                                                                                from " . $columnConfig['foreign_table'] . " i
                                                                                 inner join " . $currentTable . " c
                                                                                  on c.uid = i." . $columnConfig['foreign_field'] . "
					                                        where c.uid = " . $uid);
                                        while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_row($res) ) {
                                                $retList[] = $row[0];
                                        }
                                        return $retList;
                                }
			} elseif ( $columnConfig['type'] == 'select'
			            && array_key_exists( 'foreign_table', $columnConfig )
			            && $columnConfig['foreign_table'] == $relatedTable ) {
				//
				// it's a traditional select-type with a database table
				//
				// note: we need to put up the MM subtype before this, "specific before generic"
				//
				$res = $GLOBALS['TYPO3_DB']->sql_query("select " . $columnName . " from " . $currentTable . " where uid = " . $uid);
				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_row($res) ) {
					$retList[] = $row[0];
				}
				return $retList;
                        } elseif ( $columnConfig['type'] == 'group'
			            && array_key_exists( 'internal_type', $columnConfig )
			            && $columnConfig['internal_type'] == "db"
                                    && array_key_exists( 'foreign_table', $columnConfig )
			            && $columnConfig['foreign_table'] == $relatedTable ) {
                                //
                                // it's a traditional select-type with a database table
                                //
                                // note: we need to put up the MM subtype before this, "specific before generic"
                                //
				$res = $GLOBALS['TYPO3_DB']->sql_query("select " . $columnName . " from " . $currentTable . " where uid = " . $uid);
				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_row($res) ) {
					$retList[] = $row[0];
				}
				return $retList;
			} else {
				continue; // go on to the next field
			}
		}

		// if we get here, there was no match, so return false
		return false;
	}

	/**
	 * getConfigVal: Return the value from either plugin flexform, typoscript, or default value, in that order
	 *
	 * @param	object		$Obj: Parent object calling this function
	 * @param	string		$ffField: Field name of the flexform value
	 * @param	string		$ffSheet: Sheet name where flexform value is located
	 * @param	string		$TSfieldname: Property name of typoscript value
	 * @param	array			$lConf: TypoScript configuration array from local scope
	 * @param	mixed			$default: The default value to assign if no other values are assigned from TypoScript or Plugin Flexform
	 * @return	mixed		Configuration value found in any config, or default
	 */
	function getConfigVal( &$Obj, $ffField, $ffSheet, $TSfieldname, $lConf, $default = '' ) {

		//	Retrieve values stored in flexform and typoscript
		$ffValue = $Obj->pi_getFFvalue($Obj->cObj->data['pi_flexform'], $ffField, $ffSheet);
		$tsValue = $lConf[$TSfieldname];

		//	Use flexform value if present, otherwise typoscript value
		$retVal = $ffValue ? $ffValue : $tsValue;

		//	Return value if found, otherwise default
		return $retVal ? $retVal : $default;
	}

	/**
	 * splitTableAndUID: Helper function that splits a table name and uid from the format stored by the TYPO3 backend, returning the values in an array. Format: 'tablename_uid'
	 *
	 * @param	string		$record: The string value of tablename and uid in the form 'table_uid'
	 * @return	array		Array in the form array( 'table' => tablename, 'uid' => uid )
	 */
	function splitTableAndUID($record) {
		$break = strrpos($record, "_");
		$uid = substr($record, $break+1);
		$table = substr($record, 0, $break);

		return array("table" => $table, "uid" => $uid);
	}

	/**
	 * Computes the intersection of arrays using keys for comparison. This is included for compatibility with PHP versions < 5
	 *
	 * @return	array		Returns an array containing all the values of first array parameter which have matching keys that are present in all the arguments
	 */
	function array_intersect_key() {
		$arrs = func_get_args();
		$result = array_shift($arrs);
		foreach ($arrs as $array) {
			foreach ($result as $key => $v) {
				if (!array_key_exists($key, $array)) {
					unset($result[$key]);
				}
			}
		}
		return $result;
	}

}	// End class tx_wecsermons_pi1

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wec_sermons/pi1/class.tx_wecsermons_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wec_sermons/pi1/class.tx_wecsermons_pi1.php']);
}
 
?>
