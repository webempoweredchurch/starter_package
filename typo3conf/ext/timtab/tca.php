<?php
//
//	$Id: tca.php,v 1.4 2005/06/15 21:29:57 ingorenner Exp $
//

if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA['tx_timtab_blogroll'] = Array (
	'ctrl' => $TCA['tx_timtab_blogroll']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => 'hidden,url,name,description,rel_identity,rel_friendship,rel_physical,rel_professional,rel_geographical,rel_family,rel_romantic,img_uri,rss_uri,notes,rating,target'
	),
	'columns' => Array (
		'hidden' => Array (		
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),
		'url' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll.url',		
			'config' => Array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'required,trim,nospace',
			)
		),
		'name' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll.name',		
			'config' => Array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'required,trim',
			)
		),
		'description' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll.description',		
			'config' => Array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'trim',
			)
		),
		'rel_identity' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll.rel_identity',		
			'config' => Array (
				'type' => 'check',
				'cols' => 1,
				'items' => Array(
					Array('LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll.rel_identity.I.0', ''),
				),
			)
		),
		'rel_friendship' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll.rel_friendship',		
			'config' => Array (
				'type' => 'radio',
				'items' => Array (
					Array('LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll.rel_friendship.I.0', '0'),
					Array('LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll.rel_friendship.I.1', '1'),
					Array('LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll.rel_friendship.I.2', '2'),
					Array('LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll.rel_friendship.I.3', '3'),
				),
				'default' => 0,
			)
		),
		'rel_physical' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll.rel_physical',		
			'config' => Array (
				'type' => 'check',
				'cols' => 1,
				'items' => Array(
					Array('LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll.rel_physical.I.0', ''),
				),
			)
		),
		'rel_professional' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll.rel_professional',		
			'config' => Array (
				'type' => 'check',
				'cols' => 4,
				'items' => Array (
					Array('LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll.rel_professional.I.0', ''),
					Array('LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll.rel_professional.I.1', ''),
				),
			)
		),
		'rel_geographical' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll.rel_geographical',		
			'config' => Array (
				'type' => 'radio',
				'items' => Array (
					Array('LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll.rel_geographical.I.0', '0'),
					Array('LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll.rel_geographical.I.1', '1'),
					Array('LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll.rel_geographical.I.2', '2'),
				),
				'default' => 0,
			)
		),
		'rel_family' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll.rel_family',		
			'config' => Array (
				'type' => 'radio',
				'items' => Array (
					Array('LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll.rel_family.I.0', '0'),
					Array('LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll.rel_family.I.1', '1'),
					Array('LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll.rel_family.I.2', '2'),
					Array('LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll.rel_family.I.3', '3'),
					Array('LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll.rel_family.I.4', '4'),
					Array('LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll.rel_family.I.5', '5'),
				),
				'default' => 0,
			)
		),
		'rel_romantic' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll.rel_romantic',		
			'config' => Array (
				'type' => 'check',
				'cols' => 4,
				'items' => Array (
					Array('LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll.rel_romantic.I.0', ''),
					Array('LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll.rel_romantic.I.1', ''),
					Array('LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll.rel_romantic.I.2', ''),
					Array('LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll.rel_romantic.I.3', ''),
				),
			)
		),
		'img_uri' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll.img_uri',		
			'config' => Array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'trim',
			)
		),
		'rss_uri' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll.rss_uri',		
			'config' => Array (
				'type' => 'input',	
				'size' => '30',	
				'eval' => 'trim',
			)
		),
		'notes' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll.notes',		
			'config' => Array (
				'type' => 'text',
				'cols' => '30',	
				'rows' => '6',
			)
		),
		'rating' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll.rating',		
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll.rating.I.0', '0'),
					Array('LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll.rating.I.1', '1'),
					Array('LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll.rating.I.2', '2'),
					Array('LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll.rating.I.3', '3'),
					Array('LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll.rating.I.4', '4'),
					Array('LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll.rating.I.5', '5'),
					Array('LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll.rating.I.6', '6'),
					Array('LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll.rating.I.7', '7'),
					Array('LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll.rating.I.8', '8'),
					Array('LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll.rating.I.9', '9'),
				),
				'size' => 1,	
				'maxitems' => 1,
			)
		),
		'target' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll.target',		
			'config' => Array (
				'type' => 'radio',
				'items' => Array (
					Array('LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll.target.I.0', '0'),
					Array('LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll.target.I.1', '1'),
					Array('LLL:EXT:timtab/locallang_db.php:tx_timtab_blogroll.target.I.2', '2'),
				),
				'default' => 0,
			)
		),
	),
	'types' => Array (
		'0' => Array('showitem' => 'hidden;;1;;1-1-1, url, name, description,--div--;Relationship (XFN), rel_identity, rel_friendship, rel_physical, rel_professional, rel_geographical, rel_family, rel_romantic,--div--;Advanced, img_uri, rss_uri, notes, rating, target')
	),
	'palettes' => Array (
		'1' => Array('showitem' => '')
	)
);
?>