#!/usr/bin/php
<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Web Empowered Church Team, Foundation For Evangelism (sermon@webempoweredchurch.org)
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

if ($_SERVER['argc'] < 2) {
	die("Usage:\n{$_SERVER['argv'][0]} <path_to_locallang_db.php>\n");
}
// implicit else

$langfile = $_SERVER['argv'][1];
require_once($langfile);

// three cheers for O(pain)... the following would get you kicked out of algorithms classes
// yay for small-n pretty much guaranteed... and i like concise code
// --mjb 
foreach ($LOCAL_LANG as $languageKey => $languageArray) {
	foreach ($languageArray as $keyCurrent => $valueCurrent) {
		if ($valueCurrent != '') {
			foreach ($LOCAL_LANG as $subLanguageKey => $subLanguageArray) {
				if ((!array_key_exists($keyCurrent,$subLanguageArray)) || ($subLanguageArray[$keyCurrent] == '') ) {
					echo "$languageKey has $keyCurrent (with value $valueCurrent) but $subLanguageKey is missing this mapping\n";
				}
			}
		}
	}

}

?>
