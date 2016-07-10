<?php
/**
 * @package         Gamification\User
 * @subpackage      Points
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\User\Points;

defined('JPATH_PLATFORM') or die;

/**
 * Points manager interface.
 *
 * @package         Gamification\User
 * @subpackage      Points
 */
interface PointsManagerInterface
{
    public function getPoints();
    public function setPoints(Points $points);
    public function increase($context, $value, array $options = array());
    public function decrease($context, $value, array $options = array());
}
