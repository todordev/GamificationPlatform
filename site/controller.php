<?php
/**
 * @package      Gamification Platform
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

class GamificationController extends JControllerLegacy
{
    protected $cacheableViews = array();

    /**
     * Method to display a view.
     *
     * @param   boolean       $cachable  If true, the view output will be cached
     * @param   array         $urlparams  An array of safe url parameters and their variable types, for valid values see {@link JFilterInput::clean()}.
     *
     * @return  JController     This object to support chaining.
     * @since   1.5
     */
    public function display($cachable = false, $urlparams = array())
    {
        // Set the default view name and format from the Request.
        // Note we are using catid to avoid collisions with the router and the return page.
        // Frontend is a bit messier than the backend.
        $viewName = $this->input->getCmd('view', 'notifications');
        $this->input->set('view', $viewName);

        // Cache some views.
        if (in_array($viewName, $this->cacheableViews)) {
            $cachable   = true;
        }

        $safeurlparams = array(
            'id'               => 'INT',
            'limit'            => 'INT',
            'limitstart'       => 'INT',
            'filter_order'     => 'CMD',
            'filter_order_dir' => 'CMD',
            'catid'            => 'INT',
        );

        return parent::display($cachable, $safeurlparams);
    }
}
