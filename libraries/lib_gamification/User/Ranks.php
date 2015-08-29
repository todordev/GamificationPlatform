<?php
/**
 * @package         Gamification\User
 * @subpackage      Ranks
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\User;

use Joomla\Utilities\ArrayHelper;
use Prism\Database\ArrayObject;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing user ranks.
 *
 * @package         Gamification\User
 * @subpackage      Ranks
 */
class Ranks extends ArrayObject
{
    /**
     * Users ID
     *
     * @var integer
     */
    public $userId;

    /**
     * Group ID.
     *
     * @var int
     */
    protected $groupId;

    protected static $instances = array();

    /**
     * Create an object and load user ranks.
     *
     * <code>
     * $options = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     * $userRanks    = Gamification\User\Ranks::getInstance(JFactory::getDbo(), $options);
     * </code>
     *
     * @param  \JDatabaseDriver $db
     * @param  array $options
     *
     * @return null|self
     */
    public static function getInstance(\JDatabaseDriver $db, array $options)
    {
        $userId  = ArrayHelper::getValue($options, "user_id");
        $groupId = ArrayHelper::getValue($options, "group_id");

        $index = md5($userId . ":" . $groupId);

        if (!isset(self::$instances[$index])) {
            $item                    = new Ranks($db);
            $item->load($options);

            self::$instances[$index] = $item;
        }

        return self::$instances[$index];
    }

    /**
     * Load all user ranks and set them to group index.
     * Every user can have only one rank for a group.
     *
     * <code>
     * $options = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $userRanks     = new Gamification\User\Ranks(JFactory::getDbo());
     * $userRanks->load($options);
     * </code>
     *
     * @param array $options
     */
    public function load($options = array())
    {
        $userId  = ArrayHelper::getValue($options, "user_id");
        $groupId = ArrayHelper::getValue($options, "group_id");

        // Create a new query object.
        $query = $this->db->getQuery(true);
        $query
            ->select("a.rank_id, a.user_id, a.group_id")
            ->select("b.title, b.points, b.image, b.published, b.points_id, b.group_id")
            ->from($this->db->quoteName("#__gfy_userranks", "a"))
            ->innerJoin($this->db->quoteName("#__gfy_ranks", "b") . ' ON a.rank_id = b.id')
            ->where("a.user_id = " . (int)$userId);

        if (!empty($groupId)) {
            $query->where("a.group_id = " . (int)$groupId);
        }

        $this->db->setQuery($query);
        $results = (array)$this->db->loadAssocList();

        if (!empty($results)) {

            $this->userId = $userId;

            foreach ($results as $result) {
                $rank = new Rank(\JFactory::getDbo());
                $rank->bind($result);

                $this->items[$result["group_id"]][$rank->getRankId()] = $rank;
            }
        }
    }

    /**
     * Return all ranks.
     *
     * <code>
     * $keys = array(
     *       "user_id"  => 1,
     *       "group_id" => 2
     * );
     *
     * $userRanks   = new Gamification\User\Ranks(JFactory::getDbo());
     * $userRanks->load($keys);
     *
     * $ranks       = $userRanks->getRanks();
     * </code>
     *
     * @return array
     */
    public function getRanks()
    {
        return $this->items;
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
     * $userRanks  = new Gamification\User\Ranks(JFactory::getDbo());
     * $userRanks->load($keys);
     *
     * // Get rank by group ID.
     * $groupId    = 2;
     * $rank       = $userRanks->getRank($groupId);
     * </code>
     *
     * @param integer $groupId
     *
     * @return null|Rank
     */
    public function getRank($groupId)
    {
        return (!isset($this->items[$groupId])) ? null : $this->items[$groupId];
    }
}
