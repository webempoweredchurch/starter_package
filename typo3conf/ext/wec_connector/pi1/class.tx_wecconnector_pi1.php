<?php
/***********************************************************************
* Copyright notice
*
* (c) 2005-2010 Christian Technology Ministries International Inc.
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
*************************************************************************/

require_once(PATH_tslib.'class.tslib_pibase.php');
require_once(PATH_t3lib.'class.t3lib_basicfilefunc.php');

/**
 * Plugin 'WEC Connector' for the 'wec_connector' extension.
 *
 * @author Web-Empowered Church Team <devteam(at)webempoweredchurch.org>
 * @package TYPO3
 * @subpackage wec_connector
 *
 * DESCRIPTION:
 * ============
 * The WEC Connector extension implements a connector community.
 * A connector community is designed to help people that have a need or
 * service find people who can address that need or use that service.
 * Anyone can publicly post a need (like a prayer request) or service (like
 * a job opening), but people who want to respond do not reply publicly.
 * Instead, their reply is e-mailed back to the originator and the reply does
 * not appear on the website. If the need is fulfilled or the service is used
 * up, then the entire post can be removed by the originator.
 *
 * To help in monitoring for new posts to the connector, people can subscribe
 * to a connector and receive any new posts. In addition, each connector can
 * be moderated by one or more users and they can quickly edit or remove
 * entries if needed.
 *
 * This extension offers several configurable options to address various
 * ministry needs including prayer requests, help board, classified ads,
 * job listings, or a business directory.
 *
 */
class tx_wecconnector_pi1 extends tslib_pibase {
	var $prefixId 		= 'tx_wecconnector_pi1';	// Same as class name
	var $scriptRelPath 	= 'pi1/class.tx_wecconnector_pi1.php'; // Path to this script relative to the extension dir.
	var $extKey 		= 'wec_connector'; // The extension key.
	var $dataEntryTable = 'tx_wecconnector_entries';
	var $dataGroupTable = 'tx_wecconnector_group';
	var $dataCategoryTable 	= 'tx_wecconnector_cat';

	var $id;	// Current page id (for quick & easy reference)
	var $cObj;	// The backReference to the mother cObj object set at call time

	var $db_fields;	// database fields (for processing)
	var $action;	// action to take => list, response, subscribe, etc.
	var $respondMsgNum; // respond to message #
	var $modifyMsgNum; // modify message #
	var $deleteMsgNum; // delete message #
	var $hideMsgNum; // hide message #
	var $postvars;	// posted variables
	var $filledInVars; // filled in variables (for editting existing)
	var $submitFormText; // text to share if successful or not on submit form
	var $curCategory; // current category
	var $isSubscribed; // if is subscribed to this group
	var $isAdministrator; // if current user is an administrator
	var $canReply; // if current user can respond to a message
	var $categoryList; // category list
	var $categoryImageList; // category image list
	var $categoryCount; // # of categories
	var $submitErrors; // if submit form errors
	var $entrySortOrder; // sort order for entries
	var $freeCap = 0;	// image captcha (sr_freecap) set if loaded
	var $easyCaptcha = false;	// image captcha (captcha) set if loaded
	var $adminList;		// stores admin list
	var $viewItemID;	// current single view item
	var $viewable_cats;	// viewable categories
	var $charset = 'iso-8859-1';	// charset for sending email
	var $spamMessage;	// admin spam message to send (so know why was marked as spam)
	var $entryTemplate = '###ENTRY###'; // default entry template to use
	
	// USER...
	var $userID;	// current UID of user logged in (0 = no user logged in)
	var $userName;	// user account name for logged in user
	var $userFirstName; // first name for logged in user
	var $userLastLoginDate; // last login date for user

	/**
	* Init: Initialize the extension
	*
	* @param	array		$conf  the TypoScript configuration
	* @return	void		No return value needed.
	*/
	function init($conf) {
		$this->local_cObj = t3lib_div::makeInstance('tslib_cObj'); // Local cObj.
		$this->conf = $conf;			// TypoScript configuration

		//  $GLOBALS['TSFE']->set_no_cache();
		$this->pi_USER_INT_obj = 1;		// configure so caching not expected

		// ------------------------------------------------------------
		// Initialize vars, structures, arrays, etc.
		// ------------------------------------------------------------
		$this->pi_setPiVarDefaults();	// GetPut-parameter configuration
		$this->pi_initPIflexForm();		// Initialize the FlexForms array
		$this->pi_loadLL();				// localized language variables

		// ------------------------------------------------------------
		// SETUP Defaults
		// ------------------------------------------------------------
		$this->curCategory = 0;
		$this->action = 0;
		$this->isSubscribed = false;
		$this->modifyMsgNum = 0;
		$this->respondMsgNum = 0;
		$this->isAdministrator = 0;
		$this->submitErrors = 0;
		$this->db_fields = array('name', 'subject', 'email', 'message', 'phone', 'location', 'address', 'city', 'state', 'zipcode', 'country', 'website_url', 'business_name', 'contact_name', 'email2', 'category', 'image', 'moderationQueue');
		$this->db_showFields = array('name', 'subject', 'email', 'message', 'phone', 'location', 'address', 'city', 'state', 'zipcode', 'country', 'website_url', 'business_name', 'contact_name', 'email2', 'category', 'image');

		$this->id = $GLOBALS['TSFE']->id; // current page id

		// Set USER Info
		if ($GLOBALS['TSFE']->loginUser) {
			$this->userID = $GLOBALS['TSFE']->fe_user->user['uid'];
			$this->userName = $GLOBALS['TSFE']->fe_user->user['username'];
			$this->userFirstName = $GLOBALS['TSFE']->fe_user->user['first_name'];
			if (strlen($this->userFirstName) < 1) $this->userFirstName = $this->userName;
			$this->userGroups = $GLOBALS['TSFE']->fe_user->user['usergroup'];
		} else {
			// no user logged in...
			$this->userID = 0;
			$this->userName = '';
			$this->userFirstname = '';
		}

		// Set the storage PID (currently supports one page...could add recursive or multiple pages later)
		//-------------------------------------------------------------
		$this->config['storagePID'] = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'storagePID', 'sDEF');
		if ($this->config['storagePID'])
			$this->pid_list = $this->config['storagePID']; // can specify startingPoint in flexform
		else if ($this->conf['pid_list'])
			$this->pid_list = $this->conf['pid_list']; // or specify in TypoScript
		else
			$this->pid_list = $GLOBALS['TSFE']->id;	// the default is the current page

		// ------------------------------------------------------------
		// Load in all flexform values
		// ------------------------------------------------------------
		// this will load the connector info from flexform and set all the fields

