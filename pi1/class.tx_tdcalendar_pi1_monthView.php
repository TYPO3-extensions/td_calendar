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
 *   43: class tx_tdcalendar_pi1_monthView extends tx_tdcalendar_pi1_library
 *   49:     function displayMonth()
 *  116:     function monthsNavi()
 *  164:     function calendarTable()
 *  198:     function calendarTableCol($mondayPre,$mondayPost,$entries,$exc_entries)
 *  264:     function calendarTableRow($mondayPre,$mondayPost,$entries,$exc_entries)
 *  323:     function getDayEvents($day,$col,$entries,$exc_entries)
 *  391:     function buildDayLink($day, $m, $d)
 *  414:     function wrapSingleItem($item,$event)
 *
 * TOTAL FUNCTIONS: 8
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */
class tx_tdcalendar_pi1_monthView extends tx_tdcalendar_pi1_library {
	/**
 * Diplay Month Function: initialize month view of calendar and renders month view
 *
 * @return	string		rendered month view
 */
	function displayMonth() {
		$this->fetchCurrValue('showWeeksAsRows', '0', 'sDEF');
		$this->fetchCurrValue('onlyDaysofMonth', '0', 'sDEF');
		$this->fetchCurrValue('dayFormat', '%d', 'sDEF');
		$this->fetchCurrValue('maxDaynameLenght', '2', 'sDEF', 1);
		$this->fetchCurrValue('showEventBegin', '0', 'sDEF');
		$this->fetchCurrValue('showWeeksDisabled', '0', 'sDEF');
		$this->fetchCurrValue('wrapItemCatLen', '8', 'sDEF', 1);
		$this->fetchCurrValue('PIDeventDisplay', '0', 'sDEF', 1);
		$this->fetchCurrValue('PIDdayDisplay', '0', 'sDEF', 1);
		$this->fetchCurrValue('miniCalendar', '0', 'sDEF');
		$this->fetchCurrValue('hideExcEvents', '0', 'sDEF');
		$this->fetchCurrValue('hideViewSelection', '0', 'sDEF');
		$this->fetchCurrValue('PIDlistDisplay', '0', 'sDEF');
		$this->fetchCurrValue('PIDsingleDayDisplay', '0', 'sDEF');
		$this->fetchCurrValue('PIDweekDisplay', '0', 'sDEF');
		$this->fetchCurrValue('showWeekasLink', '0', 'sDEF');

		$tempSubpart = $this->cObj->getSubpart($this->templateCode, '###MONTH_VIEW###');
		$tempSubpartLastYear = $this->cObj->getSubpart($tempSubpart, '###GO_LAST_YEAR###');
		$tempSubpartNextYear = $this->cObj->getSubpart($tempSubpart, '###GO_NEXT_YEAR###');
		$tempSubpartSubmit = $this->cObj->getSubpart($tempSubpart, '###SUBMIT_EVENT###');

		$subpartsArray = array(); // Region markers
		$markerArray = array(); // Simple markers

		$lastYear	= 	(strftime('%Y', $this->conf['currTime'])-1); 				//strtotime((strftime('%Y', $this->conf['currTime']) - 1).'-12-01');
		$nextYear	= 	(strftime('%Y', $this->conf['currTime'])+1);				//strtotime((strftime('%Y', $this->conf['currTime']) + 1).'-01-01');

		$vars = array();
		$vars['year']=$lastYear;
		$vars['month']= '12';
		$vars['day']= '1';

		if(!empty($this->conf['currCat'])) $vars['category'] = $this->conf['currCat'];

		$markerArray['###DATA###'] = $this->pi_linkTP_keepPIvars($lastYear, $vars, $this->caching);
		$subpartsArray['###GO_LAST_YEAR###'] = $this->cObj->substituteMarkerArrayCached($tempSubpartLastYear, $markerArray, array(), array());
		$vars['year']=$nextYear;
		$vars['month']= '1';
		$markerArray['###DATA###'] = $this->pi_linkTP_keepPIvars($nextYear, $vars, $this->caching);
		$subpartsArray['###GO_NEXT_YEAR###'] = $this->cObj->substituteMarkerArrayCached($tempSubpartNextYear, $markerArray, array(), array());

		$markerArray = array(); // Simple markers

		$markerArray['###CLASS###'] = $this->conf['miniCalendar'] ? 'miniCal' : '';
		$markerArray['###VIEW_TITLE###']= $this->pi_getLL('titleMonthView');
		$markerArray['###TIME_INFO###']= $this->conf['fixServerEncoding'] ? utf8_encode(strftime('%B %Y', $this->conf['currTime'])) : strftime('%B %Y', $this->conf['currTime']);
		$markerArray['###CATEGORY_TITLE###'] = $this->getCategorySelection($this->conf['currTime']);

		$markerArray['###SUBMIT_EVENT###'] = ''; // That's where we will build the FE_Edit

		$subpartsArray['###MONTH_NAVI###'] = $this->monthsNavi();
		//$subpartsArray['###CALENDAR_TABLE###'] = $this->calendarTable();
		$swing = $this->calendarTable();
		$subpartsArray['###CALENDAR_TABLE###'] = $swing[0];
		$firstEventTime = $swing[1];
		$mvars = array();
		if ($firstEventTime == 0) {
			$firstEventTime = $this->conf['currTime'];
		}
		$mvars['year']=strftime('%Y', $firstEventTime);
		$mvars['month']= strftime('%m', $firstEventTime);
		$mvars['day']= strftime('%d', $firstEventTime);
		($this->conf['PIDlistDisplay'] == 0) ? $markerArray['###LISTVIEW###'] = '' : $markerArray['###LISTVIEW###'] = $this->pi_linkTP_keepPIvars($this->pi_getLL('titleListView'), $mvars, $this->caching, 0, $this->conf['PIDlistDisplay']);
		$markerArray['###VIEW_LIST###'] = $this->getViewSelection($firstEventTime,'m');

		$out = $this->cObj->substituteMarkerArrayCached($tempSubpart, array(), $subpartsArray, array());
		$out = $this->cObj->substituteMarkerArrayCached($out, $markerArray, array(), array());

		if ($this->conf['showTooltips'] == 1) {
			$out .= $this->tooltip;
		}
		return $out;
	}

