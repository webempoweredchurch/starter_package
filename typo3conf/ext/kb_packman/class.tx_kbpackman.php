<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2004-2010 Kraft Bernhard (kraftb@think-open.at)
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
 * Addition of an item to the clickmenu
 *
 * $Id$
 *
 * @author	Kraft Bernhard <kraftb@think-open.at>
 * @package TYPO3
 * @subpackage tx_kbpackman
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   67: class tx_kbpackman
 *   75:     function __construct()
 *   86:     function isCompressed($file)
 *  113:     function checkForbidden($files, $theDest)
 *  129:     function getList($file)
 *  152:     function zipGetList($file)
 *  169:     function rarGetList($file)
 *  185:     function targzGetList($file)
 *  199:     function tarbz2GetList($file)
 *  215:     function pack($source, $targetName)
 *  238:     function unpack($file)
 *  263:     function zipPack($file, $targetFile)
 *  289:     function zipUnpack($file)
 *  320:     function getFileResult($list, $type = 'zip')
 *  362:     function rarUnpack($file)
 *  392:     function rarPack($file, $targetFile)
 *  414:     function targzPack($file, $targetFile)
 *  435:     function tarbz2Pack($file, $targetFile)
 *  454:     function targzUnpack($file)
 *  480:     function tarbz2Unpack($file)
 *  507:     function getTarget($current, $file = 0)
 *
 * TOTAL FUNCTIONS: 20
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

require_once(PATH_t3lib.'class.t3lib_basicfilefunc.php');

class tx_kbpackman	{
	var $overwrite = 0;

	/**
	 * The constructor for the packman API
	 *
	 * @return	void
	 */
	function __construct()	{
		$this->basicFileFunc = t3lib_div::makeInstance('t3lib_basicFileFunctions');
		$this->basicFileFunc->init($GLOBALS['FILEMOUNTS'],$TYPO3_CONF_VARS['BE']['fileExtensions']);
	}

	/**
	 * This function returns a listing of an compressed file for which we have defined wrappers
	 *
	 * @param	string		Compressed file for which listing should get generated
	 * @return	array		List of files in compressed file
	 */
	function isCompressed($file)	{
		// Handle ZIP Extensions
		if (eregi('\.zip$', $file)) {
			return true;
		}
		// Handle RAR Extensions
		if (eregi('\.rar$', $file)) {
			return true;
		}
		// Handle TAR.GZ Extensions
		if (eregi('\.tar\.gz$', $file) || eregi('\.tgz$', $file)) {
			return true;
		}
		// Handle TAR.BZ2 Extensions
		if (eregi('\.tar\.bz2$', $file) || eregi('\.tbz2$', $file)) {
			return true;
		}
		return false;
	}

	/**
	 * Returns if there are php files in the list of files
	 *
	 * @param	array		List of files
	 * @param	string		Destination which to check
	 * @return	bool		PHP file is in list of files
	 */
	function checkForbidden($files, $theDest)	{
		foreach($files as $file) {
			$info = t3lib_div::split_fileref($file);
			if (!$this->basicFileFunc->checkIfAllowed($info['fileext'], $theDest, $file))	{
				return true;
			}
		}
		return false;
	}

	/**
	 * This function returns a listing of an compressed file for which we have defined wrappers
	 *
	 * @param	string		Compressed file for which listing should get generated
	 * @return	array		List of files in compressed file
	 */
	function getList($file) {
		if (preg_match('/\.zip$/', $file)) {
				// Handle ZIP Extensions
			return $this->zipGetList($file);
		} elseif (preg_match('/\.rar$/', $file)) {
				// Handle RAR Extensions
			return $this->rarGetList($file);
		} elseif (preg_match('/\.tar.gz$/', $file) || preg_match('/\.tgz$/', $file)) {
				// Handle TAR.GZ Extensions
			return $this->targzGetList($file);
		} elseif (preg_match('/\.tar.bz2/', $file) || preg_match('/\.tbz2/', $file)) {
				// Handle TAR.BZ2 Extensions
			return $this->tarbz2GetList($file);
		}
		return false;
	}

