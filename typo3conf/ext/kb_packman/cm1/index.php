<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2004-2010 Kraft Bernhard (kraftb@think-open.at)
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * kb_packman module cm1
 *
 * $Id$
 *
 * @author	Kraft Bernhard <kraftb@think-open.at>
 * @package TYPO3
 * @subpackage tx_kbpackman
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   83: class tx_dam_SCbase extends t3lib_SCbase
 *
 *
 *   91: class tx_kbpackman_module extends tx_dam_SCbase
 *  101:     function init()
 *  111:     function main()
 *  137:     function jumpToUrl(URL)
 *  169:     function printContent()
 *  182:     function moduleContent()
 *  304:     function setIndexData()
 *  318:     function getIndexData_ruleConf()
 *  339:     function getIndexData_fields($subConf)
 *  358:     function storeFilesTodo($damFileList)
 *  374:     function outputResult($header, $fileList, $param1 = '', $compProc = 0)
 *  400:     function checkValidLocation()
 *
 * TOTAL FUNCTIONS: 11
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */


	// DEFAULT initialization of a module [BEGIN]
unset($MCONF);
require ('conf.php');
require ($BACK_PATH.'init.php');
require ($BACK_PATH.'template.php');
$LANG->includeLLFile('EXT:kb_packman/cm1/locallang.php');
require_once (PATH_t3lib.'class.t3lib_scbase.php');
require_once (PATH_t3lib.'class.t3lib_basicfilefunc.php');
	// ....(But no access check here...)
	// DEFAULT initialization of a module [END]


require_once(t3lib_extMgm::extPath('kb_packman').'class.tx_kbpackman.php');
if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['DAMsupport']) {
	require_once(t3lib_extMgm::extPath('dam').'lib/class.tx_dam_guirenderlist.php');
	require_once(t3lib_extMgm::extPath('dam').'lib/class.tx_dam_scbase.php');
	require_once(t3lib_extMgm::extPath('kb_packman').'class.tx_kbpackman_dam.php');
} else	{

	/**
	 * Dummy DAM handler
	 *
	 */
	class tx_dam_SCbase extends t3lib_SCbase {
	}
}

	/**
	 * The kb_packman backend module handling compression / decompression in the fileadmin
	 *
	 */
class tx_kbpackman_module extends tx_dam_SCbase	{
	var $MOD_MENU = array(
		'tx_kbpackman_dam_filesTodo' => '',
	);

	/**
	 * The initialization method for the packman module
	 *
	 * @return	void		nothing
	 */
	function init()	{
		parent::init();
		$this->menuConfig();
	}

