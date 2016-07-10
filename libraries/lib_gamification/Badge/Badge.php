<?php
/**
 * @package         Gamification
 * @subpackage      Badges
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\Badge;

use Gamification\Points\Points;
use Gamification\Mechanic\PointsBased;
use Joomla\Registry\Registry;
use Joomla\DI\ContainerAwareInterface;
use Joomla\DI\ContainerAwareTrait;
use Prism\Database\Table;
use Prism\Utilities\StringHelper;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing a badge.
 *
 * @package         Gamification
 * @subpackage      Badges
 */
class Badge extends Table implements ContainerAwareInterface, PointsBased
{
    use ContainerAwareTrait;
    
    /**
     * Badge ID.
     *
     * @var int
     */
    protected $id;

    protected $title;
    protected $description;
    protected $activity_text;
    protected $image;
    protected $note;
    protected $published;
    protected $group_id;
    protected $points_number;

    /**
     * @var Points
     */
    protected $points;
    protected $points_id;

    /**
     * @var Registry
     */
    protected $custom_data;

    /**
     * Initialize the object.
     *
     * @param \JDatabaseDriver $db
     */
    public function __construct(\JDatabaseDriver $db = null)
    {
        parent::__construct($db);

        $this->custom_data = new Registry;
    }

    /**
     * Get badge ID.
     *
     * <code>
     * $badgeId    = 1;
     * $badge      = new Gamification\Badge\Badge(\JFactory::getDbo());
     *
     * if (!$badge->getId()) {
     * // ...
     * }
     * </code>
     *
     * @return string
     */
    public function getId()
    {
        return (int)$this->id;
    }

    /**
     * Get badge title.
     *
     * <code>
     * $badgeId    = 1;
     * $badge      = new Gamification\Badge\Badge(\JFactory::getDbo());
     * $title      = $badge->getTitle();
     * </code>
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get badge description.
     *
     * <code>
     * $badgeId    = 1;
     * $badge      = new Gamification\Badge\Badge(\JFactory::getDbo());
     * $badge->load($badgeId);
     *
     * echo $badge->getDescription();
     * </code>
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get badge image.
     *
     * <code>
     * $badgeId    = 1;
     * $badge      = new Gamification\Badge\Badge(\JFactory::getDbo());
     * $image      = $badge->getImage();
     * </code>
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Get badge note.
     *
     * <code>
     * $badgeId    = 1;
     * $badge      = new Gamification\Badge\Badge(\JFactory::getDbo());
     * $note       = $badge->getNote();
     * </code>
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Return the text that will be used as activity information. It is possible
     * to replace placeholders with dynamically generated data.
     *
     * <code>
     * $badgeId    = 1;
     * $badge      = new Gamification\Badge\Badge(\JFactory::getDbo());
     * $badge->load($badgeId);
     *
     * $data = array(
     *     "name" => "John Dow",
     *     "title" => "...",
     *     "target" => "..."
     * );
     *
     * echo $badge->getActivityText($data);
     * </code>
     *
     * @param array $data
     * @return string
     */
    public function getActivityText(array $data = array())
    {
        if (count($data) > 0) {
            $result = $this->activity_text;

            foreach ($data as $placeholder => $value) {
                $placeholder = '{'.strtoupper($placeholder).'}';
                $result = str_replace($placeholder, $value, $result);
            }

            return $result;
        } else {
            return $this->activity_text;
        }
    }

    /**
     * Check for published badge.
     *
     * <code>
     * $badgeId     = 1;
     * $badge       = new Gamification\Badge\Badge(\JFactory::getDbo());
     *
     * if(!$badge->isPublished()) {
     * ...
     * }
     * </code>
     *
     * @return bool
     */
    public function isPublished()
    {
        return (!$this->published) ? false : true;
    }

    /**
     * Get the group ID of the badge.
     *
     * <code>
     * $badgeId    = 1;
     *
     * $badge      = new Gamification\Badge\Badge(\JFactory::getDbo());
     * $badge->load($badgeId);
     *
     * $groupId    = $badge->getGroupId();
     * </code>
     *
     * @return int
     */
    public function getGroupId()
    {
        return (int)$this->group_id;
    }

    /**
     * Return custom data object.
     *
     * <code>
     * $badgeId    = 1;
     *
     * $badge      = new Gamification\Badge\Badge(\JFactory::getDbo());
     * $badge->load($badgeId);
     *
     * $customData = $badge->getCustomData();
     * </code>
     *
     * @return Registry
     */
    public function getCustomData()
    {
        return $this->custom_data;
    }

