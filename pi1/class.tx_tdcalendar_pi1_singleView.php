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
 *   44: class tx_tdcalendar_pi1_singleView extends tx_tdcalendar_pi1_library
 *   50:     function displaySingle()
 *   96:     function getSingleArray($event)
 *  124:     function getArrayFromUID($uid, $table)
 *  145:     function buildSingleOutput($row)
 *  242:     function buildLocationOutput($row)
 *  292:     function buildOrganizerOutput($row)
 *  343:     function getOrganizerLink($organizer)
 *  363:     function getLocationLink($location)
 *  384:     function queryFromSingleUID($uid, $table)
 *
 * TOTAL FUNCTIONS: 9
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */
class tx_tdcalendar_pi1_singleView extends tx_tdcalendar_pi1_library {
  	/**
 * Diplay Month Function: initialize single view of calendar and renders single, organizer or location view
 *
 * @return	string		rendered single, organizer or location view or error
 */
	function displaySingle() {
		$this->fetchCurrValue('singleViewIsShy', '0', 'sDEF');
		$this->fetchCurrValue('forceSamePageFunc', '0', 'sDEF');

		$this->conf['eventUID'] = (!empty($this->piVars['event']) AND is_numeric($this->piVars['event']) ) ? $this->piVars['event']: 0;
		$this->conf['organizer'] = (!empty($this->piVars['organizer']) AND is_numeric($this->piVars['organizer']) ) ? $this->piVars['organizer']: 0;
		$this->conf['location'] = (!empty($this->piVars['location']) AND is_numeric($this->piVars['location']) ) ? $this->piVars['location']: 0;

		if ($this->conf['eventUID']) {
			$event=$this->getSingleArray($this->conf['eventUID']);
			if (is_array($event)) {
				return $this->buildSingleOutput($event);
			} else {
				$error = $this->pi_getLL('NoEventFound');
			}
		} elseif ($this->conf['organizer']) {
			$organizer = $this->getArrayFromUID($this->conf['organizer'], 'tx_tdcalendar_organizer');
			if (is_array($organizer)) {
				return $this->buildOrganizerOutput($organizer);
			} else {
				$error = $this->pi_getLL('NoOrganizerFound');
			}
		} elseif ($this->conf['location']) {
			$location = $this->getArrayFromUID($this->conf['location'], 'tx_tdcalendar_locations');
			if (is_array($location)) {
				return $this->buildLocationOutput($location);
			} else {
				$error = $this->pi_getLL('NoLocationFound');
			}
		} else {
			$error = $this->pi_getLL('NoEventUID');
		}

		if ($this->conf['singleViewIsShy'] > 0) {
			return '';
		}else {
			return $error;
		}
	}

	/**
	 * Get Single Array function : get single array from db
	 *
	 * @param	string		$event : uid of single event
	 * @return	array		array of single event
	 */
	function getSingleArray($event){
		$select_fields = 	'tx_tdcalendar_events.*';
		$select_fields	.=	',tx_tdcalendar_categories.title as category';
		$select_fields	.=	',tx_tdcalendar_events.title as title';
	    $select_fields 	.=	',tx_tdcalendar_categories.color as catcolor';
    	$from_table 	=	'tx_tdcalendar_events ';
		$from_table		.=	'INNER JOIN tx_tdcalendar_categories ';
		$from_table		.=	'ON (tx_tdcalendar_categories.uid = tx_tdcalendar_events.category)';
		$where_clause	=	"tx_tdcalendar_events.uid = '".$event."'";
		$where_clause .= 	$this->getCategoryQuery('tx_tdcalendar_events.category');
		$where_clause .=	$this->getPagesQuery();
		$where_clause	.=	$this->enableFieldsCategories;
		$where_clause	.=	$this->enableFieldsEvents;

		return $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow(
			$select_fields,
			$from_table,
			$where_clause
		);
	}

