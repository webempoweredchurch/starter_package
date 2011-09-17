<?php

define('ON_LINE_PATTERN','/on line ([0-9]*)/');


unset($MCONF);	
require ("conf.php");
require_once ("t3quixplorer_div.php");
require ($BACK_PATH."init.php");
unset($MCONF);	
require ("conf.php");
require ($BACK_PATH."template.php");
$LANG->includeLLFile("EXT:t3quixplorer/mod1/locallang.php");
$BE_USER->modAccess($MCONF,1);



class parseChecker{

	function exec_syntaxcheck(){
		//generates random filename
		$tmp_filename = PATH_site.'typo3temp/'.md5(uniqid(rand(), true)).'.txt';
		
		//real filename (passed on via javascript)
		$realfilename = t3lib_div::_GP('realfilename');
		
		//writes the content of the textarea to a temporary file
		t3lib_div::writeFile($tmp_filename,t3lib_div::_GP('codetocheck'));
		
		
		$ok = true;
		
		$command = '';
		
		if(trim($config['php_path'])){
			$command .= $this->config['php_path'];
		} else {
			$command .= 'php'.(TYPO3_OS=='WIN' ? '.exe' : '');	
		}
		
		$command .= ' -l '.$tmp_filename;
		
		exec($command,$output,$returnvar);
		
		//deleting the temporary file	
		unlink($tmp_filename);
		
		if(is_array($output)){
			foreach($output as $key => $line){		
				if(!trim($line)){
					unset($output[$key]);						
				}
			}
		}
		$content = implode("<br />",$output);
		$content = str_replace($tmp_filename,$realfilename,$content);
		
		$content .= '<button class="close" onclick="window.parent.document.getElementById(\'parsecheckframe\').style.height=\'0px\';window.parent.document.getElementById(\'code\').rows=40;document.getElementById(\'execcontent\').style.visibility=\'hidden\'">Close</button>';
			
		if($returnvar > 0){	//there were parseerrors
			$ok = false;

			$content = '<div id="execcontent" style="position:relative;" class="error">'.$content.'</div>';

			//finding linenumbers where parseerrors were found			
			$matches = array();
			if(preg_match(ON_LINE_PATTERN, $content, $matches)){
				
				$content .= '
<script language="JavaScript1.2" type="text/javascript">
<!--	

window.parent.scrollToLine('.$matches[1].');				
		
// -->
</script>				
						';
			}
			
				
		} else {
			$ok = true;
			$content = '<div id="execcontent" style="position:relative;" class="noerror">'.$content.'</div>';
		}

		switch(t3lib_div::_GP('action')){
			case 'savedoc':
				if($ok){
					$content .= '
						<script language="JavaScript1.2" type="text/javascript">
						<!--  
							window.parent.document.getElementById(\'editfrm\').submit(); 
						// -->
						</script>
						';
				} else {						
					$content .= '
						<script language="JavaScript1.2" type="text/javascript">
						<!-- 
							window.parent.document.getElementById(\'parsecheckframe\').style.height = \'100px\';
							window.parent.document.getElementById(\'code\').rows = 20;
							if(confirm(\'Scriptet indeholder syntax-fejl. Er du sikker på at du vil gemme?\')){
								window.parent.document.getElementById(\'editfrm\').submit();
							}    
							// -->
						</script>
						';
				}
				break;
				
//			case 'closedoc':
//				if($ok){
//					$execcontent .= '
//						<script language="JavaScript1.2" type="text/javascript">
//						<!--  
//							window.parent.location = \''.t3lib_div::_GP('location').'\';   
//						// -->
//						</script>
//						';	
//								
//				} else {
//					$execcontent .= '
//						<script language="JavaScript1.2" type="text/javascript">
//						<!--  
//							window.parent.document.getElementById(\'parsecheckframe\').style.height = \'100px\';
//							window.parent.document.getElementById(\'code\').rows = 20;
//							if(confirm(\'Scriptet indeholder syntax-fejl. Er du sikker på at du vil lukke det?\')){
//								window.parent.location = \''.t3lib_div::_GP('location').'\'
//							} 
//						// --></script>
//						';		
//				}	
//				break;		
					
			case 'checksyntax':
			default:
				
				$content .= '
					<script language="JavaScript1.2" type="text/javascript">
					<!--  
						window.parent.document.getElementById(\'parsecheckframe\').style.height = \'100px\';
						window.parent.document.getElementById(\'code\').rows = 20;
					// -->
					</script>
					';
				break;						
				
				
		}
		
		return $content;
	}


	function main(){
		$this->config = unserialize($GLOBALS["TYPO3_CONF_VARS"]["EXT"]["extConf"]["t3quixplorer"]);

			

		$content = array();
		$content[] = '<html>';
		$content[] = '<head>';
		$content[] = '<title>PHP Parsechecker for Quixplorer</title>';
		$content[] = '<style type="text/css">';
		$content[] = 'BODY{margin:0;background-color: #F7F3EF;font-family: Verdana, Arial, Helvetica;font-size:12px;}';
		$content[] = '.error{border:5px solid red;padding:10px;}';
		$content[] = '.noerror{border:5px solid green;padding:10px;}';
		$content[] = '.close{position:absolute;right:0px;top:0px;font-size:10px;font-weight:bold;border:none;border-left:1px solid #000000;border-bottom:1px solid #000000;}';
		$content[] = '</style>';
		$content[] = '<!--[if IE]>';
		$content[] = '<style type="text/css">';
		$content[] = '.close{position:absolute;right:5px;top:5px;font-size:10px;font-weight:bold;border:none;border-left:1px solid #000000;border-bottom:1px solid #000000;}';
		$content[] = '</style>';		
		$content[] = '<![endif]-->';
		$content[] = '</head>';
		$content[] = '<body>';	
		$content[] = '<form id="parseform" method="post" action="parsechecker.php">';
		$content[] = '<input type="hidden" name="codetocheck" id="codetocheck" value="" />';
		$content[] = '<input type="hidden" name="realfilename" id="realfilename" value="" />';
		$content[] = '<input type="hidden" name="action" id="action" value="" />';
		$content[] = '<input type="hidden" name="location" id="location" value="" />';

		if(t3lib_div::_GP('codetocheck')){
			$content[] = $this->exec_syntaxcheck();
		}
		$content[] = '</form>';
		$content[] = '</body>';
		$content[] = '</html>';
		$this->content = implode("\n",$content);
		
	}


	function printContent(){
		echo $this->content;
	}
	
}



$parsechecker = t3lib_div::makeInstance("parseChecker");
$parsechecker->main();
$parsechecker->printContent();


?>