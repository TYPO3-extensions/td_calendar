<?php
if (!defined ('TYPO3_MODE')) {
 	die ('Access denied.');
}
t3lib_extMgm::addUserTSConfig('
	options.saveDocNew.tx_tdcalendar_events=1
	options.saveDocNew.tx_tdcalendar_categories=1
	options.saveDocNew.tx_tdcalendar_locations=1
	options.saveDocNew.tx_tdcalendar_organizer=1
	options.saveDocNew.tx_tdcalendar_exc_events=1
	options.saveDocNew.tx_tdcalendar_exc_categories=1
');

t3lib_extMgm::addPItoST43($_EXTKEY, 'pi1/class.tx_tdcalendar_pi1.php', '_pi1', 'list_type', 1);

$TYPO3_CONF_VARS['EXTCONF']['cms']['db_layout']['addTables']['tx_tdcalendar_events'][0] =
                array('fList' => 'begin,end,title,category',
                      'icon'  => 1,
                );
?>