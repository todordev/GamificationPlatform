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
 * Gamification reward controller class.
 *
 * @package      Gamification Platform
 * @subpackage   Components
 * @since        1.6
 */
class GamificationControllerReward extends Backend
{
    /**
     * @param string $name
     * @param string $prefix
     * @param array  $config
     *
     * @return GamificationModelReward
     */
    public function getModel($name = 'Reward', $prefix = 'GamificationModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);
        return $model;
    }

    public function save($key = null, $urlVar = null)
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $data   = $this->input->post->get('jform', array(), 'array');
        $itemId = ArrayHelper::getValue($data, 'id');

        $redirectOptions = array(
            'task' => $this->getTask(),
            'id'   => $itemId
        );

        $model = $this->getModel();
        /** @var $model GamificationModelReward */

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
                $resizeImage = ArrayHelper::getValue($data, 'resize_image');

                $images = $model->uploadImage($file, $resizeImage);
                if (count($images) > 0 and $images['image'] !== '') {
                    $validData = array_merge($validData, $images);
                }
            }

            $itemId = $model->save($validData);

            $redirectOptions['id'] = $itemId;
        } catch (Exception $e) {
            JLog::add($e->getMessage(), JLog::ERROR, 'com_gamification');
            throw new Exception(JText::_('COM_GAMIFICATION_ERROR_SYSTEM'));
        }

        $this->displayMessage(JText::_('COM_GAMIFICATION_REWARD_SAVED'), $redirectOptions);
    }

    public function removeImage()
    {
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));

        $itemId = $this->input->get->get('id', 0, 'int');

        $redirectOptions = array(
            'view'   => 'reward',
            'layout' => 'edit',
            'id'     => $itemId
        );

        $model = $this->getModel();
        /** @var $model GamificationModelReward */

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
