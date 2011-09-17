<?php

########################################################################
# Extension Manager/Repository config file for ext "wec_contentelements".
#
# Auto generated 08-07-2011 10:07
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'WEC Content Elements',
	'description' => 'Provides additional content elements such as a local menu, slideshow, Vimeo video, and YouTube video.',
	'category' => 'fe',
	'shy' => 0,
	'version' => '1.2.0',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => 'uploads/tx_weccontentelements/slideshow/,uploads/tx_weccontentelements/filedownload',
	'modify_tables' => '',
	'clearcacheonload' => 1,
	'lockType' => '',
	'author' => 'Web-Empowered Church Team',
	'author_email' => 'devteam@webempoweredchurch.org',
	'author_company' => 'Christian Technology Ministries International Inc.',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'php' => '5.2.0-0.0.0',
			'typo3' => '4.3.0-4.5.99',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:39:{s:9:"ChangeLog";s:4:"6eed";s:36:"class.tx_weccontentelements_cobj.php";s:4:"1447";s:42:"class.tx_weccontentelements_getxmldata.php";s:4:"3e71";s:35:"class.tx_weccontentelements_lib.php";s:4:"78ac";s:16:"ext_autoload.php";s:4:"0b5f";s:21:"ext_conf_template.txt";s:4:"9810";s:12:"ext_icon.gif";s:4:"82e6";s:17:"ext_localconf.php";s:4:"b71e";s:14:"ext_tables.php";s:4:"8682";s:14:"doc/manual.sxw";s:4:"dc8d";s:23:"filedownload/content.ts";s:4:"70d7";s:25:"filedownload/flexform.xml";s:4:"f7bc";s:21:"filedownload/icon.gif";s:4:"81ac";s:26:"filedownload/locallang.xml";s:4:"3a3e";s:30:"filedownload/locallang_csh.xml";s:4:"0185";s:28:"filedownload/wizard-icon.gif";s:4:"84e4";s:20:"localmenu/content.ts";s:4:"b4ef";s:18:"localmenu/icon.gif";s:4:"5fb0";s:23:"localmenu/locallang.xml";s:4:"6881";s:25:"localmenu/wizard-icon.gif";s:4:"ead9";s:20:"slideshow/content.ts";s:4:"0fe6";s:22:"slideshow/flexform.xml";s:4:"7dd8";s:18:"slideshow/icon.gif";s:4:"a805";s:23:"slideshow/locallang.xml";s:4:"b364";s:25:"slideshow/wizard-icon.gif";s:4:"007d";s:37:"slideshow/res/jquery.cycle.all.min.js";s:4:"0975";s:23:"slideshow/res/jquery.js";s:4:"e4af";s:16:"vimeo/content.ts";s:4:"7dc1";s:18:"vimeo/flexform.xml";s:4:"d92f";s:14:"vimeo/icon.gif";s:4:"4565";s:19:"vimeo/locallang.xml";s:4:"18f3";s:23:"vimeo/locallang_csh.xml";s:4:"bb43";s:21:"vimeo/wizard-icon.gif";s:4:"4fc4";s:18:"youtube/content.ts";s:4:"7c03";s:20:"youtube/flexform.xml";s:4:"3af1";s:16:"youtube/icon.gif";s:4:"3d3b";s:21:"youtube/locallang.xml";s:4:"b1c8";s:25:"youtube/locallang_csh.xml";s:4:"c3ea";s:23:"youtube/wizard-icon.gif";s:4:"615d";}',
);

?>