    /**
     * Return badge status.
     *
     * <code>
     * $keys = array(
     *     'badge_id' => 1,
     *     'user_id' => 2
     * );
     *
     * $badge      = new Gamification\Badge\User\Badge(\JFactory::getDbo());
     * $badge->load($keys);
     *
     * $published = $badge->getPublished();
     * </code>
     *
     * @return int
     */
    public function getPublished()
    {
        return (int)$this->published;
    }

    /**
     * Load badge data from database.
     *
     * <code>
     * $keys = array(
     *    "group_id" => 1,
     *    "points_id" => 2
     * );
     *
     * $badge      = new Gamification\Badge\Badge(\JFactory::getDbo());
     * $badge->load($keys);
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
            ->select(
                'a.id, a.title, a.description, a.activity_text, a.image, a.note, a.custom_data, ' .
                'a.published, a.params, a.group_id, a.points_id, a.points_number'
            )
            ->from($this->db->quoteName('#__gfy_badges', 'a'));

        // Prepare keys.
        if (is_array($keys)) {
            foreach ($keys as $column => $value) {
                $query->where($this->db->quoteName('a.'.$column) . '=' . $this->db->quote($value));
            }
        } else {
            $query->where('a.id = ' . (int)$keys);
        }

        $this->db->setQuery($query);
        $result = (array)$this->db->loadAssoc();

        $this->bind($result);
    }

    protected function preparePointsObject($pointsId)
    {
        if ($pointsId > 0) {
            $key = StringHelper::generateMd5Hash(Points::class, $pointsId);

            if ($this->container !== null) {
                if ($this->container->exists($key)) {
                    $this->points = $this->container->get($key);
                } else {
                    $this->points = new Points($this->db);
                    $this->points->load($pointsId);

                    $this->container->set($key, $this->points);
                }
            } else {
                $this->points = new Points($this->db);
                $this->points->load($pointsId);
            }
        }
    }
    
    /**
     * Set notification data to object parameters.
     *
     * <code>
     * $keys = array(
     *    "group_id" => 1,
     *    "points_id" => 2
     * );
     *
     * $data = array(
     *        "title"    => "......",
     *        "description"    => "......",
     *        "activity_text"    => "......",
     *        "custom_data"    => array(,,,),
     *        "params"    => array(,,,),
     *        "image"    => "picture.png",
     *        "note"    => null,
     *        "published" => 1,
     *        "group_id"  => 3,
     *        "points_number"    => 100,
     *        "points_title"    => 100,
     *        "points_id" => 1
     * );
     *
     * $badge      = new Gamification\Badge\Badge(\JFactory::getDbo());
     * $badge->load($keys);
     *
     * $badge->bind($data);
     * </code>
     *
     * @param array $data
     * @param array $ignored
     */
    public function bind($data, array $ignored = array())
    {
        // Parse custom data of the object if they exists.
        if (array_key_exists('custom_data', $data) and !in_array('custom_data', $ignored, true)) {
            $this->custom_data = new Registry($data['custom_data']);
            unset($data['custom_data']);
        }

        parent::bind($data, $ignored);
    }

    /**
     * Save the data to the database.
     *
     * <code>
     * $data = array(
     *        "title"    => "......",
     *        "description"    => "......",
     *        "activity_text"    => "......",
     *        "image"    => "picture.png",
     *        "note"    => null,
     *        "published" => 1,
     *        "group_id"  => 3,
     *        "points"    => 100,
     *        "points_id" => 1
     * );
     *
     * $badge   = new Gamification\Badge\Badge(\JFactory::getDbo());
     * $badge->bind($data);
     * $badge->store();
     * </code>
     */
    public function store()
    {
        if (!$this->id) {
            $this->id = $this->insertObject();
        } else {
            $this->updateObject();
        }
    }

