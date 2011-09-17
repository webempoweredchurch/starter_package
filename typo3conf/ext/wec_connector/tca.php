<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");

// adds the possiblity to switch the use of the "StoragePid"(general record Storage Page) for tx_wecconnector categories
//$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['wec_connector']);
//if ($confArr['useStoragePid']) {
    $fTableWhere = 'AND (tx_wecconnector_cat.pid=###CURRENT_PID### OR tx_wecconnector_cat.pid=###STORAGE_PID###) ';
//}

// ******************************************************************
// This is the standard TypoScript news category table, tt_news_cat
// ******************************************************************
$TCA['tx_wecconnector_cat'] = Array (
	'ctrl' => $TCA['tx_wecconnector_cat']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => 'sys_language_uid,l18n_parent,l18n_diffsource,title,image,shortcut,shortcut_target,hidden'
	),
	"feInterface" => $TCA["tx_wecconnector_cat"]["feInterface"],
	'columns' => Array (
		'sys_language_uid' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.language',
			'config' => Array (
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => Array(
					Array('LLL:EXT:lang/locallang_general.php:LGL.allLanguages',-1),
					Array('LLL:EXT:lang/locallang_general.php:LGL.default_value',0)
				)
			)
		),
		'l18n_parent' => Array (
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.l18n_parent',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('', 0),
				),
				'foreign_table' => 'tx_wecconnector_cat',
				'foreign_table_where' => 'AND tx_wecconnector_cat.pid=###CURRENT_PID### AND tx_wecconnector_cat.sys_language_uid IN (-1,0)',
			)
		),
		'l18n_diffsource' => Array (
			'config' => Array (
				'type' => 'passthrough'
			)
		),		
		'title' => Array (
			'label' => 'LLL:EXT:wec_connector/locallang_db.php:tx_wecconnector_cat.title',
			'config' => Array (
				'type' => 'input',
				'size' => '40',
				'max' => '256',
				'eval' => 'required'
			)
		),
		'image' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:wec_connector/locallang_db.php:tx_wecconnector_cat.image',
			'config' => Array (
				'type' => 'group',
				'internal_type' => 'file',
				'allowed' => 'gif,png,jpeg,jpg',
				'max_size' => 100,
				'uploadfolder' => 'uploads/tx_wecconnector',
				'show_thumbs' => 1,
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'sort_order' => Array (
			'label' => 'LLL:EXT:wec_connector/locallang_db.php:tx_wecconnector_cat.sort_order',
			'config' => Array (
				'type' => 'input',
				'size' => '3',
				'max' => '9999',
				'eval' => 'int',
			)
		),
		'hidden' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),		
	),
	'types' => Array (
		'0' => Array('showitem' => 'title,title_lang_ol,image;;1;;1-1-1-1,sort_order,hidden'),
	),
	'palettes' => Array (
		'1' => Array('showitem' => 'shortcut,shortcut_target'),
	)
);



$TCA["tx_wecconnector_group"] = Array (
	"ctrl" => $TCA["tx_wecconnector_group"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "parent_uid,user_uid,user_email,user_name"
	),
	"feInterface" => $TCA["tx_wecconnector_group"]["feInterface"],
	"columns" => Array (
		"parent_uid" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_connector/locallang_db.php:tx_wecconnector_group.parent_uid",
			"config" => Array (
				"type" => "input",
				"size" => "4",
				"max" => "6",
				"eval" => "int",
				"checkbox" => "0",
				"range" => Array (
					"upper" => "100000",
					"lower" => "10"
				),
				"default" => 0
			)
		),
		"user_uid" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_connector/locallang_db.php:tx_wecconnector_group.user_uid",
			"config" => Array (
				"type" => "input",
				"size" => "4",
				"max" => "6",
				"eval" => "int",
				"checkbox" => "0",
				"range" => Array (
					"upper" => "100000",
					"lower" => "10"
				),
				"default" => 0
			)
		),
		"user_email" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_connector/locallang_db.php:tx_wecconnector_group.user_email",
			"config" => Array (
				"type" => "input",
				"size" => "24",
				"max" => "48",
			)
		),
		"user_name" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_connector/locallang_db.php:tx_wecconnector_group.user_name",
			"config" => Array (
				"type" => "input",
				"size" => "24",
				"max" => "48",
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "parent_uid;;;;1-1-1, user_uid, user_email, user_name")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);