	/**
	 * This function returns a filelisting of a zip file
	 *
	 * @param	string		Return list of files in zip file
	 * @return	array		List of files
	 */
	function zipGetList($file)	{
		$unzip = $GLOBALS['TYPO3_CONF_VARS']['BE']['unzip_path']?$GLOBALS['TYPO3_CONF_VARS']['BE']['unzip_path']:'unzip';
		$cmd = $unzip.' -l '.escapeshellarg($file);
		exec($cmd, $list, $ret);
		if ($ret) {
			return array();
		}
		$result = $this->getFileResult($list, 'list-zip');
		return $result;
	}

	/**
	 * This function returns a filelisting of a rar file
	 *
	 * @param	string		Return list of files in rar file
	 * @return	array		List of files
	 */
	function rarGetList($file) {
		$unrar = $GLOBALS['TYPO3_CONF_VARS']['BE']['unrar_path']?$GLOBALS['TYPO3_CONF_VARS']['BE']['unrar_path']:'unrar';
		$cmd = $unrar.' vb '.escapeshellarg($file);
		exec($cmd, $list, $ret);
		if ($ret) {
			return array();
		}
		return $list;
	}

	/**
	 * This function returns a filelisting of a tar.gz file
	 *
	 * @param	string		Return list of files in tar.gz file
	 * @return	array		List of files
	 */
	function targzGetList($file)	{
		$cmd = 'tar -tzf '.escapeshellarg($file);
		exec($cmd, $list, $ret);
		if ($ret) {
			return array();
		}
		return $list;
	}
	/**
	 * This function returns a filelisting of a tar.bz2 file
	 *
	 * @param	string		Return list of files in tar.bz2 file
	 * @return	array		List of files
	 */
	function tarbz2GetList($file)	{
		$cmd = 'tar -tjf '.escapeshellarg($file);
		exec($cmd, $list, $ret);
		if ($ret) {
			return array();
		}
		return $list;
	}

	/**
	 * This method is a wrapper for extracting the passed source file to the specified destination with one of the supported archive types
	 *
	 * @param	string		The archive file
	 * @param	string		The destination path to which to extract the archive
	 * @return	array		A list of the extracted files
	 */
	function pack($source, $targetName) {
		if (preg_match('/\.zip$/', $targetName)) {
				// Handle ZIP Extensions
			return $this->zipPack($source, $targetName);
		} elseif (preg_match('/\.rar$/', $targetName)) {
				// Handle RAR Extensions
			return $this->rarPack($source, $targetName);
		} elseif (preg_match('/\.tar.gz$/', $targetName) || preg_match('/\.tgz$/', $targetName)) {
				// Handle TAR.GZ Extensions
			return $this->targzPack($source, $targetName);
		} elseif (preg_match('/\.tar.bz2/', $targetName) || preg_match('/\.tbz2/', $targetName)) {
				// Handle TAR.BZ2 Extensions
			return $this->tarbz2Pack($source, $targetName);
		}
		return false;
	}

	/**
	 * This function returns the files extracted by the call to the specific unpack-wrapper for the supplied file
	 *
	 * @param	string		File to unpack
	 * @return	array		Files unpacked
	 */
	function unpack($file)	{
		if (preg_match('/\.zip$/', $file)) {
				// Handle ZIP Extensions
			return $this->zipUnpack($file);
		} elseif (preg_match('/\.rar$/', $file)) {
				// Handle RAR Extensions
			return $this->rarUnpack($file);
		} elseif (preg_match('/\.tar.gz$/', $file) || preg_match('/\.tgz$/', $file)) {
				// Handle TAR.GZ Extensions
			return $this->targzUnpack($file);
		} elseif (preg_match('/\.tar.bz2$/', $file) || preg_match('/\.tbz2$/', $file)) {
				// Handle TAR.BZ2 Extensions
			return $this->tarbz2Unpack($file);
		}
		return false;
	}

	/**
	 * This function creates a zip file
	 *
	 * @param	string		File/Directory to pack
	 * @param	string		Zip-file target directory
	 * @param	string		Zip-file target name
	 * @return	array		Files packed
	 */
	function zipPack($file, $targetFile)	{
		if (!(isset($GLOBALS['TYPO3_CONF_VARS']['BE']['unzip']['unzip']['split_char'])&&
		  isset($GLOBALS['TYPO3_CONF_VARS']['BE']['unzip']['unzip']['pre_lines']) &&
		  isset($GLOBALS['TYPO3_CONF_VARS']['BE']['unzip']['unzip']['post_lines']) &&
		  isset($GLOBALS['TYPO3_CONF_VARS']['BE']['unzip']['unzip']['file_pos']))) {
			return array();
		}
		$zip = $GLOBALS['TYPO3_CONF_VARS']['BE']['zip_path']?$GLOBALS['TYPO3_CONF_VARS']['BE']['zip_path']:'zip';
		$path = dirname($file);
		$file = basename($file);
		chdir($path);
		$cmd = $zip.' -r -9 '.escapeshellarg($targetFile).' '.escapeshellarg($file);
		exec($cmd, $list, $ret);
		if ($ret) {
			return array();
		}
		$result = $this->getFileResult($list, 'zip');
		return $result;
	}

