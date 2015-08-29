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
<div class="gfy-notifications<?php echo $this->pageclass_sfx; ?>" id="js-gfy-notifications">
    <?php if ($this->params->get('show_page_heading', 1)) { ?>
        <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
    <?php } ?>

    <?php foreach ($this->items as $item) {
        $notReadClass = "";
        if (!$item->status) {
            $notReadClass = "gfy-note-notread";
        }
        ?>
        <div class="gfy-notification <?php echo $notReadClass; ?> row" id="js-gfy-note-element<?php echo $item->id; ?>">
            <div class="col-xs-10">
                <div class="media">
                    <?php if (!empty($item->image)) { ?>
                        <div class="media-left">
                            <img class="media-object" src="<?php echo $item->image; ?>">
                        </div>
                    <?php } ?>
                    <div class="media-body">
                        <a href="<?php echo JRoute::_(GamificationHelperRoute::getNotificationRoute($item->id)); ?>"><?php echo $this->escape($item->content); ?></a>
                    </div>
                </div>
            </div>
            <div class="col-xs-1">
                <img src="<?php echo (!$item->status) ? "media/com_gamification/images/status_active.png" : "media/com_gamification/images/status_inactive.png"; ?>"/>
            </div>
            <div class="col-xs-1">
                <button data-element-id="<?php echo (int)$item->id; ?>" class="btn btn-danger js-gfy-btn-remove-notification">
                    <i class="glyphicon glyphicon-trash"></i>
                </button>
            </div>
        </div>
    <?php } ?>

</div>