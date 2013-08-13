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
    <div class="span4 gfy-modprofile-points">
        <h4><?php echo JText::_("MOD_GAMIFICATIONPROFILE_POINTS");?></h4>
        <p class="gfy-modprofile-points-value">
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
    <div class="span4 gfy-modprofile-level">
        <h4><?php echo JText::_("MOD_GAMIFICATIONPROFILE_LEVEL");?></h4>
        <p class="gfy-modprofile-level-value"><?php echo $level->getLevel();?><p> 
        
        <?php if($params->get("display_level_title", 0)) {?>
        <p><?php echo $level->getTitle(); ?></p>
        <?php }?>
        
        <?php if($params->get("display_level_rank", 0) AND !empty($level->rank_id)) {?>
        <p><?php echo $level->getRank()->getTitle(); ?></p>
        <?php }?>
        
    </div>
    <?php } ?>
    
    <?php if($params->get("display_rank", 0)) {?>
    <div class="span4 gfy-modprofile-rank">
        <h4><?php echo JText::_("MOD_GAMIFICATIONPROFILE_RANK");?></h4>
        
        <?php if($params->get("display_rank_picture", 0)) {?>
            <img src="<?php echo $imagePath."/".$rank->getImage();?>" alt="<?php echo stripslashes(htmlentities($rank->getTitle(), ENT_QUOTES, "UTF-8"));?>" />
        <?php }?>
        
        <p><?php echo $rank->getTitle();?><p> 
        
    </div>
    <?php } ?>
    
</div>

<?php if($params->get("display_badges", 0)) { ?>
<div class="row-fluid">
    <div class="span12 gfy-modprofile-badges">
    <h4><?php echo JText::_("MOD_GAMIFICATIONPROFILE_BADGES");?></h4>
    <?php
        $badges_ = $badges->getBadges($groupId);
        foreach($badges_ as $badge) {
            echo JHtml::_("gamification.badge", $imagePath."/".$badge->getImage(), $badge->getTitle(), $badgeTooltip, $badge->getNote());
        }
    ?>
    </div>
</div>
<?php }?>

<?php if($params->get("display_progress_bar", 0)) { ?>
<div class="row-fluid">
    <div class="span12 gfy-modprofile-progress">
        <h4><?php echo JText::_("MOD_GAMIFICATIONPROFILE_PROGRESS");?></h4>
        <?php echo JHtml::_("gamification.progress", $progress, $params->get("display_badges_information", 0));?>
    </div>
    <div id="gfy-tooltip-container">
    </div>
</div>
<?php }?>