<?php
/**
 * @package      Gamification Platform
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

use Prism\Controller\Form\Backend;
use Joomla\Utilities\ArrayHelper;

// No direct access
defined('_JEXEC') or die;

/**
 * Gamification rank controller class.
 *
 * @package      Gamification Platform
 * @subpackage   Components
 * @since        1.6
 */
class GamificationControllerRank extends Backend
{
    public function getModel($name = 'Rank', $prefix = 'GamificationModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);

        // Load the component parameters.
        $params = JComponentHelper::getParams($this->option);

        // Extension parameters
        $model->imagesFolder = JPath::clean(JPATH_SITE . DIRECTORY_SEPARATOR . $params->get("images_directory", "images/gamification"));

        return $model;
    }

    public function save($key = null, $urlVar = null)
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        $data = $this->input->post->get('jform', array(), 'array');
        $file = $this->input->files->get('jform', array(), 'array');
        $file = ArrayHelper::getValue($file, "image");

        $itemId = ArrayHelper::getValue($data, "id");

        $redirectOptions = array(
            "task" => $this->getTask(),
            "id"   => $itemId
        );

        $model = $this->getModel();
        /** @var $model GamificationModelRank */

        $form = $model->getForm($data, false);
        /** @var $form JForm */

        if (!$form) {
            throw new Exception(JText::_("COM_GAMIFICATION_ERROR_FORM_CANNOT_BE_LOADED"), 500);
        }

        // Validate the form
        $validData = $model->validate($form, $data);

        // Check for errors
        if ($validData === false) {
            $this->displayNotice($form->getErrors(), $redirectOptions);

            return;
        }

        try {

            // Upload picture
            if (!empty($file['name'])) {

                $imageName = $model->uploadImage($file);
                if (!empty($imageName)) {
                    $validData["image"] = $imageName;
                }

            }

            $itemId = $model->save($validData);

            $redirectOptions["id"] = $itemId;

        } catch (Exception $e) {

            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_GAMIFICATION_ERROR_SYSTEM'));

        }

        $this->displayMessage(JText::_('COM_GAMIFICATION_RANK_SAVED'), $redirectOptions);

    }

    public function removeImage()
    {
        JSession::checkToken("get") or jexit(JText::_('JINVALID_TOKEN'));

        $itemId = $this->input->get->get('id', 0, 'int');

        $redirectOptions = array(
            "view"   => "rank",
            "layout" => "edit",
            "id"     => $itemId
        );

        $model = $this->getModel();
        /** @var $model GamificationModelRank */

        // Check for errors
        if (!$itemId) {
            $this->displayNotice(JText::_("COM_GAMIFICATION_INVALID_ITEM"), $redirectOptions);

            return;
        }

        try {

            jimport("joomla.filesystem.file");
            jimport("joomla.filesystem.path");

            $model->removeImage($itemId);

        } catch (Exception $e) {
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_GAMIFICATION_ERROR_SYSTEM'));
        }

        $this->displayMessage(JText::_('COM_GAMIFICATION_RANK_SAVED'), $redirectOptions);
    }
}
