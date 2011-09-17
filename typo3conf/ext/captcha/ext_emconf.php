<?php

########################################################################
# Extension Manager/Repository config file for ext "captcha".
#
# Auto generated 26-02-2010 15:20
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Captcha Library',
	'description' => 'Generates an image with an obfuscated text string which a user must repeat in a form field. Captchas are used to prevent bots from using various types of computing services. Applications include preventing bots from taking part in online polls, submitting entries in a guest book etc. This captcha extension for TYPO3 is meant to be used by multiple other extensions.',
	'category' => 'fe',
	'shy' => 0,
	'version' => '1.1.1',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'beta',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Kasper SkÃÂÃÂ¥rhÃÂÃÂ¸j',
	'author_email' => 'kasper2005@typo3.com',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'typo3' => '3.5.0-0.0.0',
			'php' => '3.0.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:6:{s:21:"ext_conf_template.txt";s:4:"5ef1";s:12:"ext_icon.gif";s:4:"8b4f";s:17:"ext_localconf.php";s:4:"e7d7";s:19:"captcha/captcha.php";s:4:"549e";s:16:"captcha/vera.ttf";s:4:"785d";s:14:"doc/manual.sxw";s:4:"944c";}',
);

?>