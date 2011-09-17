<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');


// get extension configuration
$extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['wec_sermons']);
$uploadpath = $extConf['resourceUploadPath'];
$uploadpath = $uploadpath ? $uploadpath : 'uploads/tx_wecsermons';

$TCA["tx_wecsermons_resources"] = Array (
	"ctrl" => $TCA["tx_wecsermons_resources"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "sys_language_uid,l18n_parent,l18n_diffsource,hidden,fe_group,description,title,graphic,type"
	),
	"feInterface" => $TCA["tx_wecsermons_resources"]["feInterface"],
	"columns" => Array (
		'sys_language_uid' => Array (
			'exclude' => '1',
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
			'exclude' => '1',
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.l18n_parent',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('', '0'),
				),
				'foreign_table' => 'tx_wecsermons_resources',
				'foreign_table_where' => 'AND tx_wecsermons_resources.pid=###CURRENT_PID### AND tx_wecsermons_resources.sys_language_uid IN (-1,0)',
			)
		),
		'l18n_diffsource' => Array (
			'config' => Array (
				'type' => 'passthrough'
			)
		),
		"hidden" => Array (
			"exclude" => '1',
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"fe_group" => Array (
			"exclude" => "1",
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.fe_group",
			"config" => Array (
				"type" => "select",
				"items" => Array (
					Array("", 0),
					Array("LLL:EXT:lang/locallang_general.php:LGL.hide_at_login", -1),
					Array("LLL:EXT:lang/locallang_general.php:LGL.any_login", -2),
					Array("LLL:EXT:lang/locallang_general.php:LGL.usergroups", "--div--")
				),
				"foreign_table" => "fe_groups"
			)
		),
		"starttime" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.starttime",
			"config" => Array (
				"type" => "input",
				"size" => "8",
				"max" => "20",
				"eval" => "date",
				"default" => "0",
				"checkbox" => "0"
			)
		),
		"endtime" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.endtime",
			"config" => Array (
				"type" => "input",
				"size" => "8",
				"max" => "20",
				"eval" => "date",
				"checkbox" => "0",
				"default" => "0",
				"range" => Array (
					"upper" => mktime(0,0,0,12,31,2020),
					"lower" => mktime(0,0,0,date("m")-1,date("d"),date("Y"))
				)
			)
		),
		"description" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_resources.description",
			"config" => Array (
				"type" => "text",
				"cols" => "30",
				"rows" => "5",
				"wizards" => Array(
					"_PADDING" => 2,
					"RTE" => Array(
						"notNewRecords" => 1,
						"RTEonly" => 1,
						"type" => "script",
						"title" => "Full screen Rich Text Editing|Formatteret redigering i hele vinduet",
						"icon" => "wizard_rte2.gif",
						"script" => "wizard_rte.php",
					),
				),
			)
		),
		"title" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_resources.title",
			"config" => Array (
				"type" => "input",
				"size" => "30",
			)
		),
		"subtitle" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_resources.subtitle",
			"config" => Array (
				"type" => "text",
				"cols" => "40",
				"rows" => "4",
				"wrap" => "virtual",
			)
		),
		"graphic" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_resources.graphic",
			"config" => Array (
				"type" => "group",
				"internal_type" => "file",
				"allowed" => "gif,png,jpeg,jpg",
				"max_size" => 5120,
				"uploadfolder" => "uploads/tx_wecsermons",
				"show_thumbs" => 1,
				"size" => 1,
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"alttitle" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_resources.alttitle",
			"config" => Array (
				"type" => "text",
				"wrap" => "virtual",
			)
		),
		"type" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_resources.type",
			"config" => Array (
				"type" => "select",
				"items" => Array (
					Array('LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_resources.type.I.0','0'),
				),
				"size" => 1,
				"maxitems" => 1,
				"itemsProcFunc" => 'tx_wecsermons_resourceTypeTca->resourceType_items',
			)
		),
		'file' => Array (
			'exclude' => 1,
			'l10n_mode' => 'exclude',
			'label' => 'LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_resources.file',
			'config' => Array (
				'type' => 'group',
				'internal_type' => 'file',
				'allowed' => '',	// Must be empty for disallowed to work.
				'disallowed' => 'php,php3',
				'max_size' => $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'],
				'uploadfolder' => $uploadpath,
				'show_thumbs' => '1',
				'size' => 4,
				'autoSizeMax' => 4,
				'maxitems' => 10,
				'minitems' => 0,
			)
		),
		"webaddress1" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_resources.webaddress1",
			"config" => Array (
				"type" => "input",
				"size" => "30",
				"eval" => "trim",
				'wizards' => Array(
					'_PADDING' => 2,
					'link' => Array(
						'type' => 'popup',
						'title' => 'Link',
						'icon' => 'link_popup.gif',
						'script' => 'browse_links.php?mode=wizard',
						'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1'
					)
				)
			)
		),
		"webaddress2" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_resources.webaddress2",
			"config" => Array (
				"type" => "input",
				"size" => "30",
				"eval" => "trim",
				'wizards' => Array(
					'_PADDING' => 2,
					'link' => Array(
						'type' => 'popup',
						'title' => 'Link',
						'icon' => 'link_popup.gif',
						'script' => 'browse_links.php?mode=wizard',
						'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1'
					)
				)
			)
		),
		"webaddress3" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_resources.webaddress3",
			"config" => Array (
				"type" => "input",
				"size" => "30",
				"eval" => "trim",
				'wizards' => Array(
					'_PADDING' => 2,
					'link' => Array(
						'type' => 'popup',
						'title' => 'Link',
						'icon' => 'link_popup.gif',
						'script' => 'browse_links.php?mode=wizard',
						'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1'
					)
				)
			)
		),
		"rendered_record" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_resources.rendered_record",
			"config" => Array (
				"type" => "group",
				"internal_type" => "db",
				"allowed" => "*",
				"size" => 1,
				"minitems" => 0,
				"maxitems" => 1,
			),
		),
		"summary" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_resources.summary",
			"config" => Array (
				"type" => "text",
				"cols" => "40",
				"rows" => "2",
				"wrap" => "virtual",
			)
		),
		"islinked" => Array (        
			"exclude" => 1,        
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_resources.islinked",        
			"config" => Array (
				"type" => "check",
				"default" => "1"
			)
		)
	),
	"types" => Array (
		"0" => Array("showitem" => "sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, hidden;;1, type;;;;2-2-2, title, description;;;richtext:rte_transform[mode=ts_css];,  graphic;;2;;3-3-3, file;;;;4-4-4,webaddress1,subtitle;;;;5-5-5, summary, hidden;;1;;6-6-6"),
	),
	"palettes" => Array (
		"1" => Array("showitem" => "starttime,endtime,l18n_parent,l18n _diffsource,fe_group,islinked"),
		"2" => Array("showitem" => "alttitle"),
	)
);

