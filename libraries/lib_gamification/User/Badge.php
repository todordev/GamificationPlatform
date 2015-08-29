<?php
/**
 * @package         Gamification\User
 * @subpackage      Badges
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
 * This is an object that represents user badge.
 *
 * @package         Gamification\User
 * @subpackage      Badges
 */
class Badge extends TableObservable implements PointsInterface
{
    /**
     * The ID of database record in table "#__gfy_userbadges".
     *
     * @var integer
     */
    protected $id;

    /**
     * This is the ID of the badge record in table "#__gfy_badges".
     *
     * @var integer
     */
    protected $badge_id;

    protected $user_id;
    protected $group_id;

    protected $description;

    protected $title;

    /**
     * This is the number of points needed to be reached this badge.
     * @var integer
     */
    protected $points;

    protected $image;
    protected $published;
    protected $points_id;

    protected static $instances = array();

    /**
     * Create an object and load user badge.
     *
     * <code>
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     * $userBadge    = Gamification\User\Badge::getInstance(\JFactory::getDbo(), $keys);
     * </code>
     *
     * @param  \JDatabaseDriver $db
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
            $item   = new Badge($db);
            $item->load($keys, $options);

            self::$instances[$index] = $item;
        }

        return self::$instances[$index];
    }

    /**
     * Load user badge data.
     *
     * <code>
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $userBadge     = new Gamification\User\Badge(\JFactory::getDbo());
     * $userBadge->load($keys);
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
            ->select("a.id, a.badge_id, a.user_id, a.group_id")
            ->select("b.title, b.description, b.points, b.image, b.published, b.points_id")
            ->from($this->db->quoteName("#__gfy_userbadges", "a"))
            ->leftJoin($this->db->quoteName("#__gfy_badges", "b") . ' ON a.badge_id = b.id');

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

    protected function updateObject()
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->update($this->db->quoteName("#__gfy_userbadges"))
            ->set($this->db->quoteName("user_id")  . "=" . (int)$this->user_id)
            ->set($this->db->quoteName("group_id") . "=" . (int)$this->group_id)
            ->set($this->db->quoteName("badge_id") . "=" . (int)$this->badge_id)
            ->where($this->db->quoteName("id") . "=" . (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();
    }

    protected function insertObject()
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->insert($this->db->quoteName("#__gfy_userbadges"))
            ->set($this->db->quoteName("user_id")  . "=" . (int)$this->user_id)
            ->set($this->db->quoteName("group_id") . "=" . (int)$this->group_id)
            ->set($this->db->quoteName("badge_id") . "=" . (int)$this->badge_id);

        $this->db->setQuery($query);
        $this->db->execute();

        return $this->db->insertid();
    }

    /**
     * Save the data to the database.
     *
     * <code>
     * $data = array(
     *        "user_id"   => 3,
     *        "group_id"  => 4,
     *        "badge_id"  => 2
     * );
     *
     * $userBadge   = new Gamification\User\Badge(\JFactory::getDbo());
     * $userBadge->bind($data);
     * $userBadge->store();
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
     * Return the ID of the record.
     *
     * <code>
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $userBadge   = new Gamification\User\Badge(\JFactory::getDbo());
     * $userBadge->load($keys);
     *
     * if (!$userBadge->getId()) {
     * ....
     * }
     * </code>
     *
     * @return string
     */
    public function getId()
    {
        return (int)$this->id;
    }

    /**
     * Return the title of the badge.
     *
     * <code>
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $userBadge   = new Gamification\User\Badge(\JFactory::getDbo());
     * $userBadge->load($keys);
     * 
     * $title  = $userBadge->getTitle();
     * </code>
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get badge points.
     *
     * <code>
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $userBadge      = new Gamification\User\Badge(\JFactory::getDbo());
     * $userBadge->load($keys);
     *
     * $points     = $userBadge->getPoints();
     * </code>
     *
     * @return number
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * Get the points ID used for the badge.
     *
     * <code>
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $userBadge  = new Gamification\User\Badge(\JFactory::getDbo());
     * $userBadge->load($keys);
     *
     * $pointsId   = $userBadge->getPointsId();
     * </code>
     *
     * @return integer
     */
    public function getPointsId()
    {
        return $this->points_id;
    }

    /**
     * Return badge image.
     *
     * <code>
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $userBadge   = new Gamification\User\Badge(\JFactory::getDbo());
     * $userBadge->load($keys);
     * 
     * $image       = $userBadge->getImage();
     * </code>
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Return badge description with possibility
     * to replace placeholders with dynamically generated data.
     *
     * <code>
     * $badgeId    = 1;
     * $badge      = new Gamification\Badge\User\Badge(\JFactory::getDbo());
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

    /**
     * Set user Id for the user badge.
     *
     * <code>
     * $userBadge   = new Gamification\User\Badge(\JFactory::getDbo());
     *
     * $userId        = 1;
     * $userBadge->setUserId($userId);
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
     * Set group ID for the user badge.
     *
     * <code>
     * $userBadge   = new Gamification\User\Badge(\JFactory::getDbo());
     *
     * $groupId        = 1;
     * $userBadge->setGroupId($groupId);
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
     * Set badge ID for the user badge.
     *
     * <code>
     * $userBadge   = new Gamification\User\Badge(\JFactory::getDbo());
     *
     * $badgeId        = 1;
     * $userBadge->setBadgeId($badgeId);
     * </code>
     *
     * @param int $badgeId
     *
     * @return self
     */
    public function setBadgeId($badgeId)
    {
        $this->badge_id = (int)$badgeId;

        return $this;
    }
}
