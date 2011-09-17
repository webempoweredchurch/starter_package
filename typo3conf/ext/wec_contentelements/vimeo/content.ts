tt_content.vimeo = COA
tt_content.vimeo {
	10 = < lib.stdheader
	
	20 = TEXT
	20.value (
		<object width="{t3datastructure : pi_flexform->width}" height="{t3datastructure : pi_flexform->height}">
			<param name="allowfullscreen" value="true" />
			<param name="allowscriptaccess" value="always" />
			<param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id={t3datastructure : pi_flexform->vimeoID}&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1" />
			<embed src="http://vimeo.com/moogaloop.swf?clip_id={t3datastructure : pi_flexform->vimeoID}&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" width="{t3datastructure : pi_flexform->width}" height="{t3datastructure : pi_flexform->height}"></embed>
		</object>
	)
	20.insertData = 1
}