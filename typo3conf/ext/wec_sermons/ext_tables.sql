#
# Table structure for table 'tx_wecsermons_resources'
#
CREATE TABLE tx_wecsermons_resources (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l18n_parent int(11) DEFAULT '0' NOT NULL,
	l18n_diffsource mediumblob NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	fe_group int(11) DEFAULT '0' NOT NULL,
	starttime int(11) DEFAULT '0' NOT NULL,
	endtime int(11) DEFAULT '0' NOT NULL,
	description text NOT NULL,
	title tinytext NOT NULL,
	subtitle tinytext NOT NULL,
	graphic blob NOT NULL,
	alttitle tinytext NOT NULL,
	type int(11) DEFAULT '0' NOT NULL,
	file blob NOT NULL,
	webaddress1 tinytext NOT NULL,
	webaddress2 tinytext NOT NULL,
	webaddress3 tinytext NOT NULL,
	rendered_record blob NOT NULL,
	summary tinytext NOT NULL,
	islinked tinyint(3) DEFAULT '1' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);
#
# Table structure for table 'tx_wecsermons_resource_types'
#
CREATE TABLE tx_wecsermons_resource_types (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	sorting int(10) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l18n_parent int(11) DEFAULT '0' NOT NULL,
	l18n_diffsource mediumblob NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	fe_group int(11) DEFAULT '0' NOT NULL,
	type tinyint(4) DEFAULT '0' NOT NULL,
	title tinytext NOT NULL,
	description text NOT NULL,
	icon blob NOT NULL,
	alttitle tinytext NOT NULL,
	marker_name tinytext NOT NULL,
	template_name tinytext NOT NULL,
	querystring_param blob NOT NULL,
	mime_type tinytext NOT NULL,
	typoscript_object_name tinytext NOT NULL,
	avail_fields tinytext NOT NULL,
	rendering_page int(11) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);
#
# Table structure for table 'tx_wecsermons_sermons_resources_rel'
#
CREATE TABLE tx_wecsermons_sermons_resources_rel (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sermonid int(11) DEFAULT '0' NOT NULL,
	resourceid int(11) DEFAULT '0' NOT NULL,
	sorting int(10) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY sermon (sermonid),
	KEY resource (resourceid)
);
#
# Table structure for table 'tx_wecsermons_series_resources_rel'
#
CREATE TABLE tx_wecsermons_series_resources_rel (
        uid int(11) NOT NULL auto_increment,
        pid int(11) DEFAULT '0' NOT NULL,
        tstamp int(11) DEFAULT '0' NOT NULL,
        crdate int(11) DEFAULT '0' NOT NULL,
        cruser_id int(11) DEFAULT '0' NOT NULL,
        seriesid  int(11) DEFAULT '0' NOT NULL,
        resourceid int(11) DEFAULT '0' NOT NULL,
	sorting int(10) unsigned DEFAULT '0' NOT NULL,

        PRIMARY KEY (uid),
        KEY parent (pid),
	KEY series (seriesid),
	KEY resource (resourceid)
);




#
# Table structure for table 'tx_wecsermons_sermons'
#
CREATE TABLE tx_wecsermons_sermons (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	sorting int(10) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	l18n_parent int(11) DEFAULT '0' NOT NULL,
	l18n_diffsource mediumblob NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	starttime int(11) DEFAULT '0' NOT NULL,
	endtime int(11) DEFAULT '0' NOT NULL,
	fe_group int(11) DEFAULT '0' NOT NULL,
	title tinytext NOT NULL,
	subtitle tinytext NOT NULL,
	occurrence_date int(11) DEFAULT '0' NOT NULL,
	description text NOT NULL,
	scripture tinytext NOT NULL,
	keywords tinytext NOT NULL,
	graphic blob NOT NULL,
	alttitle tinytext NOT NULL,
	series blob NOT NULL,
	topics blob NOT NULL,
	record_type int(11) DEFAULT '0' NOT NULL,
	resources int(11) DEFAULT '0' NOT NULL,
	speakers blob NOT NULL,
	islinked tinyint(3) DEFAULT '1' NOT NULL,
	current tinyint(3) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);




#
# Table structure for table 'tx_wecsermons_series'
#
CREATE TABLE tx_wecsermons_series (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	starttime int(11) DEFAULT '0' NOT NULL,
	endtime int(11) DEFAULT '0' NOT NULL,
	fe_group int(11) DEFAULT '0' NOT NULL,
	title tinytext NOT NULL,
	subtitle tinytext NOT NULL,
	description text NOT NULL,
	scripture tinytext NOT NULL,
	startdate int(11) DEFAULT '0' NOT NULL,
	enddate int(11) DEFAULT '0' NOT NULL,
	graphic blob NOT NULL,
	alttitle tinytext NOT NULL,
	seasons blob NOT NULL,
	topics blob NOT NULL,
	keywords tinytext NOT NULL,
	resources int(11) DEFAULT '0' NOT NULL,
	islinked tinyint(3) DEFAULT '1' NOT NULL,
	current tinyint(3) DEFAULT '0' NOT NULL,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);