	/**
	 * This function unpacks a zip file
	 *
	 * @param	string		File to unpack
	 * @return	array		Files unpacked
	 */
	function zipUnpack($file)	{
		if (!(isset($GLOBALS['TYPO3_CONF_VARS']['BE']['unzip']['unzip']['split_char'])&&
		  isset($GLOBALS['TYPO3_CONF_VARS']['BE']['unzip']['unzip']['pre_lines']) &&
		  isset($GLOBALS['TYPO3_CONF_VARS']['BE']['unzip']['unzip']['post_lines']) &&
		  isset($GLOBALS['TYPO3_CONF_VARS']['BE']['unzip']['unzip']['file_pos']))) {
			return array();
		}
		$path = dirname($file);
		chdir($path);
		// Unzip without overwriting existing files
		$unzip = $GLOBALS['TYPO3_CONF_VARS']['BE']['unzip_path']?$GLOBALS['TYPO3_CONF_VARS']['BE']['unzip_path']:'unzip';
		if ($this->overwrite) {
			$cmd = $unzip.' -o '.escapeshellarg($file);
		} else {
			$cmd = $unzip.' -n '.escapeshellarg($file);
		}
		exec($cmd, $list, $ret);
		if ($ret) {
			return array();
		}
		$result = $this->getFileResult($list, 'unzip');
		return $result;
	}

	/**
	 * This method helps filtering the output of the various archive binaries to get a clean php array
	 *
	 * @param	array		The output of the executed archive binary
	 * @param	string		The type/configuration for which to parse the output
	 * @return	array		A clean list of the filenames returned by the binary
	 */
	function getFileResult($list, $type = 'zip')	{
		$sc = $GLOBALS['TYPO3_CONF_VARS']['BE']['unzip'][$type]['split_char'];
		$pre = intval($GLOBALS['TYPO3_CONF_VARS']['BE']['unzip'][$type]['pre_lines']);
		$post = intval($GLOBALS['TYPO3_CONF_VARS']['BE']['unzip'][$type]['post_lines']);
		$pos = intval($GLOBALS['TYPO3_CONF_VARS']['BE']['unzip'][$type]['file_pos']);

			// Removing trailing lines
		while ($post--) {
			array_pop($list);
		}

			// Only last lines
		if ($pre === -1) {
			$fl = array();
			while ($line = trim(array_pop($list))) {
				array_unshift($fl, $line);
			}
			$list = $fl;
		}

			// Remove preceeding lines
		if ($pre > 0) {
			while ($pre--) {
				array_shift($list);
			}
		}


		$fl = array();
		foreach ($list as $file) {
			$parts = preg_split('/'.preg_quote($sc).'+/', $file);
			$fl[] = trim($parts[$pos]);
		}
		return $fl;
	}

	/**
	 * This function unpacks a rar file
	 *
	 * @param	string		File to unpack
	 * @return	array		Files unpacked
	 */
	function rarUnpack($file) {
		$unrar = $GLOBALS['TYPO3_CONF_VARS']['BE']['unrar_path']?$GLOBALS['TYPO3_CONF_VARS']['BE']['unrar_path']:'unrar';
		$path = dirname($file);
		chdir($path);
		if ($this->overwrite) {
			$cmd = $unrar.' x -o+ -idcd -y '.escapeshellarg($file);
		} else {
			$cmd = $unrar.' x -o- -idcd -y '.escapeshellarg($file);
		}
		exec($cmd, $list, $ret);
		if ($this->overwrite) {
			// We are in overwrite mode
			// Check if return value of
			// exec is set == Error
			if ($ret) {
				return array();
			}
		}
		$result = $this->getFileResult($list, 'unrar');
		return $result;
	}

