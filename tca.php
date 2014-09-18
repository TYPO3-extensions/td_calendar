<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$includeCatMounts = ''; 
$includeExcMounts = ''; 
$includeExcCatMounts = '';
$includeOrgMounts = ''; 
$includeLocMounts = ''; 
 
$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['td_calendar']);
if(!$GLOBALS['BE_USER']->user["admin"] AND $confArr['useCategoryMounts'] AND t3lib_div::_GP('edit')) {
	//global $BE_USER;
	$cmounts = array();
	if (is_array($GLOBALS['BE_USER']->userGroups)){ 
		foreach ($GLOBALS['BE_USER']->userGroups as $group) {
			if ($group['td_calendar_categorymounts']) {
				$cmounts[] = $group['td_calendar_categorymounts'];
			}
		}
	} 
	
	if ($BE_USER->user['td_calendar_categorymounts']) {
		$cmounts[] = $BE_USER->user['td_calendar_categorymounts'];
	}
	
	$categoryMounts = implode(',', $cmounts);
	$cmounts = array_unique(explode(',', $categoryMounts)); 
	
	if($confArr['categoryMountRecursive'] > 0) {
		foreach($cmounts as $mount) {
			$subCategoriesMount[] = t3lib_queryGenerator::getTreeList($mount, $confArr['categoryMountRecursive'], 0, 'pages.hidden != 1'); 
		}
		
		$categoryMounts = implode(',', $subCategoriesMount); 
	} 

	$includeCatMounts = 'AND (tx_tdcalendar_categories.pid = ###CURRENT_PID### OR tx_tdcalendar_categories.pid IN ('.$categoryMounts.')) '; 
	$includeExcMounts = 'AND (tx_tdcalendar_exc_events.pid = ###CURRENT_PID### OR tx_tdcalendar_exc_events.pid IN ('.$categoryMounts.')) '; 
	$includeExcCatMounts = 'AND (tx_tdcalendar_exc_categories.pid = ###CURRENT_PID### OR tx_tdcalendar_exc_categories.pid IN ('.$categoryMounts.')) ';
	$includeOrgMounts = 'AND (tx_tdcalendar_organizer.pid = ###CURRENT_PID### OR tx_tdcalendar_organizer.pid IN ('.$categoryMounts.')) '; 
	$includeLocMounts = 'AND (tx_tdcalendar_locations.pid = ###CURRENT_PID### OR tx_tdcalendar_locations.pid IN ('.$categoryMounts.')) '; 	
}

