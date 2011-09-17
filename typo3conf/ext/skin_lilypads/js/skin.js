// Skin Specific JS goes here. The jQuery library has already been loaded by the core templates. So if you use jQuery, you are ready to go.
jQuery(document).ready(function() {
	// Clear default search text on click.
	if (jQuery('#siteSearchInput').length > 0) {
		defaultSearchValue = jQuery('#siteSearchInput')[0].value;
		jQuery('#siteSearchInput').focus(function() {
			if (jQuery(this)[0].value === defaultSearchValue) {
				jQuery(this)[0].value = '';
			}
		});

		jQuery('#siteSearchInput').blur(function() {
			if (jQuery(this)[0].value === '') {
				jQuery(this)[0].value = defaultSearchValue;
			}
		});
	}

	// Align the site title and feature area
	if (jQuery('#feature').length > 0) {
		middleOfFeature = jQuery('#feature').height() / 2;
		middleOfTitle = jQuery('#masthead h1').height() / 2;
		jQuery('#masthead h1').css('margin-top', middleOfFeature - middleOfTitle);
	}
	
});
