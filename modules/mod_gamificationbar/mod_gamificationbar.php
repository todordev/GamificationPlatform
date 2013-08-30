<?php
/**
 * @package      Gamification Platform
 * @subpackage   Modules
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Gamification Platform is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined( "_JEXEC" ) or die;

jimport("gamification.init");

// Get component parameters
$componentParams = JComponentHelper::getParams("com_gamification");

$doc = JFactory::getDocument();

$doc->addStyleSheet(JURI::root().'modules/mod_gamificationbar/css/style.css');
$doc->addScript(JURI::root()."modules/mod_gamificationbar/js/jquery.gamificationnotifications.js");
$js = '
    jQuery(document).ready(function() {
        jQuery("#gfy-ntfy").GamificationNotifications({
            resultsLimit: '.$params->get("results_limit", 5).'
        });
                    
    });
';
$doc->addScriptDeclaration($js);

require JModuleHelper::getLayoutPath('mod_gamificationbar', $params->get('layout', 'default'));