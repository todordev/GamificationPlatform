<?php
/**
 * @package         Gamification
 * @subpackage      Notifications
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\Notification;

use Prism\Database\Table;
use Psr\Log\InvalidArgumentException;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing a notification.
 *
 * @package         Gamification
 * @subpackage      Notifications
 */
class Notification extends Table
{
    /**
     * Notification ID.
     *
     * @var integer
     */
    protected $id;

    protected $title;
    protected $content = "";
    protected $image;
    protected $url;
    protected $created;
    protected $status = 0;
    protected $user_id;

    /**
     * Load user notification.
     *
     * <code>
     * $id = 1;
     * $notification   = new Gamification\Notification\Notification(\JFactory::getDbo());
     * $notification->load($id);
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
            ->select("a.id, a.title, a.content, a.image, a.url, a.created, a.status, a.user_id")
            ->from($this->db->quoteName("#__gfy_notifications", "a"));

        // Prepare keys.
        if (is_array($keys)) {
            foreach ($keys as $column => $value) {
                $query->where($this->db->quoteName("a.".$column) . " = " . $this->db->quote($value));
            }
        } else {
            $query->where("a.id = " . (int)$keys);
        }

        $this->db->setQuery($query);
        $result = $this->db->loadAssoc();

        if (!empty($result)) { // Set values to variables
            $this->bind($result);
        } else {
            $this->init();
        }
    }

    /**
     * Store the data about the notification.
     *
     * <code>
     * $data = array(
     *        "title" => "...",
     *        "content" => "...",
     *        "image"   => "picture.png",
     *        "url"     => "http://itprism.com/",
     *        "user_id" => 1
     * );
     *
     * $notification   = new Gamification\Notification\Notification(\JFactory::getDbo());
     * $notification->bind($data);
     * $notification->store();
     * </code>
     */
    public function store()
    {
        if (!$this->user_id) {
            throw new \InvalidArgumentException("Invalid user id");
        }

        if (!$this->id) {
            $this->id = $this->insertObject();
        } else {
            $this->updateObject();
        }
    }
    