$TCA["tx_wecsermons_resource_types"] = Array (
	"ctrl" => $TCA["tx_wecsermons_resource_types"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "sys_language_uid,l18n_parent,l18n_diffsource,hidden,fe_group,description,title,graphic"
	),
	"feInterface" => $TCA["tx_wecsermons_resource_types"]["feInterface"],
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
				'foreign_table' => 'tx_wecsermons_resource_types',
				'foreign_table_where' => 'AND tx_wecsermons_resource_types.pid=###CURRENT_PID### AND tx_wecsermons_resource_types.sys_language_uid IN (-1,0)',
			)
		),
		'l18n_diffsource' => Array (
			'config' => Array (
				'type' => 'passthrough'
			)
		),
		"hidden" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"fe_group" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.fe_group",
			"config" => Array (
				"type" => "select",
				"items" => Array (
					Array("", 0),
					Array("LLL:EXT:lang/locallang_general.php:LGL.hide_at_login", -1),
					Array("LLL:EXT:lang/locallang_general.php:LGL.any_login", -2),
					Array("LLL:EXT:lang/locallang_general.php:LGL.usergroups", "--div--")
				),
				"foreign_table" => "fe_groups"
			)
		),
		"description" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_resource_types.description",
			"config" => Array (
				"type" => "text",
				"cols" => "30",
				"rows" => "5",
				"wizards" => Array(
					"_PADDING" => 2,
					"RTE" => Array(
						"notNewRecords" => 1,
						"RTEonly" => 1,
						"type" => "script",
						"title" => "Full screen Rich Text Editing|Formatteret redigering i hele vinduet",
						"icon" => "wizard_rte2.gif",
						"script" => "wizard_rte.php",
					),
				),
			)
		),
		"title" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_resource_types.title",
			"config" => Array (
				"type" => "input",
				"size" => "30",
				"eval" => "required",
			)
		),
		"icon" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_resource_types.icon",
			"config" => Array (
				"type" => "group",
				"internal_type" => "file",
				"allowed" => "gif,png,jpeg,jpg",
				"max_size" => 500,
				"uploadfolder" => "uploads/tx_wecsermons",
				"show_thumbs" => 1,
				"size" => 1,
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"alttitle" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_resources_types.alttitle",
			"config" => Array (
				"type" => "text",
				"size" => "30",
			)
		),
		"marker_name" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_resource_types.marker_name",
			"config" => Array (
				"type" => "input",
				"size" => "30",
				"eval" => "required,upper,nospace",
			)
		),
		"template_name" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_resource_types.template_name",
			"config" => Array (
				"type" => "input",
				"size" => "30",
				"eval" => "upper,nospace",
			)
		),
		"avail_fields" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_resource_types.avail_fields",
			"config" => Array (
				"type" => "select",
				"items" => Array (
					Array("LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_resource_types.avail_fields.I.0", "description"),
					Array("LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_resource_types.avail_fields.I.1", "graphic"),
					Array("LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_resource_types.avail_fields.I.2", "file"),
					Array("LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_resource_types.avail_fields.I.3", "webaddress1"),
					Array("LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_resource_types.avail_fields.I.4", "webaddress2"),
					Array("LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_resource_types.avail_fields.I.5", "webaddress3"),
					Array("LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_resource_types.avail_fields.I.6", "itunes_metadata"),
				),
				"size" => 7,
				"minitems" => 0,
				"maxitems" => 7,

			),
		),
		"type" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_resource_types.type",
			"config" => Array (
				"type" => "select",
				"items" => Array (
					Array('LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_resource_types.type.I.0','0'),
					Array('LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_resource_types.type.I.1','1'),
				),
				"size" => 1,
				"maxitems" => 1,
			)
		),
		"querystring_param" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_resource_types.querystring_param",
			"config" => Array (
				"type" => "input",
				"size" => "30",
			)
		),
		"mime_type" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_resource_types.mime_type",
			"config" => Array (
				"type" => "input",
				"size" => "30",
			)
		),
		"typoscript_object_name" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_resource_types.typoscript_object_name",
			"config" => Array (
				"type" => "input",
				"size" => "30",
				"eval" => "required,nospace",
			)
		),
		"rendering_page" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_resource_types.rendering_page",
			"config" => Array (
				"type" => "group",
				"internal_type" => "db",
				"allowed" => "pages",
				"prepend_tname" => 0,
				"size" => 1,
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, hidden;;1, type, title;;;;2-2-2, marker_name, template_name, mime_type, typoscript_object_name, description;;;richtext:rte_transform[mode=ts_css];3-3-3, icon;;2, avail_fields"),
		"1" => Array("showitem" => "sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, hidden;;1, type, title;;;;2-2-2, marker_name, template_name, querystring_param, rendering_page, typoscript_object_name, description;;;richtext:rte_transform[mode=ts_css];3-3-3, icon;;2, avail_fields")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "fe_group"),
		"2" => Array("showitem" => "alttitle"),
	)
);

