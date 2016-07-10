<?php
/**
 * @package         Gamification\User
 * @subpackage      Achievements
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\User\Achievement;

use Joomla\DI\ContainerAwareInterface;
use Joomla\DI\ContainerAwareTrait;
use Prism\Database\Table;
use Prism\Utilities\StringHelper;
use Gamification\Points\Points as BasicPoints;
use Gamification\Achievement\Achievement as BasicAchievement;

defined('JPATH_PLATFORM') or die;

/**
 * This is an object that represents user achievement.
 *
 * @package         Gamification\User
 * @subpackage      Achievements
 */
class Achievement extends Table implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    
    /**
     * The ID of database record in table '#__gfy_userachievements'.
     *
     * @var integer
     */
    protected $id;

    /**
     * This is the ID of the achievement record in table "#__gfy_achievements".
     *
     * @var int
     */
    protected $achievement_id;
    protected $user_id;
    protected $accomplished;
    protected $accomplished_at;

    protected $points_id;

    /**
     * Points object.
     *
     * @var BasicPoints
     */
    protected $points;

    /**
     * Achievement object.
     *
     * @var Achievement
     */
    protected $achievement;

    /**
     * Load user achievement data.
     *
     * <code>
     * $keys = array(
     *       'user_id'  => 1,
     *       'achievement_id' => 2
     * );
     *
     * $userAchievement     = new Gamification\User\Achievement\Achievement(\JFactory::getDbo());
     * $userAchievement->load($keys);
     * </code>
     *
     * @param array $keys
     * @param array $options
     *
     * @throws \RuntimeException
     */
    public function load($keys, array $options = array())
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);
        $query
            ->select('a.id, a.achievement_id, a.user_id, a.accomplished, a.accomplished_at')
            ->from($this->db->quoteName('#__gfy_userachievements', 'a'));

        // Prepare keys.
        if (is_array($keys)) {
            foreach ($keys as $column => $value) {
                $query->where($this->db->quoteName('a.'.$column) . ' = ' . $this->db->quote($value));
            }
        } else {
            $query->where('a.id = ' . (int)$keys);
        }

        $this->db->setQuery($query);
        $result = (array)$this->db->loadAssoc();

        $this->bind($result);
    }

    protected function prepareAchievementObject($achievementId)
    {
        if ($achievementId > 0) {
            $key = StringHelper::generateMd5Hash(Achievement::class, $achievementId);

            if ($this->container !== null) {
                if ($this->container->exists($key)) {
                    $this->achievement = $this->container->get($key);
                } else {
                    $this->achievement = new BasicAchievement($this->db);
                    $this->achievement->setContainer($this->container);
                    $this->achievement->load($achievementId);

                    $this->container->set($key, $this->achievement);
                }
            } else {
                $this->achievement = new BasicAchievement($this->db);
                $this->achievement->load($achievementId);
            }
        }
    }

    protected function preparePointsObject($pointsId)
    {
        if ($pointsId > 0) {
            $key = StringHelper::generateMd5Hash(BasicPoints::class, $pointsId);

            if ($this->container !== null) {
                if ($this->container->exists($key)) {
                    $this->points = $this->container->get($key);
                } else {
                    $this->points = new BasicPoints($this->db);
                    $this->points->load($pointsId);

                    $this->container->set($key, $this->points);
                }
            } else {
                $this->points = new BasicPoints($this->db);
                $this->points->load($pointsId);
            }
        }
    }

    /**
     * Save the data to the database.
     *
     * <code>
     * $data = array(
     *        'user_id'   => 2,
     *        'achievement_id'   => 4
     * );
     *
     * $userAchievement   = new Gamification\User\Achievement\Achievement(\JFactory::getDbo());
     * $userAchievement->bind($data);
     *
     * $userAchievement->store();
     * </code>
     */
    public function store()
    {
        if (!$this->id) {
            $this->id = $this->insertObject();
        } else {
            $this->updateObject();
        }
    }
    
    protected function updateObject()
    {
        if ((int)$this->accomplished_at === 0) {
            $accomplishedAt = $this->db->quote('0000-00-00');
        } else {
            $date = new \JDate($this->accomplished_at);
            $accomplishedAt = $this->db->quote($date->toSql());
        }
        
        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->update($this->db->quoteName('#__gfy_userachievements'))
            ->set($this->db->quoteName('achievement_id') . ' = ' . (int)$this->achievement_id)
            ->set($this->db->quoteName('accomplished') . ' = ' . (int)$this->accomplished)
            ->set($this->db->quoteName('accomplished_at') . ' = ' . $accomplishedAt)
            ->where($this->db->quoteName('id') .' = ' . (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();
    }

    protected function insertObject()
    {
        if ((int)$this->accomplished_at === 0) {
            $accomplishedAt = $this->db->quote('0000-00-00');
        } else {
            $date = new \JDate($this->accomplished_at);
            $accomplishedAt = $this->db->quote($date->toSql());
        }
        
        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->insert($this->db->quoteName('#__gfy_userachievements'))
            ->set($this->db->quoteName('user_id')  . ' = ' . (int)$this->user_id)
            ->set($this->db->quoteName('accomplished') . ' = ' . (int)$this->accomplished)
            ->set($this->db->quoteName('accomplished_at') . ' = ' . $accomplishedAt)
            ->set($this->db->quoteName('achievement_id')  . ' = ' . (int)$this->achievement_id);

        $this->db->setQuery($query);
        $this->db->execute();

        return $this->db->insertid();
    }

    /**
     * Return achievement ID.
     *
     * <code>
     * $keys = array(
     *       'user_id'  => 1,
     *       'achievement_id' => 2
     * );
     *
     * $userAchievement   = new Gamification\User\Achievement\Achievement(\JFactory::getDbo());
     * $userAchievement->load($keys);
     *
     * if (!$userAchievement->getId()) {
     * ...
     * }
     * </code>
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Return Points object.
     *
     * <code>
     * $keys = array(
     *       'user_id'  => 1,
     *       'achievement_id' => 2
     * );
     *
     * $userAchievement  = new Gamification\User\Achievement\Achievement(JFactory::getDbo());
     * $userAchievement->load($keys);
     *
     * $points     = $userAchievement->getPoints();
     * </code>
     *
     * @throws \RuntimeException
     * @return BasicPoints
     */
    public function getPoints()
    {
        // Create Points object.
        if ($this->points === null and (int)$this->points_id > 0) {
            $this->preparePointsObject($this->points_id);
        }

        return $this->points;
    }

    /**
     * Get the achievement where the achievement is positioned.
     *
     * <code>
     * $keys = array(
     *       'user_id'  => 1,
     *       'achievement_id' => 2
     * );
     *
     * $userAchievement   = new Gamification\User\Achievement\Achievement(JFactory::getDbo());
     * $userAchievement->load($keys);
     *
     * $achievement        = $userAchievement->getAchievement();
     * </code>
     *
     * @throws \RuntimeException
     * @return null|BasicAchievement
     */
    public function getAchievement()
    {
        // Create a basic Achievement object.
        if ($this->achievement === null and (int)$this->achievement_id > 0) {
            $this->prepareAchievementObject($this->achievement_id);
        }

        return $this->achievement;
    }

    /**
     * Return the achievement ID.
     *
     * <code>
     * $keys = array(
     *       'user_id'  => 1,
     *       'achievement_id' => 2
     * );
     *
     * $userAchievement   = new Gamification\User\Achievement\Achievement(\JFactory::getDbo());
     * $userAchievement->load($keys);
     *
     * $achievementId      = $userAchievement->getAchievementId();
     * </code>
     *
     * @return int
     */
    public function getAchievementId()
    {
        return (int)$this->achievement_id;
    }

    /**
     * Set the ID of the achievement.
     *
     * <code>
     * $keys = array(
     *       'user_id'  => 1,
     *       'achievement_id' => 2
     * );
     *
     * $achievementId     = 1;
     *
     * $userAchievement   = new Gamification\User\Achievement\Achievement(\JFactory::getDbo());
     * $userAchievement->load($keys);
     *
     * $userAchievement=>setAchievementId($achievementId);
     * </code>
     *
     * @param integer $achievementId
     */
    public function setAchievementId($achievementId)
    {
        $this->achievement_id = (int)$achievementId;
    }

    /**
     * Return the date when this achievement has been accomplished.
     *
     * <code>
     * $id = 1;
     *
     * $userAchievement   = new Gamification\User\Achievement\Achievement(JFactory::getDbo());
     * $userAchievement->load($id);
     *
     * echo $userAchievement->getAccomplishedAt();
     * </code>
     *
     * @return string
     */
    public function getAccomplishedAt()
    {
        return $this->accomplished_at;
    }

    /**
     * Set the date when this achievement has been accomplished.
     *
     * <code>
     * $id = 1;
     *
     * $userAchievement   = new Gamification\User\Achievement\Achievement(JFactory::getDbo());
     * $userAchievement->load($id);
     *
     * $userAchievement->setAccomplishedAt('2016-06-01');
     * </code>
     *
     * @param string $date
     *
     * @return self
     */
    public function setAccomplishedAt($date)
    {
        $this->accomplished_at = $date;

        return $this;
    }

    /**
     * Set the status of the achievement to accomplished.
     *
     * <code>
     * $id = 1;
     *
     * $userAchievement   = new Gamification\User\Achievement\Achievement(JFactory::getDbo());
     * $userAchievement->load($id);
     *
     * $userAchievement->setStatusAccomplished();
     * </code>
     *
     * @return self
     */
    public function setStatusAccomplished()
    {
        $this->accomplished = 1;

        return $this;
    }

    /**
     * Set the status of the achievement to NOT accomplished.
     *
     * <code>
     * $id = 1;
     *
     * $userAchievement   = new Gamification\User\Achievement\Achievement(JFactory::getDbo());
     * $userAchievement->load($id);
     *
     * $userAchievement->setStatusNotAccomplished();
     * </code>
     *
     * @return self
     */
    public function setStatusNotAccomplished()
    {
        $this->accomplished = 0;

        return $this;
    }

    /**
     * Check if the status of the achievement is accomplished.
     *
     * <code>
     * $id = 1;
     *
     * $userAchievement   = new Gamification\User\Achievement\Achievement(JFactory::getDbo());
     * $userAchievement->load($id);
     *
     * if (!$userAchievement->isAccomplished()) {
     * // ....
     * }
     * </code>
     *
     * @return bool
     */
    public function isAccomplished()
    {
        return (bool)$this->accomplished;
    }
    
    /**
     * Return user ID.
     *
     * <code>
     * $id = 1;
     *
     * $userAchievement   = new Gamification\User\Achievement\Achievement(JFactory::getDbo());
     * $userAchievement->load($id);
     *
     * $userId = $userAchievement->getUserId();
     * </code>
     *
     * @return int
     */
    public function getUserId()
    {
        return (int)$this->user_id;
    }

    /**
     * Set user ID.
     *
     * <code>
     * $id = 1;
     *
     * $userAchievement   = new Gamification\User\Achievement\Achievement(JFactory::getDbo());
     * $userAchievement->load($id);
     *
     * $userAchievement->setUserId(100);
     * </code>
     *
     * @param int $userId
     *
     * @return self
     */
    public function setUserId($userId)
    {
        $this->user_id = (int)$userId;

        return $this;
    }
    
    /**
     * This method creates a record in the database for this achievement.
     *
     * </code>
     * $data = array(
     *     'user_id'  => $userId,
     *     'achievement_id' => $achievementId
     * );
     *
     * $userAchievement   = new Gamification\User\Achievement\Achievement(JFactory::getDbo());
     * $userAchievement->startAchievement($data);
     * <code>
     *
     * @param array $data
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function startAchievement(array $data = array())
    {
        if (empty($data['user_id'])) {
            throw new \InvalidArgumentException(\JText::_('LIB_GAMIFICATION_ERROR_INVALID_PARAMETER_USER_ID'));
        }

        if (empty($data['achievement_id'])) {
            throw new \InvalidArgumentException(\JText::_('LIB_GAMIFICATION_ERROR_INVALID_PARAMETER_ACHIEVEMENT_ID'));
        }
        
        $this->bind($data);
        $this->store();

        // Load points ID.
        $query = $this->db->getQuery(true);
        $query
            ->select('a.points_id')
            ->from($this->db->quoteName('#__gfy_achievements', 'a'))
            ->where('a.id = '. (int)$this->achievement_id);

        $this->db->setQuery($query);
        $pointsId = $this->db->loadResult();

        if ($pointsId > 0) {
            $this->points_id = $pointsId;
        }
    }
}
