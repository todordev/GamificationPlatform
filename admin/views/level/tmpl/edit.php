<?php
/**
 * @package      Gamification Platform
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
?>
<div class="row-fluid">
    <div class="span6">
        <form action="<?php echo JRoute::_('index.php?option=com_gamification'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate form-horizontal">

            <fieldset class="adminform">
                <?php echo $this->form->renderField('title'); ?>
                <?php echo $this->form->renderField('group_id'); ?>
                <?php echo $this->form->renderField('value'); ?>
                <?php echo $this->form->renderField('rank_id'); ?>
                <?php echo $this->form->renderField('published'); ?>
                
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('points_number'); ?></div>
                    <div class="controls">
                        <?php echo $this->form->getInput('points_number'); ?>
                        <?php echo $this->form->getInput('points_id'); ?>
                    </div>
                </div>
                <?php echo $this->form->renderField('id'); ?>
            </fieldset>

            <input type="hidden" name="task" value=""/>
            <?php echo JHtml::_('form.token'); ?>
        </form>
    </div>
</div>