		// MAIN PAGE
		$templateflex_file = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'template_file', 'sDEF');
		$this->templateCode = $this->cObj->fileResource($templateflex_file ? "uploads/tx_wecconnector/".$templateflex_file : $this->conf['templateFile']);

		$flexArray = array(
			'sDEF		|connector_name, entry_description, entries_description, entry_subject, entry_button, category_selection',
			's_display	|entry_look, message_length, field_length, days_to_keep, display_per_page, websiteURL_popup',
			's_control	|do_moderate, do_moderate_response, require_login_for_posting, require_login_for_reply, usergroups_only_reply,who_can_edit, require_login_for_subscribing, can_subscribe, update_subscribe_btn',
			's_antispam |spam_administrator, html_tags_allowed, use_captcha, use_text_captcha, numlinks_allowed, filter_wordlist, filter_word_handling, spam_check_responses',
			's_fields	|display_fields, required_fields, required_response_fields',
			's_administrator|contact_name, contact_email, administrator_group, administrator_usergroup, email_admin_posts, notify_email',
			's_preview	|is_preview, preview_backPID, num_preview_items, preview_length',
			's_text		|response_title, subscribe_header, subscriber_emailHeader, subscriber_emailFooter, post_instructions_text'
		);
		// go through all flexform values and set the config[]
		foreach ($flexArray as $flexVals) {
			$flexArray = t3lib_div::trimExplode('|', $flexVals);
			$flexSheet = $flexArray[0];
			$flexVars = t3lib_div::trimExplode(',', $flexArray[1]);
			foreach ($flexVars as $flexConfig) {
				$this->config[$flexConfig] = $this->getConfigVal($this, $flexConfig, $flexSheet);
			}
		}
		// convert for backwards compatibility
		$this->config['name'] = $this->config['connector_name'];

		// make viewable categories an array
		$this->viewable_cats = 0;
		if ($this->config['category_selection']) {
			$this->viewable_cats = t3lib_div::trimExplode(',',$this->config['category_selection']);
		}
		// make display and required fields an array. If not set, then provide defaults
		if (!empty($this->config['display_fields'])) {
			$this->config['display_fields'] = t3lib_div::trimExplode(',', $this->config['display_fields']);
		}
		else  { // set defaults
			$this->config['display_fields'] = array('name','subject','message','email','category');
		}
		if (!empty($this->config['required_fields'])) {
			$this->config['required_fields'] = t3lib_div::trimExplode(',', $this->config['required_fields']);
		}
		else { // set defaults
			if (in_array('message',$this->config['display_fields']))
				$this->config['required_fields'] = array('message');
		}
		if (!empty($this->config['required_response_fields'])) {
			$this->config['required_response_fields'] = t3lib_div::trimExplode(',', $this->config['required_response_fields']);
		}
		else { // set defaults
			$this->config['required_response_fields'] = array('message');
		}
		
		// if multiple emails and separated by ;, then separate by comma
		if ($this->config['notify_email'] && strpos($this->config['notify_email'],';'))
			$this->config['notify_email'] = str_replace(";",",",$this->config['notify_email']);

		// SET if moderator
		//---------------------------------------------------
		// check if this user is in admin userlist
		if ($this->userID && ($admins = $this->config['administrator_group'])) {
			$adminList = t3lib_div::trimExplode(',', $admins);
			foreach ($adminList as $thisAdmin) {
				if (($thisAdmin == $this->userID) || ($thisAdmin == $this->userName)) {
					$this->isAdministrator = 1;
					break;
				}
			}
		}
		
		// check if this user belongs to admin usergroup or in reply UserGroup
		$adminUG = $this->config['administrator_usergroup'];
		$replyUG = $this->config['usergroups_only_reply'];
		if ($this->userGroups && ($adminUG || $replyUG)) {
			// put my groups in an array
			$myGroupArray = t3lib_div::trimExplode(',', $this->userGroups);
			
			// determine if admin, based on usergroup in
			if ($adminUG) {
				$adminGroupArray = t3lib_div::trimExplode(',', $adminUG);
				foreach ($adminGroupArray as $adminGrp) {
					if (in_array($adminGrp,$myGroupArray)) {
						$this->isAdministrator = 1;
						break;
					}
				}
			}
			// determine if can reply based on usergroup in
			if ($replyUG) {
				$replyGroupArray = t3lib_div::trimExplode(',', $replyUG);
				foreach ($replyGroupArray as $replyGrp) {
					if (in_array($replyGrp,$myGroupArray)) {
						$this->canReply = 1;
						break;
					}
				}
			}
		}

		// determine if can respond/reply to a message
		if ($this->isAdministrator) {
			$this->canReply = true;
		}
		if (!$replyUG) {
			$this->canReply = (!$this->config['require_login_for_reply'] || $this->userID);
		}

		// SET subscriber
		//---------------------------------------------------
		if ($this->userID) {
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', $this->dataGroupTable, 'pid IN('.$this->pid_list.') AND user_uid='.intval($this->userID), '');
			if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))
				$this->isSubscribed = true;
		}

		// add captcha if loaded
		if ($this->config['use_captcha'] && t3lib_extMgm::isLoaded('sr_freecap')) {
			require_once(t3lib_extMgm::extPath('sr_freecap').'pi2/class.tx_srfreecap_pi2.php');
			$this->freeCap = t3lib_div::makeInstance('tx_srfreecap_pi2');
		}
		else if ($this->config['use_captcha'] && t3lib_extMgm::isLoaded('captcha')) {
			$this->easyCaptcha = true;
		}

		// Set sort order for entries
		$this->entrySortOrder = $this->conf['sortOrder'];
		if ($this->entrySortOrder == "date_added" || !$this->entrySortOrder || $this->entrySortOrder == '')
			$this->entrySortOrder = 'post_date';
		if ($this->entrySortOrder == 'post_date')
			$this->entrySortOrder .= ' DESC';
		else
			$this->entrySortOrder .= ' ASC';

		// LOAD IN CATEGORIES
		//---------------------------------------------------
		$where = 'pid IN('.$this->pid_list.') AND deleted=0 AND hidden=0';
		// handle languages
		$lang = ($l = $GLOBALS['TSFE']->sys_language_uid) ? $l : '0,-1';
		$where .= ' AND sys_language_uid IN ('.$lang.') ';
		$orderBy = 'sort_order';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', $this->dataCategoryTable, $where, '', $orderBy);
		if (mysql_error()) t3lib_div::debug(array(mysql_error(), $res));
		$this->categoryCount = 0;
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			// if not in viewable category list, then do not show
			if ($this->viewable_cats && !in_array($row['uid'],$this->viewable_cats))
				continue;

			$this->categoryList[$this->categoryCount]['name'] = $row['title'];
			$this->categoryList[$this->categoryCount]['image'] = $row['image'];
			$this->categoryList[$this->categoryCount]['uid'] = $row['uid'];
			$this->categoryCount++;
		}

		// ----------------------------------------------------------------------------------------
		// HANDLE PASSED-IN VALUES (POST OR GET)
		//
		// ----------------------------------------------------------------------------------------
		$this->postvars = t3lib_div::_GP('tx_wecconnector');

		// SECURITY: make sure the passed in vars are secure
		if (is_array($this->postvars)) {
			foreach ($this->postvars as $pvKey => $pvVal) {
				$this->postvars[$pvKey] = htmlspecialchars($pvVal);
			}
		}

		// handle category
		if (($this->curCategory = $this->postvars['viewcat']) != 0) {
			// check for valid category UID
			$found = false;
			for ($i = 0; $i < $this->categoryCount; $i++) {
				if ($this->curCategory == $this->categoryList[$i]['uid'])
					$found = true;
			}
			if (!$found)
				$this->curCategory = 0;
		}
		if (($thisSubAction = $this->postvars['sub']) != 0) {
			// if coming from unsubscribing link in email
			if (($thisSubAction == 2) && ($thisSubEmail = $this->postvars['email'])) {
				if (!$this->unsubscribeFromGroup($thisSubEmail))
					$this->action = 'subscribe';
			}
			// make sure can subscribe and then allow
			else if ($this->config['can_subscribe'] && (!$this->config['require_login_for_subscribing'] || $this->isAdministrator || $this->userID))
				$this->action = 'subscribe';
		}
		if ($this->postvars['moderate'] != 0) {
			if ($this->isAdministrator) {
				$this->action = 'moderate';
			}
			// if not logged in, give a warning
			else if (!$this->userID) {
				$this->submitFormText = $this->pi_getLL('login_to_moderate', 'You need to be logged in to moderate. ');
				if ((int)$this->conf['loginPID']) {
					$this->submitFormText .= '<a class="button" href="' . $this->pi_getPageLink($this->conf['loginPID']) . '"> ' . $this->pi_getLL('goto_login', 'Goto Login') . '</a>';
				}
			}
		}
		if ($this->postvars['admin'] != 0) {
			if ($this->isAdministrator)
				$this->action = 'admin';
		}

		if (($showMsg = $this->postvars['msg']) != 0) {
			switch ($showMsg) {
				case 1:	$this->submitFormText = $this->pi_getLL('message_added', 'Your message was added.'); break;
				case 2:	$this->submitFormText = $this->pi_getLL('message_moderated', 'This area is moderated -- you will not see your message until it is approved.'); break;
				case 3:	$this->submitFormText = $this->pi_getLL('post_bad_words', 'Your post had inappropriate words in it and was discarded.'); break;
				case 4:	$this->submitFormText = $this->pi_getLL('post_duplicate', 'Your post has already been added. You do not need to send it again.'); break;
				case 5:	$this->submitFormText = $this->pi_getLL('delete_success', 'You deleted the message.'); break;
				case 6:	$this->submitFormText = $this->pi_getLL('delete_failure', 'There was a problem with deleting the message.'); break;
				case 7: $this->submitFormText = $this->pi_getLL('message_updated', 'Your message has been updated.'); break;
				case 8: $this->submitFormText = $this->pi_getLL('hide_failure', 'There was a problem with hiding the message.'); break;
				case 9:	$this->submitFormText = $this->pi_getLL('hide_success', 'Your message has been hidden.'); break;
				case 9:	$this->submitFormText = $this->pi_getLL('hide_success', 'Your message is now shown.'); break;
			}
		}
		
		if (($this->respondMsgNum = $this->postvars['respond']) && $this->canReply)
			$this->action = 'respond';
		if ($this->modifyMsgNum = $this->postvars['modify'])
			$this->action = 'modify';
		if ($this->deleteMsgNum = $this->postvars['del'])
			$this->deleteMessage($this->deleteMsgNum);
		if ($this->hideMsgNum = $this->postvars['hide'])
			$this->hideMessage($this->hideMsgNum);
		if ($this->hideMsgNum = $this->postvars['unhide'])
			$this->hideMessage($this->hideMsgNum, false);

		if ($this->postvars['submitresponse']) {
			$this->respondToRequest($this->postvars['name'], $this->postvars['email'], $this->postvars['subject'], $this->postvars['message'], $this->postvars['msgid']);
			if ($this->submitErrors) {
				$this->action = 'respond';
				$this->respondMsgNum = $this->postvars['msgid'];
			}
		}

		if ($this->postvars['submitsubscribe'])
			if (!$this->subscribeToGroup($this->postvars['name'], $this->postvars['email'])) {
			// if unsuccessful, then go back to form
			$this->action = 'subscribe';
		}

		if ($this->postvars['submitunsubscribe'])
			if (!$this->unsubscribeFromGroup($this->postvars['email'])) {
			// if unsuccessful, then go back to form
			$this->action = 'subscribe';
		}
		if (($numToMod = $this->postvars['processModerated']) != 0)
			$this->processModerated(t3lib_div::_POST(),$numToMod);

		// Show the view button if it is defined
		if ($this->viewItemID = (int) $this->postvars['single']) {
			$this->action = 'single';
		}

		// Do preview if set preview page
		if ($this->config['is_preview']) {
			$this->action = 'preview';
		}

		// RSS FEED?
		//----------------------------------------------------------
		if (($GLOBALS["TSFE"]->type == 224) && ($this->conf['rssFeedOn'] == 1)) {  // RSS FEED
			$this->action = 'rss';
		}
		// allow RSS feed to be "discovered" by feed readers
		// if set rssFeedOn and NOT doing an RSS feed
		if (($this->conf['rssFeedOn'] == 1) && (($rssLink = $this->conf['xml.']['rss.']['link'])) || strcmp((string) $this->action,'rss')) {
			if (strpos($rssLink,'http:') === FALSE) {
				$urlParam['type'] = 224;
				$urlParam['sp'] = $this->pid_list;
				$rssURL = $this->getAbsoluteURL($this->id,$urlParam,TRUE);
			}
			else {
				$rssURL = $rssLink;
			}
			$rssTitle =  $this->conf['xml.']['rss.']['channel_title'] ? $this->conf['xml.']['rss.']['channel_title'] : ($this->config['title'] ? $this->config['title'] : 'RSS 2.0');
			$GLOBALS['TSFE']->additionalHeaderData['tx_wecconnector'] = '<link rel="alternate" type="application/rss+xml" title="'.$rssTitle.'" href="'.$rssURL.'" />';
		}
				
		// Set CSS file(s), if exist
		if (t3lib_extMgm::isLoaded('wec_styles') && ($this->conf['isOldTemplate'] == 0)) {
			require_once(t3lib_extMgm::extPath('wec_styles') . 'class.tx_wecstyles_lib.php');
			$wecStylesLib = t3lib_div::makeInstance('tx_wecstyles_lib');
			$wecStylesLib->includePluginCSS();
		}
		else if ($baseCSSFile = $this->conf['baseCSSFile']) {
			$fileList = array(t3lib_div::getFileAbsFileName($baseCSSFile));
			$fileList = t3lib_div::removePrefixPathFromList($fileList,PATH_site);
			$GLOBALS['TSFE']->additionalHeaderData['wecconnector_basecss'] = '<link type="text/css" rel="stylesheet" href="'.$fileList[0].'" />';
		}
		if ($cssFile = $this->conf['cssFile']) {
			$fileList = array(t3lib_div::getFileAbsFileName($cssFile));
			$fileList = t3lib_div::removePrefixPathFromList($fileList,PATH_site);
			$GLOBALS['TSFE']->additionalHeaderData['wecconnector_css'] = '<link type="text/css" rel="stylesheet" href="'.$fileList[0].'" />';
		}

		// include extra CSS file for post entries look (if set)
		if ($this->config['entry_look'] && ($this->conf['isOldTemplate'] == 0)) {
			$entryCSSFile = 'EXT:wec_connector/template/wecconnector-' . $this->config['entry_look'] . '.css';
			$fileList = array(t3lib_div::getFileAbsFileName($entryCSSFile));
			$fileList = t3lib_div::removePrefixPathFromList($fileList,PATH_site);			
			$GLOBALS['TSFE']->additionalHeaderData['wecconnector_cssplus'] = '<link type="text/css" rel="stylesheet" href="'.$fileList[0].'" />';

			// change entry template for business directory
			if (($this->config['entry_look']) == 'businessdir') {
				$this->entryTemplate = '###ENTRY2###';
			}
		}
	}

	/**
	* Main function: calls the init() function and decides by the given actions which functions to display content
	*
	* @param	string		$content : function output is added to this
	* @param	array		$conf : TypoScript configuration array
	* @return	string		$content: complete content generated by the plugin
	*/
	function main($content, $conf) {
		$this->init($conf);
	    if ($this->conf['isLoaded'] != 'yes') {
		  	t3lib_div::sysLog('Static template not set for ' . $this->extKey . ' on page ID: ' . $GLOBALS['TSFE']->id . ' url: ' . $this->getAbsoluteURL($this->id,t3lib_div::_GET()), $this->extKey, 3); 
	      	return $this->pi_getLL('errorIncludeStatic');
		}
		$this->action = (string) $this->action;
		switch ($this->action) {
			case 'respond':	$content = $this->respondForm($this->respondMsgNum); break;

			case 'modify':  $content = ($modContent = $this->modifyForm($this->modifyMsgNum)) ? $modContent : $this->displayMain(); break;

			case 'moderate':$content = $this->moderateMessages(); break;

			case 'admin':	$content = $this->doAdmin(); break;

			case 'subscribe':$content = $this->subscribeForm(); break;

			case 'single':	$content = $this->viewSingle($this->viewItemID); break;

			case 'preview': $content = $this->displayPreview(); break;
			
			case 'rss': $content = $this->displayRSSFeed(); 
						return $content;
						break;

			default:		$content = $this->displayMain();
		}

		return $this->pi_wrapInBaseClass($content);
	}

	/**
	* Displays main content: display handler
	*
	* @return	string		$content: complete content generated by the plugin
	*/
	function displayMain() {
		$templateMainContent = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_MAIN###');
		$subpartArray = array();
		
		$markerArray['###TITLE###'] = $this->config['connector_name'];
		$markerArray['###RESPONSE_MSG###'] = $this->submitFormText;
		$markerArray['###INSTRUCTIONS###'] = $this->config['post_instructions_text'];

		$subscribeBtn = '';
		if ($this->config['can_subscribe'] && (!$this->config['require_login_for_subscribing'] || $this->isAdministrator || $this->userID)) {
			$paramArray['tx_wecconnector']['sub'] = 1;
			// determine if subscribe or unsubscribe
			$btnName = $this->pi_getLL('subscribe_btn', 'Subscribe');
			if ($this->userID && $this->config['update_subscribe_btn']) {
				// see if user is subscribed to this...
				if ($this->checkIfSubscribed($GLOBALS['TSFE']->fe_user->user['email'])) {
					$btnName = $this->pi_getLL('unsubscribe_btn','Unsubscribe');
					$paramArray['tx_wecconnector']['sub'] = 2;
				}
			}
			$subscribeBtn = '<a class="button" href="'.htmlspecialchars($this->pi_getPageLink($this->id, '', $paramArray)) . '">' .
				'<span class="label subscribeIcon">' . $btnName . '</span></a>';
		}
		$markerArray['###SUBSCRIBE_BTN###'] = $subscribeBtn;

		$moderateBtn = '';
		if ($this->isAdministrator && ($this->config['do_moderate'] || ($this->config['spam_check_responses'] == 'moderate'))) {
			$paramArray2['tx_wecconnector']['moderate'] = 1;
			$moderateBtn = '<a class="button" href="'.htmlspecialchars($this->pi_getPageLink($this->id, '', $paramArray2)).'">' . 
				'<span class="label adminIcon">' . $this->pi_getLL('moderate_btn', 'Moderate') . '</span></a>';
		}
		$markerArray['###MODERATE_BTN###'] = $moderateBtn;

		$adminBtn = '';
		if ($this->isAdministrator) {
			$paramArray3['tx_wecconnector']['admin'] = 1;
			$adminBtn = '<a class="button" href="'.htmlspecialchars($this->pi_getPageLink($this->id, '', $paramArray3)).'">' .
				'<span class="label adminIcon">' . $this->pi_getLL('admin_btn', 'Admin').'</span></a>';
		}
		$markerArray['###ADMIN_BTN###'] = $adminBtn;

		if (!strlen($this->config['post_instructions_text'])) 
			$subpartArray['###SHOW_INSTRUCTIONS###'] = '';
		if (!strlen($this->submitFormText))
			$subpartArray['###SHOW_RESPONSE_MSG###'] = '';
		if (!strlen($subscribeBtn) && !strlen($moderateBtn) && !strlen($adminBtn)) 
			$subpartArray['###SHOW_NAV###'] = '';			

		$markerArray['###DISPLAY_ENTRIES###'] = $this->displayRequests($this->curCategory);

		if (!$this->config['require_login_for_posting'] || $this->userID) {
			$markerArray['###ENTRY_FORM###'] = $this->enterNewRequest();
			$markerArray['###ENTRY_FORM_TOGGLEON###'] = $this->toggleEntryForm(1);
			$markerArray['###ENTRY_FORM_TOGGLEOFF###'] = $this->toggleEntryForm(0);
		}
		else {
			$markerArray['###RESPONSE_MSG###'] .= '<br />'.$this->pi_getLL('login_to_post','You must login to post here.');
		}
		if ($this->config['require_login_for_reply'] && !$this->userID) {
			$markerArray['###RESPONSE_MSG###'] .= '<br />'.$this->pi_getLL('login_to_reply','If you want to respond to one of these, you must login.');
		}

		// if errors in form, then force to show form
		if ($this->submitErrors && !$this->postvars['submitresponse']) {
			if (strpos($templateMainContent, '###ENTRY_FORM_TOGGLEOFF'))
				$markerArray['###ENTRY_FORM_TOGGLEOFF###'] = $this->toggleEntryForm(1);
			if (!strpos($templateMainContent, '###ENTRY_FORM')) // if we don't have an entry form, we need to show these errors (should be unused)
			$markerArray['###RESPONSE_MSG###'] = $this->submitFormText;
		}

		// if toggleon/off is in template, then add the ending /div
		if (strpos($templateMainContent, '###ENTRY_FORM_TOGGLEON') || (strpos($templateMainContent, '###ENTRY_FORM_TOGGLEOFF'))) {
			if (!$this->config['require_login_for_posting'] || $this->userID) {
				$markerArray['###ENTRY_FORM###'] .= '</div>';
			}
		}
		$content = $this->cObj->substituteMarkerArrayCached($templateMainContent, $markerArray, $subpartArray, array());
		$content = preg_replace('/###.*?###/', '', $content);

		return $content;
	}

	/**
	* Display Requests: displays all the connector requests that are posted. Supports paging, sorting, and categories.
	*
	* @param	string		$thisCategory : which category to show
	* @return	string		$content: content generated to show the requests
	*/
	function displayRequests($thisCategory) {
		$limit = $this->config['display_per_page'];
		if ($limit <= 0) $limit = 10000;
		if ($this->postvars['pagenum'] > 0) {
			$begin_at = $this->postvars['pagenum'] * $limit;
		} else {
			$begin_at = 0;
		}
		// Load From Database The Requests For This Connector

		// set whereTable if categories exist
		$whereTable = ($this->categoryCount > 0) ? 'A.' : '';
		// start where clause
		$selectWhere = $whereTable.'pid IN (' . $this->pid_list . ') AND ' . $whereTable.'deleted=0  AND ' . $whereTable.'moderationQueue=0 AND ' . $whereTable.'is_response=0 ';

		// if days to keep, select here
		if ($daysToKeep = $this->config['days_to_keep']) {
			$lastDate = mktime(0, 0, 0, date('m'), date('d')-$daysToKeep, date('y'));
			$selectWhere .= ' AND ' . $whereTable.'post_date >=' . $lastDate . ' ';
		}

		// if category set, then select here (should not be)
		if ($thisCategory != 0)
			$selectWhere .= ' AND ' . $whereTable.'category=' . $thisCategory . ' ';
			
		// allow admin to see hidden
		if (!$this->isAdministrator) $selectWhere .= ' AND ' . $whereTable . 'hidden=0';
		// select languages	
		$lang = ($l = $GLOBALS['TSFE']->sys_language_uid) ? $l : '0,-1';
		$selectWhere .= ' AND ' . $whereTable.'sys_language_uid IN (' . $lang . ')';				

		// set order by what have chosen in constants
		$orderByStr = $whereTable . $this->entrySortOrder;
		if ($this->categoryCount > 0) {
			$selectWhere .= ' AND (A.category=B.uid OR A.category=0)';
			$selectWhere .= ' AND B.deleted=0 AND B.hidden=0';
			$selectWhere .= ' AND B.pid IN (' . $this->pid_list . ') ';
			$orderByStr = ' B.sort_order, A.category DESC,' . $orderByStr;
			// build query
			$selectWhat = 'A.*';
			$fromTable = $this->dataEntryTable . ' as A, ' . $this->dataCategoryTable . ' as B';
		} else {
			// build query
			$selectWhat = '*';
			$fromTable = $this->dataEntryTable;
		}
		
		// Execute the request
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($selectWhat, $fromTable, $selectWhere, '', $orderByStr);		
		if (mysql_error()) 
			t3lib_div::debug(array(mysql_error(), $fromTable.' '.$selectWhere.' ORDER BY '.$orderByStr.' LIMIT '.$limit));
		$count = $GLOBALS['TYPO3_DB']->sql_num_rows($res);

		$entriesContent = '';
		$prevCategory = 0;
		$categoryCount = 0;

		// Grab main template for entries
		$templateMainContent = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_MAIN###');
		$templateDisplayContent = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_DISPLAYENTRIES###');

		if ($count > 0) {
			// FIRST, we need to read them all in and categorize them, if necessary
			//---------------------------------------------------------------------
			$i = 0;
			$fieldNameArray = array('name', 'subject', 'email', 'message', 'phone', 'location', 'address', 'city', 'state', 'zipcode', 'country', 'website_url', 'business_name', 'contact_name', 'post_date', 'uid', 'category', 'email2', 'image', 'user_uid','hidden');

			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				// only process ones if no category OR are in right category
				if (($thisCategory == 0) || ($thisCategory == $row['category'])) {
					// load  in all the fields into entry array
					foreach ($fieldNameArray AS $fieldName) {
						$entry[$i][$fieldName] = $this->html_entity_decode(stripslashes($row[$fieldName]));
					}
					$entry[$i]['message'] = str_replace("\n", '<br/>', $entry[$i]['message']);
					$i++;
				}
				if ($thisCategory != $prevCategory) {
					$categoryCount++;
					$prevCategory = $thisCategory;
				}
			}

			// THEN we need to put each entry into the template
			//----------------------------------------------------------------------
			$templateCatDisplayContent = $this->cObj->getSubpart($templateDisplayContent, '###DISPLAY_CATEGORY###');
			$curTemplateEntry = $this->cObj->getSubpart($templateDisplayContent, $this->entryTemplate);
			$curCategory = 0;
			$curCategoryUID = 0;
			$alternating = false;
			for ($k = 0; $k < $count && $k < $begin_at+$limit; $k++) {
				if ($k < $begin_at) {
					continue;
				}

				// first, if the category changes, then display a new category header
				if (($this->categoryCount > 1) && ($entry[$k]['category'] != $curCategoryUID)) {
					$curCategoryUID = $entry[$k]['category'];
					for ($c = 0; $c < $this->categoryCount; $c++) {
						if ($this->categoryList[$c]['uid'] == $curCategoryUID) {
							$curCategory = $c;
							break;
						}
					}
					
					// Handle old template compatibility ###CATEGORY... with new template ###SHOW_CATEGORY...
					$cMarkerArray['###SHOW_CATEGORY_NAME###'] = '<div class="categoryName">'.$this->categoryList[$curCategory]['name'].'</div>';
					$cMarkerArray['###CATEGORY_NAME###'] = '<div class="tx-wecconnector-showCategory">'.$this->categoryList[$curCategory]['name'].'</div>';
					if ($img = $this->categoryList[$curCategory]['image']) {
						$imgFile = 'uploads/tx_wecconnector/'.$img;
						$imgSize = '';
						$imgSize .= $this->conf['imageWidth'] ? " width=".$this->conf['imageWidth'] : "";
						$imgSize .= $this->conf['imageHeight'] ? " height=".$this->conf['imageHeight'] : "";
					}
					if (strpos($templateDisplayContent,'###SHOW_CATEGORY_IMAGE###') != FALSE) {
					 	$catImageMarker = '###SHOW_CATEGORY_IMAGE###';
					 	$catImageClass = 'categoryImage';
					}
					else {
					 	$catImageMarker = '###CATEGORY_IMAGE###';
					 	$catImageClass = 'tx-wecconnector-showCategoryImage';						
					}
					$cMarkerArray[$catImageMarker] = '<div class="' . $catImageClass . '">';
					$cMarkerArray[$catImageMarker] .= $this->categoryList[$curCategory]['image'] ? "<img src=\"".$imgFile."\" ".$imgSize.">" : "";
					$cMarkerArray[$catImageMarker] .= '</div>';

					$entriesContent .= $this->cObj->substituteMarkerArrayCached($templateCatDisplayContent, $cMarkerArray, array(), array());
				}

				$entriesContent .= $this->displaySingle($entry[$k], $curTemplateEntry, $alternating);
				$alternating = !$alternating;
			}

			//--------------------------------------------------------------------------------------------
			//
			// THEN add a next/prev/page link if we need to put each entry into the template
			//
			//--------------------------------------------------------------------------------------------
			$templatePageLinkContent = $this->cObj->getSubpart($templateMainContent, '###PAGELINK###');

			// Make Next link
			//---------------------------------------------------------------
			if ($count > $begin_at + $limit) {
				$next = ($begin_at + $limit > $count) ? $count - $limit : $begin_at + $limit;
				$next = intval($next / $limit);
				$params = t3lib_div::_GET();
				$params['tx_wecconnector']['pagenum'] = $next;
				$linkURL = $this->pi_getPageLink($this->id,'',$params);
				$markerArray['###LINK_NEXT###'] = '<div class="pageLink"><a href="'. $linkURL.'">'.$this->pi_getLL('next_page', 'Next >').'</a></div>';
			} else {
				$markerArray['###LINK_NEXT###'] = '';
			}

			// Make Previous link
			//---------------------------------------------------------------
			if ($begin_at > 0) {
				$prev = ($begin_at - $limit < 0) ? 0 : $begin_at - $limit;
				$prev = intval($prev / $limit);
				$params = t3lib_div::_GET();
				$params['tx_wecconnector']['pagenum'] = $prev;
				$linkURL = $this->pi_getPageLink($this->id,'',$params);
				$markerArray['###LINK_PREV###'] = '<div class="pageLink"><a href="'. $linkURL.'">'.$this->pi_getLL('prev_page', '< Previous').'</a></div>';

			} else {
				$markerArray['###LINK_PREV###'] = '';
			}

			// Make Page show & Pages link
			//---------------------------------------------------------------
			$firstPage = 0;
			$lastPage = ceil($count / $limit);
			$actualPage = floor($begin_at / $limit);
			$markerArray['###PAGE_NUM###'] = '<div class="pageLink">'. $this->pi_getLL('page_name', 'Page') . (string)($actualPage + 1)	. '</div>';

			$markerArray['###PAGES###'] = '';
			if (!strpos($templatePageLinkContent, '###PAGE_NUM###')) // if no PAGE_NUM, then put "Page"
			$markerArray['###PAGES###'] .= '<div class="pageLink">'.$this->pi_getLL('page_name', 'Page').'</div>';

			for ($i = $firstPage ; $i < $lastPage; $i++) {
				// allow linked page numbers
				$item = (string)($i + 1);
				$params = t3lib_div::_GET();
				$params['tx_wecconnector']['pagenum'] = $i;
				$linkAHref = '<a href="'. $this->pi_getPageLink($this->id,'',$params).'"> '. $item . ' </a> ' ;
				if ($i == $actualPage) $linkAHref = $item;
				$markerArray['###PAGES###'] .= '<div class="pageLink pageLinkPad">'.$linkAHref. $this->pi_getLL('pagenum_divider').'</div>';
			}
			
			// now build all the content and add it the main content
			//---------------------------------------------------------------------------
			if ($lastPage > 1) { // only add if more than one page
				$pagelink_content = $this->cObj->substituteMarkerArrayCached($templatePageLinkContent, $markerArray, array(), array());
			}
			
			$entriesContent .= $pagelink_content;
			
		} else {
			$templateCatDisplayContent = $this->cObj->getSubpart($templateDisplayContent, '###DISPLAY_CATEGORY###');
			$subpartArray = array();
			$entriesContent .= $this->cObj->substituteMarkerArrayCached($templateCatDisplayContent, $cMarkerArray, array(), array());

			$entriesContent .= '<div class="marginBox">'.$this->pi_getLL('no_entries', 'There are no entries yet.').'</div>';
		}

		// Display entries section
		$hMarkerArray['###CHOOSE_CATEGORY###'] = $this->chooseCat(1); // horizontal list
		$hMarkerArray['###CHOOSE_CATEGORYDROPDOWN###'] = $this->chooseCat(2); // dropdown

		$hMarkerArray['###DISPLAY_HEADER###'] = $this->pi_getLL('display_header', 'Current') . ' ';
		if ($this->config['entries_description']) {
			$hMarkerArray['###DISPLAY_HEADER###'] .= $this->config['entries_description'];
		}
		else if ($this->config['entry_description']) {
			$hMarkerArray['###DISPLAY_HEADER###'] .= $this->config['entry_description'];
		}
		else {
			$hMarkerArray['###DISPLAY_HEADER###'] .= $this->pi_getLL('entry_description');
		}		

		$hMarkerArray['###DISPLAY_TOTAL###'] = $count . ' '. (($this->curCategory == 0) ? $this->pi_getLL('display_total', 'total') : $this->pi_getLL('display_total_category', 'in category'));
		$hMarkerArray['###ENTRIES###'] = $entriesContent;
		$subpartArray['###ENTRY###'] = '';
		$subpartArray['###ENTRY2###'] = '';
		$subpartArray['###DISPLAY_CATEGORY###'] = '';
		if (strpos($templateDisplayContent,'###ENTRIES###') === FALSE) {
			$subpartArray['###ENTRY###'] = $entriesContent;
		}

		$request_content .= $this->cObj->substituteMarkerArrayCached($templateDisplayContent, $hMarkerArray, $subpartArray, array());

		// clear out any empty template fields
		$request_content = preg_replace('/###.*?###/', '', $request_content);
		return $request_content;
	}

	/**
	* View Single: views a single item
	*
	* @param	string		$theItemID : the item to associated with this request
	* @return	string		$content: content generated to show the given request
	*/
	function viewSingle($theItemID) {
		$where = 'uid=' . (int)$theItemID;
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', $this->dataEntryTable, $where, '', '', '');
		if (mysql_error()) t3lib_div::debug(array(mysql_error(), $query));

		$theItem = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		$markerArray = $this->getRequestMarkerArray($theItem); // so you can build separate template for single item
		unset($markerArray['###VIEW_SINGLE###']);
		
		$templateDisplaySingle = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_DISPLAYSINGLE###');
		if (!strstr($templateDisplaySingle,'###MESSAGE###')) {
			$templateDisplayContent = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_DISPLAYENTRIES###');
			$thisTemplate = $this->cObj->getSubpart($templateDisplayContent, $this->entryTemplate);
			$templateDisplaySingle = str_replace('###SINGLE_ENTRY###', $thisTemplate, $templateDisplaySingle);
		}
		else {
			$markerArray['###SINGLE_ENTRY###'] =  $theItem ? $this->displaySingle($theItem, $thisTemplate) : "";
		}

		$urlParams = t3lib_div::_GET();
		unset($urlParams['tx_wecconnector']['single']);
		$backURL = $this->pi_getPageLink($this->id, '', $urlParams);
		$markerArray['###BACK_BUTTON###'] = '<a class="button" href="' . $backURL . '"><span class="label prevIcon">' . $this->pi_getLL('back_button','Go back') . '</span></a>';

		$singleContent = $this->cObj->substituteMarkerArrayCached($templateDisplaySingle, $markerArray, array(), array());

		// clear out any empty template fields
		$singleContent = preg_replace('/###.*?###/', '', $singleContent);
		
		return $singleContent;
	}

	/**
	* Display Single: displays one request
	*
	* @param	string		$theItem : the item to associated with this request
	* @param	string		$thisTemplate : the template to use for rendering content
	* @param	boolean		$alternating: if alternating for the display
	* @return	string		$content: content generated to show the given request
	*/
	function displaySingle($theItem, $thisTemplate, $alternating=false) {
		$subpartArray = array();

		// then go through and fill in each entry info
		$markerArray = $this->getRequestMarkerArray($theItem);

		if ($this->conf['alternatingEntry']) {
			$markerArray['###ALTERNATING_COLOR###'] = ($alternating) ? $this->conf['alternatingEntry'] : '';
		}

		//  clear out ###SHOW_LOCATION if state/city/location are not defined
		//
		if (($markerArray['###STATE###'] == '') && ($markerArray['###CITY###'] == '') && ($markerArray['###LOCATION###'] == ''))
			$subpartArray['###SHOW_LOCATION###'] = '';
		if ($markerArray['###EMAIL###'] == '')
			$subpartArray['###SHOW_EMAIL###'] = '';
		if ($markerArray['###WEBSITE_URL###'] == '')
			$subpartArray['###SHOW_WEBSITE_URL###'] = '';
		if ($markerArray['###CONTACT_NAME###'] == '')
			$subpartArray['###SHOW_CONTACT_NAME###'] = '';

		// now build the actual content from info
		return $this->cObj->substituteMarkerArrayCached($thisTemplate, $markerArray, $subpartArray, array());
	}

	/**
	*==================================================================================
	*  DISPLAY PREVIEW
	*
	*  Show a preview of the posts on the connector page
	*
	* @param  integer  $num number to display (default is 5)
	* @return  string  text of last # previews formatted according to template
	*==================================================================================
	*/
	function displayPreview($num = 5) {
		$preview_content = '';
		$preview_entries = '';
		$numToPreview = $this->config['num_preview_items'] ? $this->config['num_preview_items'] : $num;
		$previewLen = $this->config['preview_length'] ? $this->config['preview_length'] : 255;
		$previewDataPID = $this->pid_list ? $this->pid_list : $this->config['preview_backPID'];
		$previewBackPID = $this->config['preview_backPID'] ? $this->config['preview_backPID'] : $this->pid_list;
		$templatePreviewContent = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_PREVIEW###');
		$templatePreviewEntry = $this->cObj->getSubpart($templatePreviewContent, '###PREVIEW_ENTRY###');
		$templatePreviewDisplay = $this->cObj->getSubpart($templatePreviewContent, '###PREVIEW_DISPLAY###');

		$order_by = 'post_date DESC';
		$where = 'pid IN ('.$previewDataPID.')';
		if ($this->curCategory)
			$where .= ' AND category='.intval($this->curCategory);
		$where .= ' AND deleted=0 AND hidden=0 and is_response=0';
		$limit = $numToPreview;
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', $this->dataEntryTable, $where, '', $order_by, $limit);
		if (mysql_error()) t3lib_div::debug(array(mysql_error(), 'SELECT "*" FROM '.$this->dataEntryTable.' WHERE '.$where.' ORDER BY '.$order_by.' LIMIT '.$limit));

		$count = $GLOBALS['TYPO3_DB']->sql_num_rows($res);
		if ($count == 0) {
			// If no messages, give a blank message
			$templatePreviewNoEntry = $this->cObj->getSubpart($templatePreviewContent, '###PREVIEW_NOENTRY###');
			$markerArray['###NO_ENTRY_MESSAGE###'] = $this->pi_getLL('preview_none', 'There is nothing posted yet.');
			$preview_entries .= $this->cObj->substituteMarkerArrayCached($templatePreviewNoEntry, $markerArray, array(), array());
		} else {
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$markerArray = $this->getRequestMarkerArray($row); // so you can build separate template for each item

				// grab the message and show...
				$markerArray['###ON_TEXT###'] = $this->pi_getLL('on_text', 'on');

				$urlParameters['tx_wecconnector']['single'] = $row['uid'];
				$markerArray['###PREVIEW_LINK_BEGIN###'] = '<a href="'.$this->getAbsoluteURL($previewBackPID,$urlParameters).'">';
				$markerArray['###PREVIEW_LINK_END###'] = '</a>';

				// cut off message if needed
				$showMsg = stripslashes($row['message']);

				if (strlen($showMsg) > $previewLen) {
					// find first space and then cut there
					for ($i = $previewLen; $i > 0; $i--) {
						if ($showMsg[$i] == ' ') {
							$showMsg = substr($showMsg, 0, $i);
							$showMsg .= '...';
							break;
						}
					}
				}
				$markerArray['###MESSAGE###'] = $this->html_entity_decode(stripslashes($showMsg), ENT_QUOTES);

				$preview_entries .= $this->cObj->substituteMarkerArrayCached($templatePreviewEntry, $markerArray, $subpartMarker, array());
			}
		}
		$markerArray2['###PREVIEW_TITLE###'] = $this->config['connector_name'];
		$markerArray2['###PREVIEW_GOTO_LINK###'] = '<a class="button" href=' . $this->getAbsoluteURL($previewBackPID) . '><span class="label viewIcon">' . $this->pi_getLL('preview_goto_link','Check this out') . '</span></a>';
		$markerArray2['###PREVIEW_ENTRIES###'] = $preview_entries;
		$preview_content = $this->cObj->substituteMarkerArrayCached($templatePreviewDisplay, $markerArray2, array(), array());
		return $preview_content;
	}

	/**
	* ==================================================================================
	* Getting Marker Array: Handle setting the marker array for a given item
	*
	* ==================================================================================
	*
	* @param	array		$row row data from database call
	* @return	array		marker array
	*/
	function getRequestMarkerArray($row) {
		// to add a new row here, remember to add it in fieldNameArray above
		if ($row['post_date'] != 0) {
			$markerArray['###DATE###'] = $this->local_cObj->stdWrap($row['post_date'], $this->conf['general_dateWrap.']);
			$markerArray['###TIME###'] = $this->local_cObj->stdWrap($row['post_date'], $this->conf['general_timeWrap.']);
		}
   		$markerArray['###POSTEDBY_TEXT###'] = $this->pi_getLL('postedby_text', 'Posted By:');
		$markerArray['###CATEGORY###'] = $row['category'];
		$markerArray['###NAME###'] = $row['name'];
		$markerArray['###SUBJECT###'] = stripslashes($row['subject']);
//		$showEmail = str_replace('@', '&#064;', $row['email']);
//		$markerArray['###EMAIL###'] = $row['email'] ? '<a href="javascript:linkTo_UnCryptMailto(\''.$GLOBALS['TSFE']->encryptEmail('mailto:'.$row['email']).'\');">'.$showEmail.'</a>' : "";
		$markerArray['###EMAIL###'] = $this->cObj->getTypoLink($row['email'],$row['email']);

		$msgText = stripslashes($row['message']);
		$tagsAllowed = $this->config['html_tags_allowed'];
		if (strlen($tagsAllowed)) {
			$msgText = $this->html_entity_decode($msgText);
			if ($tagsAllowed != 1 && strlen($tagsAllowed)) {
				$msgText = strip_tags($msgText,$tagsAllowed);
			}
		}
		$markerArray['###MESSAGE###'] = $msgText;
		$markerArray['###PHONE###'] = $row['phone'];
		$markerArray['###LOCATION###'] = stripslashes($row['location']);
		$markerArray['###ADDRESS###'] = stripslashes($row['address']);
		$markerArray['###CITY###'] = stripslashes($row['city']);
		$markerArray['###STATE###'] = $row['state'];
		$markerArray['###ZIPCODE###'] = $row['zipcode'];
		$markerArray['###COUNTRY###'] = $row['country'];
		$showWebsiteURL = $row['website_url'];
		if (!strstr($showWebsiteURL, 'http://'))
			$showWebsiteURL = 'http://' . $showWebsiteURL;
		$showWebsiteURL = '"' .$showWebsiteURL . '"' . ($this->config['websiteURL_popup'] ? ' target="_blank"' : '');
		$markerArray['###WEBSITE_URL###'] = $row['website_url'] ? '<a href=' . $showWebsiteURL . '>' . $row['website_url'] . '</a>' : "";
		$markerArray['###BUSINESS_NAME###'] = $row['business_name'];
		$markerArray['###CONTACT_NAME###'] = $row['contact_name'];
		$markerArray['###EMAIL2###'] = $row['email2'];

		if (!$row['name'])
			$markerArray['###NAME###'] = $this->pi_getLL('anonymous', 'Anonymous');

		if (!$row['subject'])
			$markerArray['###SUBJECT###'] = $this->pi_getLL('no_subject', '(no subject)');

		if ($row['image']) {
			// put special code for showing the image
			$imgFile = 'uploads/tx_wecconnector/'.$row['image'];
			$imgSize = '';
			$imgSize .= $this->conf['imageWidth'] ? " width=".$this->conf['imageWidth'] : "";
			$imgSize .= $this->conf['imageHeight'] ? " height=".$this->conf['imageHeight'] : "";
		}
		$markerArray['###IMAGE###'] = $row['image'] ? "<div class=\"image\"><img src=\"".$imgFile."\" ".$imgSize."></div>" : "";

		// Show Respond button if we have an email
		if ($row['email'] && $this->canReply) {
			$paramArray['tx_wecconnector']['respond'] = $row['uid'];
			$markerArray['###RESPOND###'] = '<a href="' . $this->pi_getPageLink($this->id, '', $paramArray).'">' . $this->pi_getLL('respond_btn', 'Respond') . '</a>';
			$markerArray['###RESPOND_BUTTON###'] = '<a class="button smallButton" href="' . $this->pi_getPageLink($this->id, '', $paramArray).'"><span class="label replyIcon">' . $this->pi_getLL('respond_btn', 'Respond') . '</span></a>';
		}

		$s_paramArray['tx_wecconnector']['single'] = $row['uid'];
		$markerArray['###VIEW_SINGLE###'] = '<a class="button smallButton" href="' . $this->pi_getPageLink($this->id, '', $s_paramArray).'"><span class="label viewIcon">' . $this->pi_getLL('view_btn', 'View') . '</span></a>';

		// only allow the user who entered the message OR a moderator/admin to edit it
		$whoCanEdit = $this->config['who_can_edit'];
		if  (($whoCanEdit == 3) || // anyone
     	    (($whoCanEdit == 2) && $this->userID && ($row['user_uid'] == $this->userID)) || // or user
		    $this->isAdministrator) { // or administrator anytime
			// add modify/edit button	
			$paramArray2['tx_wecconnector']['modify'] = $row['uid'];
			$markerArray['###MODIFY###'] = '<a href="'.$this->pi_getPageLink($this->id, '', $paramArray2).'">'.$this->pi_getLL('edit_btn', 'Edit').'</a>';
			$markerArray['###MODIFY_BUTTON###'] = '<a class="button smallButton" href="'.$this->pi_getPageLink($this->id, '', $paramArray2).'"><span class="label editIcon">'.$this->pi_getLL('edit_btn', 'Edit').'</span></a>';
			// add delete button
			$paramArray3['tx_wecconnector']['del'] = $row['uid'];
			$markerArray['###DELETE_BUTTON###'] = ' <a class="button smallButton" href="'.$this->pi_getPageLink($this->id, '', $paramArray3).'" onclick="javascript:return confirm(\''.$this->pi_getLL('delete_confirm', 'Are you sure you want to delete?') .'\');"><span class="label deleteIcon">' . $this->pi_getLL('delete_btn', 'Delete') . '</span></a>';

			if ($row['hidden'] == 0) {
				$paramArray4['tx_wecconnector']['hide'] = $row['uid'];
				$markerArray['###HIDE_BUTTON###'] = ' <a class="button smallButton" href="'.$this->pi_getPageLink($this->id, '', $paramArray4).'" onclick="javascript:return confirm(\''.$this->pi_getLL('hide_confirm', 'Are you sure you want to hide?') .'\');"><span class="label hideIcon">' . $this->pi_getLL('hide_btn', 'Hide') . '</span></a>';
			}
			else {
				$paramArray4['tx_wecconnector']['unhide'] = $row['uid'];
				$markerArray['###HIDE_BUTTON###'] = ' <a class="button smallButton" href="'.$this->pi_getPageLink($this->id, '', $paramArray4).'" onclick="javascript:return confirm(\''.$this->pi_getLL('unhide_confirm', 'Are you sure you want to unhide?') .'\');"><span class="label hideIcon">'.$this->pi_getLL('unhide_btn', 'UnHide').'</span></a>';
			}
		}

		if ($this->isAdministrator) {
			$markerArray['###IS_HIDDEN###'] = $row['hidden'] ? " isHidden" : '';
		}

		return $markerArray;
	}

	/**
	* ==================================================================================
	* Choose Category: Show a way to choose the category, if there are any categories
	*
	* ==================================================================================
	*
	* @param	integer		$whichWay (1 = horizontal list, 2 = vertical dropdown, 3 = select menu
	* @return	string		text content for building category selector
	*/
	function chooseCat($whichWay) {
		$category_content = '';
		if ($this->categoryCount == 0) {
			return $category_content;
		}

		$url = $this->pi_getPageLink($this->id, '', $urlParams);
		if ($whichWay == 1) {
			// horizontal list
			$category_content .= '<span class="header">'.$this->pi_getLL('choose_category', 'Choose Category: ') .'</span>';
			// first add ALL
			if ($viewAllText = $this->pi_getLL('viewall_btn')) {
				$isSel = ($this->curCategory == 0) ? ' isSelected' :  '';
				$category_content .= '<a class="button smallButton' . $isSel . '" href="' . $url . '">
					<span class="label">' . $viewAllText . '</span></a>';
			}
			$category_content .= $this->pi_getLL('category_divider');

			for ($i = 0; $i < $this->categoryCount; $i++) {
				$catUID = $this->categoryList[$i]['uid'];
				$isSel = ($this->curCategory == $catUID) ? ' isSelected' :  '';
				$urlParams['tx_wecconnector']['viewcat'] = $catUID;
				$url = $this->pi_getPageLink($this->id, '', $urlParams);
				$category_content .= '<a class="button smallButton' . $isSel . '" href="' . $url . '">
					<span class="label">' .$this->categoryList[$i]['name'] . '</span></a>';

				if ($i != ($this->categoryCount -1)) $category_content .= $this->pi_getLL('category_divider');
			}
		}
		else if ($whichWay == 2) {
			// vertical dropdown
			$fullURL = $this->getAbsoluteURL($this->id);
			$category_content .= $this->pi_getLL('choose_category', 'Choose Category: ');
			if (strpos($fullURL, '?'))
				$fullURL .= '&tx_wecconnector[viewcat]=';
			else
				$fullURL .= '?tx_wecconnector[viewcat]=';
			$category_content .= '<select name="categories" size="1" onChange="location.href=\''.$fullURL.'\'+this.options[this.selectedIndex].value;">';
			if ($viewAllText = $this->pi_getLL('viewall_btn'))
				$category_content .= '<option value="0" ' . (($this->curCategory == 0) ? "selected" : "") . '>' . $viewAllText . '</option>';
			for ($i = 0; $i < $this->categoryCount; $i++) {
				$catUID = $this->categoryList[$i]['uid'];
				$category_content .= '<option value="' . $catUID . '" ' . (($this->curCategory == $catUID) ? "selected" : "") . '>' . $this->categoryList[$i]['name'] . '</option>';
			}
			$category_content .= '</select>';
		}
		else if ($whichWay == 3) {
			// select one for entry form
			$category_content .= '<select name="tx_wecconnector[category]" size="1">';

			for ($i = 0; $i < $this->categoryCount; $i++) {
				$catUID = $this->categoryList[$i]['uid'];
				$category_content .= '<option value="' . $catUID . '" ' . (($this->curCategory == $catUID) ? "selected" : "") . '>' . $this->categoryList[$i]['name'] . '</option>';
			}
			$category_content .= '</select>';
		} else {
			$category_content = '';
		}

		return $category_content;
	}

	/**
	* ==================================================================================
	* Toggle Entry Form -- Allow to toggle the entry form -- either show or hide
	*    Requires the ###ENTRY_FORM_TOGGLEON### OR ###ENTRY_FORM_TOGGLEOFF### in template
	*
	* ==================================================================================
	*
	* @param	integer		$startState starting state (0 = off, 1 = on)
	* @return	string		text content
	*/
	function toggleEntryForm($startState) {
		$content = '
			<script type="text/javascript">
			//<![CDATA[
			function toggleShowEntryForm() {
				entryFormToggleObj = document.getElementById("entryFormToggle");
				entryFormToggleHideObj = document.getElementById("entryFormToggleHide");
				if (!entryFormToggleObj || !entryFormToggleHideObj)
					return;
				if (entryFormToggleObj.style.display=="none") {
					entryFormToggleObj.style.display="block";
					entryFormToggleHideObj.style.display="none";
				}
				else {
					entryFormToggleObj.style.display="none";
					entryFormToggleHideObj.style.display="block";
				}
			}
			//]]>
			</script>
			<noscript>'. $this->pi_getLL('no_javascript_for_post', 'You must have Javascript enabled to post a message.'). '</noscript>';

		if ($startState == 1) {
			// if show is on, then show at first and set appropriate vars
			$startHide = 'display:none';
			 $startShow = 'display:block';
		} else {
			// if show is off, then hide entry form at first
			$startHide = 'display:block';
			 $startShow = 'display:none';
		}
		$content .= '<div id="entryFormToggleHide" style="'.$startHide.'">
				<a class="button" href="#" onclick="toggleShowEntryForm();return false;">
					<span class="label addIcon">' . $this->pi_getLL('form_toggleOn', 'Add An Entry') . '</span>
				</a>
			</div>
			<div id="entryFormToggle" style="'.$startShow.'">
				<a class="button" href="#" onclick="toggleShowEntryForm();return false;">
					<span class="label hideIcon">' . $this->pi_getLL('form_toggleOff', 'Hide Entry') . '</span>
				</a>
			';

		return $content;
	}

	/**
	* ==================================================================================
	* Enter a new request -- show a form and allow to enter info. Fields based on template,
	*  required fields supported. Uses a CSS-based form.
	*
	* ==================================================================================
	*
	* @return	string		form content returned
	*/
	function enterNewRequest() {
		$subpartArray = array();

		// extract input form out of template
		$templateFormContent = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_ENTRYFORM###');

		array_push($this->db_fields, 'submit');
		foreach ($this->db_fields AS $marker) {
			$markerArray['###FORM_'.strtoupper($marker).'###'] = $this->pi_getLL('form_'.$marker);
			$markerArray['###FORM_'.strtoupper($marker).'_INSTRUCTIONS###'] = $this->pi_getLL('form_'.$marker.'_instructions');
		}
		array_pop($this->db_fields); // remove submit

		$markerArray['###FORM_LEGEND###'] = $this->pi_getLL('form_legend','Enter Your Request');
		
		if ($this->config['entry_button']) { // if specialized for this connector
			$markerArray['###FORM_SUBMIT###'] = $this->config['entry_button'];
		}

		if ($this->config['entry_description']) { // fill in 'message' description with entry description
			$markerArray['###FORM_MESSAGE###'] = $this->config['entry_description'];
		}
		if ($this->config['entry_subject']) { // fill in 'message' subject with entry subject
			$markerArray['###FORM_SUBJECT###'] = $this->config['entry_subject'];
		}

		$markerArray['###PID###'] = $this->id;
		$markerArray['###ACTION_URL###'] = $this->getAbsoluteURL($this->id);

		if ($this->categoryCount > 1) {
			$markerArray['###VALUE_CATEGORY###'] = $this->chooseCat(3);
		} else {
			$markerArray['###VALUE_CATEGORY###'] = '';
			$markerArray['###FORM_CATEGORY###'] = '';
		}

		// pre-fill in form values if exist because either submitted or want to set default values externally
		if ($this->postvars) {
			foreach($this->postvars as $k => $v) {
				$markerArray['###VALUE_'.strtoupper($k).'###'] = stripslashes($v);
			}
		}

		// if we have submitted this form and not deleting any, then process it...
		if (($this->postvars['submitted'] == 1) && !$this->deleteMsgNum && !$this->hideMsgNum) {
			if (isset($this->postvars['category'])) {
				$this->curCategory = htmlspecialchars($this->postvars['category']);
				unset($this->postvars['category']);
				$saveData['category'] = $this->curCategory;
				$markerArray['###VALUE_CATEGORY###'] = $this->chooseCat(3);
			}

			if (isset($this->postvars['website_url'])) {
				// add http, if missing, for web url
				if (!strstr($this->postvars['website_url'], 'http://') && !empty($this->postvars['website_url'])) {
					$this->postvars['website_url'] = 'http://'.$this->postvars['website_url'];
				}
			}

			// now let's make sure everything in the form is right
			$errorString = $this->checkForValidFields();
			$this->submitErrors = 0;
			if ($errorString && strlen($errorString)) {
				// if there are errors, then show them
				$this->submitErrors = 1;
				$this->submitFormText = $this->pi_getLL('form_error').':'.$errorString;
				$markerArray['###FORM_ERROR###'] = '<script type="text/javascript">window.location.hash=\'entry\';</script>';
				$markerArray['###FORM_ERROR###'] .= $this->pi_getLL('form_error');
				$markerArray['###FORM_ERROR_FIELDS###'] = $errorString;

				if ($this->postvars['modified'] == 1) {
					// if we were modifying, then bring back entryform with error
					$paramArray['tx_wecconnector']['modify'] = $this->postvars['msgid'];
					$paramArray['tx_wecconnector']['modify_err'] = $errorString; //htmlspecialchars($errorString);
					header('Location: '.$this->getAbsoluteURL($this->id, $paramArray));
				}
			}
			// else let's post and/or moderate
			//-------------------------------------------------------------------------------------
			else {
				
				// check if should set to be moderated...
				$doModerated = 0;
				if (($modGroup = $this->config['administrator_group']) && (strlen($modGroup) > 0) && $this->config['do_moderate']) {
					$doModerated = 1;
				}

				// Let's filter the name, subject & message here...
				$filter_subject = $this->filterMessage($this->postvars['subject']);
				$filter_message = $this->filterMessage($this->postvars['message']);
				$filter_name    = $this->filterMessage($this->postvars['name']);
				if (($filter_message != '0') || ($filter_subject != '0') || ($filter_name != '0')) {
					switch ($this->config['filter_word_handling']) {
						case 'filter':
							//   filter the message (put ** in place of word)
							if ($filter_message != '0') $this->postvars['message'] = $filter_message;
							if ($filter_subject != '0')	$this->postvars['subject'] = $filter_subject;
							if ($filter_name != '0')	$this->postvars['name']    = $filter_name;
							break;

						case 'moderate':
							//  send to moderator (put in moderationQueue and let moderator decide)
							$doModerated = 2;
							break;

						default:
							// discard this post
							$paramArray['tx_wecconnector']['msg'] = 3;
							header('Location:'.$this->getAbsoluteURL($this->id, $paramArray));
							return;
					}
				}

				// Process any uploaded images
				$img = 0;
				$imgName = $_FILES['tx_wecconnector']['name']['image'];
				$imgSize = $_FILES['tx_wecconnector']['size']['image'];
				$imgType = $_FILES['tx_wecconnector']['type']['image'];
				$imgTmpName = $_FILES['tx_wecconnector']['tmp_name']['image'];
				if ($imgTmpName) {
					$this->fileFunc = t3lib_div::makeInstance('t3lib_basicFileFunctions');
					$imgName = $this->fileFunc->cleanFileName($imgName);
					$imgPath = PATH_site.'uploads/tx_wecconnector/';
					$imgUniqueName = $this->fileFunc->getUniqueName($imgName, $imgPath);
					move_uploaded_file($imgTmpName, $imgUniqueName);
					$imgPathInfo = pathinfo($imgUniqueName);
					$img = $imgName;
				}

				$saveData['uid'] = '';
				$saveData['pid'] = $this->pid_list;
				$saveData['tstamp'] = mktime();
				$saveData['crdate'] = mktime();
				$saveData['deleted'] = '0';
				$saveData['hidden'] = '0';
				$saveData['post_date'] = mktime();
				$saveData['user_uid'] = $this->userID;
				$saveData['moderationQueue'] = $doModerated;
				$saveData['ip_address'] = t3lib_div::getIndpEnv('REMOTE_ADDR');
				
				if ($img) $saveData['image'] = $img;
				//    $saveData['sys_language_uid'] = $this->sys_language_uid;

				// make the input in the form secure
				foreach ($this->postvars AS $k => $v) {
					if (in_array($k, $this->db_fields)) {
						$saveData[$k] = strip_tags($v);
						$saveData[$k] = $this->removeXSS($saveData[$k]);
					}
				}

				// Test for Duplicate.
				// (duplicate defined: someone posted something with same text in last 5 minutes)
				if ($this->postvars['modified'] != 1) {
					if (($postDelayTime = $this->conf['duplicateCheckDelaySeconds']) && $saveData['message']) {
						$previousTimeCheck = mktime() - ($postDelayTime * 60);
						$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', $this->dataEntryTable, 'post_date>='.intval($previousTimeCheck).' AND pid IN('.$this->pid_list.') AND is_response=0', '');
						if (mysql_error()) t3lib_div::debug(array(mysql_error(), 'MYSQL Check for duplicates error'));
							$count = $GLOBALS['TYPO3_DB']->sql_num_rows($res);
						if ($count > 0) {
							while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
								if ((($row['user_uid'] == $saveData['user_uid']) || !$saveData['user_uid']) && (strcasecmp($saveData['message'], $row['message']) == 0)) {
									// found duplicate
									$paramArray['tx_wecconnector']['msg'] = 4;
									header('Location:'.$this->getAbsoluteURL($this->id, $paramArray));
									return;
								}
							}
						}
					}
				}

				// MODIFY OR INSERT?
				//=====================================================================
				if ($this->postvars['modified'] == 1) {
					unset($saveData['crdate']);
					unset($saveData['uid']);
					if ($this->isAdministrator) {
						$saveData['moderationQueue'] = 0;
					}
					$insert = $GLOBALS['TYPO3_DB']->exec_UPDATEquery($this->dataEntryTable, "uid=".$this->postvars['msgid'], $saveData);
				}
				else
					$insert = $GLOBALS['TYPO3_DB']->exec_INSERTquery($this->dataEntryTable, $saveData);
				if (mysql_error()) t3lib_div::debug(array(mysql_error(), $insert), 'mySQL Error in Insert');

				// if insert was successful, then determine what to do with message
				//------------------------------------------------------------------
				if ($insert) {
					if ($doModerated) {
						$adminGroup = $modGroup;
						if (($doModerated == 2) && ($this->config['spam_administrator'])) {
							$adminGroup = $this->config['spam_administrator'];
						}
						// send to moderators if moderated
						$this->sendToModerators($adminGroup, $this->postvars['name'], $this->postvars['email'], $this->postvars['subject'], $this->postvars['message']);
					} else {
						// send out the message to all subscribers
						$entry[0] = $saveData;
						$this->sendRequests($entry, 1);
					}
					if ($this->config['redirect_page'])
						header('Location: '.$this->getAbsoluteURL($this->config['redirect_page']));
					else {
						$paramArray['tx_wecconnector']['msg'] = $doModerated ? 2 : 1;
						if ($this->postvars['modified'] == 1)
							$paramArray['tx_wecconnector']['msg'] = 7;
						header('Location: '.$this->getAbsoluteURL($this->id, $paramArray));
					}
				}
			}
		} // end if submitted

		if (!$this->submitErrors) {
			$subpartArray['###SHOW_ERROR###'] = '';			
		}
		//
		// TURN OFF ALL NON-DISPLAY FIELDS
		//--------------------------------------------------------------------------
		if (is_array($this->config['display_fields']) && count($this->config['display_fields']) > 0) {
			$fieldArray = array('name', 'email', 'message', 'location', 'subject', 'phone', 'address', 'city', 'state', 'zipcode', 'country', 'image', 'website_url', 'contact_name', 'business_name');
			for ($i = 0; $i < count($fieldArray); $i++) {
				$fieldFound = false;
				foreach ($this->config['display_fields'] AS $display_field) {
					if (!strcmp($display_field, $fieldArray[$i])) {
						$fieldFound = true;
						break;
					}
				}
				// the field was not in display_list, so turn it off
				if (!$fieldFound) {
					$subpartArray['###SHOW_'.strtoupper($fieldArray[$i]).'###']='';
				}
			}
		}
		// turn on/off CATEGORY

		// Pre-fill form data if FE user is logged in
		if (!$this->postvars && $GLOBALS['TSFE']->loginUser) {
			$surname_pos = strpos($GLOBALS['TSFE']->fe_user->user['name'], ' ');
			$markerArray['###VALUE_NAME###'] = substr($GLOBALS['TSFE']->fe_user->user['name'], 0, $surname_pos);
			$markerArray['###VALUE_EMAIL###'] = $GLOBALS['TSFE']->fe_user->user['email'];
			$markerArray['###VALUE_WEBSITE_URL###'] = $GLOBALS['TSFE']->fe_user->user['www'];
			$markerArray['###VALUE_CITY###'] = $GLOBALS['TSFE']->fe_user->user['city'];
		}

		// Add Image Captcha Support...
		//----------------------------------------------
		if (is_object($this->freeCap)) {
			$markerArray = array_merge($markerArray, $this->freeCap->makeCaptcha());
		} else {
			$subpartArray['###CAPTCHA_INSERT###'] = '';
		}
		if ($this->easyCaptcha) {
			$markerArray['###FORM_CAPTCHA_LABEL###'] = $this->pi_getLL('form_captcha_label','Enter words you see image in text');
			$markerArray['###EASY_CAPTCHA_IMAGE###'] =  '<img src="'.t3lib_extMgm::siteRelPath('captcha').'captcha/captcha.php" alt="" />';
		}
		else {
			$subpartArray['###SHOW_EASY_CAPTCHA###'] = '';
		}
		
		// Add Text-Captcha Support...
		//----------------------------------------------
		if ($this->config['use_text_captcha']) {
			$markerArray['###TEXT_CAPTCHA_LABEL###'] = $this->pi_getLL('textcaptcha_field','Are you a person?');
			$markerArray['###TEXT_CAPTCHA_FIELD###'] = '<input type="checkbox" class="checkbox" name="tx_wecconnector[textcaptcha_value]"/>';
		}
		else {
			$subpartArray['###SHOW_TEXT_CAPTCHA###'] = '';
		}

		// if this is an edit, then fill in vars with data
		if ($this->filledInVars) {
			unset($this->filledInVars['category']);
			foreach ($this->filledInVars AS $k => $v) {
				if (in_array($k, $this->db_fields)) {
					$valStr = '###VALUE_'.strtoupper($k).'###';
					$markerArray[$valStr] = $v;
				}
			}
			$markerArray['###MSGID###'] = $this->filledInVars['id'];
			$markerArray['###FORM_SUBMIT###'] = $this->pi_getLL('modify_request','Modify Request');
			$markerArray['###HIDDEN_VARS###'] = '<input type="hidden" name="tx_wecconnector[modified]" value="1"/>';
			$markerArray['###CANCEL_BUTTON###'] = '<input type="button" onclick="javascript:history.go(-1)" value="'.$this->pi_getLL('cancel_btn', 'Cancel').'"/>';
		}
		$markerArray['###HIDDEN_VARS###'] .= '<input type="hidden" name="no_cache" value="1"/>';

		// put a space for ALL required fields
		foreach ($this->db_fields AS $req_field) {
			$markerArray['###FORM_'.strtoupper($req_field).'_REQUIRED###'] = '<span class="hspacer">&nbsp;</span>';
		}
		// then, mark any set required fields appropriately
		$markerArray = $this->markRequiredFields($markerArray, $this->config['required_fields']);

		$formContent = $this->cObj->substituteMarkerArrayCached($templateFormContent, $markerArray, $subpartArray, array());

		// clear out any empty template fields
		$formContent = preg_replace('/###.*?###/', '', $formContent);
		
		return $formContent;
	}

	/**
	* ==================================================================================
	* Check for Valid fields: check required fields, email, and message/field lengths
	*
	* ==================================================================================
	*
	* @return	string		$errorStr  0 = no errors, or string containing errors
	*/
	function checkForValidFields() {
		$errorStr = "";
		if (is_array($this->config['required_fields']) && count($this->config['required_fields']) > 0) {
			foreach ($this->config['required_fields'] AS $req_field) {
				$isErrorField = false;
				// handle image field as special case since data comes in $_FILES var
				if (!strcmp($req_field,'image')) {
					if (!$_FILES['tx_wecconnector']['name']['image'])
						$isErrorField = true;
				}
				else if ((empty($this->postvars[$req_field])) || !strlen(trim($this->postvars[$req_field]))) {
					$isErrorField = true;
				}
				if ($isErrorField)
					$errorStr .= '<li> "'.ucfirst($this->pi_getLL('form_'.$req_field)).'" '.$this->pi_getLL('form_required_blank') . '</li>';
			}
		}

		// check for length
		if ($maxLen = $this->config['message_length']) {
			if (($thisLen = strlen($this->postvars['message'])) > $maxLen)
				$errorStr .= '<li> Message: '.$this->pi_getLL('form_invalid_field_length').' (max='.$maxLen.', current='.$thisLen.') </li>';
		}
		if ($maxLen = $this->config['field_length']) {
			foreach ($this->db_fields AS $k) {
				if (($k != 'message') && !empty($this->postvars[$k])) {
					if (($thisLen = strlen($this->postvars[$k])) > $maxLen)
						$errorStr .= '<li> '.$this->pi_getLL('form_'.$k).': '.$this->pi_getLL('form_invalid_field_length').' (max='.$maxLen.', current='.$thisLen.') </li>';
				}
			}
		}
		if (isset($this->config['numlinks_allowed'])) {
			$numLinksFound = $this->checkNumLinks($this->postvars['message']);
			$numLinksAllowed = (int) $this->config['numlinks_allowed'];
			if ($numLinksFound > $numLinksAllowed) {
				if ($numLinksAllowed > 0)
					$errorStr .= '<li>'.$this->pi_getLL('too_many_links','Too many links found -- only allowed ').$numLinksAllowed.'</li>';
				else
					$errorStr .= '<li>'.$this->pi_getLL('no_links_allowed','You cannot post any links here.').'</li>';
			}
		}
		if (is_object($this->freeCap) && !$this->freeCap->checkWord($this->postvars['captcha_response']))
			$errorStr .= '<li>' . $this->pi_getLL('captcha_bad','Please try entering the text for the Image Check again.'). '</li>';
			
		if ($this->easyCaptcha && t3lib_extMgm::isLoaded('captcha')) {
			session_start();
			$captchaStr = $_SESSION['tx_captcha_string'];
			$_SESSION['tx_captcha_string'] = '';
			if (!$captchaStr || ($this->postvars['captcha_response'] != $captchaStr)) {
				$errorStr .= '<li>' . $this->pi_getLL('captcha_bad','Please try entering the text for the Image Check again.'). '</li>';
			}
		}	

		if ($this->config['use_text_captcha'] && (!$this->postvars['textcaptcha_value']))
			$errorStr .= '<li>' . $this->pi_getLL('textcaptcha_bad','You need to fill out the "Are you a person?" field') .'</li>';

		if (!empty($this->postvars['email'])) {
			if (t3lib_div::validEmail($this->postvars['email']) == false)
				$errorStr .= '<li> '.$this->pi_getLL('form_email').' ('.$this->pi_getLL('form_invalid_field').")</li>";
		}

		if (!empty($this->postvars['website_url']) && $this->postvars['website_url'] != 'http://') {
			if ($this->isURL($this->postvars['website_url']) == false)
				$errorStr .= '<li> '.ucfirst($this->pi_getLL('form_website_url')).' ('.$this->pi_getLL('form_invalid_field').")</li>\n";
		}
		if (strlen($errorStr)) {
			$errorStr = "<ul>".$errorStr."</ul>";
			return $errorStr;
		}
		else
			return 0;
	}

	/**
	* ==================================================================================
	*  CHECK NUMBER OF LINKS IN MESSAGE
	*
	*
	* @param  string $msg  	text of message to check
	* @return int	 		number of links found
	*/
	function checkNumLinks($msg) {
		$numLinksFound = 0;
		$msg = $this->html_entity_decode($msg);
		$msg = stripslashes($msg);

		// count and strip off all <a href>...</a>
		$numLinksFound = preg_match_all('/<a[^>]*?href=[\'"](.*?)[\'"][^>]*?>(.*?)<\/a>/si',$msg,$matches);
		if ($numLinksFound > 0)
			$msg = preg_replace('/<a[^>]*?href=[\'"](.*?)[\'"][^>]*?>(.*?)<\/a>/si',"",$msg,-1);

		// count all http:// left
		preg_match_all("/http:\/\//isU",$msg, $matches, PREG_PATTERN_ORDER);
		$numLinksFound += count($matches[0]);

		return $numLinksFound;
	}

	/**
	* ==================================================================================
	*  FILTER THE MESSAGE
	*
	*  Check for filter words and if found, run the filter.
	*
	* @param  string $msgText  text of message to filter
	* @return string text of message if filtered or '0' if no need to filter
	*/
	function filterMessage($msgText) {
		$filterWordList = trim($this->config['filter_wordlist']);

		// if * in filter word list, then use the default. This supports adding other words to the * default
		if ($filterWordList == '*' || (!(($all = strpos($filterWordList, '*')) === false))) {
			$newFilterWordList = strrev($this->conf['spamWords']);
			if ($this->conf['addSpamWords']) {
				if (strlen($newFilterWordList))
					$newFilterWordList .= ',';
				$newFilterWordList .= $this->conf['addSpamWords'];
			}
			if (strlen($filterWordList) > 1) {
				$start = $all;
				$end = $all+1;
				if (!(strpos($filterWordList, '*,') === false)) // if it is *, then remove both
					$end++;
				if (!(strpos($filterWordList, ',*') === false)) // if it is ,* then remove both
					$start--;
				$filterWordList = substr($filterWordList, 0, $start) . substr($filterWordList, $end);
				$filterWordList .= ','.$newFilterWordList;
			}
			else
				$filterWordList = $newFilterWordList;
		}
		else if (strlen($filterWordList) <= 1)
			return '0'; // if empty, then return ok

		$filterWordArray = t3lib_div::trimExplode(',', $filterWordList);
		$filterCount = 0;
		foreach ($filterWordArray as $checkWord) {
			if (strlen($checkWord) && preg_match('/' . $checkWord . '/', $msgText)) {
				$filterWord = trim(strtolower($checkWord));
				$newWord = substr($filterWord, 0, 1).str_repeat('*', strlen($filterWord)-1);
				$msgText = preg_replace('/' . $filterWord . '/', $newWord, $msgText);
				$filterCount++;
				$this->spamMessage .= ' spam word='.$filterWord; 
			}
		}
		// if none to filter then return ok
		if (!$filterCount)
			return '0';

		// return new message text
		return $msgText;
	}

	/**
	* ==================================================================================
	*  Respond form: show the email response form. Can either submit a response or cancel
	*
	* ==================================================================================
	*
	* @param	integer		$msgID message ID
	* @return	string		content to show form
	*/
	function respondForm($msgID) {
		$subpartArray = array();
		
		// Load Original Message from Database
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', $this->dataEntryTable, 'uid='.intval($msgID), '');

		$msgSubject = '';
		$msgText = '';
		if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$msgSubject = stripslashes($row['subject']);
			$msgText = stripslashes($row['message']);
			$msgName = $row['name'];
			$msgEmail = $row['email'];
			$msgDate = $row['post_date'];
		} else {
			// TO DO...no message found...
		}

		// extract response form out of template
		//
		$templateFormContent = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_RESPONSEFORM###');

		// now fill in all the markers
		if ($this->submitFormText) {
			$markerArray['###FORM_ERROR###'] = '<script type="text/javascript">window.location.hash=\'response\';</script>';
			$markerArray['###FORM_ERROR###'] .= $this->pi_getLL('form_error');
			$markerArray['###FORM_ERROR###'] .= '<ul><li>' . $this->submitFormText . '</li></ul>';
		}
		else {
			$subpartArray['###SHOW_ERROR###'] = '';
		}
				
		$substituteArray = array('name', 'subject', 'email', 'message', 'submit', 'cancel');
		foreach ($substituteArray AS $marker) {
			$markerArray['###FORM_'.strtoupper($marker).'###'] = $this->pi_getLL('responseform_'.$marker);
		}
		$markerArray = $this->markRequiredFields($markerArray, $this->config['required_response_fields']);
		
		$markerArray['###ORIG_MESSAGE###'] = $msgText;
		$markerArray['###ORIG_NAME###'] = $msgName;
		$markerArray['###ORIG_SUBJECT###'] = $msgSubject;
		$markerArray['###ORIG_DATE###'] = $this->local_cObj->stdWrap($msgDate, $this->conf['general_dateWrap.']);
		$markerArray['###ORIG_TIME###'] = $this->local_cObj->stdWrap($msgDate, $this->conf['general_timeWrap.']);
		$markerArray['###MSGNUM###'] = $msgID;
		$markerArray['###RESPONSE_TITLE###'] = $this->config['response_title'];
		$markerArray['###RESPONSE_HEADER###'] = $this->pi_getLL('response_header','Respond to Original Message:');
		$markerArray['###VALUE_SUBJECT###'] = $msgSubject;
		$markerArray['###PID###'] = $this->id;

		$markerArray['###ACTION_URL###'] = $this->getAbsoluteURL($this->id);
		// if filled in values already (were errors), then fill in those values again
		if ($this->postvars['submitresponse']) {
			if ($this->postvars['name']) $markerArray['###VALUE_NAME###'] = $this->postvars['name'];
			if ($this->postvars['email']) $markerArray['###VALUE_EMAIL###'] = $this->postvars['email'];
			if ($this->postvars['message']) $markerArray['###VALUE_MESSAGE###'] = $this->postvars['message'];
		}
		// Pre-fill form data if FE user in logged in
		else if (!$this->postvars && $GLOBALS['TSFE']->loginUser) {
			$surname_pos = strpos($GLOBALS['TSFE']->fe_user->user['name'], ' ');
			$markerArray['###VALUE_NAME###'] = substr($GLOBALS['TSFE']->fe_user->user['name'], 0, $surname_pos);
			$markerArray['###VALUE_EMAIL###'] = $GLOBALS['TSFE']->fe_user->user['email'];
		}

		// add cancel URL
		$getvarsE = t3lib_div::_GET();
		unset($getvarsE['id']);
		unset($getvarsE['tx_wecconnector']['respond']);
		$markerArray['###CANCEL_URL###'] = 'location.href=\''.$this->getAbsoluteURL($this->id, $getvarsE).'\'';

		// then do the substitution with the template
		$formContent = $this->cObj->substituteMarkerArrayCached($templateFormContent, $markerArray, $subpartArray, array());

		// clear out any empty template fields
		$formContent = preg_replace('/###.*?###/', '', $formContent);
		
		return $formContent;
	}

	/**
	* ==================================================================================
	* Respond to a request: handle respondForm post by sending email to person who posted message
	*
	* ==================================================================================
	*
	* @param	string		$thisName  	name of person responding
	* @param	string		$thisEmail  email of person responding
	* @param	string		$thisSubject subject to send
	* @param	string		$thisMessage message to send
	* @param	integer		$thisMsgID  message id to respond to
	* @param	integer		$force  	if should skip spam & moderation checks 
	* @return	void
	*/
	function respondToRequest($thisName, $thisEmail, $thisSubject, $thisMessage, $thisMsgID, $force = false) {
		// LOOKUP MESSAGE IN DATABASE
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', $this->dataEntryTable, 'uid=' . intval($thisMsgID), '');

		if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$origName = $row['name'];
			$origEmail = trim($row['email']);
			$thisMessage = stripslashes($thisMessage);

			// THEN SEND EMAIL HERE
			if ($origEmail) {
				$header = $this->setEmailHeader();
				$origName =  t3lib_div::encodeHeader($origName,'quoted_printable',$this->charset);
				$emailTo = $origName . ' <' . $origEmail . '>';
				$fromName = t3lib_div::encodeHeader($thisName,'quoted_printable',$this->charset);
				$linebreak = (TYPO3_OS=='WIN') ? chr(13).chr(10) : chr(10);

				// SETUP HEADER INFO
				// remove any , in name otherwise gets mixed up
				if (strpos($fromName,',') !== FALSE)
					$fromName = str_replace(',','/',$fromName);
				// add <> around email if name is present
				$emailFrom = 'From: ' . $fromName;
				if ((strpos($thisEmail,'<') === FALSE) && strlen($fromName))
					$emailFrom .= '<'.$thisEmail.'>';
				else
					$emailFrom .= $thisEmail;
					
				// Set subject as reply
				$thisSubject = 'RE: '.$thisSubject;
				$thisSubject = t3lib_div::encodeHeader($thisSubject,'quoted_printable',$this->charset);

				// do normal checks if not forced to send		
				if (!$force) {
					// CHECK FOR VALID FIELDS
					if (is_array($this->config['required_response_fields']) && count($this->config['required_response_fields']) > 0) {
						$errorStr = '';
						foreach ($this->config['required_response_fields'] AS $req_field) {
							if ((empty($this->postvars[$req_field])) || !strlen(trim($this->postvars[$req_field]))) {
								$errorStr .= '<li> "'.ucfirst($this->pi_getLL('form_'.$req_field)).'" '.$this->pi_getLL('form_required_blank') . '</li>';
							}
						}
						if (strlen($errorStr)) {
							$this->submitErrors = true;
							$this->submitFormText = '<ul>' . $errorStr . '</ul>';
							return;
						}
					}

					// CHECK IF VALID RESPONDER EMAIL
					if (strlen($thisEmail) && (t3lib_div::validEmail($thisEmail) == false)) {
						$this->submitErrors = true;
						$this->submitFormText = $this->pi_getLL('subscribe_error2', 'Please provide a valid email in the form name@web.com') . $thisEmail;
						return;
					}

					// DO SPAM CHECK
					$likelySpam = false;
					$spamCheckAction = $this->config['spam_check_responses'];
					if ($spamCheckAction != 'none') {
						$whySpam = 0;
						$this->spamMessage = '';
						
						// first check # links. if more than given, then return message
						$numLinksFound = $this->checkNumLinks($thisMessage);
						$numLinksAllowed = (int) $this->config['numlinks_allowed'];
						if ($numLinksFound > $numLinksAllowed) {
							$likelySpam = true;
							$this->spamMessage .= 'numLinksFound(' . $numLinksFound . ') > numLinksAllowed('.$numLinksAllowed.')';
						}
						// next check for any filter words
						$filteredWords = $this->filterMessage($thisMessage);
						if ($filteredWords != '0')
							$likelySpam = true;
						// if spam found, then handle it
						if ($likelySpam) {
							switch ($spamCheckAction) {
								case 'moderate': 
									// let do_moderate_response (below) handler deal with response msg
									break;
								case 'discard':  
									// do nothing except post error
									$this->submitFormText = $this->pi_getLL('respond_error_spam');
									return;
								case 'admin':
									// add additional info for notify message
									$spamNotifyMessage = $this->pi_getLL('spam_notify1','Spam determined in response sent from ') . $thisName . ' ' . $thisEmail . ' ' . $this->pi_getLL('spam_notify2',' in response to message posted by ') . $emailTo . ' at: '.$this->getAbsoluteURL($this->id) . $linebreak . $linebreak;
									if (strlen($this->spamMessage)) {
										$spamNotifyMessage .= '[reason=' . $this->spamMessage . ']' . $linebreak . $linebreak;
									}
									$spamNotifyMessage .= $thisMessage;
									$spamNotifyMessage .= $linebreak . $linebreak . $this->pi_getLL('ip_address_field','IP: ') . t3lib_div::getIndpEnv('REMOTE_ADDR');
									if ($GLOBALS['TSFE']->loginUser)
										$spamNotifyMessage .= $linebreak . ' fe_user uid=' . $GLOBALS['TSFE']->fe_user->user['uid'] . ' username=' . $GLOBALS['TSFE']->fe_user->user['username'];
									// send out to all in spam admin list
									$adminList = $this->config['spam_administrator'] ? $this->getAdminInfo($this->config['spam_administrator']) : $this->getAdminInfo($this->config['administrator_group']);
									if (count($adminList)) {
										foreach ($adminList as $thisAdmin) {
											$adminEmailTo = t3lib_div::encodeHeader($thisAdmin['email'],'quoted_printable',$this->charset);
											mail($adminEmailTo, $this->pi_getLL('email_notify_spam', 'NOTIFY SPAM RESPONSE:') . $thisSubject, $spamNotifyMessage, $emailFrom . $header);
										}
									}
									else {
										$notifyEmailTo = t3lib_div::encodeHeader($this->config['notify_email'],'quoted_printable',$this->charset);
										mail($notifyEmailTo, $this->pi_getLL('email_notify_spam', 'NOTIFY SPAM RESPONSE:') . $thisSubject, $spamNotifyMessage, $emailFrom . $header);
									}
									// don't give an error message, unless this is set
									$this->submitFormText = $this->pi_getLL('respond_error_spam');
									return;
							}
						}
					}

					// Send to moderators if selected or if is likely spam and should moderate
					if ($this->config['do_moderate_response'] || ($likelySpam && ($spamCheckAction == 'moderate'))) {
						$saveData['name'] = $thisName;
						$saveData['email'] = $thisEmail;
						$saveData['subject'] = $thisSubject;
						$saveData['message'] = $thisMessage;
						$saveData['is_response'] = $thisMsgID;
						$saveData['moderationQueue'] = 1;
						$saveData['pid'] = $this->pid_list;
						$saveData['ip_address'] = t3lib_div::getIndpEnv('REMOTE_ADDR');
						$saveData['crdate'] = mktime();
						$insert = $GLOBALS['TYPO3_DB']->exec_INSERTquery($this->dataEntryTable, $saveData);
						if (mysql_error()) t3lib_div::debug(array(mysql_error(), $insert), 'mySQL Error in Insert');
					
						$modGroup = $this->config['administrator_group'] ? $this->config['administrator_group'] : $this->config['spam_administrator'];
						if ($modGroup) {
							$modMessage .= $thisMessage;
							$modMessage .= $linebreak . $linebreak . $this->pi_getLL('respond_to_message','Response to message:') . $linebreak;
							$modMessage .= $origName . ' ' . $origEmail . $linebreak . $row['message'] . $linebreak;
							if ($likelySpam && strlen($this->spamMessage)) {
								$modMessage .= $linebreak . '[likely spam reason:' . $this->spamMessage . ']';
							}
							$modMessage .= $linebreak . $this->pi_getLL('ip_address_field','IP: ') . t3lib_div::getIndpEnv('REMOTE_ADDR');
							$this->sendToModerators($modGroup, $thisName, $thisEmail, 'RESPONSE TO', $modMessage);
							$this->submitFormText = $this->pi_getLL('respond_success2', 'Your reply should be sent.');
							return;
						}
					}
				}

				// PREPARE MESSAGE AND SEND TO PERSON
				$respondMessage = $this->pi_getLL('respond_header', 'This email is sent from your request on: ') . $this->getAbsoluteURL($this->id) . "\n\n";
				$respondMessage .= $thisMessage;
				mail($emailTo, $thisSubject, $respondMessage, $emailFrom.$header);

				// NOTIFY SENDER THAT IT WAS SENT
				$this->submitFormText = $this->pi_getLL('respond_success', 'Your reply was sent successfully.');

				// NOTIFY ADMIN
				if ($this->config['notify_email']) {
					$respondMessage = $this->pi_getLL('notify_response_msg1','Email sent from: ') . $thisName . ' ' . $thisEmail . $this->pi_getLL('notify_response_msg2',' in response to ') . $emailTo . ' at: '.$this->getAbsoluteURL($this->id) . $linebreak . $linebreak;
					$respondMessage .= $thisMessage;
					$respondMessage .= $linebreak . $linebreak . $this->pi_getLL('ip_address_field','IP: ') . t3lib_div::getIndpEnv('REMOTE_ADDR');
					$notifyEmailTo = t3lib_div::encodeHeader($this->config['notify_email'],'quoted_printable', $this->charset);
				
					mail($notifyEmailTo, $this->pi_getLL('notify_response_header',"NOTIFY RESPONSE:") . $thisSubject, $respondMessage, $emailFrom.$header);
				}
			}
			else {
				$this->submitFormText = $this->pi_getLL('respond_error1', 'Your reply could not be sent due to a bad email address for this message.');
			}
		}
		else {
			$this->submitFormText = $this->pi_getLL('respond_error2', 'Your reply could not be sent.');
		}
	}

	/**
	* ==================================================================================
	* Send Response Message(s): send out response messages (usually after moderated)
	*
	* ==================================================================================
	*
	* @param	string		message uid list that are comma-separated
	* @return	integer		number of responses sent
	*/
	function sendResponseMessages($msgList) {
		// grab all data, build message, and then send
		$msgCount = 0;
		if (strlen($msgList)) {
			// grab each message from database
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', $this->dataEntryTable, 'uid IN (' . $msgList . ')', '');
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				// grab original message
				$origMessage = $row['is_response'];
				$this->respondToRequest($row['name'], $row['email'], $row['subject'], $row['message'], $origMessage, true);
				$msgCount++;
			}
			$updData['moderationQueue'] = 0;
			$updData['deleted'] = 1;
			$update = $GLOBALS['TYPO3_DB']->exec_UPDATEquery($this->dataEntryTable, "uid IN (" . $msgList . ")", $updData);
			$this->submitFormText = $msgCount . $this->pi_getLL('admin_responses_sent',' response message(s) were sent.');
		}
		
		return $msgCount;
	}
	
	/**
	* ==================================================================================
	* Set Email Header: setup the header to support plain text + UTF8 or whatever charset in TYPO3 system
	*
	* ==================================================================================
	*
	* @param	none
	* @return	string	header string to put in mail from
	*/
	function setEmailHeader() {
		// code for encoding in UTF8 or other charsets (taken from t3lib_htmlmail.php)
		$this->charset = 0;
		if (is_object($GLOBALS['TSFE']) && $GLOBALS['TSFE']->renderCharset) {
			$this->charset = $GLOBALS['TSFE']->renderCharset;
		} elseif (is_object($GLOBALS['LANG']) && $GLOBALS['LANG']->charSet) {
			$this->charset = $GLOBALS['LANG']->charSet;
		} elseif ($GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset']) {
			$this->charset = $GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'];
		} else {
			$this->charset = 'iso-8859-1';
		}
		$plain_text_header = "\nMime-Version: 1.0\n";
		$plain_text_header .= "Content-Type: text/plain; charset=" . strtoupper($this->charset) . "\n";
		$plain_text_header .= "Content-Transfer-Encoding: 8bit\n\n";

		return $plain_text_header;
	}

	/**
	* ==================================================================================
	* Modify Form -- handle modifying or deleting an existing request
	*
	* ==================================================================================
	*
	* @param	integer		$thisMsgID  message id to modify/delete
	* @return	string		$content  modify content
	*/
	function modifyForm($msgID) {

		// Verify that can modify the message
		// either must be moderator or the owner
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', $this->dataEntryTable, 'uid='.intval($msgID), '');
		if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			if ($this->isAdministrator || (($this->userID != 0) && $row['user_uid'] == $this->userID) || ($this->config['who_can_edit'] == 3)) {
				$filledInArray = $this->db_fields;
				foreach ($filledInArray as $fi) {
					if ($row[$fi] != NULL)
						$this->filledInVars[$fi] = stripslashes($row[$fi]);
				}
				$this->filledInVars['id'] = $msgID;
			} else {
				$this->submitFormText = $this->pi_getLL('edit_denied', 'You do not have permission to edit this message.');
				return false;
			}
		} else {
			$this->submitFormText = $this->pi_getLL('edit_failure', 'You cannot edit this message.');
			return false;
		}

		// set the category if is filled in
		if ($this->filledInVars['category'])
			$this->curCategory = $this->filledInVars['category'];

		// use  the enterNewRequest() function to handle it all.
		// only needs fields filled in
		$modify_content = $this->pi_getLL('modify_delete_header', '<h3>MODIFY</h3>');

		// if there were errors then show those
		if ($this->postvars['modify_err'])
			$modify_content .= '<span class="error">'.$this->pi_getLL('form_error').$this->html_entity_decode($this->postvars['modify_err']).'</span>';

		// add the request form to the content
		$modify_content .= $this->enterNewRequest();

		return $modify_content;
	}

	/**
	* ==================================================================================
	* Delete message -- handle deleting an existing request (only person who posted or admin)
	*
	* ==================================================================================
	*
	* @param	integer		$thisMsgID  message id to modify/delete
	* @return	void
	*/
	function deleteMessage($msgID) {
		// Verify that can delete the message
		// either must be moderator or the owner
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', $this->dataEntryTable, 'uid = '.intval($msgID), '');
		if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			if ($this->isAdministrator || (($this->userID != 0) && ($row['user_uid'] == $this->userID)) || ($this->config['who_can_edit'] == 3)) {
				// then delete it
				$delMsg['deleted'] = 1; // make this so deleted
				$where = "uid=". intval($msgID);
				$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery($this->dataEntryTable, $where, $delMsg);
				if (mysql_error()) {
					t3lib_div::debug(array(mysql_error(), 'DELETE FROM '.$this->dataEntryTable.' WHERE '.$where));
					$paramArray['tx_wecconnector']['msg'] = 6;
				}
				else {
					$paramArray['tx_wecconnector']['msg'] = 5;
				}
				header('Location:'.$this->getAbsoluteURL($this->id, $paramArray));
			}
			else
				$this->submitFormText = $this->pi_getLL('delete_cannot', 'You do not have permission to delete this message.');
		}
		else
			$this->submitFormText = $this->pi_getLL('delete_failure', 'You cannot delete that message.');
	}

	/**
	* ==================================================================================
	* Hide message -- handle hiding/unhiding an existing request (only person who posted or admin)
	*
	* ==================================================================================
	*
	* @param	integer		$msgID  	message id to hide
	* @param	integer		$doHide 	if message should be hidden or shown
	* @return	void
	*/
	function hideMessage($msgID, $doHide=1) {
		// Verify that can hide the message
		// either must be moderator or the owner
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', $this->dataEntryTable, 'uid = '.intval($msgID), '');
		if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			if ($this->isAdministrator || (($this->userID != 0) && ($row['user_uid'] == $this->userID)) || ($this->config['who_can_edit'] == 3)) {
				// then hide it
				$hideMsg['hidden'] = $doHide; // make this hidden or not
				$where = "uid=". intval($msgID);
				$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery($this->dataEntryTable, $where, $hideMsg);
				if (mysql_error()) {
					t3lib_div::debug(array(mysql_error(), 'HIDDEN FROM '.$this->dataEntryTable.' WHERE '.$where));
					$paramArray['tx_wecconnector']['msg'] = 8;
				}
				else {
					$paramArray['tx_wecconnector']['msg'] = ($doHide == 1) ? 9 : 10;
				}
				header('Location:'.$this->getAbsoluteURL($this->id, $paramArray));
			}
			else
				$this->submitFormText = $this->pi_getLL('hide_cannot', 'You do not have permission to hide this message.');
		}
		else
			$this->submitFormText = $this->pi_getLL('hide_failure', 'You cannot hide that message.');
	}

	/**
	* ==================================================================================
	* Send Requests -- send out requests to all subscribers of the email group. Will also send out
	* to notify_email if exists. This support sending multiple requests...
	*
	* ==================================================================================
	*
	* @param	array		$dataList array of email/name list of people to send to
	* @param	integer		$total  total number of email messages to send
	* @return	void
	*/
	function sendRequests($dataList, $total) {
		// First compose the header info
		//---------------------------------------------------
		$emailFrom = 'From: '; // hardcoded for mail()
		if ($this->config['contact_name'])
			$emailFrom .= $this->config['contact_name'].' <'.$this->config['contact_email'].'>';
		else if ($this->config['contact_email'])
			$emailFrom .= $this->config['contact_email'];
		else
			$emailFrom .=  $this->pi_getLL('email_none','<noreply@here.com>');
						
		if (($total > 1) && $this->config['entries_description']) {
			$thisDescription = $this->config['entries_description'];
		}
		else if ($this->config['entry_description']) {
			$thisDescription = $this->config['entry_description'];
		}
		else {
			$thisDescription = $this->pi_getLL('entry_description');
		}
		$emailSubject = $this->pi_getLL('new_emailSubject', 'New ') . $thisDescription;

		// Next, create the email message content
		//----------------------------------------------------
		$emailBody = $emailSubject.' '.$this->pi_getLL('email_received_text','has been received at:').' '.$this->getAbsoluteURL($this->id)."\n\n";

		if ($this->config['subscriber_emailHeader']) {
			$emailBody .= $this->config['subscriber_emailHeader'] . "\n";
		}

		for ($i = 0; $i < $total; $i++) {
			if ($dataList[$i]['subject'])
				$emailBody .= $this->pi_getLL('email_subject', 'Subject: ').$this->html_entity_decode(stripslashes($dataList[$i]['subject']))."\n";

			if ($thisCat = $dataList[$i]['category']) {
				$thisCatName = "";
				for ($k = 0; $k < $this->categoryCount; $k++) {
					if ($thisCat == $this->categoryList[$k]['uid']) {
						$thisCatName = $this->categoryList[$k]['name'];
						break;
					}
				}
				$emailBody .= $this->pi_getLL('email_category', 'Category: ').$thisCatName."\n";
			}
			if ($dataList[$i]['name'])
				$emailBody .= $this->pi_getLL('email_postedby', 'Posted By: ').$dataList[$i]['name']."\n";
			if ($dataList[$i]['city'] || $dataList[$i]['state'] || $dataList[$i]['zipcode'] || $dataList[$i]['country'] || $dataList[$i]['location']) {
				$emailBody .= $this->pi_getLL('email_locatedin', 'Located in: ');
				if ($dataList[$i]['location']) $emailBody .= $dataList[$i]['location'].' ';
				if ($dataList[$i]['city']) $emailBody .= $dataList[$i]['city'].', ';
				if ($dataList[$i]['state']) $emailBody .= $dataList[$i]['state'].' ';
				if ($dataList[$i]['zipcode']) $emailBody .= $dataList[$i]['zipcode'].' ';
				if ($dataList[$i]['country']) $emailBody .= $dataList[$i]['country'].' ';
				$emailBody .= "\n";
			}

			if ($dataList[$i]['message'])
				$emailBody .= "\n".$this->pi_getLL('email_message', 'Message: ').$this->html_entity_decode(stripslashes($dataList[$i]['message']))."\n";

			$emailBody .= "\n\n";
		}

		if ($this->config['subscriber_emailFooter'])
			$emailBody .= $this->config['subscriber_emailFooter'] . "\n";

		// Now load in the email subscriber list
		//-------------------------------------------------------------------------
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', $this->dataGroupTable, 'pid IN ('.$this->pid_list.')', '');
		$listCount = 0;
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$emailList[$listCount]['email'] = $row['user_email'];
			$emailList[$listCount]['name'] = $row['user_name'];
			$listCount++;
		}
		$header = $this->setEmailHeader();
		$emailSubject = t3lib_div::encodeHeader($emailSubject,'quoted_printable',$this->charset);

		// FINALLY, send out the email to the whole list
		//-------------------------------------------------------------------------
		for ($i = 0; $i < $listCount; $i++) {
			if ($emailList[$i]['name'])
				$toName = t3lib_div::encodeHeader($emailList[$i]['name'],'quoted_printable',$this->charset).' <' . $emailList[$i]['email'] . '>';
			else
				$toName = $emailList[$i]['email'];
			$sendEmailBody = $emailBody;
			$urlPar['tx_wecconnector']['sub'] = 2;
			$urlPar['tx_wecconnector']['email'] = htmlspecialchars($emailList[$i]['email']);
			$sendEmailBody .= $this->pi_getLL('email_unsubscribe', "\n-------------------------------------------\nTo be unsubscribed from this list and not receive these emails anymore, please click or go to this URL: ").$this->getAbsoluteURL($this->id, $urlPar);

			mail($toName,$emailSubject,$sendEmailBody,$emailFrom.$header);
		}

		// and then send out to notify email if listed
		if ($this->config['notify_email']) {
			$notifyEmailTo = t3lib_div::encodeHeader($this->config['notify_email'],'quoted_printable',$this->charset);
			mail($notifyEmailTo, $this->pi_getLL('email_notify', 'NOTIFY:').$emailSubject, $emailBody, $emailFrom.$header);
		}
		// and send out to admins if requested (but if already moderated, this will be sent, so do not resend)
		if ($this->config['email_admin_posts'] && !$this->config['do_moderate']) {
			$adminList = $this->getAdminInfo($this->config['administrator_group']);
			if (count($adminList)) {
				foreach ($adminList as $thisAdmin) {
					$adminEmailTo = t3lib_div::encodeHeader($thisAdmin['email'],'quoted_printable',$this->charset);
					mail($adminEmailTo, $this->pi_getLL('email_notify', 'NOTIFY:').$emailSubject, $emailBody, $emailFrom.$header);
				}
			}
		}
	}

	/**
	* ==================================================================================
	* Send Multiple Requests -- send out several requests to all subscribers of the email group.
	*   this is particularly useful if many messages get approved at once by a moderator...
	*
	* ==================================================================================
	*
	* @param	array		$msgList array of messages to send
	* @param	integer		$total  total number of email messages to send
	* @return	void
	*/
	function sendMultiRequests($msgList) {
		// LOOKUP MESSAGE IN DATABASE
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', $this->dataEntryTable, 'uid IN ('.$msgList.')');
		$count = 0;

		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			foreach ($this->db_fields AS $field) {
				$entry[$count][$field] = $row[$field];
			}
			$count++;
		}
		$this->sendRequests($entry, $count);
	}

	/**
	* ==================================================================================
	* Get Admin Info -- retrieves admin info from database. If already loaded, just returns
	*
	* ==================================================================================
	*
	* @param	string	$adminGroup comma-separated list of fe_users or emails
	* @return	array	$saveAdminList	array of name/email of admins.
	*/
	function getAdminInfo($adminGroup) {
		$saveAdminList = array();
		$adminList = t3lib_div::trimExplode(',', $adminGroup);
		if (!count($adminList))
			return $saveAdminList; // if empty group then don't send
		$searchList = '';
		$searchNameList = '';
		$searchIDList = '';
		// go through list and either grab email, user id, or user name.
		// for user id and name, save in list to lookup in fe_users
		for ($i = 0; $i < count($adminList); $i++) {
			if (strpos($adminList[$i],'@') !== FALSE) {
				$saveAdminList[] = array('email' => $adminList[$i]);
			}
			else {
				if ($this->ctype_digit_new(substr($adminList[$i], 0, 1))) {
					if (strlen($searchIDList)) $searchIDList .= ',';
					$searchIDList .=  "'" . $adminList[$i] . "'";
				}
				else {					
					if (strlen($searchNameList)) $searchNameList .= ',';
					$searchNameList .= "'" . $adminList[$i] . "'";
				}
			}
		}
		$listQuery = '';
		if (strlen($searchNameList)) $listQuery .= ' username IN (' . $searchNameList . ')';
		if (strlen($searchIDList)) $listQuery .= ((strlen($listQuery)) ? ' OR ' : '') . ' uid IN (' . $searchIDList . ')';
		$listQuery = strlen($listQuery) ? ' AND (' . $listQuery . ')' : '';
		$queryStr = 'deleted=0' . $listQuery;
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'fe_users', $queryStr, '');
		$listCount = count($saveAdminList);
		
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			if ($row['email']) {
				$saveAdminList[$listCount]['email'] = $row['email'];
				$saveAdminList[$listCount++]['name'] = $row['name'];
			}
		}		

		return $saveAdminList;
	}

	/**
	* ==================================================================================
	* Send To Moderators -- send message to moderators for their action.
	*
	* ==================================================================================
	*
	* @param	string		$modGroup comma-list of moderator names/uids/emails
	* @param	string		$msgName name of person
	* @param	string		$msgEmail email of person
	* @param	string		$msgSubject email subject
	* @param	string		$msgText message
	* @return	void
	*/
	function sendToModerators($modGroup, $msgName, $msgEmail, $msgSubject, $msgText) {
		$msgSubject = $this->html_entity_decode(stripslashes($msgSubject));
		$msgText = $this->html_entity_decode(stripslashes($msgText));

		$modList = $this->getAdminInfo($modGroup);
		if (!count($modList))
			return;

		// COMPOSE THE EMAIL FROM CONTACT/OWNER
		$emailBody = $this->pi_getLL('email_moderateMsgHeader', "Need to Moderate New Message:\n\n") . $this->pi_getLL('email_from', 'From: ') . $msgName . ' (email: '.$msgEmail.")\n\n" . $this->pi_getLL('email_subject', 'Subject: ') . $msgSubject . "\n\n" . $this->pi_getLL('email_message', 'Message: ') . $msgText;

		// Add the link to moderate
		$paramArray['tx_wecconnector']['moderate'] = 1;
		$gotoLink = $this->getAbsoluteURL($this->id, $paramArray);
		$emailBody .= "\n\n".$this->pi_getLL('email_gotolink', 'Go to link: ').$gotoLink;

		$emailFrom = $this->pi_getLL('email_from', 'From: ');
		if ($this->config['contact_name'])
			$emailFrom .= $this->config['contact_name'].' <' . $this->config['contact_email'] . '>';
		else if ($this->config['contact_email'])
			$emailFrom .= $this->config['contact_email'];
		else
			$emailFrom .=  $this->pi_getLL('email_none','<noreply@here.com>');

		$emailSubject = $this->pi_getLL('email_moderateMsg', 'Moderate Message For ').$this->config['connector_name'];

		$header = $this->setEmailHeader();

		// FINALLY, send out the email to the whole moderator list
		for ($i = 0; $i < count($modList); $i++) {
			if ($modList[$i]['name'])
				$toName = $modList[$i]['name'] . ' <' . $modList[$i]['email'] . '>';
			else
				$toName =  $modList[$i]['email'];
			mail($toName, $emailSubject, $emailBody, $emailFrom . $header);
		}
	}

	/**
	* ==================================================================================
	* Moderate Messages -- allows the moderator to process the moderation queue and either approve
	*   or disapprove of messages
	*
	* ==================================================================================
	*
	* @return	string		moderation form content
	*/
	function moderateMessages() {
		// grab all message that can moderate
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', $this->dataEntryTable, 'moderationQueue>0 AND pid IN('.$this->pid_list.')');
		$count = 0;
		$db_fields = array('uid', 'name', 'subject', 'email', 'message', 'phone', 'address', 'city', 'state', 'zipcode', 'country', 'website_url', 'business_name', 'contact_name', 'is_response');
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			foreach ($db_fields as $field) {
				$modList[$count][$field] = $row[$field];
			}
			$count++;
		}
		
		// display  on screen with ability to mark as approve or delete
		$mod_content = '
		<script type="text/javascript">
			//<![CDATA[
			function SetAll(typename,val) {
				theForm = document.moderateMsgs;
				len = theForm.elements.length;
				for (var i = 0; i < len; i++) {
					if (theForm.elements[i].value.substr(0,3) == typename) {
						theForm.elements[i].checked=val;
					}
				}
			}
			//]]>
		</script>
			' ;

		// nothing below this
		$mod_content .= '<FORM name="moderateMsgs" method="POST" action="'.$this->getAbsoluteURL($this->id).'">
			<INPUT type="hidden" name="tx_wecconnector[processModerated]" value="'.$count.'">
			<TABLE border=0 cellspacing=0 cellpadding=0 style="width:100%">
			<TR><TD style="width:40px;height:35px" align=center valign=center>Approve/</TD>
				<TD style="width:40px;"  align=center valign=center>Delete</TD>
				<TD  align=center valign=center bgColor="#888888"><b>MODERATE MESSAGES</b></TD>
			</TR>
			<TR><TD colspan=3 height=3 bgColor="#444"></TD></TR>
			';

		// go through and place each entry in a box with all info
		//
		for ($i = 0; $i < $count; $i++) {
			$action = 'add';
			if ($modList[$i]['is_response'] >= 1) {
				$action = 'rsp';
				$modList[$i]['subject'] = 'RESPONSE TO MESSAGE...';
			}
			$mod_content .= '<TR>
				<TD align=center><INPUT type="radio" name="modMsg'.$i.'" value="'.$action.$modList[$i]['uid'].'"></TD>
				<TD align=center><INPUT type="radio" name="modMsg'.$i.'" value="del'.$modList[$i]['uid'].'"></TD>
				<TD align=left>
				<b>SUBJECT</b>: '.stripslashes($modList[$i]['subject']). '<div style="display:inline;margin-left:20px;"><b>FROM</b>: '.$modList[$i]['name'].' (email=\''.$modList[$i]['email'].'\')</div>
				<br/>
				<b>MESSAGE</b>: '.stripslashes($modList[$i]['message']).'
				<br/>
				<font size=-1>
				';
			$small_fields = array('phone', 'address', 'city', 'state', 'zipcode', 'country', 'website_url', 'business_name', 'contact_name', 'category', 'is_response');
			foreach ($small_fields as $showField) {
				if ($modList[$i][$showField])
					$mod_content .= '<b>'.$showField.'</b>:'.$modList[$i][$showField].'&nbsp;&nbsp;';
			}
			$mod_content .= '
				</font>
				</TD>
				</TR>
				';
			$mod_content .= '<TR><TD colspan=3 height=2 bgColor="#444"></TD></TR>';
		}

		$backURL = 'location.href=\''.$this->getAbsoluteURL($this->id).'\';';
		if ($count == 0) {
			$mod_content .= '<tr><td colspan=3 align=center valign=center height=50>No messages to moderate</td></tr>
				<tr><td colspan=3 align=center>
				<input type="button" value="Go Back" onclick="'.$backURL.'"/>
				</td></tr>
				';
		} else {
			// allow to submit in form
			$mod_content .= '<TR>
				<TD align=center><a href="#" onclick="SetAll(\'add\',1);SetAll(\'rsp\',1);return false;"><font size=-2>Approve All</font></a></TD>
				<TD align=center><a href="#" onclick="SetAll(\'del\',1);return false;"><font size=-2>Delete All</font></a></TD>
				<TD align=center>
				<BR/>
				<input type="submit" value="Process"/>
				<input type="button" value="Cancel" onclick="'.$backURL.'"/>
				';
		}
		$mod_content .= '</FORM>
			</TD></TR></TABLE>';

		return $mod_content;

	}

	/**
	* ==================================================================================
	* Process Moderated Messages -- does the action based on what moderator action in form
	*
	* ==================================================================================
	*
	* @param	string		$msgList list of messages to process moderation on
	* @param	integer		$msgCount number of messages to process
	* @return	void
	*/
	function processModerated($msgList, $msgCount) {
		$msgAddList = '';
		$msgDelList = '';
		$msgRespList = '';
		// go through each message and process it -- either add, del, or rsp -- by adding to correct list
		for ($i = 0; $i < $msgCount; $i++) {
			if ($msgVal = $msgList['modMsg'.$i]) {
				$msgNum = substr($msgVal, 3);
				$msgAction = substr($msgVal, 0, 3);
				if ($msgAction == 'add') {
					if (strlen($msgAddList) > 0) $msgAddList .= ',';
					$msgAddList .= $msgNum;
				} else if ($msgAction == 'rsp') {
					if (strlen($msgRespList) > 0) $msgRespList .= ',';
					$msgRespList .= $msgNum;
				} else {
					if (strlen($msgDelList) > 0) $msgDelList .= ',';
					$msgDelList .= $msgNum;
				}
			}
		}
		if (strlen($msgAddList) > 0) {
			$updData['moderationQueue'] = 0;
			$update = $GLOBALS['TYPO3_DB']->exec_UPDATEquery($this->dataEntryTable, "uid IN (".$msgAddList.")", $updData);

			// now go through and let subscribers know about all new messages
			// we are going to try to put them  all together so they will receive only one email, not many
			$this->sendMultiRequests($msgAddList);
		}
		
		// delete all messages marked to be deleted
		if (strlen($msgDelList) > 0)
			$GLOBALS['TYPO3_DB']->exec_DELETEquery($this->dataEntryTable, "uid IN (".$msgDelList.')');
			
		// send all responses...
		if (strlen($msgRespList) > 0) {
			$this->sendResponseMessages($msgRespList);
		}
	}

	/**
	* ==================================================================================
	* Subscribe form -- show the email subscribe/unsubscribe form
	*
	* ==================================================================================
	*
	* @return	string		return the content to display form
	*/
	function subscribeForm() {
		// extract subscribe form out of template
		//
		$templateFormContent = $this->cObj->getSubpart($this->templateCode, '###TEMPLATE_SUBSCRIBEFORM###');
		$subpartArray = array();

		// if any error messages, then display
		if ($this->submitFormText) {
			$markerArray['###FORM_ERROR###'] = $this->submitFormText;
		}
		else {
			$subpartArray['###SHOW_ERROR###'] = '';
		}		

		// now fill in all the markers
		$substituteArray = array('name', 'email', 'submit_sub', 'submit_unsub', 'cancel');
		foreach ($substituteArray AS $marker) {
			$markerArray['###FORM_'.strtoupper($marker).'###'] = $this->pi_getLL('subscribeform_'.$marker);
		}
		$markerArray['###SUBSCRIBE_HEADER###'] = $this->config['subscribe_header'];
		$markerArray['###PID###'] = $this->id;
		$markerArray['###ACTION_URL###'] = $this->getAbsoluteURL($this->id);

		// Pre-fill form data if FE user is logged in
		if (!$this->postvars['name'] && !$this->postvars['email'] && $GLOBALS['TSFE']->loginUser) {
			$surname_pos = strpos($GLOBALS['TSFE']->fe_user->user['name'], ' ');
			$markerArray['###VALUE_NAME###'] = substr($GLOBALS['TSFE']->fe_user->user['name'], 0, $surname_pos);
			$markerArray['###VALUE_EMAIL###'] = $GLOBALS['TSFE']->fe_user->user['email'];
		}

		// add cancel URL
		$getvarsE = t3lib_div::_GET();
		unset($getvarsE['id']);
		unset($getvarsE['tx_wecconnector']['sub']);
		$markerArray['###CANCEL_URL###'] = 'location.href=\''.$this->getAbsoluteURL($this->id, $getvarsE).'\'';

		// then do the substitution with the template
		$formContent = $this->cObj->substituteMarkerArrayCached($templateFormContent, $markerArray, $subpartArray, array());

		// clear out any empty template fields
		$formContent = preg_replace('/###.*?###/', '', $formContent);
		
		return $formContent;
	}

	/**
	* ==================================================================================
	* Subscribe to the email group -- add user to subscriber group
	*
	* ==================================================================================
	*
	* @param	string		$thisName name of person to subscribe
	* @param	string		$thisEmail email of person to subscribe
	* @return	boolean		return if subscription was successful
	*/
	function subscribeToGroup($thisName, $thisEmail) {
		if (strlen($thisEmail) < 2) {
			$this->submitFormText = $this->pi_getLL('subscribe_error1', 'Please provide your email address.');
			return false;
		}
		if (t3lib_div::validEmail($thisEmail) == false) {
			$this->submitFormText = $this->pi_getLL('subscribe_error2', 'Please provide a valid email in the form name@web.com');
			return false;
		}

		// first check to see if email or userID is already there
		if ($this->checkIfSubscribed($thisEmail)) {
			$this->submitFormText = $this->pi_getLL('subscribe_error3', 'You are already subscribed to this group.');
			$this->isSubscribed = true;
			return true;
		}

		// adding the email to subscriber group
		$saveData['pid'] = $this->pid_list;
		$saveData['user_uid'] = $this->userID;
		$saveData['user_email'] = htmlspecialchars($thisEmail);
		$saveData['user_name'] = $thisName;
		$saveData['crdate'] = mktime();
		$saveData['tstamp'] = mktime();
		$insert = $GLOBALS['TYPO3_DB']->exec_INSERTquery($this->dataGroupTable, $saveData);
		if (mysql_error()) {
			t3lib_div::debug(array(mysql_error(), $insert), 'mySQL Error in Insert');
			return false;
		}
		$this->submitFormText = $this->pi_getLL('subscribe_success', 'You are now subscribed to this group.');
		$this->isSubscribed = true;
		$this->postvars = 0; // clear out post vars
		return true;
	}

	/**
	* ==================================================================================
	* Check if subscribed to this email group
	*
	* ==================================================================================
	*
	* @param	string		$thisEmail email of person to check
	* @return	boolean		return if subscribed or not
	*/
	function checkIfSubscribed($thisEmail) {
		// first check to see if email or userID is already there
		$selectStr = 'pid IN ('.$this->pid_list.') ';
		$selectStr .= ' AND user_email=\''.$GLOBALS['TYPO3_DB']->quoteStr($thisEmail, $this->dataGroupTable).'\'';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', $this->dataGroupTable, $selectStr, '');
		if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			return true;
		}
		return false;
	}

	/**
	* ==================================================================================
	* Unsubscribe from the email group -- remove user from subscriber group
	*
	* ==================================================================================
	*
	* @param	string		$thisEmail email of person to subscribe
	* @return	boolean		return if unsubscription was successful
	*/
	function unsubscribeFromGroup($thisEmail) {
		if (strlen($thisEmail) < 2) {
			$this->submitFormText = $this->pi_getLL('subscribe_error1', 'Please provide your email address.');
			return false;
		}

		if (t3lib_div::validEmail($thisEmail) == false) {
			$this->submitFormText = $this->pi_getLL('subscribe_error2', 'Please provide a valid email in the form name@web.com (ie. mothergoose@aol.com, jacksprat@yahoo.com)');
			return false;
		}

		// lookup email
		$selectStr = "user_email='".htmlspecialchars($thisEmail)."' AND pid IN (".$this->pid_list.')';
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', $this->dataGroupTable, $selectStr, '');
		if ($count = $GLOBALS['TYPO3_DB']->sql_num_rows($res)) {
			$GLOBALS['TYPO3_DB']->exec_DELETEquery($this->dataGroupTable, $selectStr);
			$this->submitFormText = $this->pi_getLL('unsubscribe_success', 'You were successfully unsubscribed from this group.');
			$this->isSubscribed = false;
			return true;
		} else {
			$this->submitFormText = $this->pi_getLL('unsubscribe_error', 'You are not subscribed -- we could not find your email. Please check the spelling of the e-mail address and try again:')." '".$thisEmail."'";
			return false;
		}
	}

	/**
	* ==================================================================================
	* Do Admin Functions
	*     currently supports: show subscriber list
	*
	* @return	string		return content
	* ==================================================================================
	*
	* @return	none
	*/
	function doAdmin() {
		$admContent = '<div class="pluginSection">';
		$admContent .= '<h1>'.$this->pi_getLL('admin_menu_header','Admin Menu').'</h1>';
		if ($this->postvars['show_subscribers']) {
			// Load in the email subscriber list
			//-------------------------------------------------------------------------
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', $this->dataGroupTable, 'pid IN ('.$this->pid_list.')', '', 'user_email');
			$listCount = 0;
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$subscrContent .= $row['user_email'];
				if ($row['user_name'])
					$subscrContent .= ' "'.$row['user_name'] .'"';
				$subscrContent .= "\n";
				$listCount++;
			}

			// then display all subscribers  <name> <email>
			$admContent .= '<div>'.$this->pi_getLL('admin_show_subscribers_header','List Of Subscribers To This Connector').'</div>';
			if ($listCount)
				$admContent .= '<textarea style="width:400px;height:250px;">'.$subscrContent.'</textarea>';
			else
				$admContent .= '<div  class="fullWidth">'.$this->pi_getLL('admin_subscribers_none','no subscribers yet').'</div>';
			$admContent .= '<br />';

			unset($params['tx_wecconnector']['admin']);
			unset($params['tx_wecconnector']['show_subscribers']);
			$adminURL = $this->pi_getPageLink($this->id,'',$params);
			$admContent .= '<div  class="fullWidth"><a class="button" href="'.$adminURL.'"><span class="label prevIcon">' . $this->pi_getLL('admin_exit','Exit') . '</span></a></div>';
			$admContent .= '<br/>';
		}
		else {
			$params = t3lib_div::_GET();
			$params['tx_wecconnector']['show_subscribers'] = 1;
			$adminURL = $this->pi_getPageLink($this->id,'',$params);
			$admContent .= '<div class="fullWidth"><a class="button" href="'.$adminURL.'"><span class="label subscribeIcon">' . $this->pi_getLL('admin_show_subscribers','Show Subscribers') . '</span></a></div>';

			unset($params['tx_wecconnector']['admin']);
			unset($params['tx_wecconnector']['show_subscribers']);
			$adminURL = $this->pi_getPageLink($this->id,'',$params);
			$admContent .= '<div class="fullWidth"><a class="button" href="'.$adminURL.'"><span class="label prevIcon">' . $this->pi_getLL('admin_exit','Exit') . '</span></a></div>';

		}
		$admContent .= '</div>';
		
		return $admContent;
	}

	/**
	*==================================================================================
	*  Display RSS Feed
	*
	* 	@return string RSS feed content
	*==================================================================================
	*/
	function displayRSSFeed() {
		$rss_content = "";

		$rssTemplateFile = $this->conf['rssTemplateFile'];
		if (!$rssTemplateFile)
			return $this->pi_getLL('no_rss_template_file',"No RSS Template File configured.");

		// current page where connector is
		$gotoPageID = $this->config['preview_backPID'] ? $this->config['preview_backPID'] : $this->id;

		// this is set for enabling relative URLs for images and links in the RSS feed.
		$sourceURL = $this->conf['xml.']['rss.']['source_url'] ?  $this->conf['xml.']['rss.']['source_url'] : t3lib_div::getIndpEnv('TYPO3_SITE_URL');

		// load in template
		$rssTemplateCode = $this->cObj->fileResource($rssTemplateFile);
		$rssTemplate = $this->cObj->getSubpart($rssTemplateCode, '###TEMPLATE_RSS2###');

		// fill in template
		$dataArray = array('CHANNEL_TITLE','CHANNEL_LINK','CHANNEL_DESCRIPTION','LANGUAGE','NAMESPACE_ENTRIES','COPYRIGHT','DOCS','CHANNEL_CATEGORY','MANAGING_EDITOR','WEBMASTER','CHANNEL_IMAGE','TTL');
		for ($i = 0; $i < count($dataArray); $i++) {
			$rssField = $dataArray[$i];
			$linkField = strtolower(str_replace('CHANNEL_','',$rssField));
			if ($val = $this->conf['xml.']['rss.'][strtolower($rssField)]) {
				$markerArray['###'.strtoupper($rssField).'###'] = '<'.$linkField.'>'.$val.'</'.$linkField.'>';
			}
		}
		$charset = ($GLOBALS['TSFE']->metaCharset?$GLOBALS['TSFE']->metaCharset:'iso-8859-1');
		$markerArray['###XML_CHARSET###'] = ' encoding="'.$charset.'"';

		// fill in defaults...if not set
		$markerArray['###GENERATOR###'] = $this->conf['xml.']['rss.']['generator'] ? $this->conf['xml.']['rss.']['generator'] : 'TYPO3 v4 CMS';
		$markerArray['###XMLNS###'] = $this->conf['xml.']['rss.']['xmlns'];
		$markerArray['###XMLBASE###'] = 'xml:base="'.$sourceURL.'"';
		$markerArray['###GEN_DATE###'] = date('D, d M Y h:i:s T');
		if (!$markerArray['###CHANNEL_TITLE###'])
			$markerArray['###CHANNEL_TITLE###'] = '<title>'. ($this->config['title'] ? $this->config['title'] : $this->conf['title']) .'</title>';
		if (!$markerArray['###CHANNEL_LINK###'])
			$markerArray['###CHANNEL_LINK###'] = '<link>'.$sourceURL.'</link>';
		$markerArray['###CHANNEL_GENERATOR###'] = '<generator>'.$markerArray['###GENERATOR###'].'</generator>';

		// grab item template
		$itemTemplate = $this->cObj->getSubpart($rssTemplateCode, '###ITEM###');

		// grab messages
		$pidList = t3lib_div::_GP('sp') ? t3lib_div::_GP('sp') : $this->pid_list;
		$order_by = 'post_date DESC';
		$where = ' pid IN (' . $pidList . ') ';
		$where .= ' AND moderationQueue=0 AND is_response=0 ';
		if ($daysToKeep = $this->config['days_to_keep']) {
			$lastDate = mktime(0, 0, 0, date('m'), date('d')-$daysToKeep, date('y'));
			$where .= ' AND post_date >=' . $lastDate . ' ';
		}

		$where .= $this->cObj->enableFields($this->dataEntryTable);
		// handle languages
		$lang = ($l = $GLOBALS['TSFE']->sys_language_uid) ? $l : '0,-1';
		$where .= ' AND sys_language_uid IN ('.$lang.') ';
		$limit = $this->conf['numRSSItems'] ? $this->conf['numRSSItems'] : 5;
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', $this->dataEntryTable, $where, '', $order_by, $limit);
		if (mysql_error()) 
			t3lib_div::debug(array(mysql_error(), "SELECT ".$selFields.' FROM '.$this->dataEntryTable.' WHERE '.$where.' ORDER BY '.$order_by.' LIMIT '.$limit));

		// fill in item
		$item_content = "";
		$mostRecentMsgDate = 0;
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$postByName = $row['name'] ? htmlspecialchars(stripslashes($row['name'])) : $this->pi_getLL('author_anonymous','Anonymous');
			$msgText = $row['message'];
			$msgText .= '<br/><br/>' . $this->pi_getLL('postedby_text', 'Posted By:') . ' ' . $postByName;
			if (is_array($this->conf['general_stdWrap.'])) {
				$msgText = str_replace('&nbsp;',' ',$msgText);
				$msgText = $this->cObj->stdWrap($this->html_entity_decode($msgText,ENT_QUOTES), $this->conf['general_stdWrap.']);
			}


			$titleStr = $row['subject'] ? htmlspecialchars(stripslashes($row['subject'])) : htmlspecialchars(substr($row['message'],0,16));
			$itemMarker['###ITEM_TITLE###'] = '<title>' . $titleStr . '</title>';
			$urlParams = array();
			$hashParams = '';
			$urlParams['tx_wecconnector']['single'] = $row['uid'];
			$itemMarker['###ITEM_LINK###'] = '<link>' . htmlspecialchars($this->getAbsoluteURL($gotoPageID,$urlParams,TRUE)) . '</link>';

			
			$itemMarker['###ITEM_DESCRIPTION###'] = '<description>' . htmlspecialchars(stripslashes($msgText)) . '</description>';
			$itemMarker['###ITEM_AUTHOR###'] = '<author>' . $postByName . '</author>';
			if (!empty($row['category']) && $this->categoryListByUID[$row['category']])
				$itemMarker['###ITEM_CATEGORY###'] = '<category>' . $this->categoryListByUID[$row['category']] . '</category>';
			$itemMarker['###ITEM_COMMENTS###'] = '';
			$itemMarker['###ITEM_ENCLOSURE###'] = '';
			$itemMarker['###ITEM_PUBDATE###'] = '<pubDate>' . date('D, d M Y H:i:s O',$row['post_date']) . '</pubDate>';
			$itemMarker['###ITEM_GUID###'] = '<guid isPermaLink="true">' . $this->getAbsoluteURL($gotoPageID,$urlParams, TRUE) . '</guid>';
			$itemMarker['###ITEM_SOURCE###'] = '<source url="' . $sourceURL . '">' . htmlspecialchars($row['subject']) . '</source>';

			if ($mostRecentMsgDate < $row['post_date'])
				$mostRecentMsgDate = $row['post_date'];

			// generate item info
			$item_content .= $this->cObj->substituteMarkerArrayCached($itemTemplate,$itemMarker,array(),array());
		}
		$subpartArray['###ITEM###'] = $item_content;
		if ($mostRecentMsgDate)
			$markerArray['###LAST_BUILD_DATE###'] = '<pubDate>' . date('D, d M Y H:i:s O', $mostRecentMsgDate) . '</pubDate>';

		// then substitute all the markers in the template into appropriate places
		$rss_content = $this->cObj->substituteMarkerArrayCached($rssTemplate,$markerArray,$subpartArray, array());

		// clear out any empty template fields (so if ###CONTENT1### is not substituted, will not display)
		$rss_content = preg_replace('/###.*?###/', '', $rss_content);

		// remove blank lines
		$rss_content = preg_replace("/(^[\r\n]*|[\r\n]+)[\s\t]*[\r\n]+/", "\n", $rss_content);

		// make certain tags XHTML compliant
		$rss_content = preg_replace("/<(img|hr|br|input)([^>]*)>/mi", "<$1$2 />", $rss_content);

		return $rss_content;
	}
	
	/**
	* Getting the full URL (ie. http://www.host.com/... to the given ID with all needed params
	*
	* @param	integer		$id: Page ID
	* @param	string		$urlParameters: array of parameters to include in the url (i.e., "$urlParameters['action'] = 4" would append "&action=4")
	* @return	string		$url: URL
	*/
	function getAbsoluteURL($id, $urlParameters = '') {
		$serverProtocol = t3lib_div::getIndpEnv('TYPO3_SSL') ? 'https://' : 'http://';
		$dn = dirname($_SERVER['PHP_SELF']);
		if (($dn != '/') && ($dn != '\\'))
			$url = $serverProtocol . $_SERVER['HTTP_HOST'] . $dn . '/';
		else
			$url = $serverProtocol . $_SERVER['HTTP_HOST'] . '/';

		$pageUrl = $this->pi_getPageLink($id, '', $urlParameters);
		if (strpos($pageUrl,"http") === FALSE)
			$url .= $pageUrl;
		else // crosses boundaries (likely different url on same server)
			$url = $pageUrl;

		$url = str_replace('&','&amp;', $url);

		return $url;
	}

	/**
	* if string is url or not
	*
	* @param	string		$url  string to test if url
	* @return	boolean		if url or not
	*/
	function isURL($url) {
		return preg_match('#^http\\:\\/\\/[a-z0-9\-]+\.([a-z0-9\-]+\.)?[a-z]+#i', $url);
	}


	/**
	 * getConfigVal: Return the value from either plugin flexform, typoscript, or default value, in that order
	 *
	 * @param	object		$Obj: Parent object calling this function
	 * @param	string		$ffField: Field name of the flexform value
	 * @param	string		$ffSheet: Sheet name where flexform value is located
	 * @param	string		$TSfieldname: Property name of typoscript value
	 * @param	array		$lConf: TypoScript configuration array from local scope
	 * @param	mixed		$default: The default value to assign if no other values are assigned from TypoScript or Plugin Flexform
	 * @return	mixed		Configuration value found in any config, or default
	 */
	function getConfigVal( &$Obj, $ffField, $ffSheet, $TSfieldname='', $lConf='', $default = '' ) {
		if (!$lConf && $Obj->conf) $lConf = $Obj->conf;
		if (!$TSfieldname) $TSfieldname = $ffField;

		//	Retrieve values stored in flexform and typoscript
		$ffValue = $Obj->pi_getFFvalue($Obj->cObj->data['pi_flexform'], $ffField, $ffSheet);
		$tsValue = $lConf[$TSfieldname];

		//	Use flexform value if present, otherwise typoscript value
		$retVal = $ffValue ? $ffValue : $tsValue;

			//	Return value if found, otherwise default
		return $retVal ? $retVal : $default;
	}
	/**
	* Mark required fields -- all required fields are marked in form
	*
	* @param	array		$markerArray: current marker array to add onto
	* @param	array		$reqFields: array of fields that are required
	* @return	array		updated marker array
	*/
	function markRequiredFields($markerArray, $reqFields) {
		if (is_array($reqFields) && count($reqFields) > 0) {
			foreach ($reqFields AS $req_field) {
				$markerArray['###FORM_'.strtoupper($req_field).'_REQUIRED###'] = '*';
			}
			$markerArray['###SHOW_REQUIRED_TEXT###'] = $this->pi_getLL('show_required_text', '<span class="required">*</span> = required field');
		}
		return $markerArray;
	}

	function html_entity_decode($str,$quoteStyle=ENT_COMPAT) {
		if (version_compare(phpversion(),"4.3.0",">="))
			return html_entity_decode($str,$quoteStyle, $GLOBALS['TSFE']->renderCharset);

	    // replace numeric entities
	    $string = preg_replace('~&#x([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $str);
	    $string = preg_replace('~&#([0-9]+);~e', 'chr("\\1")', $str);
	    // replace literal entities
	    $trans_tbl = get_html_translation_table(HTML_ENTITIES);
	    $trans_tbl = array_flip($trans_tbl);
	    return strtr($str, $trans_tbl);
	}

	/**
	*==================================================================================
	*  ctype_digit_new -- handle it better because of problems with PHP
	*
	* 	@return boolean
	*==================================================================================
	*/	
	function ctype_digit_new($str) {
	    return (is_string($str) || is_int($str) || is_float($str)) &&
	        preg_match('/^\d+\z/', $str);
	}
	
	/**
	*==================================================================================
	*  Remove any potential XSS content
	*	taken from t3lib_div.php to be in 4.3
	*
	*	@param  string $val	string to clean
	* 	@return string		cleaned string
	*==================================================================================
	*/
	function removeXSS($val)	{
		$replaceString = '<x>';

		// remove all non-printable characters. CR(0a) and LF(0b) and TAB(9) are allowed
		// this prevents some character re-spacing such as <java\0script>
		// note that you have to handle splits with \n, \r, and \t later since they *are* allowed in some inputs
		$val = preg_replace('/([\x00-\x08][\x0b-\x0c][\x0e-\x19])/', '', $val);

		// straight replacements, the user should never need these since they're normal characters
		// this prevents like <IMG SRC=&#X40&#X61&#X76&#X61&#X73&#X63&#X72&#X69&#X70&#X74&#X3A&#X61&#X6C&#X65&#X72&#X74&#X28&#X27&#X58&#X53&#X53&#X27&#X29>
		$search = '/&#[xX]0{0,8}(21|22|23|24|25|26|27|28|29|2a|2b|2d|2f|30|31|32|33|34|35|36|37|38|39|3a|3b|3d|3f|40|41|42|43|44|45|46|47|48|49|4a|4b|4c|4d|4e|4f|50|51|52|53|54|55|56|57|58|59|5a|5b|5c|5d|5e|5f|60|61|62|63|64|65|66|67|68|69|6a|6b|6c|6d|6e|6f|70|71|72|73|74|75|76|77|78|79|7a|7b|7c|7d|7e);?/ie';
		$val = preg_replace($search, "chr(hexdec('\\1'))", $val);
		$search = '/&#0{0,8}(33|34|35|36|37|38|39|40|41|42|43|45|47|48|49|50|51|52|53|54|55|56|57|58|59|61|63|64|65|66|67|68|69|70|71|72|73|74|75|76|77|78|79|80|81|82|83|84|85|86|87|88|89|90|91|92|93|94|95|96|97|98|99|100|101|102|103|104|105|106|107|108|109|110|111|112|113|114|115|116|117|118|119|120|121|122|123|124|125|126);?/ie';
		$val = preg_replace($search, "chr('\\1')", $val);

		// now the only remaining whitespace attacks are \t, \n, and \r
		$ra1 = array('javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base', 'onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
		$ra_tag = array('applet', 'meta', 'xml', 'blink', 'link', 'style', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base');
		$ra_attribute = array('style', 'onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload');
		$ra_protocol = array('javascript', 'vbscript', 'expression');
		//remove the potential &#xxx; stuff for testing
		$val2 = preg_replace('/(&#[xX]?0{0,8}(9|10|13|a|b);)*\s*/i', '', $val);
		$ra = array();
		foreach ($ra1 as $ra1word) {
			//stripos is faster than the regular expressions used later
			//and because the words we're looking for only have chars < 0x80
			//we can use the non-multibyte safe version
			//(use strpos here because stripos is only for PHP 5)
			if (strpos(strtolower($val2), strtolower($ra1word)) !== false) {
				//keep list of potential words that were found
				if (in_array($ra1word, $ra_protocol)) {
					$ra[] = array($ra1word, 'ra_protocol');
				}
				if (in_array($ra1word, $ra_tag)) {
					$ra[] = array($ra1word, 'ra_tag');
				}
				if (in_array($ra1word, $ra_attribute)) {
					$ra[] = array($ra1word, 'ra_attribute');
				}
				//some keywords appear in more than one array
				//these get multiple entries in $ra, each with the appropriate type
			}
		}

		// remove all comments
		$val = preg_replace("!/\*.*?\*/!s", '', $val);
		// remove <style> tags
		$val = preg_replace('/\>&lt;*style.*?\&lt;\/style*\&gt;/ism', '', $val);

		// now the only remaining whitespace attacks are \t, \n, and \r
		//only process potential words
		if (count($ra) > 0) {
			// keep replacing as long as the previous round replaced something
			$found = true;
			while ($found == true) {
				$val_before = $val;
				for ($i = 0; $i < sizeof($ra); $i++) {
					$pattern = '';
					for ($j = 0; $j < strlen($ra[$i][0]); $j++) {
						if ($j > 0) {
							$pattern .= '((&#[xX]0{0,8}([9ab]);)|(&#0{0,8}(9|10|13);)|\s)*';
						}
						$pattern .= $ra[$i][0][$j];
 					}
					//handle each type a little different (extra conditions to prevent false positives a bit better)
					switch ($ra[$i][1]) {
						case 'ra_protocol':
							//these take the form of e.g. 'javascript:'
							$pattern .= '((&#[xX]0{0,8}([9ab]);)|(&#0{0,8}(9|10|13);)|\s)*(?=:)';
							break;
						case 'ra_tag':
							//these take the form of e.g. '<SCRIPT[^\da-z] ....';
							$pattern = '(?<=<)' . $pattern . '((&#[xX]0{0,8}([9ab]);)|(&#0{0,8}(9|10|13);)|\s)*(?=[^\da-z])';
							break;
						case 'ra_attribute':
							//these take the form of e.g. 'onload='  Beware that a lot of characters are allowed
							//between the attribute and the equal sign!
							$pattern .= '[\s\!\#\$\%\&\(\)\*\~\+\-\_\.\,\:\;\?\@\[\/\|\\\\\]\^\`]*(?==)';
							break;
					}
					$pattern = '/' . $pattern . '/i';
					// add in <x> to nerf the tag
					$replacement = substr_replace($ra[$i][0], $replaceString, 2, 0);
					// filter out the hex tags
					$val = preg_replace($pattern, $replacement, $val);
					if ($val_before == $val) {
						// no replacements were made, so exit the loop
						$found = false;
					}
				}
			}
		}

		return $val;
	}
}
?>
