<?php
/**
 * @package         Gamification
 * @subpackage      GamificationLibrary
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\User;

use Joomla\Utilities\ArrayHelper;
use Joomla\String\String;
use Prism\Database\TableObservable;
use Gamification\Mechanic\PointsInterface;

defined('JPATH_PLATFORM') or die;

/**
 * This is an object that represents user rank.
 *
 * @package         Gamification
 * @subpackage      GamificationLibrary
 */
class Rank extends TableObservable implements PointsInterface
{
    /**
     * The ID of database record in table "#__gfy_userranks".
     *
     * @var integer
     */
    protected $id;

    protected $title;
    protected $description;

    /**
     * This is the number of points needed to be reached this rank.
     * @var integer
     */
    protected $points;

    protected $image;
    protected $published;

    /**
     * This is the ID of the rank record in table "#__gfy_ranks".
     *
     * @var integer
     */
    protected $rank_id;

    protected $group_id;
    protected $user_id;

    protected $points_id;

    protected static $instances = array();

    /**
     * Create and initialize the object.
     *
     * <code>
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     * $userRank    = Gamification\User\Rank::getInstance(\JFactory::getDbo(), $keys);
     * </code>
     *
     * @param \JDatabaseDriver $db
     * @param  array $keys
     * @param  array $options
     *
     * @return null|self
     */
    public static function getInstance(\JDatabaseDriver $db, array $keys, array $options = array())
    {
        $userId  = ArrayHelper::getValue($keys, "user_id");
        $groupId = ArrayHelper::getValue($keys, "group_id");

        $index = md5($userId . ":" . $groupId);

        if (!isset(self::$instances[$index])) {
            $item = new Rank($db, $options);
            $item->load($keys);

            self::$instances[$index] = $item;
        }

        return self::$instances[$index];
    }

    /**
     * Load user rank data.
     *
     * <code>
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $userRank     = new Gamification\User\Rank(\JFactory::getDbo());
     * $userRank->load($keys);
     * </code>
     *
     * @param array $keys
     * @param array $options
     */
    public function load($keys, $options = array())
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);
        $query
            ->select(
                "a.id, a.rank_id, a.user_id, a.group_id, " .
                "b.title, b.description, b.points, b.image, b.published, b.points_id"
            )
            ->from($this->db->quoteName("#__gfy_userranks", "a"))
            ->leftJoin($this->db->quoteName("#__gfy_ranks", "b") . ' ON a.rank_id = b.id');

        // Prepare keys.
        if (is_array($keys)) {
            foreach ($keys as $column => $value) {
                $query->where($this->db->quoteName("a.".$column) . " = " . $this->db->quote($value));
            }
        } else {
            $query->where("a.id = " . (int)$keys);
        }

        $this->db->setQuery($query);
        $result = (array)$this->db->loadAssoc();

        $this->bind($result);
    }

    /**
     * Save the data to the database.
     *
     * <code>
     * $data = array(
     *        "user_id"   => 2,
     *        "group_id"  => 3,
     *        "rank_id"   => 4
     * );
     *
     * $userRank   = new Gamification\User\Rank(\JFactory::getDbo());
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
            ->update($this->db->quoteName("#__gfy_userranks"))
            ->set($this->db->quoteName("rank_id") . " = " . (int)$this->rank_id)
            ->where($this->db->quoteName("id") ." = " . (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();
    }

    protected function insertObject()
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->insert($this->db->quoteName("#__gfy_userranks"))
            ->set($this->db->quoteName("user_id")  . " = " . (int)$this->user_id)
            ->set($this->db->quoteName("group_id") . " = " . (int)$this->group_id)
            ->set($this->db->quoteName("rank_id")  . " = " . (int)$this->rank_id);

        $this->db->setQuery($query);
        $this->db->execute();

        return $this->db->insertid();
    }

    /**
     * Return rank ID.
     *
     * <code>
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $userRank   = new Gamification\User\Rank(\JFactory::getDbo());
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
     * Return the title of the rank.
     *
     * <code>
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $userRank   = new Gamification\User\Rank(\JFactory::getDbo());
     * $userRank->load($keys);
     *
     * $title      = $userRank->getTitle();
     * </code>
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get rank points.
     *
     * <code>
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $userRank      = new Gamification\User\Rank(\JFactory::getDbo());
     * $userRank->load($keys);
     *
     * $points     = $userRank->getPoints();
     * </code>
     *
     * @return number
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * Get the points ID used for the rank.
     *
     * <code>
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $userRank      = new Gamification\User\Rank(\JFactory::getDbo());
     * $userRank->load($keys);
     *
     * $pointsId   = $userRank->getPointsId();
     * </code>
     *
     * @return integer
     */
    public function getPointsId()
    {
        return $this->points_id;
    }

    /**
     * Return the rank ID.
     *
     * <code>
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $userRank   = new Gamification\User\Rank(\JFactory::getDbo());
     * $userRank->load($keys);
     *
     * $rankId      = $userRank->getRankId();
     * </code>
     *
     * @return string
     */
    public function getRankId()
    {
        return $this->rank_id;
    }

    /**
     * Return rank image.
     *
     * <code>
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $userRank   = new Gamification\User\Rank(\JFactory::getDbo());
     * $userRank->load($keys);
     *
     * $image      = $userRank->getImage();
     * </code>
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Set the ID of the rank.
     *
     * <code>
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $rankId     = 1;
     *
     * $userRank   = new Gamification\User\Rank(\JFactory::getDbo());
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
     * This method creates a record in the database.
     * It initializes and adds first rank.
     * Now, the system will be able to update it.
     *
     * </code>
     * $data = array(
     *     "user_id"  => $userId, // Mandatory
     *     "group_id" => $groupId, // Mandatory
     *     "rank_id" => $rankId // Mandatory
     * );
     *
     * $userRank   = new Gamification\User\Rank();
     * $userRank->startRanking($data);
     * <code>
     *
     * @param array $data
     */
    public function startRanking(array $data = array())
    {
        if (empty($data["user_id"])) {
            throw new \InvalidArgumentException(\JText::_("LIB_GAMIFICATION_ERROR_INVALID_PARAMETER_USER_ID"));
        }

        if (empty($data["group_id"])) {
            throw new \InvalidArgumentException(\JText::_("LIB_GAMIFICATION_ERROR_INVALID_PARAMETER_GROUP_ID"));
        }

        if (empty($data["rank_id"])) {
            throw new \InvalidArgumentException(\JText::_("LIB_GAMIFICATION_ERROR_INVALID_PARAMETER_RANK_ID"));
        }
        
        $this->bind($data);
        $this->store();
    }

    /**
     * Return badge description with possibility
     * to replace placeholders with dynamically generated data.
     *
     * <code>
     * $badgeId    = 1;
     * $badge      = new Gamification\Badge\Badge(\JFactory::getDbo());
     *
     * $data = array(
     *     "name" => "John Dow",
     *     "title" => "..."
     * );
     *
     * echo $badge->getDescription($data);
     * </code>
     *
     * @param array $data
     * @return string
     */
    public function getDescription(array $data = array())
    {
        if (!empty($data)) {
            $result = $this->description;

            foreach ($data as $placeholder => $value) {
                $placeholder = "{".String::strtoupper($placeholder)."}";
                $result = str_replace($placeholder, $value, $result);
            }

            return $result;

        } else {
            return $this->description;
        }
    }
}
