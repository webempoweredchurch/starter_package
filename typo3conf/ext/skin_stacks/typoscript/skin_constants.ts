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
		feature = 820
		contentBlock-1 = 820
	}
	f1b {
		feature = 820
		contentBlock-1 = 820
	}
	f1c {
		feature = 820
		contentBlock-1 = 820
	}
	f1d {
		feature = 820
		contentBlock-1 = 615
		generatedContent-1 = 180
	}
	f1e {
		feature = 820
		contentBlock-1 = 615
		generatedContent-2 = 180
	}
	f1f {
		feature = 820
		contentBlock-1 = 410
		generatedContent-1 = 180
		generatedContent-2 = 180
	}
	f2a {
		feature = 820
		contentBlock-1 = 615
		contentBlock-2 = 180
	}
	f2b {
		feature = 820
		contentBlock-1 = 615
		contentBlock-2 = 180
	}
	f2c {
		feature = 820
		contentBlock-1 = 615
		contentBlock-2 = 180
	}
	f2d {
		feature = 820
		contentBlock-1 = 410
		contentBlock-2 = 180
		generatedContent-1 = 180
	}
	f2e {
		feature = 820
		contentBlock-1 = 410
		contentBlock-2 = 180
		generatedContent-2 = 180
	}
	f3a	{
		feature = 820
		contentBlock-1 = 410
		contentBlock-2 = 180
		contentBlock-3 = 180
	}
	f3b	{
		feature = 820
		contentBlock-1 = 410
		contentBlock-2 = 180
		contentBlock-3 = 180
	}
	f3c	{
		feature = 820
		contentBlock-1 = 410
		contentBlock-2 = 180
		contentBlock-3 = 180
	}
	f3d	{
		feature = 820
		contentBlock-1 = 410
		contentBlock-2 = 180
		contentBlock-3 = 180
	}
}
