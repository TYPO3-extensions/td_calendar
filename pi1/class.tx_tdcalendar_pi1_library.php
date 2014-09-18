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
 *   70: class tx_tdcalendar_pi1_library extends tslib_pibase
 *   76:     function configure()
 *   90:     function initViewClass($class)
 *  127:     function fetchCurrValue($value, $std, $sheet, $numeric = 0)
 *  144:     function fetchConfigurationValue($param, $sheet)
 *  154:     function fetchUserTime ()
 *  168:     function getCategorySelection()
 *  220:     function getCategoryQuery($trow='tx_tdcalendar_categories.uid')
 *  246:     function selectInputOnChange($name, $array, $selected="", $onChange="", $size=1, $emptyItem=false)
 *  279:     function getEventsArray($from,$to,$exc_entries)
 *  324:     function getExceptionEventsArray($from,$to)
 *  369:     function getUpcomingEventsArray($from, $to='0')
 *  429:     function getUpcomingRecurEventsArray($from, $to, $exc_entries)
 *  485:     function makeArray($res,$from,$to,$exc_entries)
 *  505:     function initPidList()
 *  525:     function getPagesQuery($table=' tx_tdcalendar_events')
 *  540:     function setExcItems($item,$from,$to)
 *  574:     function setItems($item,$from,$to,$exc_entries)
 *  619:     function setRecurItemsList($item, $from, $to,$exc_entries)
 *  718:     function setRecItemArray($from, $to, $item, $getdate,$item_array,$exc_entries)
 *  764:     function checkExcEvents($time,$item,$exc_entries)
 *  805:     function getLastIntervalByDay($begin, $to, $interval)
 *  826:     function getLastIntervalByMonth($begin, $to, $interval)
 *  855:     function makeEventLink($event,$day, $title='', $noTooltip = 0)
 *  901:     function renderTooltip($event, $timestamp)
 *  961:     function buildExcEvents($excevents)
 *  982:     function getImage($row, $stopRow = 0 )
 * 1068:     function makeLink($label, $link, $ATagParams = '')
 * 1084:     function submitBack($backbutton = false)
 * 1097:     function addScriptResources()
 * 1176:     function printErrors()
 * 1192:     function extSort($aItem,$bItem)
 * 1224:     function excSort($aItem,$bItem)
 * 1283:     function getViewSelection($vTime)
 *
 * TOTAL FUNCTIONS: 33
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

 // require_once(PATH_tslib.'class.tslib_pibase.php');

 class tx_tdcalendar_pi1_library extends tslib_pibase {
 	/**
 * Configure function : configures default vars and local language
 *
 * @return	[type]		...
 */
	function configure() {
		if($GLOBALS['TSFE']->config['config']['locale_all'])
			setlocale(LC_ALL, $GLOBALS['TSFE']->config['config']['locale_all']);

        $this->pi_setPiVarDefaults();
		$this->pi_loadLL();
	}

	/**
	 * Init view class function : initialize requiered class of view
	 *
	 * @param	string		$class: name of requiered class
	 * @return	string		$class: initialized class
	 */
 	function initViewClass($class){
		$c = $this->prefixId.'_'.$class.'View';
		$class = new $c();
		$class->cObj 					= 	$this->cObj;
		$class->conf 					= 	$this->conf;
		$class->extKey 					= 	$this->extKey;
		$class->prefixId 				= 	$this->prefixId;
		$class->templateCode			=	$this->templateCode;
		$class->tooltipCode = 				$this->cObj->getSubpart($this->templateCode, '###TOOLTIP###');
		$class->scriptRelPath 			= 	$this->scriptRelPath;
		$class->pi_checkCHash 			= 	$this->pi_checkCHash;
		$class->caching 				=	$this->caching;
		$class->configure();
		$class->pidList 				= 	$this->pidList;

		$class->piVars 					=	$this->piVars;

		$class->enableFieldsCategories 	= 	$this->enableFieldsCategories;
		$class->enableFieldsEvents 		= 	$this->enableFieldsEvents;
		$class->enableFieldsLocation 	= 	$this->enableFieldsLocation;
		$class->enableFieldsOrganizer 	= 	$this->enableFieldsOrganizer;

		$class->enableFieldsExcEvents 	= 	$this->enableFieldsExcEvents;
		$class->enableFieldsExcCategories 	= 	$this->enableFieldsExcCategories;

		return $class;
	}

	/**
	 * Standard Format fetching : fetches standard format for default vars by checking FlexForm and TypoScipt configuration
	 *
	 * @param	string		$value : key of value to be fetched
	 * @param	string		$std : standard value / fallback, if either in FF and TS is no value given
	 * @param	string		$sheet: sheet of FF to search in for values
	 * @param	[type]		$numeric: ...
	 * @return	string		$this->conf[$value]: value of searched key in FF, TS or Std (hardcoded)
	 */
	function fetchCurrValue($value, $std, $sheet, $numeric = 0) {
		$preStd = $this->fetchConfigurationValue($value, $sheet);
		if(!empty($preStd)) $this->conf[$value] = $preStd;
		if(empty($this->conf[$value])) $this->conf[$value] = $std;
		if($numeric == 1 AND !is_numeric($this->conf[$value]))
			$this->conf[$value] = $std;

		return;
	}

	/**
	 * Fetch Configuration Value function : fetches configuration from FlexForm
	 *
	 * @param	string		$param : key, the function searches for in Flexform
	 * @param	string		$sheet: FF-sheet, the function searches in
	 * @return	string		$value: value found in FF, it's possible, the value is empty
	 */
	function fetchConfigurationValue($param, $sheet) {
		$value = trim($this->pi_getFFvalue($this->cObj->data['pi_flexform'], $param, $sheet));
		return $value;
	}

	/**
	 * Fetch User Time function : fetches current Time from GET-Parameters. If not exist or incriminated returns current time.
	 *
	 * @return	string		$value: User Time as Timestamp
	 */
	function fetchUserTime () {
		$this->conf['currTime'] = time(); // Current time for several Items like month, week, day and list
		$y = (!empty($this->piVars['year']) AND is_numeric($this->piVars['year']) ) ? $this->piVars['year']:strftime('%Y', $this->conf['currTime']);
		$m = (!empty($this->piVars['month']) AND is_numeric($this->piVars['month']) ) ? $this->piVars['month']:strftime('%m', $this->conf['currTime']);
		$d = (!empty($this->piVars['day']) AND is_numeric($this->piVars['day']) ) ? $this->piVars['day']:strftime('%d', $this->conf['currTime']);
		$this->conf['currTime'] = strtotime( $y.'-'.$m.'-'.$d);
		return;
	}

	/**
	 * Get Category Selection function : renders category selection, if enabled
	 *
	 * @return	string		rendered category selection
	 */
	function getCategorySelection() {
		if($this->conf['hideCategorySelection'])
            return '';

		$this->getCategoryQuery();

		$select_fields = 	'tx_tdcalendar_categories.uid';
		$select_fields .=	', tx_tdcalendar_categories.title';
		$select_fields .=	', tx_tdcalendar_categories.color';

		$from_table =		'tx_tdcalendar_categories';

		$where_clause = 	'1';
		$where_clause .= 	$this->enableFieldsCategories;
		$where_clause .= 	$this->getCategoryQuery();

		$where_clause .=	$this->getPagesQuery('tx_tdcalendar_categories');

		$orderBy =			'tx_tdcalendar_categories.uid';

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			$select_fields,
			$from_table,
			$where_clause,
			$groupBy='',
			$orderBy,
			$limit=''
		);

		if ($GLOBALS['TYPO3_DB']->sql_num_rows($res) <= 1)
			return '';

		$vars['category'] = 0;

		$cats = array();
		$cats[$this->pi_linkTP_keepPIvars_url($vars, $this->caching)] = $this->pi_getLL('allCats');
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
			if($this->conf['currCat']==$row['uid'])
				$sel=$row['title'];
			$vars['category']=$row['uid'];
			$cats[$this->pi_linkTP_keepPIvars_url($vars, $this->caching)] = $row['title'];
        }

		return $this->selectInputOnChange('category', $cats, $sel ,"document.location = '' + this.options[selectedIndex].value;");
	}

	/**
	 * get Category Query function : build where-clause for categories in dependence of flexform or ts-config
	 *
	 * @param	string		$trow: effected row
	 * @return	string		$query: where-clause
	 */
	function getCategoryQuery($trow='tx_tdcalendar_categories.uid') {
		if ($this->conf['categoryMode'] != 0) {
			$query = ' AND ('.$trow.' ';

			if ($this->conf['categoryMode'] == 1)
				$query.= 'IN ';
			elseif ($this->conf['categoryMode'] == -1)
				$query.= 'NOT IN ';
			else
				return;
			$query.= '('.$this->conf['categorySelection'].'))';
		}
		return $query;
	}

	/**
	 * Select Input On Change function : renders a select-field with onChange functionality
	 *
	 * @param	string		$name: name of select field
	 * @param	array		$array: array of select items
	 * @param	string		$selected: uid of selected element
	 * @param	string		$onChange: onChange handling to be rendered
	 * @param	string		$size: size of element
	 * @param	string		$emptyItem: show a empty Item? virtualy boolean
	 * @return	string		rendered selct input field
	 */
	function selectInputOnChange($name, $array, $selected='', $onChange='', $size=1, $emptyItem=FALSE){
		$selectedCode = $this->cObj->getSubpart($this->templateCode, '###SELECT_VIEW###');
		$optionsCode = $this->cObj->getSubpart($selectedCode, '###OPTIONS###');
		$emptyOptionCode = $this->cObj->getSubpart($optionsCode, '###EMPTY_OPTION###');
		$optionCode = $this->cObj->getSubpart($optionsCode, '###OPTION###');

		$markerArray = array();
		$subpartsArray = array();
		$out = '';

		if($emptyItem) $out .= $this->cObj->substituteMarkerArrayCached($emptyOptionCode, $markerArray['###EMPTY_TITLE###'] = $this->pi_getLL('emptyOptionTitle'), $subpartsArray, array());
		foreach($array as $value => $label){
			$markerArray['###VALUE###'] = $value;
			$markerArray['###LABEL###'] = $label;
			$markerArray['###SELECTED###'] = ((string)$label == (string)$selected)? 'selected="true"':'';
			$out .= $this->cObj->substituteMarkerArrayCached($optionCode, $markerArray, $subpartsArray, array());
		}

		$subpartsArray['###OPTIONS###'] = $out;
		$markerArray['###NAME###'] = $name;
		$markerArray['###ONCHANGE###'] = $onChange;
		$markerArray['###SIZE###'] = $size;
		return $this->cObj->substituteMarkerArrayCached($selectedCode, $markerArray, $subpartsArray, array());
	}

	/**
	 * Get Events Array function : fetches all possible related Events from DB and returns an Array
	 *
	 * @param	string		$from: starttime of current view
	 * @param	string		$end: endtime of current view
	 * @param	[type]		$exc_entries: ...
	 * @return	array		array of related items.
	 */
	function getEventsArray($from,$to,$exc_entries){
		$select_fields = 	'tx_tdcalendar_events.*';
		$select_fields .=	', tx_tdcalendar_categories.title as category';
		$select_fields .= 	', tx_tdcalendar_categories.color as catcolor';
		$select_fields .= 	', tx_tdcalendar_locations.location as location_name';
		$select_fields .= 	', tx_tdcalendar_organizer.name as organizer_name';

		$from_table =		'((tx_tdcalendar_events'; 
		$from_table .= 		' INNER JOIN tx_tdcalendar_categories';
        $from_table .= 		' ON tx_tdcalendar_events.category = tx_tdcalendar_categories.uid)';
		$from_table .= 		' LEFT JOIN tx_tdcalendar_locations';
		$from_table .= 		' ON tx_tdcalendar_events.location_id = tx_tdcalendar_locations.uid)';
		$from_table .= 		' LEFT JOIN tx_tdcalendar_organizer';
		$from_table .= 		' ON tx_tdcalendar_events.organizer_id = tx_tdcalendar_organizer.uid';

		$where_clause = 	"((tx_tdcalendar_events.begin < '".$from."' AND tx_tdcalendar_events.end >= '".$from."')";
		$where_clause .= 	" OR (tx_tdcalendar_events.begin >= '".$from."' AND tx_tdcalendar_events.begin < '".$to."')";

		$where_clause.= 	" OR(event_type > 0 AND event_type < 5 AND ((begin < '".$from."' AND rec_end_date = 0) OR  (begin < '".$from."' AND rec_end_date >= '".$from."') )))";

		$where_clause .= 	$this->enableFieldsCategories;
		$where_clause .=	$this->enableFieldsEvents;

		if ($this->conf['currCat'] AND !$this->conf['hideCategorySelection'])
			$where_clause .= ' AND tx_tdcalendar_events.category = '.$this->conf['currCat'];
		else
			$where_clause .= 	$this->getCategoryQuery('tx_tdcalendar_events.category');

		$where_clause .=	$this->getPagesQuery();

		$orderBy =			'tx_tdcalendar_events.begin, tx_tdcalendar_events.uid';

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			$select_fields,
			$from_table,
			$where_clause,
			$groupBy='',
			$orderBy,
			$limit=''
		);

		return  $this->makeArray($res, $from, $to, $exc_entries);
	}

	/**
	 * Get Exception Events Array function : fetches all possible related exception events from DB and returns an Array
	 *
	 * @param	string		$from: starttime of current view
	 * @param	string		$end: endtime of current view
	 * @return	array		array of related exception events.
	 */
	function getExceptionEventsArray($from,$to){
		$select_fields = 	'tx_tdcalendar_exc_events.*';
		$select_fields .= 	', tx_tdcalendar_exc_categories.uid as category';
		$select_fields .= 	', tx_tdcalendar_exc_categories.title as cattitle';
		$select_fields .= 	', tx_tdcalendar_exc_categories.color as color';
		$select_fields .= 	', tx_tdcalendar_exc_categories.bgcolor as bgcolor';
		$from_table =		'tx_tdcalendar_exc_events INNER JOIN tx_tdcalendar_exc_categories';
		$from_table .= 		' ON tx_tdcalendar_exc_events.exc_categories = tx_tdcalendar_exc_categories.uid';
		$where_clause = 	"(tx_tdcalendar_exc_events.begin < '".$from."' AND tx_tdcalendar_exc_events.end >= '".$from."')";
		$where_clause .= 	" OR (tx_tdcalendar_exc_events.begin >= '".$from."' AND tx_tdcalendar_exc_events.begin < '".$to."')";
		$where_clause .=	$this->enableFieldsExcEvents;
		$where_clause .=	$this->enableFieldsExcCategories;
		$where_clause .=	$this->getPagesQuery(' tx_tdcalendar_exc_events');
		$orderBy =			'tx_tdcalendar_exc_events.begin, tx_tdcalendar_exc_events.uid';

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			$select_fields,
			$from_table,
			$where_clause,
			$groupBy='',
			$orderBy,
			$limit=''
		);

		$exc_events = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$exc_events['category'][$row['category']][$row['uid']] = $row['title'];
			$row = $this->setExcItems($row, $from, $to);
            foreach($row as $item){
                $d = strftime('%d', $item['begin']);
                $m = strftime('%m', $item['begin']);

                $exc_events[$m][$d][] = $item;
            }
		}
		return $exc_events;
	}

	/**
	 * Get Upcoming Recurring Events Array function : fetches all related events from DB and returns an Array for upcoming view
	 *
	 * @param	string		$from: starttime of current view
	 * @param	string		$to: endtime of current view
	 * @return	array		$array: array of items in current view
	 */
	function getUpcomingEventsArray($from, $to='0') {
		$select_fields = 	'tx_tdcalendar_events.*';
		$select_fields .=	', tx_tdcalendar_categories.title as category';
		$select_fields .= 	', tx_tdcalendar_categories.color as catcolor';
		$select_fields .= 	', tx_tdcalendar_locations.location as location_name';
		$select_fields .= 	', tx_tdcalendar_organizer.name as organizer_name';

		$from_table =		'((tx_tdcalendar_events'; 
		$from_table .= 		' INNER JOIN tx_tdcalendar_categories';
        $from_table .= 		' ON tx_tdcalendar_events.category = tx_tdcalendar_categories.uid)';
		$from_table .= 		' LEFT JOIN tx_tdcalendar_locations';
		$from_table .= 		' ON tx_tdcalendar_events.location_id = tx_tdcalendar_locations.uid)';
		$from_table .= 		' LEFT JOIN tx_tdcalendar_organizer';
		$from_table .= 		' ON tx_tdcalendar_events.organizer_id = tx_tdcalendar_organizer.uid';

		$where_clause = 	"((tx_tdcalendar_events.begin < '".$from."' AND tx_tdcalendar_events.end >= '".$from."')";
		$where_clause .= 	" OR tx_tdcalendar_events.begin >= '".$from."') AND event_type=0";

		$where_clause .= 	$this->enableFieldsCategories;
		$where_clause .=	$this->enableFieldsEvents;

		if ($this->conf['currCat'] AND !$this->conf['hideCategorySelection'])
			$where_clause .= ' AND tx_tdcalendar_events.category = '.$this->conf['currCat'];
		else
			$where_clause .= 	$this->getCategoryQuery('tx_tdcalendar_events.category');

		$where_clause .=	$this->getPagesQuery();

		$orderBy =			'tx_tdcalendar_events.begin, tx_tdcalendar_events.uid';
		$limit = 			'0,'.($this->conf['listEntryCount']+1);

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			$select_fields,
			$from_table,
			$where_clause,
			$groupBy='',
			$orderBy,
			$limit
		);

		$array = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$array[] = $row;
		}

		end($array);
        $lastevent=current($array);

		if(count($array) < $this->conf['listEntryCount'])
              $lastevent['begin']=0x7fffffff;

		$exc_entries = $this->getExceptionEventsArray($this->conf['listStartTime'], $lastevent['begin']);
		$array = array_merge((array)$array, (array)$this->getUpcomingRecurEventsArray($this->conf['listStartTime'], $lastevent['begin'], $exc_entries));

		usort($array, array($this ,'extSortUpcoming'));

		return  $array;
	}

	/**
	 * Get Upcoming Recurring Events Array function : fetches recurring Events from DB and returns an Array for upcoming view
	 *
	 * @param	string		$from: starttime of current view
	 * @param	string		$to: endtime of current view
	 * @param	[type]		$exc_entries: ...
	 * @return	array		$array: array of items in current view
	 */
	function getUpcomingRecurEventsArray($from, $to, $exc_entries) {
		$select_fields = 	'tx_tdcalendar_events.*';
		$select_fields .=	', tx_tdcalendar_categories.title as category';
		$select_fields .= 	', tx_tdcalendar_categories.color as catcolor';
		$select_fields .= 	', tx_tdcalendar_locations.location as location_name';
		$select_fields .= 	', tx_tdcalendar_organizer.name as organizer_name';

		$from_table =		'((tx_tdcalendar_events'; 
		$from_table .= 		' INNER JOIN tx_tdcalendar_categories';
        $from_table .= 		' ON tx_tdcalendar_events.category = tx_tdcalendar_categories.uid)';
		$from_table .= 		' LEFT JOIN tx_tdcalendar_locations';
		$from_table .= 		' ON tx_tdcalendar_events.location_id = tx_tdcalendar_locations.uid)';
		$from_table .= 		' LEFT JOIN tx_tdcalendar_organizer';
		$from_table .= 		' ON tx_tdcalendar_events.organizer_id = tx_tdcalendar_organizer.uid';

		$where_clause = 	'(event_type > 0 AND (rec_end_date=0 ';
		$where_clause .= 	" OR  (begin >= '".$from."' AND (begin < '".$to."'))";
		$where_clause .= 	" OR  (begin < '".$from."' AND (rec_end_date >= '".$from."' OR  end >= '".$from."'))))";

		$where_clause .= 	$this->enableFieldsCategories;
		$where_clause .=	$this->enableFieldsEvents;

		if ($this->conf['currCat'] AND !$this->conf['hideCategorySelection'])
			$where_clause .= ' AND tx_tdcalendar_events.category = '.$this->conf['currCat'];
		else
			$where_clause .= 	$this->getCategoryQuery('tx_tdcalendar_events.category');

		$where_clause .=	$this->getPagesQuery();

		$orderBy =			'tx_tdcalendar_events.begin, tx_tdcalendar_events.uid';
		$limit = 			'';

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
			$select_fields,
			$from_table,
			$where_clause,
			$groupBy='',
			$orderBy,
			$limit
		);

		$array = array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$items=array();
			$items = $this->setRecurItemsList($row, $from, $to, $exc_entries);
			$array = array_merge((array)$array, (array)$items);
		}

		if(is_array($array))
			return $array;
		else
			return array();
	}

	/**
	 * Make Array function : builds an Array of entries from Database
	 *
	 * @param	array		$res : key, the function searcghes for in Flexform
	 * @param	string		$from: FF-sheet, the function searches in
	 * @param	string		$to: value found in FF, it's possible, the value is empty
	 * @param	[type]		$exc_entries: ...
	 * @return	array		array of daily events
	 */
	function makeArray($res,$from,$to,$exc_entries){
    /* order result by days */
		$entries=array();
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$row = $this->setItems($row, $from, $to, $exc_entries);
            if (is_array($row)) {
				foreach($row as $item){
					$d = strftime('%d', $item['begin']);
					$m = strftime('%m', $item['begin']);
					 $entries[$m][$d][] = $item;
				}
			}
		}

		return $entries;
	}

	/**
	 * Init PID List function : build a string for all PIDs, the current Plugin effects
	 *
	 * @return	[type]		...
	 */
	function initPidList() {
		// pidList is the pid/list of pids from where to fetch the news items.
		$pidList = $this->conf['pidList'];
		$pidList = $pidList ? implode(t3lib_div::intExplode(',', $pidList), ',') : $GLOBALS['TSFE']->id;
		$recursive = $this->conf['recursive'];

		// extend the pid_list by recursive levels
		$this->pidList = $this->pi_getPidList($pidList, $recursive);
		$this->pidList = $this->pidList ? $this->pidList : 0;
		if (!$this->pidList) {
			$this->errors[] = 'No pidList defined';
		}
	}

	/**
	 * get Pages Query function : builds a string for database selection by PID
	 *
	 * @param	string		$table : table to be selected, def. is 'tx_tdcalendar_events'
	 * @return	string		string for selection
	 */
	function getPagesQuery($table=' tx_tdcalendar_events'){
		$pages = $this->pidList;
		if(!$pages)return;
		return ' AND '.$table.'.pid IN ('.$pages.') ';
	}


	/**
	 * set Exception Items function : build exception events array for single items
	 *
	 * @param	array		$item : current item from DB
	 * @param	timestamp		$from : start time of current view as timestamp
	 * @param	timestamp		$to : end time of current view as timestamp
	 * @return	array		$itemarray: event array (could be more than one entry)
	 */
	function setExcItems($item,$from,$to){
        $itemarray = array();
        if(($item['begin'] < $item['end'])){
				$item['end'] = strtotime(strftime('%Y-%m-%d ', $item['end']).'+1day')-1;
				$new_item = $item;
				$lastday = $item['end'];
				$firstday = $item['begin'];
				$time = $item['begin'];
				$new_item['mday']= $item['begin'];
				$i=0;
				while($firstday<=$lastday){
					$new_item['begin'] = $firstday;
					$new_item['end'] = $lastday;
					if ($firstday >=$from AND $firstday <=$to ) $itemarray[]= $new_item;
					$i++;
					$firstday = strtotime(strftime('%Y-%m-%d', $time).'+'.$i.'days');
					if($firstday>$to) break;
				}
        } else {
			$item['end'] = strtotime(strftime('%Y-%m-%d ', $item['begin']).'+1day')-1;
			$itemarray[]=$item;
		}
		return $itemarray;
      }

	/**
	 * set Items function : build events array for single items
	 *
	 * @param	array		$item : current item from DB
	 * @param	timestamp		$from : start time of current view as timestamp
	 * @param	timestamp		$to : end time of current view as timestamp
	 * @param	array		$exc_entries: exception events array
	 * @return	array		$itemarray: event array (could be more than one entry)
	 */
	function setItems($item,$from,$to,$exc_entries){
        if ($item['allday'] > 0) {
			$item['begin'] = strtotime(strftime('%Y-%m-%d', $item['begin']));
			if ($item['begin'] > $item['end'])
				$item['end'] = strtotime(strftime('%Y-%m-%d', $item['begin']).'+1day')-1;
			else
				$item['end'] = strtotime(strftime('%Y-%m-%d', $item['end']).'+1day')-1;
		}

		if($item['event_type'] > 0)
            return $this->setRecurItemsList($item, $from, $to, $exc_entries);
        $itemarray = array();
        if(strtotime(strftime('%Y-%m-%d', $item['begin']))== strtotime(strftime("%Y-%m-%d", $item['end']))){
			$itemarray[]=$item;
            return $itemarray;
        }
        if(($item['end'] && $item['begin'] != $item['end']) AND $this->conf['showMultiDayOnlyOnce'] == 0){
				$new_item = $item;
				$lastday = $item['end'];
				$firstday = $item['begin'];
				$time = $item['begin'];
				$new_item['mday']= $item['begin'];
				$i=0;
				while($firstday<$lastday){
					$new_item['begin'] = $firstday;
					$new_item['end'] = $lastday;
					$new_item['i'] = $i;
					$itemarray[]= $new_item;
					$i++;
					$firstday = strtotime(strftime('%Y-%m-%d', $time).'+'.$i.'days');
				}
        } else
			$itemarray[]=$item;
		return $itemarray;
      }

	/**
	 * set Recurring Item List function : build events array for single items, if the item is a recurring event
	 *
	 * @param	array		$item : current item from DB
	 * @param	timestamp		$from : start time of current view as timestamp
	 * @param	timestamp		$to : end time of current view as timestamp
	 * @param	array		$exc_entries: exception events array
	 * @return	array		$item_array: event array (could be more than one entry)
	 */
	function setRecurItemsList($item, $from, $to,$exc_entries) {
		$begin =  $item['begin'];
		$rec_end = $item['rec_end_date'];
		$itemarray = array();

		switch ($item['event_type']){
            case 1:
                $steps = 'days';
                $repeat_time = $item['repeat_days']?$item['repeat_days']:1;
				$getdate = $this->getLastIntervalByDay($begin, $from, $repeat_time);
				$getdate['steps'] = 'days';
            break;
            case 2:
                 $steps = 'days';
                 $repeat_time = $item['repeat_weeks']?$item['repeat_weeks']*7:7;
				 $getdate = $this->getLastIntervalByDay($begin, $from, $repeat_time);
				 $getdate['steps'] = 'days';
            break;
            case 3:
                $steps = 'months';
                $repeat_time = $item['repeat_months']?$item['repeat_months']:1;
				$getdate = $this->getLastIntervalByMonth($begin, $from, $repeat_time);
				$getdate['steps']= 'months';
            break;
            case 4:
                $steps = 'months';
                $repeat_time = $item['repeat_years']?$item['repeat_years']*12:12;
				$getdate = $this->getLastIntervalByMonth($begin, $from, $repeat_time);
				$getdate['steps'] = 'months';
				break;
        }

		if($item['rec_time_x'] > 0){
             $x_end = strtotime(strftime('%Y-%m-%d %H:%M:0 ', $item['begin']).$item['rec_time_x']*$repeat_time.' '.$getdate['steps']);
		}

		if($x_end) {
			if($rec_end && $x_end > $rec_end)
				$getdate['end'] = $rec_end;
            else {
				$getdate['end'] = $x_end;
				$rec_end = 0;
			}
		} else {
			$getdate['end'] = $rec_end;
		}

		if($item['event_type'] == 2) {
			switch ($item['rec_weekly_type']) {
				case 1:
					$rwds = $weekdays = array(1,1,1,1,1,0,0);
				break;
				case 2:
					$rwds = array(0,0,0,0,0,1,1);
				break;
				default:
					$rwds = array($item['repeat_week_monday'],$item['repeat_week_tuesday'],$item['repeat_week_wednesday'],$item['repeat_week_thursday'],$item['repeat_week_friday'],$item['repeat_week_saturday'],$item['repeat_week_sunday']);
				break;
			}

			/*----BUGFIX-----
			 * If the interval is not set one back, the script skip the last days to be shown, if they are set to following month
			 * If the interval is set one back, the script automatically jumps back two intervalls
			 * ----------------------
			 * don't know why... but it works. that's all, that counts...
			/--------------*/

			$getdate['lastint']--;

			$getdate['begin'] = $item['begin'];

			$item['begin'] = strtotime(strftime('%Y-%m-%d ', $item['begin']).$getdate['interval']*$getdate['lastint'].$getdate['steps'].' monday this week')+($item['begin']-strtotime(strftime('%Y-%m-%d', $item['begin'])));
			$item['end'] = $item['begin'] + ($item['end'] - $getdate['begin']);

			for($add=0;$add<7;$add++){
				if($rwds[$add]){
					$item_array = $this->setRecItemArray($from, $to, $item, $getdate, $item_array, $exc_entries);
				}
				$item['begin'] = $item['begin'] + 86400;
				$item['end'] = $item['end'] + 86400;
			}

		} else {
			$item_array = $this->setRecItemArray($from, $to, $item, $getdate, $item_array,$exc_entries);
		}
		return $item_array;
	}

	/**
	 * Set Recurring Item Array function : build events array for single items per call by setRecItemList
	 *
	 * @param	timestamp		$from : start time of current view as timestamp
	 * @param	timestamp		$to : end time of current view as timestamp
	 * @param	array		$getdate : array of values of recurration (intervall, last interval, etc.)
	 * @param	array		$item_array : current item
	 * @param	array		$exc_entries: exception events array
	 * @param	[type]		$exc_entries: ...
	 * @return	array		$itemarray: event array (could be more than one entry)
	 */
	function setRecItemArray($from, $to, $item, $getdate,$item_array,$exc_entries) {

		for($c = $getdate['lastint']*$getdate['interval'];; $c= $c + $getdate['interval']) {
			$time = strtotime(strftime('%Y-%m-%d %H:%M:0 ', $item['begin']).$c.$getdate['steps']);
			$new_item = $item;
			$new_item['begin'] = $time;

			if (($time > $to) OR ($getdate['end'] > 0 AND $time >= $getdate['end'])) {
				break;
			}

			if(($time >= $from) AND ($time <= $to) AND ($item['event_type'] != 2 OR $getdate['begin'] <= $time) AND (empty($getdate['end']) OR $time <= $getdate['end'])) {
				if($this->checkExcEvents($time, $item, $exc_entries) == FALSE) {
					if(($item['end'] AND $item['begin'] < $item['end']) AND $this->conf['showMultiDayOnlyOnce'] == 0){
						$lastday = $time+($item['end']-$item['begin']);
						$firstday = $time;
						$new_item['mday']= $time;
						$i=0;
						while($firstday<$lastday){
							$new_item['begin'] = $firstday;
							$new_item['end'] = $lastday;
							$new_item['i'] = $i;
							$item_array[]= $new_item;

							$i++;
							$firstday = strtotime(strftime('%Y-%m-%d', $time).'+'.$i.'days');
						}
					} else {
						$new_item['end'] = $item['begin'] > $item['end'] ? 0 : $time + ($item['end'] - $item['begin']);
						$item_array[] = $new_item;
					}
				}
			}
		}

		return $item_array;
	}

	/**
	 * check Exception Events function : checks, if an exception event effects current event
	 *
	 * @param	timestamp		$time : start time of current event
	 * @param	array		$item : values of current item
	 * @param	array		$exc_entries : array of exception events
	 * @return	boolean		found an exception event effecting show event
	 */
	function checkExcEvents($time,$item,$exc_entries) {
		$m	= strftime('%m', $time);
		$d	= strftime('%d', $time);

		if ($item['exc_category']) {
			$categories = explode(',', $item['exc_category']);
			foreach($categories as $category) {
				if (is_array($exc_entries['category'][$category])) {
					foreach($exc_entries['category'][$category] as $key => $value) {
						$checkExcItems[] = $key;
					}
				}
			}
		}

		if ($item['exc_event']) {
			$exc_events = explode(',', $item['exc_event']);
			foreach($exc_events as $exc_event) {
				$checkExcItems[] = $exc_event;
			}
		}

		if(is_array($checkExcItems)) {
			foreach($exc_entries[$m][$d] as $entry) {
				if(in_array($entry['uid'], $checkExcItems)) {
					return TRUE;
				}
			}
		}

		return FALSE;
	}

	/**
	 * get Last Interval by Day function : calculating last occurrence of an event by day (modulo)
	 *
	 * @param	timestamp		$begin : start time of current view as timestamp
	 * @param	timestamp		$to : end time of current view as timestamp
	 * @param	string		$interval : user defined interval of reccurring event
	 * @return	array		$item: array of interval, last occurrence, etc.
	 */
	function getLastIntervalByDay($begin, $to, $interval) {
		$int = $interval*86400;

		$difftimest = $to-$begin;
		$remains = $difftimest%$int;
		$item['lastint'] = floor($difftimest/$int);
		if($item['lastint'] < 0)
			$item['lastint'] = 0;
		$item['interval'] = $interval;

		return $item;
	}

	/**
	 * get Last Interval by Month function : calculating last occurrence of an event by month (virtually modulo)
	 *
	 * @param	timestamp		$begin : start time of current view as timestamp
	 * @param	timestamp		$to : end time of current view as timestamp
	 * @param	string		$interval : user defined interval of reccurring event
	 * @return	array		$item: array of interval, last occurrence, etc.
	 */
	function getLastIntervalByMonth($begin, $to, $interval) {
		$start['year'] = strftime('%Y', $begin);
		$start['month'] = strftime('%m', $begin);
		$start['day'] = strftime('%d', $begin);

		$end['year'] = strftime('%Y', $to);
		$end['month'] = strftime('%m', $to);
		$end['day'] = strftime('%d', $to);

		$diffmonth= (($end['year']-$start['year'])*12) - ($start['month']-$end['month']);
		$diffmonth = $start['day'] > $end['day'] ? $diffmonth-1 : $diffmonth;

		$item['lastint'] = floor($diffmonth/$interval);
		if($item['lastint'] < 0)
			$item['lastint'] = 0;
		$item['interval'] = $interval;

		return $item;
	}

	/**
	 * Make Event Link function : linkbuiling for single events view
	 *
	 * @param	array		$event : events array
	 * @param	timestamp		$day : current day as timestamp
	 * @param	string		$title : alternative title for event link, def. is empty
	 * @param	[type]		$noTooltip: ...
	 * @return	string		$link : rendered event link
	 */
	function makeEventLink($event,$day, $title='', $noTooltip = 0){

		$day = $event['mday'] ? $event['mday'] : $day;
		$linkContent = !empty($title)? $title : $event['title'];

		if($this->conf['showEventBegin'] != 0 AND $event['i']<1) {
			if ($event['allday'])
				$linkContent = '<span class=\'il-date\'>'.$this->pi_getLL('allday').' </span>'.$linkContent;
			else
				$linkContent = '<span class=\'il-date\'>'.strftime($this->conf['timeFormat'], $event['begin']).' </span>'.$linkContent;
		}

		if(!empty($event['directlink'])){
			$link = $this->pi_linkTP_keepPIvars($linkContent, array(), $this->caching, 0, $event['directlink']);
		} else {

			$vars['year'] = strftime('%Y', $day);
			$vars['month'] = strftime('%m', $day);
			$vars['day']= strftime('%d', $day);
			$vars['event'] = $event['uid'];

			$PIDsingle = ($this->conf['PIDeventDisplay'] !== 0) ? $this->conf['PIDeventDisplay'] : $this->conf['pid'];

			$link = $this->pi_linkTP($linkContent, array($this->prefixId=>$vars), $this->caching, $PIDsingle);
			if ($this->conf['showTooltips'] == 1 AND !$noTooltip) {
				$timestamp = $event['mday'] ? $event['mday'] : $event['begin'];
				$params = array(
					'rel' => 'td-tooltip-'.$event['uid'].'-'.$timestamp,
					'class' => 'td-tooltip-trigger'
				);

				$this->tooltip .= $this->renderTooltip($event, $timestamp);

				$link = $this->cObj->addParams($link, $params);
			}
        }
        return $link;
	}

	/**
	 * render tooltip function : renders tooltips
	 *
	 * @param	array		$event : events array
	 * @param	timestamp		$timestamp : start date as timestamp
	 * @return	string		$out : rendered tooltip
	 */
	function renderTooltip($event, $timestamp) {
		$out = '';

		if ($event['i']<1) {
			$markerArray['###TITLE###'] = strip_tags($event['title']);
			$markerArray['###TOOLTIP_ID###'] = 'td-tooltip-'.$event['uid'].'-'.$timestamp;
			if ($this->conf['showTooltipImage'])
				$markerArray['###IMAGE###'] = $event['image'] ? $this->getImage($event, 1) : '';
			else
				$markerArray['###IMAGE###'] = '';

			if($event['teaser']) {
					$markerArray['###TEASER###']  = $this->cObj->crop(strip_tags($event['teaser']), $this->conf['croppingLenght']);
				} else
					$markerArray['###TEASER###'] = $event['description'] ? $this->cObj->crop(strip_tags($event['description']), $this->conf['croppingLenght']):'';

			$markerArray['###BEGIN_LABEL###'] = $this->cObj->wrap($this->pi_getLL('beginLabel'),$this->conf['labelWrap']);

			if ($event['allday'] == 0) {
				$markerArray['###BEGIN_DATE###'] = strftime($this->conf['dateFormat'], $timestamp);//$event['begin']);
				$markerArray['###BEGIN_TIME###'] = strftime($this->conf['timeFormat'], $timestamp);
				$markerArray['###AT_LABEL###'] = $this->pi_getLL('atLabel');
				$markerArray['###LOCATION###'] = !empty($event['location_name']) ? $event['location_name'] : $event['location'];
				$markerArray['###ORGANIZER###'] = !empty($event['organizer_name']) ? $event['organizer_name'] : $event['organizer'];
				
				$subpartsArray['###CUT_END###'] = $event['end'] ? $this->cObj->substituteMarkerArray(
													$this->cObj->getSubpart($this->tooltipCode, '###CUT_END###'),
														array(
															'###END_LABEL###' => $this->cObj->wrap($this->pi_getLL('endLabel'), $this->conf['labelWrap']),
															'###END_DATE###' => strftime($this->conf['dateFormat'], $timestamp + ($event['end'] - $event['begin'])),
															'###END_TIME###' => strftime($this->conf['timeFormat'], $event['end']),
															'###AT_LABEL###' => $this->pi_getLL('atLabel')
														)
													):'';
			} else {

				$markerArray['###BEGIN_DATE###'] = strftime($this->conf['dateFormat'], $timestamp);//$event['begin']);
				$markerArray['###BEGIN_TIME###'] = $this->pi_getLL('alldayLabel');
				$markerArray['###AT_LABEL###'] = '';
				$markerArray['###LOCATION###'] = !empty($event['location_name']) ? $event['location_name'] : $event['location'];
				$markerArray['###ORGANIZER###'] = !empty($event['organizer_name']) ? $event['organizer_name'] : $event['organizer'];

				$subpartsArray['###CUT_END###'] = strtotime(strftime('%Y-%m-%d 0:00', $event['begin'])) < strtotime(strftime('%Y-%m-%d 0:00', $event['end'])) ? $this->cObj->substituteMarkerArray(
													$this->cObj->getSubpart($this->tooltipCode, '###CUT_END###'),
														array(
															'###END_LABEL###' => $this->cObj->wrap($this->pi_getLL('endLabel'), $this->conf['labelWrap']),
															'###END_DATE###' => strftime($this->conf['dateFormat'], $timestamp + ($event['end'] - $event['begin'])),
															'###END_TIME###' => '',
															'###AT_LABEL###' => ''
														)
													):'';
			}

			$out = $this->cObj->substituteMarkerArrayCached($this->tooltipCode, $markerArray, $subpartsArray);
		}

		return $out;
	}

	/**
	 * Build Exception Event function : Builds Output of ExcEvents
	 *
	 * @param	array		$excevent : exception events array
	 * @return	string		$rendered : rendered exception event output
	 */
	function buildExcEvents($excevents) {
		$rendered	= '';
		if(is_array($excevents) AND !$this->conf['hideExcEvents']) {
			$tempExc = $this->cObj->getSubpart($this->templateCode, '###EXCEVENT_VIEW###');
			foreach( $excevents as $event){
				$markerArray['###COLOR###'] = $event['color'] ? 'background: '.$event['color'].';': '';
				$markerArray['###EXC_TITLE###'] = $event['title'];
				$rendered .= $this->cObj->substituteMarkerArrayCached($tempExc, $markerArray);
			}
		}
		return $rendered;
	}

	/**
	 * get Image Function: Fills the image markers with data. Based upon getImageMarkers function in tt_news
	 *
	 * @param	array		$row : result row for a news item
	 * @param	array		$conf : configuration for the current templatepart
	 * @param	string		$textRenderObj : name of the template subpart
	 * @return	array		$markerArray: filled markerarray
	 */
	function getImage($row, $stopRow = 0 ) {
		$out = '';
		$images = t3lib_div::trimExplode(',', $row['image'], 1);
		$imagesCaptions = explode(chr(10), $row['imagecaption']);
		$imagesAltTexts = explode(chr(10), $row['imagealttext']);
		$imagesTitleTexts = explode(chr(10), $row['imagetitletext']);

		$FFmaxW = $this->fetchConfigurationValue('imageMaxWidth', 's_template');
		$FFmaxH = $this->fetchConfigurationValue('imageMaxHeight', 's_template');

		if ($FFmaxW || $FFmaxH) {
			$this->conf['image.']['file.']['maxW'] = $FFmaxW;
			$this->conf['image.']['file.']['maxH'] = $FFmaxH;
		}

		$suf = '';
		if (is_numeric(substr($this->conf['image.']['file.']['maxW'], - 1))) { // 'm' or 'c' not set by TS
			if ($this->conf['imageMode']) {
				switch ($this->conf['imageMode']) {
					case 'resize2max' :
						$suf = 'm';
						break;
					case 'crop' :
						$suf = 'c';
						break;
					case 'resize' :
						$suf = '';
						break;
				}
			}
		}

		// only insert width/height if it is not given by TS and width/height is empty
		if ($this->conf['image.']['file.']['maxW'] && !$this->conf['image.']['file.']['width']) {
			$this->conf['image.']['file.']['width'] = $this->conf['image.']['file.']['maxW'] . $suf;
			unset($this->conf['image.']['file.']['maxW']);
		}
		if ($this->conf['image.']['file.']['maxH'] && !$this->conf['image.']['file.']['height']) {
			$this->conf['image.']['file.']['height'] = $this->conf['image.']['file.']['maxH'] . $suf;
			unset($this->conf['image.']['file.']['maxH']);
		}

		$cc= 0;

		foreach ($images as $val) {
			if ($stopRow AND cc== $stopRow)
				break;

			if ($val) {
				$this->conf['image.']['altText'] = $imagesAltTexts[$cc];
				$this->conf['image.']['titleText'] = $imagesTitleTexts[$cc];
				$this->conf['image.']['file'] = 'uploads/pics/' . $val;

				$out .= $this->cObj->IMAGE($this->conf['image.']);
				if (!$this->conf['hideImageCaption'])
					$out.= $this->cObj->stdWrap($imagesCaptions[$cc], $this->conf['caption_stdWrap.']);
			}
			$cc++;
		}

		/*$imgConfig = array();
		$imgConfig['file'] = 'uploads/pics/' . $img;
		$imgConfig['file.']['width'] = "200m";
		$imgConfig['file.']['height'] = "150m";
		$imgConfig['file.']['maxH'] = "200";
		$imgConfig['file.']['maxW'] = "300";

		$bildstring = $this->cObj->IMAGE($imgConfig);
		$bildadresse = $this->cObj->IMG_RESOURCE($imgConfig);  */

		if (!empty($out)) {
			return $this->cObj->wrap($out, $this->conf['imageWrap']);
		} else {
			return;
		}
	}


	/**
	 * Make Link function : internal link builing for the extension by typo3
	 *
	 * @param	string		$label : label for link
	 * @param	string		$link : link url
	 * @param	[type]		$ATagParams: ...
	 * @return	string		rendered link
	 */
	function makeLink($label, $link, $ATagParams = ''){
		if(is_array($this->conf['typolink.']))
			$myConf = $this->conf['typolink.'];
		$myConf['parameter'] = $link;
		if($ATagParams) {
			$myConf['ATagParams'] = $ATagParams;
		}
		return $this->cObj->typoLink($label, $myConf);
    }

 	/**
	* Submit Back function : output for BackButtons
	*
	* @param	string		$backbutton : alternative back button label
	* @return	string		$out: backbutton style
	*/
	function submitBack($backbutton = FALSE){
		$backButtonLabel = is_string($backbutton) ? $backbutton : $this->pi_getLL('backButtonLabel');

		$out .= '<div class="tx_td_backbutton"><a href="javascript:history.back()">'.$backButtonLabel.'</a></div>';
		return $out;
	}

	/**
	 * add Script Resources function : Adds Javascript and CSS files to Pagerendering
	 * based upon addResources function in Extension sexybookmarks by Juergen Furrer
	 *
	 * @return	[type]		...
	 */
	function addScriptResources()	{
		if (t3lib_extMgm::isLoaded('t3jquery')) {
			require_once(t3lib_extMgm::extPath('t3jquery').'class.tx_t3jquery.php');
		}

		// checks if t3jquery is loaded
		if (T3JQUERY === TRUE) {
			tx_t3jquery::addJqJS();
		} else {
			if($this->conf['jQueryRes']) $this->jsFile[] = $this->conf['jQueryRes'];
			if($this->conf['tooltipJSRes']) $this->jsFile[] = $this->conf['tooltipJSRes'];
		}

		// Fix moveJsFromHeaderToFooter (add all scripts to the footer)
		if ($GLOBALS['TSFE']->config['config']['moveJsFromHeaderToFooter']) {
			$allJsInFooter = TRUE;
		} else {
			$allJsInFooter = FALSE;
		}

		$pagerender = $GLOBALS['TSFE']->getPageRenderer();
		
		// add all defined JS files
		if (count($this->jsFile) > 0) {
			foreach ($this->jsFile as $jsToLoad) {
				if (T3JQUERY === TRUE) {
					$conf = array(
						'jsfile' => $jsToLoad,
						'tofooter' => ($this->conf['jsInFooter'] || $allJsInFooter),
						'jsminify' => $this->conf['jsMinify'],
					);
					tx_t3jquery::addJS('', $conf);
				} else {
					$file = $GLOBALS['TSFE']->tmpl->getFileName($jsToLoad);
					if ($file) {
						if ($allJsInFooter) {
							$pagerender->addJsFooterFile($file, 'text/javascript', $this->conf['jsMinify']);
						} else {
							$pagerender->addJsFile($file, 'text/javascript', $this->conf['jsMinify']);
						}
					} else {
						t3lib_div::devLog("'{".$jsToLoad."}' does not exists!", $this->extKey, 2);
					}
				}
			}
		}

		// add defined CSS file
		if($this->conf['cssFile']) {
			// Add script only once
			$css = $GLOBALS['TSFE']->tmpl->getFileName($this->conf['cssFile']);
			if ($css) {
				$pagerender->addCssFile($css, 'stylesheet', 'all', '', $this->conf['cssMinify']);
			} else {
				t3lib_div::devLog("'{".$this->conf['cssFile']."}' does not exists!", $this->extKey, 2);
			}
		}
	}

	/**
	 * print Errors function : print Errors to frontend
	 *
	 * @return	string		$c: string of recorded errors
	 */
	function printErrors() {
		$c = '<b>Upps, there\'s an error:</b> ';
		foreach ($this->error as $error) {
			$c .= $error.' ';
		}
		return $c;
	}


	/**
	 * extSort function : sort events
	 *
	 * @param	array		$aItem : event array
	 * @param	array		$bItem : event array
	 * @return	string		sort
	 */
	 function extSort($aItem,$bItem){
        $aBegin = $aItem['mday'] ? $aItem['mday'] : $aItem['begin'];
        $bBegin = $bItem['mday'] ? $bItem['mday'] : $bItem['begin'];

        if($aItem['allday']<$bItem['allday']){
            return 1;
        }elseif($aItem['allday']>$bItem['allday'])
            return -1;
       
        if($aBegin>$bBegin){
            return 1;
        }elseif($aBegin<$bBegin){
            return -1;
        }

        if($aItem['uid']<$bItem['uid']){
            return -1;
        }elseif($aItem['uid']>$bItem['uid'])
            return 1;
        return 0;
    }
