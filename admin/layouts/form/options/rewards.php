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
$form = $displayData;
?>
<div class="control-group">
    <div class="control-label"><?php echo $form->getLabel('points', 'rewards'); ?></div>
    <div class="controls">
        <?php echo $form->getInput('points', 'rewards'); ?>
        <?php echo $form->getInput('points_id', 'rewards'); ?>
    </div>
</div>

<?php
echo $form->renderField('badge_id', 'rewards');
echo $form->renderField('rank_id', 'rewards');
echo $form->renderField('reward_id', 'rewards');
