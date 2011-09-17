<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2005 Ingo Renner (typo3@ingo-renner.com)
*  All rights reserved
*
*  This script is part of the Typo3 project. The Typo3 project is
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
 * class.tx_timtab_catmenu.php
 *
 * Class which implements methods to connect between tt_news and ve_guestbook,
 * hooks for filling custom markers in these extensions an their templates.
 * Hooks in TYPO3's core are used, too.
 * The extraItemMarkerProcessor can be called by both, tt_news and ve_guestbook.
 *
 * $Id$
 *
 * @author Ingo Renner <typo3@ingo-renner.com>
 */

require_once(PATH_tslib.'class.tslib_pibase.php');

class tx_timtab_catmenu extends tslib_pibase {
	// Default plugin variables:
	var $prefixId      = 'tx_timtab_fe';		// Same as class name
	var $scriptRelPath = 'class.tx_timtab_fe.php';	// Path to this script relative to the extension dir.
	var $extKey        = 'timtab';	// The extension key.

	var $cObj;
	var $pObj;
	var $conf;
	var $newsEnableFields;
	var $catEnableFields;
	var $categoryPosts;
	var $categoryTimestamp;

	/**
	 * initializes the configuration for the class
	 *
	 * @param	array		the configuration coming from tt_news
	 * @param	object		the parent tt_news object
	 * @return	void
	 */
	function init($conf, $pObj) {
		$this->cObj = t3lib_div::makeInstance('tslib_cObj'); // local cObj.
		$this->pObj = $pObj;
		$this->pi_loadLL(); // Loading language-labels
		
		//not nice, but works
		$defaults = array( 'displayCatMenu.' => array(
			'wrap'            => '<ul>|</ul>',
			'showCount'       => '1',
			'hierarchical'    => '0',
			'sortBy'          => 'title',
			'sortOrder'       => 'ASC',
			'list'            => '1',
			'showLatestDate'  => '0',
			'catLatestDate.'  => array('strftime' => '%d.%m.%Y'),
			'hideEmpty'       => '1',
			'useDescForTitle' => '1'			
		));
		
		$this->conf = t3lib_div::array_merge_recursive_overrule(
			$defaults,
			array_merge($conf, $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_timtab.'])
		);		
		$this->conf['allowCaching'] = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tt_news.']['allowCaching'];
	
		$this->newsEnableFields = $this->cObj->enableFields('tt_news');
		$this->catEnableFields  = $this->cObj->enableFields('tt_news_cat');
	}

	/**
	 * renders the category menu for Kubrick
	 * 
	 * @param	array		the configuration array
	 * @parma	object		the parent tt_news object
	 * @return	string		the category menu
	 */
	function userDisplayCatmenu($conf, $pObj) {
		$this->init($conf, $pObj);		
		$content = '';
		
		if($pObj->conf['displayCatMenu.']['mode'] == 'timtab') {			
			$this->categories = $this->getCategories();			
			$catCounts        = $this->getCategoryCounts();
			
			if(!empty($catCounts)) {
				$this->categoryPosts = array();
				foreach($catCounts as $catCount) {
					if($this->conf['displayCatMenu.']['hideEmpty'] != 1 || $catCount['cat_count'] > 0) {
						$this->categoryPosts[$catCount['uid']] = $catCount['cat_count'];
					}
				}
			}

			//show dates?
			if($this->conf['displayCatMenu.']['showLatestDate']) {
				$catDates = $this->getCategoryDates();
				
				foreach($catDates as $catDate) {
					$this->categoryTimestamp[$catDate['uid_foreign']] = $catDate['ts'];
				}
			}
			
			$menu    = $this->getCategoryMenu(0, 0);
			$content = $this->cObj->wrap($menu, $this->conf['displayCatMenu.']['wrap']);
		}
		
		return $content;
	}
	
	/**
	 * builds the category menu recursivly
	 * 
	 * @param	boolean		???
	 * @param	integer		uid of parent category
	 * @param	array		array of categories
	 * @param	???			???
	 * @return	string		category menu
	 */
	function getCategoryMenu($childOf=0, $recurse=0) {
		$numFound = 0;
		$theList  = '';
		
		foreach($this->categories as $category) {
			if(($this->conf['displayCatMenu.']['hideEmpty'] == 0 || isset($this->categoryPosts[$category['uid']])) && 
			   (!$this->conf['displayCatMenu.']['hierarchical']) || $category['parent_category'] == $childOf) {
				
				$numFound++;
				$link = $this->getCategoryLink(
					$category, 
					$this->categoryPosts[$category['uid']],
					$this->categoryTimestamp[$category['uid']]
				);
				
				if($this->conf['displayCatMenu.']['list']) {
					$theList .= chr(13).'<li>'.$link.chr(10);
				} else {
					$theList .= chr(13).$link.'<br />'.chr(10);
				}

				if($this->conf['displayCatMenu.']['hierarchical']) {
					$theList .= $this->getCategoryMenu($category['uid'], 1);
				}
				
				if($this->conf['displayCatMenu.']['list']) {
					$theList .= '</li>'.chr(10);
				}

			}
		}
		
		if(!$numFound && !$childOf) {
			if($this->conf['displayCatMenu.']['list']) {
				$before = '<li>';
				$after  = '</li>';
			}
		
			return $before.$this->pi_getLL('no_categories').$after.chr(10);
		}
		
		if ($this->conf['displayCatMenu.']['list'] && $childOf && $numFound && $recurse) {
			$pre = "\t\t".'<ul class="children">';
			$post = "\t\t".'</ul>'.chr(10);
		} else {
			$pre = $post = '';
		}
		
		$theList = $pre.$theList.$post;
		
		if ($recurse) {
			return $theList;
		}
		
		return $theList;
	}
	
	/**
	 * 
	 */
	function getCategories() {
		$categories = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'uid, title, description, parent_category',
			'tt_news_cat',
			'1 = 1 '.$this->pObj->SPaddWhere.$this->catEnableFields,
			'',
			$this->conf['displayCatMenu.']['sortBy']
		);
		
		return $categories;
	}
	
