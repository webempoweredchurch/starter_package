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
*  The GNU General Public License can  be found at
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
 * class 'quixplorer_edit' for the 't3quixplorer' extension.
 * Class to a edit a text file
 *
 * @author	Mads Brunn <madsbrunn@gmail.com>
 */
/***************************************************************

     The Original Code is fun_edit.php, released on 2003-03-31.

     The Initial Developer of the Original Code is The QuiX project.
	 
	 quix@free.fr
	 http://www.quix.tk
	 http://quixplorer.sourceforge.net

****************************************************************/



require_once ('t3quixplorer_div.php');

class t3quixplorer_edit{

	function savefile($file_name) {			// save edited file
		global $LANG;
		$code = t3lib_div::_POST('code');
		if(t3lib_div::_POST('win_to_unix_br')){
			$code = str_replace(chr(13).chr(10),chr(10),$code);
		}
		$fp = @fopen($file_name, 'w');
		if($fp===false) t3quixplorer_div::showError(basename($file_name).': '.$LANG->getLL('error.savefile'));
		fputs($fp, $code);
		@fclose($fp);
	}
	
	function main($dir,$item,&$pObj) {	
		
		//debug(get_defined_constants());
		
			
		global $LANG;
		
		if(!t3quixplorer_div::get_is_file($dir, $item)) t3quixplorer_div::showError($item.': '.$LANG->getLL('error.fileexists'));
		if(!t3quixplorer_div::get_show_item($dir, $item)) t3quixplorer_div::showError($item.': '.$LANG->getLL('error.accessfile'));
		$fname = t3quixplorer_div::get_abs_item($dir, $item);
		$fileinfo = t3lib_div::split_fileref($fname);
		$ext = $fileinfo['fileext'];
		
		$theight = ($GLOBALS['T3Q_VARS']['textarea_height'] && is_numeric($GLOBALS['T3Q_VARS']['textarea_height']))?$GLOBALS['T3Q_VARS']['textarea_height']:20;
		
		
		$pObj->doc->JScode = '
				<script type="text/javascript" src="'.$pObj->doc->backPath.'tab.js"></script>
				<script type="text/javascript">
				<!--	
				
					var textarea_rows = '.$theight.';
					
					
					function scrollToLine(linenumber){
						var theCode = document.getElementById(\'code\');
						content = new String(theCode.value);
						var splitstr = content.split("\n");

						lineheight = theCode.scrollHeight / splitstr.length;
						theCode.scrollTop = (linenumber - 1) * lineheight;
						client="'.$CLIENT['BROWSER'].'";
						if(client=="net"){
							caretposition = 0;
							for(i = 0;i < linenumber-1; i++){
								caretposition +=  splitstr[i].length + 1 ;  
							}
							theCode.selectionStart = caretposition 
							theCode.selectionEnd = caretposition
							theCode.focus();
						}
					}

					function jumpToLine(linenumber){
						window.location="'.t3quixplorer_div::make_link('edit',$dir,$item).'#top";
						scrollToLine(linenumber);
					}
					

					function scroll(){
						var theLineNumber = document.getElementById(\'linenumber\');
						var theCode = document.getElementById(\'code\');
						content = new String(theCode.value);
						var splitstr = content.split("\n");

						lineheight = theCode.scrollHeight / splitstr.length;
						theCode.scrollTop = (theLineNumber.value - 1) * lineheight;
						client="'.$CLIENT['BROWSER'].'";
						if(client=="net"){
							caretposition = 0;
							for(i = 0;i < theLineNumber.value-1; i++){
								caretposition +=  splitstr[i].length + 1 ;  
							}
							theCode.selectionStart = caretposition 
							theCode.selectionEnd = caretposition
							theCode.focus();
						}
					}
					
					function keyhandler(e){
					  if(e==13){
					    scroll();
					  }
					}


					
					function checkSyntax(){
						textarea_rows = document.getElementById(\'code\').rows;
						document.getElementById(\'parsecheckframe\').style.height = \'100px\';
						frames[\'parsecheckframe\'].document.getElementById(\'codetocheck\').value = document.getElementById(\'code\').value;
						frames[\'parsecheckframe\'].document.getElementById(\'realfilename\').value = \''.$fname.'\'
						frames[\'parsecheckframe\'].document.getElementById(\'action\').value = \'checksyntax\';
						frames[\'parsecheckframe\'].document.getElementById(\'parseform\').submit();
					}
					
					
					function saveDoc(){
						var fileextension=\''.$ext.'\';
						if(fileextension == \'inc\' || fileextension == \'php\' || fileextension == \'php3\'){
							frames[\'parsecheckframe\'].document.getElementById(\'codetocheck\').value = document.getElementById(\'code\').value;
							frames[\'parsecheckframe\'].document.getElementById(\'realfilename\').value = \''.$fname.'\'
							frames[\'parsecheckframe\'].document.getElementById(\'action\').value = \'savedoc\';
							frames[\'parsecheckframe\'].document.getElementById(\'parseform\').submit();
						} else {
							document.getElementById(\'editfrm\').submit();							
						}
					}
					
					function closeDoc(){
						
						window.location=\''.t3quixplorer_div::make_link('list',$dir,NULL).'\';
						
					}					
					
				
				// -->
				</script>

			';
		
		$content= array();
		
		if(t3lib_div::_POST('dosave') && t3lib_div::_POST('dosave')=='yes') {
			// Save / Save As
			$item=basename(stripslashes(t3lib_div::_POST('fname')));
			$fname2=t3quixplorer_div::get_abs_item($dir, $item);
			if(!isset($item) || $item=='') t3quixplorer_div::showError($LANG->getLL('error.miscnoname'));
			if($fname!=$fname2 && @file_exists($fname2)) t3quixplorer_div::showError($item.': '.$LANG->getLL('error.itemdoesexist'));
			$this->savefile($fname2);
			$fname=$fname2;
		}
		
		// open file
		$fp = @fopen($fname, 'r');
		if($fp===false) t3quixplorer_div::showError($item.': '.$LANG->getLL('error.openfile'));
		@fclose($fp);
		
		$fileContent = t3lib_div::getUrl($fname);
		
		// header
		$s_item=t3quixplorer_div::get_rel_item($dir,$item);	if(strlen($s_item)>50) $s_item='...'.substr($s_item,-47);
		
	
		
		//$content[]=$s_item;

		//changed to absolute filename as of version 1.7 ... any complaints?
		$content[] = $fname;

		//$onkeydown = $GLOBALS['T3Q_VARS']['disable_tab'] ? '' : ' onkeydown="return catchTab(this,event)" ';

		$fileinfo = t3lib_div::split_fileref(t3quixplorer_div::get_abs_item($dir,$item));
		$lang = t3lib_div::_GP('highlight_lang');
		$ext = $fileinfo['fileext'];

		if(!$lang){
			$lang = $ext;
		}

		$content[]= '
		  <br />
		    <form id="editfrm" method="post" action="'.t3quixplorer_div::make_link('edit',$dir,$item).'" >
		    <input type="hidden" name="dosave" value="yes">
		    <textarea name="code" id="code" rows="'.$theight.'" style="width:740px;" wrap="off" class="enable-tab" >'.t3lib_div::formatForTextarea($fileContent).'</textarea>
		  ';
		  
		//iframe for parsechecker
		if($lang=='php' || $lang=='php3' || $lang=='inc'){
			$content[] = '
				<a name="parsechecker"> </a>
				<iframe frameborder="0" scrolling="no" src="parsechecker.php" style="width:740px;height:0px;" name="parsecheckframe" id="parsecheckframe"> </iframe>
			';
		}
				
		$content[]= '
		      <br />
			  <table>
				<tr>
				  <td>Convert windows linebreaks (13-10) to unix (10):</td>
				  <td><input type="checkbox" name="win_to_unix_br" value="1"'.(TYPO3_OS=="WIN"?"":" CHECKED ").'></td>
				  <td>&nbsp;&nbsp;
				</tr>
		';
		
		$content[]= '

			  </table>
			  <br />
		      <table>
			    	<tr>
				  		<td>
				    		<input type="text" name="fname" value="'.$item.'">
				  		</td>
		          		<td>
				    		<input type="button" name="savenow" value="'.$LANG->getLL('message.btnsave').'" onclick="saveDoc();" >
				  		</td>
				  		<td>
				    		<input type="reset" value="'.$LANG->getLL('message.btnreset').'">
				  		</td>
				  		<td>
		            <input type="button" value="'.$LANG->getLL('message.btnclose').'" onclick="closeDoc()">
				  		</td>
				  		<td>
		            &nbsp;'.$LANG->getLL('message.btnscroll').' &nbsp;<input type="Text" id="linenumber" name="linenumber" onkeypress="keyhandler(event.keyCode)"  value="'.t3lib_div::_GP('linenumber').'" size="5"> &nbsp;(+ ENTER)
              			</td></tr></table><br />';


		//extra functions for php-files
		if($lang=='php' || $lang=='php3' || $lang=='inc'){
			$content[] = '<table class="bgColor5"><tr>';
			
			$inputCode = $fileContent;
			$lines = explode("\n",$inputCode);

			foreach($lines as $linenumber => $line){
				if(preg_match('/\s*\bfunction\b\s*([a-z_][a-z0-9_]*)\s*/iA', $line, $match)) {
					//print_r($match);
					$functions[] = array('linenumber' => $linenumber, 'functionname' => $match[1]);
				}
			}

			if(is_array($functions) && !empty($functions)){
				reset($functions);
				$content[] = '
				<td>'.$LANG->getLL('message.btnfunction').'<select name="function_index" onchange="jumpToLine(parseInt(this.options[this.selectedIndex].value)+1)"><option value=""></option>
				';
				foreach($functions as $function){
					$content[] = '
						<option value="'.$function['linenumber'].'">'.$function['functionname'].'</option>
					';
				}
				$content[] = '
				</select></td>
				';
			}
			
			
			$content[] = '<td><input type="button" name="parsecheck" value="Check syntax" onclick="checkSyntax()" /></td>';
				
			$content[] = '</tr></table><br />';
		}

		$content[] = '
			    <table>
			    	<tr>
			    		<td>
			    			<input type="checkbox" name="highlight" '.(t3lib_div::_GP('highlight')? ' checked="checked" ' : '').' value="1">&nbsp;'.$LANG->getLL('message.highlight').'
			    		</td>
			    		<td>
			    			<select name="highlight_lang">
			    				<option value=""></option>
			    				<option value="php" '.($lang=='php' || $lang=='php3' || $lang=='inc'? ' selected ' : '').' >PHP</option>
			    				<option value="ts" '.($lang=='ts'? ' selected ' : '').'>TypoScript</option>
			    				<option value="xml" '.($lang=='xml'? ' selected ' : '').'>XML</option>
			    				<option value="sql" '.($lang=='sql'? ' selected ' : '').'>SQL</option>
			    				<option value="html4strict" '.($lang=='html' || $lang=='htm' || $lang=='html4strict' || $lang=='tmpl' ? ' selected ' : '').' >HTML</option>
			    				<option value="javascript" '.($lang=='js' || $lang=='javascript' ? ' selected ' : '').' >Javascript</option>
			    				<option value="perl" '.($lang=='perl' || $lang=='pl' ? ' selected ' : '').'>Perl</option>
			    				<option value="css" '.($lang=='css' ? ' selected ' : '').'>CSS</option>
			    				<option value="smarty" '.($lang=='smarty' ? ' selected ' : '').'>Smarty</option>
			    			</select>
			    			
			    		</td>
			    	</tr>
			    	
			    </table>

			    				
			  </form>

			 <br />
			<script language="JavaScript1.2" type="text/javascript">
			<!--
				if(document.getElementById(\'code\')) document.getElementById(\'code\').focus();
			// -->
			</script>';
		

		
		if(
			(
				t3lib_div::_GP('highlight') || 
				$GLOBALS['T3Q_VARS']['auto_highlight']
			) 
			
			&& 
			
			(
				t3lib_div::_GP('highlight_lang') || 
				(
					$GLOBALS['T3Q_VARS']['auto_highlight'] && 
					t3lib_div::inList('php,php3,inc,xml,sql,html,htm,js,pl,css,smarty,ts',$ext )
				)
			)
		){
			require_once ('geshi.php');
			$inputCode = $fileContent;

			if(t3lib_div::_GP('highlight_lang')){
				$hl = t3lib_div::_GP('highlight_lang');
			} else {

				switch($ext){
					case 'php':
					case 'php3':
					case 'inc':
						$hl = 'php';
						break;

					case 'html':
					case 'htm':
					case 'tmpl':
						$hl = 'html4strict';
						break;

					case 'js':
						$hl = 'javascript';
						break;

					case 'pl':
						$hl = 'perl';
						break;

					default:
						$hl = $ext;
						break;

				}
			}

			switch($hl){
				case 'php':
				case 'xml':
				case 'sql':
				case 'html4strict':
				case 'javascript':
				case 'perl':
				case 'css':
				case 'smarty':
					$geshi = new GeSHi($inputCode,$hl,'geshi/');
					$geshi->use_classes = true;
					$geshi->set_tab_width(4);
					$geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
					$geshi->set_link_target('_blank'); 
					$geshi->set_line_style("font-family:'Courier New', Courier, monospace; color: black; font-weight: normal; font-style: normal;font-size:12px;");

					$content[] = '
					<style type="text/css">
					
					.'.$hl.' *{font-size:11px;}
					
					'.$geshi->get_stylesheet().'
					</style>					
					';

					$content[] = '<hr />'.$geshi->parse_code(); 
					
					break;	
				case 'ts':
					require_once(PATH_t3lib.'class.t3lib_tsparser.php');
					$tsparser = t3lib_div::makeInstance('t3lib_TSparser');
					$tsparser->lineNumberOffset=1;
					$formattedContent = $tsparser->doSyntaxHighlight($inputCode, array($tsparser->lineNumberOffset), 0);
					$content[]='<hr />'.$formattedContent;		  
					break;
				default:
					break;
			}
		} elseif(!$GLOBALS['T3Q_VARS']['disable_text']) {	//show plain text (if not disabled in EM)
			$inputCode = $fileContent;
			$lines = split("\n",$inputCode);
			foreach($lines as $k => $v){
				// $lines[$k] = '<font color="black">'.str_pad(($k+1),4,' ',STR_PAD_LEFT).':</font> '.htmlspecialchars($v);
				$lines[$k] = '<li ondblclick="jumpToLine('.($k+1).')" >'.htmlspecialchars($v).'</li>';
			}
			$formattedContent = implode('',$lines);			
  			$formattedContent = ereg_replace('['.chr(10).chr(13).']','',$formattedContent);
			$content[] ='<hr /><pre class="ts-hl"><ol>'.$formattedContent.'</ol></pre>';
		}
		

		return implode('',$content);
	}



}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3quixplorer/mod1/t3quixplorer_edit.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3quixplorer/mod1/t3quixplorer_edit.php']);
}

?>