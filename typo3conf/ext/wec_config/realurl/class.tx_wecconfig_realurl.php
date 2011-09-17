<?php

/***************************************************************
* Copyright notice
*
* (c) 2007-2008 Christian Technology Ministries International Inc.
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


/**
* RealURL autoconfiguration class.
*
* @author Web-Empowered Church <developer@webempoweredchurch.org>
* @package TYPO3
* @subpackage tx_wecconfig
*/
class tx_wecconfig_realurl {
	
	var $isExtLoaded = array();
	
	/**
	 * Main hook function.  Generates an entire RealURL configuration.
	 *
	 * @param		array		Main parameters.  Typically, 'config' is the
	 *							existing RealURL configuration thas has been
	 *							generated to this point and 'extKey' is unique
	 *							that this hook used when it was registered.
	 */
	function addRealURLConfig(&$params, $parentObj) {
		$config = &$params['config'];
		$extKey = &$params['extKey'];

		/* If there's no rootpage_id set from a domain record, pick the first page in the tree */
		if(!isset($config['pagePath']['rootpage_id'])) {
			$pages = $parentObj->db->exec_SELECTgetRows('uid', 'pages', 'pid=0 AND hidden=0 AND deleted=0','', 'sorting ASC');
			if(count($pages) > 0) {
				$config['pagePath']['rootpage_id'] = $pages[0]['uid'];
			}
		}
		
		/* Automatically update URLs and create temporary redirects that will last 14 days. */
		$config['pagePath']['autoUpdatePathCache'] = true;		
		$config['pagePath']['expireDays'] = 14;
		
		/* If the rootpage is 61, add the default WEC fixedPostVars */
		if($config['pagePath']['rootpage_id'] == 61) {
			$config['fixedPostVars'] = $this->addFixedPostVars();
		}
		
		if(!is_array($config['postVarSets']['_DEFAULT'])) {
			$config['postVarSets']['_DEFAULT'] = array();
		}
		$config['postVarSets']['_DEFAULT'] = array_merge($config['postVarSets']['_DEFAULT'], $this->addPostVarSets());
		
		/* Add config for filenames */
		$config['fileName']['index'] = array_merge((array) $config['fileName']['index'], $this->addFilenameConfig());
		
		
		return $config;
	}
	
	/**
	 * Adds the fixedPostVars (specific to a page) to the RealURL config.
	 *
	 * @return		array		RealURL configuration element.
	 */
	function addFixedPostVars() {
		$fixedPostVars = array();

		if($this->isExtLoaded('timtab')) {
			/* Blog Posts */
			$fixedPostVars['82'] = array(
				$this->addTable('tx_ttnews[tt_news]', 'tt_news', 'title')
			);
		}
		
		if($this->isExtLoaded('tt_news')) {
			/* News Articles */
			$fixedPostVars['40'] = array(
				$this->addTable('tx_ttnews[tt_news]', 'tt_news', 'title')
			);
		}
		
		if($this->isExtLoaded('wec_connector')) {
			/* Prayer Connector */
			$fixedPostVars['74'] = array(
				$this->addValueMap('tx_wecconnector[sub]', array('subscribe' => 1,  'unsubscribe' => 2)),
			);
		}
		
		if($this->isExtLoaded('wec_devo')) {
			/* Devotional Journal */
			$fixedPostVars['79'] = array(
				$this->addTable('tx_wecdevo[section]', 'tx_wecdevo_section', 'name', false, ''),
				$this->addSimple('tx_wecdevo[show_date]'),
				$this->addValueMap('tx_wecdevo[txtpg]', array('single_page' => 1, 'multiple_pages' => 2, 'scrolling' => 3)),
				$this->addValueMap('tx_wecdevo[txtsz]', array('small_text' => 1, 'medium_text' => 2, 'large_text' => 3))
			);
		}
		
		if($this->isExtLoaded('wec_staffdirectory')) {
			/* Staff Directory */
			$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['wec_staffdirectory']);
			if ($confArr['useFEUsers']) {
				$staffRealURLConfig = $this->addSimple('tx_wecstaffdirectory_pi1[curstaff]');
			} else {
				$staffRealURLConfig = $this->addTable('tx_wecstaffdirectory_pi1[curstaff]', 'tx_wecstaffdirectory_info', 'full_name');
			}
			$fixedPostVars['62'] = array(
				$staffRealURLConfig
			);
		}
	
