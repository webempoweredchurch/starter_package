##############################################################################
# Copyright notice
#
# (c) 2010 Christian Technology Ministries International Inc.
# All rights reserved
#
# This file is part of the Web-Empowered Church (WEC)
# (http://WebEmpoweredChurch.org) ministry of Christian Technology Ministries
# International (http://CTMIinc.org). The WEC is developing TYPO3-based
# (http://typo3.org) free software for churches around the world. Our desire
# is to use the Internet to help offer new life through Jesus Christ. Please
# see http://WebEmpoweredChurch.org/Jesus.
#
# You can redistribute this file and/or modify it under the terms of the
# GNU General Public License as published by the Free Software Foundation;
# either version 2 of the License, or (at your option) any later version.
#
# The GNU General Public License can be found at
# http://www.gnu.org/copyleft/gpl.html.
#
# This file is distributed in the hope that it will be useful for ministry,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
#
# This copyright notice MUST APPEAR in all copies of the file!
##############################################################################

##############################################################################
#
# This is a modified version of skin_bn_wireframe developed by Ron Hall of BusyNoggin, Inc.
#
##############################################################################

##############################################################
# This is TypoScript used to modify the core templates to
# display this skin. Rewrite the header, footer, pre code and 
# post code libraries and more when needed to change structure
##############################################################

preCodeHeader = HTML
preCodeHeader.value = <div id="pageWrap-outter"><div id="pageWrap">
postCodeHeader >

preCodeFeature >
postCodeFeature >

preCodeContent >


preCodeGeneratedContent-1 >
postCodeGeneratedContent-1 >

preCodeContentBlock-1 >
postCodeContentBlock-1 >

preCodeContentBlock-2 >
postCodeContentBlock-2 >

preCodeContentBlock-3 >
postCodeContentBlock-3 >

preCodeGeneratedContent-2 >
postCodeGeneratedContent-2 >

preCodeFooter = HTML
preCodeFooter.value (
	<div class="clear"></div>
	</div>
		</div>
			<!-- end #pageWrap  -->
				<div id="footer-top"></div>
					</div>
)

postCodeFooter >


### Lets table classes be added in the RTE
lib.parseFunc_RTE.externalBlocks.table.stdWrap.HTMLparser.tags.table.fixAttrib.class.list >
lib.parseFunc_RTE.nonTypoTagStdWrap.encapsLines.addAttributes.P.class >


globalMenu >
globalMenu = HMENU
globalMenu.entryLevel = 0
globalMenu.wrap = <ul id="globalMenu">|<div class="clear"></div></ul><!-- end #globalMenu  -->
globalMenu.1 = TMENU
globalMenu.1 {
	noBlur = 1
	NO.subst_elementUid = 1
	NO.before = <li id="globalMenuItem-{elementUid}">|*|<li id="globalMenuItem-{elementUid}">|*|<li id="globalMenuItem-{elementUid}" class="last">
	NO.after = </li>
	NO.stdWrap.htmlSpecialChars = 1
	NO.ATagParams.stdWrap.cObject = COA
	NO.ATagParams.stdWrap.cObject.10 = IMG_RESOURCE
	NO.ATagParams.stdWrap.cObject.10 {
		file = GIFBUILDER
		file {
			XY = [20.w]+10,120
			backColor = #9BB718
			transparentColor = #9BB718
			transparentColor.closest = 1
			20 = TEXT
			20 {
				text.field = nav_title // title
				text.stdWrap.case = upper
				fontSize = 32
				fontFile = {$templavoila_framework.skinPath}/fonts/LeagueGothic.otf
				fontColor = #F3FFE1
				offset = 5,35
				niceText = 1
			}
			30 < .20
			### Build hover text
			50 < .20
			50 {
				fontColor = #15261C
				offset = 5,85
			}
			60 < .50
			70 < .50
		}
		stdWrap.dataWrap = class="graphicText" style="background-image: url( | );width:{TSFE:lastImgResourceInfo|0}px"
	}
	ACT = 1
	ACT.before = <li id="globalMenuItem-{elementUid}" class="active">|*|<li id="globalMenuItem-{elementUid}" class="active">|*|<li id="globalMenuItem-{elementUid}" class="active last">
	ACT.after = </li>
	ACT.ATagParams < .NO.ATagParams
	ACT.stdWrap.htmlSpecialChars = 1
}


header >
header = COA
header.wrap = <div id="header"> | </div>

# Add the masthead for site title / logo.
header.10 = COA
header.10 {
	wrap = <div id="masthead"> | </div>

	# Add <h1> wrapped title if there's no logo
	10 = IMG_RESOURCE
	10.stdWrap.if.isFalse = {$siteLogo}
	10.stdWrap.dataWrap = <h1><a id="masthead" class="graphicText" style="width:{TSFE:lastImgResourceInfo|0}px;height:{TSFE:lastImgResourceInfo|1}px;background-image: url(|)" href="/">{$siteTitle}</a></h1>
	10 {
		file = GIFBUILDER
		file {
			XY = [20.w]+10,[20.h]+20
			backColor = #162a1f
			transparentColor = #162a1f
			transparentColor.closest = 1
			20 = TEXT
			20 {
				text = {$siteTitle}
				text.insertData = 1
				fontSize = 84
				fontFile = {$templavoila_framework.skinPath}/fonts/LeagueGothic.otf
				fontColor = #F3FFE1
				offset = 0,[20.lineHeight]
				niceText = 1
				breakWidth = 330
				maxWidth = 660
				breakSpace = 0.9
			}			
			30 < .20
			### Build hover text
			#50 < .20
			#50 {
				#fontColor = #9BB718
				#offset = 10,250
			#}
			#60 < .50
		}
	}

	# Add the logo image if one is avaialble.
	20 = IMAGE
	20.if.isTrue = {$siteLogo}
	20.file = {$siteLogo}
	20.file.maxW = 390
	20.file.maxH = 200
	20.alttext.cObject = TEXT
	20.alttext.cObject.value = {$siteTitle}
	20.alttext.cObject.insertData = 1
	20.if.isTrue = {$siteLogo}
	20.stdWrap.typolink.parameter = {$siteURL}
}

