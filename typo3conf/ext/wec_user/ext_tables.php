<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');
$tempColumns = Array (
	"tx_wecuser_member" => Array (		
		"exclude" => 1,		
		"label" => "LLL:EXT:wec_user/locallang_db.php:fe_users.tx_wecuser_member",		
		"config" => Array (
			"type" => "check",
		)
	),
	"tx_wecuser_attends" => Array (		
		"exclude" => 1,		
		"label" => "LLL:EXT:wec_user/locallang_db.php:fe_users.tx_wecuser_attends",		
		"config" => Array (
			"type" => "check",
		)
	),
);


t3lib_div::loadTCA("fe_users");
t3lib_extMgm::addTCAcolumns("fe_users",$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes("fe_users","tx_wecuser_member;;;;1-1-1, tx_wecuser_attends");
?>