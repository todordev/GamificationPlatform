<?php
/**
 * @package         GamificationPlatform
 * @subpackage      GamificationLibrary
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

jimport('gamification.interface.usermechanic');

/**
 * This is an object that represents user rank.
 *
 * @package         GamificationPlatform
 * @subpackage      GamificationLibrary
 */
class GamificationUserRank implements GamificationInterfaceUserMechanic
{
    /**
     * The ID of database record in table "#__gfy_userranks".
     *
     * @var integer
     */
    public $id;

    public $title;

    /**
     * This is the number of points needed to be reached this rank.
     * @var integer
     */
    public $points;

    public $image;
    public $published;

    /**
     * This is the ID of the rank record in table "#__gfy_ranks".
     *
     * @var integer
     */
    public $rank_id;

    public $group_id;
    public $user_id;

    public $points_id;

    /**
     * Database driver
     * @var JDatabaseMySQLi
     */
    protected $db;

    protected static $instances = array();

    /**
     * Initialize the object and load data.
     *
     * <code>
     *
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     * $userRank    = new GamificationUserRank($keys);
     *
     * </code>
     *
     * @param array $keys
     */
    public function __construct($keys = array())
    {
        $this->db = JFactory::getDbo();
        if (!empty($keys)) {
            $this->load($keys);
        }
    }

    /**
     * Create and initialize the object.
     *
     * <code>
     *
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     * $userRank    = GamificationUserRank::getInstance($keys);
     *
     * </code>
     *
     *
     * @param  array $keys
     *
     * @return null|GamificationUserRank
     */
    public static function getInstance(array $keys)
    {
        $userId  = JArrayHelper::getValue($keys, "user_id");
        $groupId = JArrayHelper::getValue($keys, "group_id");

        $index = md5($userId . ":" . $groupId);

        if (empty(self::$instances[$index])) {
            $item                    = new GamificationUserRank($keys);
            self::$instances[$index] = $item;
        }

        return self::$instances[$index];
    }

    /**
     * Load user rank data.
     *
     * <code>
     *
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $userRank     = new GamificationUserRank();
     * $userRank->load($keys);
     *
     * </code>
     *
     * @param $keys
     *
     */
    public function load($keys)
    {
        // Get keys
        $userId  = JArrayHelper::getValue($keys, "user_id");
        $groupId = JArrayHelper::getValue($keys, "group_id");

        // Create a new query object.
        $query = $this->db->getQuery(true);
        $query
            ->select("a.id, a.rank_id, a.user_id, a.group_id")
            ->select("b.title, b.points, b.image, b.published, b.points_id")
            ->from($this->db->quoteName("#__gfy_userranks") . ' AS a')
            ->leftJoin($this->db->quoteName("#__gfy_ranks") . ' AS b ON a.rank_id = b.id')
            ->where("a.user_id  = " . (int)$userId)
            ->where("a.group_id = " . (int)$groupId);

        $this->db->setQuery($query);
        $result = $this->db->loadAssoc();

        if (!empty($result)) { // Set values to variables
            $this->bind($result);
        }
    }

    /**
     * Set the data to the object parameters.
     *
     * <code>
     *
     * $data = array(
     *        "user_id"   => 2,
     *        "group_id"  => 3,
     *        "rank_id"   => 4
     * );
     *
     * $userRank   = new GamificationUserRank();
     * $userRank->bind($data);
     *
     * </code>
     *
     * @param array $data
     */
    public function bind($data)
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    protected function updateObject()
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->update("#__gfy_userranks")
            ->set("rank_id  = " . (int)$this->rank_id)
            ->where("id     = " . (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();
    }

    protected function insertObject()
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->insert("#__gfy_userranks")
            ->set("user_id   = " . (int)$this->user_id)
            ->set("group_id  = " . (int)$this->group_id)
            ->set("rank_id   = " . (int)$this->rank_id);

        $this->db->setQuery($query);
        $this->db->execute();

        return $this->db->insertid();
    }

    /**
     * Save the data to the database.
     *
     * <code>
     *
     * $data = array(
     *        "user_id"   => 2,
     *        "group_id"  => 3,
     *        "rank_id"   => 4
     * );
     *
     * $userRank   = new GamificationUserRank($keys);
     * $userRank->bind($data);
     * $userRank->store();
     *
     * </code>
     *
     * @todo do it to update null values
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
     * Return the title of the rank.
     *
     * <code>
     *
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $userRank   = GamificationUserRank::getInstance($keys);
     * $title       = $userRank->getTitle();
     *
     * </code>
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Return rank image.
     *
     * <code>
     *
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $userRank   = GamificationUserRank::getInstance($keys);
     * $iamge       = $userRank->getImage();
     *
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
     *
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $rankId     = 1;
     *
     * $userRank   = GamificationUserRank::getInstance($keys);
     * $userRank->setRankId($rankId);
     *
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
     * @param array $data
     *
     * </code>
     * $data = array(
     *     "user_id"  => $userId,
     *     "group_id" => $groupId,
     *     "rank_id"  => $rankId
     * );
     *
     * $userRank   = new GamificationUserRank();
     * $userRank->startRanking($data);
     *
     * <code>
     *
     */
    public function startRanking($data)
    {
        $this->bind($data);
        $this->store();

        // Load data
        $keys = array(
            "user_id"  => $data["user_id"],
            "group_id" => $data["group_id"]
        );

        $this->load($keys);
    }
}
