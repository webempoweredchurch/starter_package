tt_content.slideshow = COA
tt_content.slideshow {
	# Show the standard content element header.
	5 = < lib.stdheader

	# User registers to do some math and store the results.
	7 = CASE
	7 {
		key.data = t3datastructure : pi_flexform->resolution
		widescreen = LOAD_REGISTER
		widescreen {
			slideshowWidth.data = register : containerWidth // {$styles.content.imgtext.maxW}
			tempSlideshowHeight.data = register : containerWidth // {$styles.content.imgtext.maxW}
			tempSlideshowHeight.wrap = (|*.5625)
			slideshowHeight.data = register : tempSlideshowHeight
			slideshowHeight.prioriCalc = intval
		}
		standard = LOAD_REGISTER
		standard {
			slideshowWidth.data = register : containerWidth // {$styles.content.imgtext.maxW}
			tempSlideshowHeight.data = register:containerWidth // {$styles.content.imgtext.maxW}
			tempSlideshowHeight.wrap = (|*.75)
			slideshowHeight.data = register : tempSlideshowHeight
			slideshowHeight.prioriCalc = intval
		}
	 	custom = LOAD_REGISTER
		custom {
			slideshowWidth.data = t3datastructure : pi_flexform->width // register : containerWidth // {$styles.content.imgtext.maxW}
			slideshowHeight.data = t3datastructure : pi_flexform->height
		}
	}

	8 = CASE
	8 {
		key.data = t3datastructure : pi_flexform->thumbnails
		none = LOAD_REGISTER
		none {
			thumbnailSize = 0
		}
		small = LOAD_REGISTER
		small {
			thumbnailSize = 25
		}
		medium = LOAD_REGISTER
		medium {
			thumbnailSize = 50
		}
		large = LOAD_REGISTER
		large {
			thumbnailSize = 100
		}
	}

	9 = LOAD_REGISTER
	9 {
		tempDisplayTime.data = t3datastructure : pi_flexform->displayTime
		tempDisplayTime.wrap = |*1000
		displayTime.data = register : tempDisplayTime
		displayTime.prioriCalc = intval
	}

	# Include the necessary Javascript and CSS libraries.
	10 = INCLUDEJSLIBS
	10 {
		tf_jquery = EXT:wec_contentelements/slideshow/res/jquery.js
		jquery_cycle = EXT:wec_contentelements/slideshow/res/jquery.cycle.all.min.js
	}
	
	11 = HEADERDATA
	11.value (
		<style>
			.rotator { overflow: hidden; }
			#nav { margin: 0; padding: 0; }
			#nav li { float: left; margin: 8px 8px 8px 0; list-style: none }
			#nav a { padding: 3px; display: block; border: 1px solid #ccc; }
			#nav a.activeSlide { background: #88f }
			#nav a:focus { outline: none; }
			#nav img { border: none; display: block }
		</style>
	)

	# Include the Javascript settings for the gallery.
	20 = COA
	20 {
		wrap = <script type="text/javascript">|</script>
		10 = TEXT
		10.value (
				jQuery(document).ready(function() {
		)
		
		15 = TEXT
		15.value (
					jQuery('#rotator{field:uid}').cycle(
		)
		15.insertData = 1

		16 = TEXT
		16.value = {

		20 = TEXT
		20.value (
			fx: '{ t3datastructure : pi_flexform->transition }',
			timeout: '{ register : displayTime }',
			fit: 1,
			pause: 1,
		)
		20.insertData = 1

		25 = TEXT
		25.value (
			pager: '#nav',
		)
		25.if.isTrue.data = t3datastructure : pi_flexform->thumbnails

		30 = TEXT
		30.value (
			pagerAnchorBuilder: function(idx, slide) { 
		        // return selector string for existing anchor 
		        return '#nav li:eq(' + idx + ') a'; 
		    }
		)
		30.if.isTrue.data = t3datastructure : pi_flexform->thumbnails

		100 = TEXT
		100.value (
					});
				});
		)
	}

	# Draw each image in the slideshow.
	40 = FFSECTION
	40.rootPath = t3datastructure : pi_flexform->images/el
	40 {
		stdWrap.wrap = <div id="rotator{field:uid}" class="rotator" style="height: {register : slideshowHeight}px;"> | </div>
		stdWrap.insertData = 1

		10 = IMAGE
		10.file.import.data = flexformSection : image/el/file
		10.file.import.wrap = uploads/tx_weccontentelements/slideshow/
		10.file.width {
			data = register : slideshowWidth
			wrap = |c
		}
		10.file.height {
			data = register : slideshowHeight
			wrap = |c
		}
		10.stdWrap.typolink.parameter.data = flexformSection : image/el/link
	}

	# Draw each image in the slideshow.
	50 = FFSECTION
	50.rootPath = t3datastructure : pi_flexform->images/el
	50 {
		if.isTrue.data = register : thumbnailSize
		stdWrap.dataWrap = <ul id="nav" style="width: {register : slideshowWidth}px"> | </ul>
		
		10 = IMAGE
		10.file.import.data = flexformSection : image/el/file
		10.file.import.wrap = uploads/tx_weccontentelements/slideshow/
		10.file.width {
			data = register : thumbnailSize
			wrap = |c
		}
		10.file.height {
			data = register : thumbnailSize
			wrap = |c
		}
		10.wrap = <li><a href="#">|</a></li>
	}
	
	60 = HTML
	60.value = <div style="clear: both"></div>
}
