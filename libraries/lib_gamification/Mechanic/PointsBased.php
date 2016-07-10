<?php
/**
 * @package      Gamification
 * @subpackage   Mechanics
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\Mechanic;

use Gamification\Points\Points;

// no direct access
defined('JPATH_PLATFORM') or die;

/**
 * Interface for objects based on points.
 *
 * @package      Prism
 * @subpackage   Mechanics
 */
interface PointsBased
{
    public function getPointsId();
    public function getPoints();
    public function setPoints(Points $points);
    public function getPointsNumber();
}