$TCA['tx_tdcalendar_events'] = array (
	'ctrl' => $TCA['tx_tdcalendar_events']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,category,begin,title,fe_group'
	),
	'feInterface' => $TCA['tx_tdcalendar_events']['feInterface'],
	'columns' => array (
		't3ver_label' => array (		
			'label'  => 'LLL:EXT:lang/locallang_general.xml:LGL.versionLabel',
			'config' => array (
				'type' => 'input',
				'size' => '30',
				'max'  => '30',
			)
		),
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'starttime' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
			'config'  => array (
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'default'  => '0',
				'checkbox' => '0'
			)
		),
		'endtime' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
			'config'  => array (
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'checkbox' => '0',
				'default'  => '0',
				'range'    => array (
					'upper' => mktime(3, 14, 7, 1, 19, 2038),
					'lower' => mktime(0, 0, 0, date('m')-1, date('d'), date('Y'))
				)
			)
		),
		'fe_group' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.fe_group',
			'config'  => array (
				'type'  => 'select',
				'items' => array (
					array('', 0),
					array('LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.any_login', -2),
					array('LLL:EXT:lang/locallang_general.xml:LGL.usergroups', '--div--')
				),
				'foreign_table' => 'fe_groups'
			)
		),
		'category' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_events.category',		
			'config' => array (
				'type' => 'select',	
				'foreign_table' => 'tx_tdcalendar_categories',	
				'foreign_table_where' => $includeCatMounts.'ORDER BY tx_tdcalendar_categories.uid',	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		
		'event_type' => Array (
		'label' => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_events.type',		
		'config' => Array (
			'type' => 'select',
				'items' => Array (
					Array('LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_events.type.regular', 0),
					Array('LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_events.type.recurring_daily', 1),
					Array('LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_events.type.recurring_weekly', 2),
					Array('LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_events.type.recurring_monthly', 3),
					Array('LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_events.type.recurring_yearly', 4),
				),
			),
		),
		
		'exc_event' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_events.exc_event',
			'config' => Array (
				'type' => 'select',
				'foreign_table' => 'tx_tdcalendar_exc_events',
				'foreign_table_where' => $includeExcMounts.'ORDER BY tx_tdcalendar_exc_events.uid',
				'size' => 5,
				'minitems' => 0,
				'maxitems' => 128,
			)
		),
		
		'exc_category' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_events.exc_category',
			'config' => Array (
				'type' => 'select',
				'foreign_table' => 'tx_tdcalendar_exc_categories',
				'foreign_table_where' => $includeExcCatMounts.'ORDER BY tx_tdcalendar_exc_categories.uid',
				'size' => 5,
				'minitems' => 0,
				'maxitems' => 128,
			)
		),

		
		'begin' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_events.begin',		
			'config' => array (
				'type'     => 'input',
				'size'     => '12',
				'max'      => '20',
				'eval'     => 'required,datetime',
				'checkbox' => '0',
				'default'  => '0'
			)
		),
		'end' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_events.end',		
			'config' => array (
				'type'     => 'input',
				'size'     => '12',
				'max'      => '20',
				'eval'     => 'datetime',
				'checkbox' => '0',
				'default'  => '0'
			)
		),
		
		'allday' => Array (		
			'exclude' => 1,	
			'label' => 'LLL:EXT:td_calendar/locallang_db.php:tx_tdcalendar_events.allday',		
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),
		
		'rec_end_date' => Array (
			'exclude' => 1,	
			'label' => 'LLL:EXT:td_calendar/locallang_db.php:tx_tdcalendar_events.rec_end_date',
			'config' => Array (
				'type' => 'input',
				'size' => '12',
				'max' => '20',
				'eval' => 'date',
				'checkbox' => '0',
				'default' => '0'
			),
		),
		
		'rec_time_x' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:td_calendar/locallang_db.php:tx_tdcalendar_events.rec_time_x',		
			'config' => Array (
				'type' => 'input',	
				'size' => '2',	
				'eval' => 'integer',
				'default' => '0',
			)
		),
		
		'repeat_days' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:td_calendar/locallang_db.php:tx_tdcalendar_events.repeat_days',		
			'config' => Array (
				'type' => 'input',	
				'size' => '2',	
				'eval' => 'integer',
				'default' => '1',
			)
		),
		
		'repeat_weeks' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:td_calendar/locallang_db.php:tx_tdcalendar_events.repeat_weeks',		
			'config' => Array (
				'type' => 'input',	
				'size' => '2',	
				'eval' => 'integer',
				'default' => '1',
			)
		),
		
		'repeat_months' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:td_calendar/locallang_db.php:tx_tdcalendar_events.repeat_months',		
			'config' => Array (
				'type' => 'input',	
				'size' => '2',	
				'eval' => 'integer',
				'default' => '1',
			)
		),
		
		'repeat_years' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:td_calendar/locallang_db.php:tx_tdcalendar_events.repeat_years',		
			'config' => Array (
				'type' => 'input',	
				'size' => '2',	
				'eval' => 'integer',
				'default' => '1',
			)
		),
		
		'rec_weekly_type' => Array (
			'exclude' => 1,	
			'label' => 'LLL:EXT:td_calendar/locallang_db.php:tx_tdcalendar_events.rec_weekly_type',		
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('LLL:EXT:td_calendar/locallang_db.php:tx_tdcalendar_events.rec_weekly_type.days', 0),
					Array('LLL:EXT:td_calendar/locallang_db.php:tx_tdcalendar_events.rec_weekly_type.workdays', 1),
					Array('LLL:EXT:td_calendar/locallang_db.php:tx_tdcalendar_events.rec_weekly_type.weekend', 2),
				),
			),
		),
	
		'repeat_week_monday' => Array (		
			'exclude' => 1,	
			'label' => 'LLL:EXT:td_calendar/locallang_db.php:tx_tdcalendar_events.repeat_week_monday',		
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),
		'repeat_week_tuesday' => Array (		
			'exclude' => 1,	
			'label' => 'LLL:EXT:td_calendar/locallang_db.php:tx_tdcalendar_events.repeat_week_tuesday',		
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),
		'repeat_week_wednesday' => Array (		
			'exclude' => 1,	
			'label' => 'LLL:EXT:td_calendar/locallang_db.php:tx_tdcalendar_events.repeat_week_wednesday',		
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),
		'repeat_week_thursday' => Array (		
			'exclude' => 1,	
			'label' => 'LLL:EXT:td_calendar/locallang_db.php:tx_tdcalendar_events.repeat_week_thursday',		
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),
		'repeat_week_friday' => Array (		
			'exclude' => 1,	
			'label' => 'LLL:EXT:td_calendar/locallang_db.php:tx_tdcalendar_events.repeat_week_friday',		
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),
		'repeat_week_saturday' => Array (		
			'exclude' => 1,	
			'label' => 'LLL:EXT:td_calendar/locallang_db.php:tx_tdcalendar_events.repeat_week_saturday',		
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),
		'repeat_week_sunday' => Array (		
			'exclude' => 1,	
			'label' => 'LLL:EXT:td_calendar/locallang_db.php:tx_tdcalendar_events.repeat_week_sunday',		
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),
		
		'title' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_events.title',		
			'config' => array (
				'type' => 'input',	
				'size' => '48',	
				'max' => '128',	
				'eval' => 'required,trim',
			)
		),
		'teaser' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_events.teaser',		
			'config' => array (
				'type' => 'text',
				'wrap' => 'OFF',
				'cols' => '48',	
				'rows' => '3',
			)
		),
		'description' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_events.description',		
			'config' => array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
				'wizards' => array(
					'_PADDING' => 2,
					'RTE' => array(
						'notNewRecords' => 1,
						'RTEonly'       => 1,
						'type'          => 'script',
						'title'         => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
						'icon'          => 'wizard_rte2.gif',
						'script'        => 'wizard_rte.php',
					),
				),
			)
		),
		'location' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_events.location',		
			'config' => array (
				'type' => 'input',	
				'size' => '48',	
				'max' => '128',	
				'eval' => 'trim',
			)
		),
		'location_id' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_events.location_id',		
			'config' => array (
				'type' => 'select',	
				'items' => array (
					array('',0),
				),
				'foreign_table' => 'tx_tdcalendar_locations',	
				'foreign_table_where' => $includeLocMounts.'ORDER BY tx_tdcalendar_locations.uid',	
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'organizer' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_events.organizer',		
			'config' => array (
				'type' => 'input',	
				'size' => '48',	
				'max' => '128',	
			)
		),
		'organizer_id' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_events.organizer_id',		
			'config' => array (
				'type' => 'select',	
				'items' => array (
					array('',0),
				),
				'foreign_table' => 'tx_tdcalendar_organizer',	
				'foreign_table_where' =>  $includeOrgMounts.'ORDER BY tx_tdcalendar_organizer.uid',		//'AND tx_tdcalendar_organizer.pid=###STORAGE_PID### ORDER BY tx_tdcalendar_organizer.uid'
				'size' => 1,	
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'link' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_events.link',		
			'config' => array (
				'type'     => 'input',
				'size'     => '15',
				'max'      => '255',
				'checkbox' => '',
				'eval'     => 'trim',
				'wizards'  => array(
					'_PADDING' => 2,
					'link'     => array(
						'type'         => 'popup',
						'title'        => 'Link',
						'icon'         => 'link_popup.gif',
						'script'       => 'browse_links.php?mode=wizard',
						'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1'
					)
				)
			)
		),
		'image' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.images',
			'config' => Array (
				'type' => 'group',
				'internal_type' => 'file',
				'allowed' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
				'max_size' => $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'],
				'uploadfolder' => 'uploads/pics',
				'show_thumbs' => '1',
				'size' => 3,
				'autoSizeMax' => 15,
				'maxitems' => '99',
				'minitems' => '0'
			)
		),
		'imagecaption' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.caption',
			'config' => Array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '3'
			)
		),
		'imagealttext' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar.imagealttext',
			'config' => Array (
				'type' => 'text',
				'cols' => '20',
				'rows' => '3'
			)
		),
		'imagetitletext' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar.imagetitletext',
			'config' => Array (
				'type' => 'text',
				'cols' => '20',
				'rows' => '3'
			)
		),		
		'directlink' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_events.directlink',		
			'config' => array (
				'type'     => 'input',
				'size'     => '15',
				'max'      => '255',
				'checkbox' => '',
				'eval'     => 'trim',
				'wizards'  => array(
					'_PADDING' => 2,
					'link'     => array(
						'type'         => 'popup',
						'title'        => 'Link',
						'icon'         => 'link_popup.gif',
						'script'       => 'browse_links.php?mode=wizard',
						'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1'
					)
				)
			)
		),
	),
	
	'palettes' => array (
		'1' => Array('showitem' => 'starttime, endtime, fe_group'),
		'2'  => Array('showitem' => 'begin, end, allday'),
		'3'	=>	Array('showitem' => 'repeat_week_monday, repeat_week_tuesday, repeat_week_wednesday, repeat_week_thursday, repeat_week_friday, repeat_week_saturday, repeat_week_sunday'),
		'4' => Array('showitem' => 'imagealttext,imagetitletext'),
	),
	
	'types' => array (
		'0' => array('showitem' => 'hidden, category, event_type;;2;;1-1-1'),
		'1' => array('showitem' => 'hidden, category, event_type;;2;;1-1-1, repeat_days, rec_time_x,rec_end_date'),
		'2' => array('showitem' => 'hidden, category, event_type;;2;;1-1-1, rec_weekly_type;;3;;, repeat_weeks, rec_time_x, rec_end_date',
					 'subtype_value_field'	=>	'rec_weekly_type',
					 'subtypes_excludelist'	=>	Array(
							'1'	=>	'repeat_week_monday, repeat_week_tuesday, repeat_week_wednesday, repeat_week_thursday, repeat_week_friday, repeat_week_saturday, repeat_week_sunday',
							'2'	=>	'repeat_week_monday, repeat_week_tuesday, repeat_week_wednesday, repeat_week_thursday, repeat_week_friday, repeat_week_saturday, repeat_week_sunday',
						),
				),
		'3' => array('showitem' => 'hidden, category, event_type;;2;;1-1-1, repeat_months, rec_time_x,rec_end_date'),
		'4' => array('showitem' => 'hidden, category, event_type;;2;;1-1-1, repeat_years, rec_time_x,rec_end_date')
	)
	
);