$TCA["tx_wecsermons_sermons_resources_rel"] = Array (
	"ctrl" => $TCA["tx_wecsermons_sermons_resources_rel"]["ctrl"],
	"feInterface" => $TCA["tx_wecsermons_sermons_resources_rel"]["feInterface"],
	"columns" => Array (
		"sermonid" => Array (
			"exclude" => "1",
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_sermons_resources_rel.sermonid",
			"config" => Array (
				"type" => "select",
				"foreign_table" => "tx_wecsermons_sermons",
				"size" => 1,
				"minitems" => 0,
				"maxitems" => 1
			)
		),
		"resourceid" => Array (
			"exclude" => "1",
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_sermons_resources_rel.resourceid",
			"config" => Array (
				"type" => "select",
				"foreign_table" => "tx_wecsermons_resources",
				"foreign_table_where" => "ORDER BY tx_wecsermons_resources.title",
				"size" => 1,
				"minitems" => 0,
				"maxitems" => 1
			)
		),
		"sorting" => Array (
			"config" => Array (
				"type" => "passthrough"
			)
		)
	),
	"types" => Array (
		"0" => Array ( "showitem" => "sermonid,resourceid" )
	),
	"palettes" => Array (
		"1" => Array ( "showitem" => "sermonid,resourceid" )
	)
);

$TCA["tx_wecsermons_series_resources_rel"] = Array (
	"ctrl" => $TCA["tx_wecsermons_series_resources_rel"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "seriesid,resourceid"
	),
	"feInterface" => $TCA["tx_wecsermons_series_resources_rel"]["feInterface"],
	"columns" => Array (
		"seriesid" => Array (
			"exclude" => "1",
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_series_resources_rel.seriesid",
			"config" => Array (
				"type" => "select",
				"foreign_table" => "tx_wecsermons_series",
				"size" => "1",
				"minitems" => "0",
				"maxitems" => "1"
			)
		),
		"resourceid" => Array (
			"exclude" => "1",
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_series_resources_rel.resourceid",
			"config" => Array (
				"type" => "select",
				"foreign_table" => "tx_wecsermons_resources",
				"foreign_table_where" => "ORDER BY tx_wecsermons_resources.title",
				"size" => "1",
				"minitems" => "0",
				"maxitems" => "1"
			)
		),
		"sorting" => Array (
			"config" => Array (
				"type" => "passthrough"
			)
		)
	)
);



