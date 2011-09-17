<?php

/***************************************************************
*  Copyright notice
*
*  (c) 1999-2005 Kasper Skaarhoj (kasperYYYY@typo3.com)
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
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/***************************************************************
* Copyright notice
*
* (c) 2005 Foundation for Evangelism (info@evangelize.org)
* All rights reserved
*
* This file is part of the Web-Empowered Church (WEC) ministry of the
* Foundation for Evangelism (http://evangelize.org). The WEC is developing 
* TYPO3-based free software for churches around the world. Our desire 
* use the Internet to help offer new life through Jesus Christ. Please
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
 * Wizard to help make pairings of slides and timings.  Each line 
 * is a pairing and a slide image and time are separated by a space.
 *
 * Modified version of Kasper Skaarhoj's Table Wizard from the TYPO3 core.
 *
 * @author	Kasper Skaarhoj <kasperYYYY@typo3.com>
 * @author	Web-Empowered Church Team <flashpresentation@webempoweredchurch.org>
 */

define('TYPO3_MOD_PATH', '../typo3conf/ext/wec_flashpresentation/');
$BACK_PATH='../../../typo3/';

require_once($BACK_PATH.'init.php');
require_once(PATH_typo3.'template.php');
$LANG->includeLLFile('EXT:wec_flashpresentation/locallang_db.xml');

/**
 * Script Class for rendering the Slide Wizard
 *
 * @author	Kasper Skaarhoj <kasperYYYY@typo3.com>
 * @author	Web-Empowered Church Team <flashpresentation@webempoweredchurch.org>
 * @package TYPO3
 * @subpackage wec_flashpresentation
 */
class SC_wizard_slides {

	// Internal, dynamic:
	var $doc;					// Document template object
	var $content;				// Content accumulation for the module.
	var $include_once=array();	// List of files to include.
	
	// Internal, static
	var $colsFieldName='cols';
	var $xmlStorage=0;
	
	// Internal, static: GPvars
	var $P;						// Wizard parameters, coming from TCEforms linking to the wizard.
	var $SLIDECFG;				// The array which is constantly submitted by the multidimensional form of this wizard.


