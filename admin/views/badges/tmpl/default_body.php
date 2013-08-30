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
<?php foreach ($this->items as $i => $item) {?>
	<tr class="row<?php echo $i % 2; ?>">
        <td class="center hidden-phone">
            <?php echo JHtml::_('grid.id', $i, $item->id); ?>
        </td>
        <td class="center">
            <?php echo JHtml::_('jgrid.published', $item->published, $i, "badges."); ?>
        </td>
		<td>
		    <a href="<?php echo JRoute::_("index.php?option=com_gamification&view=badge&layout=edit&id=".$item->id);?>" ><?php echo $item->title; ?></a>
	    </td>
		<td class="center hidden-phone">
		    <?php echo JHtml::_("gamification.points", $item->points, $item->points_name, $item->points_type);?>
	    </td>
		<td class="center hidden-phone">
		    <a href="<?php echo JRoute::_("index.php?option=com_gamification&view=group&layout=edit&id=".$item->group_id);?>" ><?php echo $item->group_name;?></a>
	    </td>
        <td class="center hidden-phone">
            <?php echo $item->id;?>
        </td>
	</tr>
<?php }?>
	  