<?php
/**
 * @package         Gamification\User
 * @subpackage      Progress
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\User\Progress;

defined('JPATH_PLATFORM') or die;

/**
 * Interface for measurable unit used in displaying user progress.
 *
 * @package         Gamification\User
 * @subpackage      Progress
 */
interface ProgressMeasurable
{
    public function prepareData();
    public function getPercentageCurrent();
    public function getPercentageNext();
    public function getCurrentUnit();
    public function getNextUnit();
    public function hasNext();
}
