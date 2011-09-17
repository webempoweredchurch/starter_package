tt_content.youtube = COA
tt_content.youtube {
	10 = < lib.stdheader
	
	20 = TEXT
	20.value (
		<object width="{t3datastructure : pi_flexform->width}" height="{t3datastructure : pi_flexform->height}">
			<param name="movie" value="http://www.youtube-nocookie.com/v/{t3datastructure : pi_flexform->youtubeID}"></param>
			<param name="allowFullScreen" value="true"></param>
			<param name="allowscriptaccess" value="always"></param>
			<embed src="http://www.youtube-nocookie.com/v/{t3datastructure : pi_flexform->youtubeID}" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="{t3datastructure : pi_flexform->width}" height="{t3datastructure : pi_flexform->height}"></embed>
		</object>
	)
	20.insertData = 1
}