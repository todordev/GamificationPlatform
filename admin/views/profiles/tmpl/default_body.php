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
	    $ordering  = ($this->listOrder == 'a.registerDate');
	?>
	<tr class="row<?php echo $i % 2; ?>">
        <td><?php echo JHtml::_('grid.id', $i, $item->id); ?></td>
		<td><?php echo $item->name; ?></td>
		<td class="center"><?php echo $item->registerDate; ?></td>
        <td class="center"><?php echo JHtml::_('grid.boolean', $i, !$item->block); ?></td>
        <td class="center"><?php echo $item->id;?></td>
	</tr>
<?php }?>
	  