preCodeContent = COA
preCodeContent.wrap = <div id="globalMenu-wrapper"> | </div><div id="contentWrap">
preCodeContent.10 < globalMenu


### top nagivation
header.20 = COA
header.20.stdWrap.wrap = <div id="topNav"> | </div>
header.20.stdWrap.required = 1
header.20.10 = COA
header.20.10 {
	stdWrap.wrap = <div id="login">|</div>
	stdWrap.required = 1

	10 = COA
	10 {
		20 = TEXT
		20 {
			# Only show the login link if there's a valid page to link to
			if.isTrue = {$loginPID}
			if.isTrue.insertData = 1

			value = Sign In
			typolink.parameter = {$loginPID}
			typolink.additionalParams = &return_url={getIndpEnv : REQUEST_URI}
			typolink.additionalParams.insertData = 1
		}

		30 = TEXT
		30 {
			# Only show the login link if there's a valid page to link to
			if.isTrue = {$loginPID}
			if.isTrue.insertData = 1

			value = &nbsp;&#124;&nbsp;
		}

		40 = TEXT
		40 {
			# Only show the registration link if there's a valid page to link to
			if.isTrue = {$registerPID}
			if.isTrue.insertData = 1

			value = Sign Up
			typolink.parameter = {$registerPID}
			typolink.additionalParams = &tx_srfeuserregister_pi1[cmd]=create
		}
	}
}

[loginUser = *]
header.20.10.10 >
header.20.10.10 = COA_INT
header.20.10.10 {
	10 = TEXT
	10 {
		data = TSFE:fe_user|user|first_name // TSFE:fe_user|user|username
		wrap = Welcome,&nbsp; | &nbsp;&#124;&nbsp;

		# Only show the edit link if there's a valid page to link to
		typolink.if.isTrue = {$registerPID}
		typolink.if.isTrue.insertData = 1
		typolink.parameter = {$registerPID}
		typolink.additionalParams = &tx_srfeuserregister_pi1[cmd]=edit
	}

	20 = TEXT
	20 {
		value = Sign Out
		typolink.parameter.data = TSFE : id
		typolink.addQueryString = 1
		typolink.addQueryString.method = GET 
		typolink.additionalParams = &logintype=logout
	}
}
[global]

### Search box
header.20.20 = COA
header.20.20 {
	# Only show the search box if there is a valid search page.
	if.isTrue = {$searchPID}
	if.isTrue.insertData = 1

	wrap = <div id="search"> | </form></div>

	10 = TEXT
	10 {
		typolink.parameter = {$searchPID}
		typolink.returnLast = url
		wrap = <form id="siteSearch" name="site_search" method="post" action="|">
	}

	20 = HTML
	20.value (
		<label class="outOfSight" for="siteSearchInput">Search the Site</label>
		<input id="siteSearchInput" type="text" value="Search the Site" name="tx_indexedsearch[sword]"/>
		<input id="siteSearchSubmit" type="image" class="searchsubmit" src="{$templavoila_framework.skinPath}css/images/search.png" value="search" name="siteSearchSubmit" />
	)
	20.insertData = 1
}

#header.40 < globalMenu


footer >
footer = COA
footer {
	wrap = <div id="footer" class="clear"> | </div>
	10 = TEXT
	10.data = date:U
	10.strftime = %Y
	10.wrap = <p id="footerCopyright">&copy;&nbsp; | &nbsp;{$copyright}, {$contact}</p>

	20 = TEXT
	20.value = We are a Web-Empowered Church.
	20.typolink.parameter = http://www.webempoweredchurch.org/
	20.typolink.ATagParams = id="footerHomeLink"
	20.if.isTrue = {$enableWECFooter}
}

additionalDocHeadCode = HTML
additionalDocHeadCode.value (

	<!--[if IE 6]>
		<link rel="stylesheet" type="text/css" href="{$templavoila_framework.skinPath}css/ie6.css" />
	<![endif]-->
	<!--[if IE]>
	<style type="text/css">
		#feature {
			border-image: url({$templavoila_framework.skinPath}css/images/border.png) 16 stretch;
        		behavior: url({$templavoila_framework.skinPath}css/htc/PIE.htc);
		}
	</style>
	<![endif]-->
)

# "Menu of subpages to these pages (with abstract)"
tt_content.menu.20.4 {
	special = directory
	wrap >
	1 >
	1 = TMENU
	1 {
		target = {$PAGE_TARGET}
		wrap = <div class="sectionMenuWrapper"><div class="sectionMenu">|</div><div class="clearOnly"></div></div>
		NO {
			allWrap = <div class="menuItem"> | </div>
			before.cObject = COA
			before.cObject {
				10 = IMAGE
				10.if.isTrue.field = media
				10.file.import = uploads/media/
				10.file.import.field = media
				10.file.import.listNum = 0
				10.file.width = 108m
				10.alttext.field = title
				10.params = align="left"
				10.stdWrap.typolink.parameter.field = uid
			}

			after.cObject = COA
			after.cObject {
				30 = TEXT
				30.field = abstract
				30.wrap = <p>|</p></div>
			}

			stdWrap.override.field = subtitle
			linkWrap = <div class="wrapper"><h3>|</h3>
			noBlur = 1
		}
	}
}
