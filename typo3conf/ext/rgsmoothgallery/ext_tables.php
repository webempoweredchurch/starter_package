<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key,pages';


t3lib_extMgm::addPlugin(array('LLL:EXT:rgsmoothgallery/locallang_db.xml:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');


t3lib_extMgm::addStaticFile($_EXTKEY,"pi1/static/","SmoothGallery");

// Flexforms
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1']='pi_flexform';
if (t3lib_extMgm::isLoaded('dam')) {
  t3lib_extMgm::addPiFlexFormValue('rgsmoothgallery_pi1', 'FILE:EXT:rgsmoothgallery/flexformDAM_ds.xml');
} else { 
  t3lib_extMgm::addPiFlexFormValue('rgsmoothgallery_pi1', 'FILE:EXT:rgsmoothgallery/flexform_ds.xml');
}

if (TYPO3_MODE=="BE")	$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_rgsmoothgallery_pi1_wizicon"] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_rgsmoothgallery_pi1_wizicon.php';

t3lib_extMgm::allowTableOnStandardPages('tx_rgsmoothgallery_image');

$TCA["tx_rgsmoothgallery_image"] = array (
    "ctrl" => array (
        'title'     => 'LLL:EXT:rgsmoothgallery/locallang_db.xml:tx_rgsmoothgallery_image',        
        'label'     => 'title',    
        'tstamp'    => 'tstamp',
        'crdate'    => 'crdate',
        'cruser_id' => 'cruser_id',
        'languageField'            => 'sys_language_uid',    
        'transOrigPointerField'    => 'l18n_parent',    
        'transOrigDiffSourceField' => 'l18n_diffsource',    
        'sortby' => 'sorting',    
        'delete' => 'deleted',    
        'enablecolumns' => array (        
            'disabled' => 'hidden',
        ),
        'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
        'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_rgsmoothgallery_image.gif',
    ),
    "feInterface" => array (
        "fe_admin_fieldList" => "sys_language_uid, l18n_parent, l18n_diffsource, hidden, title, description, image",
    )
);


$tempColumns = Array (
    "tx_rgsmoothgallery_rgsg" => Array (        
        "exclude" => 1,        
        "label" => "LLL:EXT:rgsmoothgallery/locallang_db.xml:tt_content.tx_rgsmoothgallery_rgsg",        
        "config" => Array (
            "type" => "check",
        )
    ),
);
t3lib_div::loadTCA("tt_content");
t3lib_extMgm::addTCAcolumns("tt_content",$tempColumns,1);

 
$GLOBALS['TCA']['tt_content']['palettes']['7']['showitem'] .= ',tx_rgsmoothgallery_rgsg';

/*
t3lib_div::loadTCA("tt_content");
t3lib_extMgm::addTCAcolumns("tt_content",$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes("tt_content","tx_rgsmoothgallery_rgsg;;;;1-1-1");
*/


?>