		if($this->isExtLoaded('wec_sermons')) {
			/* Sermons */
			$fixedPostVars['78'] = array(
				$this->addValueMap('tx_wecsermons_pi1[recordType]', array(
					'sermons' => 'tx_wecsermons_sermons',
					'series' => 'tx_wecsermons_series',
					'topics' => 'tx_wecsermons_topics',
					'speakers' =>'tx_wecsermons_speakers',
					'seasons' => 'tx_wecsermons_seasons',
					'resources' => 'tx_wecsermons_resources'
				)),
				$this->addTable('tx_wecsermons_pi1[showUid]', 'tx_wecsermons_sermons', 'title', 'tx_wecsermons_sermons'),
				$this->addTable('tx_wecsermons_pi1[showUid]', 'tx_wecsermons_series', 'title', 'tx_wecsermons_series'),
				$this->addTable('tx_wecsermons_pi1[showUid]', 'tx_wecsermons_speakers', 'fullname', 'tx_wecsermons_speakers'),
				$this->addTable('tx_wecsermons_pi1[showUid]', 'tx_wecsermons_topics', 'title', 'tx_wecsermons_topics'),
				$this->addTable('tx_wecsermons_pi1[showUid]', 'tx_wecsermons_seasons', 'title', 'tx_wecsermons_seasons'),
				$this->addTable('tx_wecsermons_pi1[showUid]', 'tx_wecsermons_resources', 'title', 'tx_wecsermons_resources'),
				$this->addSimple('tx_wecsermons_pi1[sermonUid]', 'bypass')
			);
		}
		
		if($this->isExtLoaded('mm_forum')) {
			$fixedPostVars['73'] = array(
				$this->addValueMap('tx_mmforum_pi1[action]', 
									array(
										'topic' => 'open_topic',
										'topics' => 'list_topic',
										'posts' => 'list_post',
										'user' => 'forum_view_profil',
										'reply' => 'new_post',
										'open' => 'new_topic',
										'report' => 'post_alert',
										'edit' => 'post_edit',
										'delete' => 'post_del',
										'all_posts' => 'post_history',
										'unanswered' => 'list_unans',
										'unread' => 'list_unread',
										'all_read' => 'reset_read',
										'subscribe' => 'set_havealook',
										'unsubscribe' => 'del_havealook',
										'favorite' => 'set_favorite',
										'no_favorite' => 'del_favorite',
										'prefix' => 'list_prefix',
										'attachments' => 'get_attachment',
									)),
				$this->addTable('tx_mmforum_pi1[fid]', 'tx_mmforum_forums', 'forum_name'),
				$this->addTable('tx_mmforum_pi1[tid]', 'tx_mmforum_topics', 'topic_title'),
				$this->addSimple('tx_mmforum_pi1[pid]'),
				$this->addSimple('tx_mmforum_pi1[page]'),
				$this->addTable('tx_mmforum_pi1[user_id]', 'fe_users', 'username')
			);
		}
		
