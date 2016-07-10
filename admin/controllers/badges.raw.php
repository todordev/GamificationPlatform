<?php
/**
 * @package      Gamification
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

/**
 * Gamification badges controller class.
 *
 * @package        Gamification
 * @subpackage     Component
 * @since          1.6
 */
class GamificationControllerBadges extends JControllerAdmin
{
    /**
     * Method to get a model object, loading it if required.
     *
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

    /**
     * Method to save the submitted ordering values for records via AJAX.
     *
     * @return  void
     * @throws  Exception
     *
     * @since   3.0
     */
    public function saveOrderAjax()
    {
        $response = new Prism\Response\Json();

        // Get the input
        $pks   = $this->input->post->get('cid', array(), 'array');
        $order = $this->input->post->get('order', array(), 'array');

        // Sanitize the input
        $pks   = Joomla\Utilities\ArrayHelper::toInteger($pks);
        $order = Joomla\Utilities\ArrayHelper::toInteger($order);

        // Get the model
        $model = $this->getModel();

        try {
            $model->saveorder($pks, $order);
        } catch (Exception $e) {
            JLog::add($e->getMessage(), JLog::ERROR, $this->option);
            throw new Exception(JText::_('COM_USERIDEAS_ERROR_SYSTEM'));
        }

        $response
            ->setTitle(JText::_('COM_USERIDEAS_SUCCESS'))
            ->setText(JText::_('JLIB_APPLICATION_SUCCESS_ORDERING_SAVED'))
            ->success();

        echo $response;
        JFactory::getApplication()->close();
    }
}
