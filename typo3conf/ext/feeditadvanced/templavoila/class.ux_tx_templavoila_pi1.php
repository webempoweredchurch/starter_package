<?php
require_once(t3lib_extMgm::extPath('templavoila') . 'pi1/class.tx_templavoila_pi1.php');

class ux_tx_templavoila_pi1 extends tx_templavoila_pi1 {
	/**
	 * Common function for rendering of the Flexible Content / Page Templates.
	 * For Page Templates the input row may be manipulated to contain the proper reference to a data structure (pages can have those inherited which content elements cannot).
	 *
	 * @param	array		Current data record, either a tt_content element or page record.
	 * @param	string		Table name, either "pages" or "tt_content".
	 * @return	string		HTML output.
	 */
	function renderElement($row,$table)	{
		global $TYPO3_CONF_VARS;

			// First prepare user defined objects (if any) for hooks which extend this function:
		$hookObjectsArr = array();
		if (is_array ($TYPO3_CONF_VARS['EXTCONF']['templavoila']['pi1']['renderElementClass'])) {
			foreach ($TYPO3_CONF_VARS['EXTCONF']['templavoila']['pi1']['renderElementClass'] as $classRef) {
				$hookObjectsArr[] = &t3lib_div::getUserObj($classRef);
			}
		}

			// Hook: renderElement_preProcessRow
		foreach($hookObjectsArr as $hookObj)	{
			if (method_exists ($hookObj, 'renderElement_preProcessRow')) {
				$hookObj->renderElement_preProcessRow($row, $table, $this);
			}
		}

			// Get data structure:
		$srcPointer = $row['tx_templavoila_ds'];
		if (t3lib_div::testInt($srcPointer))	{	// If integer, then its a record we will look up:
			$DSrec = $GLOBALS['TSFE']->sys_page->checkRecord('tx_templavoila_datastructure', $srcPointer);
			$DS = t3lib_div::xml2array($DSrec['dataprot']);
		} else {	// Otherwise expect it to be a file:
			$file = t3lib_div::getFileAbsFileName($srcPointer);
			if ($file && @is_file($file))	{
				$DS = t3lib_div::xml2array(t3lib_div::getUrl($file));
			}
		}

			// If a Data Structure was found:
		if (is_array($DS))	{

				// Sheet Selector:
			if ($DS['meta']['sheetSelector'])	{
					// <meta><sheetSelector> could be something like "EXT:user_extension/class.user_extension_selectsheet.php:&amp;user_extension_selectsheet"
				$sheetSelector = &t3lib_div::getUserObj($DS['meta']['sheetSelector']);
				$renderSheet = $sheetSelector->selectSheet();
			} else {
				$renderSheet = 'sDEF';
			}

				// Initialize:
			$langChildren = $DS['meta']['langChildren'] ? 1 : 0;
			$langDisabled = $DS['meta']['langDisable'] ? 1 : 0;
			list ($dataStruct, $sheet, $singleSheet) = t3lib_div::resolveSheetDefInDS($DS,$renderSheet);

				// Data from FlexForm field:
			$data = t3lib_div::xml2array($row['tx_templavoila_flex']);

			$lKey = ($GLOBALS['TSFE']->sys_language_isocode && !$langDisabled && !$langChildren) ? 'l'.$GLOBALS['TSFE']->sys_language_isocode : 'lDEF';

			$dataValues = is_array($data['data']) ? $data['data'][$sheet][$lKey] : '';
			if (!is_array($dataValues))	$dataValues = array();

				// Init mark up object.
			$this->markupObj = t3lib_div::makeInstance('tx_templavoila_htmlmarkup');
			$this->markupObj->htmlParse = t3lib_div::makeInstance('t3lib_parsehtml');

				// Get template record:
			if ($row['tx_templavoila_to'])	{

					// Initialize rendering type:
				if ($this->conf['childTemplate'])	{
					$renderType = $this->conf['childTemplate'];
				} else {	// Default:
					$renderType = t3lib_div::GPvar('print') ? 'print' : '';
				}

					// Get Template Object record:
				$TOrec = $this->markupObj->getTemplateRecord($row['tx_templavoila_to'], $renderType, $GLOBALS['TSFE']->sys_language_uid);
				if (is_array($TOrec))	{

						// Get mapping information from Template Record:
					$TO = unserialize($TOrec['templatemapping']);
					if (is_array($TO))	{

							// Get local processing:
						$TOproc = t3lib_div::xml2array($TOrec['localprocessing']);
						if (!is_array($TOproc))	$TOproc=array();

							// Processing the data array:
						if ($GLOBALS['TT']->LR) $GLOBALS['TT']->push('Processing data');
							$vKey = ($GLOBALS['TSFE']->sys_language_isocode && !$langDisabled && $langChildren) ? 'v'.$GLOBALS['TSFE']->sys_language_isocode : 'vDEF';
							$TOlocalProc = $singleSheet ? $TOproc['ROOT']['el'] : $TOproc['sheets'][$sheet]['ROOT']['el'];

							/**
							 * Added for for frontend editing support.
							 **/

								// Store the original data values before the get processed.
							$originalDataValues = $dataValues;

							/**
							 * End frontend editing support.
							 **/

							$this->processDataValues($dataValues,$dataStruct['ROOT']['el'],$TOlocalProc,$vKey);

							/**
							 * Added for for frontend editing support.
							 **/
							if (is_object($GLOBALS['BE_USER']) && $GLOBALS['BE_USER']->isFrontendEditingActive()) {
									// Calculate flexformPointers. Can we do this via API instead?.
								foreach ($dataValues as $key => &$value) {
									$flexformPointer = array();
									$flexformPointer['table'] = $table;
									$flexformPointer['uid'] = $row['uid'];
									$flexformPointer['sheet'] = $renderSheet;
									$flexformPointer['sLang'] = $lKey;
									$flexformPointer['field'] = $key;
									$flexformPointer['vLang'] = $vKey;

										// Add a hidden field at the end of each container that provides destination pointer and ID, 
										// but only to elements that are not attributes.
									if ((!isset($DS['ROOT']['el'][$key]['type']) || $DS['ROOT']['el'][$key]['type'] != 'attr') && $DS['ROOT']['el'][$key]['tx_templavoila']['eType'] == 'ce') {
										$value[$vKey] .=  '<input type="hidden" class="feEditAdvanced-flexformPointers" title="' . implode(':', $flexformPointer) . '" value="' . $originalDataValues[$key][$vKey] . '" />';
									
											// Add some content to identify the container at the very beginning
										$value[$vKey] = '<div class="feEditAdvanced-firstWrapper" id="feEditAdvanced-firstWrapper-field-' . $flexformPointer['field'] . '-pages-' . $GLOBALS['TSFE']->id . '"></div>' . $value[$vKey];
									}
								}
							}
							/**
							 * End frontend editing support.
							 **/
						if ($GLOBALS['TT']->LR) $GLOBALS['TT']->pull();

							// Merge the processed data into the cached template structure:
						if ($GLOBALS['TT']->LR) $GLOBALS['TT']->push('Merge data and TO');
								// Getting the cached mapping data out (if sheets, then default to "sDEF" if no mapping exists for the specified sheet!)
							$mappingDataBody = $singleSheet ? $TO['MappingData_cached'] : (is_array($TO['MappingData_cached']['sub'][$sheet]) ? $TO['MappingData_cached']['sub'][$sheet] : $TO['MappingData_cached']['sub']['sDEF']);
							$content = $this->markupObj->mergeFormDataIntoTemplateStructure($dataValues,$mappingDataBody,'',$vKey);
							$this->markupObj->setHeaderBodyParts($TO['MappingInfo_head'],$TO['MappingData_head_cached'],$TO['BodyTag_cached']);
						if ($GLOBALS['TT']->LR) $GLOBALS['TT']->pull();

							// Edit icon (frontend editing):
						$eIconf = array('styleAttribute'=>'position:absolute;');
						if ($table=='pages')	$eIconf['beforeLastTag']=-1;	// For "pages", set icon in top, not after.
						$content = $this->pi_getEditIcon($content,'tx_templavoila_flex','Edit element',$row,$table,$eIconf);

							// Visual identification aids:
						if ($GLOBALS['TSFE']->fePreview && $GLOBALS['TSFE']->beUserLogin && !$GLOBALS['TSFE']->workspacePreview && !$this->conf['disableExplosivePreview'])	{
							$content = $this->visualID($content,$srcPointer,$DSrec,$TOrec,$row,$table);
						}
					} else {
						$content = $this->formatError('Template Object could not be unserialized successfully.
							Are you sure you saved mapping information into Template Object with UID "'.$row['tx_templavoila_to'].'"?');
					}
				} else {
					$content = $this->formatError('Couldn\'t find Template Object with UID "'.$row['tx_templavoila_to'].'".
						Please make sure a Template Object is accessible.');
				}
			} else {
				$content = $this->formatError('You haven\'t selected a Template Object yet for table/uid "'.$table.'/'.$row['uid'].'".
					Without a Template Object TemplaVoila cannot map the XML content into HTML.
					Please select a Template Object now.');
			}
		} else {
			$content = $this->formatError('
				Couldn\'t find a Data Structure set for table/row "'.$table.':'.$row['uid'].'".
				Please select a Data Structure and Template Object first.');
		}

		return $content;
	}
}

?>