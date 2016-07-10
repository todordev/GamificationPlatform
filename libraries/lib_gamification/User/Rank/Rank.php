<?php
/**
 * @package         Gamification\User
 * @subpackage      Ranks
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\User\Rank;

use Joomla\DI\ContainerAwareInterface;
use Joomla\DI\ContainerAwareTrait;
use Prism\Database\Table;
use Prism\Utilities\StringHelper;
use Gamification\Points\Points as BasicPoints;
use Gamification\Rank\Rank as BasicRank;

defined('JPATH_PLATFORM') or die;

/**
 * This is an object that represents user rank.
 *
 * @package         Gamification\User
 * @subpackage      Ranks
 */
class Rank extends Table implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    
    /**
     * The ID of database record in table '#__gfy_userranks'.
     *
     * @var integer
     */
    protected $id;

    /**
     * This is the ID of the rank record in table "#__gfy_ranks".
     *
     * @var int
     */
    protected $rank_id;
    protected $group_id;
    protected $user_id;

    protected $points_id;

    /**
     * Points object.
     *
     * @var BasicPoints
     */
    protected $points;

    /**
     * Rank object.
     *
     * @var Rank
     */
    protected $rank;

    /**
     * Load user rank data.
     *
     * <code>
     * $keys = array(
     *       'user_id'  => 1,
     *       'group_id' => 2
     * );
     *
     * $userRank     = new Gamification\User\Rank\Rank(\JFactory::getDbo());
     * $userRank->load($keys);
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
            ->select('a.id, a.rank_id, a.user_id, a.group_id')
            ->from($this->db->quoteName('#__gfy_userranks', 'a'));

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

    protected function prepareRankObject($rankId)
    {
        if ($rankId > 0) {
            $key = StringHelper::generateMd5Hash(Rank::class, $rankId);

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

    /**
     * Save the data to the database.
     *
     * <code>
     * $data = array(
     *        'user_id'   => 2,
     *        'group_id'  => 3,
     *        'rank_id'   => 4
     * );
     *
     * $userRank   = new Gamification\User\Rank\Rank(\JFactory::getDbo());
     * $userRank->bind($data);
     *
     * $userRank->store();
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
        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->update($this->db->quoteName('#__gfy_userranks'))
            ->set($this->db->quoteName('rank_id') . ' = ' . (int)$this->rank_id)
            ->where($this->db->quoteName('id') .' = ' . (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();
    }

    protected function insertObject()
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->insert($this->db->quoteName('#__gfy_userranks'))
            ->set($this->db->quoteName('user_id')  . ' = ' . (int)$this->user_id)
            ->set($this->db->quoteName('group_id') . ' = ' . (int)$this->group_id)
            ->set($this->db->quoteName('rank_id')  . ' = ' . (int)$this->rank_id);

        $this->db->setQuery($query);
        $this->db->execute();

        return $this->db->insertid();
    }

    /**
     * Return rank ID.
     *
     * <code>
     * $keys = array(
     *       'user_id'  => 1,
     *       'group_id' => 2
     * );
     *
     * $userRank   = new Gamification\User\Rank\Rank(\JFactory::getDbo());
     * $userRank->load($keys);
     *
     * if (!$userRank->getId()) {
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
     *       'rank_id' => 2
     * );
     *
     * $userRank  = new Gamification\User\Rank\Rank(JFactory::getDbo());
     * $userRank->load($keys);
     *
     * $points     = $userRank->getPoints();
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
     * Get the rank where the rank is positioned.
     *
     * <code>
     * $keys = array(
     *       'user_id'  => 1,
     *       'group_id' => 2
     * );
     *
     * $userRank   = new Gamification\User\Rank\Rank(JFactory::getDbo());
     * $userRank->load($keys);
     *
     * $rank        = $userRank->getRank();
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
     * Return the rank ID.
     *
     * <code>
     * $keys = array(
     *       'user_id'  => 1,
     *       'group_id' => 2
     * );
     *
     * $userRank   = new Gamification\User\Rank\Rank(\JFactory::getDbo());
     * $userRank->load($keys);
     *
     * $rankId      = $userRank->getRankId();
     * </code>
     *
     * @return int
     */
    public function getRankId()
    {
        return (int)$this->rank_id;
    }

    /**
     * Set the ID of the rank.
     *
     * <code>
     * $keys = array(
     *       'user_id'  => 1,
     *       'group_id' => 2
     * );
     *
     * $rankId     = 1;
     *
     * $userRank   = new Gamification\User\Rank\Rank(\JFactory::getDbo());
     * $userRank->load($keys);
     *
     * $userRank=>setRankId($rankId);
     * </code>
     *
     * @param integer $rankId
     */
    public function setRankId($rankId)
    {
        $this->rank_id = (int)$rankId;
    }

    /**
     * Return group ID.
     *
     * <code>
     * $id = 1;
     *
     * $userRank   = new Gamification\User\Rank\Rank(JFactory::getDbo());
     * $userRank->load($id);
     *
     * $groupId = $userRank->getGroupId();
     * </code>
     *
     * @return int
     */
    public function getGroupId()
    {
        return (int)$this->group_id;
    }

    /**
     * Set the ID of the rank.
     *
     * <code>
     * $id = 1;
     *
     * $userRank   = new Gamification\User\Rank\Rank(JFactory::getDbo());
     * $userRank->load($id);
     *
     * $userRank->setGroupId(100);
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
     * Return user ID.
     *
     * <code>
     * $id = 1;
     *
     * $userRank   = new Gamification\User\Rank\Rank(JFactory::getDbo());
     * $userRank->load($id);
     *
     * $userId = $userRank->getUserId();
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
     * $userRank   = new Gamification\User\Rank\Rank(JFactory::getDbo());
     * $userRank->load($id);
     *
     * $userRank->setUserId(100);
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
     * This method creates a record in the database.
     * It initializes and adds first rank.
     * Now, the system will be able to update it.
     *
     * </code>
     * $data = array(
     *     'user_id'  => $userId,
     *     'group_id' => $groupId,
     *     'rank_id' => $rankId
     * );
     *
     * $userRank   = new Gamification\User\Rank\Rank(JFactory::getDbo());
     * $userRank->startRanking($data);
     * <code>
     *
     * @param array $data
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function startRanking(array $data = array())
    {
        if (empty($data['user_id'])) {
            throw new \InvalidArgumentException(\JText::_('LIB_GAMIFICATION_ERROR_INVALID_PARAMETER_USER_ID'));
        }

        if (empty($data['group_id'])) {
            throw new \InvalidArgumentException(\JText::_('LIB_GAMIFICATION_ERROR_INVALID_PARAMETER_GROUP_ID'));
        }

        if (empty($data['rank_id'])) {
            throw new \InvalidArgumentException(\JText::_('LIB_GAMIFICATION_ERROR_INVALID_PARAMETER_RANK_ID'));
        }
        
        $this->bind($data);
        $this->store();

        // Load points ID.
        $query = $this->db->getQuery(true);
        $query
            ->select('a.points_id')
            ->from($this->db->quoteName('#__gfy_ranks', 'a'))
            ->where('a.id = '. (int)$this->rank_id);

        $this->db->setQuery($query);
        $pointsId = $this->db->loadResult();

        if ($pointsId > 0) {
            $this->points_id = $pointsId;
        }
    }
}
