// Skin Specific JS goes here. The jQuery library has already been loaded by the core templates. So if you use jQuery, you are ready to go.
jQuery(document).ready(function() {
	originalHeaderHeight = jQuery('#header').height();

	// If we're on the home page, hide the submenu and pull content up.
	if (jQuery('ul#globalMenu > li.active').length == 0) {
		jQuery('#header').height(originalHeaderHeight - 34);
	}

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

	// Set up hoverIntent for global menu
	var config = {
		interval: 200,
		over: function () {
			// Remove an existing active classes and flag the new one
			jQuery('ul#globalMenu > li.active').removeClass('active');
			jQuery(this).addClass('active');

			// Increase the header height so that second level menu is visible on blue background
			jQuery('#header').height(originalHeaderHeight);

		},
		out: function () {
		}
	};
	jQuery("ul#globalMenu > li").hoverIntent( config );
});
