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
 * Authentification class for the XML-RPC Server
 *
 * @author    Ingo Renner <typo3@ingo-renner.com>
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   49: class tx_timtab_pi2_xmlrpcAuth extends t3lib_beuserauth
 *   67:     function initAuth($username, $password)
 *   82:     function getUser()
 *  102:     function authUser()
 *  117:     function authLikeInInit()
 *
 * TOTAL FUNCTIONS: 4
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

require_once(PATH_t3lib.'class.t3lib_userauth.php');
require_once(PATH_t3lib.'class.t3lib_userauthgroup.php');
require_once(PATH_t3lib.'class.t3lib_beuserauth.php');

class tx_timtab_pi2_xmlrpcAuth extends t3lib_beuserauth {
	var $loginType = 'BE';
	var $security_level = 'normal';
	var $writeAttemptLog = true;
	var $writeDevLog = true;

	var $xmlrpcLoginData;
	var $xmlrpcAuthInfo;
	var $xmlrpcUser;


	/**
	 * initialize login data
	 *
	 * @param	string		username the username
	 * @param	string		password clear text password
	 * @return	void
	 */
	function initAuth($username, $password) {
		$this->xmlrpcLoginData = array(
			'uname'  => $username,
			'uident' => md5($password),
			'status' => 'login',
		);

		$this->xmlrpcAuthInfo = $this->getAuthInfoArray();
	}

	/**
	 * get a BE user, will return false on failure
	 *
	 * @return	user		object on success, false otherwise
	 */
	function getUser() {

		if(is_object($serviceObj = t3lib_div::makeInstanceService('auth', 'getUserBE'))) {

			$serviceObj->initAuth('getUserBE', $this->xmlrpcLoginData, $this->xmlrpcAuthInfo, $this);

			//get a login user
			if($this->xmlrpcUser = $serviceObj->getUser()) {
				return $this->xmlrpcUser;
			}
		}

		return false;
	}

	/**
	 * authentify user with username, password
	 *
	 * @return	boolean
	 */
	function authUser() {
		$OK = false;

		if(is_object($serviceObj = t3lib_div::makeInstanceService('auth', 'authUserBE'))) {

			$serviceObj->initAuth('authUserBE', $this->xmlrpcLoginData, $this->xmlrpcAuthInfo, $this);

			//auth user
			$OK = $serviceObj->authUser($this->xmlrpcUser);
		}

		return $OK;
	}

	//maybe we'll do it like this later, for now it's ok what we have
	function authLikeInInit() {
		global $TYPO3_CONF_VARS;

		$XMLRPC_USER = t3lib_div::makeInstance('t3lib_beUserAuth');	// New backend user object
		$XMLRPC_USER->name               = 'xmlrpc_typo_user';
		$XMLRPC_USER->security_level     = 'normal';

		$XMLRPC_USER->warningEmail       = $TYPO3_CONF_VARS['BE']['warning_email_addr'];
		$XMLRPC_USER->lockIP             = $TYPO3_CONF_VARS['BE']['lockIP'];
		$XMLRPC_USER->auth_timeout_field = intval($TYPO3_CONF_VARS['BE']['sessionTimeout']);
		$XMLRPC_USER->OS                 = TYPO3_OS;
		$XMLRPC_USER->start();			// Object is initialized
		$XMLRPC_USER->backendCheckLogin();	// Checking if there's a user logged in
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/timtab/pi2/class.tx_timtab_pi2_xmlrpcauth.php'])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/timtab/pi2/class.tx_timtab_pi2_xmlrpcauth.php']);
}
?>
