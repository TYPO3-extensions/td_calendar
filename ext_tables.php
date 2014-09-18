<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

t3lib_extMgm::allowTableOnStandardPages('tx_tdcalendar_events');

$TCA['tx_tdcalendar_events'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_events',		
		'label'     => 'title',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'versioningWS' => TRUE, 
		'origUid' => 't3_origuid',
		'default_sortby' => 'ORDER BY begin DESC',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',	
			'starttime' => 'starttime',	
			'endtime' => 'endtime',	
			'fe_group' => 'fe_group',
		),
		'dividers2tabs' => TRUE, 
		'mainpalette' => '1',
		'canNotCollapse'=>	'1',
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/images/icon_tx_tdcalendar_events.gif',
	),
);

t3lib_extMgm::allowTableOnStandardPages('tx_tdcalendar_categories');
//t3lib_extMgm::addToInsertRecords('tx_tdcalendar_events');

$TCA['tx_tdcalendar_categories'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_categories',		
		'label'     => 'title',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY title ASC',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',	
			'starttime' => 'starttime',	
			'endtime' => 'endtime',	
			'fe_group' => 'fe_group',
		),
		'dividers2tabs' => TRUE, 
		'mainpalette' => '1',
		'canNotCollapse'=>	'1',
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/images/icon_tx_tdcalendar_categories.gif',
	),
);


t3lib_extMgm::allowTableOnStandardPages('tx_tdcalendar_locations');

$TCA['tx_tdcalendar_locations'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_locations',		
		'label'     => 'location',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY location ASC',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',	
			'fe_group' => 'fe_group',
		),
		'dividers2tabs' => TRUE, 
		'mainpalette' => '1',
		'canNotCollapse'=>	'1',
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/images/icon_tx_tdcalendar_locations.gif',
	),
);


t3lib_extMgm::allowTableOnStandardPages('tx_tdcalendar_organizer');

$TCA['tx_tdcalendar_organizer'] = array (
	'ctrl' => array (
		'title'     => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_organizer',		
		'label'     => 'name',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY name ASC',	
		'delete' => 'deleted',	
		'enablecolumns' => array (		
			'disabled' => 'hidden',	
			'fe_group' => 'fe_group',
		),
		'dividers2tabs' => true, 
		'mainpalette' => '1',
		'canNotCollapse'=>	'1',
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'res/images/icon_tx_tdcalendar_organizer.gif',
	),
);

t3lib_extMgm::allowTableOnStandardPages('tx_tdcalendar_exc_events');

$TCA['tx_tdcalendar_exc_events'] = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:td_calendar/locallang_db.php:tx_tdcalendar_exc_events',		
		'label' => 'title',	
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY begin DESC',	
		'delete' => 'deleted',	
		'dividers2tabs' => TRUE, 
		'mainpalette' => '1',
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'res/images/icon_tx_tdcalendar_exc_events.gif',
		'enablecolumns' => Array (
			'disabled' => 'hidden',
			'fe_group' => 'fe_group',
		),
	),
	'feInterface' => Array (
		'fe_admin_fieldList' => 'title',
	)
);

t3lib_extMgm::allowTableOnStandardPages('tx_tdcalendar_exc_categories');

$TCA['tx_tdcalendar_exc_categories'] = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:td_calendar/locallang_db.php:tx_tdcalendar_exc_categories',		
		'label' => 'title',	
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => 'ORDER BY title ASC',	
		'delete' => 'deleted',	
		'dividers2tabs' => TRUE, 
		'mainpalette' => '1',
		
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'res/images/icon_tx_tdcalendar_exc_categories.gif',
		'enablecolumns' => Array (
			'disabled' => 'hidden',
			'fe_group' => 'fe_group',
		),
	),
	'feInterface' => Array (
		'fe_admin_fieldList' => 'title',
	)
);

// 2013-03-15 Introducing: CATEGORY MOUNTS 

$tempColumns = array (
		'td_calendar_categorymounts' => array (
			'exclude' => 1,
			'label' => 'LLL:EXT:td_calendar/locallang_tca.xml:td_calendar.categorymounts',
			'config' => array (
				'type' => 'group',
				'internal_type' => 'db',
				'allowed' => 'pages',
				'size' => 5,
				'minitems' => 0,
				'maxitems' => 250,
			)
		),
);

t3lib_div::loadTCA('be_groups');
t3lib_extMgm::addTCAcolumns('be_groups', $tempColumns, 1);
t3lib_extMgm::addToAllTCAtypes('be_groups', 'td_calendar_categorymounts;;;;1-1-1');

$tempColumns['td_calendar_categorymounts']['displayCond'] = 'FIELD:admin:=:0';

t3lib_div::loadTCA('be_users');
t3lib_extMgm::addTCAcolumns('be_users', $tempColumns,1);
t3lib_extMgm::addToAllTCAtypes('be_users', 'td_calendar_categorymounts;;;;1-1-1');


t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key';


t3lib_extMgm::addPlugin( array(
	'LLL:EXT:td_calendar/locallang_db.xml:tt_content.list_type_pi1',
	$_EXTKEY . '_pi1',
	t3lib_extMgm::extRelPath($_EXTKEY) . 'ext_icon.gif'
),'list_type');

t3lib_extMgm::addStaticFile($_EXTKEY, 'static/', 'Calendar');

$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY.'_pi1'] = 'pi_flexform';
t3lib_extMgm::addPiFlexFormValue($_EXTKEY.'_pi1', 'FILE:EXT:'.$_EXTKEY.'/flexform_ds.xml');

if (TYPO3_MODE == 'BE') {
	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_tdcalendar_pi1_wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.tx_tdcalendar_pi1_wizicon.php';
}
?>