foreach(array('0', '1', '2', '3', '4') as $type) {
	$TCA['tx_tdcalendar_events']['types'][$type]['showitem'] .= ', title;;;;2-2-2, teaser;;;;3-3-3, description;;;richtext[]:rte_transform[mode=ts], location, location_id, organizer, organizer_id, '; 
	$TCA['tx_tdcalendar_events']['types'][$type]['showitem'] .= $type != '0' ? '--div--;LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar.tabs.exc_events, exc_event, exc_category, ' : '';
	$TCA['tx_tdcalendar_events']['types'][$type]['showitem'] .= '--div--;LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar.tabs.media, image,;;;;1-1-1,imagecaption;;4;;, link, directlink, 
		--div--;LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar.tabs.access ;;1;;1-1-1';
}

$TCA['tx_tdcalendar_events']['ctrl']['type']			=	'event_type';
$TCA['tx_tdcalendar_events']['ctrl']['mainpalette']		=	'1';
$TCA['tx_tdcalendar_events']['ctrl']['canNotCollapse']	=	'1';
$TCA['tx_tdcalendar_events']['ctrl']['requestUpdate']	=	'rec_weekly_type,';



$TCA['tx_tdcalendar_categories'] = array (
	'ctrl' => $TCA['tx_tdcalendar_categories']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,fe_group,title,comment'
	),
	'feInterface' => $TCA['tx_tdcalendar_categories']['feInterface'],
	'columns' => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'starttime' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.starttime',
			'config'  => array (
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'default'  => '0',
				'checkbox' => '0'
			)
		),
		'endtime' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.endtime',
			'config'  => array (
				'type'     => 'input',
				'size'     => '8',
				'max'      => '20',
				'eval'     => 'date',
				'checkbox' => '0',
				'default'  => '0',
				'range'    => array (
					'upper' => mktime(3, 14, 7, 1, 19, 2038),
					'lower' => mktime(0, 0, 0, date('m')-1, date('d'), date('Y'))
				)
			)
		),
		'fe_group' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.fe_group',
			'config'  => array (
				'type'  => 'select',
				'items' => array (
					array('', 0),
					array('LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.any_login', -2),
					array('LLL:EXT:lang/locallang_general.xml:LGL.usergroups', '--div--')
				),
				'foreign_table' => 'fe_groups'
			)
		),
		'title' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_categories.title',		
			'config' => array (
				'type' => 'input',	
				'size' => '48',	
				'max' => '128',	
				'eval' => 'required,trim',
			)
		),
		'color' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_categories.color',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',	
				'wizards' => array(
					'_PADDING' => 2,
					'color' => array(
						'title' => 'Color:',
						'type' => 'colorbox',
						'dim' => '12x12',
						'tableStyle' => 'border:solid 1px black;',
						'script' => 'wizard_colorpicker.php',
						'JSopenParams' => 'height=300,width=250,status=0,menubar=0,scrollbars=1',
					),
				),
			)
		),
		'comment' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_categories.comment',		
			'config' => array (
				'type' => 'input',	
				'size' => '48',	
				'max' => '128',
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 
		
		'hidden, title;;;;2-2-2, color;;;;3-3-3, comment,
		--div--;LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar.tabs.access ;;1;;1-1-1')
	),
	'palettes' => array (
		'1' => array('showitem' => 'starttime, endtime, fe_group')
	)
);



