<?php
/**
 * @package         Gamification
 * @subpackage      Points
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\Points;

use Joomla\Utilities\ArrayHelper;
use Prism\Database\Table;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing kind of points.
 *
 * @package         Gamification
 * @subpackage      Points
 */
class Points extends Table
{
    protected $id;
    protected $title;
    protected $abbr;
    protected $note;
    protected $published;
    protected $group_id;

    protected static $instances = array();

    /**
     * Create an instance of the object and load data.
     *
     * <code>
     * // create object points by ID
     * $pointsId   = 1;
     * $points     = Gamification\Points\Points::getInstance(\JFactory::getDbo(), $pointsId);
     *
     * // create object points by abbreviation
     * $keys = array(
     *    "abbr" => "P"
     * );
     * $points     = Gamification\Points\Points::getInstance(\JFactory::getDbo(), $keys);
     * </code>
     *
     * @param \JDatabaseDriver $db
     * @param int|array $keys
     *
     * @return null|self
     */
    public static function getInstance($db, $keys)
    {
        if (is_array($keys)) {
            $index = ArrayHelper::getValue($keys, "abbr");
        } else {
            $index = (int)$keys;
        }

        $index = \JApplicationHelper::getHash($index);

        if (!isset(self::$instances[$index])) {
            $item   = new Points($db);
            $item->load($keys);
            
            self::$instances[$index] = $item;
        }

        return self::$instances[$index];
    }

    /**
     * Load points data using the table object.
     *
     * <code>
     * $keys = array(
     *    "group_id" => 1,
     *    "published" => Prism\Constants::PUBLISHED
     * );
     *
     * $points     = new Gamification\Points\Points(\JFactory::getDbo());
     * $points->load($pointsId);
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
            ->select("a.id, a.title, a.abbr, a.note, a.published, a.group_id")
            ->from($this->db->quoteName("#__gfy_points", "a"));

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
     *        "title"    => "Points",
     *        "abbr"    => "P",
     *        "published" => 1,
     *        "group_id"  => 4
     * );
     *
     * $points   = new Gamification\Points\Points(\JFactory::getDbo());
     * $points->bind($data);
     *
     * $points->store();
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
            ->update($this->db->quoteName("#__gfy_points"))
            ->set($this->db->quoteName("title") . "  = " . $this->db->quote($this->title))
            ->set($this->db->quoteName("abbr") . "  = " . $this->db->quote($this->abbr))
            ->set($this->db->quoteName("note") . "  = " . $note)
            ->set($this->db->quoteName("published") . "  = " . (int)$this->published)
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
            ->insert($this->db->quoteName("#__gfy_points"))
            ->set($this->db->quoteName("title") . "  = " . $this->db->quote($this->title))
            ->set($this->db->quoteName("abbr") . "  = " . $this->db->quote($this->abbr))
            ->set($this->db->quoteName("published") . "  = " . (int)$this->published)
            ->set($this->db->quoteName("group_id") . "  = " . (int)$this->group_id);

        if (!empty($this->note)) {
            $query->set($this->db->quoteName("note") . "  = " . $this->db->quote($this->note));
        }
        
        $this->db->setQuery($query);
        $this->db->execute();

        return $this->db->insertid();
    }

    /**
     * Return points ID.
     *
     * <code>
     * // create object points by abbreviation.
     * $keys = array(
     *    "abbr" => "P"
     * );
     * 
     * $points     = new Gamification\Points\Points(\JFactory::getDbo());
     * $points->load($keys);
     * 
     * if (!$points->getId()) {
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
     * Return points title.
     *
     * <code>
     * // create object points by abbreviation.
     * $keys = array(
     *    "abbr" => "P"
     * );
     *
     * $points  = new Gamification\Points\Points(\JFactory::getDbo());
     * $points->load($keys);
     *
     * $title   = $points->getTitle();
     * </code>
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Return abbreviation.
     *
     * <code>
     * // create object points by ID.
     * $pointsId = 1;
     *
     * $points  = new Gamification\Points\Points(\JFactory::getDbo());
     * $points->load($pointsId);
     *
     * $abbr   = $points->getAbbr();
     * </code>
     *
     * @return string
     */
    public function getAbbr()
    {
        return $this->abbr;
    }

    /**
     * Return note.
     *
     * <code>
     * // create object points by ID.
     * $pointsId = 1;
     *
     * $points  = new Gamification\Points\Points(\JFactory::getDbo());
     * $points->load($pointsId);
     *
     * $note   = $points->getNote();
     * </code>
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Return group ID.
     *
     * <code>
     * // create object points by ID.
     * $pointsId = 1;
     *
     * $points  = new Gamification\Points\Points(\JFactory::getDbo());
     * $points->load($pointsId);
     *
     * $groupId   = $points->getGroupId();
     * </code>
     *
     * @return int
     */
    public function getGroupId()
    {
        return $this->group_id;
    }

    /**
     * Check for published item.
     *
     * <code>
     * $pointsId    = 1;
     * 
     * $points      = new Gamification\Points\Points(\JFactory::getDbo());
     * $points->load($pointsId);
     * 
     * if(!$points->isPublished()) {
     * .....
     * }
     * </code>
     *
     * @return boolean
     */
    public function isPublished()
    {
        return (!$this->published) ? false : true;
    }
}