	/**
	 * 
	 */
	function getCategoryCounts() {
		$catCounts = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'tt_news_cat.uid, tt_news_cat.title, COUNT(tt_news_cat_mm.uid_local) AS cat_count',
			'tt_news_cat
			INNER JOIN tt_news_cat_mm ON (tt_news_cat.uid = tt_news_cat_mm.uid_foreign)
			INNER JOIN tt_news ON (tt_news.uid = tt_news_cat_mm.uid_local)',				
			'tt_news.datetime < '.time().
				$pObj->SPaddWhere.
				$this->catEnableFields.
				$this->newsEnableFields,
			'tt_news_cat_mm.uid_foreign'
		);
		
		return $catCounts;
	}
	
	/**
	 * 
	 */
	function getCategoryDates() {
		$catDates = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'tt_news_cat_mm.uid_foreign, MAX(tt_news.datetime) AS ts', 
			'tt_news, tt_news_cat_mm',
			'tt_news_cat_mm.uid_local = tt_news.uid'.$this->newsEnableFields,
			'tt_news_cat_mm.uid_foreign'
		);
		
		return $catDates;
	}
	
	/**
	 * builds the category link
	 * 
	 * @param	array		the category data
	 * @param	integer		number of posts in the category
	 * @param	integer		timestamp of the latest post in that category
	 * @return	string		the link to the category
	 */
	function getCategoryLink($category, $count, $lastPost) {
		$urlParams = array(
			'tx_ttnews[cat]' => $category['uid'],
		);

		$aTagParams = '';
		if ($this->conf['displayCatMenu.']['useDescForTitle'] == 0 || empty($category['description'])) {
			$aTagParams .= ' title="'.sprintf($this->pi_getLL('view_category_posts'), $category['title']).'"';
		} else {
			$aTagParams .= ' title="'.$category['description'].'"';
		}

		$conf = array(
			'useCacheHash'     => $this->conf['allowCaching'],
			'no_cache'         => !$this->conf['allowCaching'],
			'parameter'        => $this->conf['displayCatMenu.']['targetPid'],
			'additionalParams' => $this->conf['parent.']['addParams'].t3lib_div::implodeArrayForUrl('',$urlParams,'',1).$this->pi_moreParams,
			'ATagParams'       => $aTagParams
		);		
		$link = $this->cObj->typoLink($category['title'], $conf);
	
		if($this->conf['displayCatMenu.']['showCount']) {
			$count = $count ? $count : 0;
			$link .= ' ('.$count.')';
		}
		
		if($this->conf['displayCatMenu.']['showLatestDate']) {
			$link .= ' '.strftime($this->conf['displayCatMenu.']['catLatestDate.']['strftime'], $lastPost);
		}

		return $link;
	}
	
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/timtab/class.tx_timtab_catmenu.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/timtab/class.tx_timtab_catmenu.php']);
}

?>