		return $fixedPostVars;
	}
	
	/**
	 * Adds the postVarSets (not specific to a page) to the RealURL config.
	 *
	 * @return		array		RealURL configuration element.
	 */
	function addPostVarSets() {
		$postVarSets = array();
		
		if($this->isExtLoaded('sr_feuser_register')) {
			/* Registration confirmation */
			$postVarSets['user'] = array(
				$this->addSimple('tx_srfeuserregister_pi1[regHash]')
			);
			
			/* General account actions */
			$postVarSets['cmd'] = array(
				$this->addSimple('tx_srfeuserregister_pi1[cmd]')
			);
		}
		
		
		if($this->isExtLoaded('felogin')) {
			/* Forgot password link */
			$postVarSets['forgot_password'] = $this->addSingle(array('tx_felogin_pi1[forgot]' => 1));
		}
		
		if($this->isExtLoaded('tt_news')) {
			/* TIMTAB / tt_news page browser */
			$postVarSets['browse'] = array(
				$this->addSimple('tx_ttnews[pointer]')
			);
			
			/* TIMTAB / tt_news archive */
			$postVarSets['archive'] = array(
				$this->addSimple('tx_ttnews[year]'),
				$this->addSimple('tx_ttnews[month]')
			);

			/* TIMTAB / tt_news categories */
			$postVarSets['category'] = array(
				$this->addTable('tx_ttnews[cat]', 'tt_news_cat')
			);
		}
		
		if($this->isExtLoaded('timtab')) {
			/* TIMTAB XML-RPC endpoint */
			$postVarSets['xmlrpc'] = $this->addSingle(array('type' => 200));
		
			/* TIMTAB trackbacks */
			$postVarSets['trackback'] = $this->addSingle(array('tx_timtab_pi1[trackback]' => 1));
		}

		if($this->isExtLoaded('chc_forum')) {
			/* CHC Forum */
			$postVarSets['forum'] = array(
				$this->addSimple('view'),
				$this->addTable('cat_uid', 'tx_chcforum_category', 'cat_title'),
				$this->addTable('conf_uid', 'tx_chcforum_conference', 'conference_name'),
				$this->addTable('thread_uid', 'tx_chcforum_thread', 'thread_subject'),
				$this->addTable('post_uid', 'tx_chcforum_post', 'post_subject')
			);
		}
		
		if($this->isExtLoaded('wec_servant')) {
			/* Servant */
			$postVarSets['view'] = array(
				$this->addTable('tx_wecservant[single]', 'tx_wecservant_minopp', 'name')
			);
			$postVarSets['contact'] = array(
				$this->addTable('tx_wecservant[showinterest]', 'tx_wecservant_minopp', 'name')
			);
		}
		
		if($this->isExtLoaded('wec_connector')) {
			/* Prayer Connector */
			$postVarSets['respond'] = array(
				$this->addTable('tx_wecconnector[respond]', 'tx_wecconnector_entries', 'subject')
			);
			/* Prayer Connector */
			$postVarSets['edit'] = array(
				$this->addTable('tx_wecconnector[modify]', 'tx_wecconnector_entries', 'subject')
			);
			$postVarSets['delete'] = array(
				$this->addTable('tx_wecconnector[del]', 'tx_wecconnector_entries', 'subject')
			);
			$postVarSets['hide'] = array(
				$this->addTable('tx_wecconnector[hide]', 'tx_wecconnector_entries', 'subject')
			);
		}
		
		
		/* Generic parameter for uncached pages */
		$postVarSets['not_cached'] = $this->addSingle(array('no_cache' => 1));
		
		return $postVarSets;
	}
	
	/**
	 * Adds the filename mappings to the RealURL configration.
	 * 
	 * @return		array		RealURL config element.
	 */
	function addFilenameConfig() {
		return array (
			'index.xml' => array('keyValues' => array('type' => 100)),
			'podcast.xml' => array('keyValues' => array('type' => 222)),
			'vodcast.xml' => array('keyValues' => array('type' => 223)),
			'print.html' => array('keyValues' => array('type' => 98))
		);
	}
	
	
	/*************************************************************************
	 *
	 * Helper functions for generating common RealURL config elements.
	 *
	 ************************************************************************/
	
	/**
	 * Adds a RealURL config element for simple GET variables.
	 *
	 *	array( 'GETvar' => 'tx_calendar_pi1[f1]' ),
	 *
	 * @param		string		The GET variable.
	 * @return		array		RealURL config element.
	 */	
	function addSimple($key, $noMatch = NULL) {
		$config = array( 'GETvar' => $key );
		if ($noMatch) {
			$config['noMatch'] = $noMatch;
		}
	}
	
	
	/**
	 * Adds RealURL config element for table lookups.
	 *
	 *	array(
	 *		'GETvar'      => 'tx_ttnews[tt_news]',
	 *		'lookUpTable' => array(
	 *			'table'               => 'tt_news',
	 *			'id_field'            => 'uid',
	 *			'alias_field'         => 'title',
	 *			'addWhereClause'      => ' AND NOT deleted',
	 *			'useUniqueCache'      => 1,
	 *			'useUniqueCache_conf' => array(
	 *				'strtolower'     => 1,
	 *				'spaceCharacter' => '_',
	 *			)						
	 *		)
	 *	)
	 *
	 * @param		string		The GET variable.
	 * @param		string		The name of the table.
	 * @param		string		The field in the table to be used in the URL.
	 * @param		string		Previous GET variable that must be present for
	 *							this rule to be evaluated.
	 * @param		boolean		True/False whether this value is optional or not.
	 * @return		array		RealURL config element.
	 */
	function addTable($key, $table, $aliasField='title', $condForPrevious=FALSE, $where=' AND NOT deleted', $isOptional=TRUE) {
		$configArray = array();
		
		if($condForPrevious) {
			$configArray['cond'] = array ('prevValueInList' => $condForPrevious);
		}

		$configArray['GETvar'] = $key;
		$configArray['lookUpTable'] = array(
			'table' => $table,
			'id_field' => 'uid',
			'alias_field' => $aliasField,
			'addWhereClause' => $where,
			'useUniqueCache' => 1,
			'useUniqueCache_conf' => array(
				'strtolower' => 1,
				'spaceCharacter' => '-',
				'encodeTitle_userProc' => 'EXT:wec_config/realurl/class.tx_wecconfig_cleantitle.php:&tx_wecconfig_cleantitle->cleanTitle'
			),
			'autoUpdate' => true
		);
		$configArray['optional'] = $isOptional;

		return $configArray;
	}
	
	/**
	 * Adds RealURL config element for value map.
	 *	array(
	 *		'GETvar' => 'sub',
	 *		'valueMap' => array(
	 *			'subscribe' => '1',
	 *			'unsubscribe' => '2',
	 *		),
	 *		'noMatch' => 'bypass',
	 *	)
	 *
	 * @param		string		The GET variable.
	 * @param		array		Associative array with label and value.
	 * @param		string		noMatch behavior.
	 * @return		array		RealURL config element.
	 */
	function addValueMap($key, $valueMapArray, $noMatch='bypass') {
		$configArray = array();
		$configArray['GETvar'] = $key;
		
		if(is_array($valueMapArray)) {
			foreach($valueMapArray as $key => $value) {
				$configArray['valueMap'][$key] = $value;
			}
		}
		
		$configArray['noMatch'] = $noMatch;
		return $configArray;
	}
	
	/**
	 * Adds RealURL config element for single type.
	 *
	 *	array(
	 *		'type' => 'single',
	 *		'keyValues' => array(
	 *			'tx_newloginbox_pi1[forgot]' => 1,
	 *		)
	 *	)
	 *
	 * @param		array		Associative array of GET variables and values. 
	 *							All values must be matched.
	 * @return		array		RealURL config element.
	 */
	function addSingle($keyValueArray) {
		$configArray = array();
		$configArray['type'] = 'single';
		
		if(is_array($keyValueArray)) {
			foreach($keyValueArray as $key => $value) {
				$configArray['keyValues'][$key] = $value;
			}
		}
		
		return $configArray;
	}

	/**
	 * Checks if a particular extension key is loaded.  Acts as a wrapper for
	 * t3lib_extMgm::isLoaded($key).
	 *
	 * @param		string		The extension key.
	 * @return		boolean		Whether or not the key is loaded.
	 */
	function isExtLoaded($key) {
		if(!array_key_exists($key, $this->isExtLoaded)) {
			$this->isExtLoaded[$key] = t3lib_extMgm::isLoaded($key);
		}
		return $this->isExtLoaded[$key];
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wec_config/realurl/class.tx_wecconfig_realurl.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wec_config/realurl/class.tx_wecconfig_realurl.php']);
}
?>
