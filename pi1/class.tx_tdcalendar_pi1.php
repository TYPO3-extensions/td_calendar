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
 *   50: class tx_tdcalendar_pi1 extends tx_tdcalendar_pi1_library
 *   64:     function main($content, $conf)
 *  110:     function init()
 *
 * TOTAL FUNCTIONS: 2
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */

require_once(t3lib_extMgm::extPath('td_calendar').'pi1/class.tx_tdcalendar_pi1_library.php');

//require_once(PATH_tslib.'class.tslib_pibase.php');


/**
 * Plugin 'TD Calendar' for the 'td_calendar' extension.
 *
 * @author    Thomas Dudzak <thomas@buergerbuero-borna.de>
 * @package    TYPO3
 * @subpackage    tx_tdcalendar
 */
class tx_tdcalendar_pi1 extends tx_tdcalendar_pi1_library {
    var $prefixId          = 'tx_tdcalendar_pi1';        // Same as class name
    var $scriptRelPath     = 'pi1/class.tx_tdcalendar_pi1.php';    // Path to this script relative to the extension dir.
    var $extKey            = 'td_calendar';    // The extension key.
    var $pi_checkCHash     = TRUE;
    var $caching         = 1;

    /**
     * The main method of the PlugIn
     *
     * @param    string        $content: The PlugIn content
     * @param    array        $conf: The TS-PlugIn configuration
     * @return    The        content that is displayed on the website
     */
    function main($content, $conf) {
        $this->conf=$conf;
        $this->configure();
        $this->pi_initPIflexForm();
        $this->init();

        if (empty($this->error)) {
            switch (strtoupper($this->conf['view'])) {
                case 'LIST' :
                    $view = $this->initViewClass('list');
                    $content .= $view->displayList();
                    break;
                case 'MONTH' :
                    $view = $this->initViewClass('month');
                    $content .= $view->displayMonth();
                    break;
                case 'WEEK' :
                    $view = $this->initViewClass('week');
                    $content .= $view->displayWeek();
                    break;
                case 'DAY' :
                    $view = $this->initViewClass('day');
                    $content .= $view->displayDay();
                    break;
                case 'SINGLE' :
                    $view = $this->initViewClass('single');
                    $content .= $view->displaySingle();
                    break;
                default :
                    $this->error[] = 'No correct TS';
                    break;
            }
        }

        if(!empty($this->error)) {
            $content = $this->printErrors();
        }
        return $this->pi_wrapInBaseClass($content);
    }


    /**
     * initialize function: read and set main values for plugin configuration
     *
     * @return    [type]        ... 
     */
    function init() {
        $this->fetchUserTime();
        if($this->piVars['category']!= 0 AND is_numeric($this->piVars['category'])) $this->conf['currCat'] = $this->piVars['category'];
        $this->conf['pid'] = $this->cObj->data['pid'];
        $this->conf['uid'] = $this->cObj->data['uid'];

        $viewmode = $this->fetchConfigurationValue('what_to_display', 'sDEF');
        $this->conf['view'] = (!empty($viewmode)) ? $viewmode : $this->conf['view'];
        if(empty($this->conf['view'])) $this->conf['view'] = 'MONTH';

        if(file_exists(t3lib_extMgm::extPath('td_calendar').'pi1/class.tx_tdcalendar_pi1_'.strtolower($this->conf['view']).'View.php')) {
            require_once(t3lib_extMgm::extPath('td_calendar').'pi1/class.tx_tdcalendar_pi1_'.strtolower($this->conf['view']).'View.php');
        } else {
            $this->error[] = 'No class defined for '.strtolower($this->conf['view']).'View';
        }

        if (is_array($this->conf[strtolower($this->conf['view']).'View.'])) {
            $this->conf= t3lib_div::array_merge_recursive_overrule($this->conf, $this->conf[strtolower($this->conf['view']).'View.']);
        }

        $this->addScriptResources();

        $this->fetchCurrValue('pidList', '', 'sDEF', 0);
        $this->fetchCurrValue('recursive', 0, 'sDEF', 0);
        $this->initPidList();

        if ($this->fetchConfigurationValue('hideTooltips', 's_misc') == 1) $this->conf['showTooltips'] = 0;

        $this->fetchCurrValue('dateFormat', $this->pi_getLL('stdDateFormat'), 's_misc', 0);
        $this->fetchCurrValue('timeFormat', $this->pi_getLL('stdTimeFormat'), 's_misc', 0);
        $this->fetchCurrValue('templateFile', 'EXT:td_calendar/res/tmpl/td_calendar.tmpl', 's_template');
        $this->fetchCurrValue('showMultiDayOnlyOnce', 0, 'sDEF', 1);
        $this->fetchCurrValue('hideCategorySelection', 0, 's_category', 1);
        $this->fetchCurrValue('categoryMode', 0, 's_category', 0);
        $this->fetchCurrValue('categorySelection', 0, 's_category', 0);
        $this->fetchCurrValue('croppingLenght', '250|...|true', 's_template', 0);

        $this->templateCode = $this->cObj->fileResource($this->conf['templateFile']);
        if (empty($this->templateCode)) {
                  $this->error[] = 'No Template File';
                return;
        }

        $this->enableFieldsCategories     =     $this->cObj->enableFields('tx_tdcalendar_categories');
        $this->enableFieldsEvents         =     $this->cObj->enableFields('tx_tdcalendar_events');
        $this->enableFieldsLocation     =     $this->cObj->enableFields('tx_tdcalendar_locations');
        $this->enableFieldsOrganizer     =     $this->cObj->enableFields('tx_tdcalendar_organizer');
        $this->enableFieldsExcEvents     =     $this->cObj->enableFields('tx_tdcalendar_exc_events');
        $this->enableFieldsExcCategories=     $this->cObj->enableFields('tx_tdcalendar_exc_categories');

        return;
    }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/td_calendar/pi1/class.tx_tdcalendar_pi1.php'])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/td_calendar/pi1/class.tx_tdcalendar_pi1.php']);
}

?>