	/**
	 * Get Array form UID function : get single location or organizer array from db
	 *
	 * @param	string		$uid : uid of item
	 * @param	string		$table : table, from which the uid is to be selected
	 * @return	array		array of single array
	 */
	function getArrayFromUID($uid, $table){
		$select_fields = 	'*';
    	$from_table 	=	$table;
		$where_clause	=	"uid = '".$uid."'";
		$where_clause  .= 	$this->cObj->enableFields($table);
		$where_clause .=	$this->getPagesQuery($table);

		return $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow(
			$select_fields,
			$from_table,
			$where_clause
		);

	}

	/**
	 * Build Single Output function : renders single view
	 *
	 * @param	array		$row : array of single view
	 * @return	string		rendered single view
	 */
	function buildSingleOutput($row){
	  	$SingleViewT = $this->cObj->getSubpart($this->templateCode, '###SINGLE_VIEW###');
    	$markerArray = array(); // Simple markers
    	$markerArray['###VIEW_TITLE###'] = $this->cObj->wrap($this->pi_getLL('singleEventTitle'), $this->conf['viewTitleWrap']);
    	$markerArray['###CATEGORY###'] = $row['category'] ? $this->cObj->wrap(strip_tags($row['category']), $this->conf['categoryWrap']) : '';
    	$markerArray['###IMAGE###'] =	$row['image'] ? $this->getImage($row) : '';
	    $markerArray['###TITLE###'] = $row['title'] ? $this->cObj->wrap(strip_tags($row['title']), $this->conf['titleWrap']) : '';
		$markerArray['###TEASER###'] = $row['teaser'] ? $this->cObj->wrap(strip_tags($row['teaser']), $this->conf['teaserWrap']) : '';
	    $markerArray['###DESCRIPTION###'] = $row['description'] ? $this->cObj->wrap($this->cObj->parseFunc($row['description'], $this->conf['parseFunc.']), $this->conf['descWrap']) : '';
		$markerArray['###LINK###'] = $row['link'] ? $this->cObj->wrap($this->makeLink($this->pi_getLL('readMoreLinkLabel'), $row['link']), $this->conf['itemLinkWrap']) : '';

		if($row['organizer_id']){
			$subpartsArray['###CUT_ORGANIZER###'] = 	$this->cObj->substituteMarkerArray(
												$this->cObj->getSubpart($SingleViewT, '###CUT_ORGANIZER###'),
												array(
													'###ORGANIZER###' => $this->getOrganizerLink($row['organizer_id']),		//$this->getLocationLink($row['location_id']);
													'###ORGANIZER_LABEL###' => $this->cObj->wrap($this->pi_getLL('organizerLabel'), $this->conf['labelWrap']),
													'###EMAIL###' => ''
												)
											);
		} else if($row['organizer']){
			$subpartsArray['###CUT_ORGANIZER###'] = 	$this->cObj->substituteMarkerArray(
												$this->cObj->getSubpart($SingleViewT, '###CUT_ORGANIZER###'),
												array(
													'###ORGANIZER###' => strip_tags($row['organizer']),
													'###ORGANIZER_LABEL###' => $this->cObj->wrap($this->pi_getLL('organizerLabel'), $this->conf['labelWrap']),
													'###EMAIL###' => $row['email']? $this->cObj->wrap($this->makeLink('', $row['email']), $this->conf['mailLinkWrap']) : ''
												)
											);
		} else {
			$subpartsArray['###CUT_ORGANIZER###'] = '';
		}

    	if($row['location_id']){
			$subpartsArray['###CUT_LOCATION###'] = 	$this->cObj->substituteMarkerArray(
												$this->cObj->getSubpart($SingleViewT, '###CUT_LOCATION###'),
												array(
													'###LOCATION###' => $this->getLocationLink($row['location_id']),
													'###LOCATION_LABEL###' => $this->cObj->wrap($this->pi_getLL('locationLabel'), $this->conf['labelWrap'])
												)
											);
		} else if($row['location']){
			$subpartsArray['###CUT_LOCATION###'] = 	$this->cObj->substituteMarkerArray(
												$this->cObj->getSubpart($SingleViewT, '###CUT_LOCATION###'),
												array(
													'###LOCATION###' => strip_tags($row['location']),
													'###LOCATION_LABEL###' => $this->cObj->wrap($this->pi_getLL('locationLabel'), $this->conf['labelWrap'])
												)
											);
		} else {
			$subpartsArray['###CUT_LOCATION###'] = '';
		}

		$markerArray['###BEGIN_LABEL###'] = $this->cObj->wrap($this->pi_getLL('beginLabel'), $this->conf['labelWrap']);
		$markerArray['###COLOR###'] =	$row['catcolor'] ? $this->cObj->wrap($row['catcolor'], $this->conf['wrapColorSingleView']):'';

		$this->conf['currTime'] = $this->conf['currTime'] + ($row['begin'] - strtotime(strftime('%Y-%m-%d 0:00', $row['begin'])));

		if ($row['allday'] == 0) {
			$markerArray['###BEGIN_DATE###'] = strftime($this->conf['dateFormat'], $this->conf['currTime']);//$row['begin']);
			$markerArray['###BEGIN_TIME###'] = strftime($this->conf['timeFormat'],$this->conf['currTime']);
			$markerArray['###AT_LABEL###'] = $this->pi_getLL('atLabel');
			$subpartsArray['###CUT_END###'] = $row['end'] ? $this->cObj->substituteMarkerArray(
														$this->cObj->getSubpart($SingleViewT, '###CUT_END###'),
														array(
															'###END_LABEL###' => $this->cObj->wrap($this->pi_getLL('endLabel'), $this->conf['labelWrap']),
															'###END_DATE###' => strftime($this->conf['dateFormat'], $this->conf['currTime'] + ($row['end'] - $row['begin'])),
															'###END_TIME###' => strftime($this->conf['timeFormat'], $row['end']),
															'###AT_LABEL###' => $this->pi_getLL('atLabel')
														)
													):'';
		} else {
			$markerArray['###BEGIN_DATE###'] = strftime($this->conf['dateFormat'], $this->conf['currTime']);//$row['begin']);
			$markerArray['###BEGIN_TIME###'] = $this->pi_getLL('alldayLabel');
			$markerArray['###AT_LABEL###'] = '';

			$subpartsArray['###CUT_END###'] = strtotime(strftime('%Y-%m-%d 0:00', $row['begin'])) < strtotime(strftime('%Y-%m-%d 0:00', $row['end'])) ? $this->cObj->substituteMarkerArray(
														$this->cObj->getSubpart($SingleViewT, '###CUT_END###'),
														array(
															'###END_LABEL###' => $this->cObj->wrap($this->pi_getLL('endLabel'), $this->conf['labelWrap']),
															'###END_DATE###' => strftime($this->conf['dateFormat'], $this->conf['currTime'] + ($row['end'] - $row['begin'])),
															'###END_TIME###' => '',
															'###AT_LABEL###' => ''
														)
													):'';
		}

		$markerArray['###SUBMIT_EVENT###'] = $this->submitBack();
    	return $this->cObj->substituteMarkerArrayCached($SingleViewT, $markerArray, $subpartsArray);
	}

