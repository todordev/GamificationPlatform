<?php
/**
 * @package      Gamification Platform
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
defined('_JEXEC') or die;
?>
<div class="row-fluid">
	<div class="span6">
        <form action="<?php echo JRoute::_('index.php?option=com_gamification'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate" enctype="multipart/form-data">
            <div class="width-100 fltlft">
                <fieldset class="adminform">
                    <legend><?php echo JText::_("COM_GAMIFICATION_BADGE_DATA_LEGEND"); ?></legend>
                    
                    <ul class="adminformlist">
                        <li><?php echo $this->form->getLabel('title'); ?>
                        <?php echo $this->form->getInput('title'); ?></li>
                        
                        <li><?php echo $this->form->getLabel('group_id'); ?>
                        <?php echo $this->form->getInput('group_id'); ?></li>
                        
                        <li><?php echo $this->form->getLabel('points'); ?>
                        <?php echo $this->form->getInput('points'); ?></li>
                        
                        <li><?php echo $this->form->getLabel('points_id'); ?>
                        <?php echo $this->form->getInput('points_id'); ?></li>
                        
                        <li><?php echo $this->form->getLabel('image'); ?>
                        <?php echo $this->form->getInput('image'); ?></li>
                        
                        <li><?php echo $this->form->getLabel('published'); ?>
                        <?php echo $this->form->getInput('published'); ?></li>
                           
                        <li><?php echo $this->form->getLabel('id'); ?>
                        <?php echo $this->form->getInput('id'); ?></li>
                    </ul>
                </fieldset>
            </div>
        
            <input type="hidden" name="task" value="" />
            <?php echo JHtml::_('form.token'); ?>
        </form>
    </div>
    
    <?php if(!empty($this->item->image)){?>
    <div class="span6">
    	<img src="<?php echo "../". $this->imagesFolder ."/". $this->item->image; ?>" class="img-polaroid" />
    	<div>
    	<img src="<?php echo "../media/com_gamification/images/remove.png"; ?>" />
    	<a href="<?php echo JRoute::_("index.php?option=com_gamification&task=badge.removeimage&id=".(int)$this->item->id."&".JSession::getFormToken()."=1");?>"><?php echo JText::_("COM_GAMIFICATION_REMOVE_IMAGE");?></a>
    	</div> 
    </div>
    <?php }?>
</div>