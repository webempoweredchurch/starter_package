<?php

########################################################################
# Extension Manager/Repository config file for ext "wec_sermons".
#
# Auto generated 08-03-2010 16:00
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'WEC Sermon Management System',
	'description' => 'Provides centralized management of online resources associated with a sermon',
	'category' => 'plugin',
	'shy' => 0,
	'version' => '0.10.2',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'beta',
	'uploadfolder' => 1,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 1,
	'lockType' => '',
	'author' => 'Web-Empowered Church Team',
	'author_email' => 'sermon@webempoweredchurch.org',
	'author_company' => 'Christian Technonlogy Ministries International Inc.',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'php' => '4.3.0-0.0.0',
			'wec_api' => '0.9.1-0.0.0',
			'typo3' => '4.1.6-0.0.0',
			'wec_flashplayer' => '1.3.1-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:59:{s:9:"ChangeLog";s:4:"a922";s:10:"README.txt";s:4:"5275";s:4:"TODO";s:4:"6f4c";s:20:"class.ext_update.php";s:4:"f13f";s:39:"class.tx_wecsermons_resourceTypeTca.php";s:4:"85a7";s:31:"class.tx_wecsermons_xmlView.php";s:4:"650c";s:21:"ext_conf_template.txt";s:4:"572c";s:12:"ext_icon.gif";s:4:"0e1f";s:17:"ext_localconf.php";s:4:"eab4";s:14:"ext_tables.php";s:4:"4632";s:14:"ext_tables.sql";s:4:"67f5";s:28:"ext_typoscript_constants.txt";s:4:"d41d";s:24:"ext_typoscript_setup.txt";s:4:"d41d";s:19:"flexform_ds_pi1.xml";s:4:"4252";s:37:"icon_tx_wecsermons_resource_types.gif";s:4:"3a1a";s:32:"icon_tx_wecsermons_resources.gif";s:4:"86f1";s:30:"icon_tx_wecsermons_seasons.gif";s:4:"c430";s:29:"icon_tx_wecsermons_series.gif";s:4:"67c9";s:30:"icon_tx_wecsermons_sermons.gif";s:4:"d132";s:31:"icon_tx_wecsermons_speakers.gif";s:4:"1435";s:29:"icon_tx_wecsermons_topics.gif";s:4:"4d62";s:13:"locallang.php";s:4:"9206";s:16:"locallang_db.php";s:4:"4aaa";s:47:"selicon_tx_wecsermons_sermons_record_type_0.gif";s:4:"02b6";s:47:"selicon_tx_wecsermons_sermons_record_type_1.gif";s:4:"02b6";s:47:"selicon_tx_wecsermons_sermons_record_type_2.gif";s:4:"02b6";s:47:"selicon_tx_wecsermons_sermons_record_type_3.gif";s:4:"02b6";s:7:"tca.php";s:4:"72c6";s:36:"csh/locallang_csh_resource_types.xml";s:4:"cb50";s:31:"csh/locallang_csh_resources.xml";s:4:"f67a";s:29:"csh/locallang_csh_seasons.xml";s:4:"18b9";s:28:"csh/locallang_csh_series.xml";s:4:"7919";s:29:"csh/locallang_csh_sermons.xml";s:4:"db23";s:30:"csh/locallang_csh_speakers.xml";s:4:"4879";s:28:"csh/locallang_csh_topics.xml";s:4:"3da3";s:38:"devtools/find_missing_translations.php";s:4:"f3c4";s:43:"devtools/find_missing_translations_inxml.py";s:4:"6158";s:34:"devtools/wec_sermons_killswitch.sh";s:4:"94fb";s:12:"doc/TODO.txt";s:4:"b550";s:14:"doc/manual.sxw";s:4:"cc58";s:19:"doc/wizard_form.dat";s:4:"0173";s:20:"doc/wizard_form.html";s:4:"c4ce";s:14:"pi1/ce_wiz.gif";s:4:"085e";s:31:"pi1/class.tx_wecsermons_pi1.php";s:4:"2c1b";s:39:"pi1/class.tx_wecsermons_pi1_wizicon.php";s:4:"f89a";s:13:"pi1/clear.gif";s:4:"cc11";s:17:"pi1/locallang.php";s:4:"e270";s:16:"pi1/seasons.tmpl";s:4:"e249";s:15:"pi1/series.tmpl";s:4:"afe9";s:16:"pi1/sermons.tmpl";s:4:"49af";s:17:"pi1/speakers.tmpl";s:4:"8f8d";s:15:"pi1/topics.tmpl";s:4:"710f";s:19:"pi1/wecsermons.tmpl";s:4:"cb6d";s:28:"res/tt_news_v2_template.html";s:4:"e9b6";s:36:"res/tx_wecsermons_resource_types.t3d";s:4:"1466";s:28:"res/tx_wecsermons_styles.css";s:4:"0782";s:20:"static/constants.txt";s:4:"4428";s:16:"static/setup.txt";s:4:"1c23";s:22:"static/style/setup.txt";s:4:"a1bc";}',
);

?>