    protected function updateObject()
    {
        $title = (!$this->title) ? null : $this->db->quote($this->title);
        $image = (!$this->image) ? null : $this->db->quote($this->image);
        $url = (!$this->url) ? null : $this->db->quote($this->url);

        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->update($this->db->quoteName("#__gfy_notifications"))
            ->set($this->db->quoteName("content") . " = " . $this->db->quote($this->content))
            ->set($this->db->quoteName("title") . " = " . $title)
            ->set($this->db->quoteName("image") . " = " . $image)
            ->set($this->db->quoteName("url") . " = " . $url)
            ->set($this->db->quoteName("status") . " = " . (int)$this->status)
            ->set($this->db->quoteName("user_id") . " = " . (int)$this->user_id)
            ->where($this->db->quoteName("id") . " = " . (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();
    }

    protected function insertObject()
    {
        // Check for valid date
        if (!(int)$this->created) {
            $date          = new \JDate();
            $this->created = $date->toSql();
        }

        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->insert($this->db->quoteName("#__gfy_notifications"))
            ->set($this->db->quoteName("content") . " = " . $this->db->quote($this->content))
            ->set($this->db->quoteName("created") . " = " . $this->db->quote($this->created))
            ->set($this->db->quoteName("status") . " = " . (int)$this->status)
            ->set($this->db->quoteName("user_id") . " = " . (int)$this->user_id);

        if (!empty($this->title)) {
            $query->set($this->db->quoteName("title") . " = " . $this->db->quote($this->title));
        }

        if (!empty($this->image)) {
            $query->set($this->db->quoteName("image") . " = " . $this->db->quote($this->image));
        }

        if (!empty($this->url)) {
            $query->set($this->db->quoteName("url") . " = " . $this->db->quote($this->url));
        }

        $this->db->setQuery($query);
        $this->db->execute();

        return $this->db->insertid();
    }

    /**
     * Remove the notification.
     *
     * <code>
     * $id = 1;
     *
     * $notification   = new Gamification\Notification\Notification(\JFactory::getDbo());
     * $notification->setId($id);
     *
     * $notification->remove();
     * </code>
     */
    public function remove()
    {
        if (!empty($this->id)) {
            $query = $this->db->getQuery(true);
            $query
                ->delete($this->db->quoteName("#__gfy_notifications"))
                ->where($this->db->quoteName("id") . " = " . (int)$this->id);

            $this->db->setQuery($query);
            $this->db->execute();

            $this->init();
        }
    }

    /**
     * Initialize main variables, create a new notification
     * and send it to user.
     *
     * <code>
     * $content   = "......";
     * $userId = 1;
     *
     * $notification   = new Gamification\Notification\Notification(\JFactory::getDbo());
     * $notification->send($content, $userId);
     * </code>
     *
     * @param string $content The message, that will be send to a user.
     * @param integer $userId This is the receiver of the message.
     */
    public function send($content = null, $userId = null)
    {
        if (!empty($content)) {
            $this->setContent($content);
        }
        if (!empty($userId)) {
            $this->setUserId($userId);
        }

        // Initialize the properties status, id, created.
        $this->init();

        $this->store();
    }

    /**
     * Get notification ID.
     *
     * <code>
     * $id = 1;
     *
     * $notification   = new Gamification\Notification\Notification(\JFactory::getDbo());
     * $notification->load($id);
     *
     * if (!$notification->getId()) {
     * ....
     * )
     * </code>
     *
     * @return int
     */
    public function getId()
    {
        return (int)$this->id;
    }

    /**
     * Set an ID of a notification.
     *
     * <code>
     * $id = 1;
     *
     * $notification   = new Gamification\Notification\Notification(\JFactory::getDbo());
     * $notification->setId($id);
     * </code>
     *
     * @param int $id  Notification ID.
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * Set notification as read.
     *
     * <code>
     * $id = 1;
     * $statusRead = 1;
     *
     * $notification   = new Gamification\Notification\Notification(\JFactory::getDbo());
     * $notification->load($id);
     *
     * $notification->setStatus($statusRead);
     * </code>
     *
     * @param int $status
     */
    public function setStatus($status = 0)
    {
        $this->status = $status;
    }

    /**
     * Set the title of the object where the URL points.
     *
     * <code>
     * $title = "....";
     *
     * $notification   = new Gamification\Notification\Notification(\JFactory::getDbo());
     * $notification->setTitle($title);
     * </code>
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Set the content of the notification.
     *
     * <code>
     * $content = "....";
     *
     * $notification   = new Gamification\Notification\Notification(\JFactory::getDbo());
     * $notification->setContent($content);
     * </code>
     *
     * @param string $content The content of the notification.
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * Get the title of the object where the URL points.
     *
     * <code>
     * $id = 1;
     *
     * $notification   = new Gamification\Notification\Notification(\JFactory::getDbo());
     * $notification->load($id);
     *
     * echo $notification->getTitle();
     * </code>
     *
     * @return string
     */
    public function getTitle()
    {
        return (string)$this->title;
    }

    /**
     * Get notification content.
     *
     * <code>
     * $id = 1;
     *
     * $notification   = new Gamification\Notification\Notification(\JFactory::getDbo());
     * $notification->load($id);
     *
     * echo $notification->getContent();
     * </code>
     *
     * @return string
     */
    public function getContent()
    {
        return (string)$this->content;
    }

    /**
     * Set full URL to image.
     *
     * <code>
     * $notification   = new Gamification\Notification\Notification(\JFactory::getDbo());
     * $notification->setImage("http://mydomain.com/images/picture.png");
     * </code>
     *
     * @param string $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * Get notification image.
     *
     * <code>
     * $id = 1;
     *
     * $notification   = new Gamification\Notification\Notification(\JFactory::getDbo());
     * $notification->load($id);
     *
     * $image = $notification->getImage();
     * </code>
     *
     * @return string
     */
    public function getImage()
    {
        return (string)$this->image;
    }

    /**
     * Set URL.
     *
     * <code>
     * $notification   = new Gamification\Notification\Notification(\JFactory::getDbo());
     * $notification->setUrl("http://mydomain.com/");
     * </code>
     *
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Get notification URL.
     *
     * <code>
     * $id = 1;
     *
     * $notification   = new Gamification\Notification\Notification(\JFactory::getDbo());
     * $notification->load($id);
     *
     * $url = $notification->getUrl();
     * </code>
     *
     * @return string
     */
    public function getUrl()
    {
        return (string)$this->url;
    }

    /**
     * Initialize main variables, create a new notification
     * and send it to user.
     *
     * <code>
     * $userId = 1;
     *
     * $notification   = new Gamification\Notification\Notification(\JFactory::getDbo());
     * $notification->setUserId($userId);
     * </code>
     *
     * @param integer $userId User ID ( receiver of the message )
     */
    public function setUserId($userId)
    {
        $this->user_id = $userId;
    }

    /**
     * Get notification user ID.
     *
     * <code>
     * $id = 1;
     *
     * $notification   = new Gamification\Notification\Notification(\JFactory::getDbo());
     * $notification->load($id);
     *
     * $userId = $notification->getUserId();
     * </code>
     *
     * @return int
     */
    public function getUserId()
    {
        return (int)$this->user_id;
    }

    /**
     * Check if the notification is read.
     *
     * <code>
     * $id = 1;
     *
     * $notification   = new Gamification\Notification\Notification(\JFactory::getDbo());
     * $notification->load($id);
     *
     * if (!$notification->isRead()) {
     * ....
     * )
     * </code>
     *
     * @return bool
     */
    public function isRead()
    {
        return (bool)$this->status;
    }

    /**
     * Update the status of the notification.
     *
     * <code>
     * $id = 1;
     *
     * $notification   = new Gamification\Notification\Notification(\JFactory::getDbo());
     * $notification->load($id);
     *
     * $notification->updateStatus(Prism\Constants::READ);
     * </code>
     *
     * @param int $status Status of a notification (0 - Not Read, 1 - Read, -2 - trashed )
     */
    public function updateStatus($status)
    {
        if (!$this->id or !$this->user_id) {
            throw new InvalidArgumentException(\JText::_("LIB_GAMIFICATION_ERROR_INVALID_PARAMETER_ID_OR_USER_ID"));
        }

        $this->status = (int)$status;

        $query = $this->db->getQuery(true);

        $query
            ->update($this->db->quoteName("#__gfy_notifications"))
            ->set($this->db->quoteName("status") . "=" . (int)$this->status)
            ->where($this->db->quoteName("id") . "=" . (int)$this->id)
            ->where($this->db->quoteName("user_id") . "=" . (int)$this->user_id);

        $this->db->setQuery($query);
        $this->db->execute();
    }

    protected function init()
    {
        $date          = new \JDate();
        $this->created = $date->format("Y-m-d H:i:s");
        $this->status  = 0;
        $this->id      = null;
    }
}