	/**
	 * Build Location Output function : renders location view
	 *
	 * @param	array		$row : array of location view
	 * @return	string		rendered location view
	 */
	function buildLocationOutput($row){
	  	$LocationViewT = $this->cObj->getSubpart($this->templateCode, '###LOCATION_VIEW###');
    	$markerArray = array(); // Simple markers
		$subpartsArray = array();
		$markerArray['###VIEW_TITLE###'] = $this->cObj->wrap($this->pi_getLL('locationTitle'), $this->conf['viewTitleWrap']);
    	$markerArray['###IMAGE###'] =	$row['image'] ? $this->getImage($row): '';
	    $markerArray['###LOCATION###'] = $row['location'] ? $this->cObj->wrap(strip_tags($row['location']), $this->conf['titleWrap']) : '';
	    $markerArray['###DESCRIPTION###'] = $row['description'] ? $this->cObj->wrap($this->cObj->parseFunc($row['description'], $this->conf['parseFunc.']), $this->conf['descWrap']) : '';

    	$subpartsArray['###CUT_ADDRESS###'] = $row['city'] || $row['street'] || $row['zip'] ? $this->cObj->substituteMarkerArray(
														$this->cObj->getSubpart($LocationViewT, '###CUT_ADDRESS###'),
														array(
															'###ADDR_NAME###' => $row['location'],
															'###STREET###' => $row['street'] ? $row['street'] : '',
															'###ZIP###' => $row['zip'] ? $row['zip'] : '',
															'###CITY###' => $row['city'] ? $row['city'] : '',
															'###ADDRESS_LABEL###' => $this->cObj->wrap($this->pi_getLL('addressLabel'), $this->conf['labelWrap'])
														)
													):'';
		 $subpartsArray['###CUT_PHONE###'] = $row['phone'] ? $this->cObj->substituteMarkerArray(
														$this->cObj->getSubpart($LocationViewT, '###CUT_PHONE###'),
														array(
															'###PHONE###' => $row['phone'],
															'###PHONE_LABEL###' => $this->cObj->wrap($this->pi_getLL('phoneLabel'), $this->conf['labelWrap'])
														)
													):'';
		$subpartsArray['###CUT_EMAIL###'] = $row['email'] ? $this->cObj->substituteMarkerArray(
														$this->cObj->getSubpart($LocationViewT, '###CUT_EMAIL###'),
														array(
															'###EMAIL###' => $this->makeLink('', $row['email']),
															'###EMAIL_LABEL###' => $this->cObj->wrap($this->pi_getLL('emailLabel'), $this->conf['labelWrap'])
														)
													):'';
		$subpartsArray['###CUT_LINK###'] = $row['link'] ? $this->cObj->substituteMarkerArray(
														$this->cObj->getSubpart($LocationViewT, '###CUT_LINK###'),
														array(
															'###LINK###' => $this->makeLink('', $row['link']),
															'###LINK_LABEL###' => $this->cObj->wrap($this->pi_getLL('homepageLabel'), $this->conf['labelWrap'])
														)
													):'';
		$markerArray['###BACK###'] = $this->submitBack();
    	return $this->cObj->substituteMarkerArrayCached($LocationViewT, $markerArray, $subpartsArray);
	}

