siteLogo = {$templavoila_framework.skinPath}css/images/logo.png

# cat=Site Constants/general//; type=user[EXT:templavoila_framework/class.tx_templavoilaframework_pagelink.php:&tx_templavoilaframework_pagelink->main]; label=Search Page ID: If not page ID is provided, the search box will not be shown.
searchPID =

# cat=Site Constants/general//; type=user[EXT:templavoila_framework/class.tx_templavoilaframework_pagelink.php:&tx_templavoilaframework_pagelink->main]; label=Login Page ID: If no page ID is provided, the login link will not be shown.
loginPID =

# cat=Site Constants/general//; type=user[EXT:templavoila_framework/class.tx_templavoilaframework_pagelink.php:&tx_templavoilaframework_pagelink->main]; label=Registration Page ID: If no page ID is provided, the registration link will not be shown.
registerPID =

# cat=Site Constants/general//; type=boolean; label=Enable WEC Footer?: Enables footer text that indicates involvement with the Web-Empowered Church.
enableWECFooter = 1

##############################################################
# These are various values needed to render the page and modules
##############################################################
autoMainHeadlineDefault = 1
featureBleedDefault = 0
featureLeftPadding = 0
featureRightPadding = 0
generatedContent-1.source = 8
generatedContent-2.source = 7
localMenu.root = 1
localMenu.title = 
globalGutter = 20
columnRuleDefault = 0
moduleBodyTopBuffer = 15
moduleBodyBottomBuffer = 10
moduleBodyLeftBuffer = 10
moduleBodyRightBuffer = 10
moduleBodyWrapBorderWidth = 1
showModuleTitleDefault = 1

### Enter zero on the following if you do not want a footer on your module.
### Also include any top/bottom border or padding in the height parameter
moduleFooterTotalHeight = 20

##############################################################
# These are widths of the containers for each core template.
# These are only the raw widths and do not contain padding, border
# or margins. They are used in calcuating the widths of modules,
# columns and maximum image widths.
##############################################################

containerWidth {
	f1a {
		feature = 880
		contentBlock-1 = 880
	}
	f1b {
		feature = 880
		contentBlock-1 = 880
	}
	f1c {
		feature = 880
		contentBlock-1 = 880
	}
	f1d {
		feature = 880
		contentBlock-1 = 674
		generatedContent-1 = 186
	}
	f1e {
		feature = 880
		contentBlock-1 = 674
		generatedContent-2 = 189
	}
	f1f {
		feature = 880
		contentBlock-1 = 468
		generatedContent-1 = 186
		generatedContent-2 = 189
	}
	f2a {
		feature = 880
		contentBlock-1 = 674
		contentBlock-2 = 189
	}
	f2b {
		feature = 880
		contentBlock-1 = 674
		contentBlock-2 = 189
	}
	f2c {
		feature = 880
		contentBlock-1 = 674
		contentBlock-2 = 189
	}
	f2d {
		feature = 880
		contentBlock-1 = 468
		contentBlock-2 = 189
		generatedContent-1 = 186
	}
	f2e {
		contentBlock-1 = 468
		contentBlock-2 = 189
		generatedContent-1 = 186
	}
	f3a	{
		feature = 880
		contentBlock-1 = 468
		contentBlock-2 = 189
		contentBlock-3 = 189
	}
	f3b	{
		feature = 880
		contentBlock-1 = 468
		contentBlock-2 = 189
		contentBlock-3 = 189
	}
	f3c	{
		feature = 880
		contentBlock-1 = 468
		contentBlock-2 = 189
		contentBlock-3 = 189
	}
	f3d	{
		feature = 880
		contentBlock-1 = 468
		contentBlock-2 = 189
		contentBlock-3 = 189
	}
}