$TCA["tx_wecsermons_sermons"] = Array (
	"ctrl" => $TCA["tx_wecsermons_sermons"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "sys_language_uid,l18n_parent,l18n_diffsource,hidden,starttime,endtime,fe_group,title,occurrence_date,description,scripture,keywords,graphic,series,topics,record_type,resources"
	),
	"feInterface" => $TCA["tx_wecsermons_sermons"]["feInterface"],
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
				'foreign_table' => 'tx_wecsermons_sermons',
				'foreign_table_where' => 'AND tx_wecsermons_sermons.pid=###CURRENT_PID### AND tx_wecsermons_sermons.sys_language_uid IN (-1,0)',
			)
		),
		'l18n_diffsource' => Array (
			'config' => Array (
				'type' => 'passthrough'
			)
		),
		"hidden" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"starttime" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.starttime",
			"config" => Array (
				"type" => "input",
				"size" => "8",
				"max" => "20",
				"eval" => "date",
				"default" => "0",
				"checkbox" => "0"
			)
		),
		"endtime" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.endtime",
			"config" => Array (
				"type" => "input",
				"size" => "8",
				"max" => "20",
				"eval" => "date",
				"checkbox" => "0",
				"default" => "0",
				"range" => Array (
					"upper" => mktime(0,0,0,12,31,2020),
					"lower" => mktime(0,0,0,date("m")-1,date("d"),date("Y"))
				)
			)
		),
		"fe_group" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.fe_group",
			"config" => Array (
				"type" => "select",
				"items" => Array (
					Array("", 0),
					Array("LLL:EXT:lang/locallang_general.php:LGL.hide_at_login", -1),
					Array("LLL:EXT:lang/locallang_general.php:LGL.any_login", -2),
					Array("LLL:EXT:lang/locallang_general.php:LGL.usergroups", "--div--")
				),
				"foreign_table" => "fe_groups"
			)
		),
		"title" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_sermons.title",
			"config" => Array (
				"type" => "input",
				"size" => "30",
				"eval" => "required",
			)
		),
		"subtitle" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_sermons.subtitle",
			"config" => Array (
				"type" => "text",
				"cols" => "30",
				"rows" => "2",
			)
		),
		"occurrence_date" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_sermons.occurrence_date",
			"config" => Array (
				"type" => "input",
				"size" => "8",
				"max" => "20",
				"eval" => "date",
				"checkbox" => "0",
				"default" => "0"
			)
		),
		"description" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_sermons.description",
			"config" => Array (
				"type" => "text",
				"cols" => "30",
				"rows" => "5",
				"wizards" => Array(
					"_PADDING" => 2,
					"RTE" => Array(
						"notNewRecords" => 1,
						"RTEonly" => 1,
						"type" => "script",
						"title" => "Full screen Rich Text Editing|Formatteret redigering i hele vinduet",
						"icon" => "wizard_rte2.gif",
						"script" => "wizard_rte.php",
					),
				),
			)
		),
		"scripture" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_sermons.scripture",
			"config" => Array (
				"type" => "input",
				"size" => "48",
			)
		),
		"keywords" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_sermons.keywords",
			"config" => Array (
				"type" => "input",
				"size" => "30",
			)
		),
		"graphic" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_sermons.graphic",
			"config" => Array (
				"type" => "group",
				"internal_type" => "file",
				"allowed" => "gif,png,jpeg,jpg",
				"max_size" => 5120,
		                "show_thumbs" => 1,
 				"uploadfolder" => "uploads/tx_wecsermons",
				"size" => 1,
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"alttitle" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_sermons.alttitle",
			"config" => Array (
				"type" => "text",
				"wrap" => "virtual",
			)
		),
		"series" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_sermons.series",
			"config" => Array (
				"type" => "inline",
				"foreign_table" => "tx_wecsermons_sermons_series_rel",
				"foreign_field" => "sermonid",
				"foreign_label" => "seriesid",
				"foreign_selector" => "seriesid",
				"foreign_unique" => "seriesid",
				"appearance" => Array (
					"newRecordLinkAddTitle" => "1",
					"expandSingle" => "1",
					"collapseAll" => "1",
					"useCombination" => "1"
				),
				"size" => 4,
				"minitems" => 0,
				"maxitems" => 100
			)
		),
		"topics" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_sermons.topics",
			"config" => Array (
				"type" => "inline",
				"foreign_table" => "tx_wecsermons_sermons_topics_rel",
				"foreign_field" => "sermonid",
				"foreign_label" => "topicid",
				"foreign_selector" => "topicid",
				"foreign_unique" => "topicid",
				"appearance" => Array (
					"newRecordLinkAddTitle" => "1",
					"expandSingle" => "1",
					"collapseAll" => "1",
					"useCombination" => "1"
				),
				"size" => 4,
				"minitems" => 0,
				"maxitems" => 10
			)
		),
		"resources" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_sermons.resources",
			"config" => Array (
				"type" => "inline",
				"foreign_table" => "tx_wecsermons_sermons_resources_rel",
				"foreign_field" => "sermonid",
				"foreign_label" => "resourceid",
				"foreign_selector" => "resourceid",
				"foreign_unique" => "resourceid",
				"foreign_sortby" => "sorting",
				"appearance" => Array (
					"newRecordLinkAddTitle" => "1",
					"expandSingle" => "1",
					"collapseAll" => "1",
					"useCombination" => "1",
					"useSortable" => "1"
				),
				"size" => 10,
				"minitems" => 0,
				"maxitems" => 100
			)
		),
		"speakers" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_sermons.speakers",
			"config" => Array (
				"type" => "inline",
				"foreign_table" => "tx_wecsermons_sermons_speakers_rel",
				"foreign_field" => "sermonid",
				"foreign_label" => "speakerid",
				"foreign_selector" => "speakerid",
				"foreign_unique" => "speakerid",
				"appearance" => Array (
					"newRecordLinkAddTitle" => "1",
					"expandSingle" => "1",
					"collapseAll" => "1",
					"useCombination" => "1"
				),
				"size" => 4,
				"minitems" => 0,
				"maxitems" => 100,
			)
		),
		"islinked" => Array (        
			"exclude" => 1,        
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_sermons.islinked",        
			"config" => Array (
				"type" => "check",
				"default" => "1",
			)
		),
		"current" => Array (        
			"exclude" => 1,        
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_series.current",        
			"config" => Array (
				"type" => "check",
				"default" => "0",
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "sys_language_uid;;;;1-1-1,l18n_parent,l18n_diffsource,hidden;;1,title;;3;;2-2-2,occurrence_date,scripture,description;;;richtext:rte_transform[mode=ts_css],resources;;;;3-3-3,speakers,series,topics,graphic;;2;;4-4-4,keywords"),
	),
	"palettes" => Array (
		"1" => Array("showitem" => "starttime,endtime,fe_group,islinked,current"),
		"2" => Array("showitem" => "alttitle"),
		"3" => Array("showitem" => "subtitle"),
	)
);



