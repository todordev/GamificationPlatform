<?php
/**
 * @package      Gamification Platform
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

$statuses = array(
    0 => array(
        "task" => "notifications.read",
        "text" => JText::_("COM_GAMIFICATION_NOT_READ"),
        "active_title" => JText::_("COM_GAMIFICATION_MARK_AS_READ"),
        "inactive_title" => JText::_("COM_GAMIFICATION_MARK_AS_NOT_READ"),
        "tip" => true,
        "active_class" => "unpublish",
        "inactive_class" => "publish",
    ),

    1 => array(
        "task" => "notifications.notread",
        "text" => JText::_("COM_GAMIFICATION_READ"),
        "active_title" => JText::_("COM_GAMIFICATION_MARK_AS_NOT_READ"),
        "inactive_title" => JText::_("COM_GAMIFICATION_MARK_AS_READ"),
        "tip" => true,
        "active_class" => "publish",
        "inactive_class" => "unpublish",
    )
);

// no direct access
defined('_JEXEC') or die;
?>
<?php foreach ($this->items as $i => $item) { ?>
    <tr class="row<?php echo $i % 2; ?>">
        <td class="center hidden-phone">
            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
        </td>
        <td class="center hidden-phone">
            <?php echo JHtml::_('jgrid.state', $statuses, $item->status, $i);?>
        </td>
        <td class="title">
            <a href="<?php echo JRoute::_("index.php?option=com_gamification&view=notification&layout=edit&id=" . (int)$item->id); ?>">
                <?php echo JHtmlString::truncate(strip_tags($item->content), 64); ?>
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
	  