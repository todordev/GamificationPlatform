<?php
/**
 * @package      Gamification Platform
 * @subpackage   Components Platform
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('itprism.controller.form');

/**
 * Gamification Platform Profile controller class.
 *
 * @package      GamificationPlatform
 * @subpackage   Components
 *
 * @todo         Fix this controller
 * @since        1.6
 */
class GamificationControllerProfile extends ITPrismControllerForm
{
    /**
     * Proxy for getModel.
     * @since   1.6
     */
    public function getModel($name = 'Profile', $prefix = 'GamificationModel', $config = array('ignore_request' => true))
    {
        $model = parent::getModel($name, $prefix, $config);

        return $model;
    }

    /**
     * Save an item
     */
    public function save($key = null, $urlVar = null)
    {
        JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

        // Get form data
        $data  = $this->input->post->get('jform', array(), 'array');
        $model = $this->getModel();
        /** @var $model GamificationModelProfile * */

        $form = $model->getForm($data, false);
        /** @var $form JForm * */

        if (!$form) {
            throw new Exception(JText::_("COM_GAMIFICATION_ERROR_FORM_CANNOT_BE_LOADED"), 500);
        }

        // Get item ID
        $itemId = JArrayHelper::getValue($data, "id");

        // Validate form data
        $validData = $model->validate($form, $data);

        // Check for validation errors.
        if ($validData === false) {
            $this->defaultLink .= "&view=" . $this->view_item . "&layout=edit";

            if ($itemId) {
                $this->defaultLink .= "&id=" . $itemId;
            }

            $this->setMessage($model->getError(), "notice");
            $this->setRedirect(JRoute::_($this->defaultLink, false));

            return;
        }

        try {
            $itemId = $model->save($validData);
        } catch (Exception $e) {
            JLog::add($e->getMessage());
            throw new Exception(JText::_('COM_GAMIFICATION_ERROR_SYSTEM'));
        }

        $msg  = JText::_('COM_GAMIFICATION_PROFILE_SAVED');
        $link = $this->prepareRedirectLink($itemId);

        $this->setRedirect(JRoute::_($link, false), $msg);

    }
}
