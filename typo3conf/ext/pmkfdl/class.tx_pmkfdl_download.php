<?php
	/***************************************************************
	*  Copyright notice
	*
	*  (c) 2009 Peter Klein <pmk@io.dk>
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
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   46: class tx_pmkfdl_download
 *   53:     public function makeDownloadLink()
 *  130:     private function getMimeType()
 *  158:     private function decrypt($encrypted,$key)
 *  174:     private function checkAccess($userGroups,$accessGroups)
 *  190:     private function error()
 *
 * TOTAL FUNCTIONS: 5
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

 /**
  * Main class. eID based. Sends the file using the 'header' function.
  *
  */
	class tx_pmkfdl_download {

	/**
	 * Force download of file
	 *
	 * @return	void
	 */
		public function makeDownloadLink() {
			// Connect to database. Currently not needed, but might be useful for hooks.
			tslib_eidtools::connectDB();
			if ($sdata = t3lib_div::_GET('sfile')) {
				// Encrypted data
				$this->feUserObj = tslib_eidtools::initFeUser(); // Initialize FE user object
				parse_str($this->decrypt($sdata,$GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']),$this->getParams);
				$this->userGroups =  t3lib_div::intExplode(',',$this->feUserObj->user['usergroup']);
				$this->accessGroups = t3lib_div::intExplode(',',$this->getParams['access']);
				$this->access = $this->checkAccess($this->userGroups,$this->accessGroups);
			}
			else {
				$this->getParams = t3lib_div::_GET();
				$this->accessGroups = array(-1);
				$this->userGroups = array(-1);
				$this->access = true;
			}
			$this->file = rawurldecode($this->getParams['file']);
			$this->md5 = $this->getParams['ck'];
			$this->forcedl = intval($this->getParams['forcedl']);
			$this->extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['pmkfdl']);
			$this->blockedExt = preg_split('/\s*,\s*/',$this->extConf['blockedExt']);
			$this->filesegments = pathinfo(strtolower($this->file));

			// Call hook for possible manipulation of data
			if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['pmkfdl']['postProcessHook'])) {
				$_params = array('pObj' => &$this);
				foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['pmkfdl']['postProcessHook'] as $_funcRef) {
					t3lib_div::callUserFunction($_funcRef,$_params,$this);
				}
			}

			// Exit (redirect to 404 page) if:
			//  No filename or checksum argument is present
			//  File doesn't exist
			//  md5 checksum of file doesn't match the checksum argument
			//  File extension is in list of illegal file extensions
			if ($this->file == '' || $this->md5 == '' || !file_exists($this->file) || @md5_file($this->file) != $this->md5 || in_array($this->filesegments['extension'], $this->blockedExt)) {
				$this->error();
			}

			// Exit (redirect to 404 or custom page) if:
			//  Usergroups of user downloading doesn't match the accesgroups for the file
			if (!$this->access) {
				if ($this->extConf['noAccess_handling'] && $this->extConf['noAccess_handling_statheader']) {
					$this->error($this->extConf['noAccess_handling'],$this->extConf['noAccess_handling_statheader']);
				}
				else {
					$this->error();
				}
			}

			// Make sure there's nothing else in the buffer
			ob_end_clean();

			// Get mimetype
			$mimetype = $this->forcedl ? 'application/force-download' : $this->getMimeType();

			// Start sending headers
			header('Pragma: public'); // required
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Cache-Control: private', false); // required for certain browsers
			header('Content-Transfer-Encoding: binary');
			header('Content-Type: ' . $mimetype);
			header('Content-Length: ' . filesize($this->file));
			header('Content-Disposition: attachment; filename="' . $this->filesegments['basename'] . '";' );
			// Send data
			readfile($this->file);
			exit;
		}

	/**
	 * Returns mimetype of current file
	 *
	 * @return	string		$mimetype; Mimetype that match selected filetype
	 */
		private function getMimeType() {
			$mimetype = '';
			// 1st choice: finfo_file
			if (function_exists('finfo_file')) {
				$finfo = finfo_open(FILEINFO_MIME);
				$mimetype = finfo_file($finfo, PATH_site.$this->file);
				finfo_close($finfo);
			}
			// 2nd choice: mime_content_type
			if ($mimetype == '' && function_exists('mime_content_type')) {
				$mimetype = mime_content_type(PATH_site.$this->file);
			}
			// 3rd choice: Use external list of mimetypes
			if ($mimetype == '') {
				require_once(t3lib_extMgm::extPath('pmkfdl').'mimetypes.php');
				$defaultmimetype = 'application/octet-stream';
				$mimetype = isset($mimetypes[$this->filesegments['extension']]) ? $mimetypes[$this->filesegments['extension']] : $defaultmimetype;
			}
			return $mimetype;
		}

	/**
	 * Decrypt file using mcrypt
	 *
	 * @param	string		$encrypted: encrypted text
	 * @param	string		$key: decryption key
	 * @return	string		$decrypted; decrypted text
	 */
		private function decrypt($encrypted,$key) {
			$cipher = mcrypt_module_open(MCRYPT_BLOWFISH,'','ecb','');
			$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($cipher), MCRYPT_RAND);
			mcrypt_generic_init($cipher, $key, $iv);
			$decrypted = mdecrypt_generic($cipher,base64_decode(rawurldecode($encrypted)));
			mcrypt_generic_deinit($cipher);
			mcrypt_module_close($cipher);
			return rtrim($decrypted);
		}
	/**
	 * Checks if user has access to download file, based on TYPO3 access groups
	 *
	 * @param	array		$userGroups; fe_groups user belongs to
	 * @param	array		$accessGroups; fe_groups required for access
	 * @return	boolean		$access; True if user has the correct access credentials
	 */
		private function checkAccess($userGroups,$accessGroups) {
			$access = false;
			foreach ($userGroups as $group) {
				if (in_array($group,$accessGroups)) {
					$access = true;
					break;
				}
			}
			return $access;
		}

	/**
	 * Returns 404 header to browser
	 *
	 * @return	void
	 */
		private function error($code='',$header='',$reason='') {
			$code = $code ? $code : $GLOBALS['TYPO3_CONF_VARS']['FE']['pageNotFound_handling'];
			$header = $header ? $header : $GLOBALS['TYPO3_CONF_VARS']['FE']['pageNotFound_handling_statheader'];
			$tsfe = t3lib_div::makeInstance('tslib_fe', $GLOBALS['TYPO3_CONF_VARS'], 0, 0);
			$tsfe->pageErrorHandler($code, $header, $reason);
			exit;
		}

	}
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pmkfdl/class.tx_pmkfdl_download.php']) {
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pmkfdl/class.tx_pmkfdl_download.php']);
	}

	// Make instance:
	$output = t3lib_div::makeInstance('tx_pmkfdl_download');
	$output->makeDownloadLink();
?>
