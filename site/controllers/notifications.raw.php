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
 * Gamification notifications controller.
 *
 * @package     Gamification Platform
 * @subpackage  Components
 */
class GamificationControllerNotifications extends JControllerLegacy
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
    public function getModel($name = 'Notifications', $prefix = 'GamificationModel', $config = array('ignore_request' => false))
    {
        $model = parent::getModel($name, $prefix, $config);

        return $model;
    }

    /**
     * Method to load data via AJAX
     */
    public function getNumber()
    {
        $response = new Prism\Response\Json();

        try {

            $notifications = new Gamification\Notification\Notifications(JFactory::getDbo());

            $options = array(
                "user_id" => JFactory::getUser()->get("id"),
                "status"  => Prism\Constants::NOT_READ
            );

            $notRead = $notifications->getNumber($options);

        } catch (Exception $e) {
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_CROWDFUNDING_ERROR_SYSTEM'));
        }

        $data = array("results" => $notRead);

        $response
            ->setData($data)
            ->success();

        echo $response;
        JFactory::getApplication()->close();
    }
}