	/**
	 * Build Organizer Output function : renders organizer view
	 *
	 * @param	array		$row : array of organizer view
	 * @return	string		rendered organizer view
	 */
	function buildOrganizerOutput($row){
	  	$OrganizerViewT = $this->cObj->getSubpart($this->templateCode, '###ORGANIZER_VIEW###');
    	$markerArray = array(); // Simple markers
		$subpartsArray = array();
		$markerArray['###VIEW_TITLE###'] = $this->cObj->wrap($this->pi_getLL('organizerTitle'), $this->conf['viewTitleWrap']);

    	$markerArray['###IMAGE###'] =	$row['image'] ? $this->getImage($row): '';
	    $markerArray['###NAME###'] = $row['name'] ? $this->cObj->wrap(strip_tags($row['name']), $this->conf['titleWrap']) : '';
	    $markerArray['###DESCRIPTION###'] = $row['description'] ? $this->cObj->wrap($this->cObj->parseFunc($row['description'], $this->conf['parseFunc.']), $this->conf['descWrap']) : '';

    	$subpartsArray['###CUT_ADDRESS###'] = $row['city'] || $row['street'] || $row['zip'] ? $this->cObj->substituteMarkerArray(
														$this->cObj->getSubpart($OrganizerViewT, '###CUT_ADDRESS###'),
														array(
															'###ADDR_NAME###' => $row['name'],
															'###STREET###' => $row['street'] ? $row['street'] : '',
															'###ZIP###' => $row['zip'] ? $row['zip'] : '',
															'###CITY###' => $row['city'] ? $row['city'] : '',
															'###ADDRESS_LABEL###' => $this->cObj->wrap($this->pi_getLL('addressLabel'), $this->conf['labelWrap'])
														)
													):'';
		 $subpartsArray['###CUT_PHONE###'] = $row['phone'] ? $this->cObj->substituteMarkerArray(
														$this->cObj->getSubpart($OrganizerViewT, '###CUT_PHONE###'),
														array(
															'###PHONE###' => $row['phone'],
															'###PHONE_LABEL###' => $this->cObj->wrap($this->pi_getLL('phoneLabel'), $this->conf['labelWrap'])
														)
													):'';
		$subpartsArray['###CUT_EMAIL###'] = $row['email'] ? $this->cObj->substituteMarkerArray(
														$this->cObj->getSubpart($OrganizerViewT, '###CUT_EMAIL###'),
														array(
															'###EMAIL###' => $this->makeLink('', $row['email']),
															'###EMAIL_LABEL###' => $this->cObj->wrap($this->pi_getLL('emailLabel'), $this->conf['labelWrap'])
														)
													):'';
		$subpartsArray['###CUT_LINK###'] = $row['link'] ? $this->cObj->substituteMarkerArray(
														$this->cObj->getSubpart($OrganizerViewT, '###CUT_LINK###'),
														array(
															'###LINK###' => $this->makeLink('', $row['link']),
															'###LINK_LABEL###' => $this->cObj->wrap($this->pi_getLL('homepageLabel'), $this->conf['labelWrap'])
														)
													):'';
		$markerArray['###BACK###'] = $this->submitBack();
    	return $this->cObj->substituteMarkerArrayCached($OrganizerViewT, $markerArray, $subpartsArray);
	}

