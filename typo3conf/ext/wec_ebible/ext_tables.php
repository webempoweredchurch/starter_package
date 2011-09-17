<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

require_once(t3lib_extMgm::extPath('wec_ebible')."class.tx_wecebible_itemsProcFunc.php");

if (TYPO3_MODE=='BE')    {
	t3lib_extMgm::addModule('tools','txwecebibleM1',"",t3lib_extMgm::extPath($_EXTKEY).'mod1/');
}

/* Set up the tt_content fields for the frontend plugin */
t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages,recursive';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform';
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi2']='layout,select_key,pages,recursive';

/* Adds the plugins and flexforms to the TCA */
t3lib_extMgm::addPlugin(Array('LLL:EXT:wec_ebible/locallang_db.xml:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:wec_ebible/pi1/flexform_ds.xml');

t3lib_extMgm::addPlugin(Array('LLL:EXT:wec_ebible/locallang_db.xml:tt_content.list_type_pi2', $_EXTKEY.'_pi2'),'list_type');


$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_wecebible_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_wecebible_pi1_wizicon.php';
$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_wecebible_pi2_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi2/class.tx_wecebible_pi2_wizicon.php';
	
t3lib_extMgm::addStaticFile($_EXTKEY,'static/', 'WEC eBible');

t3lib_div::loadTCA('fe_users');
$translationTCA = array(
	'tx_wecebible_translation' => Array (
		'exclude' => 1,
		'l10n_mode' => 'mergeIfNotBlank',
		'label' => 'LLL:EXT:wec_ebible/locallang_db.php:fe_users.tx_wecebible_translation',
		'config' => Array (
			'type' => 'select',
			'size' => 1,
			'items' => Array (
				Array('', ''),
				Array('English Standard Version', 'ESV'),
				Array('Holman Christian Standard Bible', 'HCSB'),
				Array('King James Version', 'KJV'),
				Array('New American Standard Bible', 'NASB'),
				Array('New Century Version', 'NCV'),
				Array('New King James Version', 'NKJV'),
				Array('New International Version', 'NIV'),
				Array('The Message', 'MSG'),
				Array('Today\'s New International Version', 'TNIV'),
				Array('------------------------------', '--div--'),
				Array('1909 Spanish Reina-Valera Antigua', 'SpaRV'),
				Array('Italian Riveduta (1927)', 'ItalRV'),
			),
			// 'itemsProcFunc' => 'tx_wecebible_itemsProcFunc->getBibleTranslations'
		)
	),
);

t3lib_extMgm::addTCAcolumns('fe_users', $translationTCA, 1);
$TCA['fe_users']['interface']['showRecordFieldList'] .= ',tx_wecebible_translation';
t3lib_extMgm::addToAllTCAtypes('fe_users', 'tx_wecebible_translation');

/* Add CSH for the translation field */
t3lib_extMgm::addLLrefForTCAdescr('fe_users','EXT:wec_ebible/csh/locallang_csh_feusers.xml');


?>