<?php
/**
 * @package         Gamification\User
 * @subpackage      Badges
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\User\Badge;

use Gamification\Badge\Badge as BasicBadge;
use Gamification\Profile\Profile;
use Prism\Observer\Observable;

defined('JPATH_PLATFORM') or die;

/**
 * This is an object that represents user badge.
 *
 * @package         Gamification\User
 * @subpackage      Badges
 */
class BadgeManager extends Observable implements BadgeManagerInterface
{
    /**
     * @var BasicBadge
     */
    protected $badge;
    
    public function setBadge(BasicBadge $badge)
    {
        $this->badge = $badge;
    }
    
    /**
     * Give a new badge.
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
     * $badge  = new Gamification\Badge\Badge(\JFactory::getDbo());
     * $badge->load($keys);
     *
     * $badgeManager = new Gamification\User\Badge\BadgeManager(\JFactory::getDbo());
     * $badgeManager->setBadge($badge);
     *
     * if ($badgeManager->give($context, $userId, $options)) {
     * // ...
     * }
     * </code>
     *
     * @param string $context
     * @param int $userId
     * @param array $options
     *
     * @throws \RuntimeException
     *
     * @return null|Badge
     */
    public function give($context, $userId, array $options = array())
    {
        if (!($this->badge instanceof BasicBadge)) {
            throw new \UnexpectedValueException('It is missing user badge object.');
        }

        // Check if this badge already exists in user profile.
        $userProfile = new Profile($this->db);
        $badgeExists = $userProfile->hasBadge($this->badge, $userId);

        if (!$badgeExists) {
            $data = array(
                'user_id'        => $userId,
                'badge_id'       => $this->badge->getId(),
                'group_id'       => $this->badge->getGroupId()
            );

            // Create user badge.
            $userBadge = new Badge($this->db);
            $userBadge->bind($data);

            // Implement JObservableInterface: Pre-processing by observers
            $this->observers->update('onBeforeGiveBadge', array($context, &$userBadge, &$options));

            $userBadge->store();

            // Implement JObservableInterface: Post-processing by observers
            $this->observers->update('onAfterGiveBadge', array($context, &$userBadge, &$options));

            return $userBadge;
        }

        return null;
    }
}
