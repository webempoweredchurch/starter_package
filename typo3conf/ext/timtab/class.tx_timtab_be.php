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
 * class.tx_timtab_be.php
 *
 * Class which implements methods to connect to hooks in TCEmain
 *
 * $Id: class.tx_timtab_be.php,v 1.7 2005/10/08 23:01:53 ingorenner Exp $
 *
 * @author Ingo Renner <typo3@ingo-renner.com>
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   66: class tx_timtab_be
 *   85:     function preInit($status, $id, $fieldArray, $pObj)
 *  114:     function init()
 *  159:     function processDatamap_postProcessFieldArray($status, $table, $id, &$fieldArray, $pObj)
 *  227:     function processDatamap_afterDatabaseOperations($status, $table, $id, $fieldArray, $pObj)
 *  274:     function getCurrentPost($tt_news_uid)
 *  291:     function isBlogPost($id, $tt_news = '')
 *  308:     function clearPageCache()
 *
 * TOTAL FUNCTIONS: 7
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */
$PATH_timtab = t3lib_extMgm::extPath('timtab');
define('PATH_tslib', PATH_site.'typo3/sysext/cms/tslib/');
require_once($PATH_timtab.'class.tx_timtab_trackback.php');
#require_once($PATH_timtab.'class.tx_timtab_pingback.php');
require_once(PATH_t3lib.'class.t3lib_tstemplate.php');
require_once(PATH_t3lib.'class.t3lib_page.php');
require_once(PATH_t3lib.'class.t3lib_timetrack.php');
require_once(PATH_t3lib.'class.t3lib_userauth.php');
require_once(PATH_tslib.'class.tslib_feuserauth.php');
require_once(PATH_tslib.'class.tslib_fe.php');
require_once(PATH_tslib.'class.tslib_content.php');


require_once (PATH_t3lib."class.t3lib_tsparser_ext.php");

$TT = new t3lib_timeTrack;
$TT->start();

class tx_timtab_be {
	var $prefixId 		= 'tx_timtab_be';		// Same as class name
	var $scriptRelPath 	= 'class.tx_timtab_be.php';	// Path to this script relative to the extension dir.
	var $extKey 		= 'timtab';	// The extension key.

	var $conf;
	var $cObj;
	var $pid;
	var $status;

	/**
	 * perform some checks before doing the big init
	 *
	 * @param	string		the current status of the operation: eather 'new' or 'update'
	 * @param	integer		the uid of the current post
	 * @param	array		array of changed fields for an update, all fields for a new post
	 * @param	object		$pObj: ...
	 * @return	array
	 */
	function preInit($status, $id, &$fieldArray, &$pObj) {
		$tt_news      = array();
		$isBlogPost   = false;
		$this->status = $status;
		$this->pid    = $pObj->checkValue_currentRecord['pid'];

		if($status == 'new') { //new record
			if(!$id = $pObj->substNEWwithIDs[$id]) {
				$id = 0;
			}
		}

		if($id) {
			//update
			$isBlogPost = $this->isBlogPost($id, array());
			if($isBlogPost) {
				$tt_news = $this->getCurrentPost($id);
			}
		} else {
			//new
			$isBlogPost  = $this->isBlogPost($id, $fieldArray);
		}

		return array($isBlogPost, $tt_news);
	}

	/**
	 * initializes the configuration for the extension as we need the TS setup
	 * like blog title and timeouts for trackback in the BE, too
	 *
	 * @return	void
	 */
	function init() {
		
		//we need to get the plugin setup to create correct source URLs		
		$template = t3lib_div::makeInstance('t3lib_tsparser_ext'); 		// Defined global here!
		$template->tt_track = 0; 													// Do not log time-performance information
		$template->init();
		$sys_page = t3lib_div::makeInstance('t3lib_pageSelect');
		$rootLine = $sys_page->getRootLine($this->pid);
		$template->runThroughTemplates($rootLine); 		// This generates the constants/config + hierarchy info for the template.
		$template->generateConfig();

		$this->conf = array_merge(
			$template->setup['plugin.']['tx_timtab.'],
			$template->setup['plugin.']['tx_timtab_pi2.']
		);
	}

