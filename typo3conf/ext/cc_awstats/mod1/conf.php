<?php

	// DO NOT REMOVE OR CHANGE THESE 3 LINES:
define('TYPO3_MOD_PATH', '../typo3conf/ext/cc_awstats/mod1/');
$BACK_PATH='../../../../typo3/';
$MCONF['name']='tools_txccawstatsM1';

$MCONF['access']='admin';
$MLANG['default']['tabs_images']['tab'] = 'moduleicon.gif';
$MCONF['script']='index.php';


	// Default (english) labels:
$MLANG['default']['tabs']['tab'] = 'AWStats';	
$MLANG['default']['labels']['tabdescr'] = "Integrated 'Third-party module', one of the best free logfile analyzers.";
$MLANG['default']['labels']['tablabel'] = 'AWStats logfile analyzer';

	// German language:
$MLANG['de']['tabs']['tab'] = 'AWStats';	
$MLANG['de']['labels']['tabdescr'] = "Dieses Modul integriert den Logfile Analyzer 'AWStats' in Typo3.";
$MLANG['de']['labels']['tablabel'] = 'AWStats Logfile Analyse';
?>
