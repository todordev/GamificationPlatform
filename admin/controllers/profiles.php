<?php
/**
 * @package      Gamification Platform
 * @subpackage   Components Platform
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

use Prism\Controller\Admin;

// No direct access
defined('_JEXEC') or die;

/**
 * Gamification Platform profile Controller
 *
 * @package     Gamification
 * @package     Components Platform
 */
class GamificationControllerProfiles extends Admin
{
    /**
     * Proxy for getModel.
     *
     * @param string $name
     * @param string $prefix
     * @param array  $config
     *
     * @return GamificationModelProfile
     *
     * @todo fix this controller
     *
     * @since   1.6
     */
    public function getModel($name = 'Profile', $prefix = 'GamificationModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    public function create()
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Get form data
        $pks   = $this->input->post->get('cid', array(), 'array');
        $model = $this->getModel('Profile', 'GamificationModel');
        /** @var $model GamificationModelProfile */

        $pks = Joomla\Utilities\ArrayHelper::toInteger($pks);

        // Check for validation errors.
        if (!$pks) {
            $this->defaultLink .= '&view=' . $this->view_list;

            $this->setMessage(JText::_('COM_GAMIFICATION_INVALID_ITEM'), 'notice');
            $this->setRedirect(JRoute::_($this->defaultLink, false));

            return;
        }

        try {
            $pks = $model->filterProfiles($pks);

            if (!$pks) {
                $this->defaultLink .= '&view=' . $this->view_list;

                $this->setMessage(JText::_('COM_GAMIFICATION_INVALID_ITEM'), 'notice');
                $this->setRedirect(JRoute::_($this->defaultLink, false));

                return;
            }

            $model->create($pks);

        } catch (Exception $e) {
            JLog::add($e->getMessage(), JLog::ERROR, 'com_gamification');
            throw new Exception(JText::_('COM_GAMIFICATION_ERROR_SYSTEM'));
        }

        $msg  = JText::plural('COM_GAMIFICATION_N_PROFILES_CREATED', count(pks));
        $link = $this->defaultLink . '&view=' . $this->view_list;

        $this->setRedirect(JRoute::_($link, false), $msg);

    }
}
