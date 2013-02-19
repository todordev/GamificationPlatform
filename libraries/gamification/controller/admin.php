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

jimport('joomla.application.component.controlleradmin');

/**
 * 
 * This class contains common methods and properties 
 * used in work with forms.
 */
class GamificationControllerAdmin extends JControllerAdmin {
    
    /**
     * 
     * A default link to the extension
     * @var string
     */
     protected $defaultLink = 'index.php?option=com_gamification';
     
    /**
     * @var     string  The prefix to use with controller messages.
     * @since   1.6
     */
    protected $text_prefix = 'COM_GAMIFICATION';
    
    public function backToDashboard() {
        $this->setRedirect( JRoute::_($this->defaultLink, false) );
    }
    
}

