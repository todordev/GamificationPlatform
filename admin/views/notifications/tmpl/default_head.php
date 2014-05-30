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
<tr>
    <th width="1%" class="nowrap center hidden-phone">
        <?php echo JHtml::_('grid.checkall'); ?>
    </th>
    <th class="title">
        <?php echo JText::_("COM_GAMIFICATION_NOTIFICATION"); ?>
    </th>
    <th width="10%" class="nowrap center hidden-phone">
        <?php echo JHtml::_('grid.sort', 'COM_GAMIFICATION_USER', 'b.name', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="10%" class="nowrap center hidden-phone">
        <?php echo JHtml::_('grid.sort', 'COM_GAMIFICATION_CREATED', 'a.created', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="5%" class="nowrap center hidden-phone">
        <?php echo JText::_("COM_GAMIFICATION_URL"); ?>
    </th>
    <th width="5%" class="nowrap center hidden-phone">
        <?php echo JText::_("COM_GAMIFICATION_IMAGE"); ?>
    </th>
    <th width="5%" class="nowrap center hidden-phone">
        <?php echo JHtml::_('grid.sort', 'COM_GAMIFICATION_STATUS', 'a.status', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="1%" class="nowrap center hidden-phone">
        <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $this->listDirn, $this->listOrder); ?>
    </th>
</tr>
	  