$TCA['tx_tdcalendar_locations'] = array (
	'ctrl' => $TCA['tx_tdcalendar_locations']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,fe_group,location'
	),
	'feInterface' => $TCA['tx_tdcalendar_locations']['feInterface'],
	'columns' => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'fe_group' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.fe_group',
			'config'  => array (
				'type'  => 'select',
				'items' => array (
					array('', 0),
					array('LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.any_login', -2),
					array('LLL:EXT:lang/locallang_general.xml:LGL.usergroups', '--div--')
				),
				'foreign_table' => 'fe_groups'
			)
		),
		'location' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_locations.location',		
			'config' => array (
				'type' => 'input',	
				'size' => '48',	
				'max' => '128',	
				'eval' => 'required,trim',
			)
		),
		'description' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_locations.description',		
			'config' => array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
				'wizards' => array(
					'_PADDING' => 2,
					'RTE' => array(
						'notNewRecords' => 1,
						'RTEonly'       => 1,
						'type'          => 'script',
						'title'         => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
						'icon'          => 'wizard_rte2.gif',
						'script'        => 'wizard_rte.php',
					),
				),
			)
		),
		'contact' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_locations.contact',		
			'config' => array (
				'type' => 'input',	
				'size' => '48',	
				'max' => '128',	
				'eval' => 'trim',
			)
		),
		'street' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_locations.street',		
			'config' => array (
				'type' => 'input',	
				'size' => '48',	
				'max' => '128',	
				'eval' => 'trim',
			)
		),
		'zip' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_locations.zip',		
			'config' => array (
				'type' => 'input',	
				'size' => '10',	
				'max' => '6',
			)
		),
		'city' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_locations.city',		
			'config' => array (
				'type' => 'input',	
				'size' => '48',	
				'max' => '128',
			)
		),
		'phone' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_locations.phone',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
		'email' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_locations.email',		
			'config' => array (
				'type' => 'input',	
				'size' => '48',	
				'max' => '64',
			)
		),
		'image' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.images',
			'config' => Array (
				'type' => 'group',
				'internal_type' => 'file',
				'allowed' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
				'max_size' => $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'],
				'uploadfolder' => 'uploads/pics',
				'show_thumbs' => '1',
				'size' => 3,
				'autoSizeMax' => 15,
				'maxitems' => '99',
				'minitems' => '0'
			)
		),
		'imagecaption' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.caption',
			'config' => Array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '3'
			)
		),
		'imagealttext' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar.imagealttext',
			'config' => Array (
				'type' => 'text',
				'cols' => '20',
				'rows' => '3'
			)
		),
		'imagetitletext' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar.imagetitletext',
			'config' => Array (
				'type' => 'text',
				'cols' => '20',
				'rows' => '3'
			)
		),	
		'link' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_locations.link',		
			'config' => array (
				'type'     => 'input',
				'size'     => '15',
				'max'      => '255',
				'checkbox' => '',
				'eval'     => 'trim',
				'wizards'  => array(
					'_PADDING' => 2,
					'link'     => array(
						'type'         => 'popup',
						'title'        => 'Link',
						'icon'         => 'link_popup.gif',
						'script'       => 'browse_links.php?mode=wizard',
						'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1'
					)
				)
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 
		'hidden, location, description;;;richtext[]:rte_transform[mode=ts], contact, street, zip, city, phone, email, 
		--div--;LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar.tabs.media, image,;;;;1-1-1,imagecaption;;2;;, link,
		--div--;LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar.tabs.access ;;1;;1-1-1')
	),
	'palettes' => array (
		'1' => array('showitem' => 'fe_group'),
		'2' => Array('showitem' => 'imagealttext,imagetitletext')
	)
);

