<?php
/**
 * @package         Gamification\User
 * @subpackage      Points\Levels
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\User\Level;

use Gamification\Level\Level as BasicLevel;
use Prism\Observer\Observable;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that manage user level based on points.
 *
 * @package         Gamification\User
 * @subpackage      Points\Levels
 */
class LevelManager extends Observable implements LevelManagerInterface
{
    /**
     * @var BasicLevel
     */
    protected $level;

    public function setLevel(BasicLevel $level)
    {
        $this->level = $level;
    }

    /**
     * Change user level to higher one.
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
     * $level  = new Gamification\Level\Level(\JFactory::getDbo());
     * $level->load($keys);
     *
     * $levelManager = new Gamification\User\Level\LevelManager(\JFactory::getDbo());
     * $levelManager->setLevel($level);
     *
     * if ($levelManager->give($context, $userId, $options)) {
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
     * @return null|Level
     */
    public function levelUp($context, $userId, array $options = array())
    {
        if (!($this->level instanceof BasicLevel)) {
            throw new \UnexpectedValueException('It is missing user level object.');
        }

        $keys = array(
            'user_id'  => $userId,
            'group_id' => $this->level->getGroupId()
        );
        
        $userLevel = new Level(\JFactory::getDbo());
        $userLevel->load($keys);

        // Implement JObservableInterface: Pre-processing by observers
        $this->observers->update('onBeforeLevelUp', array($context, &$userLevel, &$options));
        
        if (!$userLevel->getId()) {
            $keys['level_id'] = $this->level->getId();
            $userLevel->startLeveling($keys);
        } else {
            if ((int)$this->level->getId() === (int)$userLevel->getLevelId()) {
                return null;
            }

            // Change the current rank ID with another one.
            $userLevel->setLevelId($this->level->getId());
            $userLevel->store();
        }

        // Implement JObservableInterface: Post-processing by observers
        $this->observers->update('onAfterLevelUp', array($context, &$userLevel, &$options));

        return $userLevel;
    }
}
