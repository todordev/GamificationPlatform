<?php
/**
 * @package         Gamification
 * @subpackage      Badges
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\Badge;

use Prism\Database\Table;
use Gamification\Mechanic;
use Joomla\String\String;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing a badge.
 *
 * @package         Gamification
 * @subpackage      Badges
 */
class Badge extends Table implements Mechanic\PointsInterface
{
    /**
     * Badge ID.
     *
     * @var integer
     */
    protected $id;

    protected $title;
    protected $description;
    protected $points;
    protected $image;
    protected $note;
    protected $published;
    protected $points_id;
    protected $group_id;

    protected static $instances = array();

    /**
     * Create an instance of the object and load data.
     *
     * <code>
     * $badgeId = 1;
     * $badge   = Gamification\Badge\Badge::getInstance(\JFactory::getDbo(), $badgeId);
     * </code>
     *
     * @param \JDatabaseDriver $db
     * @param int $id
     *
     * @return null|self
     */
    public static function getInstance($db, $id)
    {
        if (!isset(self::$instances[$id])) {
            $item   = new Badge($db);
            $item->load($id);
            
            self::$instances[$id] = $item;
        }

        return self::$instances[$id];
    }

    /**
     * Get badge title.
     *
     * <code>
     * $badgeId    = 1;
     * $badge      = new Gamification\Badge\Badge(\JFactory::getDbo());
     * $title      = $badge->getTitle();
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
     * $badgeId    = 1;
     * $badge      = new Gamification\Badge\Badge(\JFactory::getDbo());
     * $badge->load($badgeId);
     *
     * $points     = $badge->getPoints();
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
     * $badgeId    = 1;
     *
     * $badge      = new Gamification\Badge\Badge(\JFactory::getDbo());
     * $badge->load($badgeId);
     *
     * $pointsId   = $badge->getPointsId();
     * </code>
     *
     * @return int
     */
    public function getPointsId()
    {
        return $this->points_id;
    }

    /**
     * Get badge image.
     *
     * <code>
     * $badgeId    = 1;
     * $badge      = new Gamification\Badge\Badge(\JFactory::getDbo());
     * $image      = $badge->getImage();
     * </code>
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Get badge note.
     *
     * <code>
     * $badgeId    = 1;
     * $badge      = new Gamification\Badge\Badge(\JFactory::getDbo());
     * $note       = $badge->getNote();
     * </code>
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
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

    /**
     * Check for published badge.
     *
     * <code>
     * $badgeId     = 1;
     * $badge       = new Gamification\Badge\Badge(\JFactory::getDbo());
     *
     * if(!$badge->isPublished()) {
     * ...
     * }
     * </code>
     *
     * @return boolean
     */
    public function isPublished()
    {
        return (!$this->published) ? false : true;
    }

    /**
     * Get the group ID of the badge.
     *
     * <code>
     * $badgeId    = 1;
     *
     * $badge      = new Gamification\Badge\Badge(\JFactory::getDbo());
     * $badge->load($badgeId);
     *
     * $groupId    = $badge->getGroupId();
     * </code>
     *
     * @return integer
     */
    public function getGroupId()
    {
        return $this->group_id;
    }

    /**
     * Load badge data from database.
     *
     * <code>
     * $keys = array(
     *    "group_id" => 1,
     *    "points_id" => 2
     * );
     *
     * $badge      = new Gamification\Badge\Badge(\JFactory::getDbo());
     * $badge->load($keys);
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
            ->select("a.id, a.title, a.description, a.points, a.image, a.note, a.published, a.points_id, a.group_id")
            ->from($this->db->quoteName("#__gfy_badges", "a"));

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
     * Set the data to the object parameters.
     *
     * <code>
     * $data = array(
     *        "title"    => "......",
     *        "description"    => "......",
     *        "points"    => 100,
     *        "image"    => "picture.png",
     *        "note"    => "......",
     *        "published" => 1,
     *        "points_id" => 2,
     *        "group_id"  => 3
     * );
     *
     * $badge   = new Gamification\Badge\Badge(\JFactory::getDbo());
     * $badge->bind($data);
     * </code>
     *
     * @param array $data
     * @param array $ignored
     */
    public function bind($data, $ignored = array())
    {
        foreach ($data as $key => $value) {
            if (!in_array($key, $ignored)) {
                $this->$key = $value;
            }
        }
    }

    /**
     * Save the data to the database.
     *
     * <code>
     * $data = array(
     *        "title"    => "......",
     *        "description"    => "......",
     *        "points"    => 100,
     *        "image"    => "picture.png",
     *        "note"    => null,
     *        "published" => 1,
     *        "points_id" => 2,
     *        "group_id"  => 3
     * );
     *
     * $badge   = new Gamification\Badge\Badge(\JFactory::getDbo());
     * $badge->bind($data);
     * $badge->store();
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
        $note = (!$this->note) ? null : $this->db->quote($this->note);
        $description = (!$this->description) ? null : $this->db->quote($this->description);

        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->update($this->db->quoteName("#__gfy_badges"))
            ->set($this->db->quoteName("title") . "  = " . $this->db->quote($this->title))
            ->set($this->db->quoteName("points") . "  = " . $this->db->quote($this->points))
            ->set($this->db->quoteName("image") . "  = " . $this->db->quote($this->image))
            ->set($this->db->quoteName("note") . "  = " . $note)
            ->set($this->db->quoteName("description") . "  = " . $description)
            ->set($this->db->quoteName("published") . "  = " . (int)$this->published)
            ->set($this->db->quoteName("points_id") . "  = " . (int)$this->points_id)
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
            ->insert($this->db->quoteName("#__gfy_badges"))
            ->set($this->db->quoteName("title") . "  = " . $this->db->quote($this->title))
            ->set($this->db->quoteName("points") . "  = " . $this->db->quote($this->points))
            ->set($this->db->quoteName("image") . "  = " . $this->db->quote($this->image))
            ->set($this->db->quoteName("published") . "  = " . (int)$this->published)
            ->set($this->db->quoteName("points_id") . "  = " . (int)$this->points_id)
            ->set($this->db->quoteName("group_id") . "  = " . (int)$this->group_id);

        if (!empty($this->note)) {
            $query->set($this->db->quoteName("note") . " = " . $this->db->quote($this->note));
        }

        if (!empty($this->description)) {
            $query->set($this->db->quoteName("description") . " = " . $this->db->quote($this->description));
        }

        $this->db->setQuery($query);
        $this->db->execute();

        return $this->db->insertid();
    }
}