    protected function updateObject()
    {
        $note = (!$this->note) ? null : $this->db->quote($this->note);
        $description = (!$this->description) ? null : $this->db->quote($this->description);
        $activityText = (!$this->activity_text) ? null : $this->db->quote($this->activity_text);

        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->update($this->db->quoteName('#__gfy_badges'))
            ->set($this->db->quoteName('title') . ' = ' . $this->db->quote($this->title))
            ->set($this->db->quoteName('image') . ' = ' . $this->db->quote($this->image))
            ->set($this->db->quoteName('note') . ' = ' . $note)
            ->set($this->db->quoteName('description') . ' = ' . $description)
            ->set($this->db->quoteName('activity_text') . ' = ' . $activityText)
            ->set($this->db->quoteName('published') . ' = ' . (int)$this->published)
            ->set($this->db->quoteName('group_id') . ' = ' . (int)$this->group_id)
            ->set($this->db->quoteName('points_number') . ' = ' . $this->db->quote($this->points_number))
            ->set($this->db->quoteName('points_id') . ' = ' . $this->db->quote($this->points_id))
            ->set($this->db->quoteName('params') . ' = ' . $this->db->quote($this->params->toString()))
            ->set($this->db->quoteName('custom_data') . ' = ' . $this->db->quote($this->custom_data->toString()))
            ->where($this->db->quoteName('id') . ' = ' . (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();
    }

    protected function insertObject()
    {
        $query = $this->db->getQuery(true);

        $query
            ->insert($this->db->quoteName('#__gfy_badges'))
            ->set($this->db->quoteName('title') . '=' . $this->db->quote($this->title))
            ->set($this->db->quoteName('image') . '=' . $this->db->quote($this->image))
            ->set($this->db->quoteName('published') . '=' . (int)$this->published)
            ->set($this->db->quoteName('group_id') . '=' . (int)$this->group_id)
            ->set($this->db->quoteName('points_number') . '=' . $this->db->quote($this->points_number))
            ->set($this->db->quoteName('points_id') . '=' . $this->db->quote($this->points_id))
            ->set($this->db->quoteName('params') . ' = ' . $this->db->quote($this->params->toString()))
            ->set($this->db->quoteName('custom_data') . ' = ' . $this->db->quote($this->custom_data->toString()));

        if ($this->note !== null and $this->note !== '') {
            $query->set($this->db->quoteName('note') . '=' . $this->db->quote($this->note));
        }

        if ($this->description !== null and $this->description !== '') {
            $query->set($this->db->quoteName('description') . '=' . $this->db->quote($this->description));
        }

        if ($this->activity_text !== null and $this->activity_text !== '') {
            $query->set($this->db->quoteName('activity_text') . '=' . $this->db->quote($this->activity_text));
        }

        $this->db->setQuery($query);
        $this->db->execute();

        return $this->db->insertid();
    }

    /**
     * Get points number needed to reach this badge.
     *
     * <code>
     * $badgeId    = 1;
     * $badge      = new Gamification\Badge\Badge(\JFactory::getDbo());
     * $badge->load($badgeId);
     *
     * $points     = $badge->getPointsNumber();
     * </code>
     *
     * @return int
     */
    public function getPointsNumber()
    {
        return $this->points_number;
    }

    /**
     * Get points ID.
     *
     * <code>
     * $badgeId    = 1;
     * $badge      = new Gamification\Badge\Badge(\JFactory::getDbo());
     * $badge->load($badgeId);
     *
     * if ($badge->getPointsId()) {
     * // ...
     * }
     * </code>
     *
     * @return Points
     */
    public function getPointsId()
    {
        return $this->points_id;
    }
    
    /**
     * Get points object.
     *
     * <code>
     * $badgeId    = 1;
     * $badge      = new Gamification\Badge\Badge(\JFactory::getDbo());
     * $badge->load($badgeId);
     *
     * $points     = $badge->getPoints();
     * </code>
     *
     * @return Points
     */
    public function getPoints()
    {
        // Create a basic points object.
        if ($this->points === null and $this->points_id > 0) {
            $this->preparePointsObject($this->points_id);
        }

        return $this->points;
    }

    /**
     * Set Points object.
     *
     * <code>
     * $pointsId   = 1;
     * $points     = new Gamification\Points\Points(\JFactory::getDbo());
     * $points->load($pointsId);
     *
     * $badge      = new Gamification\Badge\Badge(\JFactory::getDbo());
     * $badge->setPoints($points);
     * </code>
     *
     * @param Points $points
     * @throws \UnexpectedValueException
     * @throws \OutOfBoundsException
     *
     * @return self
     */
    public function setPoints(Points $points)
    {
        $this->points = $points;

        if ($this->points_id > 0 and $this->points_id !== $points->getId()) {
            throw new \UnexpectedValueException('The points ID already exists and it does not much with new Points object.');
        }

        $this->points_id = $points->getId();

        // Add the points object in the container.
        $key = StringHelper::generateMd5Hash(Points::class, $this->points_id);
        if ($this->container !== null and !$this->container->exists($key)) {
            $this->container->set($key, $this->points);
        }

        return $this;
    }
}
