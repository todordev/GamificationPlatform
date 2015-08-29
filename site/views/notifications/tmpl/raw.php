<?php
/**
 * @package      Gamification
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;?>
<?php foreach ($this->items as $item) {
    $notReadClass = "";
    if (!$item->status) {
        $notReadClass = "gfy-note-notread";
    }
    ?>
    <div class="row gfy-note-tiny <?php echo $notReadClass; ?>">
        <div class="col-xs-10">
            <a href="<?php echo JRoute::_(GamificationHelperRoute::getNotificationRoute($item->id)); ?>">
                <?php echo $this->escape($item->content); ?>
            </a>
        </div>
        <div class="col-xs-2">
            <img src="<?php echo (!$item->status) ? "media/com_gamification/images/status_active.png" : "media/com_gamification/images/status_inactive.png"; ?>"/>
        </div>
    </div>
<?php } ?>