	/**
	 * Get Organizer Link function : build organizer link for organizer-UID
	 *
	 * @param	string		$organizer : organizer uid
	 * @return	string		rendered link for organizer from DB
	 */
	function getOrganizerLink($organizer){
		$org = $this->queryFromSingleUID($organizer, 'tx_tdcalendar_organizer');
		if($this->conf['forceSamePageFunc']) {
			$vars['year']=strftime('%Y', $this->conf['currTime']);
			$vars['month']=strftime('%m', $this->conf['currTime']);
			$vars['day']= strftime('%d', $this->conf['currTime']);
			$vars['uid'] = $this->uid;
			if(!empty($this->conf['currCat'])) $vars['category'] = $this->conf['currCat'];
		}
		$vars['organizer'] = $organizer;
		$link = $org['name'] ? $this->pi_linkTP($org['name'], array($this->prefixId=>$vars), $this->caching, $this->conf['pid']) : '';
		return $link;
    }

	/**
	 * Get Location Link function : build location link for location-UID
	 *
	 * @param	string		$location : location uid
	 * @return	string		rendered link for location from DB
	 */
	function getLocationLink($location){
		$org = $this->queryFromSingleUID($location, 'tx_tdcalendar_locations');
		if($this->conf['forceSamePageFunc']) {
			$vars['year']=strftime('%Y', $this->conf['currTime']);
			$vars['month']=strftime('%m', $this->conf['currTime']);
			$vars['day']= strftime('%d', $this->conf['currTime']);
			$vars['uid'] = $this->uid;
			if(!empty($this->conf['currCat'])) $vars['category'] = $this->conf['currCat'];
		}
		$vars['location'] = $location;
		$link = $org['location'] ? $this->pi_linkTP($org['location'], array($this->prefixId=>$vars),$this->caching,$this->conf['pid']): '';
		return $link;
    }

	/**
	 * Query from Single UID function : select array for location or organizer by uid
	 *
	 * @param	string		$uid : uid  to be selected
	 * @param	[type]		$table: ...
	 * @return	string		array of uid
	 */
	function queryFromSingleUID($uid, $table){
		$select_fields  = 	'*';
		$from_table		= 	$table;
		$where_clause	= 	'uid = \''.$uid.'\'';
		$where_clause  .= 	$this->cObj->enableFields($table);
		$where_clause .=	$this->getPagesQuery($table);

		return $GLOBALS['TYPO3_DB']->exec_SELECTgetSingleRow(
			$select_fields,
			$from_table,
			$where_clause
		);
    }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/td_calendar/pi1/class.tx_tdcalendar_pi1_singleView.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/td_calendar/pi1/class.tx_tdcalendar_pi1_singleView.php']);
}
 ?>