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
defined('_JEXEC') or die; ?>
<div class="row-fluid">
    <?php if($params->get("display_points", 0)) {?>
    <div class="span4">
        <h4><?php echo JText::_("MOD_GAMIFICATIONPROFILE_POINTS");?></h4>
        <p class="gfy-modprofile-points">
        <?php 
        if($params->get("display_points_abbr", 0)) {
            echo $points->getPointsString(); 
        } else {
            echo $points->getPoints();
        }
        ?>
        <p> 
    </div>
    <?php }?>
    <?php if($params->get("display_level", 0)) {?>
    <div class="span4">
        <h4><?php echo JText::_("MOD_GAMIFICATIONPROFILE_LEVEL");?></h4>
        <p class="gfy-modprofile-level">
        <?php echo $level->getLevel();?>
        <p> 
        
        <?php if($params->get("display_level_title", 0)) {?>
        <p class="gfy-modprofile-level-title"><?php echo $level->gettitle(); ?></p>
        <?php }?>
        
    </div>
    <?php } ?>
</div>