$TCA["tx_wecconnector_entries"] = Array (
	"ctrl" => $TCA["tx_wecconnector_entries"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "sys_language_uid,l18n_parent,l18n_diffsource,post_date,category,user_uid,name,email,subject,message,phone,address,city,state,zipcode,country,website_url,business_name,contact_name,image,is_response,hidden"
	),
	"feInterface" => $TCA["tx_wecconnector_entries"]["feInterface"],
	"columns" => Array (
		'sys_language_uid' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.language',
			'config' => Array (
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => Array(
					Array('LLL:EXT:lang/locallang_general.php:LGL.allLanguages',-1),
					Array('LLL:EXT:lang/locallang_general.php:LGL.default_value',0)
				)
			)
		),
		'l18n_parent' => Array (
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.l18n_parent',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('', 0),
				),
				'foreign_table' => 'tx_wecconnector_entries',
				'foreign_table_where' => 'AND tx_wecconnector_entries.pid=###CURRENT_PID### AND tx_wecconnector_entries.sys_language_uid IN (-1,0)',
			)
		),
		'l18n_diffsource' => Array (
			'config' => Array (
				'type' => 'passthrough'
			)
		),		
		'hidden' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),		
		"parent_uid" => Array (
			"exclude" => 0,
			"label" => "LLL:EXT:wec_connector/locallang_db.php:tx_wecconnector_entries.parent_uid",
			"config" => Array (
				"type" => "input",
				"size" => "4",
				"max" => "6",
				"eval" => "int",
				"checkbox" => "0",
				"range" => Array (
					"upper" => "100000",
					"lower" => "10"
				),
				"default" => 0
			)
		),
		"post_date" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_connector/locallang_db.php:tx_wecconnector_entries.post_date",
			"config" => Array (
				"type" => "input",
				"size" => "12",
				"max" => "20",
				"eval" => "datetime",
				"checkbox" => "0",
				"default" => "0"
			)
		),
		"category" => Array (
			"exclude" => 1,
			"l10n_mode" => "exclude",
			"label" => "LLL:EXT:wec_connector/locallang_db.php:tx_wecconnector_entries.category",
			"config" => Array (
				"type" => "select",
				"foreign_table" => "tx_wecconnector_cat",
				"foreign_table_where" => $fTableWhere." ORDER BY tx_wecconnector_cat.uid",
				"size" => 3,
				"autoSizeMax" => 10,
				"minitems" => 0,
				"maxitems" => 100,
				"wizards" => Array(
					"_PADDING" => 2,
					"_VERTICAL" => 1,
					"add" => Array(
						"type" => "script",
						"title" => "Create new category",
						"icon" => "add.gif",
						"params" => Array(
							"table"=>"tx_wecconnector_cat",
							"pid" => "###CURRENT_PID###",
							"setValue" => "set"
						),
						"script" => "wizard_add.php",
					),
					"edit" => Array(
							"type" => "popup",
							"title" => "Edit category",
							"script" => "wizard_edit.php",
							"popup_onlyOpenIfSelected" => 1,
							"icon" => "edit2.gif",
							"JSopenParams" => "height=350,width=580,status=0,menubar=0,scrollbars=1",
					),
				),
			)
		),
		"user_uid" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_connector/locallang_db.php:tx_wecconnector_entries.user_uid",
			"config" => Array (
				"type" => "input",
				"size" => "4",
				"max" => "6",
				"eval" => "int",
				"checkbox" => "0",
				"range" => Array (
					"upper" => "100000",
					"lower" => "10"
				),
				"default" => 0
			)
		),
		"name" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_connector/locallang_db.php:tx_wecconnector_entries.name",
			"config" => Array (
				"type" => "input",
				"size" => "32",
				"max" => "128",
			)
		),
		"email" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_connector/locallang_db.php:tx_wecconnector_entries.email",
			"config" => Array (
				"type" => "input",
				"size" => "30",
				"max" => "48",
			)
		),
		"subject" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_connector/locallang_db.php:tx_wecconnector_entries.subject",
			"config" => Array (
				"type" => "input",
				"size" => "30",
				"max" => "64",
			)
		),
		"message" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_connector/locallang_db.php:tx_wecconnector_entries.message",
			"config" => Array (
				"type" => "text",
				"cols" => "40",
				"rows" => "4",
			)
		),
		"phone" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_connector/locallang_db.php:tx_wecconnector_entries.phone",
			"config" => Array (
				"type" => "input",
				"size" => "16",
				"max" => "24",
			)
		),
		"location" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_connector/locallang_db.php:tx_wecconnector_entries.location",
			"config" => Array (
				"type" => "input",
				"size" => "30",
				"max" => "40",
			)
		),
		"address" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_connector/locallang_db.php:tx_wecconnector_entries.address",
			"config" => Array (
				"type" => "input",
				"size" => "30",
				"max" => "40",
			)
		),
		"city" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_connector/locallang_db.php:tx_wecconnector_entries.city",
			"config" => Array (
				"type" => "input",
				"size" => "30",
				"max" => "48",
			)
		),
		"state" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_connector/locallang_db.php:tx_wecconnector_entries.state",
			"config" => Array (
				"type" => "input",
				"size" => "6",
				"max" => "16",
			)
		),
		"zipcode" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_connector/locallang_db.php:tx_wecconnector_entries.zipcode",
			"config" => Array (
				"type" => "input",
				"size" => "8",
				"max" => "16",
			)
		),
		"country" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_connector/locallang_db.php:tx_wecconnector_entries.country",
			"config" => Array (
				"type" => "input",
				"size" => "16",
				"max" => "32",
			)
		),
		"website_url" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_connector/locallang_db.php:tx_wecconnector_entries.website_url",
			"config" => Array (
				"type" => "input",
				"size" => "30",
			)
		),
		"business_name" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_connector/locallang_db.php:tx_wecconnector_entries.business_name",
			"config" => Array (
				"type" => "input",
				"size" => "32",
				"max" => "48",
			)
		),
		"contact_name" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_connector/locallang_db.php:tx_wecconnector_entries.contact_name",
			"config" => Array (
				"type" => "input",
				"size" => "30",
				"max" => "48",
			)
		),
		"email2" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_connector/locallang_db.php:tx_wecconnector_entries.email2",
			"config" => Array (
				"type" => "input",
				"size" => "30",
				"max" => "48",
			)
		),
		'image' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:wec_connector/locallang_db.php:tx_wecconnector_entries.image',
			'config' => Array (
				'type' => 'group',
				'internal_type' => 'file',
				'allowed' => 'gif,png,jpeg,jpg',
				'max_size' => 100,
				'uploadfolder' => 'uploads/tx_wecconnector',
				'show_thumbs' => 1,
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'is_response' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:wec_connector/locallang_db.php:tx_wecconnector_entries.is_response',
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),		
		't3ver_label' => Array (
			'displayCond' => 'FIELD:t3ver_label:REQ:true',
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.versionLabel',
			'config' => Array (
				'type'=>'none',
				'cols' => 27
			)
		),		
	),
	"types" => Array (
		"0" => Array("showitem" => "parent_uid;;;;1-1-1, post_date, category;;;;2-2-2, user_uid, name, email, subject, message, phone, address, city, state, zipcode, country, website_url, business_name, contact_name, email2, image;;1;;3-3-3-3,hidden,is_response")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "shortcut,shortcut_target")
	)
);

?>