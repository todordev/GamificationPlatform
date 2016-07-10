<?php
/**
 * @package         Gamification
 * @subpackage      Activities
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\Activity;

use Prism\Database\Table;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing an activity.
 *
 * @package         Gamification
 * @subpackage      Activities
 */
class Activity extends Table
{
    /**
     * Activity ID.
     *
     * @var int
     */
    protected $id;

    protected $title;
    protected $content;
    protected $image;
    protected $url;
    protected $created;
    protected $user_id;

    /**
     * Load user activity data.
     *
     * <code>
     * $activityId = 1;
     *
     * $activity   = new Gamification\Activity\Activity(\JFactory::getDbo());
     * $activity->load($activityId);
     * </code>
     *
     * @param int|array $keys
     * @param array $options
     *
     * @throws \RuntimeException
     */
    public function load($keys, array $options = array())
    {
        $query = $this->db->getQuery(true);

        $query
            ->select('a.id, a.title, a.content, a.image, a.url, a.created, a.user_id')
            ->from($this->db->quoteName('#__gfy_activities', 'a'));

        // Prepare keys.
        if (is_array($keys)) {
            foreach ($keys as $column => $value) {
                $query->where($this->db->quoteName('a.'.$column) . ' = ' . $this->db->quote($value));
            }
        } else {
            $query->where('a.id = ' . (int)$keys);
        }

        $this->db->setQuery($query);
        $result = (array)$this->db->loadAssoc();

        $this->bind($result);
    }

    /**
     * Save the data to the database.
     *
     * <code>
     * $data = array(
     *        'title'     => '......',
     *        'content'   => '......',
     *        'image'     => 'picture.png',
     *        'url'       => 'http://itprism.com/',
     *        'user_id'   => 1
     * );
     *
     * $activity   = new Gamification\Activity\Activity(\JFactory::getDbo());
     * $activity->bind($data);
     * $activity->store();
     * </code>
     *
     * @throws \InvalidArgumentException
     */
    public function store()
    {
        if (!$this->user_id) {
            throw new \InvalidArgumentException('Invalid user ID');
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
        $url   = (!$this->url) ? null : $this->db->quote($this->url);

        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->update($this->db->quoteName('#__gfy_activities'))
            ->set($this->db->quoteName('title') . ' = ' . $this->db->quote($title))
            ->set($this->db->quoteName('content') . ' = ' . $this->db->quote($this->content))
            ->set($this->db->quoteName('image') . ' = ' . $image)
            ->set($this->db->quoteName('url') . ' = ' . $url)
            ->set($this->db->quoteName('user_id') . ' = ' . (int)$this->user_id)
            ->where($this->db->quoteName('id') . ' = ' . (int)$this->id);

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
            ->insert($this->db->quoteName('#__gfy_activities'))
            ->set($this->db->quoteName('content') . ' = ' . $this->db->quote($this->content))
            ->set($this->db->quoteName('created') . ' = ' . $this->db->quote($this->created))
            ->set($this->db->quoteName('user_id') . ' = ' . (int)$this->user_id);

        if ($this->title !== null and $this->title !== '') {
            $query->set($this->db->quoteName('title') . ' = ' . $this->db->quote($this->title));
        }

        if ($this->image !== null and $this->image !== '') {
            $query->set($this->db->quoteName('image') . ' = ' . $this->db->quote($this->image));
        }

        if ($this->url !== null and $this->url !== '') {
            $query->set($this->db->quoteName('url') . ' = ' . $this->db->quote($this->url));
        }

        $this->db->setQuery($query);
        $this->db->execute();

        return $this->db->insertid();
    }

    /**
     * Set the title where the URL points.
     *
     * <code>
     * $activity   = new Gamification\Activity\Activity(\JFactory::getDbo());
     * $activity->setTitle(...);
     * </code>
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Set the content.
     *
     * <code>
     * $activity   = new Gamification\Activity\Activity(\JFactory::getDbo());
     * $activity->setContent(...);
     * </code>
     *
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }

    /**
     * Set full URL to an image.
     *
     * <code>
     * $activity   = new Gamification\Activity\Activity(\JFactory::getDbo());
     * $activity->setImage('http://mydomain.com/images/picture.png');
     * </code>
     *
     * @param string $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * Set an URL.
     *
     * <code>
     * $activity   = new Gamification\Activity\Activity(\JFactory::getDbo());
     * $activity->setUrl('http://mydomain.com/');
     * </code>
     *
     * @param string $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * Set an user ID.
     *
     * <code>
     * $userId = 1;
     *
     * $activity   = new Gamification\Activity\Activity(\JFactory::getDbo());
     * $activity->setUserId($userId);
     * </code>
     *
     * @param int $userId
     */
    public function setUserId($userId)
    {
        $this->user_id = $userId;
    }

    /**
     * Return activity ID.
     *
     * <code>
     * $keys = array(
     *     'user_id' => 1,
     *     'created' => 2015-01-01
     * );
     *
     * $activity   = new Gamification\Activity\Activity(\JFactory::getDbo());
     * $activity->load($keys);
     *
     * if (!$activity->getId()) {
     * ...
     * }
     * </code>
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Return the title of the object where the URL points.
     *
     * <code>
     * $activityId = 1;
     *
     * $activity   = new Gamification\Activity\Activity(\JFactory::getDbo());
     * $activity->load($activityId);
     *
     * echo $activity->getTitle();
     * </code>
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Return the content of the activity.
     *
     * <code>
     * $activityId = 1;
     *
     * $activity   = new Gamification\Activity\Activity(\JFactory::getDbo());
     * $activity->load($activityId);
     *
     * echo $activity->getContent();
     * </code>
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Return the image of the activity.
     *
     * <code>
     * $activityId = 1;
     *
     * $activity   = new Gamification\Activity\Activity(\JFactory::getDbo());
     * $activity->load($activityId);
     *
     * echo $activity->getImage();
     * </code>
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Return the URL of the activity.
     *
     * <code>
     * $activityId = 1;
     *
     * $activity   = new Gamification\Activity\Activity(\JFactory::getDbo());
     * $activity->load($activityId);
     *
     * echo $activity->getUrl();
     * </code>
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Return the date where the activity has been created.
     *
     * <code>
     * $activityId = 1;
     *
     * $activity   = new Gamification\Activity\Activity(\JFactory::getDbo());
     * $activity->load($activityId);
     *
     * echo $activity->getCreated();
     * </code>
     *
     * @return string
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Return user ID.
     *
     * <code>
     * $activityId = 1;
     *
     * $activity   = new Gamification\Activity\Activity(\JFactory::getDbo());
     * $activity->load($activityId);
     *
     * echo $activity->getUserId();
     * </code>
     *
     * @return int
     */
    public function getUserId()
    {
        return (int)$this->user_id;
    }
}
