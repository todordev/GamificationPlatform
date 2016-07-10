<?php
/**
 * @package      Gamification Platform
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

use Prism\Controller\Admin;
use Joomla\Utilities\ArrayHelper;

// no direct access
defined('_JEXEC') or die;

/**
 * Gamification notifications controller
 *
 * @package     Gamification
 * @package     Components
 */
class GamificationControllerNotifications extends Admin
{
    public function __construct($config = array())
    {
        parent::__construct($config);

        // Define task mappings.

        // Value = 0
        $this->registerTask('notread', 'read');
    }

    public function getModel($name = 'Notification', $prefix = 'GamificationModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    public function read()
    {
        // Check for request forgeries
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Get items to publish from the request.
        $cid  = $this->input->get('cid', array(), 'array');
        $data = array(
            'read'    => 1,
            'notread' => 0
        );

        $task  = $this->getTask();
        $value = ArrayHelper::getValue($data, $task, 0, 'int');

        $redirectOptions = array(
            'view' => 'notifications'
        );

        // Make sure the item ids are integers
        $cid = ArrayHelper::toInteger($cid);
        if (!$cid) {
            $this->displayNotice(JText::_($this->text_prefix . '_NO_ITEM_SELECTED'), $redirectOptions);
            return;
        }

        try {
            $model = $this->getModel();
            $model->read($cid, $value);

        } catch (RuntimeException $e) {
            $this->displayWarning($e->getMessage(), $redirectOptions);
            return;
        } catch (Exception $e) {
            JLog::add($e->getMessage(), JLog::ERROR, 'com_gamification');
            throw new Exception(JText::_('COM_GAMIFICATION_ERROR_SYSTEM'));
        }

        if ((int)$value === 1) {
            $msg = $this->text_prefix . '_N_ITEMS_READ';
        } else {
            $msg = $this->text_prefix . '_N_ITEMS_NOT_READ';
        }

        $this->displayMessage(JText::plural($msg, count($cid)), $redirectOptions);
    }
}