$TCA["tx_wecsermons_series"] = Array (
	"ctrl" => $TCA["tx_wecsermons_series"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "hidden,title,description,scripture,startdate,enddate,graphic,seasons"
	),
	"feInterface" => $TCA["tx_wecsermons_series"]["feInterface"],
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
		"hidden" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"fe_group" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.fe_group",
			"config" => Array (
				"type" => "select",
				"items" => Array (
					Array("", 0),
					Array("LLL:EXT:lang/locallang_general.php:LGL.hide_at_login", -1),
					Array("LLL:EXT:lang/locallang_general.php:LGL.any_login", -2),
					Array("LLL:EXT:lang/locallang_general.php:LGL.usergroups", "--div--")
				),
				"foreign_table" => "fe_groups"
			)
		),
		"starttime" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.starttime",
			"config" => Array (
				"type" => "input",
				"size" => "8",
				"max" => "20",
				"eval" => "date",
				"default" => "0",
				"checkbox" => "0"
			)
		),
		"endtime" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.endtime",
			"config" => Array (
				"type" => "input",
				"size" => "8",
				"max" => "20",
				"eval" => "date",
				"checkbox" => "0",
				"default" => "0",
				"range" => Array (
					"upper" => mktime(0,0,0,12,31,2020),
					"lower" => mktime(0,0,0,date("m")-1,date("d"),date("Y"))
				)
			)
		),
		"title" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_series.title",
			"config" => Array (
				"type" => "input",
				"size" => "30",
				"eval" => "required",
			)
		),
		"subtitle" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_series.subtitle",
			"config" => Array (
				"type" => "text",
				"cols" => "30",
				"rows" => "2",
			)
		),
		"description" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_series.description",
			"config" => Array (
				"type" => "text",
				"cols" => "30",
				"rows" => "5",
				"wizards" => Array(
					"_PADDING" => 2,
					"RTE" => Array(
						"notNewRecords" => 1,
						"RTEonly" => 1,
						"type" => "script",
						"title" => "Full screen Rich Text Editing|Formatteret redigering i hele vinduet",
						"icon" => "wizard_rte2.gif",
						"script" => "wizard_rte.php",
					),
				),
			)
		),
		"scripture" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_series.scripture",
			"config" => Array (
				"type" => "input",
				"size" => "48",
			)
		),
		"startdate" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_series.startdate",
			"config" => Array (
				"type" => "input",
				"size" => "8",
				"max" => "20",
				"eval" => "date",
				"checkbox" => "0",
				"default" => "0"
			)
		),
		"enddate" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_series.enddate",
			"config" => Array (
				"type" => "input",
				"size" => "8",
				"max" => "20",
				"eval" => "date",
				"checkbox" => "0",
				"default" => "0"
			)
		),
		"graphic" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_series.graphic",
			"config" => Array (
				"type" => "group",
				"internal_type" => "file",
				"allowed" => "gif,png,jpeg,jpg",
				"max_size" => 5120,
                "show_thumbs" => 1,
				"uploadfolder" => "uploads/tx_wecsermons",
				"size" => 1,
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"alttitle" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_series.alttitle",
			"config" => Array (
				"type" => "text",
				"wrap" => "virtual",
			)
		),
		"seasons" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_series.seasons",
			"config" => Array (
				"type" => "inline",
				"foreign_table" => "tx_wecsermons_series_seasons_rel",
				"foreign_field" => "seriesid",
				"foreign_label" => "seasonid",
				"foreign_selector" => "seasonid",
				"foreign_unique" => "seasonid",
				"appearance" => Array (
					"newRecordLinkAddTitle" => "1",
					"expandSingle" => "1",
					"collapseAll" => "1",
					"useCombination" => "1"
				),
				"size" => 4,
				"minitems" => 0,
				"maxitems" => 10
			)
		),
		"topics" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_sermons.topics",
			"config" => Array (
				"type" => "inline",
				"foreign_table" => "tx_wecsermons_series_topics_rel",
				"foreign_field" => "seriesid",
				"foreign_label" => "topicid",
				"foreign_selector" => "topicid",
				"foreign_unique" => "topicid",
				"appearance" => Array (
					"newRecordLinkAddTitle" => "1",
					"expandSingle" => "1",
					"collapseAll" => "1",
					"useCombination" => "1"
				),
				"size" => 4,
				"minitems" => 0,
				"maxitems" => 10
			)
		),
		"resources" => Array (
			"exclude" => "1",
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_series.resources",
			"config" => Array (
				"type" => "inline",
				"foreign_table" => "tx_wecsermons_series_resources_rel",
				"foreign_field" => "seriesid",
				"foreign_label" => "resourceid",
				"foreign_selector" => "resourceid",
				"foreign_unique" => "resourceid",
				"foreign_sortby" => "sorting",
				"appearance" => Array (
					"newRecordLinkAddTitle" => "1",
					"expandSingle" => "1",
					"collapseAll" => "1",
					"useCombination" => "1",
					"useSortable" => "1"
				),
				"size" => 4,
				"minitems" => 0,
				"maxitems" => 100
			)
		),
		"keywords" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_sermons.keywords",
			"config" => Array (
				"type" => "input",
				"size" => "30",
			)
		),
		"islinked" => Array (        
			"exclude" => 1,        
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_series.islinked",        
			"config" => Array (
				"type" => "check",
				"default" => "1"
			)
		),
		"current" => Array (        
			"exclude" => 1,        
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_series.current",        
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		)
	),
	"types" => Array (
		"0" => Array("showitem" => "sys_language_uid,l18n_parent,l18n_diffsource,hidden;;1;;1-1-1,title;;3;;2-2-2,startdate;;;;3-3-3,enddate,description;;;richtext:rte_transform[mode=ts_css];4-4-4,scripture;;;;5-5-5,resources,keywords,seasons,topics,graphic;;2")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "starttime,endtime,fe_group,islinked,current"),
		"2" => Array("showitem" => "alttitle"),
		"3" => Array("showitem" => "subtitle"),
	)
);

