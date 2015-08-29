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
<div class="gfy-notification-view<?php echo $this->pageclass_sfx; ?>">
    <?php if ($this->params->get('show_page_heading', 1)) { ?>
        <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
    <?php } ?>

    <?php if (!empty($this->item)) { ?>
        <div class="media gfy-notification">
            <?php if (!empty($this->item->image)) { ?>
            <div class="media-left">
                <img class="media-object" src="<?php echo $this->item->image; ?>">
            </div>
            <?php } ?>
            <div class="media-body">
                <p><?php echo $this->escape($this->item->content); ?></p>
                <?php if (!empty($this->item->url)) {
                    $title = (!empty($this->item->title)) ? JText::sprintf("COM_GAMIFICATION_LINK_TO_S", $this->item->title) : JText::_("COM_GAMIFICATION_LINK_TO_ITEM");
                    echo JHtml::_("gamification.link", $this->item->url, $title, array("class" => "small"));
                }?>
            </div>
        </div>
    <?php } ?>
</div>