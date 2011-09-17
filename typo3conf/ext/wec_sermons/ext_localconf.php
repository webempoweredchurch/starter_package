<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_wecsermons_resources=1
');
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_wecsermons_resource_types=1
');
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_wecsermons_sermons=1
');
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_wecsermons_topics=1
');

  ## Extending TypoScript from static template uid=43 to set up userdefined tag:
t3lib_extMgm::addTypoScript($_EXTKEY,'editorcfg','
	tt_content.CSS_editor.ch.tx_wecsermons_pi1 = < plugin.tx_wecsermons_pi1.CSS_editor
',43);


t3lib_extMgm::addPItoST43($_EXTKEY,'pi1/class.tx_wecsermons_pi1.php','_pi1','list_type',1);

	//	Load necessary classes and register respective hooks
require_once( t3lib_extMgm::extPath($_EXTKEY) . '/class.tx_wecsermons_resourceTypeTca.php' );
require_once( t3lib_extMgm::extPath($_EXTKEY) . '/class.tx_wecsermons_xmlView.php' );

/**
* Register hooks:
*/

$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tceforms.php']['getMainFieldsClass'][] = 'tx_wecsermons_resourceTypeTca';
$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:wecsermons/class.tx_wecsermons_resourceTypeTca.php:tx_wecsermons_resourceTypeTca';

$TYPO3_CONF_VARS['EXTCONF']['tx_wecapi_list']['preProcessPageArray'][] = 'tx_wecsermons_xmlView';

t3lib_extMgm::addTypoScript($_EXTKEY,'setup','
	tt_content.shortcut.20.0.conf.tx_wecsermons_sermons = < plugin.'.t3lib_extMgm::getCN($_EXTKEY).'_pi1
	tt_content.shortcut.20.0.conf.tx_wecsermons_sermons.CMD = singleView
',43);


  ## the first run... we must check to see if updates are required or not (so a new user isn't hitting the update message by default)
  $extConf = unserialize($TYPO3_CONF_VARS['EXT']['extConf'][$_EXTKEY]);
  if ( !$extConf['haverunbefore'] ){

    # our purpose... determine if this is a blank database, if so we can init our version

    require_once( t3lib_extMgm::extPath($_EXTKEY) . '/class.ext_update.php' );
    $smsUpdater = new ext_update;

    if ( !$smsUpdater->missingTables() ) { // if our tables aren't there yet, we can't update anyway, so no setting the version property
      if ( $smsUpdater->isBlankDB() ) {
        $smsUpdater->setCurrentSchemaVersion();
      }

      # never run this check again... mark that we've taken care of this first-run behavior
      # borrowed logic from wec_map/tx_wecmap_domainmgr.php
      $extConf['haverunbefore'] = 1;
      $instObj = t3lib_div::makeInstance('t3lib_install');
      $instObj->allowUpdateLocalConf = 1;
      $instObj->updateIdentity = $_EXTKEY;
      $lines = $instObj->writeToLocalconf_control();
      $instObj->setValueInLocalconfFile($lines, '$TYPO3_CONF_VARS[\'EXT\'][\'extConf\'][\''.$_EXTKEY.'\']', serialize($extConf));
      $instObj->writeToLocalconf_control($lines);
    }
  }

    

  ## hide the irre-intermediate tables from the backend user (the latter TSConfig is only applicable to T3 v4.2+)
  t3lib_extMgm::addPageTSConfig('mod.web_list.hideTables := addToList(tx_wecsermons_sermons_resources_rel,tx_wecsermons_series_resources_rel,tx_wecsermons_sermons_series_rel,tx_wecsermons_sermons_topics_rel,tx_wecsermons_series_topics_rel,tx_wecsermons_series_seasons_rel,tx_wecsermons_sermons_speakers_rel)');
  t3lib_extMgm::addPageTSConfig('mod.web_list.deniedNewTables := addToList(tx_wecsermons_sermons_resources_rel,tx_wecsermons_series_resources_rel,tx_wecsermons_sermons_series_rel,tx_wecsermons_sermons_topics_rel,tx_wecsermons_series_topics_rel,tx_wecsermons_series_seasons_rel,tx_wecsermons_sermons_speakers_rel)');

?>
