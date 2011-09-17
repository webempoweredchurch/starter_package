tt_content.filedownload = COA
tt_content.filedownload {
		10 = < lib.stdheader

		20 = HEADERDATA
		20.value (
				<style type="text/css">
						.fileDownload {
								border-top: 1px solid #ccc;
								margin-bottom: 10px;
						}
						.fileDownloadColumn1 {
								width: 70%;
								padding: 10px;
								float: left;
						}
						.fileDownloadColumn2 {
								width: 20%;
								border-left: 1px solid #ccc;
								float: right;
								padding: 10px;
						}

						ul.fileDownloadFiles {
								list-style: none;
								margin-left: 0;
						}

						.clearOnly {
								clear: both;
								height: 0px;
								margin: 0;
								padding: 0;
								overflow: hidden;
								line-height: 0;
								font-size: 0;
						}
				</style>
	)

	30 = COA
	30 {
		wrap = <div class="fileDownload">|</div><div class="clearOnly"></div>

		10 = COA
		10 {
			wrap = <div class="fileDownloadColumn1">|</div>

			10 = TEXT
			10.data = t3datastructure : pi_flexform->title
			10.wrap = <h4 class="fileDownloadTitle">|</h4>

			20 = TEXT
			20.data = t3datastructure : pi_flexform->description
		}

		20 = COA
		20 {
			stdWrap.if.isTrue.data = t3datastructure : pi_flexform->files
			wrap = <div class="fileDownloadColumn2">|</div>

			10 = TEXT
			10.value = Downloads
			10.wrap = <h5>|</h5>

			20 = TEXT
			20.wrap = <ul class="fileDownloadFiles">|</ul>
			20.data = t3datastructure : pi_flexform->files
			20.split {
				token = ,
				cObjNum = 1

				1 {
					10 = TEXT
					10.wrap = <li>|</li>
					10.current = 1
					10.typolink.parameter = uploads/tx_weccontentelements/filedownload/{current : 1}
					10.typolink.parameter.insertData = 1
				}
			}
		}
	}
}

