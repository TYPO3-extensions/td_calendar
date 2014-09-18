#
# Table structure for table 'tx_tdcalendar_events'
#
CREATE TABLE tx_tdcalendar_events (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	t3ver_oid int(11) DEFAULT '0' NOT NULL,
	t3ver_id int(11) DEFAULT '0' NOT NULL,
	t3ver_wsid int(11) DEFAULT '0' NOT NULL,
	t3ver_label varchar(30) DEFAULT '' NOT NULL,
	t3ver_state tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_stage tinyint(4) DEFAULT '0' NOT NULL,
	t3ver_count int(11) DEFAULT '0' NOT NULL,
	t3ver_tstamp int(11) DEFAULT '0' NOT NULL,
	t3_origuid int(11) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	starttime int(11) DEFAULT '0' NOT NULL,
	endtime int(11) DEFAULT '0' NOT NULL,
	allday tinyint(4) unsigned DEFAULT '0' NOT NULL,
	fe_group int(11) DEFAULT '0' NOT NULL,
	category int(11) DEFAULT '0' NOT NULL,
	begin int(11) DEFAULT '0' NOT NULL,
	end int(11) DEFAULT '0' NOT NULL,
	event_type tinyint(1) DEFAULT '0' NOT NULL,	
	exc_event varchar(128) DEFAULT '0' NOT NULL,
	exc_category varchar(128) DEFAULT '0' NOT NULL,
	rec_end_date int(11) unsigned DEFAULT '0' NOT NULL,
	rec_time_x tinyint(5) DEFAULT '0' NOT NULL,
	repeat_days int(11) unsigned DEFAULT '0' NOT NULL,
	repeat_weeks int(11) unsigned DEFAULT '0' NOT NULL,
	repeat_months int(11) unsigned DEFAULT '0' NOT NULL,
	repeat_years int(11) unsigned DEFAULT '0' NOT NULL,
	rec_weekly_type int(11) unsigned DEFAULT '0' NOT NULL,
	repeat_week_monday tinyint(4) unsigned DEFAULT '0' NOT NULL,
	repeat_week_tuesday tinyint(4) unsigned DEFAULT '0' NOT NULL,
	repeat_week_wednesday tinyint(4) unsigned DEFAULT '0' NOT NULL,
	repeat_week_thursday tinyint(4) unsigned DEFAULT '0' NOT NULL,
	repeat_week_friday tinyint(4) unsigned DEFAULT '0' NOT NULL,
	repeat_week_saturday tinyint(4) unsigned DEFAULT '0' NOT NULL,
	repeat_week_sunday tinyint(4) unsigned DEFAULT '0' NOT NULL,
	title varchar(128) DEFAULT '' NOT NULL,
	teaser text,
	description text,
	location varchar(128) DEFAULT '' NOT NULL,
	location_id int(11) DEFAULT '0' NOT NULL,
	organizer tinytext,
	organizer_id int(11) DEFAULT '0' NOT NULL,
	link tinytext,
	image text,
	imagecaption text,
	imagealttext text,
	imagetitletext text,
	directlink tinytext,
	
	PRIMARY KEY (uid),
	KEY parent (pid),
	KEY t3ver_oid (t3ver_oid,t3ver_wsid)
);



#
# Table structure for table 'tx_tdcalendar_categories'
#
CREATE TABLE tx_tdcalendar_categories (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sorting int(10) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	starttime int(11) DEFAULT '0' NOT NULL,
	endtime int(11) DEFAULT '0' NOT NULL,
	fe_group int(11) DEFAULT '0' NOT NULL,
	title varchar(128) DEFAULT '' NOT NULL,
	color tinytext,
	comment tinytext,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_tdcalendar_locations'
#
CREATE TABLE tx_tdcalendar_locations (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sorting int(10) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	fe_group int(11) DEFAULT '0' NOT NULL,
	location varchar(128) DEFAULT '' NOT NULL,
	description text,
	contact varchar(128) DEFAULT '' NOT NULL,
	street varchar(128) DEFAULT '' NOT NULL,
	zip tinytext,
	city tinytext,
	phone tinytext,
	email tinytext,
	image text,
	imagecaption text,
	imagealttext text,
	imagetitletext text,
	link tinytext,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);



#
# Table structure for table 'tx_tdcalendar_organizer'
#
CREATE TABLE tx_tdcalendar_organizer (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,
	tstamp int(11) DEFAULT '0' NOT NULL,
	crdate int(11) DEFAULT '0' NOT NULL,
	cruser_id int(11) DEFAULT '0' NOT NULL,
	sorting int(10) DEFAULT '0' NOT NULL,
	deleted tinyint(4) DEFAULT '0' NOT NULL,
	hidden tinyint(4) DEFAULT '0' NOT NULL,
	fe_group int(11) DEFAULT '0' NOT NULL,
	name tinytext,
	description text,
	street tinytext,
	zip tinytext,
	city tinytext,
	phone tinytext,
	email tinytext,
	image text,
	imagecaption text,
	imagealttext text,
	imagetitletext text,
	link tinytext,
	
	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_tdcalendar_exc_events'
#

CREATE TABLE tx_tdcalendar_exc_events (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	begin int(11) DEFAULT '0' NOT NULL,
	end int(11) DEFAULT '0' NOT NULL,
	title varchar(128) DEFAULT '' NOT NULL,
	priority int(11) DEFAULT '0' NOT NULL,
	exc_categories int(11) DEFAULT '0' NOT NULL,
	fe_group int(11) DEFAULT '0' NOT NULL,
	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'tx_tdcalendar_exc_categories'
#

CREATE TABLE tx_tdcalendar_exc_categories (
	uid int(11) unsigned DEFAULT '0' NOT NULL auto_increment,
	pid int(11) unsigned DEFAULT '0' NOT NULL,
	tstamp int(11) unsigned DEFAULT '0' NOT NULL,
	crdate int(11) unsigned DEFAULT '0' NOT NULL,
	cruser_id int(11) unsigned DEFAULT '0' NOT NULL,
	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,
	title tinytext NOT NULL,
	color tinytext NOT NULL,
	bgcolor tinyint(4) unsigned DEFAULT '0' NOT NULL,
	fe_group int(11) DEFAULT '0' NOT NULL,
	PRIMARY KEY (uid),
	KEY parent (pid)
);

#
# Table structure for table 'be_groups'
#
CREATE TABLE be_groups (
	td_calendar_categorymounts varchar(255) DEFAULT '' NOT NULL,
	
);

#
# Table structure for table 'be_users'
#
CREATE TABLE be_users (
	td_calendar_categorymounts varchar(255) DEFAULT '' NOT NULL,
);