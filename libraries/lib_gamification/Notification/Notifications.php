<?php
/**
 * @package         Gamification
 * @subpackage      Notifications
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\Notification;

use Prism\Database\ArrayObject;
use Joomla\Utilities\ArrayHelper;
use Psr\Log\InvalidArgumentException;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing notifications.
 *
 * @package         Gamification
 * @subpackage      Notifications
 */
class Notifications extends ArrayObject
{
    /**
     * Load all user notifications.
     *
     * <code>
     * $options = array(
     *      "user_id"        => 1,
     *      "limit"          => 10,
     *      "sort_direction" => "DESC"
     * );
     *
     * $notifications = new Gamification\Notification\Notifications(JFactory::getDbo());
     * $notifications->load($options);
     * </code>
     *
     * @param array $options  Options that will be used for filtering results.
     */
    public function load($options = array())
    {
        $userId  = ArrayHelper::getValue($options, "user_id", 0, "integer");
        if (!$userId) {
            throw new InvalidArgumentException(\JText::sprintf("LIB_GAMIFICATION_ERROR_INVALID_PARAMETER_TO_OBJECT", "user_id", get_class($this)));
        }

        $sortDir = ArrayHelper::getValue($options, "sort_direction", "DESC");
        $sortDir = (strcmp("DESC", $sortDir) == 0) ? "DESC" : "ASC";

        $limit   = ArrayHelper::getValue($options, "limit", 10, "int");

        // Create a new query object.
        $query = $this->db->getQuery(true);
        $query
            ->select(
                "a.content, a.image, a.url, a.created, a.status, a.user_id, " .
                "b.name"
            )
            ->from($this->db->quoteName("#__gfy_notifications", "a"))
            ->innerJoin($this->db->quoteName("#__users", "b") . ' ON a.user_id = b.id')
            ->where("a.user_id = " . (int)$userId);

        $query->order("a.created " . $sortDir);

        $this->db->setQuery($query, 0, $limit);
        $this->items = (array)$this->db->loadAssocList();
    }

    public function getNumber($options = array())
    {
        $userId  = ArrayHelper::getValue($options, "user_id", 0, "integer");
        if (!$userId) {
            return count($this->items);
        }

        $query = $this->db->getQuery(true);
        $query
            ->select("COUNT(*)")
            ->from($this->db->quoteName("#__gfy_notifications", "a"))
            ->where("a.user_id = " . (int)$userId);

        $status  = ArrayHelper::getValue($options, "status");
        if (!is_numeric($status)) { // Count read and not read.
            $query->where("a.status IN (0,1)");
        } else { // count one from both - read or not read.
            $status = (!$status) ? 0 : 1;
            $query->where("a.status = " .(int)$status);
        }

        $this->db->setQuery($query, 0, 1);

        return (int)$this->db->loadResult();
    }
}
