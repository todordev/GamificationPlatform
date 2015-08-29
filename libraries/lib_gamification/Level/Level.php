<?php
/**
 * @package         Gamification
 * @subpackage      Levels
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\Level;

use Prism\Database\Table;
use Gamification\Mechanic;
use Gamification\Rank\Rank;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing a level.
 *
 * @package         Gamification
 * @subpackage      Levels
 */
class Level extends Table implements Mechanic\PointsInterface
{
    /**
     * Badge ID.
     *
     * @var integer
     */
    protected $id;

    protected $title;
    protected $points;
    protected $value;
    protected $published;
    protected $points_id;
    protected $rank_id;
    protected $group_id;

    protected $rank;

    protected static $instances = array();

    /**
     * Create an instance of the object and load data.
     *
     * <code>
     * $levelId = 1;
     * $level   = Gamification\Level\Level::getInstance(\JFactory::getDbo(), $levelId);
     * </code>
     *
     * @param \JDatabaseDriver $db
     * @param int $id
     *
     * @return null|Level
     */
    public static function getInstance(\JDatabaseDriver $db, $id)
    {
        if (empty(self::$instances[$id])) {
            $item   = new Level($db);
            $item->load($id);
            
            self::$instances[$id] = $item;
        }

        return self::$instances[$id];
    }

    /**
     * Get the rank where the level is positioned.
     *
     * <code>
     * $levelId = 1;
     * $level   = new Gamification\Level\Level(\JFactory::getDbo());
     *
     * $rank    = $level->getRank();
     * </code>
     *
     * @return null|Rank
     */
    public function getRank()
    {
        if (!$this->rank_id) {
            return null;
        }

        if (!$this->rank) {
            $this->rank = Rank::getInstance($this->db, $this->rank_id);
        }

        return $this->rank;
    }

    /**
     * Get points.
     *
     * <code>
     * $levelId    = 1;
     *
     * $level      = new Gamification\Level\Level(\JFactory::getDbo());
     * $level->load($levelId);
     *
     * $points     = $level->getTitle();
     * </code>
     *
     * @return number
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get points.
     *
     * <code>
     * $levelId    = 1;
     *
     * $level      = new Gamification\Level\Level(\JFactory::getDbo());
     * $level->load($levelId);
     *
     * $points     = $level->getPoints();
     * </code>
     *
     * @return number
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * Get points ID used for the level.
     *
     * <code>
     * $levelId = 1;
     *
     * $level   = new Gamification\Level\Level(\JFactory::getDbo());
     * $level->load($levelId);
     *
     * $pointsId    = $level->getPointsId();
     * </code>
     *
     * @return int
     */
    public function getPointsId()
    {
        return $this->points_id;
    }

    /**
     * Load level data using the table object.
     *
     * <code>
     * $keys    = array(
     *    "group_id" => 1,
     *    "rank_id"  => 2,
     * );
     *
     * $level      = new Gamification\Level\Level(\JFactory::getDbo());
     * $level->load($keys);
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
            ->select("a.id, a.title, a.points, a.value, a.published, a.points_id, a.rank_id, a.group_id")
            ->from($this->db->quoteName("#__gfy_levels", "a"));

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
     * $data = array(
     *        "title"    => "......",
     *        "points"    => 100,
     *        "value"    => 1,
     *        "published" => 1,
     *        "points_id" => 2,
     *        "rank_id"   => 3,
     *        "group_id"  => 4
     * );
     *
     * $level   = new Gamification\Level\Level(\JFactory::getDbo());
     * $level->bind($data);
     *
     * $level->store();
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
            ->update($this->db->quoteName("#__gfy_levels"))
            ->set($this->db->quoteName("title") . "  = " . $this->db->quote($this->title))
            ->set($this->db->quoteName("points") . "  = " . $this->db->quote($this->points))
            ->set($this->db->quoteName("value") . "  = " . $this->db->quote($this->value))
            ->set($this->db->quoteName("published") . "  = " . (int)$this->published)
            ->set($this->db->quoteName("points_id") . "  = " . (int)$this->points_id)
            ->set($this->db->quoteName("rank_id") . "  = " . (int)$this->rank_id)
            ->set($this->db->quoteName("group_id") . "  = " . (int)$this->group_id)
            ->where($this->db->quoteName("id") . "  = " . (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();
    }

    protected function insertObject()
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->insert($this->db->quoteName("#__gfy_levels"))
            ->set($this->db->quoteName("title") . "  = " . $this->db->quote($this->title))
            ->set($this->db->quoteName("points") . "  = " . $this->db->quote($this->points))
            ->set($this->db->quoteName("value") . "  = " . $this->db->quote($this->value))
            ->set($this->db->quoteName("published") . "  = " . (int)$this->published)
            ->set($this->db->quoteName("points_id") . "  = " . (int)$this->points_id)
            ->set($this->db->quoteName("rank_id") . "  = " . (int)$this->rank_id)
            ->set($this->db->quoteName("group_id") . "  = " . (int)$this->group_id);

        $this->db->setQuery($query);
        $this->db->execute();

        return $this->db->insertid();
    }
}
