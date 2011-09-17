<?php
/*

 NOTICE ABOUT CONFIGURATION:
 
 TYPO3 offers an install tool with three simple steps to configure the most necessary options such as the database.
 Please go to typo3/install/index.php to configure TYPO3 and install the database tables.
 Just make sure this file is writable by PHP - otherwise the Install Tool will not succeed with the configuration.

 If you want to manually enter values into this file take a look at t3lib/config_default.php which is the file that includes this one. By looking into this file you should be able to figure out which variables set which values.
 
*/

$TYPO3_CONF_VARS["SYS"]["sitename"] = 'WEC Starter Package';
$TYPO3_CONF_VARS["SYS"]["encryptionKey"] ="";
$TYPO3_CONF_VARS['SYS']['compat_version'] = '4.5';

$TYPO3_CONF_VARS["SYS"]["USdateFormat"] = '1';
$TYPO3_CONF_VARS['SYS']['ddmmyy'] = 'm-d-y';
$TYPO3_CONF_VARS['SYS']['respectTimeZones'] = '1';

$TYPO3_CONF_VARS['SYS']['enableDeprecationLog'] = '0';

// Add .ts to allowed extensions so that skins can be edited from Filelist.
$TYPO3_CONF_VARS["SYS"]["textfile_ext"] .= ',ts';

$TYPO3_CONF_VARS['BE']['interfaces'] = 'backend,frontend';

// Setting Install Tool password to the default "joh316":
$TYPO3_CONF_VARS["BE"]["installToolPassword"] = "bacb98acf97e0b6112b1d1b650b84971";

$TYPO3_CONF_VARS['EXT']['extList'] = 'css_styled_content,wec_contentelements,tsconfig_help,context_help,extra_page_cm_options,impexp,sys_note,tstemplate,tstemplate_ceditor,tstemplate_info,tstemplate_objbrowser,tstemplate_analyzer,func_wizards,wizard_crpages,wizard_sortpages,lowlevel,install,belog,beuser,aboutmodules,setup,info_pagetsconfig,viewpage,rtehtmlarea,t3quixplorer,indexed_search,tt_news,metatags,be_acl,static_info_tables,templavoila,realurl,wec_about,wec_user,wec_flashplayer,wec_flashpresentation,wec_devo,wec_button,wec_discussion,templavoila_framework,timtab,cc_awstats,t3skin,wec_api,wec_sermons,wec_servant,wec_ebible,sr_feuser_register,rgsmoothgallery,felogin,cal,opendocs,ve_guestbook,div2007,about,cshmanual,recycler,openid,t3editor,wec_styles,pmkfdl,captcha,kb_packman,mm_forum,wec_staffdirectory,feeditadvanced,wec_connector,pagebrowse,info,perm,func,filelist,reports,wec_config,wec_map,skin_bifold,skin_brushstrokes,skin_cityscape,skin_lightsout,skin_lilypads,skin_meadow,skin_regal,skin_sketchbook,skin_stacks,skin_touchofelegance,skin_warehouse,skin_weatheredwood,skin_wireframe';
$TYPO3_CONF_VARS['EXT']['extList_FE'] = 'css_styled_content,wec_contentelements,install,rtehtmlarea,t3quixplorer,indexed_search,tt_news,metatags,be_acl,static_info_tables,templavoila,realurl,wec_about,wec_user,wec_flashplayer,wec_flashpresentation,wec_devo,wec_button,wec_discussion,templavoila_framework,timtab,cc_awstats,t3skin,wec_api,wec_sermons,wec_servant,wec_ebible,sr_feuser_register,rgsmoothgallery,felogin,cal,ve_guestbook,div2007,openid,wec_styles,pmkfdl,captcha,kb_packman,mm_forum,wec_staffdirectory,feeditadvanced,wec_connector,pagebrowse,wec_config,wec_map,skin_bifold,skin_brushstrokes,skin_cityscape,skin_lightsout,skin_lilypads,skin_meadow,skin_regal,skin_sketchbook,skin_stacks,skin_touchofelegance,skin_warehouse,skin_weatheredwood,skin_wireframe';

