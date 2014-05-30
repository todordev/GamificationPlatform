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
    <div class="gfy-notification-view<?php echo $this->pageclass_sfx; ?>">
        <?php if ($this->params->get('show_page_heading', 1)) { ?>
            <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
        <?php } ?>

        <?php if (!empty($this->item)) { ?>
            <div class="media gfy-notification">
                <?php if (!empty($this->item->image)) { ?>
                    <a class="pull-right" href="#">
                        <img class="media-object" src="<?php echo $this->item->image; ?>">
                    </a>
                <?php } ?>
                <div class="media-body">
                    <p><?php echo $this->item->note; ?></p>
                </div>
            </div>
        <?php } ?>
    </div>
    <div class="clearfix">&nbsp;</div>
<?php echo $this->version->backlink; ?>