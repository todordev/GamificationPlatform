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
<tr>
    <th width="1%" class="nowrap center hidden-phone">
        <?php echo JHtml::_('grid.checkall'); ?>
    </th>
	<th class="title">
	     <?php echo JText::_("COM_GAMIFICATION_INFORMATION");?>
	</th>
	<th width="10%" class="nowrap center hidden-phone">
	     <?php echo JHtml::_('grid.sort',  'COM_GAMIFICATION_USER', 'b.name', $this->listDirn, $this->listOrder); ?>
	</th>
	<th width="10%" class="nowrap center hidden-phone">
	     <?php echo JHtml::_('grid.sort',  'COM_GAMIFICATION_CREATED', 'a.created', $this->listDirn, $this->listOrder); ?>
	</th>
	<th width="5%" class="nowrap center hidden-phone">
	     <?php echo JText::_("COM_GAMIFICATION_URL"); ?>
	</th>
	<th width="5%" class="nowrap center hidden-phone">
	     <?php echo JText::_("COM_GAMIFICATION_IMAGE"); ?>
	</th>
    <th width="1%" class="nowrap center hidden-phone">
        <?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'a.id', $this->listDirn, $this->listOrder); ?>
    </th>
</tr>
	  