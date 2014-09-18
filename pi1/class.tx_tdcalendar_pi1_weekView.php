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
 *   38: class tx_tdcalendar_pi1_weekView extends tx_tdcalendar_pi1_library
 *   44:     function displayWeek()
 *  123:     function weekTable($firstDay,$lastDay,$entries,$exc_entries)
 *  181:     function dayTable($day,$events,$exc_events)
 *
 * TOTAL FUNCTIONS: 3
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */
 class tx_tdcalendar_pi1_weekView extends tx_tdcalendar_pi1_library {
   	/**
 * Diplay Week Function: initialize week view of calendar and renders week view
 *
 * @return	string		$out: rendered week view
 */
	function displayWeek() {
		$this->fetchCurrValue('dateFormat', $this->pi_getLL('stdDateFormat'), 's_misc');
		$this->fetchCurrValue('PIDeventDisplay', '0', 'sDEF', 1);
		$this->fetchCurrValue('showAsList', 0, 'sDEF', 1);
		$this->fetchCurrValue('showEventBegin', '0', 'sDEF');
		$this->fetchCurrValue('hideExcEvents', '0', 'sDEF');
		$this->fetchCurrValue('PIDlistDisplay', '0', 'sDEF');
		$this->fetchCurrValue('hideViewSelection', '0', 'sDEF');
		$this->fetchCurrValue('PIDsingleDayDisplay', '0', 'sDEF');
		$this->fetchCurrValue('PIDmonthDisplay', '0', 'sDEF');


		if ( $this->conf['multiplyRowspan'] > 0 AND is_numeric($this->conf['multiplyRowspan']))
			$this->conf['multiplyRowspan'];
		else
			$this->conf['multiplyRowspan'] = 2;

		$this->conf['start_t']=$this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'startHour', 'sDEF');
		if(!$this->conf['start_t'] || $this->conf['start_t']<0 || $this->conf['start_t']>24) {
			$this->conf['start_t']=0;
		}

		$this->conf['stop_t']=$this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'endHour', 'sDEF');
		if(!$this->conf['stop_t'] || $this->conf['stop_t'] > 24) {
			$this->conf['stop_t']=24;
		}

		/*$this->conf['max_t']=$this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'maxHours','sDEF');
		if(!$this->conf['max_t']) {
			$this->conf['max_t']=24;
		}*/

		$this->weekTemplateCode = $this->cObj->getSubpart($this->templateCode, '###WEEK_VIEW###');

		$subpartsArray = array(); // Region markers
		$markerArray = array(); // Simple markers

		$daynr = strftime('%w', $this->conf['currTime']) == 0 ? 6 : strftime('%w', $this->conf['currTime'])-1;
		$week_first = strtotime(strftime('%Y-%m-%d', $this->conf['currTime']).'-'.$daynr.'days');
		$week_last  = strtotime(strftime('%Y-%m-%d', $week_first).'+1week')-1;
		$exc_entries = $this->getExceptionEventsArray($week_first, $week_last+1);
		$entries = $this->getEventsArray($week_first, $week_last+1, $exc_entries);

		/* create week links */
		$markerArray['###VIEW_TITLE###']=$this->pi_getLL('titleWeekView');
		$firstEventTime = 0;
		foreach($entries as $m => $d) {
			foreach($d as $key => $value) {
				foreach($value as $key => $reach) {
					if ($firstEventTime == 0) {
						$firstEventTime = $reach['begin'];
						break 3;
					}
				}
			}
		}

		$mvars = array();
		if ($firstEventTime == 0) {
			$firstEventTime = $this->conf['currTime'];
		}
		$mvars['year']=strftime('%Y', $firstEventTime);
		$mvars['month']= strftime('%m', $firstEventTime);
		$mvars['day']= strftime('%d', $firstEventTime);
		($this->conf['PIDlistDisplay'] == 0) ? $markerArray['###LISTVIEW###'] = '' : $markerArray['###LISTVIEW###'] = $this->pi_linkTP_keepPIvars($this->pi_getLL('titleListView'), $mvars, $this->caching, 0, $this->conf['PIDlistDisplay']);
		$markerArray['###VIEW_LIST###'] = $this->getViewSelection($firstEventTime,'w');
		$markerArray['###CATEGORY_TITLE###'] = $this->getCategorySelection($this->conf['currTime']);

		$markerArray['###TIME_INFO###']=$markerArray['###TIME_INFO###']= strftime($this->conf['dateFormat'], $week_first).' - '.strftime($this->conf['dateFormat'], $week_last);
		$vars=array();
		$vars['year']=strftime('%Y', $week_first-1);
		$vars['month']=strftime('%m', $week_first-1);
		$vars['day']= strftime('%d', $week_first-1);
		$vars['uid'] = $this->uid;
		if(!empty($this->conf['currCat'])) $vars['category'] = $this->conf['currCat'];

		$markerArray['###PREW_WEEK###']=$this->pi_linkTP($this->pi_getLL('week_prev'), array($this->prefixId=>$vars), $this->caching);

		$vars['year']=strftime('%Y', $week_last+1);
		$vars['month']=strftime('%m', $week_last+1);
		$vars['day']= strftime('%d', $week_last+1);
		$markerArray['###NEXT_WEEK###']=$this->pi_linkTP($this->pi_getLL('week_next'), array($this->prefixId=>$vars), $this->caching);

		$markerArray['###SUBMIT_EVENT###'] = ''; // That's where we will build the FE_Edit

		$subpartsArray['###WEEK_TABLE###'] = $this->weekTable($week_first, $week_last, $entries, $exc_entries);

		$out = $this->cObj->substituteMarkerArrayCached($this->weekTemplateCode, $markerArray, $subpartsArray, array());

		if ($this->conf['showTooltips'] == 1) {
			$out .= $this->tooltip;
		}

		return $out;
	}

	/**
	 * Week Table function : rendering of week table
	 *
	 * @param	timestamp		$firstDay : first day of week as timestamp
	 * @param	timestamp		$lastDay: last day of week as timestamp
	 * @param	array		$entries: events for current week as array
	 * @param	[type]		$exc_entries: ...
	 * @return	string		rendered week table
	 */
	function weekTable($firstDay,$lastDay,$entries,$exc_entries) {
		/* load main template */
		//$exc_color_events =$this->setExcEventBgColor($firstDay,$lastDay+1);
		$weekTableCode= $this->cObj->getSubpart($this->weekTemplateCode, '###WEEK_TABLE###');
		$moment = $firstDay;
		//$moment = date();
    	$markerArray = array();
		$subpartsArray = array();
		$timesCode = $this->cObj->getSubpart($weekTableCode, '###TIMES###');
		$this->timeCode = $this->cObj->getSubpart($timesCode, '###TIME###');
		$this->daysCode = $this->cObj->getSubpart($weekTableCode, '###DAYS###');

		$this->dayCode = $this->cObj->getSubpart($this->daysCode, '###DAY###');
		$this->dayeventCode = $this->cObj->getSubpart($this->daysCode, '###DAYEVENT###');
		$this->eventCode = $this->cObj->getSubpart($this->dayeventCode, '###EVENT###');
		$this->excEventCode = $this->cObj->getSubpart($this->dayeventCode, '###EXCEVENT###');
		$this->eventTimeCode = $this->cObj->getSubpart($this->dayeventCode, '###EVENT_TIME###');
		$this->emptyCellCode = $this->cObj->getSubpart($this->dayeventCode, '###EMPTY_CELL###');

		if ($this->conf['showAsList'] != 1) {
			for($i=$this->conf['start_t'];$i < $this->conf['stop_t'];$i++) {
				$markerArray['###WIDTH###'] = $width;
				$markerArray['###DATA###']=$i;
				$subpartsArray['###TIME###'] .= $this->cObj->substituteMarkerArrayCached($this->timeCode, $markerArray);
			}
			$subpartsArray['###TIMES###'] = $this->cObj->substituteMarkerArrayCached($timesCode, $markerArray, $subpartsArray);
		} else {
			$subpartsArray['###TIMES###'] = '';
		}

		//$subpartsArray['###TIMES###'] = $this->cObj->substituteMarkerArrayCached($timesCode,$markerArray, $subpartsArray);
		$markerArray = array(); // Region markers

		for($moment; $moment < $lastDay; $moment = strtotime('+1 day', $moment)) {
    		$m = strftime('%m', $moment);
			$d = strftime('%d', $moment);

			if(is_array($exc_entries[$m][$d])) {
				usort($exc_entries[$m][$d], array($this,'extSort'));
				$exc_events = $exc_entries[$m][$d];
				//$exc_events['category'] = $exc_entries['category'];
			} else $exc_events = ''; 
			
			if(is_array($entries[$m][$d])) {
				usort($entries[$m][$d], array($this,'extSort'));
				$events = $entries[$m][$d]; //is_array($entries[$m][$d]) ?  : array();
			} else $events = ''; 
			
			$subpartsArray['###DAYS###'] .= $this->dayTable($moment, $events, $exc_events);
		}

		return $this->cObj->substituteMarkerArrayCached($weekTableCode, array(), $subpartsArray, array());
	}

	/**
	 * Day Table function : rendering of day rows of week table
	 *
	 * @param	timestamp		$day : current day as timestamp
	 * @param	array		$entries: events for current day as array
	 * @param	array		$exc_events: exception events for curent day as array
	 * @return	string		rendered single day row for week table
	 */
	function dayTable($day,$events,$exc_events) {
		$d = 0;
		$rowspan = is_array($events) ? count($events)*$this->conf['multiplyRowspan'] : 0;
		
		$start_t = $this->conf['start_t'];
	   	$stop_t = $this->conf['stop_t'];

		$day_first = $day;
		$day_last =  strtotime(strftime('%Y-%m-%d', $day_first).'+1day')-1;
		
		if(is_array($exc_events)) {
			reset($exc_events);
			$first_exc_event = current($exc_events);
			$exc_color = $first_exc_event['bgcolor'] == 1 ? 'background: '.$first_exc_event['color'].';' : '';
		}

		if(is_array($exc_events) AND !$this->conf['hideExcEvents']) {
			$col=$stop_t-$start_t;
			$rowspan = $rowspan + 1;

			$markerArray['###ROWSPAN###'] = $rowspan;
			$markerArray['###DATA###'] = strftime('%A', $day);
			$subpartsArray['###DAYEVENT###'] = $this->cObj->substituteMarkerArrayCached($this->dayCode, $markerArray);
			$d++;

			$markerArray['###COLSPAN###'] = !$this->conf['showAsList'] ? $col : 1;
			$markerArray['###DATA###'] = $this->buildExcEvents($exc_events);
			$markerArray['###STYLE###'] = 'style="'.$exc_color.'"';
			$subpartsArray['###DAYEVENT###'] .= $this->cObj->substituteMarkerArrayCached($this->excEventCode, $markerArray);

			$out = $this->cObj->substituteMarkerArrayCached($this->daysCode, $markerArray, $subpartsArray);
		}

		if (is_array($events)) {
			foreach($events as $event) {
				$subpartsArray['###DAYEVENT###'] = '';

				if ($d == 0) {
					$markerArray['###ROWSPAN###'] = $rowspan;
					$markerArray['###DATA###'] = strftime('%A', $day);
					$subpartsArray['###DAYEVENT###'] .= $this->cObj->substituteMarkerArrayCached($this->dayCode, $markerArray);
					$d++;
				}

				if ($this->conf['showAsList'] != 1) {
					$start_event = $event['mDay'] ? strtotime(strftime('%Y-%m-%d', $event['mday'])) : strtotime(strftime('%Y-%m-%d', $event['begin']));
					$stop_event = $event['end'] ? strtotime(strftime('%Y-%m-%d', $event['end'])): 0;

					if ($event['allday'] > 0) {
						$start_e = 0;
						$stop_e = 24;
					} else {
						if ($start_event < $day_first) {
							$start_e = 0;
						} else {
							$start_e = strftime('%H', $event['begin']);
						}

						if ($stop_event > 0) {
							if ($stop_event > $day_last) {
								$stop_e = 24;
							} else {
								$stop_e = strftime('%H', $event['end']);
							}
						} else {
							$stop_e = $start_e + 1;
						}
					}

					$col_first = $start_e > $start_t ? $start_e-$start_t : 0;

					if ($stop_e < $stop_t) {
						$col_eventTime = $stop_e > $start_t ? $stop_e - $col_first - $start_t : 0;
					} else {
						$col_eventTime = $stop_t - $col_first - $start_t;
					}

					$col_event = $stop_t - $col_first - $start_t;
					$col_last = $stop_e < $stop_t ? $stop_t-$col_first-$col_eventTime-$start_t: 0;
				} else {
					$col_eventTime = 1;
					$col_event = 1;
				}

				if ($col_first > 0) {
					$markerArray['###COLSPAN###'] = $col_first;
					$markerArray['###CLASS###'] = 'weekTimeEmptyBefore';
					$markerArray['###STYLE###'] = 'style = "border-color: '.$event['catcolor'].';'.$exc_color.'"';
					$subpartsArray['###DAYEVENT###'] .= $this->cObj->substituteMarkerArrayCached($this->emptyCellCode, $markerArray);
				}

				if ($col_eventTime > 0) {
					$markerArray['###COLSPAN###'] = $col_eventTime;
					$markerArray['###DATA###'] = $event['category'];
					$markerArray['###STYLE###'] = 'style="background:'.$event['catcolor'].'; border-color:'.$event['catcolor'].';"';
					$subpartsArray['###DAYEVENT###'] .= $this->cObj->substituteMarkerArrayCached($this->eventTimeCode, $markerArray);
				}

				if ($col_last > 0) {
					$markerArray['###COLSPAN###'] = $col_last;
					$markerArray['###CLASS###'] = 'weekTimeEmptyAfter';
					$markerArray['###STYLE###'] = 'style = "border-color: '.$event['catcolor'].';'.$exc_color.'"';
					$subpartsArray['###DAYEVENT###'] .= $this->cObj->substituteMarkerArrayCached($this->emptyCellCode, $markerArray);
				}

				$out .= $this->cObj->substituteMarkerArrayCached($this->daysCode, $markerArray, $subpartsArray);

				$subpartsArray['###DAYEVENT###'] = '';
				if ($col_first > 0) {
					$markerArray['###COLSPAN###'] = $col_first;
					$markerArray['###CLASS###'] = 'weekEmptyCell';
					$markerArray['###STYLE###'] = $exc_color ? 'style="'.$exc_color.'";' : '';
					$subpartsArray['###DAYEVENT###'] .= $this->cObj->substituteMarkerArrayCached($this->emptyCellCode, $markerArray);
				}

				$markerArray['###COLSPAN###'] = $col_event;
				$markerArray['###DATA###'] = $this->makeEventLink($event, $day);
				$markerArray['###STYLE###'] = 'style="border-color:'.$event['catcolor'].';'.$exc_color.'"';
				$subpartsArray['###DAYEVENT###'] .= $this->cObj->substituteMarkerArrayCached($this->eventCode, $markerArray);

				$out .= $this->cObj->substituteMarkerArrayCached($this->daysCode, $markerArray, $subpartsArray);

			}

		} elseif(!is_array($exc_events) OR $this->conf['hideExcEvents']) {

			$markerArray['###ROWSPAN###'] = 1;
			$markerArray['###DATA###'] = strftime('%A', $day);
			$subpartsArray['###DAYEVENT###'] = $this->cObj->substituteMarkerArrayCached($this->dayCode, $markerArray);

			$markerArray['###COLSPAN###'] = $this->conf['showAsList'] != 1 ? $stop_t - $start_t : 1;
			$markerArray['###CLASS###'] = 'weekEmptyRow';
			$markerArray['###STYLE###'] = $exc_color? 'style="'.$exc_color.'"' : '';
			$subpartsArray['###DAYEVENT###'] .= $this->cObj->substituteMarkerArrayCached($this->emptyCellCode, $markerArray);

			$out .= $this->cObj->substituteMarkerArrayCached($this->daysCode, $markerArray, $subpartsArray);
		}

		return $out;
	}

 }

 if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/td_calendar/pi1/class.tx_tdcalendar_pi1_weekView.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/td_calendar/pi1/class.tx_tdcalendar_pi1_weekView.php']);
}
 ?>