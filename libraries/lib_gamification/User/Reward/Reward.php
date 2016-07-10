<?php
/**
 * @package         Gamification\User
 * @subpackage      Rewards
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\User\Reward;

use Joomla\DI\ContainerAwareInterface;
use Joomla\DI\ContainerAwareTrait;
use Prism\Database\Table;
use Prism\Utilities\StringHelper;
use Gamification\Points\Points as BasicPoints;
use Gamification\Reward\Reward as BasicReward;

defined('JPATH_PLATFORM') or die;

/**
 * This is an object that represents user reward.
 *
 * @package         Gamification\User
 * @subpackage      Rewards
 */
class Reward extends Table implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    
    /**
     * The ID of database record in table '#__gfy_userrewards'.
     *
     * @var integer
     */
    protected $id;

    /**
     * This is the ID of the reward record in table "#__gfy_rewards".
     *
     * @var int
     */
    protected $reward_id;
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
     * Reward object.
     *
     * @var Reward
     */
    protected $reward;

    /**
     * Load user reward data.
     *
     * <code>
     * $keys = array(
     *       'user_id'  => 1,
     *       'reward_id' => 2
     * );
     *
     * $userReward     = new Gamification\User\Reward\Reward(\JFactory::getDbo());
     * $userReward->load($keys);
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
            ->select('a.id, a.reward_id, a.user_id, a.group_id')
            ->from($this->db->quoteName('#__gfy_userrewards', 'a'));

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

    protected function prepareRewardObject($rewardId)
    {
        if ($rewardId > 0) {
            $key = StringHelper::generateMd5Hash(Reward::class, $rewardId);

            if ($this->container !== null) {
                if ($this->container->exists($key)) {
                    $this->reward = $this->container->get($key);
                } else {
                    $this->reward = new BasicReward($this->db);
                    $this->reward->setContainer($this->container);
                    $this->reward->load($rewardId);

                    $this->container->set($key, $this->reward);
                }
            } else {
                $this->reward = new BasicReward($this->db);
                $this->reward->load($rewardId);
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
     *        'reward_id'   => 4
     * );
     *
     * $userReward   = new Gamification\User\Reward\Reward(\JFactory::getDbo());
     * $userReward->bind($data);
     *
     * $userReward->store();
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
            ->update($this->db->quoteName('#__gfy_userrewards'))
            ->set($this->db->quoteName('reward_id') . ' = ' . (int)$this->reward_id)
            ->set($this->db->quoteName('group_id') . ' = ' . (int)$this->group_id)
            ->where($this->db->quoteName('id') .' = ' . (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();
    }

    protected function insertObject()
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->insert($this->db->quoteName('#__gfy_userrewards'))
            ->set($this->db->quoteName('user_id')  . ' = ' . (int)$this->user_id)
            ->set($this->db->quoteName('group_id')  . ' = ' . (int)$this->group_id)
            ->set($this->db->quoteName('reward_id')  . ' = ' . (int)$this->reward_id);

        $this->db->setQuery($query);
        $this->db->execute();

        return $this->db->insertid();
    }

    /**
     * Return reward ID.
     *
     * <code>
     * $keys = array(
     *       'user_id'  => 1,
     *       'reward_id' => 2
     * );
     *
     * $userReward   = new Gamification\User\Reward\Reward(\JFactory::getDbo());
     * $userReward->load($keys);
     *
     * if (!$userReward->getId()) {
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
     *       'reward_id' => 2
     * );
     *
     * $userReward  = new Gamification\User\Reward\Reward(JFactory::getDbo());
     * $userReward->load($keys);
     *
     * $points     = $userReward->getPoints();
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
     * Get the reward where the reward is positioned.
     *
     * <code>
     * $keys = array(
     *       'user_id'  => 1,
     *       'reward_id' => 2
     * );
     *
     * $userReward   = new Gamification\User\Reward\Reward(JFactory::getDbo());
     * $userReward->load($keys);
     *
     * $reward        = $userReward->getReward();
     * </code>
     *
     * @throws \RuntimeException
     * @return null|BasicReward
     */
    public function getReward()
    {
        // Create a basic Reward object.
        if ($this->reward === null and (int)$this->reward_id > 0) {
            $this->prepareRewardObject($this->reward_id);
        }

        return $this->reward;
    }

    /**
     * Return the reward ID.
     *
     * <code>
     * $keys = array(
     *       'user_id'  => 1,
     *       'reward_id' => 2
     * );
     *
     * $userReward   = new Gamification\User\Reward\Reward(\JFactory::getDbo());
     * $userReward->load($keys);
     *
     * $rewardId      = $userReward->getRewardId();
     * </code>
     *
     * @return int
     */
    public function getRewardId()
    {
        return (int)$this->reward_id;
    }

    /**
     * Set the ID of the reward.
     *
     * <code>
     * $keys = array(
     *       'user_id'  => 1,
     *       'reward_id' => 2
     * );
     *
     * $rewardId     = 1;
     *
     * $userReward   = new Gamification\User\Reward\Reward(\JFactory::getDbo());
     * $userReward->load($keys);
     *
     * $userReward=>setRewardId($rewardId);
     * </code>
     *
     * @param integer $rewardId
     */
    public function setRewardId($rewardId)
    {
        $this->reward_id = (int)$rewardId;
    }

    /**
     * Return user ID.
     *
     * <code>
     * $id = 1;
     *
     * $userReward   = new Gamification\User\Reward\Reward(JFactory::getDbo());
     * $userReward->load($id);
     *
     * $userId = $userReward->getUserId();
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
     * $userReward   = new Gamification\User\Reward\Reward(JFactory::getDbo());
     * $userReward->load($id);
     *
     * $userReward->setUserId(100);
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
     * Return group ID.
     *
     * <code>
     * $id = 1;
     *
     * $userReward   = new Gamification\User\Reward\Reward(JFactory::getDbo());
     * $userReward->load($id);
     *
     * $groupId = $userReward->getGroupId();
     * </code>
     *
     * @return int
     */
    public function getGroupId()
    {
        return (int)$this->group_id;
    }

    /**
     * Set group ID.
     *
     * <code>
     * $id = 1;
     *
     * $userReward   = new Gamification\User\Reward\Reward(JFactory::getDbo());
     * $userReward->load($id);
     *
     * $userReward->setGroupId(100);
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
}
