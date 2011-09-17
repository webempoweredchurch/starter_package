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
});

jQuery(window).load(function() {
	// Ensure that all content areas are equal height
	jQuery('#pageWrap').each(function(){
		var maxHeight = 0;
		jQuery(this).children('div[id^=contentBlock], div[id^=generatedContent]').each(function() {
			if (jQuery(this).height() > maxHeight) {
				maxHeight = jQuery(this).height();
			}
		});
		if (maxHeight) {
			// Only update the generated content height since its less likely to have DOM changes after load.
			// @todo Need a more robust way to set the height for all columns while still allowing resize after DOM changes.
			jQuery(this).children('div[id^=generatedContent]').height(maxHeight + 'px');
		}
	});
});
