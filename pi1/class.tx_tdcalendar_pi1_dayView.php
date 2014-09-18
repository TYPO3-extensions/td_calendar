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
 *   39: class tx_tdcalendar_pi1_dayView extends tx_tdcalendar_pi1_library
 *   46:     function displayDay()
 *  114:     function dayTable($firstDay,$lastDay,$entries,$exc_entries)
 *  155:     function hourTable($firstDay,$lastDay,$events,$exc_events)
 *  247:     function listTable($events,$exc_events)
 *
 * TOTAL FUNCTIONS: 4
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */
 class tx_tdcalendar_pi1_dayView extends tx_tdcalendar_pi1_library {

 	/**
 * Diplay Day Function: initialize Day view of calendar and renders day view
 *
 * @return	string		$out: rendered day view
 */
	function displayDay() {
		$this->fetchCurrValue('dateFormat', $this->pi_getLL('stdDateFormat'), 's_misc');
		$this->fetchCurrValue('PIDeventDisplay', '0', 'sDEF', 1);
		$this->fetchCurrValue('showEventBegin', '0', 'sDEF');
		$this->fetchCurrValue('showAsList', 0, 'sDEF', 1);
		$this->fetchCurrValue('additionalColumnAtEnd', '0', 'sDEF');
		$this->fetchCurrValue('hideExcEvents', '0', 'sDEF');
		$this->fetchCurrValue('hideViewSelection', '0', 'sDEF');
		$this->fetchCurrValue('PIDlistDisplay', '0', 'sDEF');
		$this->fetchCurrValue('PIDweekDisplay', '0', 'sDEF');
		$this->fetchCurrValue('PIDmonthDisplay', '0', 'sDEF');

		$this->conf['start_t']=$this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'startHour', 'sDEF');
		if(!$this->conf['start_t'] || $this->conf['start_t']<0 || $this->conf['start_t']>24) {
			$this->conf['start_t']=0;
		}

		$this->conf['stop_t']=$this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'endHour', 'sDEF');
		if(!$this->conf['stop_t'] || $this->conf['stop_t'] > 24) {
			$this->conf['stop_t']=24;
		}

		$this->dayTemplateCode = $this->cObj->getSubpart($this->templateCode, '###DAY_VIEW###');

		$subpartsArray = array(); // Region markers
		$markerArray = array(); // Simple markers

		$day_first = strtotime(strftime('%Y-%m-%d', $this->conf['currTime']));
		$day_last  = strtotime(strftime('%Y-%m-%d', $day_first).'+1day')-1;

		$exc_entries = $this->getExceptionEventsArray($day_first, $day_last+1);
		$entries = $this->getEventsArray($day_first, $day_last+1, $exc_entries);

		$markerArray['###VIEW_TITLE###']=$this->pi_getLL('titleDayView');

		$vars['year']=strftime('%Y', $day_first-1);
		$vars['month']=strftime('%m', $day_first-1);
		$vars['day']= strftime('%d', $day_first-1);
		$vars['uid'] = $this->uid;
		if(!empty($this->conf['currCat'])) $vars['category'] = $this->conf['currCat'];

		$markerArray['###PREW_DAY###']=$this->pi_linkTP($this->pi_getLL('day_prev'), array($this->prefixId=>$vars), $this->caching);

		$vars['year']=strftime('%Y', $day_last+1);
		$vars['month']=strftime('%m', $day_last+1);
		$vars['day']= strftime('%d', $day_last+1);
		$markerArray['###NEXT_DAY###']=$this->pi_linkTP($this->pi_getLL('day_next'), array($this->prefixId=>$vars), $this->caching);
		$markerArray['###TIME_INFO###']=strftime($this->conf['dateFormat'], $day_first);
		$markerArray['###CATEGORY_TITLE###'] = $this->getCategorySelection($this->conf['currTime']);

		$markerArray['###VIEW_LIST###'] = $this->getViewSelection($day_first,'d');

		$subpartsArray['###DAY_TABLE###'] = $this->dayTable($day_first, $day_last, $entries, $exc_entries);

		$markerArray['###SUBMIT_EVENT###'] = ''; // That's where we will build the FE_Edit

		$out =  $this->cObj->substituteMarkerArrayCached($this->dayTemplateCode, $markerArray, $subpartsArray, array());

		if ($this->conf['showTooltips'] == 1) {
			$out .= $this->tooltip;
		}

		return $out;
	}

	/**
	 * Day Table function : rendering of day table
	 *
	 * @param	timestamp		$firstDay : start time of dayview as timestamp
	 * @param	timestamp		$lastDay: end time of dayview as timestamp
	 * @param	array		$entries: array of calendar entries from DB
	 * @param	[type]		$exc_entries: ...
	 * @return	string		rendered day table
	 */
	function dayTable($firstDay,$lastDay,$entries,$exc_entries) {
		$this->dayCode = $this->cObj->getSubpart($this->dayTemplateCode, '###DAY_TABLE###');
		$this->excEventsCode = $this->cObj->getSubpart($this->dayTemplateCode, '###EXCEVENTS###');
		$this->emptyTimeCode = $this->cObj->getSubpart($this->dayTemplateCode, '###EMPTY_TIME###');
		$this->excEventCode = $this->cObj->getSubpart($this->dayTemplateCode, '###EXCEVENT###');
		$this->excEmptyCode = $this->cObj->getSubpart($this->dayTemplateCode, '###EMPTY_CELL###');
		$this->hoursCode = $this->cObj->getSubpart($this->dayTemplateCode, '###HOURS###');
		$this->timeCode = $this->cObj->getSubpart($this->dayTemplateCode, '###TIME###');
		$this->eventCode = $this->cObj->getSubpart($this->dayTemplateCode, '###EVENT###');
		$this->emptyCode = $this->cObj->getSubpart($this->dayTemplateCode, '###EMPTY_CELL###');

		$m = strftime('%m', $firstDay);
		$d = strftime('%d', $firstDay);
		
		if($exc_entries[$m][$d]) {
			usort($exc_entries[$m][$d], array($this, 'excSort'));
			$exc_events = $exc_entries[$m][$d];
		} else $exc_events = '';

		if(is_array($entries[$m][$d])) {
			usort($entries[$m][$d], array($this,'extSort'));
			$events = $entries[$m][$d];
		} else $events = ''; 
		
		if ($this->conf['showAsList']== 0)
			$subpartsArray['###HOURS###'] .= $this->hourTable($firstDay, $lastDay, $events, $exc_events);

		else
			$subpartsArray['###HOURS###'] .= $this->listTable($events, $exc_events);

		 $subpartsArray['###EXCEVENTS###'] = '';

		return $this->cObj->substituteMarkerArrayCached($this->dayCode, array(), $subpartsArray, array());
	}

	/**
	 * Hour Table function : rendering of table by hours if listview is not selected
	 *
	 * @param	timestamp		$firstDay : start time of dayview as timestamp
	 * @param	timestamp		$lastDay: end time of dayview as timestamp
	 * @param	array		$entries: array of calendar entries from DB
	 * @param	array		$exc_events: array of calendar exception events from DB
	 * @return	string		rendered table by hours
	 */
	function hourTable($firstDay,$lastDay,$events,$exc_events) {
		$start_t = $this->conf['start_t'];
		$stop_t = $this->conf['stop_t'];

		$hour = array();
		if(!is_array($events)) {
			$events = array();
		}
		$event_t = array();

		// exc_events color as background
		if(is_array($exc_events)) {
			reset($exc_events);
			$first_exc_event = current($exc_events);
			$exc_color = $first_exc_event['bgcolor'] == 1 ? 'background: '.$first_exc_event['color'].';' : '';
		}
		
		foreach($events as $key=>$value) {
			if ($value['allday']>0) {
				$b = 0;
				$e = 24;
			} else {
				$b	= strftime('%H', $value['begin']);

				if(!$value['end'] OR $value['end'] <= $value['begin']) {
					$e = $b+1;
				} elseif($value['end'] > $lastDay) {
					$e=24;
				}else {
					$e	= strftime('%H', $value['end']);
				}
			}

			for($i=0; $i<24; $i++) {
				if ($b<=$i AND $i<$e) {
					$hour[$i]++;
				}
			}

			$event_t[$key]['hBegin'] = $b;
			$event_t[$key]['hEnd'] = $e;
		}

		if(is_array($hour)) {
			if (sizeof($hour)>0) {
				$maxPerHour = max($hour);
			} else {
				$maxPerHour = 0;
			}
		}
		
		if ($this->conf['additionalColumnAtEnd']==1) $maxPerHour++;

		if(is_array($exc_events) AND !$this->conf['hideExcEvents']) {
			$markerArray['###DATA###'] = $this->buildExcEvents($exc_events);
			$markerArray['###COLSPAN###'] = $maxPerHour;
			$markerArray['###STYLE###'] = 'style="'.$exc_color.'"';
			$subpartsArray['###EMPTY_CELL###'] = '';
			$subpartsArray['###EXCEVENT###'] = $this->cObj->substituteMarkerArrayCached($this->excEventCode, $markerArray, $subpartsArray);
			$subpartsArray['###HOURS###'] .= $this->cObj->substituteMarkerArrayCached($this->excEventsCode, $markerArray, $subpartsArray);
		}

		for($h = $start_t; $h< $stop_t; $h++) {
			$t = $maxPerHour!= 0 ? $maxPerHour - $hour[$h]: 1;
			//else
			//	$t = $maxPerHour!= 0 ? 1+ $maxPerHour - $hour[$h]: 1;

			$subpartsArray['###EMPTY_CELL###'] = '';
			$subpartsArray['###EVENT###'] = '';

			$markerArray['###DATA###'] = $h;
			$subpartsArray['###TIME###'] = $this->cObj->substituteMarkerArrayCached($this->timeCode, $markerArray, $subpartsArray);
			foreach($event_t as $event => $time) {
				if(($time['hBegin'] == $h) OR ($h == $start_t AND $time['hBegin'] < $start_t AND $time['hEnd'] > $start_t)) {
					$hEnd = $time['hEnd'] > $stop_t ? $stop_t : $time['hEnd'];
					$day = $events[$event]['mDay'] ? $events[$event]['mDay'] : $events[$event]['begin'];
					$rowspan = ($hEnd - $h) == 0 ? 1 : $hEnd - $h;
					$markerArray['###DATA###'] = $this->makeEventLink($events[$event], $day);
					$markerArray['###CATEGORY###']= $events[$event]['category'];
					$markerArray['###COLOR###'] = $events[$event]['catcolor'];
					$markerArray['###STYLE###'] = ' style="border-color: '.$events[$event]['catcolor'].';"';
					$markerArray['###ROWSPAN###'] = $rowspan;
					$subpartsArray['###EVENT###'] .= $this->cObj->substituteMarkerArrayCached($this->eventCode, $markerArray, $subpartsArray);
				}
			}

			while ($t > 0) {
				$markerArray['###STYLE###'] = $exc_color ? 'style="'.$exc_color.'"' : '';
				$subpartsArray['###EMPTY_CELL###'] .= $this->cObj->substituteMarkerArrayCached($this->emptyCode, $markerArray, array());
				$t--;
			}
			$subpartsArray['###HOURS###'] .= $this->cObj->substituteMarkerArrayCached($this->hoursCode, $markerArray, $subpartsArray);
		}

		return $subpartsArray['###HOURS###'];
	}

	/**
	 * Hour Table function : rendering of table by hours if listview is selected
	 *
	 * @param	array		$entries: array of calendar entries from DB
	 * @param	array		$exc_events: array of calendar exception events from DB
	 * @return	string		rendered table as list
	 */
	function listTable($events,$exc_events){
		// exc_events color as background
		if(is_array($exc_events) AND !$this->conf['hideExcEvents']) {
			reset($exc_events);
			$first_exc_event = current($exc_events);
			$exc_color = $first_exc_event['bgcolor'] == 1 ? 'background: '.$first_exc_event['color'].';' : '';

			$markerArray['###DATA###'] = $this->buildExcEvents($exc_events);
			$markerArray['###COLSPAN###'] = 1;
			$markerArray['###STYLE###'] = 'style="'.$exc_color.'"';
			$subpartsArray['###EMPTY_TIME###'] = '';
			$subpartsArray['###EMPTY_CELL###'] = '';
			$subpartsArray['###EXCEVENT###'] = $this->cObj->substituteMarkerArrayCached($this->excEventCode, $markerArray, $subpartsArray);
			$subpartsArray['###HOURS###'] .= $this->cObj->substituteMarkerArrayCached($this->excEventsCode, $markerArray, $subpartsArray);
		}

		$subpartsArray['###EMPTY_CELL###'] = '';
		$subpartsArray['###TIME###'] = '';

		foreach($events as $event => $key) {
			$day = $key['mDay'] ? $key['mDay'] : $key['begin'];

			$markerArray = array();

			$markerArray['###DATA###'] = $this->makeEventLink($key, $day);
			$markerArray['###CATEGORY###']= $key['category'];
			$markerArray['###COLOR###'] = $key['catcolor'];
			$markerArray['###STYLE###'] = ' style="border-color: '.$key['catcolor'].';"';
			$markerArray['###ROWSPAN###'] = 1;

			$subpartsArray['###EVENT###'] = $this->cObj->substituteMarkerArrayCached($this->eventCode, $markerArray, $subpartsArray);

			$subpartsArray['###HOURS###'] .= $this->cObj->substituteMarkerArrayCached($this->hoursCode, $markerArray, $subpartsArray);
		}

		return $subpartsArray['###HOURS###'];

	}
 }

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/td_calendar/pi1/class.tx_tdcalendar_pi1_dayView.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/td_calendar/pi1/class.tx_tdcalendar_pi1_dayView.php']);
}
?>