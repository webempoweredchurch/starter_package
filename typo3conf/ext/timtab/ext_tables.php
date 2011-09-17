<?php
//
//	$Id: ext_tables.php,v 1.11 2005/10/29 22:07:27 ingorenner Exp $
//

if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

t3lib_extMgm::allowTableOnStandardPages('tx_timtab_blogroll');
// finding the rel path takes time, so we store it in a variable
$thisExtRelPath = t3lib_extMgm::extRelPath($_EXTKEY); 

$TCA['tx_timtab_blogroll'] = array (
	'ctrl' => array (
		'title' => 'LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll',		
		'label' => 'name',	
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'sortby' => 'sorting',	
		'delete' => 'deleted',	
		'dividers2tabs' => TRUE,
		'enablecolumns' => array (		
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile' => $thisExtRelPath.'icon_tx_timtab_blogroll.gif',
	),
	'feInterface' => array (
		'fe_admin_fieldList' => 'hidden, url, name, description, rel_identity, rel_friendship, rel_physical, rel_professional, rel_geographical, rel_family, rel_romantic, img_uri, rss_uri, notes, rating, target',
	)
);

$tempColumns = Array (
	'tx_timtab_trackbacks' => array (		
		'exclude' => 1,		
		'label' => 'LLL:EXT:timtab/locallang_db.php:tt_news.tx_timtab_trackbacks',		
		'config' => array (
			'type' => 'text',
			'cols' => '40',	
			'rows' => '7',
		),
		'defaultExtras' => 'nowrap'
	),	
	'tx_timtab_comments_allowed' => array (
		'exclude' => 1,
		'label' => 'LLL:EXT:timtab/locallang_db.php:tt_news.tx_timtab_comments_allowed',
		'config' => Array (
			'type' => 'check',
			'default' => 1
		)
	),
	'tx_timtab_ping_allowed' => array (
		'exclude' => 1,
		'label' => 'LLL:EXT:timtab/locallang_db.php:tt_news.tx_timtab_ping_allowed',
		'config' => Array (
			'type' => 'check',
			'default' => 1
		)
	),
);

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key';
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi2']='layout,select_key';
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi3']='layout,select_key';

t3lib_extMgm::addPlugin(Array('LLL:EXT:timtab/locallang_db.php:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');
t3lib_extMgm::addPlugin(Array('LLL:EXT:timtab/locallang_db.php:tt_content.list_type_pi3', $_EXTKEY.'_pi3'),'list_type');

t3lib_div::loadTCA('tt_news');
t3lib_extMgm::addTCAcolumns('tt_news', $tempColumns, 1);
$TCA['tt_news']['ctrl']['typeicons'][] = $thisExtRelPath.'icon_tx_timtab_post.gif';
$TCA['tt_news']['columns']['type']['config']['items'][] = Array('LLL:EXT:timtab/locallang_db.php:tt_news.type.I.timtab', 3);
$TCA['tt_news']['interface']['showRecordFieldList'] .= ',tx_timtab_trackbacks,tx_timtab_ping_allowed,tx_timtab_comments_allowed';
$TCA['tt_news']['types']['3'] = array();

t3lib_div::loadTCA('tx_veguestbook_entries');
$TCA['tx_veguestbook_entries']['columns']['homepage']['config']['max'] = 2083;

t3lib_extMgm::addToAllTCAtypes('tt_news', 'title;;1;;,type,editlock,datetime;;2;;1-1-1,author;;3;;,short,bodytext;;4;richtext[paste|bold|italic|underline|formatblock|class|left|center|right|orderedlist|unorderedlist|outdent|indent|link|table|image]:rte_transform[flag=rte_enabled|mode=ts];4-4-4,no_auto_pb,--div--;Relations,category,image;;;;1-1-1,imagecaption;;5;;,links;;;;2-2-2,related;;;;3-3-3,news_files;;;;4-4-4,--div--;Blog Post,tx_timtab_trackbacks;;;;1-1-1,tx_timtab_comments_allowed;;;;2-2-2,tx_timtab_ping_allowed;;;;', 3);

t3lib_extMgm::addStaticFile($_EXTKEY,'static/kubrick_main/','Kubrick (default weblog template)');
t3lib_extMgm::addStaticFile($_EXTKEY,'static/webservice/','Blog Webservices');

if (TYPO3_MODE=="BE") {
	$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_timtab_pi1_wizicon"] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_timtab_pi1_wizicon.php';
	$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_timtab_pi3_wizicon"] = t3lib_extMgm::extPath($_EXTKEY).'pi3/class.tx_timtab_pi3_wizicon.php';
}
?>