/**
 * extSortUpcoming function : sort events for Upcoming View
 *
 * @param	array		$aItem : event array
 * @param	array		$bItem : event array
 * @return	string		sort
 */	
	function extSortUpcoming($aItem,$bItem){
        $aBegin = $aItem['mday'] ? $aItem['mday'] : $aItem['begin'];
        $bBegin = $bItem['mday'] ? $bItem['mday'] : $bItem['begin'];

        if($aBegin>$bBegin){
            return 1;
        }elseif($aBegin<$bBegin){
            return -1;
        }

        if($aItem['uid']<$bItem['uid']){
            return -1;
        }elseif($aItem['uid']>$bItem['uid'])
            return 1;
        return 0;
    }

/**
 * excSort function : sort exc_events
 *
 * @param	array		$aItem : event array
 * @param	array		$bItem : event array
 * @return	string		sort
 */
	function excSort($aItem,$bItem){
		if($aItem['bgcolor']<$bItem['bgcolor']){
			return 1;
		}elseif($aItem['bgcolor']>$bItem['bgcolor'])
			return -1;

		if($aItem['priority']>$bItem['priority']){
			return 1;
		}elseif($aItem['priority']<$bItem['priority']){
			return -1;
		}

		if($aItem['uid']<$bItem['uid']){
			return -1;
		}elseif($aItem['uid']>$bItem['uid'])
			return 1;
		return 0;
	}

	/**
     * Get View Selection function : renders view selection, if enabled
	 * @param	integer		$vTime: timestamp to view
	 * @param	string		$whoami: type of calling view ['l','m','w','d']
     * @return      string          rendered view selection
     */
	function getViewSelection($vTime=0,$whoami='') {
		if($this->conf['hideViewSelection'])
			return '';

		$mvars = array();
		$mvars['year']=strftime('%Y', $vTime);
		$mvars['month']= strftime('%m', $vTime);
		$mvars['day']= strftime('%d', $vTime);

		$myId = intval($GLOBALS['TSFE']->id);

		$spid['titleViewList'] = $this->conf['PIDlistDisplay'];
		$spid['titleMonthView'] = $this->conf['PIDmonthDisplay'];
		$spid['titleWeekView'] = $this->conf['PIDweekDisplay'];
		$spid['titleDayView'] = $this->conf['PIDsingleDayDisplay'];

		switch ($whoami) {
			case 'l':
				$spid['titleViewList'] = $myId;
				$sel=$this->pi_getLL('titleViewlist');
				break;
			case 'm':
				$spid['titleMonthView'] = $myId;
				$sel=$this->pi_getLL('titleMonthView');
				break;
			case 'w':
				$spid['titleWeekView'] = $myId;
				$sel=$this->pi_getLL('titleWeekView');
				break;
			case 'd':
				$spid['titleDayView'] = $myId;
				$sel=$this->pi_getLL('titleDayView');
				break;
		}

		$slist = array();
		foreach($spid as $view => $viewPid) {
			if ($viewPid > 0) {
				$slist[$this->pi_linkTP_keepPIvars_url($mvars, $this->caching, 0, $viewPid)] = $this->pi_getLL($view);
			}
		}
       
		return $this->selectInputOnChange('category', $slist, $sel ,"document.location = '' + this.options[selectedIndex].value;");
	}	
	
}
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/td_calendar/pi1/class.tx_tdcalendar_pi1_libary.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/td_calendar/pi1/class.tx_tdcalendar_pi1_libary.php']);
}
 ?>