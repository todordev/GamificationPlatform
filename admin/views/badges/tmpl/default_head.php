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
    <th width="1%" style="min-width: 55px" class="nowrap center">
        <?php echo JHtml::_('grid.sort', 'JSTATUS', 'a.published', $this->listDirn, $this->listOrder); ?>
    </th>
    <th class="title">
        <?php echo JHtml::_('grid.sort', 'COM_GAMIFICATION_TITLE', 'a.title', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="20%" class="nowrap center hidden-phone">
        <?php echo JHtml::_('grid.sort', 'COM_GAMIFICATION_POINTS', 'a.points', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="20%" class="nowrap hidden-phone">
        <?php echo JHtml::_('grid.sort', 'COM_GAMIFICATION_GROUP', 'b.name', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="5%" class="nowrap center hidden-phone">
        <?php echo JText::_('COM_GAMIFICATION_NOTE'); ?>
    </th>
    <th width="3%" class="nowrap center hidden-phone">
        <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $this->listDirn, $this->listOrder); ?>
    </th>
</tr>
	  