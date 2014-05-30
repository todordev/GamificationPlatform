<?php
/**
 * @package      Gamification Platform
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */
defined('JPATH_BASE') or die;

jimport('joomla.html.html');
jimport('joomla.form.formfield');
jimport('joomla.form.helper');
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
     * @return  array   The field option objects.
     * @since   1.6
     */
    protected function getOptions()
    {
        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('a.id AS value, CONCAT(a.title, " [", a.abbr, "] ") AS text')
            ->from($db->quoteName('#__gfy_points', 'a'))
            ->order("a.title ASC");

        // Get the options.
        $db->setQuery($query);
        $options = $db->loadObjectList();


        $displayRoot = (!empty($this->element["display_root"])) ? true : false;
        if ($displayRoot) {
            array_unshift($options, JHtml::_('select.option', '', JText::_('COM_GAMIFICATION_SELECT_POINTS'), 'value', 'text'));
        }

        // Merge any additional options in the XML definition.
        $options = array_merge(parent::getOptions(), $options);

        return $options;
    }
}