$TCA['tx_tdcalendar_organizer'] = array (
	'ctrl' => $TCA['tx_tdcalendar_organizer']['ctrl'],
	'interface' => array (
		'showRecordFieldList' => 'hidden,fe_group,name'
	),
	'feInterface' => $TCA['tx_tdcalendar_organizer']['feInterface'],
	'columns' => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		'fe_group' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.fe_group',
			'config'  => array (
				'type'  => 'select',
				'items' => array (
					array('', 0),
					array('LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.any_login', -2),
					array('LLL:EXT:lang/locallang_general.xml:LGL.usergroups', '--div--')
				),
				'foreign_table' => 'fe_groups'
			)
		),
		'name' => array (		
			'exclude' => 0,		
			'label' => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_organizer.name',		
			'config' => array (
				'type' => 'input',	
				'size' => '48',	
				'max' => '128',	
				'eval' => 'required',
			)
		),
		'description' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_organizer.description',		
			'config' => array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '5',
				'wizards' => array(
					'_PADDING' => 2,
					'RTE' => array(
						'notNewRecords' => 1,
						'RTEonly'       => 1,
						'type'          => 'script',
						'title'         => 'Full screen Rich Text Editing|Formatteret redigering i hele vinduet',
						'icon'          => 'wizard_rte2.gif',
						'script'        => 'wizard_rte.php',
					),
				),
			)
		),
		'street' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_organizer.street',		
			'config' => array (
				'type' => 'input',	
				'size' => '48',	
				'max' => '128',
			)
		),
		'zip' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_organizer.zip',		
			'config' => array (
				'type' => 'input',	
				'size' => '10',	
				'max' => '6',
			)
		),
		'city' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_organizer.city',		
			'config' => array (
				'type' => 'input',	
				'size' => '48',	
				'max' => '128',
			)
		),
		'phone' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_organizer.phone',		
			'config' => array (
				'type' => 'input',	
				'size' => '30',
			)
		),
		'email' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_organizer.email',		
			'config' => array (
				'type' => 'input',	
				'size' => '48',	
				'max' => '64',
			)
		),
		'image' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.images',
			'config' => Array (
				'type' => 'group',
				'internal_type' => 'file',
				'allowed' => $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext'],
				'max_size' => $GLOBALS['TYPO3_CONF_VARS']['BE']['maxFileSize'],
				'uploadfolder' => 'uploads/pics',
				'show_thumbs' => '1',
				'size' => 3,
				'autoSizeMax' => 15,
				'maxitems' => '99',
				'minitems' => '0'
			)
		),
		'imagecaption' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.caption',
			'config' => Array (
				'type' => 'text',
				'cols' => '30',
				'rows' => '3'
			)
		),
		'imagealttext' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar.imagealttext',
			'config' => Array (
				'type' => 'text',
				'cols' => '20',
				'rows' => '3'
			)
		),
		'imagetitletext' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar.imagetitletext',
			'config' => Array (
				'type' => 'text',
				'cols' => '20',
				'rows' => '3'
			)
		),	
		'link' => array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_organizer.link',		
			'config' => array (
				'type'     => 'input',
				'size'     => '15',
				'max'      => '255',
				'checkbox' => '',
				'eval'     => 'trim',
				'wizards'  => array(
					'_PADDING' => 2,
					'link'     => array(
						'type'         => 'popup',
						'title'        => 'Link',
						'icon'         => 'link_popup.gif',
						'script'       => 'browse_links.php?mode=wizard',
						'JSopenParams' => 'height=300,width=500,status=0,menubar=0,scrollbars=1'
					)
				)
			)
		),
	),
	'types' => array (
		'0' => array('showitem' => 
		
		'hidden, name, description;;;richtext[]:rte_transform[mode=ts], street, zip, city, phone, email, 
		--div--;LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar.tabs.media,image,;;;;1-1-1,imagecaption;;2;;, link,
		--div--;LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar.tabs.access ;;1;;1-1-1')
	),
	'palettes' => array (
		'1' => array('showitem' => 'fe_group'),
		'2' => Array('showitem' => 'imagealttext,imagetitletext')
	)
);

