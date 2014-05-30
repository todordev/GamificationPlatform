<?php
/**
 * @package      Gamification Platform
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;
?>
<div class="row-fluid">
    <div class="span6 form-horizontal">
        <form action="<?php echo JRoute::_('index.php?option=com_gamification'); ?>" method="post" name="adminForm"
              id="adminForm" class="form-validate" enctype="multipart/form-data">

            <fieldset class="adminform">
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('title'); ?></div>
                    <div class="controls"><?php echo $this->form->getInput('title'); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('group_id'); ?></div>
                    <div class="controls"><?php echo $this->form->getInput('group_id'); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('points'); ?></div>
                    <div class="controls"><?php echo $this->form->getInput('points'); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('points_id'); ?></div>
                    <div class="controls"><?php echo $this->form->getInput('points_id'); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('image'); ?></div>
                    <div class="controls"><?php echo $this->form->getInput('image'); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('published'); ?></div>
                    <div class="controls"><?php echo $this->form->getInput('published'); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('note'); ?></div>
                    <div class="controls"><?php echo $this->form->getInput('note'); ?></div>
                </div>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('id'); ?></div>
                    <div class="controls"><?php echo $this->form->getInput('id'); ?></div>
                </div>
            </fieldset>

            <input type="hidden" name="task" value=""/>
            <?php echo JHtml::_('form.token'); ?>
        </form>
    </div>

    <?php if (!empty($this->item->image)) { ?>
        <div class="span6">
            <img src="<?php echo "../" . $this->imagesFolder . "/" . $this->item->image; ?>" class="img-polaroid"/>
            <br/><br/>
            <a class="btn btn-mini btn-danger"
               href="<?php echo JRoute::_("index.php?option=com_gamification&task=rank.removeimage&id=" . (int)$this->item->id . "&" . JSession::getFormToken() . "=1"); ?>">
                <i class="icon-trash"></i>
                <?php echo JText::_("COM_GAMIFICATION_REMOVE_IMAGE"); ?>
            </a>
        </div>
    <?php } ?>
</div>