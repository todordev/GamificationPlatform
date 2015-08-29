<?php
/**
 * @package      Gamification Platform
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<div class="row-fluid">
    <div class="span6">
        <form action="<?php echo JRoute::_('index.php?option=com_gamification'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal" enctype="multipart/form-data">

            <fieldset class="adminform">
                <?php echo $this->form->getControlGroup('title'); ?>
                <?php echo $this->form->getControlGroup('group_id'); ?>
                <?php echo $this->form->getControlGroup('points'); ?>
                <?php echo $this->form->getControlGroup('points_id'); ?>
                <?php echo $this->form->getControlGroup('image'); ?>
                <?php echo $this->form->getControlGroup('published'); ?>

                <div class="control-group">
                    <div class="control-label">
                        <?php echo $this->form->getLabel('description'); ?>
                        <a class="btn btn-mini hasTooltip" href="javascript: void(0);" title="<?php echo JHtml::tooltipText('COM_GAMIFICATION_PLACEHOLDERS', 'COM_GAMIFICATION_PLACEHOLDERS_RANK'); ?>">
                            <i class="icon-info"></i>
                        </a>
                    </div>
                    <div class="controls">
                        <?php echo $this->form->getInput('description'); ?>
                    </div>
                </div>

                <?php echo $this->form->getControlGroup('note'); ?>
                <?php echo $this->form->getControlGroup('id'); ?>
            </fieldset>

            <input type="hidden" name="task" value=""/>
            <?php echo JHtml::_('form.token'); ?>
        </form>
    </div>

    <?php if (!empty($this->item->image)) { ?>
        <div class="span6">
            <img src="<?php echo "../" . $this->imagesFolder . "/" . $this->item->image; ?>" class="img-polaroid"/>
            <br/><br/>
            <a class="btn btn-danger" href="<?php echo JRoute::_("index.php?option=com_gamification&task=rank.removeimage&id=" . (int)$this->item->id . "&" . JSession::getFormToken() . "=1"); ?>" id="gfy-remove-image">
                <i class="icon-trash"></i>
                <?php echo JText::_("COM_GAMIFICATION_REMOVE_IMAGE"); ?>
            </a>
        </div>
    <?php } ?>
</div>