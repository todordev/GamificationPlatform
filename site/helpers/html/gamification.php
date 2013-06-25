<?php
/**
 * @package      Gamification
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

/**
 * Gamification Html Helper
 *
 * @package		Gamification
 * @subpackage	Components
 * @since		1.6
 */
abstract class JHtmlGamification {
    
    /**
     * @var   array   array containing information for loaded files
     */
    protected static $loaded = array();
    
    public static function helper() {
    
        // Only load once
        if (!empty(self::$loaded[__METHOD__])) {
            return;
        }
    
        JHtml::_('script', 'media/com_gamification/js/helper.js', false, false, false, false, false);
        self::$loaded[__METHOD__] = true;
    
        return;
    
    }
    
}
