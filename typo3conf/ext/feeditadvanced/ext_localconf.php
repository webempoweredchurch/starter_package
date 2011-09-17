<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

	// Hooks to view display for advanced frontend editing
$TYPO3_CONF_VARS['SC_OPTIONS']['typo3/classes/class.frontendedit.php']['edit']  = 'EXT:feeditadvanced/view/class.tx_feeditadvanced_editpanel.php:tx_feeditadvanced_editpanel';

	// @note Changed to hook to place Code before </body> directly before output
$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-output'][] = 'EXT:feeditadvanced/view/class.tx_feeditadvanced_adminpanel.php:tx_feeditadvanced_adminpanel->showMenuBar';

	// Use TCEMain hook to work around TYPO3 Core bug: http://bugs.typo3.org/view.php?id=15496
$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:feeditadvanced/hooks/class.tx_feeditadvanced_tcemain_processdatamap.php:tx_feeditadvanced_tcemain_processdatamap';

	// Add AJAX support
$TYPO3_CONF_VARS['FE']['eID_include']['feeditadvanced'] = 'EXT:feeditadvanced/service/ajax.php';

	// Set Language Files
	// @todo Dave: Is there a better way to do this? .php and .xml???
$TYPO3_CONF_VARS['SYS']['locallangXMLOverride']['EXT:lang/locallang_tsfe.php']['EXT:' . $_EXTKEY] = t3lib_extMgm::extPath($_EXTKEY) . "locallang.xml";
$TYPO3_CONF_VARS['SYS']['locallangXMLOverride']['EXT:lang/locallang_tsfe.xml']['EXT:' . $_EXTKEY] = t3lib_extMgm::extPath($_EXTKEY) . "locallang.xml";

	// Adds disable palettes functionality for Frontend forms
$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tceforms.php']['getMainFieldsClass'][] = 'EXT:feeditadvanced/view/class.tx_feeditadvanced_getMainFields_preProcess.php:tx_feeditadvanced_getMainFields_preProcess';

	// Register the controller for feeditadvanced.
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tsfebeuserauth.php']['frontendEditingController']['feeditadvanced'] = 'EXT:feeditadvanced/controller/class.tx_feeditadvanced_frontendedit.php:tx_feeditadvanced_frontendedit';

	// Use pageRenderer hook to concatenate CSS files for the backend editing form.
	// Should be removed when the pageRenderer handles frontend editing properly.
if(t3lib_div::int_from_ver(TYPO3_version) >= 4004000) {
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-preProcess']['feeditadvanced'] = 'EXT:feeditadvanced/hooks/class.tx_feeditadvanced_pagerenderer.php:tx_feeditadvanced_pagerenderer->preProcessPageRenderer';
}

	// Configure settings, etc for showing the icons, menubar, and frontend forms on the page
t3lib_extMgm::addPageTSConfig('
	FeEdit {
		#possible disable complete with set FeEdit.disable=1
		disable = 0
		
		useAjax = 1
		clickContentToEdit = 0
		reloadPageOnContentUpdate = 0
		showIcons = edit, new, copy, cut, hide, delete, draggable
		showPageIcons = edit, new
		skin {
			#cssFile = typo3conf/ext/feeditadvanced/res/css/fe_edit_advanced.css
			#templateFile = EXT:feeditadvanced/res/template/feedit.tmpl
			
			imageType = GIF
		}
		menuBar {
			disable = 0
			config = action, type, clipboard, context
			typeMenu = text, header, image, html
			contextMenu = close, logout
		}
		editWindow {
			height = 600
			width = 800
		}
	}
');

	// Settings needed to be forced for showing hidden records to work
t3lib_extMgm::addUserTSConfig('
	admPanel {
			override.edit.displayIcons = 1
			override.preview.showHiddenRecords = 1
			override.preview.showHiddenPages = 1
	}
');

t3lib_extMgm::addTypoScript('feeditadvanced', 'setup', '
#############################################
## TypoScript added by extension "FE Editing Advanced"
#############################################
config.disablePreviewNotification = 1
');

	// Temporary home for TemplaVoila changes to make testing easier. Should eventually be rolled into TemplaVoila itself.
if (t3lib_extMgm::isLoaded('templavoila')) {
		// @todo Remove this code once TV 1.4 is released and required by feeditadvanced
		// Save the extension key and include TemplaVoila's ext_emconf to get the version number.
	$realExtKey = $_EXTKEY;
	$_EXTKEY = 'templavoila';
	include(t3lib_extMgm::extPath($_EXTKEY) . 'ext_emconf.php');
	$version = $EM_CONF[$_EXTKEY]['version'];
	if (t3lib_div::int_from_ver($version) < 1004000) {
			// XCLASS for necessary code changes in tx_templavoila_pi1->renderElement in older versions of TV.
		$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/templavoila/pi1/class.tx_templavoila_pi1.php'] = t3lib_extMgm::extPath('feeditadvanced').'templavoila/class.ux_tx_templavoila_pi1.php';
	} else {
		$TYPO3_CONF_VARS['EXTCONF']['templavoila']['pi1']['renderElementClass'][] = 'EXT:feeditadvanced/templavoila/class.tx_templavoila_renderelement.php:tx_templavoila_renderelement';
	}
		// Restore the extension key
	$_EXTKEY = $realExtKey;

		// TemplaVoila frontend editing controller is the default when TemplaVoila is installed.
	t3lib_extMgm::addPageTSConfig('TSFE.frontendEditingController = templavoila');

		// Register the TemplaVoila frontend editing controller.
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tsfebeuserauth.php']['frontendEditingController']['templavoila'] = 'EXT:feeditadvanced/templavoila/class.tx_templavoila_frontendedit.php:tx_templavoila_frontendedit';

		// Needs to be included to avoid errors when editing page properties.
	include_once(t3lib_extMgm::extPath('templavoila').'class.tx_templavoila_handlestaticdatastructures.php');
} else {
	t3lib_extMgm::addPageTSConfig('TSFE.frontendEditingController = feeditadvanced');
	// add the wrap for each CE container area, so we can drop new CEs into empty areas
t3lib_extMgm::addTypoScript('feeditadvanced', 'setup', '
#############################################
## TypoScript added by extension "FE Editing Advanced"
#############################################

[globalVar = BE_USER|user|uid > 0]
styles.content.get.stdWrap {
	prepend = TEXT
	prepend.value = 0
	prepend.dataWrap = |-pages-{TSFE:id}
	prepend.wrap3 = <div class="feEditAdvanced-firstWrapper" id="feEditAdvanced-firstWrapper-colPos-|"></div>
}

styles.content.getLeft.stdWrap < styles.content.get.stdWrap
styles.content.getLeft.stdWrap.prepend.value = 1
styles.content.getRight.stdWrap < styles.content.get.stdWrap
styles.content.getRight.stdWrap.prepend.value = 2
styles.content.getBorder.stdWrap < styles.content.get.stdWrap
styles.content.getBorder.stdWrap.prepend.value = 3
styles.content.getNews.stdWrap  < styles.content.get.stdWrap
styles.content.getNews.stdWrap.prepend.value = news

[global]
', 43); // add this code AFTER the "css_styled_content" code (43) (because CSC empties styles > and would delete our changes)

}

?>