	/**
	 * Main function of the module. Write the content to $this->content
	 *
	 * @return	void		nothing
	 */
	function main()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['DAMsupport'])	{
			$this->guiItems = t3lib_div::makeInstance('tx_dam_guiRenderList');
		}

		$this->filefunc = t3lib_div::makeInstancE('t3lib_basicFileFunctions');
		$this->filefunc->init($GLOBALS['FILEMOUNTS'], $GLOBALS['TYPO3_CONF_VARS']['BE']['fileExtensions']);
		$this->dirTarget = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['kb_packman']['dirTarget'];
		$this->fileTarget = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['kb_packman']['fileTarget'];
		$this->targetExt = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['kb_packman']['targetExt']?$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['kb_packman']['targetExt']:'zip';

			// Draw the header.
		$this->doc = t3lib_div::makeInstance('mediumDoc');
		$this->doc->backPath = $BACK_PATH;
		$this->doc->form='<form action="" method="POST">';

		$this->file = t3lib_div::_GET('file');
		$this->comp = t3lib_div::_GET('comp');
		$this->overwrite = intval(t3lib_div::_GET('overwrite'));

			// JavaScript
		$this->doc->JScode = '
			<script language="javascript" type="text/javascript">
				script_ended = 0;
				function jumpToUrl(URL)	{
					document.location = URL;
				}
			</script>
		';

		$this->pageinfo=array('title' => 'File-List','uid'=>0,'pid'=>0);

		$headerSection = $this->doc->getHeader('pages',$this->pageinfo,$this->pageinfo['_thePath']).'<br />'.$LANG->sL('LLL:EXT:lang/locallang_core.php:labels.path').': '.t3lib_div::fixed_lgd_pre($this->pageinfo['_thePath'],50);

		$this->content.=$this->doc->startPage($LANG->getLL('title'));
		$this->content.=$this->doc->header($LANG->getLL('title'));
		$this->content.=$this->doc->spacer(5);

		// Render content:
		$this->moduleContent();


		// ShortCut
		if ($BE_USER->mayMakeShortcut())	{
			$this->content.=$this->doc->spacer(20).$this->doc->section('',$this->doc->makeShortcutIcon('id',implode(',',array_keys($this->MOD_MENU)),$this->MCONF['name']));
		}
		$this->content.=$this->doc->spacer(10);
	} // EOF: function main()



	/**
	 * Prints out the content
	 *
	 * @return	void		nothing
	 */
	function printContent()	{
		$this->content.=$this->doc->endPage();
		$this->content = $this->doc->insertStylesAndJS($this->content);
		echo $this->content;
	} // EOF: function printContent()



	/**
	 * Generate the module content
	 *
	 * @return	void		nothing
	 */
	function moduleContent()	{
		global $LANG, $BE_USER;
		$head = $LANG->getLL('unpack_head');
		$doIndexing = intval(t3lib_div::_GP('doIndexing'));
		if (!$doIndexing && strlen($this->comp)) {
			$head = $LANG->getLL('pack_head');
			$pacman = t3lib_div::makeInstance('tx_kbpackman');
			$pacman->overwrite = $this->overwrite;

			$item = $this->comp;
			$isfile = 0;
			$what = '';
			if (@is_dir($item))	{
				$isfile = 0;
				$target = $item;
				$what = 'dir';
			} elseif (@is_file($item))	{
				$isfile = 1;
				$target = dirname($item).'/';
				$what = 'file';
			} else	{
				$content = 'Invalid file/directory selected !';
				$this->content.=$this->doc->section($LANG->getLL('unpack_head'),$content,0,1);
				return;
			}
			$dirTarget = $pacman->getTarget($target, $isfile);
			$pathOK = $this->filefunc->checkPathAgainstMounts($dirTarget);
			if (!(is_writeable($dirTarget)&&(($pathOK&&($BE_USER->user['fileoper_perms']&0x1)) || intval($BE_USER->user['admin'])))) {
				$content = 'You are not allowed to create files !';
				$this->content.=$this->doc->section($LANG->getLL('unpack_head'),$content,0,1);
				return;
			}

			$targetFile = str_replace('.', '_', basename($item)).'.'.$this->targetExt;
 			$targetName = $this->filefunc->getUniqueName($targetFile, $dirTarget);

			$fileList = $pacman->pack($item, $targetName);
			$indexFileList = array($targetName);
			if (!$fileList || !count($fileList)) {;
				$content = 'No files in created archive (This shouldn\'t happen) !';
				$this->content.=$this->doc->section($LANG->getLL('unpack_head'),$content,0,1);
				return;
			}
			$outputReduced = ($this->targetExt == 'zip')?true:false;
			$content .= $this->outputResult($LANG->getLL('files_packed'), $fileList, basename($targetName), $outputReduced);
			$this->task = 'pack_'.$what;
		} elseif ($this->file && @is_file($this->file) && !$doIndexing) {
			$dir = dirname($this->file).'/';
			$pathOK = $this->filefunc->checkPathAgainstMounts($dir);
			if (!(is_writeable($dir)&&(($pathOK&&($BE_USER->user['fileoper_perms']&0x1)&&($BE_USER->user['fileoper_perms']&0x2)) || intval($BE_USER->user['admin'])))) {
				$content = 'You are not allowed to unpack files !';
				$this->content.=$this->doc->section($LANG->getLL('unpack_head'),$content,0,1);
				return;
			}
			if (!$this->checkValidLocation()) {
				$content = 'You are not allowed to unpack files in this location !';
				$this->content.=$this->doc->section($LANG->getLL('unpack_head'),$content,0,1);
				return;
			}
			$pacman = t3lib_div::makeInstance('tx_kbpackman');
			$pacman->overwrite = $this->overwrite;
			$fileList = $pacman->getList($this->file);
			if (!$fileList || !count($fileList)) {;
				$content = 'No files in archive (This shouldn\'t happen) !';
				$this->content.=$this->doc->section($LANG->getLL('unpack_head'),$content,0,1);
				return;
			}
			if (!$BE_USER->user['admin']) {
				if ($pacman->checkForbidden($fileList, dirname($this->file))) {
					$content = $LANG->getLL('files_not_allowed');
					$this->content.=$this->doc->section($LANG->getLL('unpack_head'),$content,0,1);
					return;
				}
			}
			$fileList = $pacman->unpack($this->file);
			$indexFileList = $fileList;
			$content .= $this->outputResult($LANG->getLL('files_unpacked'), $fileList);
			$this->task = 'unpack'.($this->overwrite?'_overwrite':'');
		} elseif (!$doIndexing) {
			$content .= 'Invalid commands';
		}
		if (!$doIndexing)	{
			$this->content.=$this->doc->section($head,$content,0,1);
		}

			// DAM integration
		if ($this->task && $fileList && $this->extClassConf)	{
			$damFileList = array();
			foreach ($indexFileList as $file)	{
				$absFile = $dir.$file;
				$damFileList[md5($absFile)] = $absFile;
			}
			$this->checkExtObj();
			$this->storeFilesTodo($damFileList);
			$this->addParams['task'] = $this->task;
			$_GET['indexStart'] = 1;
			$this->pathInfo = $dir;
			$this->extObjHeader();
			$this->setIndexData();
			$this->extObjContent();
		} elseif ($doIndexing)	{
			$this->task = t3lib_div::_GP('task');
			$this->addParams['task'] = $this->task;
			$this->checkExtObj();
			$this->extObjHeader();
			$this->setIndexData();
			$this->extObjContent();
		}

		if($unpacked && $this->extClassConf)	{
			$this->content .= $this->doc->divider(5);
		}
		$this->content .= '<a href="#" onClick="top.goToModule(top.currentModuleLoaded); return false;">'.$LANG->getLL('back').'</a>';
	} // EOF: function moduleContent()



	/**
	 * This method puts together all information required for DAM indexing of new (extracted) files
	 *
	 * @return	void
	 */
	function setIndexData()	{
		$setup = array();
		$setup['ruleConf'] = $this->getIndexData_ruleConf();
		$setup['dataPreset'] = $this->getIndexData_fields('dataPreset');
		$setup['dataPostset'] = $this->getIndexData_fields('dataPostset');
		$GLOBALS['SOBE']->MOD_SETTINGS['tx_damindex_indexSetup'] = serialize($setup);
		$this->extObj->processIndexSetup();
	}

	/**
	 * Setting up information required for DAM indexing of new (extracted) files
	 *
	 * @return	array		DAM indexing rules
	 */
	function getIndexData_ruleConf()	{
		$result = array();
		$this->extObj->index->setDefaultSetup();
		foreach ($this->extObj->index->ruleConf as $ruleName => $options)	{
			if ($ruleConf = $this->modTSconfig['properties']['dam.'][$this->task.'.']['ruleConf.'][$ruleName.'.'])	{
				foreach ($options as $key => $value)	{
					if (isset($ruleConf[$key]))	{
						$result[$ruleName][$key] = $ruleConf[$key];
					}
				}
			}
		}
		return $result;
	}

	/**
	 * Setting up information required for DAM indexing of new (extracted) files
	 *
	 * @param	array		DAM sub configuration key
	 * @return	array		DAM indexing fields
	 */
	function getIndexData_fields($subConf)	{
		global $TCA;
		$TSpreset = $this->modTSconfig['properties']['dam.'][$this->task.'.'][$subConf.'.'];
		$result = array();
		t3lib_div::loadTCA('tx_dam');
		foreach ($TCA['tx_dam']['columns'] as $field => $conf)	{
			if (isset($TSpreset[$field]))	{
				$result[$field] = $TSpreset[$field];
			}
		}
		return $result;
	}

	/**
	 * Storing data required for DAM indexing in session
	 *
	 * @param	array		Files scheduled for DAM indexing
	 * @return	void
	 */
	function storeFilesTodo($damFileList)	{
		$newSettings = array(
			'tx_kbpackman_dam_filesTodo' => serialize($damFileList),
		);
		$this->MOD_SETTINGS = t3lib_BEfunc::getModuleData($GLOBALS['SOBE']->MOD_MENU, $newSettings, $GLOBALS['SOBE']->MCONF['name'], $GLOBALS['SOBE']->modMenu_type, $GLOBALS['SOBE']->modMenu_dontValidateList, $GLOBALS['SOBE']->modMenu_setDefaultList);
	}

	/**
	 * Generate output for display of extracted/archived files
	 *
	 * @param	string		Header which to prepend output
	 * @param	array		List of files which were processed
	 * @param	string		Parameter which will replace the "###PARAM1###" marker
	 * @param	boolean		When set the amount of data size reduction would get shown (only valid for zip files being created)
	 * @return	string		Generated output
	 */
	function outputResult($header, $fileList, $param1 = '', $compProc = 0)	{
		global $LANG;
		$content = '';
		$content .= str_replace('###PARAM1###', $param1, $header).':';
		$content .= $this->doc->divider(5);
		$reducedLabel = $GLOBALS['LANG']->getLL('reduced');
		foreach ($fileList as $file) {
			if ($compProc)	{
				$parts = explode(' ', $file);
				$def = substr(trim(array_pop($parts)), 0, -1);
				array_pop($parts);
				$file = implode(' ', $parts);
				$file = basename($file).' <b>'.$def.' '.$reducedLabel.'</b>';
			}
			$content .= $file.'<br />'.chr(10);
		}
		$content .= $this->doc->divider(5);
		return $content;
	}


	/**
	 * Returns if the file is in a valid location
	 *
	 * @return	bool		File in valid location
	 */
	function checkValidLocation()	{
		if (strpos($this->file, PATH_site.'fileadmin/') !== 0) {
			return false;
		}
		foreach ($GLOBALS['BE_USER']->groupData['filemounts'] as $key => $val) {
			if (strpos($this->file, $val['path']) === 0) {
				return true;
			}
		}
		return false;
	} // EOF: function moduleContent()


} // EOF: class tx_kbpackman_module extends t3lib_SCbase



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kb_packman/cm1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kb_packman/cm1/index.php']);
}




// Make instance:
$SOBE = t3lib_div::makeInstance('tx_kbpackman_module');
$SOBE->init();


$SOBE->main();
$SOBE->printContent();

?>
