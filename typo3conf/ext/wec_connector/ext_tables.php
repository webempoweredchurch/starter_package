<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");
t3lib_div::loadTCA("tt_content");
## $confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['tx_wecconnector_entries']);

t3lib_extMgm::allowTableOnStandardPages("tx_wecconnector_entries");

t3lib_extMgm::allowTableOnStandardPages("tx_wecconnector_cat");

$TCA["tx_wecconnector_cat"] = Array (
	"ctrl" => Array (
		"title" => "LLL:EXT:wec_connector/locallang_db.php:tx_wecconnector_cat",
		"label" => "title",
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",
		"languageField" => "sys_language_uid",
		"transOrigPointerField" => "l18n_parent",
		"transOrigDiffSourceField" => "l18n_diffsource",		
		"default_sortby" => "ORDER BY sort_order",
		"delete" => "deleted",
		"enablecolumns" => Array (
			"disabled" => "hidden",
		),		
		"shadowColumnsForNewPlaceholders" => "sys_language_uid,l18n_parent",
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."res/icon_tx_wecconnector_category.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "title, image, sort_order, sys_language_uid, l18n_parent, l18n_diffsource, hidden",
	)
);

$TCA["tx_wecconnector_group"] = Array (
	"ctrl" => Array (
		"title" => "LLL:EXT:wec_connector/locallang_db.php:tx_wecconnector_group",
		"label" => "user_email",
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",
		"sortby" => "sorting",
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."res/icon_tx_wecconnector_group.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "parent_uid, user_uid, user_email, user_name",
	)
);

$TCA["tx_wecconnector_entries"] = Array (
	"ctrl" => Array (
		"title" => "LLL:EXT:wec_connector/locallang_db.php:tx_wecconnector_entries",
		"label" => "name",
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",
		"languageField" => "sys_language_uid",
		"transOrigPointerField" => "l18n_parent",
		"transOrigDiffSourceField" => "l18n_diffsource",		
		"default_sortby" => "ORDER BY post_date DESC",
		"delete" => "deleted",
		"enablecolumns" => Array (
			"disabled" => "hidden",
		),		
		"versioningWS" => TRUE,
		"origUid" => "t3_origuid",
		"shadowColumnsForNewPlaceholders" => "sys_language_uid,l18n_parent",
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."res/icon_tx_wecconnector_entries.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "parent_uid,  sys_language_uid, l18n_parent, l18n_diffsource, post_date, category, user_uid, name, email, message, phone, location, address, city, state, zipcode, country, website_url, business_name, contact_name, email2, image, hidden",
	)
);

##t3lib_extMgm::addToInsertRecords("tx_wecconnector_entries");
t3lib_extMgm::addToInsertRecords("tx_wecconnector_cat");

$TCA["tt_content"]["types"]["list"]["subtypes_excludelist"][$_EXTKEY."_pi1"]="layout,select_key,pages,recursive";

t3lib_extMgm::addPlugin(Array("LLL:EXT:wec_connector/locallang_db.php:tt_content.list_type_pi1", $_EXTKEY."_pi1"),"list_type");

$TCA["tt_content"]["types"]["list"]["subtypes_addlist"][$_EXTKEY."_pi1"]="pi_flexform";
t3lib_extMgm::addPiFlexFormValue($_EXTKEY."_pi1", "FILE:EXT:wec_connector/flexform_ds.xml");

t3lib_extMgm::addStaticFile($_EXTKEY,"static/ts/","WEC Connector (old) Template");
t3lib_extMgm::addStaticFile($_EXTKEY,"static/tsnew/","WEC Connector Template");
t3lib_extMgm::addStaticFile($_EXTKEY, 'static/rss2/', 'WEC Connector RSS 2.0 Feed' );

if (TYPO3_MODE=="BE")    $TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_wecconnector_pi1_wizicon"] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_wecconnector_pi1_wizicon.php';

?>