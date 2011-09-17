<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');


$TCA["tx_wecconfig_features"] = Array (
	"ctrl" => $TCA["tx_wecconfig_features"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "disabled,title,description,elements",
	),
	"feInterface" => $TCA["tx_wecconfig_features"]["feInterface"],
	"columns" => Array (
		"disabled" => Array (		
			"exclude" => 1,
			"label" => "LLL:EXT:wec_config/features/locallang_db.xml:tx_wecconfig_features.disabled",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"title" => Array (
			"exclude" => 1,
			"label" => "LLL:EXT:wec_config/features/locallang_db.xml:tx_wecconfig_features.title",		
			"config" => Array (
				"type" => "input",
				"size" => "25",
			),
		),
		"description" => Array (
			"exclude" => 1,		
			"label" => "LLL:EXT:wec_config/features/locallang_db.xml:tx_wecconfig_features.description",		
			"config" => Array (
				'type' => 'text',
	            'cols' => '40',    
	            'rows' => '6',
			),
		),
		"elements" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:wec_config/features/locallang_db.xml:tx_wecconfig_features.elements",		
			"config" => Array (
				"type" => "group",	
				"internal_type" => "db",
				"allowed" => "*",
				"prepend_tname" => true,		
				"size" => 10,	
				"minitems" => 0,
				"maxitems" => 100,	
			),
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "disabled,title,description,elements")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);

?>