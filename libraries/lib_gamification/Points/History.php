<?php
/**
 * @package         Gamification
 * @subpackage      Points
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\Points;

use Prism\Database\Table;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for monitoring distributed points.
 *
 * @package         Gamification
 * @subpackage      Points
 */
class History extends Table
{
    protected $id;
    protected $user_id;
    protected $points_id;
    protected $points;
    protected $hash;
    protected $record_date;

    /**
     * Load points data using the table object.
     *
     * <code>
     * $keys = array(
     *    "id"        => 1,
     *    "user_id"   => 2,
     *    "hash"      => md5($ip . $userId . $itemId)
     * );
     *
     * $pointsHistory     = new Gamification\Points\History(\JFactory::getDbo());
     * $pointsHistory->load($pointsId);
     * </code>
     *
     * @param int|array $keys
     * @param array $options
     */
    public function load($keys, $options = array())
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->select("a.id, a.user_id, a.points_id, a.points, a.hash, a.record_date")
            ->from($this->db->quoteName("#__gfy_points_history", "a"));

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

        if (!empty($result)) {
            $this->bind($result);
        }
    }

    /**
     * Save the data to the database.
     *
     * <code>
     * $pointsId = 1;
     * $data = array(
     *        "user_id"   => "Points",
     *        "points_id" => $pointsId,
     *        "points"    => 100,
     *        "hash"      => md5($ip . $userId . $pointsId . $itemId)
     * );
     *
     * $pointsHistory   = new Gamification\Points\History(\JFactory::getDbo());
     * $pointsHistory->bind($data);
     *
     * $pointsHistory->store();
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
            ->update($this->db->quoteName("#__gfy_points_history"))
            ->set($this->db->quoteName("user_id") . "  = " . (int)$this->user_id)
            ->set($this->db->quoteName("points_id") . "  = " . (int)$this->points_id)
            ->set($this->db->quoteName("points") . "  = " . (int)$this->points)
            ->set($this->db->quoteName("hash") . "  = " . $this->db->quote($this->hash))
            ->set($this->db->quoteName("record_date") . "  = " . $this->db->quote($this->record_date))
            ->where($this->db->quoteName("id") . "  = " . (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();
    }

    protected function insertObject()
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->insert($this->db->quoteName("#__gfy_points_history"))
            ->set($this->db->quoteName("user_id") . "  = " . (int)$this->user_id)
            ->set($this->db->quoteName("points_id") . "  = " . (int)$this->points_id)
            ->set($this->db->quoteName("points") . "  = " . (int)$this->points)
            ->set($this->db->quoteName("hash") . "  = " . $this->db->quote($this->hash));

        $this->db->setQuery($query);
        $this->db->execute();

        return $this->db->insertid();
    }

    /**
     * Return points history ID.
     *
     * <code>
     * $id = 1;
     * 
     * $pointsHistory     = new Gamification\Points\History(\JFactory::getDbo());
     * $pointsHistory->load($id);
     * 
     * if (!$pointsHistory->getId()) {
     * ....
     * }
     * </code>
     *
     * @return null|integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Return user ID.
     *
     * <code>
     * $keys = array(
     *    "hash" => md5($ip . $userId . $itemId)
     * );
     *
     * $pointsHistory  = new Gamification\Points\History(\JFactory::getDbo());
     * $pointsHistory->load($keys);
     *
     * $userId   = $pointsHistory->getUserId();
     * </code>
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->user_id;
    }

    /**
     * Return the number of points given to the user.
     *
     * <code>
     * $keys = array(
     *    "hash" => md5($ip . $userId . $itemId)
     * );
     *
     * $pointsHistory  = new Gamification\Points\History(\JFactory::getDbo());
     * $pointsHistory->load($keys);
     *
     * $points  = $pointsHistory->getPoints();
     * </code>
     *
     * @return int
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * Return the unique string used to identify the connection between user and objects.
     *
     * <code>
     * $keys = array(
     *    "hash" => md5($ip . $userId . $itemId)
     * );
     *
     * $pointsHistory  = new Gamification\Points\History(\JFactory::getDbo());
     * $pointsHistory->load($keys);
     *
     * echo $pointsHistory->getHash();
     * </code>
     *
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }
    /**
     * Return the date when the record has been created.
     *
     * <code>
     * $keys = array(
     *    "hash" => md5($ip . $userId . $itemId)
     * );
     *
     * $pointsHistory  = new Gamification\Points\History(\JFactory::getDbo());
     * $pointsHistory->load($keys);
     *
     * echo $pointsHistory->getRecordDate();
     * </code>
     *
     * @return string
     */
    public function getRecordDate()
    {
        return $this->record_date;
    }

    /**
     * Check for existing records in the database.
     *
     * <code>
     * $keys = array(
     *    "hash" => md5($ip . $userId . $itemId)
     * );
     *
     * $pointsHistory     = new Gamification\Points\Points(\JFactory::getDbo());
     * if ($pointsHistory->isExists($keys)) {
     * .....
     * }
     * </code>
     *
     * @param int|array $keys
     *
     * @return bool
     */
    public function isExists($keys)
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->select("COUNT(*)")
            ->from($this->db->quoteName("#__gfy_points_history", "a"));

        // Prepare keys.
        if (is_array($keys)) {
            foreach ($keys as $column => $value) {
                $query->where($this->db->quoteName("a.".$column) . " = " . $this->db->quote($value));
            }
        } else {
            $query->where("a.id = " . (int)$keys);
        }

        $this->db->setQuery($query, 0, 1);

        return (bool)$this->db->loadResult();
    }

    /**
     * Set user ID.
     *
     * @param int $userId
     */
    public function setUserId($userId)
    {
        $this->user_id = $userId;
    }

    /**
     * Set the number of points given to the user.
     *
     * @param int $points
     */
    public function setPoints($points)
    {
        $this->points = $points;
    }

    /**
     * Set the ID of points given to the user.
     *
     * @param int $pointsId
     */
    public function setPointsId($pointsId)
    {
        $this->points_id = $pointsId;
    }

    /**
     * Set the hash, generated to provide connection between the object and the user.
     *
     * @param string $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }
}