$TCA["tx_wecsermons_sermons_series_rel"] = Array (
	"ctrl" => $TCA["tx_wecsermons_sermons_series_rel"]["ctrl"],
	"interfaces" => Array (
		"showRecordFieldList" => "sermonid,seriesid"
	),
	"feInterface" => $TCA["tx_wecsermons_sermons_series_rel"]["feInterface"],
	"columns" => Array (
		"sermonid" => Array (
			"exclude" => "1",
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_sermons_series_rel.sermonid",
			"config" => Array (
				"type" => "select",
				"foreign_table" => "tx_wecsermons_sermons",
				"size" => "1",
				"minitems" => "0",
				"maxitems" => "1"
			)
		),
		"seriesid" => Array (
			"exclude" => "1",
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_sermons_series_rel.seriesid",
			"config" => Array (
				"type" => "select",
				"foreign_table" => "tx_wecsermons_series",
				"size" => "1",
				"minitems" => "0",
				"maxitems" => "1"
			)
		)
	),
	"types" => Array (
		"0" => Array ( "showitem" => "" )
	),
	"palettes" => Array (
		"1" => Array ( "showitem" => "" )
	)
);



$TCA["tx_wecsermons_topics"] = Array (
	"ctrl" => $TCA["tx_wecsermons_topics"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "hidden,description,title"
	),
	"feInterface" => $TCA["tx_wecsermons_topics"]["feInterface"],
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
		"hidden" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"description" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_topics.description",
			"config" => Array (
				"type" => "text",
				"cols" => "30",
				"rows" => "5",
				"wizards" => Array(
					"_PADDING" => 2,
					"RTE" => Array(
						"notNewRecords" => 1,
						"RTEonly" => 1,
						"type" => "script",
						"title" => "Full screen Rich Text Editing|Formatteret redigering i hele vinduet",
						"icon" => "wizard_rte2.gif",
						"script" => "wizard_rte.php",
					),
				),
			)
		),
		"title" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_topics.title",
			"config" => Array (
				"type" => "input",
				"size" => "30",
				"eval" => "required",
			)
		),
		"islinked" => Array (        
			"exclude" => 1,        
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_topics.islinked",        
			"config" => Array (
				"type" => "check",
				"default" => "1"
			)
		)
	),
	"types" => Array (
		"0" => Array("showitem" => "sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, hidden;;1;;1-1-1, title;;;;2-2-2, description;;;richtext:rte_transform[mode=ts_css]")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "islinked")
	)
);

