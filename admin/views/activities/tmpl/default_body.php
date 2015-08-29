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
<?php foreach ($this->items as $i => $item) { ?>
    <tr class="row<?php echo $i % 2; ?>">
        <td class="center hidden-phone">
            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
        </td>
        <td class="title">
            <a href="<?php echo JRoute::_("index.php?option=com_gamification&view=activity&layout=edit&id=" . (int)$item->id); ?>">
                <?php echo $this->escape($item->content); ?>
            </a>
            <?php echo JHtml::_('gamification.iconLink', $item->url, $item->title);?>
            <?php echo JHtml::_('gamification.iconPicture', $item->image);?>
        </td>
        <td class="hidden-phone">
            <?php echo $this->escape($item->name); ?>
        </td>
        <td class="hidden-phone">
            <?php echo JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC3')); ?>
        </td>
        <td class="center hidden-phone">
            <?php echo $item->id; ?>
        </td>
    </tr>
<?php } ?>
	  