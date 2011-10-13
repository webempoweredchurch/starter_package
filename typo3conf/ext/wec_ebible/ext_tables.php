<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

require_once(t3lib_extMgm::extPath('wec_ebible')."class.tx_wecebible_itemsProcFunc.php");

/* Set up the tt_content fields for the frontend plugin */
t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi2']='layout,select_key,pages,recursive';

/* Adds the plugins and flexforms to the TCA */
t3lib_extMgm::addPlugin(Array('LLL:EXT:wec_ebible/locallang_db.xml:tt_content.list_type_pi2', $_EXTKEY.'_pi2'),'list_type');


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
			),
			'itemsProcFunc' => 'tx_wecebible_itemsProcFunc->getBibleTranslations'
		)
	),
);

// Workaround sr_feuser_register's inability to handle itemsProcFun by inserting items manually.
if (TYPO3_MODE == 'FE') {
	$pObj = new stdClass();
	t3lib_div::callUserFunction($translationTCA['tx_wecebible_translation']['config']['itemsProcFunc'], $translationTCA['tx_wecebible_translation']['config'], $pObj);
	unset($translationTCA['tx_wecebible_translation']['config']['itemsProcFunc']);
}

t3lib_extMgm::addTCAcolumns('fe_users', $translationTCA, 1);
$TCA['fe_users']['interface']['showRecordFieldList'] .= ',tx_wecebible_translation';
t3lib_extMgm::addToAllTCAtypes('fe_users', 'tx_wecebible_translation');

/* Add CSH for the translation field */
t3lib_extMgm::addLLrefForTCAdescr('fe_users','EXT:wec_ebible/csh/locallang_csh_feusers.xml');


?>