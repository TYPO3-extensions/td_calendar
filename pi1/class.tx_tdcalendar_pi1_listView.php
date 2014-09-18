<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Thomas Dudzak <thomas@buergerbuero-borna.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   37: class tx_tdcalendar_pi1_listView extends tx_tdcalendar_pi1_library
 *   44:     function displayList()
 *   82:     function listTable($events)
 *
 * TOTAL FUNCTIONS: 2
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */
 class tx_tdcalendar_pi1_listView extends tx_tdcalendar_pi1_library {

  	/**
 * Diplay List Function: initialize upcoming list view of calendar and renders view
 *
 * @return	string		rendered list view
 */
	function displayList() {
		$this->fetchCurrValue('PIDeventDisplay', '0', 'sDEF', 1);
		$this->fetchCurrValue('listStartTime', time(), 'sDEF', 1);
		$this->fetchCurrValue('listStartTime', $this->conf['currTime'], 'sDEF', 1);
		$this->fetchCurrValue('hideViewSelection', '0', 'sDEF');
		$this->fetchCurrValue('PIDsingleDayDisplay', '0', 'sDEF');
		$this->fetchCurrValue('PIDweekDisplay', '0', 'sDEF');
		$this->fetchCurrValue('PIDmonthDisplay', '0', 'sDEF');
		$this->fetchCurrValue('listEntryCount', 5, 'sDEF', 1);
		$this->fetchCurrValue('PIDallEventsDisplay', 0, 'sDEF', 1);

		$this->conf['showMultiDayOnlyOnce'] = 1;

		$this->listTemplateCode = $this->cObj->getSubpart($this->templateCode, '###LIST_VIEW###');
		$this->itemTemplateCode = $this->cObj->getSubpart($this->listTemplateCode, '###ITEM###');

		$markerArray = array();
		$subpartsArray = array();

		$markerArray['###VIEW_TITLE###'] = $this->pi_getLL('titleListView');
		$markerArray['###CATEGORY_TITLE###'] = $this->getCategorySelection();
		$entries = $this->getUpcomingEventsArray($this->conf['listStartTime']);
		/************
		 * Changed Rendering of item list - START - Sept., 9th 2014
		 ************/ 
		// $subpartsArray['###ITEM###'] = $this->listTable($entries);

		$swing = $this->listTable($entries);
		$subpartsArray['###ITEM###'] = $swing[0];
		$firstEventTime = $swing[1];
		$nextEventTime = $swing[2];
		// $markerArray['###VIEW_LIST###'] = $this->getViewSelection($this->conf['listStartTime'],'l');
		$markerArray['###VIEW_LIST###'] = $this->getViewSelection($firstEventTime,'l');

		$markerArray['###ALL_EVENTS###'] = $this->conf['PIDallEventsDisplay'] ? $this->pi_linkTP($this->pi_getLL('allEvents'), array(), $this->caching, $this->conf['PIDallEventsDisplay']) : '';

		$mvars = array();
		$mvars['year']=strftime('%Y', $nextEventTime);
		$mvars['month']= strftime('%m', $nextEventTime);
		$mvars['day']= strftime('%d', $nextEventTime);
		//	if ($nextEventTime == 0 OR ($this->conf['PIDallEventsDisplay'] ? $this->pi_linkTP($this->pi_getLL('allEvents'), array(), $this->caching, $this->conf['PIDallEventsDisplay']) : '') == '') {
		if ($nextEventTime == 0) {
	                $markerArray['###NEXTLIST###'] = '';
		} else {
			$markerArray['###NEXTLIST###'] = $this->pi_linkTP_keepPIvars($this->pi_getLL('next'), $mvars, $this->caching);
		}
		/************
		 * Changed Rendering of item list - END - Sept., 9th 2014
		 ************/ 

		$markerArray['###SUBMIT_EVENT###'] = ''; // That's where we will build the FE_Edit

		$out =  $this->cObj->substituteMarkerArrayCached($this->listTemplateCode, $markerArray, $subpartsArray, array());
		if ($this->conf['showTooltips'] == 1) {
			$out .= $this->tooltip;
		}

		return $out;
	}

	/**
	 * list Table function : rendering of upcoming list table
	 *
	 * @param	array		$events: array of calendar entries from DB
	 * @return	array		including rendered upcoming list table, start- and endtime
	 */
	function listTable($events) {
		$lastEventTime = 0;
		$firstEventTime = 0;
		$i = 0;
		foreach ($events as $event => $key) {
			if ($firstEventTime == 0) {
				$firstEventTime = $key['begin'];
			}
			if ($key['begin'] >= $this->conf['listStartTime'] OR $key['end'] >= $this->conf['listStartTime']) {
				if($i == $this->conf['listEntryCount']) {
					$lastEventTime = $key['begin'];
					break;
				}
				// $markerArray['###TITLE###']  = $key['title'];
				$markerArray['###TITLE###']  = $this->makeEventLink($key, $key['begin']);
				
				$markerArray['###DATE_BEGIN###'] = strftime($this->conf['dateFormat'], $key['begin']);
				$markerArray['###TIME_BEGIN###'] = $key['allday'] == 0 ? strftime($this->conf['timeFormat'], $key['begin']) : $this->pi_getLL('alldayLabel');
				$markerArray['###CATEGORY###'] = $key['category'];
				$markerArray['###CATCOLOR###'] = $key['catcolor'];
				$markerArray['###ODD_EVEN###'] = $i%2==0 ? 'even' : 'odd';
				
				$markerArray['###LOCATION###'] = !empty($key['location_name']) ? $key['location_name'] : $key['location'];
				$markerArray['###ORGANIZER###'] = !empty($key['organizer_name']) ? $key['organizer_name'] : $key['organizer'];

				$markerArray['###READ_MORE###']  = $this->makeEventLink($key, $key['begin'], $this->pi_getLL('readMore'), 1);

				if($key['teaser']) {
					$markerArray['###TEASER###']  = $this->cObj->crop(strip_tags($key['teaser']), $this->conf['croppingLenght']);
				} else
					$markerArray['###TEASER###'] = $key['description'] ? $this->cObj->crop(strip_tags($key['description']), $this->conf['croppingLenght']):'';

				$content .= $this->cObj->substituteMarkerArrayCached($this->itemTemplateCode, $markerArray, $subpartsArray, array());

				$lastEventTime = 0;

				$i++;
			}
		}

		// return $content;
		return array($content,$firstEventTime,$lastEventTime);
	}

 }

 if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/td_calendar/pi1/class.tx_tdcalendar_pi1_listView.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/td_calendar/pi1/class.tx_tdcalendar_pi1_listView.php']);
}

 ?>