$TCA['tx_tdcalendar_exc_events'] = Array (
	'ctrl' => $TCA['tx_tdcalendar_exc_events']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => 'hidden,fe_group,begin,end,title,exc_categories,priority'
	),
	'feInterface' => $TCA['tx_tdcalendar_exc_events']['feInterface'],
	'columns' => Array (
		'hidden' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),
		'fe_group' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.fe_group',
			'config'  => array (
				'type'  => 'select',
				'items' => array (
					array('', 0),
					array('LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.any_login', -2),
					array('LLL:EXT:lang/locallang_general.xml:LGL.usergroups', '--div--')
				),
				'foreign_table' => 'fe_groups'
			)
		),
		'begin' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_exc_events.begin',
			'config' => Array (
				'type' => 'input',
				'size' => '8',
				'max' => '12',
				'eval' => 'required,date',
				'checkbox' => '0',
				'default' => '0'
			)
		),
		'end' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:td_calendar/locallang_db.xml:td_tdcalendar_exc_events.end',
			'config' => Array (
				'type' => 'input',
				'size' => '8',
				'max' => '12',
				'eval' => 'date',
				'checkbox' => '0',
				'default' => '0'
			)
		),
		'title' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_exc_events.title',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'required',
			)
		),
		'priority' => Array (		
			'exclude' => 1,		
			'label' => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_exc_events.priority',		
			'config' => Array (
				'type' => 'input',	
				'size' => '2',	
				'eval' => 'integer',
				'default' => '1',
			)
		),
		'exc_categories' => Array(
			'exclude' => 1,
			'label' => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_exc_events.exc_categories',
			'config' => Array (
				'type' => 'select',
				'foreign_table' => 'tx_tdcalendar_exc_categories',
				'foreign_table_where' => $includeExcCatMounts.'ORDER BY tx_tdcalendar_exc_categories.title',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
	),
	'types' => Array (
		'0' => Array('showitem' => 'hidden,title;;2;;1-1-1,exc_categories,priority,
									--div--;LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar.tabs.access ;;1;;1-1-1')
	),
	'palettes' => Array (
		'1' => Array('showitem' => 'fe_group'),
		'2' => Array('showitem' => 'begin,end')
	)
);

$TCA['tx_tdcalendar_exc_categories'] = Array (
	'ctrl' => $TCA['tx_tdcalendar_exc_categories']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => 'hidden,fe_group,title'
	),
	'feInterface' => $TCA['tx_tdcalendar_exc_categories']['feInterface'],
	'columns' => Array (
		'hidden' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.hidden',
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),
		'fe_group' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.fe_group',
			'config'  => array (
				'type'  => 'select',
				'items' => array (
					array('', 0),
					array('LLL:EXT:lang/locallang_general.xml:LGL.hide_at_login', -1),
					array('LLL:EXT:lang/locallang_general.xml:LGL.any_login', -2),
					array('LLL:EXT:lang/locallang_general.xml:LGL.usergroups', '--div--')
				),
				'foreign_table' => 'fe_groups'
			)
		),
		'title' => Array (
			'exclude' => 0,
			'label' => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_exc_categories.title',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'required',
			)
		),
		'color' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_exc_categories.color',
			'config' => Array (
				'type' => 'input',
				'size' => '7',
				'max' => '7',
				'wizards' => Array(
					'_PADDING' => 2,
					'color' => Array(
						'title' => 'Color:',
						'type' => 'colorbox',
						'dim' => '12x12',
						'tableStyle' => 'border:solid 1px black;',
						'script' => 'wizard_colorpicker.php',
						'JSopenParams' => 'height=300,width=250,status=0,menubar=0,scrollbars=1',
					),
				),
				'eval' => 'trim,nospace',
			)
		),
		'bgcolor' => Array (		
			'exclude' => 1,	
			'label' => 'LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar_exc_categories.bgcolor',		
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),
	),
	'types' => Array (
		'0' => Array('showitem' => 'hidden,title;;2;;1-1-1,--div--;LLL:EXT:td_calendar/locallang_db.xml:tx_tdcalendar.tabs.access ;;1;;1-1-1')
	),
	'palettes' => Array (
		'1' => Array('showitem' => 'fe_group'),
		'2' => Array('showitem' => 'color, bgcolor')
	)
);

?>