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
JHtml::addIncludePath(ITPRISM_PATH_LIBRARY.'/ui/helpers');

// Load Twitter Bootstrap
if($componentParams->get("bootstrap_include", 1)) {
    JHtml::_("itprism.ui.bootstrap");
}

$imagePath       = $componentParams->get("images_directory", "images/gamification");

$doc = JFactory::getDocument();
$doc->addStyleSheet('media/com_gamification/css/modules/mod_gamificationleaderboard.css');

$layout  =  $params->get('layout', 'default');

jimport('gamification.leaderboard');

switch($layout) {
    
    case "levels":
        
        $displayLevelTitle  = $params->get("levels_display_title", 1);
        $displayLevelRank   = $params->get("levels_display_rank", 0);
        
        $keys = array(
            "group_id" => $params->get("levels_group_id")
        );
        
        $options = array(
            "sort_direction" => "DESC",
            "limit"          => $params->get("results_number", 10)
        );
        
        $leaderboard    = GamificationLeaderboard::factory("levels", $keys, $options);
        break;
        
    default: // Points
        
        $pointsType     = $params->get("points_display_type", "abbr");
        
        $keys = array(
            "points_id" => $params->get("points_id")
        );
        
        $options = array(
            "sort_direction" => "DESC",
            "limit"          => $params->get("results_number", 10)
        );
        
        $leaderboard    = GamificationLeaderboard::factory("points", $keys, $options);
        break;
}

$displayNumber  = $params->get("display_number", 1);
$avatarSize     = $params->get("avatar_size", 50);
$nameLinkable   = $params->get("name_linkable", 1);
$integrateType  = $params->get("integrate", "none");

$socialProfiles = null;
$numberItems    = count($leaderboard);

if( (strcmp("none", $integrateType) != 0) AND !empty($numberItems)) {
    
    foreach($leaderboard as $item) {
        $usersIds[] = $item->user_id; 
    }
    
    jimport("itprism.integrate.social");
    $socialProfiles = ITPrismIntegrateSocial::factory($integrateType, $usersIds);
}

require JModuleHelper::getLayoutPath('mod_gamificationleaderboard', $params->get('layout', 'default'));