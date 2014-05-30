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
 * This class contains methods that are used for managing a notification.
 *
 * @package         GamificationPlatform
 * @subpackage      GamificationLibrary
 */
class GamificationNotification
{
    /**
     * Notification ID
     * @var integer
     */
    public $id;

    public $note = "";
    public $status = 0;
    public $image;
    public $url;
    public $created;
    public $user_id;

    /**
     * Database driver
     * @var JDatabaseMySQLi
     */
    protected $db;

    /**
     * Initialize the object and load data.
     *
     * <code>
     *
     * $id = 1;
     * $notification   = new GamificationNotification($id);
     *
     * </code>
     *
     * @param int $id
     */
    public function __construct($id = 0)
    {
        $this->db = JFactory::getDbo();

        if (!empty($id)) {
            $this->load($id);
        } else {
            $this->init();
        }
    }

    /**
     * Load user notification.
     *
     * <code>
     *
     * $id = 1;
     * $notification   = new GamificationNotification();
     * $notification->load($id);
     *
     * </code>
     *
     * @param integer $id
     */
    public function load($id)
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->select("a.*")
            ->from($this->db->quoteName("#__gfy_notifications", "a"))
            ->where("a.id   = " . (int)$id);

        $this->db->setQuery($query);
        $result = $this->db->loadAssoc();

        if (!empty($result)) { // Set values to variables
            $this->bind($result);
        } else {
            $this->init();
        }
    }

    protected function init()
    {
        $date          = new JDate();
        $this->created = $date->format("Y-m-d H:i:s");
        $this->read    = 0;
        $this->id      = null;
    }

    /**
     * Set notification data to object parameters.
     *
     * <code>
     *
     * $data = array(
     *        "note"      => "...",
     *        "image"   => "picture.png",
     *        "url"     => "http://itprism.com/",
     *        "user_id" => 1
     * );
     *
     * $notification   = new GamificationNotification();
     * $notification->bind($data);
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

    /**
     * Set notification as read.
     *
     * <code>
     *
     * $id = 1;
     * $notification   = new GamificationNotification($id);
     * $notification->setStatus();
     *
     * </code>
     *
     */
    public function setStatus($status = 0)
    {
        $this->status = $status;
    }

    protected function updateObject()
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->update($this->db->quoteName("#__gfy_notifications"))
            ->set($this->db->quoteName("note") . " = " . $this->db->quote($this->note))
            ->set($this->db->quoteName("image") . " = " . $this->db->quote($this->image))
            ->set($this->db->quoteName("url") . " = " . $this->db->quote($this->url))
            ->set($this->db->quoteName("read") . " = " . (int)$this->read)
            ->set($this->db->quoteName("user_id") . " = " . (int)$this->user_id)
            ->where($this->db->quoteName("id") . " = " . (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();
    }

    protected function insertObject()
    {
        if (!$this->user_id) {
            throw new Exception("Invalid user id", 500);
        }

        // Create a new query object.
        $query = $this->db->getQuery(true);

        $date          = new JDate($this->created);
        $unixTimestamp = $date->toSql();

        $query
            ->insert($this->db->quoteName("#__gfy_notifications"))
            ->set($this->db->quoteName("note") . " = " . $this->db->quote($this->note))
            ->set($this->db->quoteName("created") . " = " . $this->db->quote($unixTimestamp))
            ->set($this->db->quoteName("status") . " = " . (int)$this->status)
            ->set($this->db->quoteName("user_id") . " = " . (int)$this->user_id);

        if (!empty($this->image)) {
            $query->set($this->db->quoteName("image") . " = " . $this->db->quote($this->image));
        }

        if (!empty($this->image)) {
            $query->set($this->db->quoteName("url") . " = " . $this->db->quote($this->url));
        }

        $this->db->setQuery($query);
        $this->db->execute();

        return $this->db->insertid();
    }

    /**
     * Store the data about the notification.
     *
     * <code>
     *
     * $data = array(
     *        "note"      => "...",
     *        "image"   => "picture.png",
     *        "url"     => "http://itprism.com/",
     *        "user_id" => 1
     * );
     *
     * $notification   = new GamificationNotification();
     * $notification->bind($data);
     * $notification->store();
     *
     * </code>
     *
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
     * Remove the notification.
     *
     * <code>
     *
     * $id = 1;
     * $notification   = new GamificationNotification($id);
     * $notification->remove();
     *
     * </code>
     *
     */
    public function remove()
    {
        if (!$this->id) {
            throw new Exception(JText::_("Invalid notification."));
        }

        // Create a new query object.
        $query = $this->db->getQuery(true);
        $query
            ->delete($this->db->quoteName("#__gfy_notifications"))
            ->where($this->db->quoteName("id") . " = " . (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();

        $this->init();
    }

    /**
     * Initialize main variables, create a new notification
     * and send it to user.
     *
     * <code>
     *
     * $note   = "......";
     * $userId = 1;
     *
     * $notification   = new GamificationNotification();
     * $notification->send($note, $userId);
     *
     * </code>
     *
     * @param string $note The message, that will be send to a user.
     * @param integer $userId This is the receiver of the message.
     */
    public function send($note = null, $userId = null)
    {
        if (!empty($note)) {
            $this->note = $note;
        }
        if (!empty($userId)) {
            $this->user_id = (int)$userId;
        }

        // Initialize the properties read, id, created.
        $this->init();

        $this->store();
    }

    /**
     * Initialize main variables, create a new notification
     * and send it to user.
     *
     * <code>
     *
     * $userId = 1;
     *
     * $notification   = new GamificationNotification();
     * $notification->setUserId($note, $userId);
     *
     * </code>
     *
     * @param integer $userId User ID ( receiver of the message )
     */
    public function setUserId($userId)
    {
        $this->user_id = $userId;
    }
}