	/**
	 * Month Navigation function : rendering code for month navigation marker of template
	 *
	 * @return	string		$out: rendered month navigation
	 */
	function monthsNavi() {
		// fetch year from CurrentTime
		$y = strftime('%Y', $this->conf['currTime']);

		// template reading
		$out = $this->cObj->getSubpart($this->templateCode, '###MONTH_NAVI###');
		$rowT = $this->cObj->getSubpart($out, '###MONTH_ROW###');
		$cMonthT = $this->cObj->getSubpart($out, '###CURRENT_MONTH###');
		$oMonthT = $this->cObj->getSubpart($out, '###OTHER_MONTH###');

		// collect months to rowsArray
		$divider = 6; // Fields in row

		$currMonth = 1;
		for($currMonth; $currMonth <= 12; $currMonth++){
			$monthT = ($currMonth==strftime('%m', $this->conf['currTime'])) ? $cMonthT : $oMonthT;
			$markerArray = array(); // Simple markers

			$vars=array();
			$vars['year']	=	$y;
			$vars['month']	=	$currMonth;
			$vars['day']	=	'1';
			if(!empty($this->conf['currCat'])) $vars['category'] = $this->conf['currCat'];
			/* serverside encoding bug - fixed on 6-3-2013 */
			if ($this->conf['fixServerEncoding']) {
				$markerArray['###DATA###'] = $this->pi_linkTP_keepPIvars(utf8_encode(strftime('%b', strtotime('1970-'.$currMonth.'-1'))), $vars, $this->caching);
			} else {
				$markerArray['###DATA###'] = $this->pi_linkTP_keepPIvars(strftime('%b', strtotime('1970-'.$currMonth.'-1')), $vars, $this->caching);
			}
			/* replace it all */
			$rowsArray[(($currMonth-1)/$divider)] .= $this->cObj->substituteMarkerArrayCached($monthT, $markerArray, array(), array());
		}

		// create the rows
		$subpartsArray = array(); // Region markers
		foreach($rowsArray as $row){
			$subpartsArray['###MONTHS###'] = $row;
			$rows .= $this->cObj->substituteMarkerArrayCached($rowT, array(), $subpartsArray, array() );
		}

		// create the table
		$subpartsArray = array(); // Region markers
		$subpartsArray['###MONTH_ROW###'] = $rows;
		$out = $this->cObj->substituteMarkerArrayCached($out, array(), $subpartsArray, array() );
		return $out;
	}