	/**
	 * This function creates a rar file
	 *
	 * @param	string		File/Directory to pack
	 * @param	string		RAR-file target directory
	 * @param	string		RAR-file target name
	 * @return	array		Files packed
	 */
	function rarPack($file, $targetFile) {
		$rar = $GLOBALS['TYPO3_CONF_VARS']['BE']['rar_path']?$GLOBALS['TYPO3_CONF_VARS']['BE']['rar_path']:'rar';
		$path = dirname($file);
		$file = basename($file);
		chdir($path);
		$cmd = $rar.' a -r -idcd -m5 '.escapeshellarg($targetFile).' '.escapeshellarg($file);
		exec($cmd, $list, $ret);
		if ($ret) {
			return array();
		}
		$result = $this->getFileResult($list, 'rar');
		return $result;
	}

	/**
	 * This function creates a tar.gz file
	 *
	 * @param	string		File/Directory to pack
	 * @param	string		tar.gz file target directory
	 * @param	string		tar.gz file target name
	 * @return	array		Files packed
	 */
	function targzPack($file, $targetFile) {
		$path = dirname($file);
		$file = basename($file);
		chdir($path);
		$relTarget = str_replace($path.'/', '', $targetFile);
		$cmd = 'tar -czv --exclude='.escapeshellarg($relTarget).' -f '.escapeshellarg($targetFile).' '.escapeshellarg($file);
		exec($cmd, $list, $ret);
		if ($ret) {
			return array();
		}
		return $list;
	}

	/**
	 * This function creates a tar.bz2 file
	 *
	 * @param	string		File/Directory to pack
	 * @param	string		tar.bz2 file target directory
	 * @param	string		tar.bz2 file target name
	 * @return	array		Files packed
	 */
	function tarbz2Pack($file, $targetFile) {
		$path = dirname($file);
		$file = basename($file);
		chdir($path);
		$relTarget = str_replace($path.'/', '', $targetFile);
		$cmd = 'tar -cjv --exclude='.escapeshellarg($relTarget).' -f '.escapeshellarg($targetFile).' '.escapeshellarg($file);
		exec($cmd, $list, $ret);
		if ($ret) {
			return array();
		}
		return $list;
	}

	/**
	 * This function unpacks a tar.gz file
	 *
	 * @param	string		File to unpack
	 * @return	array		Files unpacked
	 */
	function targzUnpack($file)	{
		$path = dirname($file);
		chdir($path);
		if ($this->overwrite) {
			$cmd = 'tar -xzvf '.escapeshellarg($file);
		} else {
			$cmd = 'tar -xzvkf '.escapeshellarg($file);
		}
		exec($cmd, $list, $ret);
		if ($this->overwrite) {
			// We are in overwrite mode
			// Check if return value of
			// exec is set == Error
			if ($ret) {
				return array();
			}
		}
		return $list;
	}

	/**
	 * This function unpacks a tar.bz2 file
	 *
	 * @param	string		File to unpack
	 * @return	array		Files unpacked
	 */
	function tarbz2Unpack($file)	{
		$path = dirname($file);
		chdir($path);
		if ($this->overwrite) {
			$cmd = 'tar -xjvf '.escapeshellarg($file);
		} else {
			$cmd = 'tar -xjvkf '.escapeshellarg($file);
		}
		exec($cmd, $list, $ret);
		if ($this->overwrite) {
			// We are in overwrite mode
			// Check if return value of
			// exec is set == Error
			if ($ret) {
				return array();
			}
		}
		return $list;
	}

	/**
	 * This method returns the filesystem name for the specified target
	 *
	 * @param	string		The current path
	 * @param	string		When set the target should be a file
	 * @return	string		The final target path for the archive
	 */
	function getTarget($current, $file = 0)	{
		if ($file)	{
			$dirTarget = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['kb_packman']['fileTarget'];
		} else	{
			$dirTarget = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['kb_packman']['dirTarget'];
		}
		if (substr($dirTarget, 0, 1)=='.')	{
			$dirTarget = $current.$dirTarget;
		} else	{
			$dirTarget = PATH_site.$dirTarget;
		}
		$dirTarget = str_replace('/./', '/', t3lib_div::resolveBackPath($dirTarget));
		if (substr($dirTarget, -1)!='/')	{
			$dirTarget .= '/';
		}
		return $dirTarget;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kb_packman/ux.kbpacman.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kb_packman/ux.kbpacman.php']);
}


?>
