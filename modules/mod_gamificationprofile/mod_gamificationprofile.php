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

// Load Twitter Bootstrap
JHtml::addIncludePath(ITPRISM_PATH_LIBRARY.'/ui/helpers');
JHtml::_("itprism.ui.bootstrap");

$doc = JFactory::getDocument();

$doc->addStyleSheet('modules/mod_gamificationprofile/style.css');

$userId  = JFactory::getUser()->id;
$groupId = $params->get("group_id");

if($params->get("display_points", 0)) {
    jimport('gamification.userpoints');
    
    $pointsId = $params->get("points_id");
    $keys     = array("user_id"=>$userId, "points_id" => $pointsId);
    $points   = GamificationUserPoints::getInstance($keys);
    
}

if($params->get("display_level", 0)) { 
    jimport('gamification.level');
    
    $keys     = array(
        "user_id"   => $userId, 
        "group_id"  => $groupId
    );
    
    $level = GamificationUserLevel::getInstance($keys);
}

require JModuleHelper::getLayoutPath('mod_gamificationprofile', $params->get('layout', 'default'));