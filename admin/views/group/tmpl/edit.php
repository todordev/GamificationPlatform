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
	<div class="span6 form-horizontal">
        <form action="<?php echo JRoute::_('index.php?option=com_gamification'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate" >
            
            <fieldset>
                <div class="control-group">
                    <div class="control-label"><?php echo $this->form->getLabel('name'); ?></div>
    				<div class="controls"><?php echo $this->form->getInput('name'); ?></div>
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
        
            <input type="hidden" name="task" value="" />
            <?php echo JHtml::_('form.token'); ?>
        </form>
    </div>
</div>
