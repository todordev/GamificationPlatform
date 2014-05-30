<?php
/**
 * @package      Gamification Platform
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;?>
<?php foreach ($this->items as $i => $item) { ?>
    <tr class="row<?php echo $i % 2; ?>">
        <td class="center hidden-phone">
            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
        </td>
        <td class="center">
            <?php
            if (!$item->block) {
                $title = JText::_("COM_GAMIFICATION_ENABLED");
            } else {
                $title = JText::_("COM_GAMIFICATION_BLOCKED");
            }

            echo JHtml::_('gamification.boolean', !$item->block, $title);
            ?>
        </td>
        <td>
            <?php echo $this->escape($item->name); ?>
        </td>
        <td class="center hidden-phone">
            <?php echo JHtml::_('date', $item->registerDate, JText::_('DATE_FORMAT_LC3')); ?>
        </td>
        <td class="center hidden-phone">
            <?php echo $item->id; ?>
        </td>
    </tr>
<?php } ?>
	  