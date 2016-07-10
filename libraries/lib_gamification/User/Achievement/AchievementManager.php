<?php
/**
 * @package         Gamification\User
 * @subpackage      Points\Achievements
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\User\Achievement;

use Gamification\Achievement\Achievement as BasicAchievement;
use Prism\Observer\Observable;
use Gamification\Profile\Profile;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that manage user achievement based on points.
 *
 * @package         Gamification\User
 * @subpackage      Points\Achievements
 */
class AchievementManager extends Observable implements AchievementManagerInterface
{
    /**
     * @var BasicAchievement
     */
    protected $achievement;

    public function setAchievement(BasicAchievement $achievement)
    {
        $this->achievement = $achievement;
    }

    /**
     * Change user achievement to higher one.
     *
     * <code>
     * $context = "com_user.registration";
     *
     * $keys = array(
     *     "id" => 1,
     *     "group_id" => 2
     * );
     *
     * // Create user achievement object based.
     * $achievement  = new Gamification\Achievement\Achievement(\JFactory::getDbo());
     * $achievement->load($keys);
     *
     * $achievementManager = new Gamification\User\Achievement\AchievementManager(\JFactory::getDbo());
     * $achievementManager->setAchievement($achievement);
     *
     * if ($achievementManager->accomplish($context, $userId, $options)) {
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
     * @return null|Achievement
     */
    public function accomplish($context, $userId, array $options = array())
    {
        if (!($this->achievement instanceof BasicAchievement)) {
            throw new \UnexpectedValueException('It is missing user achievement object.');
        }

        // Check if this achievement already exists in user profile.
        $userProfile = new Profile($this->db);
        $achievementAccomplished = $userProfile->isAchievementAccomplished($this->achievement, $userId);

        if (!$achievementAccomplished) {
            $date = new \JDate();
            $data = array(
                'user_id'        => $userId,
                'achievement_id' => $this->achievement->getId(),
                'accomplished'      => 1,
                'accomplished_at'   => $date->toSql()
            );

            // Create user achievement.
            $userAchievement = new Achievement(\JFactory::getDbo());
            $userAchievement->bind($data);

            // Implement JObservableInterface: Pre-processing by observers
            $this->observers->update('onBeforeAccomplishAchievement', array($context, &$userAchievement, &$options));

            $userAchievement->store();

            // Implement JObservableInterface: Post-processing by observers
            $this->observers->update('onAfterAccomplishAchievement', array($context, &$userAchievement, &$options));

            return $userAchievement;
        }

        return null;
    }
}