	/**
	 * Month Table function : fetch entries from DB and initialize row- or col- view of view
	 *
	 * @return	array		rendered month table as col or row
	 */
	function calendarTable() {
		/* eval time borders */
		$m	= strftime('%m', $this->conf['currTime']);
		$y	= strftime('%Y', $this->conf['currTime']);
		$mondayPre	= strtotime('last Monday',	strtotime($y.'-'.$m.'-2 12:00'));

		/* if december, next year is one higher and month is january. else month is one higher */
		if ($m == 12) { 
			$m	= 1; 
			$y++; 
		} else { 
			$m++; 
		}

		$mondayPost	= strtotime('first Monday',	strtotime($y.'-'.$m.'-1 12:00'));

		/* load exc_events and events */
		$exc_entries = $this->getExceptionEventsArray($mondayPre, $mondayPost);

		//print_r($exc_entries);

		$entries = $this->getEventsArray($mondayPre,$mondayPost, $exc_entries);

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
		if($this->conf['showWeeksAsRows'] == 1)
			// return $this->calendarTableRow($mondayPre, $mondayPost, $entries, $exc_entries);
			return array($this->calendarTableRow($mondayPre, $mondayPost, $entries, $exc_entries),$firstEventTime);
		else
			// return $this->calendarTableCol($mondayPre, $mondayPost, $entries, $exc_entries);
			return array($this->calendarTableCol($mondayPre, $mondayPost, $entries, $exc_entries),$firstEventTime);
	}

