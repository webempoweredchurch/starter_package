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
 *   44: class tx_pmkfdl_ttnews_filemarkers
 *   53:     function extraItemMarkerProcessor($parentMarkerArray, $row, $lConf, $tt_news)
 *
 * TOTAL FUNCTIONS: 1
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

/**
 * Class which hook into tt_news and do additional processing on filemarker
 *
 */
class tx_pmkfdl_ttnews_filemarkers {

	/**
	 * Hook for adding extra markers for a news item
	 *
	 * @param	array		$parentMarkerArray: Current marker array
	 * @param	array		$row : result row for a news item
	 * @param	array		$lConf : conf vars for the current template
	 * @param	array		$tt_news : calling object
	 * @return	array		$parentMarkerArray: filled marker array
	 */
	function extraItemMarkerProcessor($parentMarkerArray, $row, $lConf, $tt_news) {
		$this->conf = &$tt_news->conf;
		// filelinks
		if ($row['news_files']) {
			$files_stdWrap = t3lib_div::trimExplode('|', $this->conf['newsFiles_stdWrap.']['wrap']);
			$fileArr = explode(',', $row['news_files']);
			$files = '';
			$rss2Enclousres = '';
			$path = trim($this->conf['newsFiles.']['path']);
			while (list(, $val) = each($fileArr)) {
				// fills the marker ###FILE_LINK### with the links to the atached files
				$theFile = $path.$val;
				$tt_news->cObj->data['pmkfdl_filename'] = $val;
				$tt_news->cObj->data['pmkfdl_filepath'] = $theFile;
				$filelinks.= $tt_news->cObj->cObjGetSingle($this->conf['newsFiles_pmkfdl'],$this->conf['newsFiles_pmkfdl.']);
					// <enclosure> support for RSS 2.0
				if($this->theCode == 'XML') {
					if (@is_file($theFile))	{
						$fileURL      = $this->config['siteUrl'].$theFile;
						$fileSize     = filesize($theFile);
						$fileMimeType = t3lib_htmlmail::getMimeType($fileURL);

						$rss2Enclousres .= '<enclosure url="'.$fileURL.'" ';
						$rss2Enclousres .= 'length ="'.$fileSize.'" ';
						$rss2Enclousres .= 'type="'.$fileMimeType.'" />'."\n\t\t\t";
					}
				}
			}
			$parentMarkerArray['###FILE_LINK###'] = $filelinks.$files_stdWrap[1];
			$parentMarkerArray['###NEWS_RSS2_ENCLOSURES###'] = trim($rss2Enclousres);
		}

		return $parentMarkerArray;
	}
}
	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pmkfdl/res/class.tx_pmkfdl_ttnews_filemarkers.php'])	{
		include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/pmkfdl/res/class.tx_pmkfdl_ttnews_filemarkers.php']);
	}
?>