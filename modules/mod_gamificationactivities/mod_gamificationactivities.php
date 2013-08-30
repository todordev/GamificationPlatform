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

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

jimport("gamification.init");

// Get component parameters
$componentParams = JComponentHelper::getParams("com_gamification");

// Load helpers
JHtml::addIncludePath(GAMIFICATION_PATH_COMPONENT_SITE.'/helpers/html');

$imagePath       = $componentParams->get("images_directory", "images/gamification");

// $doc = JFactory::getDocument();
// $doc->addStyleSheet('media/com_gamification/css/modules/mod_gamificationactivities.css');

jimport('gamification.activities');

$options = array(
    "sort_direction" => "DESC",
    "limit"          => $params->get("results_number", 10)
);

$activities     = new GamificationActivities();
$activities->load($options);

$avatarSize     = $params->get("avatar_size", 50);
$nameLinkable   = $params->get("name_linkable", 1);
$integrateType  = $params->get("integrate", "none");

$socialProfiles = null;
$numberItems    = count($activities);

if( (strcmp("none", $integrateType) != 0) AND !empty($numberItems)) {
    
    foreach($activities as $item) {
        $usersIds[] = $item->user_id; 
    }
    
    jimport("itprism.integrate.profiles");
    $socialProfiles = ITPrismIntegrateProfiles::factory($integrateType, $usersIds);
}

require JModuleHelper::getLayoutPath('mod_gamificationactivities', $params->get('layout', 'default'));