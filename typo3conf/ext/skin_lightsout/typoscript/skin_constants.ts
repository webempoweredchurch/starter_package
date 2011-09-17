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
		feature = 940
		contentBlock-1 = 940
	}
	f1b {
		feature = 940
		contentBlock-1 = 940
	}
	f1c {
		feature = 940
		contentBlock-1 = 940
	}
	f1d {
		feature = 940
		contentBlock-1 = 720
		generatedContent-1 = 160
	}
	f1e {
		feature = 940
		contentBlock-1 = 720
		generatedContent-2 = 190
	}
	f1f {
		feature = 940
		contentBlock-1 = 500
		generatedContent-1 = 160
		generatedContent-2 = 190
	}
	f2a {
		feature = 940
		contentBlock-1 = 720
		contentBlock-2 = 190
	}
	f2b {
		feature = 940
		contentBlock-1 = 720
		contentBlock-2 = 190
	}
	f2c {
		feature = 940
		contentBlock-1 = 720
		contentBlock-2 = 190
	}
	f2d {
		feature = 940
		contentBlock-1 = 500
		contentBlock-2 = 190
		generatedContent-1 = 190
	}
	f2e {
		feature = 940
		contentBlock-1 = 500
		contentBlock-2 = 190
		generatedContent-2 = 190
	}
	f3a	{
		feature = 940
		contentBlock-1 = 500
		contentBlock-2 = 190
		contentBlock-3 = 190
	}
	f3b	{
		feature = 940
		contentBlock-1 = 500
		contentBlock-2 = 190
		contentBlock-3 = 190
	}
	f3c	{
		feature = 940
		contentBlock-1 = 500
		contentBlock-2 = 190
		contentBlock-3 = 190
	}
	f3d	{
		feature = 940
		contentBlock-1 = 500
		contentBlock-2 = 190
		contentBlock-3 = 190
	}
}