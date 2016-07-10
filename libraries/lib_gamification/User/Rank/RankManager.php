<?php
/**
 * @package         Gamification\User
 * @subpackage      Points\Ranks
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\User\Rank;

use Gamification\Rank\Rank as BasicRank;
use Prism\Observer\Observable;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that manage user rank based on points.
 *
 * @package         Gamification\User
 * @subpackage      Points\Ranks
 */
class RankManager extends Observable implements RankManagerInterface
{
    /**
     * @var BasicRank
     */
    protected $rank;

    public function setRank(BasicRank $rank)
    {
        $this->rank = $rank;
    }

    /**
     * Change user rank to higher one.
     *
     * <code>
     * $context = "com_user.registration";
     *
     * $keys = array(
     *     "id" => 1,
     *     "group_id" => 2
     * );
     *
     * // Create user badge object based.
     * $rank  = new Gamification\Rank\Rank(\JFactory::getDbo());
     * $rank->load($keys);
     *
     * $rankManager = new Gamification\User\Rank\RankManager(\JFactory::getDbo());
     * $rankManager->setRank($rank);
     *
     * if ($rankManager->give($context, $userId, $options)) {
     * // ...
     * }
     * </code>
     *
     * @param string $context
     * @param int $userId
     * @param array $options
     *
     * @throws \UnexpectedValueException
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     *
     * @return null|Rank
     */
    public function give($context, $userId, array $options = array())
    {
        if (!($this->rank instanceof BasicRank)) {
            throw new \UnexpectedValueException('It is missing user rank object.');
        }

        $keys = array(
            'user_id'  => $userId,
            'group_id' => $this->rank->getGroupId()
        );
        
        $userRank = new Rank(\JFactory::getDbo());
        $userRank->load($keys);

        // Implement JObservableInterface: Pre-processing by observers
        $this->observers->update('onBeforeGiveRank', array($context, &$userRank, &$options));
        
        if (!$userRank->getId()) { // Start ranking.
            $keys['rank_id'] = $this->rank->getId();
            $userRank->startRanking($keys);
        } else {
            if ((int)$this->rank->getId() === (int)$userRank->getRankId()) {
                return null;
            }

            // Change the current rank ID with another one.
            $userRank->setRankId($this->rank->getId());
            $userRank->store();
        }
        
        // Implement JObservableInterface: Post-processing by observers
        $this->observers->update('onAfterGiveRank', array($context, &$userRank, &$options));

        return $userRank;
    }
}
