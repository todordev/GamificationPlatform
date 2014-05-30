<?php
/**
 * @package         GamificationPlatform
 * @subpackage      GamificationLibrary
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing user ranks.
 *
 * @package         GamificationPlatform
 * @subpackage      GamificationLibrary
 */
class GamificationUserRanks
{
    /**
     * Users ID
     * @var integer
     */
    public $userId;

    public $ranks = array();

    /**
     * Database driver
     *
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
     * $userRanks    = new GamificationUserRanks($keys);
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
     * Create an object and load user ranks.
     *
     * <code>
     *
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     * $userRanks    = GamificationUserRanks::getInstance($keys);
     *
     * </code>
     *
     * @param  array $keys
     *
     * @return null|GamificationUserRanks
     */
    public static function getInstance(array $keys)
    {
        $userId  = JArrayHelper::getValue($keys, "user_id");
        $groupId = JArrayHelper::getValue($keys, "group_id");

        $index = md5($userId . ":" . $groupId);

        if (empty(self::$instances[$index])) {
            $item                    = new GamificationUserRanks($keys);
            self::$instances[$index] = $item;
        }

        return self::$instances[$index];
    }

    /**
     * Load all user ranks and set them to group index.
     * Every user can have only one rank for a group.
     *
     * <code>
     *
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $userRanks     = new GamificationUserRanks();
     * $userRanks->load($keys);
     *
     * </code>
     *
     * @param array $keys
     */
    public function load($keys)
    {
        $userId  = JArrayHelper::getValue($keys, "user_id");
        $groupId = JArrayHelper::getValue($keys, "group_id");

        // Create a new query object.
        $query = $this->db->getQuery(true);
        $query
            ->select("a.rank_id, a.user_id, a.group_id")
            ->select("b.title, b.points, b.image, b.published, b.points_id, b.group_id")
            ->from($this->db->quoteName("#__gfy_userranks") . ' AS a')
            ->innerJoin($this->db->quoteName("#__gfy_ranks") . ' AS b ON a.rank_id = b.id')
            ->where("a.user_id = " . (int)$userId);

        if (!empty($groupId)) {
            $query->where("a.group_id = " . (int)$groupId);
        }

        $this->db->setQuery($query);
        $results = $this->db->loadAssocList();

        if (!empty($results)) {

            $this->userId = $userId;

            foreach ($results as $result) {
                $rank = new GamificationUserRank();
                $rank->bind($result);

                $this->ranks[$result["group_id"]][$rank->rank_id] = $rank;
            }
        }
    }

    /**
     * Return all ranks.
     *
     * <code>
     *
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $userRanks   = GamificationUserRanks::getInstance($keys);
     * $ranks       = $userRanks->getRanks();
     *
     * </code>
     *
     * @return array
     */
    public function getRanks()
    {
        return $this->ranks;
    }

    /**
     * Get a rank by group ID.
     * Users can have only one rank in a group.
     *
     * <code>
     *
     * $keys = array(
     *       "user_id"  => 1
     * );
     *
     * // Get all user ranks
     * $userRanks  = GamificationUserRanks::getInstance($keys);
     *
     * // Get rank by group ID.
     * $groupId    = 2;
     * $rank       = $userRanks->getRank($groupId);
     *
     * </code>
     *
     * @param integer $groupId
     *
     * @return null|GamificationUserRank
     */
    public function getRank($groupId)
    {
        return (!isset($this->ranks[$groupId])) ? null : $this->ranks[$groupId];
    }
}
