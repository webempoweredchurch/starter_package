<?php

########################################################################
# Extension Manager/Repository config file for ext "pagebrowse".
#
# Auto generated 21-01-2011 11:31
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Universal page browser',
	'description' => 'Provides page browsing services for extensions',
	'category' => 'fe',
	'shy' => 0,
	'version' => '1.3.0',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Dmitry Dulepov',
	'author_email' => 'dmitry@typo3.org',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'typo3' => '4.0.0-4.5.99',
			'php' => '5.2.0-10.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:14:{s:9:"ChangeLog";s:4:"a2c9";s:12:"ext_icon.gif";s:4:"c538";s:17:"ext_localconf.php";s:4:"821e";s:14:"ext_tables.php";s:4:"cccc";s:14:"doc/manual.sxw";s:4:"e646";s:30:"doc/template-structure.graffle";s:4:"e24e";s:31:"pi1/class.tx_pagebrowse_pi1.php";s:4:"2e53";s:19:"pi1/flexform_ds.xml";s:4:"b1b9";s:17:"pi1/locallang.xml";s:4:"e8ee";s:14:"res/styles.css";s:4:"34c0";s:18:"res/styles_min.css";s:4:"e907";s:17:"res/template.html";s:4:"d59b";s:33:"static/page_browser/constants.txt";s:4:"156c";s:29:"static/page_browser/setup.txt";s:4:"6aa6";}',
);

?>