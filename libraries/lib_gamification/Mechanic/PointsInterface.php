<?php
/**
 * @package      Gamification
 * @subpackage   Mechanics
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\Mechanic;

// no direct access
defined('JPATH_PLATFORM') or die;

/**
 * This interface should be used to create classes based on points.
 *
 * @package      Prism
 * @subpackage   Mechanics
 */
interface PointsInterface
{
    public function getTitle();
    public function getPoints();
    public function getPointsId();
}
