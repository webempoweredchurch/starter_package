tt_content.localmenu = COA
tt_content.localmenu {
	10 = < lib.stdheader
	
	20 = HMENU
	20 {
		entryLevel = 1
		wrap = <div id="localMenu-top"></div><div id="localMenu"><ul>|</ul></div><div id="localMenu-bottom"></div><!-- end #localMenu  -->

		1 = TMENU
		1 {
			noBlur = 1
			NO.wrapItemAndSub = <li>|</li>
			ACT = 1
			ACT.wrapItemAndSub = <li class="active">|</li>
			CUR = 1
			CUR.wrapItemAndSub = <li class="current">|</li>
		}

		## Comment out the following if a second tier is not needed on the global menu
		2 = TMENU
		2 {
			noBlur = 1
			wrap = <ul>|</ul>
			NO.wrapItemAndSub = <li>|</li>
			ACT = 1
			ACT.wrapItemAndSub = <li class="active">|</li>
			CUR = 1
			CUR.wrapItemAndSub = <li class="current">|</li>
		}

		3 = TMENU
		3 {
			noBlur = 1
			wrap = <ul>|</ul>
			NO.wrapItemAndSub = <li>|</li>
			ACT = 1
			ACT.wrapItemAndSub = <li class="active">|</li>
			CUR = 1
			CUR.wrapItemAndSub = <li class="current">|</li>
		}


		# this is for the 5th level menu

		4 = TMENU
		4 {
			noBlur = 1
			wrap = <ul>|</ul>
			NO.wrapItemAndSub = <li>|</li>
			ACT = 1
			ACT.wrapItemAndSub = <li class="active">|</li>
			CUR = 1
			CUR.wrapItemAndSub = <li class="current">|</li>
		}
	}
}