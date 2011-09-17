<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$conf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['wec_config']);

if($conf['tcaAdditions']) {
	t3lib_div::loadTCA('pages');
	$TCA['pages']['columns']['content_from_pid']['exclude'] = 1;
	$TCA['pages']['columns']['alias']['exclude'] = 1;
	$TCA['pages']['columns']['tx_realurl_pathsegment']['exclude'] = 1;

	t3lib_div::loadTCA('tt_content');
	$TCA['tt_content']['columns']['header_position']['exclude'] = 1;
	$TCA['tt_content']['columns']['header_link']['exclude'] = 1;
	$TCA['tt_content']['columns']['list_type']['config']['authMode'] = 'explicitAllow';
	$TCA['tt_content']['columns']['CType']['config']['authMode'] = 'explicitAllow';
}

if (TYPO3_MODE == 'BE') {
	t3lib_extMgm::addModule("web","txwecconfig","", t3lib_extMgm::extPath($_EXTKEY)."mod1/");

	if(t3lib_extMgm::isLoaded('wec_constants') || t3lib_extMgm::isLoaded('templavoila_framework')) {
		t3lib_extMgm::insertModuleFunction(
			"web_txwecconfig",		
			"tx_wecconfig_constants",
			t3lib_extMgm::extPath($_EXTKEY)."constants/class.tx_wecconfig_constants.php",
			"LLL:EXT:wec_config/constants/locallang.xml:title"
		);
	}

	if(t3lib_extMgm::isLoaded('templavoila')) {
		if (t3lib_extMgm::isLoaded('templavoila_framework')) {
			t3lib_extMgm::insertModuleFunction(
				"web_txwecconfig",		
				"tx_wecconfig_skins",
				t3lib_extMgm::extPath($_EXTKEY)."skins/class.tx_wecconfig_skins.php",
				"LLL:EXT:wec_config/skins/locallang.xml:title"
			);
		} else {
			t3lib_extMgm::insertModuleFunction(
				"web_txwecconfig",		
				"tx_wecconfig_templates",
				t3lib_extMgm::extPath($_EXTKEY)."templates/class.tx_wecconfig_templates.php",
				"LLL:EXT:wec_config/templates/locallang.xml:title"
			);
		}
	}

	require_once(t3lib_extMgm::extPath('wec_config').'features/class.tx_wecconfig_features.php');

	if(tx_wecconfig_features::countFeatures() > 0) {
		t3lib_extMgm::insertModuleFunction(
			"web_txwecconfig",		
			"tx_wecconfig_features",
			t3lib_extMgm::extPath($_EXTKEY)."features/class.tx_wecconfig_features.php",
			"LLL:EXT:wec_config/features/locallang.xml:title"
		);
	}
}

$TCA['tx_wecconfig_features'] = array (
	'ctrl' => array (
		'title' => 'LLL:EXT:wec_config/features/locallang_db.xml:tx_wecconfig_features',
		'label' => 'title',	
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY title',	
		'delete' => 'deleted',
		'adminOnly' => 1,
		'rootLevel' => 1,
		'enablecolumns' => array (
			'disabled' => 'disabled',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY)."features/tca.php",
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY)."features/icon.gif",
	),
	"feInterface" => array (
		'fe_admin_fieldList' => 'disabled,title,description,elements',
	),
);

/* Add context sensitive help */
t3lib_extMgm::addLLrefForTCAdescr('_MOD_web_wecconfig','EXT:wec_config/mod1/locallang_csh_web_wecconfig.xml');
t3lib_extMgm::addLLrefForTCAdescr('_MOD_web_wecconfig_constants','EXT:wec_config/constants/locallang_csh_web_wecconfig_constants.xml');
t3lib_extMgm::addLLrefForTCAdescr('_MOD_web_wecconfig_features','EXT:wec_config/features/locallang_csh_web_wecconfig_features.xml');
t3lib_extMgm::addLLrefForTCAdescr('_MOD_web_wecconfig_templates','EXT:wec_config/templates/locallang_csh_web_wecconfig_templates.xml');

/* Add some additional constants for the templavoila_framework extension if installed */
if (t3lib_extMgm::isLoaded('templavoila_framework')) {
	t3lib_extMgm::addStaticFile($_EXTKEY, 'static/','Additional TV Framework Constants (WEC)');
}

?>
