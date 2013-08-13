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
<form action="<?php echo JRoute::_('index.php?option=com_gamification'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate" >
    <div class="width-40 fltlft">
        <fieldset class="adminform">
        
            <ul class="adminformlist">
                <li><?php echo $this->form->getLabel('note'); ?>
                <?php echo $this->form->getInput('note'); ?></li>
                
    			<li><?php echo $this->form->getLabel('url'); ?>
                <?php echo $this->form->getInput('url'); ?></li>   
                
                <li><?php echo $this->form->getLabel('image'); ?>
                <?php echo $this->form->getInput('image'); ?></li>
                
                <li><?php echo $this->form->getLabel('read'); ?>
                <?php echo $this->form->getInput('read'); ?></li>
                
                <li><?php echo $this->form->getLabel('id'); ?>
                <?php echo $this->form->getInput('id'); ?></li>
            </ul>
            
        </fieldset>
    </div>

    <input type="hidden" name="task" value="" />
    <?php echo JHtml::_('form.token'); ?>
</form>