$TYPO3_CONF_VARS['EXT']['extConf']['be_acl'] = 'a:2:{s:26:"disableOldPermissionSystem";s:1:"1";s:20:"enableFilterSelector";s:1:"1";}';	//  Modified or inserted by TYPO3 Extension Manager.
$TYPO3_CONF_VARS['EXT']['extConf']['cal'] = 'a:20:{s:13:"noTabDividers";s:1:"0";s:21:"hideLocationTextfield";s:1:"0";s:22:"hideOrganizerTextfield";s:1:"0";s:20:"useLocationStructure";s:15:"tx_cal_location";s:21:"useOrganizerStructure";s:16:"tx_cal_organizer";s:16:"categoryTVHeight";s:3:"280";s:10:"newRecurUI";s:1:"1";s:9:"useTeaser";s:1:"0";s:17:"useRecordSelector";s:1:"0";s:11:"todoSubtype";s:5:"event";s:20:"useNewRecurringModel";s:1:"0";s:15:"recurrenceStart";s:8:"20000101";s:13:"recurrenceEnd";s:8:"20201231";s:18:"useInternalCaching";s:1:"1";s:11:"cachingMode";s:6:"normal";s:13:"cacheLifetime";s:1:"0";s:13:"cachingEngine";s:8:"internal";s:11:"treeOrderBy";s:3:"uid";s:9:"showTimes";s:1:"1";s:30:"enableRealURLAutoConfiguration";s:1:"1";}';	// Modified or inserted by TYPO3 Extension Manager. 
$TYPO3_CONF_VARS['EXT']['extConf']['captcha'] = 'a:21:{s:6:"useTTF";s:1:"1";s:8:"imgWidth";s:3:"150";s:9:"imgHeight";s:2:"50";s:12:"captchaChars";s:1:"5";s:9:"noNumbers";s:1:"0";s:4:"bold";s:1:"0";s:7:"noLower";s:1:"0";s:7:"noUpper";s:1:"1";s:13:"letterSpacing";s:2:"12";s:5:"angle";s:2:"20";s:5:"diffx";s:1:"0";s:5:"diffy";s:1:"2";s:4:"xpos";s:2:"10";s:4:"ypos";s:2:"10";s:6:"noises";s:1:"6";s:9:"backcolor";s:7:"#f4f4f4";s:9:"textcolor";s:7:"#000000";s:11:"obfusccolor";s:7:"#c0c0c0";s:8:"fontSize";s:2:"18";s:8:"fontFile";s:0:"";s:12:"excludeChars";s:14:"gijloGIJLO0169";}';
$TYPO3_CONF_VARS['EXT']['extConf']['be_acl'] = 'a:2:{s:26:"disableOldPermissionSystem";s:1:"1";s:20:"enableFilterSelector";s:1:"1";}';
$TYPO3_CONF_VARS['EXT']['extConf']['css_styled_content'] = 'a:1:{s:15:"setPageTSconfig";s:1:"1";}';
$TYPO3_CONF_VARS['EXT']['extConf']['date2cal'] = 'a:9:{s:11:"calendarCSS";s:6:"t3skin";s:8:"datetime";s:1:"1";s:11:"calendarImg";s:0:"";s:7:"doCache";s:1:"0";s:13:"natLangParser";s:1:"0";s:7:"helpImg";s:0:"";s:8:"firstDay";s:1:"0";s:18:"secOptionsAlwaysOn";s:1:"1";s:6:"calImg";s:29:"EXT:date2cal/res/calendar.png";}';
$TYPO3_CONF_VARS['EXT']['extConf']['indexed_search'] = 'a:17:{s:8:"pdftools";s:0:"";s:8:"pdf_mode";s:2:"20";s:14:"nativeOOMethod";s:1:"0";s:10:"OOoExtract";s:0:"";s:4:"ruby";s:0:"";s:6:"catdoc";s:0:"";s:6:"xlhtml";s:0:"";s:7:"ppthtml";s:0:"";s:5:"unrtf";s:0:"";s:9:"debugMode";s:1:"0";s:23:"disableFrontendIndexing";s:1:"0";s:6:"minAge";s:2:"24";s:6:"maxAge";s:3:"168";s:16:"maxExternalFiles";s:1:"5";s:11:"flagBitMask";s:3:"196";s:16:"ignoreExtensions";s:0:"";s:17:"indexExternalURLs";s:1:"1";}';
$TYPO3_CONF_VARS['EXT']['extConf']['kb_packman'] = 'a:5:{s:9:"dirTarget";s:2:"./";s:10:"fileTarget";s:2:"./";s:9:"extTarget";s:3:"zip";s:10:"DAMsupport";s:1:"1";s:9:"targetExt";s:3:"zip";}';
$TYPO3_CONF_VARS['EXT']['extConf']['opendocs'] = 'a:1:{s:12:"enableModule";s:1:"0";}';
$TYPO3_CONF_VARS['EXT']['extConf']['pmkfdl'] = 'a:6:{s:10:"blockedExt";s:21:"php,php4,php5,inc,sql";s:17:"noAccess_handling";s:0:"";s:28:"noAccess_handling_statheader";s:22:"HTTP/1.0 404 Not Found";s:14:"ttnewsFileHook";s:1:"0";s:17:"enableExampleHook";s:1:"0";s:7:"logfile";s:14:"pmkfdl_log.txt";}';
$TYPO3_CONF_VARS['EXT']['extConf']['realurl'] = 'a:5:{s:10:"configFile";s:26:"typo3conf/realurl_conf.php";s:14:"enableAutoConf";s:1:"1";s:14:"autoConfFormat";s:1:"0";s:12:"enableDevLog";s:1:"0";s:19:"enableChashUrlDebug";s:1:"0";}';	// Modified or inserted by TYPO3 Extension Manager. 
$TYPO3_CONF_VARS['EXT']['extConf']['rgsmoothgallery'] = 'a:2:{s:11:"splitRecord";s:2:"\\n";s:12:"splitComment";s:1:"|";}';
$TYPO3_CONF_VARS['EXT']['extConf']['rtehtmlarea'] = 'a:18:{s:21:"noSpellCheckLanguages";s:23:"ja,km,ko,lo,th,zh,b5,gb";s:15:"AspellDirectory";s:15:"/usr/bin/aspell";s:17:"defaultDictionary";s:2:"en";s:14:"dictionaryList";s:2:"en";s:18:"HTMLAreaPluginList";s:158:"TableOperations, SpellChecker, ContextMenu, SelectColor, TYPO3Browsers, InsertSmiley, FindReplace, RemoveFormat, CharacterMap, QuickTag, InlineCSS, DynamicCSS";s:16:"enableAllOptions";s:1:"1";s:22:"enableMozillaExtension";s:1:"1";s:16:"forceCommandMode";s:1:"0";s:15:"enableDebugMode";s:1:"0";s:23:"enableCompressedScripts";s:1:"1";s:20:"mozAllowClipboardUrl";s:104:"http://ftp.mozilla.org/pub/mozilla.org/extensions/allowclipboard_helper/allowclipboard_helper-0.2-fx.xpi";s:18:"plainImageMaxWidth";s:3:"640";s:19:"plainImageMaxHeight";s:3:"680";s:20:"defaultConfiguration";s:105:"Typical (Most commonly used features are enabled. Select this option if you are unsure which one to use.)";s:12:"enableImages";s:1:"1";s:24:"enableAccessibilityIcons";s:1:"0";s:16:"enableDAMBrowser";s:1:"0";s:14:"enableInOpera9";s:1:"1";}';
$TYPO3_CONF_VARS['EXT']['extConf']['sr_feuser_register'] = 'a:6:{s:12:"uploadFolder";s:27:"uploads/tx_srfeuserregister";s:10:"imageTypes";s:30:"png, jpg, jpeg, gif, tif, tiff";s:12:"imageMaxSize";s:3:"250";s:12:"useFlexforms";s:1:"1";s:14:"useMd5Password";s:1:"0";s:12:"usePatch1822";s:1:"0";}';
$TYPO3_CONF_VARS['EXT']['extConf']['static_info_tables'] = 'a:2:{s:7:"charset";s:5:"utf-8";s:12:"usePatch1822";s:1:"0";}';
$TYPO3_CONF_VARS['EXT']['extConf']['t3quixplorer'] = 'a:10:{s:9:"no_access";s:5:"^\\.ht";s:11:"show_hidden";s:1:"1";s:8:"home_url";s:0:"";s:8:"home_dir";s:0:"";s:11:"show_thumbs";s:1:"1";s:15:"textarea_height";s:2:"30";s:12:"editable_ext";s:215:"\\.phpcron$|\\.ts$|\\.tmpl$|\\.txt$|\\.php$|\\.php3$|\\.phtml$|\\.inc$|\\.sql$|\\.pl$|\\.htm$|\\.html$|\\.shtml$|\\.dhtml$|\\.xml$|\\.js$|\\.css$|\\.cgi$|\\.cpp$\\.c$|\\.cc$|\\.cxx$|\\.hpp$|\\.h$|\\.pas$|\\.p$|\\.java$|\\.py$|\\.sh$\\.tcl$|\\.tk$";s:14:"auto_highlight";s:1:"1";s:12:"disable_text";s:1:"1";s:8:"php_path";s:3:"php";}';
$TYPO3_CONF_VARS['EXT']['extConf']['templavoila'] = 'a:2:{s:7:"enable.";a:5:{s:20:"pageTemplateSelector";s:1:"1";s:13:"oldPageModule";s:1:"0";s:16:"selectDataSource";s:1:"0";s:15:"renderFCEHeader";s:1:"0";s:19:"selectDataStructure";s:1:"0";}s:9:"staticDS.";a:3:{s:6:"enable";s:1:"1";s:8:"path_fce";s:36:"fileadmin/config/templavoila/ds/fce/";s:9:"path_page";s:37:"fileadmin/config/templavoila/ds/page/";}}';	// Modified or inserted by TYPO3 Extension Manager. 
$TYPO3_CONF_VARS['EXT']['extConf']['templavoila_framework'] = 'a:2:{s:14:"customSkinPath";s:23:"fileadmin/config/skins/";s:17:"templateObjectPID";s:3:"108";}';	
$TYPO3_CONF_VARS['EXT']['extConf']['tt_news'] = 'a:22:{s:13:"useStoragePid";s:1:"0";s:13:"noTabDividers";s:1:"0";s:25:"l10n_mode_prefixLangTitle";s:1:"1";s:22:"l10n_mode_imageExclude";s:1:"1";s:20:"hideNewLocalizations";s:1:"0";s:13:"prependAtCopy";s:1:"1";s:5:"label";s:5:"title";s:9:"label_alt";s:0:"";s:10:"label_alt2";s:0:"";s:15:"label_alt_force";s:1:"0";s:21:"categorySelectedWidth";s:1:"0";s:17:"categoryTreeWidth";s:1:"0";s:18:"categoryTreeHeigth";s:1:"5";s:11:"treeOrderBy";s:5:"title";s:17:"requireCategories";s:1:"0";s:18:"useInternalCaching";s:1:"1";s:11:"cachingMode";s:6:"normal";s:13:"cacheLifetime";s:1:"0";s:13:"cachingEngine";s:8:"internal";s:24:"writeCachingInfoToDevlog";s:10:"disabled|0";s:23:"writeParseTimesToDevlog";s:1:"0";s:18:"parsetimeThreshold";s:3:"0.1";}';
$TYPO3_CONF_VARS['EXT']['extConf']['wec_config'] = 'a:5:{s:10:"rootpageid";s:2:"61";s:12:"tcaAdditions";s:1:"1";s:15:"templateStorage";s:2:"31";s:11:"templatePID";s:2:"31";s:15:"templateFeedURL";s:64:"http://templates.webempoweredchurch.org/repository/templates.xml";}';
$TYPO3_CONF_VARS['EXT']['extConf']['wec_contentelements'] = 'a:1:{s:29:"includeDefaultContentElements";s:1:"1";}';
$TYPO3_CONF_VARS['EXT']['extConf']['wec_discussion'] = 'a:2:{s:5:"label";s:7:"subject";s:9:"label_alt";s:0:"";}';
$TYPO3_CONF_VARS['EXT']['extConf']['wec_map'] = 'a:5:{s:15:"feUserRecordMap";s:1:"0";s:15:"geocodingStatus";s:1:"0";s:14:"defaultCountry";s:3:"USA";s:10:"apiVersion";s:1:"2";s:6:"apiURL";s:66:"http://maps.google.com/maps?file=api&amp;v=%s&amp;key=%s&amp;hl=%s";}';
$TYPO3_CONF_VARS['EXT']['extConf']['wec_sermons'] = 'a:2:{s:18:"resourceUploadPath";s:21:"uploads/tx_wecsermons";s:13:"haverunbefore";i:1;}';
$TYPO3_CONF_VARS['EXT']['extConf']['wec_staffdirectory'] = 'a:3:{s:10:"useFEUsers";s:1:"0";s:7:"showMap";s:1:"0";s:19:"useStaffDeptRecords";s:1:"1";}';

$TYPO3_CONF_VARS["FE"]["lifetime"] = '604800';
$TYPO3_CONF_VARS["FE"]["pageNotFound_handling"] = '/notfound/';
$TYPO3_CONF_VARS["FE"]["logfile_dir"] = 'fileadmin/logs/';

## INSTALL SCRIPT EDIT POINT TOKEN - all lines after this points may be changed by the install script!

?>
