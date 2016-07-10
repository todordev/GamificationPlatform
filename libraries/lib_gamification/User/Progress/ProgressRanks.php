<?php
/**
 * @package         Gamification\User
 * @subpackage      Progress
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\User\Progress;

use Gamification\User\Rank\Rank as UserRank;
use Gamification\Rank\Rank as BasicRank;
use Prism\Utilities\MathHelper;
use Prism\Constants;

defined('JPATH_PLATFORM') or die;

/**
 * This is an object that represents user progress based on points and ranks.
 *
 * @package         Gamification\User
 * @subpackage      Progress
 */
class ProgressRanks extends Progress
{
    /**
     * Prepare current and next ranks.
     *
     * @throws \RuntimeException
     */
    public function prepareData()
    {
        $userPoints = (int)$this->points->getPointsNumber();

        // Get current level.
        $keys = array(
            'user_id'  => $this->points->getUserId(),
            'group_id' => $this->points->getPoints()->getGroupId(),
        );

        $userRank = new UserRank($this->db);
        $userRank->load($keys);
        
        $this->currentUnit = $userRank->getRank();

        // Get incoming level.
        $query = $this->db->getQuery(true);
        $query
            ->select('a.id, a.title, a.description, a.image, a.activity_text, a.published, a.points_id, a.points_number, a.group_id')
            ->from($this->db->quoteName('#__gfy_ranks', 'a'))
            ->where('a.points_id = ' . (int)$this->points->getPointsId())
            ->where('a.published = ' . (int)Constants::PUBLISHED)
            ->where('a.points_number > ' . $userPoints)
            ->order('a.points_number ASC');

        $this->db->setQuery($query, 0, 1);
        $result = (array)$this->db->loadAssoc();

        if (count($result) > 0) {
            $this->nextUnit    = new BasicRank($this->db);
            $this->nextUnit->bind($result);

            $this->percentageCurrent    = (int)MathHelper::calculatePercentage($userPoints, $this->nextUnit->getPointsNumber());
            $this->percentageNext       = 100 - $this->percentageCurrent;
        } else {
            $this->percentageCurrent    = 100;
            $this->percentageNext       = 100;
        }
    }
}
