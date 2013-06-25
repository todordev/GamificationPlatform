<?php
/**
 * @package      Gamification
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Gamification is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;?>
<div class="gfy-notification-view<?php echo $this->pageclass_sfx;?>">
    <?php if ($this->params->get('show_page_heading', 1)) { ?>
    <h1><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
    <?php } ?>
    
    <?php if(!empty($this->item)){ ?>
    <div class="media gfy-notification">
        <?php if(!empty($this->item->image)){ ?>
        <a class="pull-right" href="#">
            <img class="media-object" src="<?php echo $this->item->image; ?>" >
        </a>
        <?php }?>
        <div class="media-body">
            <p><?php echo $this->item->note; ?></p>
        </div>
    </div>
    <?php } ?>
</div>
<div class="clearfix">&nbsp;</div>
<?php echo $this->version->backlink;?>