	/**
	 * Month Table function : rendering of month table as col view
	 *
	 * @param	timestamp		$mondayPre : last monday before current month
	 * @param	timestamp		$mondayPost: first minday after current month
	 * @param	[type]		$entries: ...
	 * @param	[type]		$exc_entries: ...
	 * @return	string		$out: rendered month table
	 */
	function calendarTableCol($mondayPre,$mondayPost,$entries,$exc_entries){
		$calendarTableView = $this->cObj->getSubpart($this->templateCode, '###CALENDAR_TABLE###');
		$rowWeekday = $this->cObj->getSubpart($calendarTableView, '###WEEKDAYS###');
		$rowWeekend = $this->cObj->getSubpart($calendarTableView, '###WEEKEND###');

		$moment = $mondayPre;
		for($moment; $moment < $mondayPost; $moment = strtotime('+1 day', $moment)){
			/* order it by daytypes not by weeks for vertical weeks */
			/* Correction Mo = 0 ... Su = 6 */
			$daynr = strftime('%w', $moment) == 0 ? 6 : strftime('%w', $moment)-1;
			$daySets[$daynr][] = $moment;
			$dayTypes[$daynr] =  substr(strftime('%a', $moment), 0, $this->conf['maxDaynameLenght']);
		}

		for($i = 0; $i <= 6; $i++){
    	/* load templates */
		$row = ($i < 5) ? $row = $rowWeekday : $row = $rowWeekend;

		$days = '';
		foreach($daySets[$i] as $day){
            $days .= $this->getDayEvents($day, $row, $entries, $exc_entries);
		}

		/* fill table rows */
		 $subpartsArray = array(); // Region markers
		 $daynameT = $this->cObj->getSubpart($row, '###DAYNAME###');
		 $subpartsArray['###WEEK###']='';
		 $subpartsArray['###DAYNAME###'] = $this->cObj->substituteMarker($daynameT, '###DATA###', $dayTypes[$i]);
		 $subpartsArray['###DAYS###'] = $days;
			$row = $this->cObj->substituteMarkerArrayCached($row, array(), $subpartsArray, array() );
			$rows .= $row;
		}
		/* create week headers */
		if($this->conf['showWeeksDisabled'] == 0){
			$weekHeader = $this->cObj->getSubpart($calendarTableView, '###WEEKS###');
			$data = $this->cObj->getSubpart($weekHeader, '###WEEK###');
			foreach($daySets[0] as $monday){
				$markerArray = array();
			// if($GLOBALS['WINDIR'])
			//		$markerArray['###DATA###'] = $this->pi_getLL('week').strftime('%W', $monday);
			//	else
			//		$markerArray['###DATA###'] = $this->pi_getLL('week').strftime('%V', $monday);
			$wnum = ($GLOBALS['WINDIR']) ? strftime('%W', $monday) : strftime('%V', $monday);
			$mvars = array();
			$mvars['year']=strftime('%Y', $monday);
			$mvars['month']= strftime('%m', $monday);
			$mvars['day']= strftime('%d', $monday);
			//	$markerArray['###DATA###'] = ($this->conf['PIDweekDisplay'] == 0) ? $this->pi_getLL('week').$wnum : $this->pi_linkTP_keepPIvars($this->pi_getLL('week').$wnum, $mvars, $this->caching, 0, $this->conf['PIDweekDisplay']);
			$markerArray['###DATA###'] = (($this->conf['PIDweekDisplay'] > 0)and($this->conf['showWeekasLink'])) ? $this->pi_linkTP_keepPIvars($this->pi_getLL('week').$wnum, $mvars, $this->caching, 0, $this->conf['PIDweekDisplay']) : $this->pi_getLL('week').$wnum;
				$weeks .= $this->cObj->substituteMarkerArrayCached($data, $markerArray, array(), array());
			}

			$subpartsArray['###WEEK###'] = $weeks;

			$weekHeader = $this->cObj->substituteMarkerArrayCached($weekHeader, array(), $subpartsArray, array());
		}

			/* fill table */
		$subpartsArray = array(); // Region markers
		$subpartsArray['###TABLE###'] = $weekHeader.$rows;
			$out = $this->cObj->substituteMarkerArrayCached($calendarTableView, array(), $subpartsArray, array());
		return $out;
	}

