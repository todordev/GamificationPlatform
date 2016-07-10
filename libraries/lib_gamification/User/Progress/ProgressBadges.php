<?php
/**
 * @package         Gamification\User
 * @subpackage      Progress
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\User\Progress;

use Gamification\Badge\Badge as BasicBadge;
use Prism\Utilities\MathHelper;
use Prism\Constants;

defined('JPATH_PLATFORM') or die;

/**
 * This is an object that represents user progress based on points and badges.
 *
 * @package         Gamification\User
 * @subpackage      Progress
 */
class ProgressBadges extends Progress
{
    /**
     * Prepare current and next badges.
     *
     * @throws \RuntimeException
     */
    public function prepareData()
    {
        $userPoints = $this->points->getPointsNumber();

        // Get current badge.
        $query = $this->db->getQuery(true);
        $query
            ->select(
                'a.id, a.title, a.points_number, a.image, a.description, a.published, a.points_id, a.group_id, ' .
                'b.id AS user_badge_id'
            )
            ->from($this->db->quoteName('#__gfy_badges', 'a'))
            ->innerJoin($this->db->quoteName('#__gfy_userbadges', 'b') . ' ON a.id = b.badge_id')
            ->where('a.points_id = ' . (int)$this->points->getPointsId())
            ->where('a.published = ' . (int)Constants::PUBLISHED)
            ->where('a.points_number <= ' . (int)$userPoints)
            ->order('a.points_number DESC');

        $this->db->setQuery($query, 0, 1);
        $result = (array)$this->db->loadAssoc();
        if (count($result) > 0) {
            $this->currentUnit = new BasicBadge($this->db);
            $this->currentUnit->bind($result);
        }

        // Get incoming badge.
        $query = $this->db->getQuery(true);
        $query
            ->select('a.id, a.title, a.points_number, a.image, a.description, a.published, a.points_id, a.group_id')
            ->from($this->db->quoteName('#__gfy_badges', 'a'))
            ->where('a.points_id = ' . (int)$this->points->getPointsId())
            ->where('a.published = 1')
            ->where('a.points_number > ' . (int)$userPoints)
            ->order('a.points_number ASC');

        $this->db->setQuery($query, 0, 1);
        $result = (array)$this->db->loadAssoc();

        if (count($result) > 0) {
            $this->nextUnit    = new BasicBadge($this->db);
            $this->nextUnit->bind($result);

            $this->percentageCurrent    = (int)MathHelper::calculatePercentage($userPoints, $this->nextUnit->getPointsNumber());
            $this->percentageNext       = 100 - $this->percentageCurrent;
        } else {
            $this->percentageCurrent    = 100;
            $this->percentageNext       = 100;
        }
    }
}
