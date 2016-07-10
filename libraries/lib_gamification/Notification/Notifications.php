<?php
/**
 * @package         Gamification
 * @subpackage      Notifications
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\Notification;

use Prism\Database\Collection;
use Joomla\Utilities\ArrayHelper;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing notifications.
 *
 * @package         Gamification
 * @subpackage      Notifications
 */
class Notifications extends Collection
{
    /**
     * Load all user notifications.
     *
     * <code>
     * $options = array(
     *      "user_id"        => 1,
     *      "limit"          => 10,
     *      "order_column"    => "a.created"
     *      "order_direction" => "DESC"
     * );
     *
     * $notifications = new Gamification\Notification\Notifications(JFactory::getDbo());
     * $notifications->load($options);
     * </code>
     *
     * @param array $options  Options that will be used for filtering results.
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     */
    public function load(array $options = array())
    {
        $userId         = $this->getOptionId($options, 'user_id');
        if (!$userId) {
            throw new \InvalidArgumentException(\JText::sprintf('LIB_GAMIFICATION_ERROR_INVALID_PARAMETER_TO_OBJECT', 'user_id', get_class($this)));
        }

        $orderColumn    = $this->getOptionOrderColumn($options, 'a.created');
        $orderDirection = $this->getOptionOrderDirection($options);
        $limit          = $this->getOptionLimit($options);

        // Create a new query object.
        $query = $this->db->getQuery(true);
        $query
            ->select(
                'a.content, a.image, a.url, a.created, a.status, a.user_id, ' .
                'b.name'
            )
            ->from($this->db->quoteName('#__gfy_notifications', 'a'))
            ->innerJoin($this->db->quoteName('#__users', 'b') . ' ON a.user_id = b.id')
            ->where('a.user_id = ' . (int)$userId);

        $query->order($this->db->quoteName($orderColumn) .' '. $orderDirection);

        $this->db->setQuery($query, 0, $limit);
        $this->items = (array)$this->db->loadAssocList();
    }

    /**
     * Counts and returns the number of the notifications.
     *
     * <code>
     * $options = array(
     *      "user_id" => 1,
     *      "status"  => Prism\Constants::NOT_READ
     * );
     *
     * $notifications = new Gamification\Notification\Notifications(JFactory::getDbo());
     * $notifications->load($options);
     * </code>
     *
     * @param array $options  Options that will be used for filtering results.
     *
     * @throws \RuntimeException
     * @throws \InvalidArgumentException
     *
     * @return int
     */
    public function getNumber(array $options = array())
    {
        $result  = 0;
        $userId  = $this->getOptionId($options, 'user_id');
        
        if (count($this->items) > 0) {
            if ($userId > 0) {
                foreach ($this->items as &$item) {
                    if ($userId === (int)$item['user_id']) {
                        $result++;
                    }
                }
            } else {
                $result = count($this->items);
            }
        } else {
            if ($userId > 0) {
                $query = $this->db->getQuery(true);
                $query
                    ->select('COUNT(*)')
                    ->from($this->db->quoteName('#__gfy_notifications', 'a'))
                    ->where('a.user_id = ' . (int)$userId);

                $status = ArrayHelper::getValue($options, 'status');
                if (!is_numeric($status)) { // Counts read AND not read.
                    $query->where('a.status IN (0,1)');
                } else { // counts read OR not read.
                    $status = (!$status) ? 0 : 1;
                    $query->where('a.status = ' . (int)$status);
                }

                $this->db->setQuery($query, 0, 1);

                return (int)$this->db->loadResult();
            }
        }
        
        return $result;
    }

    /**
     * Create a notification object and return it.
     *
     * <code>
     * $options = array(
     *     "ids" => array(1,2,3,4,5)
     * );
     *
     * $notifications   = new Gamification\Notification\Notifications(\JFactory::getDbo());
     * $notifications->load($options);
     *
     * $notificationId = 1;
     * $notification   = $notifications->getNotification($notificationId);
     * </code>
     *
     * @param int|string $id Notification ID
     *
     * @throws \UnexpectedValueException
     *
     * @return null|Notification
     */
    public function getNotification($id)
    {
        $notification = null;

        foreach ($this->items as $item) {
            if ((int)$item['id'] === (int)$id) {
                $notification = new Notification($this->db);
                $notification->bind($item);
                break;
            }
        }

        return $notification;
    }

    /**
     * Return the notifications as array with objects.
     *
     * <code>
     * $options = array(
     *     "ids" => array(1,2,3,4,5)
     * );
     *
     * $notifications   = new Gamification\Notification\Notifications(\JFactory::getDbo());
     * $notifications->load($options);
     *
     * $notifications = $notifications->getNotifications();
     * </code>
     *
     * @return array
     */
    public function getNotifications()
    {
        $results = array();

        $i = 0;
        foreach ($this->items as $item) {
            $notification = new Notification($this->db);
            $notification->bind($item);

            $results[$i] = $notification;
            $i++;
        }

        return $results;
    }
}
