<?php
/**
 * @package      Gamification
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;?>
<?php foreach ($this->items as $item) {
    $notReadClass = "";
    if (!$item->status) {
        $notReadClass = "gfy-note-notread";
    }
    ?>
    <div class="row-fluid gfy-note-tiny <?php echo $notReadClass; ?>">
        <div class="span11">
            <a href="<?php echo JRoute::_(GamificationHelperRoute::getNotificationRoute($item->id)); ?>">
                <?php echo $this->escape($item->note); ?>
            </a>
        </div>
        <div class="span1">
            <img
                src="<?php echo (!$item->status) ? "media/com_gamification/images/status_active.png" : "media/com_gamification/images/status_inactive.png"; ?>"/>
        </div>
    </div>
<?php } ?>