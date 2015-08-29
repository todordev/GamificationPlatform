<?php
/**
 * @package         Gamification
 * @subpackage      Ranks
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\Rank;

use Prism\Database\Table;
use Gamification\Mechanic\PointsInterface;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods used for managing a rank.
 *
 * @package         Gamification
 * @subpackage      Ranks
 */
class Rank extends Table implements PointsInterface
{
    /**
     * Rank ID.
     *
     * @var integer
     */
    protected $id;

    protected $title;
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
     * $rankId = 1;
     * $rank   = Gamification\Rank\Rank::getInstance(\JFactory::getDbo(), $rankId);
     * </code>
     *
     * @param \JDatabaseDriver $db
     * @param int $id
     *
     * @return null|Rank
     */
    public static function getInstance(\JDatabaseDriver $db, $id)
    {
        if (empty(self::$instances[$id])) {
            $item   = new Rank($db);
            $item->load($id);
            
            self::$instances[$id] = $item;
        }

        return self::$instances[$id];
    }

    /**
     * Get rank title.
     *
     * <code>
     * $rankId = 1;
     * $rank   = new Gamification\Rank\Rank(\JFactory::getDbo());
     * $rank->load($rankId);
     * 
     * $title  = $rank->getTitle();
     * </code>
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get points.
     *
     * <code>
     * $rankId    = 1;
     *
     * $rank      = new Gamification\Rank\Rank(\JFactory::getDbo());
     * $rank->load($rankId);
     *
     * $points     = $rank->getPoints();
     * </code>
     *
     * @return number
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * Get the rank where the level is positioned.
     *
     * <code>
     * $rankId    = 1;
     *
     * $rank      = new Gamification\Rank\Rank(\JFactory::getDbo());
     * $rank->load($rankId);
     *
     * $pointsId    = $rank->getPointsId();
     * </code>
     *
     * @return int
     */
    public function getPointsId()
    {
        return $this->points_id;
    }

    /**
     * Get rank image.
     *
     * <code>
     * $rankId = 1;
     * $rank   = new Gamification\Rank\Rank(\JFactory::getDbo());
     * $rank->load($rankId);
     *
     * $image  = $rank->getImage();
     * </code>
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Load rank data.
     *
     * <code>
     * $keys    = array(
     *    "group_id" => 1,
     *    "points_id"  => 2,
     * );
     *
     * $rank   = new Gamification\Rank\Rank(\JFactory::getDbo());
     * $rank->load($keys);
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
            ->select("a.id, a.title, a.points, a.image, a.note, a.published, a.points_id, a.group_id")
            ->from($this->db->quoteName("#__gfy_ranks", "a"));

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
     *        "image"    => "....",
     *        "note"    => "....",
     *        "published" => 1,
     *        "points_id" => 2,
     *        "group_id"  => 4
     * );
     *
     * $rank   = new Gamification\Rank\Rank(\JFactory::getDbo());
     * $rank->bind($data);
     * $rank->store();
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

        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->update($this->db->quoteName("#__gfy_ranks"))
            ->set($this->db->quoteName("title") . "  = " . $this->db->quote($this->title))
            ->set($this->db->quoteName("points") . "  = " . $this->db->quote($this->points))
            ->set($this->db->quoteName("image") . "  = " . $this->db->quote($this->image))
            ->set($this->db->quoteName("note") . "  = " . $note)
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
            ->insert($this->db->quoteName("#__gfy_ranks"))
            ->set($this->db->quoteName("title") . "  = " . $this->db->quote($this->title))
            ->set($this->db->quoteName("points") . "  = " . $this->db->quote($this->points))
            ->set($this->db->quoteName("image") . "  = " . $this->db->quote($this->image))
            ->set($this->db->quoteName("published") . "  = " . (int)$this->published)
            ->set($this->db->quoteName("points_id") . "  = " . (int)$this->points_id)
            ->set($this->db->quoteName("group_id") . "  = " . (int)$this->group_id);

        if (!empty($this->note)) {
            $query->set($this->db->quoteName("note") . "  = " . $this->db->quote($this->note));
        }

        $this->db->setQuery($query);
        $this->db->execute();

        return $this->db->insertid();
    }
}
