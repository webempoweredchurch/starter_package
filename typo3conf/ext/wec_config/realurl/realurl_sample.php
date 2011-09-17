<?php

$TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT'] = array(
	'init' => array(
		'enableCHashCache' => 1,
		'appendMissingSlash' => 'ifNotFile',
		'enableUrlDecodeCache' => 1,
		'enableUrlEncodeCache' => 1,
	),
	'pagePath' => array(
		'type' => 'user',
		'userFunc' => 'EXT:realurl/class.tx_realurl_advanced.php:&tx_realurl_advanced->main',
		'spaceCharacter' => '_',
		'languageGetVar' => 'L',
		'expireDays' => 14,
		'autoUpdatePathCache' => true,
		'rootpage_id' => 61,
	),

	 /* Prevars show up in the first part of the URL */
	'preVars' => array(
	),
	
	 /* fixedPostVars are limited to a single page in the backend */
	'fixedPostVars' => array(
		/* User Account */
		'52' => array(
			array( 
				'GETvar' => 'tx_srfeuserregister_pi1[cmd]',
			),
		),
		
		/*
		 *	Calendar Pages
		 * ************************************************************* /
		
		/* Month */
		'53'=> array(
			array( 'GETvar' => 'tx_calendar_pi1[f1]' ),
			array( 'GETvar' => 'tx_calendar_pi1[f2]' ),
		),
		
		/* Week */
		'56'=> array(
			array( 'GETvar' => 'tx_calendar_pi1[f1]' ),
			array( 'GETvar' => 'tx_calendar_pi1[f2]' ),
		),
		
		/* Day */
		'55'=> array(
			array( 'GETvar' => 'tx_calendar_pi1[f1]' ),
			array( 'GETvar' => 'tx_calendar_pi1[f2]' ),
			array( 'GETvar' => 'tx_calendar_pi1[f3]' ),
		),
		
		/* Event Page */
		'54'=> array(
			array(
				'GETvar' => 'tx_calendar_pi1[f1]',
				'lookUpTable' => array(
					'table' => 'tx_calendar_item',
					'id_field' => 'uid',
					'alias_field' => 'title',
					'addWhereClause' => ' AND NOT deleted',
					'useUniqueCache' => 1,
					'userUniqueCache_conf' => array(
						'strtolower' => 1,
						'spaceCharacter' => '-',
					),
				),
			),
		),
		
		/* Blog Home */
		/*
		'81' => array(
			// news categories
			'category' => array(
				array(
					'GETvar' => 'tx_ttnews[cat]',
					'lookUpTable' => array(
						'table' => 'tt_news_cat',
						'id_field' => 'uid',
						'alias_field' => 'title',
						'addWhereClause' => ' AND NOT deleted',
						'useUniqueCache' => 1,
						'useUniqueCache_conf' => array(
							'strtolower' => 1,
							'spaceCharacter' => '_',
						),
					),
				),
			),			
		),*/
			
		'82' => array(
			array(
				'GETvar'	  => 'tx_ttnews[tt_news]',
				'lookUpTable' => array(
					'table'				  => 'tt_news',
					'id_field'			  => 'uid',
					'alias_field'		  => 'title',
					'addWhereClause'	  => ' AND NOT deleted',
					'useUniqueCache'	  => 1,
					'useUniqueCache_conf' => array(
						'strtolower'	 => 1,
						'spaceCharacter' => '_',
					),						
				),
			),
		),
		
		'40' => array(
			array(
				'GETvar'	  => 'tx_ttnews[tt_news]',
				'lookUpTable' => array(
					'table'				  => 'tt_news',
					'id_field'			  => 'uid',
					'alias_field'		  => 'title',
					'addWhereClause'	  => ' AND NOT deleted',
					'useUniqueCache'	  => 1,
					'useUniqueCache_conf' => array(
						'strtolower'	 => 1,
						'spaceCharacter' => '_',
					),						
				),
			),
		),
		
		'74' => array(
			array(
				'GETvar' => 'sub',
				'valueMap' => array(
					'subscribe' => '1',
					'unsubscribe' => '2',
				),
				'noMatch' => 'bypass',
			),
		),
		
		'79' => array(
			array(
				'GETvar' => 'devo_sec',
				'lookUpTable' => array	(
					'table' => 'tx_wecdevo_section',
					'id_field' => 'uid',
					'alias_field' => 'name',
					'useUniqueCache' => 1,
					'useUniqueCache_conf' => array (
						'strtolower' => 1,
						'spaceCharacter' => '_',
					),	
				),
			),
			array(
				'GETvar' => 'txtpg',
				'valueMap' => array(
					'single_page' => '1',
					'multiple_pages' => '2',
					'scrolling' => '3',
				),
				'noMatch' => 'bypass',	
			),
			array(
				'GETvar' => 'txtsz',
				'valueMap' => array(
					'small_text' => '1',
					'medium_text' => '2',
					'large_text' => '3',
				),
				'noMatch' => 'bypass',
			),
		),
		
		'87' => array(
			array(
				'GETvar' => 'showinterest',
				'lookUpTable' => array(
					'table' => 'tx_wecservant_minopp',
					'id_field' => 'uid',
					'alias_field' => 'name',
					'addWhereClause'  => ' AND NOT deleted',
					'useUniqueCache' => 1,
					'useUniqueCache_conf' => array(
						'strtolower' => 1,
						'spaceCharacter' => '_',
					),
				),
			),
		),
		
		'62' => array(
			array(
				'GETvar' => 'tx_wecstaffdirectory_pi1[curstaff]'
			),
		),
		
		/* wec_sermons plug-in */
		'78' => array (
			array (
				'GETvar' => 'tx_wecsermons_pi1[recordType]',
				'valueMap' => array(	//	Value map is a static translation
					'sermons' => 'tx_wecsermons_sermons',
					'series' => 'tx_wecsermons_series',
					'topics' => 'tx_wecsermons_topics',
					'speakers' =>'tx_wecsermons_speakers',
					'seasons' => 'tx_wecsermons_seasons',
					'resources' => 'tx_wecsermons_resources',
				)				
			),
			array(
				'cond' => array (
					'prevValueInList' => 'tx_wecsermons_sermons'
				),
				'GETvar' => 'tx_wecsermons_pi1[showUid]',
				'lookUpTable' => array( 
					'table' => 'tx_wecsermons_sermons',
					'id_field' => 'uid',
					'alias_field' => 'title',
					'useUniqueCache' => 1,
					'useUniqueCache_conf' => array(
						'spaceCharacter' => '_',
					),
				)
			),
			array(
				'cond' => array (
					'prevValueInList' => 'tx_wecsermons_series'
				),
				'GETvar' => 'tx_wecsermons_pi1[showUid]',
				'lookUpTable' => array( 
					'table' => 'tx_wecsermons_series',
					'id_field' => 'uid',
					'alias_field' => 'title',
					'useUniqueCache' => 1,
					'useUniqueCache_conf' => array(
						'spaceCharacter' => '_',
					),
				)
			),
			array(
				'cond' => array (
					'prevValueInList' => 'tx_wecsermons_speakers'
				),
				'GETvar' => 'tx_wecsermons_pi1[showUid]',
				'lookUpTable' => array( 
					'table' => 'tx_wecsermons_speakers',
					'id_field' => 'uid',
					'alias_field' => 'fullname',
					'useUniqueCache' => 1,
					'useUniqueCache_conf' => array(
						'spaceCharacter' => '_',
					),
				)
			),
			array(
				'cond' => array (
					'prevValueInList' => 'tx_wecsermons_topics'
				),
				'GETvar' => 'tx_wecsermons_pi1[showUid]',
				'lookUpTable' => array( 
					'table' => 'tx_wecsermons_topics',
					'id_field' => 'uid',
					'alias_field' => 'title',
					'useUniqueCache' => 1,
					'useUniqueCache_conf' => array(
						'spaceCharacter' => '_',
					),
				)
			),
			array(
				'cond' => array (
					'prevValueInList' => 'tx_wecsermons_seasons'
				),
				'GETvar' => 'tx_wecsermons_pi1[showUid]',
				'lookUpTable' => array( 
					'table' => 'tx_wecsermons_seasons',
					'id_field' => 'uid',
					'alias_field' => 'title',
					'useUniqueCache' => 1,
					'useUniqueCache_conf' => array(
						'spaceCharacter' => '_',
					),
				)
			),
			array(
				'cond' => array (
					'prevValueInList' => 'tx_wecsermons_resources'
				),
				'GETvar' => 'tx_wecsermons_pi1[showUid]',
				'lookUpTable' => array( 
					'table' => 'tx_wecsermons_resources',
					'id_field' => 'uid',
					'alias_field' => 'title',
					'useUniqueCache' => 1,
					'useUniqueCache_conf' => array(
						'spaceCharacter' => '_',
					),

				)
			),
			array( 
				'GETvar' => 'tx_wecsermons_pi1[sermonUid]'
			),
			array( 
				'GETvar' => 'tx_wecsermons_pi1[showUid]',
			),
			array(	//	Partdef
					'GETvar' => 'tx_wecsermons_pi1[pointer]',
			),
		),
	   
		
		

	),
	
	'postVarSets' => array(
		'_DEFAULT' => array (
				'user' => array (
					array(
						'GETvar' => 'tx_srfeuserregister_pi1[regHash]',
					),
				),
				'forgot_password' => array(
					'type' => 'single',
					'keyValues' => array(
						'tx_newloginbox_pi1[forgot]' => 1,
					),
				),
				
				'browse' => array(
					array(
						'GETvar' => 'tx_ttnews[pointer]',
					),
				),
				
				'xmlrpc' => array(
					'type' => 'single',
					'keyValues' => array(
						'type' => 200,
					),
				),

				'trackback' => array(
					'type' => 'single',
					'keyValues' => array(
						'tx_timtab_pi2[trackback]' => 1,
					),
				),
				
				'archive' => array(
					array(
						'GETvar' => 'tx_ttnews[year]',
					),
					array(
						'GETvar'   => 'tx_ttnews[month]',
					),
				),
				
				
				/* News Categories */
				'category' => array(
					array(
						'GETvar'	  => 'tx_ttnews[cat]',
						'lookUpTable' => array(
							'table' => 'tt_news_cat',
							'id_field' => 'uid',
							'alias_field' => 'title',
							'addWhereClause' => ' AND NOT deleted',
							'useUniqueCache' => 1,
							'useUniqueCache_conf' => array(
								'strtolower' => 1,
								'spaceCharacter' => '_',
							),
						),
					),
				),					


			'forum' => array(
				array(
						'GETvar' => 'view',																								
				),
				array(
						'GETvar' => 'cat_uid',
						'lookUpTable' => array(
								'table' => 'tx_chcforum_category',
								'id_field' => 'uid',
								'alias_field' => 'cat_title',
								'addWhereClause' => ' AND NOT deleted',
								'useUniqueCache' => 1,
								'useUniqueCache_conf' => array(
									  'strtolower' => 1,
									 'spaceCharacter' => '-',
												),
								),
				),
				array(
						'GETvar' => 'conf_uid',
						'lookUpTable' => array(
										'table' => 'tx_chcforum_conference',
										'id_field' => 'uid',
										'alias_field' => 'conference_name',
										'addWhereClause' => ' AND NOT deleted',
										'useUniqueCache' => 1,
										'useUniqueCache_conf' => array(
												'strtolower' => 1,
												'spaceCharacter' => '-',
										),
								),
				),
				array(
						'GETvar' => 'thread_uid',
						'lookUpTable' => array(
												'table' => 'tx_chcforum_thread',
												'id_field' => 'uid',
												'alias_field' => 'thread_subject',
												'addWhereClause' => ' AND NOT deleted',
												'useUniqueCache' => 1,
												'useUniqueCache_conf' => array(
														'strtolower' => 1,
														'spaceCharacter' => '-',
												),
						),
				),
				array(
						'GETvar' => 'post_uid',
						'lookUpTable' => array(
												'table' => 'tx_chcforum_post',
												'id_field' => 'uid',
												'alias_field' => 'post_subject',
												'addWhereClause' => ' AND NOT deleted',
												'useUniqueCache' => 1,
												'useUniqueCache_conf' => array(
												'strtolower' => 1,
												'spaceCharacter' => '-',
						),
				),
			),
		),
		 'cal_group' => array(
			  array(
				  'GETvar' => 'tx_calendar_pi1[targetgroup]',
				  'lookUpTable' => array(
					  'table' => 'tx_calendar_targetgroup',
					  'id_field' => 'uid',
					  'alias_field' => 'title',
					  'addWhereClause' => ' AND NOT deleted',
					  'useUniqueCache' => 1,
					  'useUniqueCache_conf' => array(
						  'strtolower' => 1,
						  'spaceCharacter' => '-',
					  ),
				  ),
			  ),
		  ),
		  'cal_category' => array(
			  array(
				  'GETvar' => 'tx_calendar_pi1[category]',
				  'lookUpTable' => array(
					  'table' => 'tx_calendar_cat',
					  'id_field' => 'uid',
					  'alias_field' => 'title',
					  'addWhereClause' => ' AND NOT deleted',
					  'useUniqueCache' => 1,
					  'useUniqueCache_conf' => array(
						  'strtolower' => 1,
						  'spaceCharacter' => '-',
					  ),
				  ),
			  ),
		  ),

			'not_cached' => array(
				'type' => 'single',
				'keyValues' => array(
				'no_cache' => 1,
			),			
		),
	),
	),
	'fileName' => array (
		'index' => array(
			'index.xml' => array(
				'keyValues' => array (
					'type' => 100,
				)
			),
			'podcast.xml' => array(
				'keyValues' => array(
					'type' => 222,
				)
			),
			'vodcast.xml' => array(
				'keyValues' => array(
					'type' => 223,
				)
			),
			'print.html' => array(						   
				'keyValues' => array(
					'type' => 98,
				)
			),
				'defaultToHTMLsuffixOnPrev' => 0,
	  ),
	),
);

?>