$TCA["tx_wecsermons_sermons_topics_rel"] = Array (
	"ctrl" => $TCA["tx_wecsermons_sermons_topics_rel"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "sermonid,topicid"
	),
	"feInterface" => $TCA["tx_wecsermons_sermons_topics_rel"]["feInterface"],
	"columns" => Array (
		"sermonid" => Array (
			"exclude" => "1",
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_sermons_topics_rel.sermonid",
			"config" => Array (
				"type" => "select",
				"foreign_table" => "tx_wecsermons_sermons",
				"size" => "1",
				"minitems" => "0",
				"maxitems" => "1"
			)
		),
		"topicid" => Array (
			"exclude" => "1",
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_sermons_topics_rel.topicid",
			"config" => Array (
				"type" => "select",
				"foreign_table" => "tx_wecsermons_topics",
				"size" => "1",
				"minitems" => "0",
				"maxitems" => "1"
			)
		)
	),
	"types" => Array (
		"0" => Array ( "showitem" => "sermonid,topicid" )
	),
	"palettes" => Array (
		"1" => Array ( "showitem" => "sermonid,topicid" )
	)
);

$TCA["tx_wecsermons_series_topics_rel"] = Array (
        "ctrl" => $TCA["tx_wecsermons_series_topics_rel"]["ctrl"],
        "interface" => Array (
                "showRecordFieldList" => "seriesid,topicid"
        ),
        "feInterface" => $TCA["tx_wecsermons_series_topics_rel"]["feInterface"],
        "columns" => Array (
                "seriesid" => Array (
                        "exclude" => "1",
                        "label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_series_topics_rel.seriesid",
                        "config" => Array (
                                "type" => "select",
                                "foreign_table" => "tx_wecsermons_series",
                                "size" => "1",
                                "minitems" => "0",
                                "maxitems" => "1"
                        )
                ),
                "topicid" => Array (
                        "exclude" => "1",
                        "label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_series_topics_rel.topicid",
                        "config" => Array (
                                "type" => "select",
                                "foreign_table" => "tx_wecsermons_topics",
                                "size" => "1",
                                "minitems" => "0",
                                "maxitems" => "1"
                        )
                )
        ),
        "types" => Array (
                "0" => Array ( "showitem" => "seriesid,topicid" )
        ),
        "palettes" => Array (
                "1" => Array ( "showitem" => "seriesid,topicid" )
        )
);
	

$TCA["tx_wecsermons_seasons"] = Array (
	"ctrl" => $TCA["tx_wecsermons_seasons"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "hidden,title"
	),
	"feInterface" => $TCA["tx_wecsermons_seasons"]["feInterface"],
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
		"hidden" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"title" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_seasons.title",
			"config" => Array (
				"type" => "input",
				"size" => "30",
				"eval" => "required",
			)
		),
		"description" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_seasons.description",
			"config" => Array (
				"type" => "text",
				"cols" => "30",
				"rows" => "5",
				"wizards" => Array(
					"_PADDING" => 2,
					"RTE" => Array(
						"notNewRecords" => 1,
						"RTEonly" => 1,
						"type" => "script",
						"title" => "Full screen Rich Text Editing|Formatteret redigering i hele vinduet",
						"icon" => "wizard_rte2.gif",
						"script" => "wizard_rte.php",
					),
				),
			)
		),
		"islinked" => Array (        
			"exclude" => 1,        
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_seasons.islinked",        
			"config" => Array (
				"type" => "check",
				"default" => "1"
			)
		)
	),
	"types" => Array (
		"0" => Array("showitem" => "sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, hidden;;1, title;;;;2-2-2, description;;;richtext:rte_transform[mode=ts_css]")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "islinked")
	)
);

