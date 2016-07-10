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

/**
 * Form Field class for the Joomla Framework.
 *
 * @package      Gamification Platform
 * @subpackage   Components
 * @since        1.6
 */
class JFormFieldGfycustomdata extends JFormField
{
    /**
     * The form field type.
     *
     * @var     string
     * @since   1.6
     */
    protected $type = 'gfycustomdata';

    /**
     * Layout to render
     *
     * @var  string
     */
    protected $layout = 'form.field.customdata';

    /**
     * Method to get the user field input markup.
     *
     * @return  string  The field input markup.
     *
     * @since   1.6
     *
     * @throw   \UnexpectedValueException
     */
    protected function getInput()
    {
        if (!$this->layout) {
            throw new \UnexpectedValueException(sprintf('%s has no layout assigned.', $this->name));
        }

        return $this->getRenderer($this->layout)->render($this->getLayoutData());
    }

    /**
     * Get the data that is going to be passed to the layout
     *
     * @return  array
     */
    public function getLayoutData()
    {
        // Get the basic field data
        $data = parent::getLayoutData();

        $options = array();
        if ($this->value !== '') {
            $options = json_decode($this->value, true);
        }

        $data['customData'] = $options;

        return $data;
    }
}
