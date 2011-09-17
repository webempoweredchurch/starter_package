<?php

########################################################################
# Extension Manager/Repository config file for ext "feeditadvanced".
#
# Auto generated 10-05-2011 12:39
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Advanced Frontend Editing',
	'description' => 'This extension is the next generation for editing basic content directly through the frontend. It has all the bells an whistles like AJAX and Drag&Drop. TemplaVoila support included.',
	'category' => 'fe',
	'shy' => 0,
	'version' => '1.5.0',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'beta',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Frontend Editing Team',
	'author_email' => 'jeff@webempoweredchurch.org',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'typo3' => '4.3.0-0.0.0',
			'php' => '5.2.0-0.0.0',
		),
		'conflicts' => array(
			'feedit' => '',
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:134:{s:9:"ChangeLog";s:4:"3006";s:16:"ext_autoload.php";s:4:"384f";s:12:"ext_icon.gif";s:4:"3248";s:17:"ext_localconf.php";s:4:"3e19";s:13:"locallang.xml";s:4:"079c";s:51:"controller/class.tx_feeditadvanced_frontendedit.php";s:4:"3342";s:14:"doc/manual.sxw";s:4:"c67c";s:14:"doc/manual.txt";s:4:"758a";s:46:"hooks/class.tx_feeditadvanced_pagerenderer.php";s:4:"172a";s:56:"hooks/class.tx_feeditadvanced_tcemain_processdatamap.php";s:4:"340e";s:20:"res/css/csshover.htc";s:4:"c518";s:28:"res/css/csshover3-source.htc";s:4:"464e";s:28:"res/css/fe_edit_advanced.css";s:4:"416e";s:32:"res/css/fe_edit_advanced.css.rej";s:4:"b510";s:26:"res/css/fe_edit_closed.css";s:4:"7d79";s:30:"res/css/fe_edit_closed.css.rej";s:4:"c29e";s:26:"res/css/fe_formsOnPage.css";s:4:"4433";s:20:"res/css/lightbox.css";s:4:"0a2b";s:25:"res/icons/editing_off.gif";s:4:"f547";s:24:"res/icons/editing_on.gif";s:4:"753a";s:22:"res/icons/new_page.gif";s:4:"c854";s:24:"res/icons/new_record.gif";s:4:"d0f7";s:21:"res/icons/spinner.gif";s:4:"384e";s:40:"res/icons/typo3logo_mini_transparent.gif";s:4:"636d";s:35:"res/icons/forms/action_btn_left.png";s:4:"aebf";s:36:"res/icons/forms/action_btn_right.png";s:4:"451a";s:38:"res/icons/forms/action_button_left.png";s:4:"d24e";s:40:"res/icons/forms/action_button_middle.png";s:4:"cb79";s:39:"res/icons/forms/action_button_right.png";s:4:"8d76";s:32:"res/icons/forms/back_opacity.png";s:4:"8798";s:25:"res/icons/forms/close.gif";s:4:"97ab";s:29:"res/icons/forms/close_12h.gif";s:4:"a7a7";s:29:"res/icons/forms/close_win.png";s:4:"0047";s:28:"res/icons/forms/closedok.gif";s:4:"b89b";s:31:"res/icons/forms/group_clear.gif";s:4:"a728";s:34:"res/icons/forms/group_tobottom.gif";s:4:"0f4e";s:31:"res/icons/forms/group_totop.gif";s:4:"12b7";s:32:"res/icons/forms/group_upload.gif";s:4:"8a96";s:30:"res/icons/forms/helpbubble.gif";s:4:"ca92";s:30:"res/icons/forms/leftActive.gif";s:4:"a020";s:32:"res/icons/forms/leftDisabled.gif";s:4:"c111";s:32:"res/icons/forms/leftNoActive.gif";s:4:"a28a";s:31:"res/icons/forms/leftRounded.gif";s:4:"2b18";s:32:"res/icons/forms/middleActive.gif";s:4:"f3a0";s:34:"res/icons/forms/middleDisabled.gif";s:4:"4cc2";s:34:"res/icons/forms/middleNoActive.gif";s:4:"985e";s:28:"res/icons/forms/new_page.gif";s:4:"c854";s:30:"res/icons/forms/new_record.gif";s:4:"d0f7";s:31:"res/icons/forms/rightActive.gif";s:4:"fb15";s:33:"res/icons/forms/rightDisabled.gif";s:4:"f729";s:33:"res/icons/forms/rightNoActive.gif";s:4:"c4b2";s:32:"res/icons/forms/rightRounded.gif";s:4:"981a";s:24:"res/icons/forms/save.gif";s:4:"4d8d";s:32:"res/icons/forms/saveandclose.png";s:4:"6324";s:35:"res/icons/forms/saveandclosedok.gif";s:4:"4afa";s:27:"res/icons/forms/savebtn.png";s:4:"0907";s:27:"res/icons/forms/savedok.gif";s:4:"9137";s:23:"res/icons/forms/tab.png";s:4:"43ba";s:30:"res/icons/forms/tab_active.png";s:4:"dc48";s:34:"res/icons/forms/tab_background.png";s:4:"b680";s:28:"res/icons/forms/tab_left.png";s:4:"7513";s:35:"res/icons/forms/tab_left_active.png";s:4:"ff9f";s:29:"res/icons/forms/tab_right.png";s:4:"79ee";s:36:"res/icons/forms/tab_right_active.png";s:4:"215a";s:34:"res/icons/hovermenu/background.png";s:4:"7db2";s:35:"res/icons/hovermenu/button_down.gif";s:4:"55b0";s:35:"res/icons/hovermenu/button_hide.gif";s:4:"6b2c";s:37:"res/icons/hovermenu/button_unhide.gif";s:4:"da4d";s:33:"res/icons/hovermenu/button_up.gif";s:4:"57d8";s:33:"res/icons/hovermenu/clip_copy.gif";s:4:"6ed9";s:32:"res/icons/hovermenu/clip_cut.gif";s:4:"fbb3";s:39:"res/icons/hovermenu/clip_pasteafter.gif";s:4:"1f63";s:42:"res/icons/hovermenu/content_background.png";s:4:"d789";s:28:"res/icons/hovermenu/copy.png";s:4:"8b54";s:27:"res/icons/hovermenu/cut.png";s:4:"d417";s:30:"res/icons/hovermenu/delete.png";s:4:"1993";s:37:"res/icons/hovermenu/delete_record.gif";s:4:"e386";s:28:"res/icons/hovermenu/down.png";s:4:"60c7";s:28:"res/icons/hovermenu/drag.png";s:4:"e5c2";s:28:"res/icons/hovermenu/drop.png";s:4:"60c7";s:28:"res/icons/hovermenu/edit.png";s:4:"a34e";s:29:"res/icons/hovermenu/edit2.gif";s:4:"bb52";s:28:"res/icons/hovermenu/hide.png";s:4:"345b";s:27:"res/icons/hovermenu/new.png";s:4:"7698";s:32:"res/icons/hovermenu/new_page.gif";s:4:"c854";s:34:"res/icons/hovermenu/new_record.gif";s:4:"d0f7";s:30:"res/icons/hovermenu/unhide.png";s:4:"5e23";s:26:"res/icons/hovermenu/up.png";s:4:"34ce";s:28:"res/icons/menubar/button.png";s:4:"5d5a";s:33:"res/icons/menubar/button_left.png";s:4:"6088";s:34:"res/icons/menubar/button_right.png";s:4:"4b71";s:34:"res/icons/menubar/button_small.png";s:4:"cff2";s:39:"res/icons/menubar/button_small_left.png";s:4:"d1de";s:40:"res/icons/menubar/button_small_right.png";s:4:"b34c";s:35:"res/icons/menubar/button_square.png";s:4:"e3d3";s:36:"res/icons/menubar/clipboard_back.png";s:4:"ad03";s:26:"res/icons/menubar/form.png";s:4:"bde8";s:28:"res/icons/menubar/header.png";s:4:"eb2d";s:26:"res/icons/menubar/html.png";s:4:"97d0";s:32:"res/icons/menubar/menu2_back.png";s:4:"65d0";s:31:"res/icons/menubar/menu_back.png";s:4:"7e5a";s:29:"res/icons/menubar/picture.png";s:4:"d204";s:28:"res/icons/menubar/remove.png";s:4:"11d3";s:26:"res/icons/menubar/text.png";s:4:"d751";s:27:"res/icons/menubar/video.png";s:4:"2fe8";s:27:"res/icons/treemenu/drag.png";s:4:"051e";s:27:"res/icons/treemenu/file.png";s:4:"f6c9";s:29:"res/icons/treemenu/folder.png";s:4:"f058";s:36:"res/icons/treemenu/folder_closed.png";s:4:"ff73";s:34:"res/icons/treemenu/folder_open.png";s:4:"3264";s:24:"res/js/ext-base-debug.js";s:4:"ce24";s:18:"res/js/ext-base.js";s:4:"c870";s:22:"res/js/ext-dd-debug.js";s:4:"eefe";s:16:"res/js/ext-dd.js";s:4:"79be";s:20:"res/js/extjsinfo.txt";s:4:"d2fc";s:16:"res/js/feEdit.js";s:4:"9c30";s:25:"res/js/fe_logout_timer.js";s:4:"ef39";s:29:"res/js/getDynTabMenuJScode.js";s:4:"4b3e";s:18:"res/js/lightbox.js";s:4:"5f18";s:33:"res/template/content_element.tmpl";s:4:"c669";s:24:"res/template/feedit.tmpl";s:4:"d1a1";s:28:"res/template/feedit.tmpl.rej";s:4:"7b86";s:30:"res/template/page_buttons.tmpl";s:4:"eb1a";s:16:"service/ajax.php";s:4:"74a9";s:49:"templavoila/class.tx_templavoila_frontendedit.php";s:4:"f732";s:50:"templavoila/class.tx_templavoila_renderelement.php";s:4:"1ab9";s:43:"templavoila/class.ux_tx_templavoila_pi1.php";s:4:"8263";s:23:"templavoila/feEditTV.js";s:4:"7efe";s:43:"view/class.tx_feeditadvanced_adminpanel.php";s:4:"6cca";s:42:"view/class.tx_feeditadvanced_editpanel.php";s:4:"1654";s:57:"view/class.tx_feeditadvanced_getMainFields_preProcess.php";s:4:"eb57";s:37:"view/class.tx_feeditadvanced_menu.php";s:4:"38b1";s:51:"view/class.tx_feeditadvanced_newcontentelements.php";s:4:"66d9";s:41:"view/class.tx_feeditadvanced_tceforms.php";s:4:"6ee8";}',
);

?>