<?php

class tx_weccontentelements_lib {

	public function addContentElement($extensionKey, $key, $flexformPath = '', $type = '', $title = '', $description = '', $icon = '', $wizardIcon = '') {
		global $TCA;
		t3lib_div::loadTCA('tt_content');

			// Set defaults for title, description, icons, and content element type.
		$locallangPath = 'LLL:EXT:' . $extensionKey . '/' . $key . '/locallang.xml:tt_content.' . $key . '.';
		if (!$title) {
			$title = $locallangPath . 'title';
		}
		if (!$description) {
			$description = $locallangPath . 'description';
		}
		if (!$icon) {
			$icon = 'EXT:' . $extensionKey . '/' . $key . '/icon.gif';
		}
		if (!$wizardIcon) {
			$wizardIcon = t3lib_extMgm::extRelPath($extensionKey) . $key . '/wizard-icon.gif';
		}
		if (!$type) {
			$type = 'special';
		}
		if (!$flexformPath && @file_exists(t3lib_extMgm::extPath($extensionKey) . $key . '/flexform.xml')) {
			$flexformPath = 'FILE:EXT:' . $extensionKey . '/' . $key . '/flexform.xml';
		}


		if ($flexformPath) {
			$TCA['tt_content']['columns']['pi_flexform']['config']['ds']['*,' . $key] = $flexformPath;
			if (t3lib_div::int_from_ver(TYPO3_version) >= 4005000) {
				$TCA['tt_content']['types'][$key]['showitem'] = '--palette--;LLL:EXT:cms/locallang_ttc.xml:palette.general;general,
																	--palette--;LLL:EXT:cms/locallang_ttc.xml:palette.header;header,
																	--div--;' . $title . ',
																	pi_flexform;;;;1-1-1,
																	--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,
																	--palette--;LLL:EXT:cms/locallang_ttc.xml:palette.visibility;visibility,
																	--palette--;LLL:EXT:cms/locallang_ttc.xml:palette.access;access,
																	--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.appearance,
																	--palette--;LLL:EXT:cms/locallang_ttc.xml:palette.frames;frames,
																	--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.behaviour,
																	--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.extended';
			} else {
				$TCA['tt_content']['types'][$key] = array(
					'showitem' => 'CType;;4;;1-1-1, hidden, header;;3;;2-2-2, linkToTop;;;;3-3-3, --div--;' . $title . ', pi_flexform;;;;1-1-1, --div--;LLL:EXT:cms/locallang_tca.xml:pages.tabs.access, starttime, endtime'
				);
			}
		} else {
			if (t3lib_div::int_from_ver(TYPO3_version) >= 4005000) {
				$TCA['tt_content']['types'][$key]['showitem'] = '--palette--;LLL:EXT:cms/locallang_ttc.xml:palette.general;general,
																	--palette--;LLL:EXT:cms/locallang_ttc.xml:palette.header;header,
																	--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.access,
																	--palette--;LLL:EXT:cms/locallang_ttc.xml:palette.visibility;visibility,
																	--palette--;LLL:EXT:cms/locallang_ttc.xml:palette.access;access,
																	--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.appearance,
																	--palette--;LLL:EXT:cms/locallang_ttc.xml:palette.frames;frames,
																	--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.behaviour,
																	--div--;LLL:EXT:cms/locallang_ttc.xml:tabs.extended';
			} else {
				$TCA['tt_content']['types'][$key] = array(
					'showitem' => 'CType;;4;;1-1-1, hidden, header;;3;;2-2-2, linkToTop;;;;3-3-3, --div--;LLL:EXT:cms/locallang_tca.xml:pages.tabs.access, starttime, endtime'
				);
			}
		}

		t3lib_extMgm::addPlugin(array(
			$title,
			$key,
			$icon
		), 'CType');


		$TSConfig = 
			'wizards.newContentElement.wizardItems.' . $type . ' {
				elements {
					' . $key . ' {
						icon = ' . $wizardIcon . '
						title = ' . $title . '
						description = ' . $description . '
						tt_content_defValues {
							CType = ' . $key .'
						}
					}
				}
				show := addToList(' . $key .')
			}';

		t3lib_extMgm::addPageTSConfig(
			'mod.' . $TSConfig . chr(10) .
			'templavoila.' . $TSConfig . chr(10)
		);
	}

	public function addTyposcript($extensionKey, $key, $typoScriptPath = '') {
		if (!$typoScriptPath) {
			$typoScriptPath = t3lib_extMgm::extPath($extensionKey) . $key . '/content.ts';
		}

		$type = 'CType';
		$typoScriptContent = t3lib_div::getURL($typoScriptPath);
		if ($typoScriptContent) {
			t3lib_extMgm::addTypoScript($key, 'setup', '
			# Setting ' . $key . ' TypoScript
			' . $typoScriptContent . '
			', 43);
		}
	}


	/**
	 * Adds a Fluid-based template file.
	 *
	 * @param string The extension key.
	 * @param string The content element's unique key
	 * @param string Fluid template filename, relative to content element directory.
	 * @param string Path to Fluid partials, relative to content element directory.
	 * @param string Path to Fluid layouts, relative to content element directory.
	 */
	public function addFluid($extensionKey, $key, $file='content.html', $partialRootPath='Partials', $layoutRootPath='Layouts') {
		// Loop over all the flexform variables, converting them into TypoScript cObjects.
		$flexformVariables = self::getFlexformFieldsAsCObjects(t3lib_extMgm::extPath($extensionKey) . '/' . $key . '/flexform.xml');

		$typoScriptContent = '
			tt_content.' . $key . ' = COA
			tt_content.' . $key .' {
				10 = < lib.stdheader

				20 = FLUIDTEMPLATE
				20 {
					file = EXT:' . $extensionKey . '/' . $key . '/' . $file . '
					partialRootPath = EXT:' . $extensionKey . '/' . $key . '/' . $partialRootPath . '/
					layoutRootPath = EXT:' . $extensionKey . '/' . $key . '/' . $layoutRootPath . '/
					variables {
						' . $flexformVariables . '
					}
				}
			}
		';

		// If there's a custom TS file, use it instead of the default.
		$customTypoScriptPath = t3lib_extMgm::extPath($extensionKey) . $key . '/content.ts';
		if (file_exists($customTypoScriptPath)) {
			// Set up Fluid variables as temp.fluidVariables and then append the custom TypoScript.
 			$typoScriptContent .= chr(10) . t3lib_div::getURL($customTypoScriptPath);
		}

		t3lib_extMgm::addTypoScript($key, 'setup', '
		# Setting ' . $key . ' TypoScript
		' . $typoScriptContent . '
		', 43);
	}

	/**
	 * Reads fields from a FlexForm and converts them into TypoScript cObjects.
	 *
	 * @param string Path to the flexform file.
	 * @return string
	 */
	protected function getFlexformFieldsAsCObjects($flexformPath) {
		$flexform = simplexml_load_file($flexformPath);
		$cObjects = array();

		// Loop over all flexform fields and create an array element of TS for each.
		foreach ($flexform->ROOT->el->children() as $el) {
			$name = $el->getName();
			$cObjects[] = '
				' . $name . ' = TEXT
				' . $name . '.data = t3datastructure : pi_flexform->' . $name . '
			';
		}

		$output = implode(chr(10), $cObjects);
		return $output;
	}

}

?>