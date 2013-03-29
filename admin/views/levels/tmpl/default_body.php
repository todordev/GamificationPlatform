<?php
/**
 * @package      ITPrism Components
 * @subpackage   Gamification
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
<?php foreach ($this->items as $i => $item) {
	    $ordering  = ($this->listOrder == 'a.ordering');
	?>
	<tr class="row<?php echo $i % 2; ?>">
        <td><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
		<td><a href="<?php echo JRoute::_("index.php?option=com_gamification&view=level&layout=edit&id=".$item->id);?>" ><?php echo $item->title; ?></a></td>
		<td class="center"><strong><?php echo $item->value; ?></strong></td>
		<td class="center hasTip" title="<?php echo $item->points_name;?>"><?php echo $item->points; ?> [ <span><?php echo $item->points_type;?></span> ]</td>
		<td class="center">
		<?php if(!empty($item->rank_id)) {?>
    		<a href="<?php echo JRoute::_("index.php?option=com_gamification&view=rank&layout=edit&id=".$item->rank_id);?>" >
    		<?php echo $item->rank_title;?>
    		</a>
		<?php }else{?>
			<?php echo JText::_("COM_GAMIFICATION__DASHES");?>
		<?php }?>
		</td>
		<td class="center"><a href="<?php echo JRoute::_("index.php?option=com_gamification&view=group&layout=edit&id=".$item->group_id);?>" ><?php echo $item->group_name;?></a></td>
        <td class="center"><?php echo $item->id;?></td>
	</tr>
<?php }?>
	  