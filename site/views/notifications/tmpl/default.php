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
            <div class="row-fluid gfy-notification <?php echo $notReadClass; ?>"
                 id="js-gfy-note-element<?php echo $item->id; ?>">
                <div class="span10">
                    <div class="media">
                        <?php if (!empty($item->image)) { ?>
                            <a class="pull-left" href="#">
                                <img class="media-object" src="<?php echo $item->image; ?>">
                            </a>
                        <?php } ?>
                        <div class="media-body">
                            <p>
                                <a href="<?php echo JRoute::_(GamificationHelperRoute::getNotificationRoute($item->id)); ?>"><?php echo $item->note; ?></a>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="span1">
                    <img
                        src="<?php echo (!$item->status) ? "media/com_gamification/images/status_active.png" : "media/com_gamification/images/status_inactive.png"; ?>"/>
                </div>
                <div class="span1">
                    <a href="<?php echo JRoute::_("index.php?option=com_gamification&task=notification.remove"); ?>"
                       data-element-id="<?php echo (int)$item->id; ?>" class="gfy-btn-remove-notification">
                        <i class="icon-trash">&nbsp;</i>
                    </a>
                </div>

            </div>
        <?php } ?>

    </div>
    <div class="clearfix">&nbsp;</div>
<?php echo $this->version->backlink; ?>