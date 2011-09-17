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

##############################################################
# This is TypoScript used to modify the core templates to
# display this skin. Rewrite the header, footer, pre code and 
# post code libraries and more when needed to change structure
##############################################################

preCodeHeader = HTML
preCodeHeader.value = <div id="pageWrap">
postCodeHeader >

preCodeFeature = HTML
preCodeFeature.value = <div id="feature-top"></div>
postCodeFeature = HTML
postCodeFeature.value = <div id="feature-bottom"></div>

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

preCodeFooter >
postCodeFooter = HTML
postCodeFooter.value (

	</div>
	<!-- end #pageWrap  -->
)

### Lets table classes be added in the RTE
lib.parseFunc_RTE.externalBlocks.table.stdWrap.HTMLparser.tags.table.fixAttrib.class.list >
lib.parseFunc_RTE.nonTypoTagStdWrap.encapsLines.addAttributes.P.class >

globalMenu >
globalMenu = HMENU
globalMenu.entryLevel = 0
# the first <div class="clear"></div> fix the header missing issue for IE7 and the second <div class="clear"></div> fix the F1d template float issue when the main menu goes to multi lines.
globalMenu.wrap = <div class="clear"></div><ul id="globalMenu">|</ul><div class="clear"></div><!-- end #globalMenu  -->
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
			XY = [20.w]+10,60
			backColor = #d0d0d0
			transparentColor = #d0d0d0
			transparentColor.closest = 1
			20 = TEXT
			20 {
				text.field = nav_title // title
				fontSize = 20
				fontFile = {$templavoila_framework.skinPath}/fonts/DESYREL.ttf
				fontColor = #555555
				offset = 7,18
				niceText = 1
			}
			30 < .20
			### Build hover text
			50 < .20
			50 {
				fontColor = #222222
				offset = 7,50
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
	#10 = TEXT
	#10.if.isFalse = {$siteLogo}
	#10.value = {$siteTitle}
	#10.htmlSpecialChars = 1
	#10.typolink.parameter = {$siteURL}
	#10.wrap = <h1> | </h1>

	10 = IMG_RESOURCE
	10.stdWrap.if.isFalse = {$siteLogo}
	10.stdWrap.dataWrap = <h1><a id="masthead" class="graphicText" style="width:{TSFE:lastImgResourceInfo|0}px;height:{TSFE:lastImgResourceInfo|1}px;background-image: url(|)" href="/">{$siteTitle}</a></h1>
	10 {
		file = GIFBUILDER
		file {
			XY = [20.w]+10,[20.h]+10
			backColor = #f7f7f7
			transparentColor = #f7f7f7
			transparentColor.closest = 1
			20 = TEXT
			20 {
				text = {$siteTitle}
				text.insertData = 1
				fontSize = 34
				fontFile = {$templavoila_framework.skinPath}fonts/DESYREL.ttf
				fontColor = #555555
				offset = 4,35
				niceText = 1
				breakWidth = 940
				breakSpace = 1
			}			30 < .20
			### Build hover text
			#50 < .20
			#50 {
				#fontColor = #333333
				#offset = 4,100
			#}
			#60 < .50
			#70 < .50
		}
	}

	# Add the logo image if one is avaialble.
	20 = IMAGE
	20.if.isTrue = {$siteLogo}
	20.file = {$siteLogo}
	20.file.maxW = 450
	20.file.maxH = 200
	20.alttext.cObject = TEXT
	20.alttext.cObject.value = {$siteTitle}
	20.alttext.cObject.insertData = 1
	20.if.isTrue = {$siteLogo}
	20.stdWrap.typolink.parameter = {$siteURL}
}

