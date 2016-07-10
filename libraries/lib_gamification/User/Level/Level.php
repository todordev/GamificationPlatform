<?php
/**
 * @package         Gamification\User
 * @subpackage      Levels
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\User\Level;

use Joomla\DI\ContainerAwareInterface;
use Joomla\DI\ContainerAwareTrait;
use Prism\Database\Table;
use Prism\Utilities\StringHelper;
use Gamification\Rank\Rank as BasicRank;
use Gamification\Points\Points as BasicPoints;
use Gamification\Level\Level as BasicLevel;

defined('JPATH_PLATFORM') or die;

/**
 * This is an object that represents user level.
 *
 * @package         Gamification\User
 * @subpackage      Levels
 */
class Level extends Table implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * The ID of the record that contains user level data.
     * @var integer
     */
    protected $id;

    /**
     * This is the ID of the level record in table "#__gfy_levels".
     *
     * @var integer
     */
    protected $level_id;
    protected $group_id;
    protected $user_id;
    
    protected $points_id;
    protected $rank_id;

    /**
     * Points object.
     *
     * @var BasicPoints
     */
    protected $points;

    /**
     * Level object.
     *
     * @var Level
     */
    protected $level;

    /**
     * Rank object.
     *
     * @var BasicRank
     */
    protected $rank;

    /**
     * Load user level data.
     *
     * <code>
     * $keys = array(
     *       'user_id'  => 1,
     *       'group_id' => 2
     * );
     *
     * $userLevel     = new Gamification\User\Level\Level(JFactory::getDbo());
     *
     * $userLevel->load($keys);
     * </code>
     *
     * @param int|array $keys
     * @param array $options
     *
     * @throws \RuntimeException
     */
    public function load($keys, array $options = array())
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);
        $query
            ->select(
                'a.id, a.level_id, a.user_id, a.group_id, ' .
                'b.points_id, b.rank_id'
            )
            ->from($this->db->quoteName('#__gfy_userlevels', 'a'))
            ->leftJoin($this->db->quoteName('#__gfy_levels', 'b') . ' ON a.level_id = b.id');

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
        
        $this->rank   = null;
        $this->points = null;
    }

    protected function prepareLevelObject($levelId)
    {
        if ($levelId > 0) {
            $key = StringHelper::generateMd5Hash(BasicLevel::class, $levelId);

            if ($this->container !== null) {
                if ($this->container->exists($key)) {
                    $this->level = $this->container->get($key);
                } else {
                    $this->level = new BasicLevel($this->db);
                    $this->level->setContainer($this->container);
                    $this->level->load($levelId);

                    $this->container->set($key, $this->level);
                }
            } else {
                $this->level = new BasicLevel($this->db);
                $this->level->load($levelId);
            }
        }
    }
    
    protected function prepareRankObject($rankId)
    {
        if ($rankId > 0) {
            $key = StringHelper::generateMd5Hash(BasicRank::class, $rankId);

            if ($this->container !== null) {
                if ($this->container->exists($key)) {
                    $this->rank = $this->container->get($key);
                } else {
                    $this->rank = new BasicRank($this->db);
                    $this->rank->setContainer($this->container);
                    $this->rank->load($rankId);

                    $this->container->set($key, $this->rank);
                }
            } else {
                $this->rank = new BasicRank($this->db);
                $this->rank->load($rankId);
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
    
    protected function updateObject()
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->update($this->db->quoteName('#__gfy_userlevels'))
            ->set($this->db->quoteName('user_id') .' = ' . (int)$this->user_id)
            ->set($this->db->quoteName('group_id') .' = ' . (int)$this->group_id)
            ->set($this->db->quoteName('level_id') .' = ' . (int)$this->level_id)
            ->where($this->db->quoteName('id') .' = ' . (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();
    }

    protected function insertObject()
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->insert($this->db->quoteName('#__gfy_userlevels'))
            ->set($this->db->quoteName('user_id')  .' = ' . (int)$this->user_id)
            ->set($this->db->quoteName('group_id') .' = ' . (int)$this->group_id)
            ->set($this->db->quoteName('level_id') .' = ' . (int)$this->level_id);

        $this->db->setQuery($query);
        $this->db->execute();

        return $this->db->insertid();
    }

    /**
     * Save the data to the database.
     *
     * <code>
     * $data = array(
     *        'user_id'   => 2,
     *        'group_id'  => 3,
     *        'level_id'  => 4
     * );
     *
     * $userLevel   = new Gamification\User\Level\Level(JFactory::getDbo());
     * $userLevel->load($keys);
     *
     * $userLevel->bind($data);
     * $userLevel->store();
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

    /**
     * Return Points object.
     *
     * <code>
     * $keys = array(
     *       'user_id'  => 1,
     *       'level_id' => 2
     * );
     *
     * $userLevel  = new Gamification\User\Level\Level(JFactory::getDbo());
     * $userLevel->load($keys);
     *
     * $points     = $userLevel->getPoints();
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
     * Return Level object.
     *
     * <code>
     * $keys = array(
     *       'user_id'  => 1,
     *       'group_id' => 2
     * );
     *
     * $userLevel   = new Gamification\User\Level\Level(JFactory::getDbo());
     * $userLevel->load($keys);
     *
     * $level       = $userLevel->getLevel();
     * </code>
     *
     * @throws \RuntimeException
     *
     * @return BasicLevel
     */
    public function getLevel()
    {
        // Create basic Level object.
        if ($this->level === null and (int)$this->level_id > 0) {
            $this->prepareLevelObject($this->level_id);
        }
        
        return $this->level;
    }

    /**
     * Get the rank where the level is positioned.
     *
     * <code>
     * $keys = array(
     *       'user_id'  => 1,
     *       'group_id' => 2
     * );
     *
     * $userLevel   = new Gamification\User\Level\Level(JFactory::getDbo());
     * $userLevel->load($keys);
     *
     * $rank        = $userLevel->getRank();
     * </code>
     *
     * @throws \RuntimeException
     * @return null|BasicRank
     */
    public function getRank()
    {
        // Create a basic Rank object.
        if ($this->rank === null and (int)$this->rank_id > 0) {
            $this->prepareRankObject($this->rank_id);
        }

        return $this->rank;
    }

    /**
     * Return the ID of the level.
     *
     * <code>
     * $keys = array(
     *       'user_id'  => 1,
     *       'group_id' => 2
     * );
     *
     * $userLevel   = new Gamification\User\Level\Level(JFactory::getDbo());
     * $userLevel->load($keys);
     *
     * if (!$userLevel->getId()) {
     * ...
     * }
     * </code>
     *
     * @return int
     */
    public function getId()
    {
        return (int)$this->id;
    }
    
    /**
     * Return level ID.
     *
     * <code>
     * $keys = array(
     *       'user_id'  => 1,
     *       'group_id' => 2
     * );
     *
     * $userLevel   = new Gamification\User\Level\Level(JFactory::getDbo());
     * $userLevel->load($keys);
     *
     * $levelId = $userLevel->getLevelId();
     * </code>
     *
     * @return int
     */
    public function getLevelId()
    {
        return (int)$this->level_id;
    }

    /**
     * Set the ID of the level.
     *
     * <code>
     * $keys = array(
     *       'user_id'  => 1,
     *       'group_id' => 2
     * );
     *
     * $levelId     = 1;
     *
     * $userLevel   = new Gamification\User\Level\Level(JFactory::getDbo());
     * $userLevel->load($keys);
     *
     * $userLevel->setLevelId($levelId);
     * </code>
     *
     * @param int $levelId
     *
     * @return self
     */
    public function setLevelId($levelId)
    {
        $this->level_id = (int)$levelId;
        
        return $this;
    }

    /**
     * Return group ID.
     *
     * <code>
     * $id = 1;
     *
     * $userLevel   = new Gamification\User\Level\Level(JFactory::getDbo());
     * $userLevel->load($id);
     *
     * $groupId = $userLevel->getGroupId();
     * </code>
     *
     * @return int
     */
    public function getGroupId()
    {
        return (int)$this->group_id;
    }

    /**
     * Set the ID of the level.
     *
     * <code>
     * $id = 1;
     *
     * $userLevel   = new Gamification\User\Level\Level(JFactory::getDbo());
     * $userLevel->load($id);
     *
     * $userLevel->setGroupId(100);
     * </code>
     *
     * @param int $groupId
     *
     * @return self
     */
    public function setGroupId($groupId)
    {
        $this->group_id = (int)$groupId;
        
        return $this;
    }
    
    /**
     * Create a record to the database, adding first level.
     *
     * <code>
     * $keys = array(
     *       'user_id'  => 1,
     *       'group_id' => 2
     * );
     *
     * $userLevel   = new Gamification\User\Level\Level(JFactory::getDbo());
     * $userLevel->load($keys);
     *
     * if (!$userLevel->getId()) {
     *
     *      $data = array(
     *          'user_id'  => 1,
     *          'group_id' => 2,
     *          'level_id' => 3
     *      );
     *
     *      $userLevel->startLeveling($data);
     * }
     * </code>
     *
     * @param array $data
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     */
    public function startLeveling(array $data = array())
    {
        if (empty($data['user_id'])) {
            throw new \InvalidArgumentException(\JText::_('LIB_GAMIFICATION_ERROR_INVALID_PARAMETER_USER_ID'));
        }

        if (empty($data['group_id'])) {
            throw new \InvalidArgumentException(\JText::_('LIB_GAMIFICATION_ERROR_INVALID_PARAMETER_GROUP_ID'));
        }

        if (empty($data['level_id'])) {
            throw new \InvalidArgumentException(\JText::_('LIB_GAMIFICATION_ERROR_INVALID_PARAMETER_LEVEL_ID'));
        }

        $this->bind($data);
        $this->store();

        // Load points ID and rank ID.
        $query = $this->db->getQuery(true);
        $query
            ->select('a.points_id, a.rank_id')
            ->from($this->db->quoteName('#__gfy_levels', 'a'))
            ->where('a.id = '. (int)$this->level_id);
        
        $this->db->setQuery($query);
        $result = $this->db->loadAssoc();

        if (count($result) > 0) {
            $this->bind($result);
        }
    }
}