	/**
	 * Initialization of the class
	 *
	 * @return	void
	 */
	function init()	{
		global $BACK_PATH;

		// GPvars:
		$this->P = t3lib_div::_GP('P');
		$this->SLIDECFG = t3lib_div::_GP('SLIDE');
		
		$this->xmlStorage = $this->P['params']['xmlOutput'];
		
		
		// Document template object:
		$this->doc = t3lib_div::makeInstance('mediumDoc');
		$this->doc->docType = 'xhtml_trans';
		$this->doc->backPath = $BACK_PATH;
		$this->doc->JScode=$this->doc->wrapScriptTags('
			function jumpToUrl(URL,formEl)	{	//
				document.location = URL;
			}
		');

		// Setting form tag:
		list($rUri) = explode('#',t3lib_div::getIndpEnv('REQUEST_URI'));
		$this->doc->form = '<form action="'.htmlspecialchars($rUri).'" method="post" name="wizardForm">';
		
		// Start page:
		$this->content .= $this->doc->startPage('Slide Images and Timings');
		
		// If save command found, include tcemain:
		if ($_POST['savedok_x'] || $_POST['saveandclosedok_x'])	{
			$this->include_once[]=PATH_t3lib.'class.t3lib_tcemain.php';
		}
	}

	/**
	 * Main function, rendering the slide wizard
	 *
	 * @return	void
	 */
	function main()	{
		global $LANG;

		if ($this->P['table'] && $this->P['field'] && $this->P['uid'])	{
			$this->content.=$this->doc->section($LANG->getLL('wizard.title'),$this->slideWizard(),0,1);
		} else {
			$this->content.=$this->doc->section($LANG->getLL('wizard.title'),'<span class="typo3-red">'.$LANG->getLL('wizard.noData',1).'</span>',0,1);
		}
		
		$this->content .= $this->doc->endPage();
	}

	/**
	 * Outputting the accumulated content to screen
	 *
	 * @return	void
	 */
	function printContent()	{
		echo $this->content;
	}

	/**
	 * Draws the slide wizard content
	 *
	 * @return	string		HTML content for the form.
	 */
	function slideWizard()	{
		
		// First, check the references by selecting the record:
		$row=t3lib_BEfunc::getRecord($this->P['table'],$this->P['uid']);
		if (!is_array($row))	{
			t3lib_BEfunc::typo3PrintError ('Wizard Error','No reference to record',0);
			exit;
		}

		// This will get the content of the form configuration code field to us - possibly cleaned up, saved to database etc. if the form has been submitted in the meantime.
		$SLIDECFGArray = $this->getConfigCode($row);

		// Generation of the Slide Wizards HTML code:
		$content = $this->getSlideHTML($SLIDECFGArray,$row);
		// Return content:
		return $content;
		
		
	}

	/***************************
	 *
	 * Helper functions
	 *
	 ***************************/

	/**
	 * Will get and return the configuration code string
	 * Will also save (and possibly redirect/exit) the content if a save button has been pressed
	 *
	 * @param	array		Current parent record row
	 * @return	array		Table config code in an array
	 * @access private
	 */
	function getConfigCode($row)	{
		
		$flexform = t3lib_div::xml2array($row[$this->P['field']]);
		
		// If some data has been submitted, then construct
		if (isset($this->SLIDECFG['c']))	{

			// Process incoming:
			$this->changeFunc();
			
			if ($this->xmlStorage)	{
				// Convert the input array to XML:
				$bodyText = t3lib_div::array2xml($this->SLIDECFG['c'],'',0,'T3FlexForms');

				// Setting cfgArr directly from the input:
				$cfgArr = $this->SLIDECFG['c'];
			} else {
				// Convert the input array to a string of configuration code:
				$bodyText = $this->cfgArray2CfgString($this->SLIDECFG['c']);

				// Create cfgArr from the string based configuration - that way it is cleaned up and any incompatibilities will be removed!
				$cfgArr = $this->cfgString2CfgArray($bodyText,$row[$this->colsFieldName]);
				
				// Update the flexform array with the new SLIDECFG
				$flexform['data']['slides']['lDEF']['slides']['vDEF'] = $bodyText;
			}
		}
		
		

		// If a save button has been pressed, then save the new field content:
		if ($_POST['savedok_x'] || $_POST['saveandclosedok_x'])	{

				// Make TCEmain object:
				$tce = t3lib_div::makeInstance('t3lib_TCEmain');
				$tce->stripslashes_values=0;

				// Put content into the data array:
				$data=array();
				//$data[$this->P['table']][$this->P['uid']][$this->P['field']]=$bodyText;
				$data[$this->P['table']][$this->P['uid']][$this->P['field']] = t3lib_div::array2xml($flexform,'',0,'T3FlexForms');

				// Perform the update:
				$tce->start($data,array());
				$tce->process_datamap();

				// If the save/close button was pressed, then redirect the screen:
				if ($_POST['saveandclosedok_x'])	{
					header('Location: '.t3lib_div::locationHeaderUrl($this->P['returnUrl']));
					exit;
				}
		} else {	// If nothing has been submitted, load the $bodyText variable from the selected database row:
			if ($this->xmlStorage)	{
				$cfgArr = t3lib_div::xml2array($row[$this->P['field']]);
			} else {	// Regular linebased slide configuration:
				$slideData = $flexform['data']['slides']['lDEF']['slides']['vDEF'];
				$cfgArr = $this->cfgString2CfgArray($slideData, $row[$this->colsFieldName]);
			}
			
			$cfgArr = is_array($cfgArr) ? $cfgArr : array();
		}

		return $cfgArr;
	}
	
	
	/**
	 * Creates the HTML for the Slide Wizard:
	 *
	 * @param	array		Table config array
	 * @param	array		Current parent record array
	 * @return	string		HTML for the slide wizard
	 * @access private
	 */
	function getSlideHTML($cfgArr,$row)	{
		
		global $LANG;

		// Traverse the rows:
		$tRows=array();
		$tHead=array();
		$k=0;
		
		foreach($cfgArr as $cellArr)	{
			if (is_array($cellArr))	{
				// Initialize:
				$cells=array();
				$a=0;

				// Traverse the columns:
				foreach($cellArr as $cellContent)	{
					$cells[]='<input type="text"'.$this->doc->formWidth(20).' name="SLIDE[c]['.(($k+1)*2).']['.(($a+1)*2).']" value="'.htmlspecialchars($cellContent).'" />';

					// Increment counter:
					$a++;
				}
				
				// CTRL panel for a slide/timing pairing (move up/down/around):
				$onClick="document.wizardForm.action+='#ANC_".(($k+1)*2-2)."';";
				$onClick=' onclick="'.htmlspecialchars($onClick).'"';
				$ctrl='';

				$brTag=$this->inputStyle?'':'<br />';
				if ($k!=0)	{
					$ctrl.='<input type="image" name="SLIDE[row_up]['.(($k+1)*2).']"'.t3lib_iconWorks::skinImg($this->doc->backPath,'gfx/pil2up.gif','').$onClick.' title="'.$LANG->getLL('wizard.slide_up',1).'" />'.$brTag;
				} else {
					$ctrl.='<input type="image" name="SLIDE[row_bottom]['.(($k+1)*2).']"'.t3lib_iconWorks::skinImg($this->doc->backPath,'gfx/turn_up.gif','').$onClick.' title="'.$LANG->getLL('wizard.slide_bottom',1).'" />'.$brTag;
				}
				$ctrl.='<input type="image" name="SLIDE[row_remove]['.(($k+1)*2).']"'.t3lib_iconWorks::skinImg($this->doc->backPath,'gfx/garbage.gif','').$onClick.' title="'.$LANG->getLL('wizard.slide_remove',1).'" />'.$brTag;

				if (($k+1)!=count($tLines))	{
					$ctrl.='<input type="image" name="SLIDE[row_down]['.(($k+1)*2).']"'.t3lib_iconWorks::skinImg($this->doc->backPath,'gfx/pil2down.gif','').$onClick.' title="'.$LANG->getLL('wizard.slide_down',1).'" />'.$brTag;
				} else {
					$ctrl.='<input type="image" name="SLIDE[row_top]['.(($k+1)*2).']"'.t3lib_iconWorks::skinImg($this->doc->backPath,'gfx/turn_down.gif','').$onClick.' title="'.$LANG->getLL('wizard.slide_top',1).'" />'.$brTag;
				}
				$ctrl.='<input type="image" name="SLIDE[row_add]['.(($k+1)*2).']"'.t3lib_iconWorks::skinImg($this->doc->backPath,'gfx/add.gif','').$onClick.' title="'.$LANG->getLL('wizard.slide_add',1).'" />'.$brTag;

				$tRows[]='
					<tr class="bgColor4">
						<td class="bgColor5"><a name="ANC_'.(($k+1)*2).'"></a><span class="c-wizButtonsV">'.$ctrl.'</span></td>
						<td>'.implode('</td>
						<td>',$cells).'</td>
					</tr>';

					// Increment counter:
				$k++;
			}
		}
		
		$tHead = '<tr class="bgColor5">' .
				  '<td />'.
				  '<td align="center">Slide Images</th>' .
				  '<td align="center">Slide Timings</th>'.
				 '</tr>';
		
		$content = '';
		
		// Add CSH:
		$content.= t3lib_BEfunc::cshItem('xMOD_csh_corebe', 'wizard_slide_wiz', $GLOBALS['BACK_PATH'],'');

		// Implode all table rows into a string, wrapped in table tags.
		$content.= '
			<!--
				Slide wizard
			-->
			<table border="0" cellpadding="0" cellspacing="1" id="typo3-slidewizard">' .
				$tHead.
				implode('',$tRows).'
			</table>';

		// Add saving buttons in the bottom:
		$content.= '

			<!--
				Save buttons:
			-->
			<div id="c-saveButtonPanel">';
		$content.= '<input type="image" class="c-inputButton" name="savedok"'.t3lib_iconWorks::skinImg($this->doc->backPath,'gfx/savedok.gif','').' title="'.$LANG->sL('LLL:EXT:lang/locallang_core.php:rm.saveDoc',1).'" />';
		$content.= '<input type="image" class="c-inputButton" name="saveandclosedok"'.t3lib_iconWorks::skinImg($this->doc->backPath,'gfx/saveandclosedok.gif','').' title="'.$LANG->sL('LLL:EXT:lang/locallang_core.php:rm.saveCloseDoc',1).'" />';
		$content.= '<a href="#" onclick="'.htmlspecialchars('jumpToUrl(unescape(\''.rawurlencode($this->P['returnUrl']).'\')); return false;').'">'.
					'<img'.t3lib_iconWorks::skinImg($this->doc->backPath,'gfx/closedok.gif','width="21" height="16"').' class="c-inputButton" title="'.$LANG->sL('LLL:EXT:lang/locallang_core.php:rm.closeDoc',1).'" alt="" />'.
					'</a>';
		$content.= '<input type="image" class="c-inputButton" name="_refresh"'.t3lib_iconWorks::skinImg($this->doc->backPath,'gfx/refresh_n.gif','').' title="'.$LANG->getLL('wizard.forms_refresh',1).'" />';
		$content.= t3lib_BEfunc::cshItem('xMOD_csh_corebe', 'wizard_slide_wiz_buttons', $GLOBALS['BACK_PATH'],'');
		$content.= '
			</div>
			';
		
		// Return content:
		return $content;
	}

	/**
	 * Detects if a control button (up/down/around/delete) has been pressed for an item and accordingly it will manipulate the internal SLIDECFG array
	 *
	 * @return	void
	 * @access private
	 */
	function changeFunc()	{
		if ($this->SLIDECFG['row_remove'])	{
			$kk = key($this->SLIDECFG['row_remove']);
			$cmd='row_remove';
		} elseif ($this->SLIDECFG['row_add'])	{
			$kk = key($this->SLIDECFG['row_add']);
			$cmd='row_add';
		} elseif ($this->SLIDECFG['row_top'])	{
			$kk = key($this->SLIDECFG['row_top']);
			$cmd='row_top';
		} elseif ($this->SLIDECFG['row_bottom'])	{
			$kk = key($this->SLIDECFG['row_bottom']);
			$cmd='row_bottom';
		} elseif ($this->SLIDECFG['row_up'])	{
			$kk = key($this->SLIDECFG['row_up']);
			$cmd='row_up';
		} elseif ($this->SLIDECFG['row_down'])	{
			$kk = key($this->SLIDECFG['row_down']);
			$cmd='row_down';
		}

		if ($cmd && t3lib_div::testInt($kk)) {
			if (substr($cmd,0,4)=='row_')	{
				switch($cmd)	{
					case 'row_remove':
						unset($this->SLIDECFG['c'][$kk]);
					break;
					case 'row_add':
						$this->SLIDECFG['c'][$kk+1]=array();
					break;
					case 'row_top':
						$this->SLIDECFG['c'][1]=$this->SLIDECFG['c'][$kk];
						unset($this->SLIDECFG['c'][$kk]);
					break;
					case 'row_bottom':
						$this->SLIDECFG['c'][10000000]=$this->SLIDECFG['c'][$kk];
						unset($this->SLIDECFG['c'][$kk]);
					break;
					case 'row_up':
						$this->SLIDECFG['c'][$kk-3]=$this->SLIDECFG['c'][$kk];
						unset($this->SLIDECFG['c'][$kk]);
					break;
					case 'row_down':
						$this->SLIDECFG['c'][$kk+3]=$this->SLIDECFG['c'][$kk];
						unset($this->SLIDECFG['c'][$kk]);
					break;
				}
				ksort($this->SLIDECFG['c']);
			}
		}

		// Convert line breaks to <br /> tags:
		reset($this->SLIDECFG['c']);
		while(list($a)=each($this->SLIDECFG['c']))	{
			reset($this->SLIDECFG['c'][$a]);
			while(list($b)=each($this->SLIDECFG['c'][$a]))	{
				$this->SLIDECFG['c'][$a][$b] = str_replace(chr(10),'<br />',str_replace(chr(13),'',$this->SLIDECFG['c'][$a][$b]));
			}
		}
	}

	/**
	 * Converts the input array to a configuration code string
	 *
	 * @param	array		Array of slide configuration (follows the input
	 * structure from the slide wizard POST form)
	 * @return	string		The array converted into a string with line-based configuration.
	 * @see cfgString2CfgArray()
	 */
	function cfgArray2CfgString($cfgArr)	{
		// Initialize:
		$inLines=array();

		// Traverse the elements of the slide wizard and transform the settings into configuration code.
		reset($this->SLIDECFG['c']);
		while(list($a)=each($this->SLIDECFG['c']))	{
			$thisLine=array();
			reset($this->SLIDECFG['c'][$a]);
			while(list($b)=each($this->SLIDECFG['c'][$a]))	{
				$thisLine[]=str_replace(' ','',$this->SLIDECFG['c'][$a][$b]);
			}
			$inLines[]=implode(' ',$thisLine);
		}

		// Finally, implode the lines into a string:
		$bodyText = implode(chr(10),$inLines);

		// Return the configuration code:
		return $bodyText;
	}

	/**
	 * Converts the input configuration code string into an array
	 *
	 * @param	string		Configuration code
	 * @param	integer		Default number of columns
	 * @return	array		Configuration array
	 * @see cfgArray2CfgString()
	 */
	function cfgString2CfgArray($cfgStr,$cols)	{
		//select only specified tag
		
		// Explode lines in the configuration code - each line is a slide/timing pair.
		$tLines=explode(chr(10),$cfgStr);

		// Setting number of columns
		if (!$cols && trim($tLines[0]))	{	// auto...
			$cols = count(explode(' ',$tLines[0],2));
		}
		$cols=$cols?$cols:2;

		// Traverse the number of slide/timing pairs:
		$cfgArr=array();
		foreach($tLines as $k => $v)	{

			// Initialize:
			$vParts = explode(chr(32),$v);
			
			// Traverse columns:
			for ($a=0;$a<$cols;$a++)	{
				$cfgArr[$k][$a]=$vParts[$a];
			}
		}

		// Return configuration array:
		return $cfgArr;
	}
}

if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/wec_flashpresentation/wizard_slides.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/wec_flashpresentation/wizard_slides.php"]);
}

// Make instance:
$SOBE = t3lib_div::makeInstance('SC_wizard_slides');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>
