<?php
/**
 * @package         GamificationPlatform
 * @subpackage      GamificationLibrary
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

JLoader::register("GamificationTableLevel", JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR . "com_gamification" . DIRECTORY_SEPARATOR . "tables" . DIRECTORY_SEPARATOR . "level.php");
JLoader::register("GamificationInterfaceTable", JPATH_LIBRARIES . DIRECTORY_SEPARATOR . "gamification" . DIRECTORY_SEPARATOR . "interface" . DIRECTORY_SEPARATOR . "table.php");

/**
 * This class contains methods that are used for managing a level.
 *
 * @package         GamificationPlatform
 * @subpackage      GamificationLibrary
 */
class GamificationLevel implements GamificationInterfaceTable
{
    protected $table;

    protected $rank;

    protected static $instances = array();

    /**
     * Initialize the object and load data.
     *
     * <code>
     *
     * $levelId = 1;
     * $level   = new GamificationLevel($levelId);
     *
     * </code>
     *
     * @param int $id
     */
    public function __construct($id = 0)
    {

        $this->table = new GamificationTableLevel(JFactory::getDbo());

        if (!empty($id)) {
            $this->table->load($id);
        }

    }

    /**
     * Create an instance of the object and load data.
     *
     * <code>
     *
     * $levelId = 1;
     * $level   = GamificationLevel::getInstance($levelId);
     *
     * </code>
     *
     * @param int $id
     *
     * @return null|GamificationLevel
     */
    public static function getInstance($id = 0)
    {
        if (empty(self::$instances[$id])) {
            $item                 = new GamificationLevel($id);
            self::$instances[$id] = $item;
        }

        return self::$instances[$id];
    }

    /**
     * Get the rank where the level is positioned.
     *
     * <code>
     *
     * $levelId = 1;
     * $level   = GamificationLevel::getInstance($levelId);
     *
     * $rank    = $level->getRank();
     *
     * </code>
     *
     * @return null|GamificationRank
     */
    public function getRank()
    {
        if (!$this->rank_id) {
            return null;
        }

        if (!$this->rank) {
            jimport("gamification.rank");
            $this->rank = GamificationRank::getInstance($this->rank_id);
        }

        return $this->rank;
    }

    /**
     * Load level data using the table object.
     *
     * <code>
     *
     * $levelId    = 1;
     * $level      = new GamificationLevel();
     * $level->load($levelId);
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
     *        "title"    => "......",
     *        "points"    => 100,
     *        "value"    => 1,
     *        "published" => 1,
     *        "points_id" => 2,
     *        "rank_id"   => 3,
     *        "group_id"  => 4
     * );
     *
     * $level   = new GamificationLevel();
     * $level->bind($data);
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
     *        "title"    => "......",
     *        "points"    => 100,
     *        "value"    => 1,
     *        "published" => 1,
     *        "points_id" => 2,
     *        "rank_id"   => 3,
     *        "group_id"  => 4
     * );
     *
     * $level   = new GamificationLevel();
     * $level->bind($data);
     * $level->store(true);
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
}
