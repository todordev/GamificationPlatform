<?php
/**
 * @package      Gamification Platform
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Gamification notification controller.
 *
 * @package     Gamification Platform
 * @subpackage  Components
 */
class GamificationControllerNotification extends JControllerLegacy
{
    /**
     * Method to get a model object, loading it if required.
     *
     * @param    string $name   The model name. Optional.
     * @param    string $prefix The class prefix. Optional.
     * @param    array  $config Configuration array for model. Optional.
     *
     * @return    object    The model.
     * @since    1.5
     */
    public function getModel($name = 'Notification', $prefix = 'GamificationModel', $config = array('ignore_request' => false))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    /**
     * This method remove a notification.
     */
    public function remove()
    {
        $itemId = $this->input->getUint("id");
        $userId = JFactory::getUser()->get("id");

        $response = new Prism\Response\Json();

        $validatorOwner = new Gamification\Validator\Notification\Owner(JFactory::getDbo(), $itemId, $userId);
        if (!$validatorOwner->isValid()) {
            $response
                ->setTitle(JText::_('COM_GAMIFICATION_FAIL'))
                ->setText(JText::_('COM_GAMIFICATION_INVALID_NOTIFICATION'))
                ->failure();

            echo $response;
            JFactory::getApplication()->close();
        }

        try {

            $notification = new Gamification\Notification\Notification(JFactory::getDbo());
            $notification->load($itemId);
            $notification->remove();

        } catch (Exception $e) {
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_GAMIFICATION_ERROR_SYSTEM'));
        }

        $response
            ->setTitle(JText::_('COM_GAMIFICATION_SUCCESS'))
            ->setText(JText::_('COM_GAMIFICATION_NOTIFICATION_REMOVED_SUCCESSFULLY'))
            ->success();

        echo $response;
        JFactory::getApplication()->close();
    }
}