### top nagivation
header.20 = COA
header.20.wrap = <div id="topNav"> | </div>
header.20.10 = COA
header.20.10 {
	wrap = <div id="login">|</div>

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
header.20.10.10 = COA
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

header.40 < globalMenu

footer >

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




#handwriting font for local menu

tt_content.localmenu {
	
	20 = HMENU
	20 {
		entryLevel = 1
		1 = TMENU
		1 {
			noBlur = 1
			NO.subst_elementUid = 1
			NO.stdWrap.htmlSpecialChars = 1
			NO.ATagParams.stdWrap.cObject = COA
			NO.ATagParams.stdWrap.cObject.10 = IMG_RESOURCE
			NO.ATagParams.stdWrap.cObject.10 {
				file = GIFBUILDER
				file {
					XY = 170,[20.h]+[30.h]+[40.h]+15
					backColor = #ededed
					transparentColor = #ededed
					transparentColor.closest = 1
					20 = TEXT
					20 {
						text.field = nav_title // title
						text.listNum = 0
						fontSize = 15
						fontFile = {$templavoila_framework.skinPath}/fonts/DESYREL.ttf
						fontColor = #444444
						breakWidth = 170
						breakSpace = 1
						offset = 5,18
						niceText = 1
					}
					25 < .20
					
					30 < .20
					30.text.listNum = 1
					30.offset = 0,[20.lineHeight]+18
					35 < .30
					
					40 < .20
					40.text.listNum = 2
					40.offset = 0,[20.h]+[30.lineHeight]+18
					45 < .40
					
					50 < .20
					50.text.listNum = 3
					50.offset = 0,[20.h]+[30.h]+[40.lineHeight]+18
					55 < .50

				}
				stdWrap.dataWrap = style="background-image: url( | );width:{TSFE:lastImgResourceInfo|0}px;line-height:1000px; height:{TSFE:lastImgResourceInfo|1}px;display:block;"
			}
			ACT = 1
			ACT.ATagParams < .NO.ATagParams
			ACT.stdWrap.htmlSpecialChars = 1
			CUR = 1
			CUR.ATagParams < .NO.ATagParams
			CUR.stdWrap.htmlSpecialChars = 1
		}
		
		## Comment out the following if a second tier is not needed on the global menu
		2 = TMENU
		2 {
			noBlur = 1
			NO.subst_elementUid = 1
			NO.stdWrap.htmlSpecialChars = 1
			NO.ATagParams.stdWrap.cObject = COA
			NO.ATagParams.stdWrap.cObject.10 = IMG_RESOURCE
			NO.ATagParams.stdWrap.cObject.10 {
				file = GIFBUILDER
				file {
					XY = 170,[20.h]+[30.h]+[40.h]+15
					backColor = #d0d0d0
					transparentColor = #d0d0d0
					transparentColor.closest = 1
					20 = TEXT
					20 {
						text.field = nav_title // title
						text.listNum = 0
						fontSize = 15
						fontFile = {$templavoila_framework.skinPath}/fonts/DESYREL.ttf
						fontColor = #444444
						breakWidth = 170
						breakSpace = 1
						offset = 5,18
						niceText = 1
					}
					25 < .20
					
					30 < .20
					30.text.listNum = 1
					30.offset = 0,[20.lineHeight]+18
					35 < .30
					
					40 < .20
					40.text.listNum = 2
					40.offset = 0,[20.h]+[30.lineHeight]+18
					45 < .40
					
					50 < .20
					50.text.listNum = 3
					50.offset = 0,[20.h]+[30.h]+[40.lineHeight]+18
					55 < .50

				}
				stdWrap.dataWrap = style="background-image: url( | );width:{TSFE:lastImgResourceInfo|0}px;line-height:1000px; height:{TSFE:lastImgResourceInfo|1}px;display:block;"
			}
			ACT = 1
			ACT.ATagParams < .NO.ATagParams
			ACT.stdWrap.htmlSpecialChars = 1
			CUR = 1
			CUR.ATagParams < .NO.ATagParams
			CUR.stdWrap.htmlSpecialChars = 1
		}

		3 = TMENU
		3 {
			noBlur = 1
			NO.subst_elementUid = 1
			NO.stdWrap.htmlSpecialChars = 1
			NO.ATagParams.stdWrap.cObject = COA
			NO.ATagParams.stdWrap.cObject.10 = IMG_RESOURCE
			NO.ATagParams.stdWrap.cObject.10 {
				file = GIFBUILDER
				file {
					XY = 170,[20.h]+[30.h]+[40.h]+15
					backColor = #d0d0d0
					transparentColor = #d0d0d0
					transparentColor.closest = 1
					20 = TEXT
					20 {
						text.field = nav_title // title
						text.listNum = 0
						fontSize = 15
						fontFile = {$templavoila_framework.skinPath}/fonts/DESYREL.ttf
						fontColor = #444444
						breakWidth = 170
						breakSpace = 1
						offset = 5,18
						niceText = 1
					}
					25 < .20
					
					30 < .20
					30.text.listNum = 1
					30.offset = 0,[20.lineHeight]+18
					35 < .30
					
					40 < .20
					40.text.listNum = 2
					40.offset = 0,[20.h]+[30.lineHeight]+18
					45 < .40
					
					50 < .20
					50.text.listNum = 3
					50.offset = 0,[20.h]+[30.h]+[40.lineHeight]+18
					55 < .50

				}
				stdWrap.dataWrap = style="background-image: url( | );width:{TSFE:lastImgResourceInfo|0}px;line-height:1000px; height:{TSFE:lastImgResourceInfo|1}px;display:block;"
			}
			ACT = 1
			ACT.ATagParams < .NO.ATagParams
			ACT.stdWrap.htmlSpecialChars = 1
			CUR = 1
			CUR.ATagParams < .NO.ATagParams
			CUR.stdWrap.htmlSpecialChars = 1
		}

		# this is for the 5th level menu

		4 = TMENU
		4 {
			noBlur = 1
			NO.subst_elementUid = 1
			NO.stdWrap.htmlSpecialChars = 1
			NO.ATagParams.stdWrap.cObject = COA
			NO.ATagParams.stdWrap.cObject.10 = IMG_RESOURCE
			NO.ATagParams.stdWrap.cObject.10 {
				file = GIFBUILDER
				file {
					XY = 170,[20.h]+[30.h]+[40.h]+15
					backColor = #d0d0d0
					transparentColor = #d0d0d0
					transparentColor.closest = 1
					20 = TEXT
					20 {
						text.field = nav_title // title
						text.listNum = 0
						fontSize = 15
						fontFile = {$templavoila_framework.skinPath}/fonts/DESYREL.ttf
						fontColor = #444444
						breakWidth = 170
						breakSpace = 1
						offset = 5,18
						niceText = 1
					}
					25 < .20
					
					30 < .20
					30.text.listNum = 1
					30.offset = 0,[20.lineHeight]+18
					35 < .30
					
					40 < .20
					40.text.listNum = 2
					40.offset = 0,[20.h]+[30.lineHeight]+18
					45 < .40
					
					50 < .20
					50.text.listNum = 3
					50.offset = 0,[20.h]+[30.h]+[40.lineHeight]+18
					55 < .50

				}
				stdWrap.dataWrap = style="background-image: url( | );width:{TSFE:lastImgResourceInfo|0}px;line-height:1000px; height:{TSFE:lastImgResourceInfo|1}px;display:block;"
			}
			ACT = 1
			ACT.ATagParams < .NO.ATagParams
			ACT.stdWrap.htmlSpecialChars = 1
			CUR = 1
			CUR.ATagParams < .NO.ATagParams
			CUR.stdWrap.htmlSpecialChars = 1
		}
	}
}
