# Last updated 2010-01-05 20:17 by mm_forum backend module.
plugin {
	tx_mmforum {
		realUrl_specialLinks = 1
		storagePID = 110
		userPID = 31
		userGroup = 2
		adminGroup = 29
		path_img = EXT:mm_forum/res/img/default/
		img_border = 0
		path_smilie = EXT:mm_forum/res/smilies/
		site_name = 
		team_name = 
		support_mail = 
		mailer_mail = 
		dateFormat = 
		indexingPassword = 21232f297a57a5a743894a0e4a801fc3
		indexCount = 50
		threadsPerPage = 30
		postsPerPage = 10
		boardPID = 73
		userProfilePID = 
		rssPID = 
		pmPID = 
		notifyMail_sender = 
		boardName = 
		displayRealName = 1
		postparser {
			insertButton_path = EXT:mm_forum/res/img/default/editor_icons/
		}
		disableRootline = 0
		topic_hotPosts = 10
		user_hotPosts = 5000
		prefixes = HowTo,Info,Test
		spamblock_interval = 20
		signatureLimit = 6
		pmBlocktime = 30
		sword_minLength = 2
		resultsPerPage = 10
		path_template = EXT:mm_forum/res/tmpl/
		path_altTemplate = EXT:wec_styles/extensions/mm_forum/tmpl/
		attachment_enable = 1
		attachment_allow = *
		attachment_deny = php,php3
		attachment_filesize = 1048576
		attachment_preview = 1
		polls_enable = 1
		enableShadows = 1
		signatureBBCodes = 0
		enableRanks = 0
		moderatedBoard = 0
		informal = 1
		useCaptcha = 1
		userNameField = username
		topicIconMode = modern
		attachment_count = 1
		cron_htmlemail = 0
		cron_notifyPublish_group = 
		cron_lang = en
		cron_sitetitle = mm_forum
		cron_postqueue_link = http://www.typo3.net
		cron_notifyPublishSender = "mm_forum" <mm_forum@typo3.net>
		cron_pathTmpl = typo3conf/ext/mm_forum/res/tmpl/cron/
		cron_pm_readlink = http://www.typo3.net/?id=messages&tx_mmforum_pi3[action]=message_read&tx_mmforum_pi3[messid]=%s
		cron_pm_realUID = 1
		cron_index_count = 10
		cron_verbose = all
		requiredFields = name,email
		style = wec
		style_path = typo3conf/ext/wec_styles/extensions/mm_forum/tmpl/wec
		moderatorGroup = 28
	}
}