$TCA["tx_wecsermons_series_seasons_rel"] = Array (
	"ctrl" => $TCA["tx_wecsermons_series_seasons_rel"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "seriesid,seasonid"
	),
	"feInterface" => $TCA["tx_wecsermons_series_seasons_rel"]["feInterface"],
	"columns" => Array (
		"seriesid" => Array (
			"exclude" => "1",
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_series_seasons_rel.seriesid",
			"config" => Array (
				"type" => "select",
				"foreign_table" => "tx_wecsermons_series",
				"size" => "1",
				"minitems" => "0",
				"maxitems" => "1"
			)
		),
		"seasonid" => Array (
			"exclude" => "1",
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_series_seasons_rel.seasonid",
			"config" => Array (
				"type" => "select",
				"foreign_table" => "tx_wecsermons_seasons",
				"size" => "1",
				"minitems" => "0",
				"maxitems" => "1"
			)
		)
	)
);


$TCA["tx_wecsermons_speakers"] = Array (
	"ctrl" => $TCA["tx_wecsermons_speakers"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "hidden,title"
	),
	"feInterface" => $TCA["tx_wecsermons_speakers"]["feInterface"],
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
		"hidden" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"fullname" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_speakers.fullname",
			"config" => Array (
				"type" => "input",
				"size" => "40",
				"max" => "80",
				"eval" => "required",
			)
		),
		"firstname" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_speakers.firstname",
			"config" => Array (
				"type" => "input",
				"size" => "15",
				"max" => "50",
				"eval" => "",
			)
		),
		"lastname" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_speakers.lastname",
			"config" => Array (
				"type" => "input",
				"size" => "15",
				"max" => "50",
				"eval" => "",
			)
		),
		"email" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_speakers.email",
			"config" => Array (
				"type" => "input",
				"size" => "30",
			)
		),
		"photo" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_speakers.photo",
			"config" => Array (
				"type" => "group",
				"internal_type" => "file",
				"allowed" => "gif,png,jpeg,jpg",
				"max_size" => 5120,
                "show_thumbs" => 1,
				"uploadfolder" => "uploads/tx_wecsermons",
				"size" => 1,
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"alttitle" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_speakers.alttitle",
			"config" => Array (
				"type" => "text",
				"wrap" => "virtual",
			)
		),
		"url" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_speakers.url",
			"config" => Array (
				"type" => "input",
				"size" => "30",
			)
		),
		"islinked" => Array (        
			"exclude" => 1,        
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_speakers.islinked",        
			"config" => Array (
				"type" => "check",
				"default" => "1",
			)
		),
		"blogurl" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_speakers.blogurl",
			"config" => Array (
				"type" => "input",
				"size" => "50"
			)
		)
	),
	"types" => Array (
		"0" => Array("showitem" => "sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, hidden;;1;;2-2-2, fullname;;2;;3-3-3, email;;;;4-4-4, url, blogurl, photo;;3")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "islinked"),
		"2" => Array("showitem" => "firstname, lastname"),
		"3" => Array("showitem" => "alttitle"),
	)
);

$TCA["tx_wecsermons_sermons_speakers_rel"] = Array (
	"ctrl" => $TCA["tx_wecsermons_sermons_speakers_rel"]["ctrl"],
	"feInterface" => $TCA["tx_wecsermons_sermons_speakers_rel"]["feInterface"],
	"columns" => Array (
		"sermonid" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_sermons_speakers_rel.sermonid",
			"config" => Array (
				"type" => "select",
				"foreign_table" => "tx_wecsermons_sermons",
				"size" => 1,
				"minitems" => 0,
				"maxitems" => 1
			)
		),
		"speakerid" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_sermons/locallang_db.php:tx_wecsermons_sermons_speakers_rel.speakerid",
			"config" => Array (
				"type" => "select",
				"foreign_table" => "tx_wecsermons_speakers",
				"size" => 1,
				"minitems" => 0,
				"maxitems" => 1
			)
		),
	),
	"types" => Array (
		"0" => Array ( "showitem" => "" )
	),
	"palettes" => Array (
		"1" => Array ( "showitem" => "")
	)
);

?>
