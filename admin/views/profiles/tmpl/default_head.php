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
        <?php echo JHtml::_('grid.sort', 'COM_GAMIFICATION_STATE', 'a.block', $this->listDirn, $this->listOrder); ?>
    </th>
    <th class="title">
        <?php echo JHtml::_('grid.sort', 'COM_GAMIFICATION_NAME', 'a.name', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="10%" class="nowrap center hidden-phone">
        <?php echo JHtml::_('grid.sort', 'COM_GAMIFICATION_REGISTERED_DATE', 'a.registerDate', $this->listDirn, $this->listOrder); ?>
    </th>
    <th width="3%" class="nowrap center hidden-phone">
        <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $this->listDirn, $this->listOrder); ?>
    </th>
</tr>
	  