#
# Table structure for table 'tx_wecsermons_sermons_series_rel'
#
CREATE TABLE tx_wecsermons_sermons_series_rel (
        uid int(11) NOT NULL auto_increment,
        pid int(11) DEFAULT '0' NOT NULL,
        tstamp int(11) DEFAULT '0' NOT NULL,
        crdate int(11) DEFAULT '0' NOT NULL,
        cruser_id int(11) DEFAULT '0' NOT NULL,
        sermonid  int(11) DEFAULT '0' NOT NULL,
        seriesid int(11) DEFAULT '0' NOT NULL,

        PRIMARY KEY (uid),
        KEY parent (pid),
	KEY sermonid (sermonid),
	KEY seriesid (seriesid)
);




#
# Table structure for table 'tx_wecsermons_topics'
#
CREATE TABLE tx_wecsermons_topics (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	description text NOT NULL,
	title tinytext NOT NULL,
	islinked tinyint(3) DEFAULT '1' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);
#
# Table structure for table 'tx_wecsermons_sermons_topics_rel'
#
CREATE TABLE tx_wecsermons_sermons_topics_rel (
        uid int(11) NOT NULL auto_increment,
        pid int(11) DEFAULT '0' NOT NULL,
        tstamp int(11) DEFAULT '0' NOT NULL,
        crdate int(11) DEFAULT '0' NOT NULL,
        cruser_id int(11) DEFAULT '0' NOT NULL,
        sermonid  int(11) DEFAULT '0' NOT NULL,
        topicid int(11) DEFAULT '0' NOT NULL,

        PRIMARY KEY (uid),
        KEY parent (pid),
	KEY sermonid (sermonid),
	KEY topicid (topicid),
);
#
# Table structure for table 'tx_wecsermons_series_topics_rel'
#
CREATE TABLE tx_wecsermons_series_topics_rel (
        uid int(11) NOT NULL auto_increment,
        pid int(11) DEFAULT '0' NOT NULL,
        tstamp int(11) DEFAULT '0' NOT NULL,
        crdate int(11) DEFAULT '0' NOT NULL,
        cruser_id int(11) DEFAULT '0' NOT NULL,
        seriesid  int(11) DEFAULT '0' NOT NULL,
        topicid int(11) DEFAULT '0' NOT NULL,

        PRIMARY KEY (uid),
        KEY parent (pid),
	KEY series (seriesid),
	KEY topic (topicid)
);




#
# Table structure for table 'tx_wecsermons_seasons'
#
CREATE TABLE tx_wecsermons_seasons (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	title tinytext NOT NULL,
	description text NOT NULL,
	islinked tinyint(3) DEFAULT '1' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);
#
# Table structure for table 'tx_wecsermons_series_seasons_rel'
#
CREATE TABLE tx_wecsermons_series_seasons_rel (
        uid int(11) NOT NULL auto_increment,
        pid int(11) DEFAULT '0' NOT NULL,
        tstamp int(11) DEFAULT '0' NOT NULL,
        crdate int(11) DEFAULT '0' NOT NULL,
        cruser_id int(11) DEFAULT '0' NOT NULL,
        seriesid  int(11) DEFAULT '0' NOT NULL,
        seasonid int(11) DEFAULT '0' NOT NULL,

        PRIMARY KEY (uid),
        KEY parent (pid),
	KEY series (seriesid),
	KEY season (seasonid)
);




#
# Table structure for table 'tx_wecsermons_speakers'
#
CREATE TABLE tx_wecsermons_speakers (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sys_language_uid int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	fullname tinytext NOT NULL,
	firstname tinytext NOT NULL,
	lastname tinytext NOT NULL,
	url tinytext NOT NULL,
	photo blob NOT NULL,
	alttitle tinytext NOT NULL,
	email tinytext NOT NULL,
	islinked tinyint(3) DEFAULT '1' NOT NULL,
	blogurl tinytext NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);
#
# Table structure for table 'tx_wecsermons_sermons_speakers_rel'
#
CREATE TABLE tx_wecsermons_sermons_speakers_rel (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sermonid  int(11) DEFAULT '0' NOT NULL,
	speakerid int(11) DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY sermon (sermonid),
	KEY speaker (speakerid)
);

#
# Table structure for our metadata 'tx_wecsermons_meta'
#
CREATE TABLE tx_wecsermons_meta (
	property varchar(50) DEFAULT '' NOT NULL,
	value varchar(100) DEFAULT '' NOT NULL
);
