<?php
/**
 * @package      Gamification Platform
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */
defined('JPATH_BASE') or die;

jimport('Prism.init');
jimport('Gamification.init');

JFormHelper::loadFieldClass('list');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package      Gamification Platform
 * @subpackage   Components
 * @since        1.6
 */
class JFormFieldGfyPoints extends JFormFieldList
{
    /**
     * The form field type.
     *
     * @var     string
     * @since   1.6
     */
    protected $type = 'gfypoints';

    /**
     * Method to get the field options.
     *
     * @throws  \RuntimeException
     * @throws  \InvalidArgumentException
     *
     * @return  array   The field option objects.
     */
    protected function getOptions()
    {
        $pointsItems = new Gamification\Points\PointsCollection(JFactory::getDbo());
        $pointsItems->load();

        $options = $pointsItems->toOptions('id', 'title', 'abbr');

        $displayRoot = (!empty($this->element['display_root'])) ? true : false;
        if ($displayRoot) {
            array_unshift($options, JHtml::_('select.option', '', JText::_('COM_GAMIFICATION_SELECT_POINTS'), 'value', 'text'));
        }

        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }
}
