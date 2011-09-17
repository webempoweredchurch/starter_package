<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Jeff Segars <jeff@webempoweredchurch.org>
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
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Hooks for pageRenderer.
 *
 * @author	Jeff Segars <jeff@webempoweredchurch.org>
 * @package TYPO3
 * @subpackage feeditadvanced
 */
class tx_feeditadvanced_pagerenderer {

	/**
	 * Pre-processes the CSS files when a backend editing form is loaded
	 * via frontend editing, concatenating them to a single file.
	 * Mimics the behavior of t3lib_pagerenderer->doConcatenate() and should
	 * be removed with the pageRenderer handles frontend editing properly.
	 *
	 * @param	$params			Array of parameters, including CSS, Javascript, etc.
	 * @param	$parentObject	The pageRenderer object.
	 * @return	void
	 */
	public function preProcessPageRenderer($params, $parentObject) {
		if ($parentObject->getConcatenateFiles() && (t3lib_div::_GP('eID') === 'feeditadvanced') && $GLOBALS['TBE_TEMPLATE']) {
			$compressor = t3lib_div::makeInstance('t3lib_compressor');
			$cssOptions = array('baseDirectories' => $GLOBALS['TBE_TEMPLATE']->getSkinStylesheetDirectories());
			$params['cssFiles'] = $compressor->concatenateCssFiles($params['cssFiles'], $cssOptions);
		}
	}
	
}

?>