	/**
	 * Month Table function : rendering of month table as row view
	 *
	 * @param	timestamp		$mondayPre : last monday before current month
	 * @param	timestamp		$mondayPost: first minday after current month
	 * @param	[type]		$entries: ...
	 * @param	[type]		$exc_entries: ...
	 * @return	string		$out: rendered month table
	 */
	function calendarTableRow($mondayPre,$mondayPost,$entries,$exc_entries){
		$calendarTableView = $this->cObj->getSubpart($this->templateCode, '###CALENDAR_TABLE###');
		$rowWeekday = $this->cObj->getSubpart($calendarTableView, '###WEEKDAYS###');
		$rowWeekend = $this->cObj->getSubpart($calendarTableView, '###WEEKEND###');
		$WeekofYear = $this->cObj->getSubpart($calendarTableView, '###WEEK###');
		$moment = $mondayPre;
		$weeks = array();
		$weekend = 0;

		$daynameHeader = $this->cObj->getSubpart($calendarTableView, '###DAYNAMES###');

		for($moment; $moment < $mondayPost; $moment = $weekend){
			$subpartsArray = array(); // Region markers
			if($this->conf['showWeeksDisabled'] == 0){
				//$subpartsArray['###WEEK###'] = $this->cObj->substituteMarker($WeekofYear, '###DATA###', $this->pi_getLL('week').strftime('%W', $moment));
				$mvars = array();
				$mvars['year']=strftime('%Y', $moment);
				$mvars['month']= strftime('%m', $moment);
				$mvars['day']= strftime('%d', $moment);
				$subpartsArray['###WEEK###'] = $this->cObj->substituteMarker($WeekofYear, '###DATA###', ($this->conf['PIDweekDisplay'] == 0) ? $this->pi_getLL('week').strftime('%W', $moment) : $this->pi_linkTP_keepPIvars($this->pi_getLL('week').strftime('%W', $moment), $mvars, $this->caching, 0, $this->conf['PIDweekDisplay']));
				$subpartsArray['###CORNER###'] = $this->cObj->getSubpart($daynameHeader, '###CORNER###');
			} else {
				$subpartsArray['###WEEK###'] = '';
				$subpartsArray['###CORNER###'] = '';
			}
			$subpartsArray['###DAYNAME###']='';
			$weekend = strtotime('+1 week', $moment);
			$days='';
			for($day=$moment; $day < $weekend; $day = strtotime('+1 day', $day)){
				$daynr = strftime('%w', $day) == 0 ? 6 : strftime('%w', $day)-1;
				$weekdays[$daynr]=$day;
				if($daynr < 5)
					 $row = $rowWeekday;
				else
					 $row = $rowWeekend;
				$days .= $this->getDayEvents($day, $row, $entries, $exc_entries);
			}
			$subpartsArray['###DAYS###'] = $days;
			$rows .= $this->cObj->substituteMarkerArrayCached($row, array(), $subpartsArray, array() );
		}

		/* create day headers */
		$data = $this->cObj->getSubpart($daynameHeader, '###DAYNAME###');
		foreach($weekdays as $day){
			$dayName =  substr(strftime('%a', $day), 0, $this->conf['maxDaynameLenght']);
			$dayNames .= $this->cObj->substituteMarker($data, '###DATA###', $dayName);
		}
		$subpartsArray['###DAYNAME###'] = $dayNames;
		$daynameHeader = $this->cObj->substituteMarkerArrayCached($daynameHeader, array(), $subpartsArray);
		$subpartsArray = array(); // Region markers
		$subpartsArray['###TABLE###'] = $daynameHeader.$rows;
		$out = $this->cObj->substituteMarkerArrayCached($calendarTableView, array(), $subpartsArray, array());
		return $out;
	}

	/**
	 * Get Day Events function : rendering of each day in month view with or without single entries
	 *
	 * @param	timestamp		$day : single day as timestamp
	 * @param	string		$col : current subpart for single day
	 * @param	[type]		$entries: ...
	 * @param	[type]		$exc_entries: ...
	 * @return	string		rendered single day subpart
	 */
	function getDayEvents($day,$col,$entries,$exc_entries){
		$m = strftime('%m', $day);
		$d = strftime('%d', $day);
		
		if(is_array($exc_entries[$m][$d])) {
			// usort($exc_entries[$m][$d], excSort);
			usort($exc_entries[$m][$d], array($this,'excSort'));
			
			$exc_events = $exc_entries[$m][$d];
			reset($exc_events);
			$first_exc_event = current($exc_events);
			$exc_color = $first_exc_event['bgcolor'] == 1 ? 'background: '.$first_exc_event['color'].';' : '';
		}

		$markerArray = array();
	    $bOutday = FALSE;
        if(strftime('%m', $day).'/'.strftime('%d', $day) == strftime('%m').'/'.strftime('%d')){
    	    if(strftime('%m', $day) == strftime('%m', $this->conf['currTime']))
               	$tDay=$this->cObj->getSubpart($col, '###TODAY###');
            else{
                $tDay=$this->cObj->getSubpart($col, '###DAY_OUTSIDE_MONTH###');
                $bOutday = TRUE;
            }
        }else
           	if(strftime('%m', $day) == strftime('%m', $this->conf['currTime']))
                    $tDay=$this->cObj->getSubpart($col, '###DAY_INSIDE_MONTH###');
            else{
                $tDay=$this->cObj->getSubpart($col, '###DAY_OUTSIDE_MONTH###');
                $bOutday = TRUE;
			}

	    if($this->conf['onlyDaysofMonth'] == 1 && $bOutday){
	        	$markerArray['###SINGLE_DAY###'] ='';
				$markerArray['###DATA###'] ='';
            	$markerArray['###COLOR###'] ='';
		} else {
			$markerArray['###SINGLE_DAY###'] = $this->buildDayLink($day, $m, $d, $entries[$m][$d]);

            if($exc_color){
            	//if($coloritem['first'])
               		//$links .= ' <span style="font-weight:normal">'.$coloritem['title'].'</span>';
            	$markerArray['###COLOR###'] = ' style="'.$exc_color.'"';
            }
            else
            	$markerArray['###COLOR###'] ='';

			if (!$this->conf['miniCalendar']) {
				$links = $this->buildExcEvents($exc_events);

				$events = is_array($entries[$m][$d]) ? $entries[$m][$d] : array();
				foreach( $events as $event){
					$link = $this->makeEventLink($event, $day);
					$links .= $this->wrapSingleItem($link, $event);
				}

				$markerArray['###DATA###'] = $links;
			} else {
				$markerArray['###DATA###'] = '';
			}
        }
        return $this->cObj->substituteMarkerArrayCached($tDay, $markerArray);
	}

