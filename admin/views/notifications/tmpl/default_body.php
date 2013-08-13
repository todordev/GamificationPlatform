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
<?php foreach ($this->items as $i => $item) {
	    $ordering  = ($this->listOrder == 'a.ordering');
	?>
	<tr class="row<?php echo $i % 2; ?>">
        <td><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
		<td>
		    <a href="<?php echo JRoute::_("index.php?option=com_gamification&view=notification&layout=edit&id=".(int)$item->id);?>" >
		        <?php echo JHtmlString::truncate(strip_tags($item->note), 64); ?>
	        </a>
	    </td>
		<td class="center">
		    <a href="<?php echo JRoute::_("index.php?option=com_gamification&view=users&filter_search=id:".(int)$item->id);?>" >
		        <?php echo $item->name; ?>
	        </a>
	    </td>
	    <td class="center">
	        <?php echo JHtml::_('date', $item->created, JText::_('DATE_FORMAT_LC3')); ?>
	    </td>
	    <td class="center">
	        <?php echo (!$item->url) ? "--" : '<a href="'.$item->url.'">'.JText::_("COM_GAMIFICATION_LINK").'</a>'; ?>
	    </td>
	    <td class="center">
	        <?php echo (!$item->image) ? "--" : '<img src="'.$item->image.'" />'; ?>
	    </td>
	    <td class="center">
	        <?php echo JHtml::_('grid.boolean', $i, $item->read); ?>
	    </td>
        <td class="center"><?php echo $item->id;?></td>
	</tr>
<?php }?>
	  