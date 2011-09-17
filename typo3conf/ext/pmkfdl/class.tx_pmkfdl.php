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
 *   46: class tx_pmkfdl
 *   55:     public function makeDownloadLink($content, $conf)
 *  109:     private function encrypt($uncrypted,$key)
 *
 * TOTAL FUNCTIONS: 2
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

	/**
	 * USE:
	 * The class is intended to be used without creating an instance of it.
	 * So: Don't instantiate - call functions with "tx_pmkfdl::" prefixed the function name.
	 * So use tx_pmkfdl::[method-name] to refer to the functions, eg. 'tx_pmkfdl::makeDownloadLink()'
	 *
	 */
	class tx_pmkfdl {

	/**
	 * Modifies typolink output so that link points to pmkfdl
	 *
	 * @param	string		$content: Current link
	 * @param	array		$$conf: Config options
	 * @return	string		$content: Modified link
	 */
		public function makeDownloadLink($content, $conf) {
			if (!$content) return;
			$this->conf = $conf;
			$file = str_replace(t3lib_div::getIndpEnv('TYPO3_SITE_URL'), '', $content);
			$filepath = PATH_site.$file;
			$filesegments = pathinfo(strtolower($filepath));
			$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['pmkfdl']);
			$blockedExt = preg_split('/\s*,\s*/',$extConf['blockedExt']);
			if (file_exists($filepath) && !in_array($filesegments['extension'], $blockedExt)) {
				$getParams['file'] = $file;
				$getParams['ck'] = md5_file($filepath);

				// Set register values with size and type for use from TypoScript
				$GLOBALS['TSFE']->register['filesize'] = filesize($filepath);
				$GLOBALS['TSFE']->register['filetype'] = $filesegments['extension'];

				if (preg_match('/\|?forcedl\|?/i', $conf['makeDownloadLink'])) {
					// Force download
					$getParams['forcedl'] = 1;
				}
				if (preg_match('/\|?secure\|?/i', $conf['makeDownloadLink']) && $accessGroups = preg_replace('/^0,-[1|2],?/', '', $GLOBALS['TSFE']->gr_list)) {
					// Secure download
					$getParams['access'] = $accessGroups;
					// Add current language "L" GETvar, for use when redirecting
					if ($lang = t3lib_div::_GET('L')) {
							$getParams['L'] = $lang;
					}
				}

				// Call hook for possible manipulation of data
				if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['pmkfdl']['preProcessHook'])) {
					$_params = array('pObj' => &$this,'getParams' => &$getParams);
					foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['pmkfdl']['preProcessHook'] as $_funcRef) {
						t3lib_div::callUserFunction($_funcRef,$_params,$this);
					}
				}

				if (preg_match('/\|?secure\|?/i', $conf['makeDownloadLink'])) {
					$content = 'index.php?eID=pmkfdl&sfile='.tx_pmkfdl::encrypt(http_build_query($getParams,'','&'),$GLOBALS['TYPO3_CONF_VARS']['SYS']['encryptionKey']);
				}
				else {
					$content = 'index.php?eID=pmkfdl&'.http_build_query($getParams,'','&');
				}
			}
			return $content;
		}

	/**
	 * Encrypt file using mcrypt
	 *
	 * @param	string		$uncrypted: unencrypted text
	 * @param	string		$key: decryption key
	 * @return	string		$encrypted; encrypted text
	 */
		private function encrypt($uncrypted,$key) {
			$cipher = mcrypt_module_open(MCRYPT_BLOWFISH,'','ecb','');
			$iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($cipher), MCRYPT_RAND);
			mcrypt_generic_init($cipher, $key, $iv);
			$encrypted = mcrypt_generic($cipher,$uncrypted);
			mcrypt_generic_deinit($cipher);
			mcrypt_module_close($cipher);
			return rawurlencode(base64_encode($encrypted));
		}
	}

	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pmkfdl/class.tx_pmkfdl.php'])	{
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pmkfdl/class.tx_pmkfdl.php']);
	}
?>
