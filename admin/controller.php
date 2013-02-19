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

jimport( 'joomla.application.component.controller' );

/**
 * Default Controller
 *
 * @package		ITPrism Components
 * @subpackage	Gamification
  */
class GamificationController extends JController {
    
	public function display( ) {

        $app = JFactory::getApplication();
        /** @var $app JAdministrator **/
        
        $option   = $app->input->getCmd("option");
        
        $document = JFactory::getDocument();
		/** @var $document JDocumentHtml **/
        
        // Add component style
        $document->addStyleSheet('../media/'.$option.'/css/admin/style.css');
        
        $viewName      = $app->input->getCmd('view', 'dashboard');
        $app->input->set("view", $viewName);

        parent::display();
        return $this;
	}

}