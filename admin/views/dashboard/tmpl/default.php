<?php
/**
 * @package      Gamification Platform
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
// no direct access
defined('_JEXEC') or die;
?>
<?php if (!empty($this->sidebar)): ?>
<div id="j-sidebar-container" class="span2">
    <?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
    <?php else : ?>
    <div id="j-main-container">
        <?php endif; ?>
        <div class="span8">

        </div>
        <div class="span4">
            <a href="http://itprism.com/free-joomla-extensions/ecommerce-gamification/game-mechanics-platform"
               target="_blank"><img src="../media/com_gamification/images/logo.png"
                                    alt="<?php echo JText::_("COM_GAMIFICATION"); ?>"/></a>
            <a href="http://itprism.com" target="_blank"
               title="<?php echo JText::_("COM_GAMIFICATION_PRODUCT"); ?>"><img
                    src="../media/com_gamification/images/product_of_itprism.png"
                    alt="<?php echo JText::_("COM_GAMIFICATION_PRODUCT"); ?>"/></a>

            <p><?php echo JText::_("COM_GAMIFICATION_YOUR_VOTE"); ?></p>

            <p><?php echo JText::_("COM_GAMIFICATION_SPONSORSHIP"); ?></p>

            <p><?php echo JText::_("COM_GAMIFICATION_SUBSCRIPTION"); ?></p>
            <table class="table table-striped">
                <tbody>
                <tr>
                    <td><?php echo JText::_("COM_GAMIFICATION_INSTALLED_VERSION"); ?></td>
                    <td><?php echo $this->version->getMediumVersion(); ?></td>
                </tr>
                <tr>
                    <td><?php echo JText::_("COM_GAMIFICATION_RELEASE_DATE"); ?></td>
                    <td><?php echo $this->version->releaseDate ?></td>
                </tr>
                <tr>
                    <td><?php echo JText::_("COM_GAMIFICATION_ITPRISM_LIBRARY_VERSION"); ?></td>
                    <td><?php echo $this->itprismVersion; ?></td>
                </tr>
                <tr>
                    <td><?php echo JText::_("COM_GAMIFICATION_COPYRIGHT"); ?></td>
                    <td><?php echo $this->version->copyright; ?></td>
                </tr>
                <tr>
                    <td><?php echo JText::_("COM_GAMIFICATION_LICENSE"); ?></td>
                    <td><?php echo $this->version->license; ?></td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>