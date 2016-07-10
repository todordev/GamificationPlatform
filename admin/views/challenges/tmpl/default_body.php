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
<?php foreach ($this->items as $i => $item) { ?>
    <tr class="row<?php echo $i % 2; ?>">
        <td class="center hidden-phone">
            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
        </td>
        <td class="center">
            <?php echo JHtml::_('jgrid.published', $item->published, $i, 'achievements.'); ?>
        </td>
        <td class="has-context">
            <a href="<?php echo JRoute::_('index.php?option=com_gamification&view=achievement&layout=edit&id=' . $item->id); ?>"><?php echo $this->escape($item->title); ?></a>
            <?php echo JHtml::_('gamificationbackend.helptip', $item->note); ?>
            <div class="small">
                <?php echo JText::sprintf('COM_GAMIFICATION_GROUP_S', $this->escape($item->group_name)); ?>
            </div>
        </td>
        <td class="center hidden-phone">
            <?php echo JHtml::_('gamificationbackend.goals', 0); ?>
        </td>
        <td class="center hidden-phone">
            <?php echo $item->id; ?>
        </td>
    </tr>
<?php } ?>
	  