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
defined('_JEXEC') or die;
?>
<div class="gfy-modlb<?php echo $moduleclass_sfx;?>">

<?php for($i = 0; $i < $numberItems; $i++) {

    // Social Profile
    if(!empty($socialProfiles)) {
    
        // Get avatar
        $avatar = $socialProfiles->getAvatar($activities[$i]->user_id, $avatarSize);
        if(!$avatar) {
            $avatar = '<img class="media-object" src="media/com_gamification/images/no_picture.png">';
        } else {
            $avatar = '<img class="media-object" src="'.$avatar.'" width="'.$avatarSize.'" height="'.$avatarSize.'">';
        }
    
        $link   =  $socialProfiles->getLink($activities[$i]->user_id);
    
    } else {
        $avatar = '<img class="media-object" src="media/com_gamification/images/no_picture.png" width="'.$avatarSize.'" height="'.$avatarSize.'">';
        $link   = 'javascript: vodi(0);';
    }
    
    if(!$nameLinkable) {
        $name = htmlspecialchars($activities[$i]->name, ENT_QUOTES, "UTF-8");
    } else {
        $name = '<a href="'.$link.'">'.htmlspecialchars($activities[$i]->name, ENT_QUOTES, "UTF-8").'</a>';
    }
    
    ?>
    <div class="media">
    
        <a class="pull-left" href="<?php echo $link;?>">
        <?php echo $avatar; ?>
        </a>
            
        <div class="media-body">
            <h5 class="media-heading"><?php echo $name;?></h5>
            <p class="gfy-media-activity"><?php echo htmlspecialchars($activities[$i]->info, ENT_QUOTES, "UTF-8");?></p>
        </div>
        
    </div>
        
<?php }?>
</div>
