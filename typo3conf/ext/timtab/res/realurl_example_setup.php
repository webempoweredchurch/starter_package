<?php
$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tstemplate.php']['linkData-PostProc'][] = 'EXT:realurl/class.tx_realurl.php:&tx_realurl->encodeSpURL';
$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['checkAlternativeIdMethods-PostProc'][] = 'EXT:realurl/class.tx_realurl.php:&tx_realurl->decodeSpURL';

$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearAllCache_additionalTables']['tx_realurl_urldecodecache'] = 'tx_realurl_urldecodecache';
$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearAllCache_additionalTables']['tx_realurl_urlencodecache'] = 'tx_realurl_urlencodecache';

$TYPO3_CONF_VARS['FE']['addRootLineFields'] .= ',tx_realurl_pathsegment,alias,nav_title,title';

$TYPO3_CONF_VARS['EXTCONF']['realurl'] = array(
	'_DEFAULT' => array(
		'init' => array(			
			'enableCHashCache'     => 1,
			'appendMissingSlash'   => 'ifNotFile',
			'enableUrlDecodeCache' => 1,
			'enableUrlEncodeCache' => 1,			
		),
		'redirects' => array(),
		'preVars'   => array(),
		'pagePath'  => array(
			'type'           => 'user',
			'userFunc'       => 'EXT:realurl/class.tx_realurl_advanced.php:&tx_realurl_advanced->main',
			'spaceCharacter' => '_',
			'languageGetVar' => 'L',
			'expireDays'     => 7,
			'rootpage_id'    => 1,
		),
		'fixedPostVars' => array(),
		'postVarSets'   => array(
			'_DEFAULT' => array(
				// news archive parameters
				'archive' => array(
					array(
						'GETvar' => 'tx_ttnews[year]',
					),
					array(
						'GETvar'   => 'tx_ttnews[month]',
						'valueMap' => array(
							'january'   => '01',
							'february'  => '02',
							'march'     => '03',
							'april'     => '04',
							'may'       => '05',
							'june'      => '06',
							'july'      => '07',
							'august'    => '08',
							'september' => '09',
							'october'   => '10',
							'november'  => '11',
							'december'  => '12',
						),
					),
					array(
						'GETvar' => 'tx_ttnews[day]',
					),
					array(
						'GETvar'      => 'tx_ttnews[tt_news]',
						'lookUpTable' => array(
							'table'               => 'tt_news',
							'id_field'            => 'uid',
							'alias_field'         => 'title',
							'addWhereClause'      => ' AND NOT deleted',
							'useUniqueCache'      => 1,
							'useUniqueCache_conf' => array(
								'strtolower'     => 1,
								'spaceCharacter' => '_',
							)						
						),
					),
				),
				// news pagebrowser
				'browse' => array(
					array(
						'GETvar' => 'tx_ttnews[pointer]',
					),
				),			
				// news categories
				'category' => array(
					array(
						'GETvar'      => 'tx_ttnews[cat]',
						'lookUpTable' => array(
							'table'               => 'tt_news_cat',
							'id_field'            => 'uid',
							'alias_field'         => 'title',
							'addWhereClause'      => ' AND NOT deleted',
							'useUniqueCache'      => 1,
							'useUniqueCache_conf' => array(
								'strtolower'     => 1,
								'spaceCharacter' => '_',
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
				'xmlrpc' => array(
					'type' => 'single',
					'keyValues' => array(
						'type' => 200,
					),
				),
				'trackback' => array(
					'type' => 'single',
					'keyValues' => array(
						'type' => 200,
					),
				),
			),
		),
		'fileName' => array(
			'index' => array(
				'rss.xml' => array(
					'keyValues' => array(
						'type' => 100,
					),
				),
				'index.htm' => array(
					'keyValues' => array(),
				),
			),
			'defaultToHTMLsuffixOnPrev' => 1,
		),
	)
);

?>