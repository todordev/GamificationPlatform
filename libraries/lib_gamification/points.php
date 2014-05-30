<?php
/**
 * @package         GamificationPlatform
 * @subpackage      GamificationLibrary
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

JLoader::register("GamificationTablePoint", JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_gamification" . DIRECTORY_SEPARATOR . "tables" . DIRECTORY_SEPARATOR . "point.php");
JLoader::register("GamificationInterfaceTable", JPATH_LIBRARIES . DIRECTORY_SEPARATOR . "gamification" . DIRECTORY_SEPARATOR . "interface" . DIRECTORY_SEPARATOR . "table.php");

/**
 * This class contains methods that are used for managing points.
 *
 * @package         GamificationPlatform
 * @subpackage      GamificationLibrary
 */
class GamificationPoints implements GamificationInterfaceTable
{
    protected $table;
    protected static $instances = array();

    /**
     * Initialize the object and load data.
     *
     * <code>
     *
     * // create object points by ID
     * $pointsId   = 1;
     * $points     = new GamificationPoints($pointsId);
     *
     * // create object points by abbreviation
     * $pointsAbbr = "P";
     * $points     = new GamificationPoints($pointsAbbr);
     *
     * </code>
     *
     * @param int|string $id
     *
     */
    public function __construct($id = 0)
    {
        $this->table = new GamificationTablePoint(JFactory::getDbo());

        if (!empty($id)) {
            $this->table->load($id);
        }
    }

    /**
     * Create an instance of the object and load data.
     *
     * <code>
     *
     * // create object points by ID
     * $pointsId   = 1;
     * $points     = GamificationPoints::getInstance($pointsId);
     *
     * // create object points by abbreviation
     * $pointsAbbr = "P";
     * $points     = GamificationPoints::getInstance($pointsAbbr);
     *
     * </code>
     *
     * @param int|string $id
     *
     * @return null|GamificationPoints
     */
    public static function getInstance($id = 0)
    {
        if (!is_numeric($id)) {

            $keys = array(
                "abbr" => $id
            );

        } else {
            $keys = $id;
        }

        $index = JApplicationHelper::getHash($id);

        if (empty(self::$instances[$index])) {
            $item                    = new GamificationPoints($keys);
            self::$instances[$index] = $item;
        }

        return self::$instances[$index];
    }

    /**
     * Load points data using the table object.
     *
     * <code>
     *
     * $pointsId   = 1;
     * $points     = new GamificationPoints();
     * $points->load($pointsId);
     *
     * </code>
     *
     * @param $keys
     * @param $reset
     *
     */
    public function load($keys, $reset = true)
    {
        $this->table->load($keys, $reset);
    }

    /**
     * Set the data to the object parameters.
     *
     * <code>
     *
     * $data = array(
     *        "title"    => "Points",
     *        "abbr"    => "P",
     *        "published" => 1,
     *        "group_id"  => 4
     * );
     *
     * $points   = new GamificationPoints();
     * $points->bind($data);
     *
     * </code>
     *
     * @param array $src
     * @param array $ignore
     */
    public function bind($src, $ignore = array())
    {
        $this->table->bind($src, $ignore);
    }

    /**
     * Save the data to the database.
     *
     * <code>
     *
     * $data = array(
     *        "title"    => "Points",
     *        "abbr"    => "P",
     *        "published" => 1,
     *        "group_id"  => 4
     * );
     *
     * $points   = new GamificationPoints();
     * $points->bind($data);
     * $points->store(true);
     *
     * </code>
     *
     * @param $updateNulls
     *
     */
    public function store($updateNulls = false)
    {
        $this->table->store($updateNulls);
    }

    /**
     * Return points ID.
     *
     * <code>
     *
     * // create object points by abbreviation
     * $abbr       = "P";
     * $points     = GamificationPoints::getInstance($abbr);
     *
     * $pointsId   = $points->getId();
     *
     * </code>
     *
     * @return null|integer
     */
    public function getId()
    {
        return $this->table->id;
    }

    /**
     * Return points title.
     *
     * <code>
     * $points     = GamificationPoints::getInstance($abbr);
     *
     * $title   = $points->getTitle();
     *
     * </code>
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->table->title;
    }

    /**
     * Check for published item.
     *
     * <code>
     *
     * $pointsId    = 1;
     * $points      = GamificationPoints::getInstance($pointsId);
     *
     * if(!$points->isPublished()) {
     *    // .....
     * }
     *
     * </code>
     *
     * @return boolean
     */
    public function isPublished()
    {
        return (!$this->table->published) ? false : true;
    }
}