	/**
	 * Build Day Link function : rendebuild link for minicalendar day view
	 *
	 * @param	timestamp		$day : current day as timestamp
	 * @param	string		$m: current month
	 * @param	string		$d: current day
	 * @return	string		rendered link or simple string
	 */
	function buildDayLink($day, $m, $d, $entries) {
		$stime = strftime($this->conf['dayFormat'], $day);

		if(is_array($entries) AND $this->conf['miniCalendar']){
			// comes back with tooltipping events for minicalendar
			// usort($this->entries[$m][$d],extSort);
			$vars = array();
			$vars['year'] = strftime('%Y', $this->conf['currTime']);
			$vars['month'] = $m;
			$vars['day']= $d;
			if(!empty($this->conf['currCat'])) $vars['category'] = $this->conf['currCat'];
			$PIDday = ($this->conf['PIDdayDisplay'] !== 0) ? $this->conf['PIDdayDisplay'] : $this->conf['pid'];

			return $this->pi_linkTP($stime, array($this->prefixId=>$vars), $this->caching, $PIDday);
		} else
           return $stime;
	}

	/**
	 * Wrap Single Item function : rendering of single calendar item
	 *
	 * @param	string		$item: title of single item
	 * @param	array		$event: current event item array
	 * @return	string		rendered single item
	 */
	function wrapSingleItem($item, $event){
		$SingleItemWrap = $this->cObj->getSubpart($this->templateCode, '###SINGLE_ITEM###');// ");
		$cat = $event['category'];
		if($this->conf['wrapItemCatLen'] > 0)
			$cat = '<a title="'.$cat.'" >'.substr($cat, 0, $this->conf['wrapItemCatLen']).'</a>';
			else
			$cat ='';
		$markers = array();
		$markers['###ITEM###'] = $item; //$this->submitEventLink($item,$event);
		$markers['###CAT###'] = $cat;
		$markers['###TIME###']  = $event['i'] > 1 ? '' : strftime($this->conf['dateFormat'], $event['begin']);
		$markers['###CATCOLOR###'] =$event['catcolor'];
		$markers['###LOCATION###'] = !empty($event['location_name']) ? $event['location_name'] : $event['location'];
		$markers['###ORGANIZER###'] = !empty($event['organizer_name']) ? $event['organizer_name'] : $event['organizer'];
		//$markers['###TEASER###'] = $event['teaser'];

		return $this->cObj->substituteMarkerArray($SingleItemWrap, $markers);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/td_calendar/pi1/class.tx_tdcalendar_pi1_monthView.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/td_calendar/pi1/class.tx_tdcalendar_pi1_monthView.php']);
}
?>