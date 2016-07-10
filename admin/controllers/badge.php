<?php
/**
 * @package      Gamification Platform
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

use Prism\Controller\Form\Backend;
use Joomla\Utilities\ArrayHelper;

// No direct access
defined('_JEXEC') or die;

/**
 * Gamification badge controller class.
 *
 * @package      Gamification Platform
 * @subpackage   Components
 * @since        1.6
 */
class GamificationControllerBadge extends Backend
{
    /**
     * @param string $name
     * @param string $prefix
     * @param array  $config
     *
     * @return GamificationModelBadge
     */
    public function getModel($name = 'Badge', $prefix = 'GamificationModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    public function save($key = null, $urlVar = null)
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $app  = JFactory::getApplication();
        
        $data = $this->input->post->get('jform', array(), 'array');

        $itemId  = ArrayHelper::getValue($data, 'id');
        
        // Store group ID to the session.
        $groupId = ArrayHelper::getValue($data, 'group_id');
        $app->setUserState('com_gamification.badge.group_id', $groupId);

        $redirectOptions = array(
            'task' => $this->getTask(),
            'id'   => $itemId
        );

        $model = $this->getModel();
        /** @var $model GamificationModelBadge */

        $form = $model->getForm($data, false);
        /** @var $form JForm */

        if (!$form) {
            throw new Exception(JText::_('COM_GAMIFICATION_ERROR_FORM_CANNOT_BE_LOADED'));
        }

        // Validate the form
        $validData = $model->validate($form, $data);

        // Check for errors
        if ($validData === false) {
            $this->displayNotice($form->getErrors(), $redirectOptions);

            return;
        }

        try {
            $file = $this->input->files->get('jform', array(), 'array');
            $file = ArrayHelper::getValue($file, 'image');

            // Upload picture
            if (!empty($file['name'])) {
                $imageName = $model->uploadImage($file);
                if ($imageName !== null and $imageName !== '') {
                    $validData['image'] = $imageName;
                }
            }

            $itemId = $model->save($validData);

            $redirectOptions['id'] = $itemId;

        } catch (Exception $e) {
            JLog::add($e->getMessage(), JLog::ERROR, 'com_gamification');
            throw new Exception(JText::_('COM_GAMIFICATION_ERROR_SYSTEM'));
        }

        $this->displayMessage(JText::_('COM_GAMIFICATION_BADGE_SAVED'), $redirectOptions);
    }

    public function removeImage()
    {
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));

        $itemId = $this->input->get->get('id', 0, 'int');

        $redirectOptions = array(
            'view'   => 'badge',
            'layout' => 'edit',
            'id'     => $itemId
        );

        $model = $this->getModel();
        /** @var $model GamificationModelBadge */

        // Check for errors
        if (!$itemId) {
            $this->displayNotice(JText::_('COM_GAMIFICATION_INVALID_ITEM'), $redirectOptions);
            return;
        }

        try {
            $model->removeImage($itemId);
        } catch (Exception $e) {
            JLog::add($e->getMessage(), JLog::ERROR, 'com_gamification');
            throw new Exception(JText::_('COM_GAMIFICATION_ERROR_SYSTEM'));
        }

        $this->displayMessage(JText::_('COM_GAMIFICATION_IMAGE_DELETED'), $redirectOptions);
    }
}