	/**
	 * pre processing of posts, detecting trackback URLs and saving
	 * them into $fieldsArray so that they get stored into the DB and we can ping
	 * them afterwards
	 *
	 * @param	string		$status: ...
	 * @param	string		$table: ...
	 * @param	integer		$id: ...
	 * @param	array		$fieldArray: ...
	 * @param	object		$pObj: ...
	 * @return	void
	 */
	function processDatamap_postProcessFieldArray($status, $table, $id, &$fieldArray, &$pObj) {
		//only do something when we get a tt_news record and the bodytext is changed
#xdebug_var_dump(xdebug_get_function_stack());
		if($table == 'tt_news' && $fieldArray['bodytext']) {
			$result = $this->preInit($status, $id, $fieldArray, $pObj);
			if(!$result[0]) { return; }
			$this->init();

			//initialize processing of trackbacks
			$tbObj = t3lib_div::makeInstance('tx_timtab_trackback');
			$tbObj->init($this, $fieldArray);

			if($foundURLs = $tbObj->tbAutoDiscovery($fieldArray['bodytext'])) {

				$newTbURLs = '';
				if($this->status == 'update') {
					//update a post, find new trackbacks
					$tbField = '';
					if(isset($fieldArray['tx_timtab_trackbacks'])) {
						$tbField = $fieldArray['tx_timtab_trackbacks'];
					} else {
						$tt_news = $this->getCurrentPost($id);
						$tbField = $tt_news['tx_timtab_trackbacks'];
					}
					$oldTBarray = t3lib_div::trimExplode("\n", $tbField);

					$temp = array();
					foreach($oldTBarray as $TB) {
						$parts = explode('|', $TB);
						$temp[] = (string) trim($parts[0]);
					}
					//extract new TBs
					$newTBarray = array_diff($foundURLs, $temp);

					unset($TB);
					reset($oldTBarray);
					$oldTBs = '';
					foreach($oldTBarray as $TB) {
						$oldTBs .= $TB.chr(10);
					}

					foreach($newTBarray as $url) {
						$newTbURLs .= $url.'|0|new'.chr(10);
					}
					$newTbURLs = trim($oldTBs.$newTbURLs);

				} elseif($this->status == 'new') {
					//creating a new post
					foreach($foundURLs as $url) {
						$newTbURLs .= $url.'|0|new'.chr(10);
					}
					$newTbURLs = trim($newTbURLs);
				}

				$fieldArray['tx_timtab_trackbacks'] = $newTbURLs;
			}
		}
	}

	/**
	 * post processing of tt_news entries, sending pings
	 *
	 * @param	string		not relevant for us
	 * @param	string		telling us which table the record belongs to, we will process tt_news records only
	 * @param	integer		record uid
	 * @param	array		database record
	 * @param	object		the parentobject (TCEmain)
	 * @return	void
	 */
	function processDatamap_afterDatabaseOperations($status, $table, $id, &$fieldArray, &$pObj) {
		//only do something when we get a tt_news record and the bodytext is changed
//xdebug_var_dump(xdebug_get_function_stack());
		if($table == 'tt_news' && $fieldArray['bodytext']) {
			$result = $this->preInit($status, $id, $fieldArray, $pObj);
			if(!$result[0]) { return; } else { $tt_news = $result[1]; }
			$this->init();

			//processing trackbacks
			$tbObj = t3lib_div::makeInstance('tx_timtab_trackback');
			$tbObj->init($this, $tt_news);
			$TBstatus = $tbObj->getTrackbackStatus($tt_news['tx_timtab_trackbacks'], $this->status);

			if(is_array($TBstatus)) {
				foreach($TBstatus as $k => $TB) {
					// Attempt to ping each trackback URL
					if(!empty($TB['url']) && $TB['status'] == 0) {
						$result = $tbObj->ping($TB['url']);
						if($result[0]) {
							//success
							$TBstatus[$k]['status'] = 1;
							unset($TBstatus[$k]['reason']);
						} else {
							//failed
							$TBstatus[$k]['reason'] = $result[1];
						}
					}
				}
			}

			//update trackback status in tt_news record
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery(
				'tt_news',
				'uid = '.$tt_news['uid'],
				array('tx_timtab_trackbacks' => $tbObj->setTrackbackStatus($TBstatus))
			);
			//end processing trackbacks

			$this->clearPageCache();
		}
	}

	/**
	 * gets the current tt_news record we are working on
	 *
	 * @param	integer		the tt_news uid of the record we want to get
	 * @return	array
	 */
	function getCurrentPost($tt_news_uid) {
		//get the current tt_news record
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
 			'*',
 			'tt_news',
 			'uid = '.$tt_news_uid
 		);
 		return $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
	}

	/**
	 * checks whether the current tt_news record is a blog post
	 *
	 * @param	integer		tt_news uid to fetch the record in case the available information is not sufficent
	 * @param	array		tt_news record or parts of it
	 * @return	boolean
	 */
	function isBlogPost($id, $tt_news = '') {
		$result = false;
		if(isset($tt_news['type']) && $tt_news['type'] == 3) {
			$result = true;
		} elseif(!isset($tt_news['type']) && $this->status == 'update') {
			$tt_news = $this->getCurrentPost($id);
			$tt_news['type'] == 3 ? $result = true : $result = false;
		}

		return $result;
	}

	/**
	 * explicitly clears cache for the blog page as it is not updating sometimes
	 *
	 * @return	void
	 */
	function clearPageCache() {
		//TODO put this in a class timtab_lib
		$tce = t3lib_div::makeInstance('t3lib_TCEmain');
		$tce->admin = 1;

//		$clearCachePages = split(',', $this->conf['clearPageCacheOnUpdate']);
		$clearCachePages = split(',', 1);	
		foreach($clearCachePages as $page) {
			$tce->clear_cacheCmd($page);
		}
//		$tce->clear_cacheCmd('all');
		$tce->admin = 0;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/timtab/class.tx_timtab_be.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/timtab/class.tx_timtab_be.php']);
}

?>