<?php
/**
 * @package         Gamification\User
 * @subpackage      Progressbars
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\Mechanic;

use Prism\Renderer\RendererInterface;
use Prism\Renderer\RendererTrait;
use Gamification\User\Progress\Progress;

defined('JPATH_PLATFORM') or die;

/**
 * This is an object that represents user progress.
 *
 * @package         Gamification\User
 * @subpackage      Progressbars
 */
class Progressbar implements RendererInterface
{
    use RendererTrait;
    
    /**
     * This is the number of points needed to be reached this level.
     *
     * @var Progress
     */
    protected $progress;

    /**
     * Initialize the object.
     *
     * <code>
     * $keys = array(
     *       'user_id'  => 1,
     *       'group_id' => 2
     * );
     * $progressBadges = Gamification\User\Progress\ProgressBadges(\JFactory::getDbo, $keys);
     *
     * $progressBar    = new Gamification\User\ProgressBar($progressBadges);
     * </code>
     *
     * @param Progress $progress
     */
    public function __construct(Progress $progress)
    {
        $this->progress = $progress;
        $this->layout   = 'mechanic.progressbar';
    }

    /**
     * Method to get an element.
     *
     * @param   array  $data  Data to be passed into the rendering of the element.
     *
     * @return  string  A string containing the html for the element.
     */
    public function render(array $data = array())
    {
        $data = array_merge($this->getLayoutData(), $data);

        return $this->getRenderer($this->layout)->render($data);
    }

    /**
     * Method to get the data to be passed to the layout for rendering.
     *
     * @return  array
     */
    protected function getLayoutData()
    {
        return array(
            'progress' => $this->progress
        );
    }
}
