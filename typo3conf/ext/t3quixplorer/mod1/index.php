<?php
/***************************************************************
*  Copyright notice
*  
*  (c) 2004 Mads Brunn (madsbrunn@gmail.com)
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
					
/***************************************************************

     The Original Code is index.php, released on 2003-04-02.

     The Initial Developer of the Original Code is The QuiX project.
	 
	 quix@free.fr
	 http://www.quix.tk
	 http://quixplorer.sourceforge.net

****************************************************************/


	
/** 	
 * Module 'Quixplorer' for the 't3quixplorer' extension.
 *
 * @author	Mads Brunn <madsbrunn@gmail.com>
 */
	


	// DEFAULT initialization of a module [BEGIN]
unset($MCONF);	
require ('conf.php');
require_once ('t3quixplorer_div.php');
require ($BACK_PATH.'init.php');
unset($MCONF);	
require ('conf.php');
require ($BACK_PATH.'template.php');
$LANG->includeLLFile('EXT:t3quixplorer/mod1/locallang.xml');
require_once (PATH_t3lib.'class.t3lib_scbase.php');

$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]

class tx_t3quixplorer_module1 extends t3lib_SCbase {


	/**
	 * Initializes the module. Basically just calls parent::init
	 *
	 * @return	void
	 */
	function init()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;
		parent::init();
	}

	/**
	 * Main function of the module. Write the content to $this->content
	 */
	function main()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS,$FILEICONS, $MCONF;

		$GLOBALS['T3Q_VARS'] = array();
		$config = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['t3quixplorer']);
		if (is_array($config)) $GLOBALS['T3Q_VARS'] = array_merge($GLOBALS['T3Q_VARS'],$config);

		if(!strlen(trim($GLOBALS['T3Q_VARS']['editable_ext']))){
			$GLOBALS['T3Q_VARS']['editable_ext'] = "\.phpcron$|\.ts$|\.tmpl$|\.txt$|\.php$|\.php3$|\.phtml$|\.inc$|\.sql$|\.pl$|\.htm$|\.html$|\.shtml$|\.dhtml$|\.xml$|\.js$|\.css$|\.cgi$|\.cpp$\.c$|\.cc$|\.cxx$|\.hpp$|\.h$|\.pas$|\.p$|\.java$|\.py$|\.sh$\.tcl$|\.tk$";
		} 

		
		if(!strlen(trim($GLOBALS['T3Q_VARS']['home_dir']))){
			$GLOBALS["T3Q_VARS"]["home_dir"] = preg_replace('/\/$/','',PATH_site);
		}

		if(!strlen(trim($GLOBALS['T3Q_VARS']['home_url']))){
			$GLOBALS['T3Q_VARS']['home_url'] = t3lib_div::getIndpEnv('TYPO3_REQUEST_HOST');
		}

		$GLOBALS['T3Q_VARS']['super_mimes'] = array(
			// dir, exe, file
			'dir'	=> array($LANG->getLL('mime.dir'),'folder.gif'),
			'exe'	=> array($LANG->getLL('mime.exe'),'exe.gif',"\.exe$|\.com$|\.bin$"),
			'file'	=> array($LANG->getLL('mime.file'),'default.gif')
		);
		$GLOBALS['T3Q_VARS']['used_mime_types'] = array(
			// text
			'text'	=> array($LANG->getLL('mime.text'),'txt.gif',"\.txt$"),
			
			// programming
			'php'	=> array($LANG->getLL('mime.php'),'php.gif',"\.php$|\.php3$|\.phtml$|\.inc$"),
			'sql'	=> array($LANG->getLL('mime.sql'),'src.gif',"\.sql$"),
			'perl'	=> array($LANG->getLL('mime.perl'),'pl.gif',"\.pl$"),
			'html'	=> array($LANG->getLL('mime.html'),'html.gif',"\.htm$|\.html$|\.shtml$|\.dhtml$|\.xml$"),
			'js'	=> array($LANG->getLL('mime.js'),'js.gif',"\.js$"),
			'css'	=> array($LANG->getLL('mime.css'),'src.gif',"\.css$"),
			'cgi'	=> array($LANG->getLL('mime.cgi'),'exe.gif',"\.cgi$"),
			//'py'	=> array($LANG->getLL('mime.py'),'py.gif',"\.py$"),
			//'sh'	=> array($LANG->getLL('mime.sh'),'sh.gif',"\.sh$"),
			// C++
			'cpps'	=> array($LANG->getLL('mime.cpps'),'cpp.gif',"\.cpp$|\.c$|\.cc$|\.cxx$"),
			'cpph'	=> array($LANG->getLL('mime.cpph'),'h.gif',"\.hpp$|\.h$"),
			// Java
			'javas'	=> array($LANG->getLL('mime.javas'),'java.gif',"\.java$"),
			'javac'	=> array($LANG->getLL('mime.javac'),'java.gif',"\.class$|\.jar$"),
			// Pascal
			'pas'	=> array($LANG->getLL('mime.pas'),'src.gif',"\.p$|\.pas$"),
			
			// images
			'gif'	=> array($LANG->getLL('mime.gif'),'image.gif',"\.gif$"),
			'jpg'	=> array($LANG->getLL('mime.jpg'),'image.gif',"\.jpg$|\.jpeg$"),
			'bmp'	=> array($LANG->getLL('mime.bmp'),'image.gif',"\.bmp$"),
			'png'	=> array($LANG->getLL('mime.png'),'image.gif',"\.png$"),
			
			// compressed
			'zip'	=> array($LANG->getLL('mime.zip'),'zip.gif',"\.zip$"),
			'tar'	=> array($LANG->getLL('mime.tar'),'tar.gif',"\.tar$"),
			'gzip'	=> array($LANG->getLL('mime.gzip'),'tgz.gif',"\.tgz$|\.gz$"),
			'bzip2'	=> array($LANG->getLL('mime.bzip2'),'tgz.gif',"\.bz2$"),
			'rar'	=> array($LANG->getLL('mime.rar'),'tgz.gif',"\.rar$"),
			//'deb'	=> array($LANG->getLL('mime.deb'),'package.gif',"\.deb$"),
			//'rpm'	=> array($LANG->getLL('mime.rpm'),'package.gif',"\.rpm$"),
			
			// music
			'mp3'	=> array($LANG->getLL('mime.mp3'),'mp3.gif',"\.mp3$"),
			'wav'	=> array($LANG->getLL('mime.wav'),'sound.gif',"\.wav$"),
			'midi'	=> array($LANG->getLL('mime.midi'),'midi.gif',"\.mid$"),
			'real'	=> array($LANG->getLL('mime.real'),'real.gif',"\.rm$|\.ra$|\.ram$"),
			//'play'	=> array($LANG->getLL('mime.play'),'mp3.gif',"\.pls$|\.m3u$"),
			
			// movie
			'mpg'	=> array($LANG->getLL('mime.mpg'),'video.gif',"\.mpg$|\.mpeg$"),
			'mov'	=> array($LANG->getLL('mime.mov'),'video.gif',"\.mov$"),
			'avi'	=> array($LANG->getLL('mime.avi'),'video.gif',"\.avi$"),
			'flash'	=> array($LANG->getLL('mime.flash'),'flash.gif',"\.swf$"),
			
			// Micosoft / Adobe
			'word'	=> array($LANG->getLL('mime.word'),'word.gif',"\.doc$"),
			'excel'	=> array($LANG->getLL('mime.excel'),'spread.gif',"\.xls$"),
			'pdf'	=> array($LANG->getLL('mime.pdf'),'pdf.gif',"\.pdf$")
		);
		
		
		//general date format used in listings
		$GLOBALS['T3Q_VARS']['date_fmt'] = 'd/m/Y H:i';
		$GLOBALS['T3Q_VARS']['moduleData'] = $BE_USER->getModuleData($MCONF['name']);

		if(t3lib_div::_GET('action')) $GLOBALS['T3Q_VARS']['action']=t3lib_div::_GET('action');
			else $GLOBALS['T3Q_VARS']['action']='list';
		if($GLOBALS['T3Q_VARS']['action']=="post" && t3lib_div::_POST('do_action')) {
			$GLOBALS['T3Q_VARS']['action']=t3lib_div::_POST('do_action');
		}
		if($GLOBALS['T3Q_VARS']['action']=='') $GLOBALS['T3Q_VARS']['action']='list';


		if(t3lib_div::_POST('jumptodir')){
			 $GLOBALS['T3Q_VARS']['dir']=t3lib_div::_POST('jumpdir');
		} elseif(t3lib_div::_GET('dir')){
			$GLOBALS['T3Q_VARS']['dir'] = t3lib_div::_GET('dir');
		} elseif($GLOBALS['T3Q_VARS']['moduleData']['dir'] && !isset($_GET['dir'])){
			 $GLOBALS['T3Q_VARS']['dir']=$GLOBALS['T3Q_VARS']['moduleData']['dir'];
		}

		if($GLOBALS['T3Q_VARS']['dir']=='.') $GLOBALS['T3Q_VARS']['dir']='';

		$GLOBALS['T3Q_VARS']['moduleData']['dir'] =  $GLOBALS['T3Q_VARS']['dir'];
		
		if(t3lib_div::_GET('item')) $GLOBALS['T3Q_VARS']['item']=t3lib_div::_GET('item');
		else $GLOBALS['T3Q_VARS']['item']='';

		if(t3lib_div::_GET('order')) $GLOBALS['T3Q_VARS']['order'] = t3lib_div::_GET('order');
		else $GLOBALS['T3Q_VARS']['order']='name';
		if($GLOBALS['T3Q_VARS']['order']=='') $GLOBALS['T3Q_VARS']['order'] = 'name';
		
		if(t3lib_div::_GET('srt')) $GLOBALS['T3Q_VARS']['srt']=t3lib_div::_GET('srt');
		else $GLOBALS['T3Q_VARS']['srt']='yes';
		if($GLOBALS['T3Q_VARS']['srt']=='') $GLOBALS['T3Q_VARS']['srt']=='yes';
		
			// Draw the header.
		$this->doc = t3lib_div::makeInstance('bigDoc');
		$this->doc->backPath = $BACK_PATH;

		# Render content:
		# NOTE NOTE NOTE
		# $this->doc->JScode is set in $this->moduleContent()
		# therefore it is very important that $this->moduleContent()
		# is called before $this->doc->startPage() and that the output
		# of this function is added to $this->content AFTER that
		# $this->doc->startPage() is called.
		
		$mainsection = $this->moduleContent();
		$this->content .= '<a name="top"></a>';		
		$this->content .= $this->doc->startPage($LANG->getLL('title'));		
		$this->content .= $this->doc->header($LANG->getLL('title'));
		$this->content .= $this->doc->spacer(5);
		$this->content .= $this->doc->section($mainsection['header'],$mainsection['content']);
		
		// ShortCut
		if ($BE_USER->mayMakeShortcut())	{
			$this->content.=$this->doc->spacer(20).$this->doc->section('',$this->doc->makeShortcutIcon('id,dir,item,action',implode(',',array_keys($this->MOD_MENU)),$this->MCONF['name']));
		}
		$this->content.=$this->doc->spacer(10);
		$BE_USER->pushModuleData($MCONF['name'],$GLOBALS['T3Q_VARS']['moduleData']);
	}

	/**
	 * Prints out the module HTML
	 */
	function printContent()	{

		$this->content.=$this->doc->endPage();
		echo $this->content;
	}



	/**
	 * Generates the module content
	 */
	function moduleContent()	{
		global $LANG;
		$section = array();
		$GLOBALS['T3Q_DEBUG']['initial_errors']= count($GLOBALS['T3Q_ERRORS']);

		switch ($GLOBALS['T3Q_VARS']['action']){

			case 'edit':
				require(t3lib_extMgm::extPath('t3quixplorer').'mod1/t3quixplorer_edit.php');
				$editObj = t3lib_div::makeInstance('t3quixplorer_edit');
				$section['header'] = $LANG->getLL('message.actedit');
				$section['content'] = $editObj->main($GLOBALS['T3Q_VARS']['dir'],$GLOBALS['T3Q_VARS']['item'],$this);
				break;

			case 'extract':
				require(t3lib_extMgm::extPath('t3quixplorer').'mod1/t3quixplorer_extract.php');
				$extractObj = t3lib_div::makeInstance('t3quixplorer_extract');
				$section['header'] = $LANG->getLL('message.actextract');
				$section['content'] = $extractObj->main($GLOBALS['T3Q_VARS']['dir'],$GLOBALS['T3Q_VARS']['item'],$this);
				break;

			case 'arch':
				require(t3lib_extMgm::extPath('t3quixplorer').'mod1/t3quixplorer_archive.php');
				$archObj = t3lib_div::makeInstance('t3quixplorer_archive');
				$section['header'] = $LANG->getLL('message.actarchive');
				$section['content'] = $archObj->main($GLOBALS['T3Q_VARS']['dir'],$this);
				break;
				
			case 'copy':	
			case 'move':
			case 'paste':
			case 'clear':
				require(t3lib_extMgm::extPath('t3quixplorer').'mod1/t3quixplorer_copymove.php');
				$copymoveObj = t3lib_div::makeInstance('t3quixplorer_copymove');
				$copymoveObj->main($GLOBALS['T3Q_VARS']['dir'],$this);
				break;

			case 'rename':
				require(t3lib_extMgm::extPath('t3quixplorer').'mod1/t3quixplorer_rename.php');
				$renameObj = t3lib_div::makeInstance('t3quixplorer_rename');
				$section['header'] = $LANG->getLL('message.actrename');
				$section['content'] = $renameObj->main($GLOBALS['T3Q_VARS']['dir'],$GLOBALS['T3Q_VARS']['item'],$this);
				break;

			case 'chmod':
				require(t3lib_extMgm::extPath('t3quixplorer').'mod1/t3quixplorer_chmod.php');
				$chmodObj = t3lib_div::makeInstance('t3quixplorer_chmod');
				$section['header'] = $LANG->getLL('message.actperms');
				$section['content'] = $chmodObj->main($GLOBALS['T3Q_VARS']['dir'],$GLOBALS['T3Q_VARS']['item'],$this);
				break;
				
			case 'delete':
				require(t3lib_extMgm::extPath('t3quixplorer').'mod1/t3quixplorer_delete.php');
				$deleteObj = t3lib_div::makeInstance('t3quixplorer_delete');
				$deleteObj->main($GLOBALS['T3Q_VARS']['dir'],$this); //no content added
				break;
				
			case 'mkitem':
				require(t3lib_extMgm::extPath('t3quixplorer').'mod1/t3quixplorer_mkitem.php');
				$mkitemObj = t3lib_div::makeInstance('t3quixplorer_mkitem');
				$mkitemObj->main($GLOBALS['T3Q_VARS']['dir'],$this); //no content added
				break;

			case 'upload':
				require(t3lib_extMgm::extPath('t3quixplorer').'mod1/t3quixplorer_upload.php');
				$uploadObj = t3lib_div::makeInstance('t3quixplorer_upload');
				$section['header'] = $LANG->getLL('message.actupload');
				$section['content'] = $uploadObj->main($GLOBALS['T3Q_VARS']['dir'],$this);
				break;
				
			case 'download':
				require(t3lib_extMgm::extPath('t3quixplorer').'mod1/t3quixplorer_download.php');
				$downloadObj = t3lib_div::makeInstance('t3quixplorer_download');
				$downloadObj->main($GLOBALS['T3Q_VARS']['dir'],$GLOBALS['T3Q_VARS']['item'],$this);
				break;
			
			case 'search':
				require(t3lib_extMgm::extPath('t3quixplorer').'mod1/t3quixplorer_search.php');
				$searchObj = t3lib_div::makeInstance('t3quixplorer_search');
				$section['header'] = $LANG->getLL('message.actsearch');
				$section['content'] = $searchObj->main($GLOBALS['T3Q_VARS']['dir'],$this);
				break;

				
			case 'list':
			default:
				require(t3lib_extMgm::extPath('t3quixplorer').'mod1/t3quixplorer_listdir.php');
				$listdirObj = t3lib_div::makeInstance('t3quixplorer_listdir');
				$section['header'] = '';
				$section['content'] = $listdirObj->main($GLOBALS['T3Q_VARS']['dir'],$this);
				break;

		}

		return $section;

	}
	
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3quixplorer/mod1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3quixplorer/mod1/index.php']);
}




// Make instance:
$SOBE = t3lib_div::makeInstance('tx_t3quixplorer_module1');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>