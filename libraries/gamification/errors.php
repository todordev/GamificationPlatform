<?php
/**
* @package      ITPrism Components
* @subpackage   Gamification
* @author       Todor Iliev
* @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
* @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Gamification is free software. This vpversion may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality  
 * to manage errors of the extension
 */
abstract class GamificationErrors {

    const CODE_WARNING         = 1001;
    
    /**
     * This constant will be used when 
     * developer does not want to display error message
     * but he will display a default system error message.
     * @var integer
     */
    const CODE_HIDDEN_WARNING  = 1002;
    
    const CODE_ERROR           = 500;
    
}
