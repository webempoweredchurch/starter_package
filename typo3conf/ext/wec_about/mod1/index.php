<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2005-2009 Christian Technology Ministries International Inc.
 * All rights reserved
 *
 * This file is part of the Web-Empowered Church (WEC)
 * (http://WebEmpoweredChurch.org) ministry of Christian Technology Ministries 
 * International (http://CTMIinc.org). The WEC is developing TYPO3-based
 * (http://typo3.org) free software for churches around the world. Our desire
 * is to use the Internet to help offer new life through Jesus Christ. Please
 * see http://WebEmpoweredChurch.org/Jesus.
 *
 * You can redistribute this file and/or modify it under the terms of the 
 * GNU General Public License as published by the Free Software Foundation;
 * either version 2 of the License, or (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This file is distributed in the hope that it will be useful for ministry,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the file!
 ***************************************************************/
/**
 * Module 'About WEC' for the 'wec_about' extension.
 *
 * @author	Web Empowered Church Team <developer@webempoweredchurch.org>
 */



	// DEFAULT initialization of a module [BEGIN]
unset($MCONF);
require ("conf.php");
require ($BACK_PATH."init.php");
require ($BACK_PATH."template.php");
$LANG->includeLLFile("EXT:wec_about/mod1/locallang.php");
require_once(PATH_t3lib.'class.t3lib_scbase.php');
$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]

class tx_wecabout_module1 extends t3lib_SCbase {
	var $pageinfo;

	/**
	 *
	 */
	function init()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		parent::init();
	}

	/**
	 * Main function of the module. Write the content to $this->content
	 */
	function main()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;
		
		// Draw the header.
		$this->doc = t3lib_div::makeInstance('template');
		$this->doc->inDocStylesArray[] = 'body { padding: 0px; margin: 0px; }';
		$this->doc->backPath = $BACK_PATH;
		$this->doc->moduleTemplate = t3lib_div::getURL(PATH_site.'typo3conf/ext/wec_about/mod1/template.html');
		$this->doc->docType = 'xhtml_trans';

		$moduleContent = $this->moduleContent();

		$markers = array();
		$markers = array(
			'TITLE' => $LANG->getLL("title"),
			'CONTENT' => $moduleContent,
		);

		$this->content.= $this->doc->startPage('');
		$this->content.= $this->doc->moduleBody('', '', $markers);
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
		$content='<h2>About the Web-Empowered Church</h2>
				    <p style="margin-bottom: 10px;">
					  The Web-Empowered Church (WEC) is a ministry of <a href="http://www.ctmiinc.org">Christian Technology 
					  Ministries International Inc.</a> (CTMI). The mission of WEC is to innovatively apply WEB technology 
					  to EMPOWER the worldwide CHURCH for ministry.
					 </p>
					 <p style="margin-bottom: 10px;">
					  WEC will help churches around the world expand evangelism, discipleship, and care through the innovative 
					  application of Internet technology. WEC web-based tools and training will help make church ministries more 
					  efficient and effective, and will extend ministry impact to a world in need of 
					  <a href="http://www.webempoweredchurch.org/Jesus">Jesus</a>. We want to fuel a worldwide movement using the 
					  Internet to point the world to Jesus Christ, to grow disciples, and to care for those in need. Our desire is to 
					  use the web to empower the Church to become a truly 24 hours per day 7 days per week ministry that is not 
					  constrained by walls, distance, or time.
					 </p>
					 <p style="margin-bottom: 10px;">
					  If you would like to find out more about WEC, our tools, or support us in any way, please visit our website at
					  <a href="http://www.webempoweredchurch.org">www.webempoweredchurch.org</a>.
					 <h2>About the WEC Starter Package</h2>
					 <p style="margin-bottom: 10px;">
					  The WEC Starter Package is a default install of TYPO3 and commonly used extensions.
					 </p>
					 <p style="margin-bottom: 10px;">
					  When creating a new TYPO3 site, the biggest initial challenge is determining which extensions to install and how 
					  to configure those extensions to work properly.  The WEC Starter Package seeks to lower this barrier to entry 
					  by providing a default configuration that works for most websites out of the box.  The full power and flexibility 
					  of TYPO3 is preserved since all configuration options can still be changed if the user wishes to.
					 </p>
					 <p style="margin-bottom: 10px;">
					  In addition to a common set of extensions, the WEC Starter Package provides multiple WEC Templates and a default 
					  page tree populated with basic content. The goal is to give users a clear starting point to create a website, 
					  rather than opening the TYPO3 backend with a blank slate. Once again, the default templates and page tree in no 
					  way limit the experienced user as each can be removed and replaced with custom content.
					 </p>';
										  
		return $content;
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wec_about/mod1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/wec_about/mod1/index.php']);
}




// Make instance:
$SOBE = t3lib_div::makeInstance('tx_wecabout_module1');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>