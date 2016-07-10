<?php
/**
 * @package         Gamification
 * @subpackage      Achievements
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\Achievement;

use Joomla\DI\ContainerAwareInterface;
use Joomla\DI\ContainerAwareTrait;
use Prism\Utilities\StringHelper;
use Joomla\Registry\Registry;
use Prism\Database\Table;
use Gamification\Points\Points;
use Gamification\Mechanic\PointsBased;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing a goal.
 *
 * @package         Gamification
 * @subpackage      Achievements
 */
class Achievement extends Table implements ContainerAwareInterface, PointsBased
{
    use ContainerAwareTrait;

    /**
     * Achievement ID.
     *
     * @var int
     */
    protected $id;

    protected $title;
    protected $context;
    protected $description;
    protected $image;
    protected $image_small;
    protected $image_square;
    protected $note;
    protected $activity_text;
    protected $published;
    protected $ordering;
    protected $group_id;
    protected $points_id;
    protected $points_number;

    /**
     * @var Registry
     */
    protected $custom_data;
    protected $rewards;

    /**
     * @var Points
     */
    protected $points;

    /**
     * Initialize the object.
     *
     * @param \JDatabaseDriver $db
     */
    public function __construct(\JDatabaseDriver $db = null)
    {
        parent::__construct($db);

        $this->custom_data = new Registry();
        $this->rewards     = new Registry();
    }

    /**
     * Get achievement ID.
     *
     * <code>
     * $achievementId    = 1;
     * $achievement      = new Gamification\Achievement\Achievement(\JFactory::getDbo());
     *
     * if (!$achievement->getId()) {
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
     * Get title.
     *
     * <code>
     * $achievementId    = 1;
     * $achievement      = new Gamification\Achievement\Achievement(\JFactory::getDbo());
     * $achievement->load($achievementId);
     *
     * $title            = $achievement->getTitle();
     * </code>
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get image.
     *
     * <code>
     * $achievementId    = 1;
     * $achievement      = new Gamification\Achievement\Achievement(\JFactory::getDbo());
     * $achievement->load($achievementId);
     *
     * $image     = $achievement->getImage();
     * </code>
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Get small image.
     *
     * <code>
     * $achievementId    = 1;
     * $achievement      = new Gamification\Achievement\Achievement(\JFactory::getDbo());
     * $achievement->load($achievementId);
     *
     * echo $achievement->getImageSmall();
     * </code>
     *
     * @return string
     */
    public function getImageSmall()
    {
        return $this->image_small;
    }

    /**
     * Get square image.
     *
     * <code>
     * $achievementId    = 1;
     * $achievement      = new Gamification\Achievement\Achievement(\JFactory::getDbo());
     * $achievement->load($achievementId);
     *
     * echo $achievement->getImageSquare();
     * </code>
     *
     * @return string
     */
    public function getImageSquare()
    {
        return $this->image_square;
    }

    /**
     * Get context.
     *
     * <code>
     * $achievementId    = 1;
     * $achievement      = new Gamification\Achievement\Achievement(\JFactory::getDbo());
     * $achievement->load($achievementId);
     *
     * echo $achievement->getContext();
     * </code>
     *
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }
    
    /**
     * Get note.
     *
     * <code>
     * $achievementId    = 1;
     * $achievement      = new Gamification\Achievement\Achievement(\JFactory::getDbo());
     * $achievement->load($achievementId);
     *
     * $note      = $achievement->getNote();
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
     * $achievementId    = 1;
     * $achievement      = new Gamification\Achievement\Achievement(\JFactory::getDbo());
     * $achievement->load($achievementId);
     *
     * $data = array(
     *     "name" => "John Dow",
     *     "title" => "...",
     *     "target" => "..."
     * );
     *
     * echo $achievement->getActivityText($data);
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
     * Return description of the achievement.
     *
     * <code>
     * $achievementId    = 1;
     * $achievement      = new Gamification\Achievement\Achievement(\JFactory::getDbo());
     * $achievement->load($achievementId);
     *
     * echo $achievement->getDescription();
     * </code>
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Check for published goal.
     *
     * <code>
     * $achievementId     = 1;
     * $achievement       = new Gamification\Achievement\Achievement(\JFactory::getDbo());
     *
     * if(!$achievement->isPublished()) {
     * ...
     * }
     * </code>
     *
     * @return boolean
     */
    public function isPublished()
    {
        return (!$this->published) ? false : true;
    }

    /**
     * Get the group ID of the goal.
     *
     * <code>
     * $achievementId    = 1;
     *
     * $achievement      = new Gamification\Achievement\Achievement(\JFactory::getDbo());
     * $achievement->load($achievementId);
     *
     * $groupId    = $achievement->getGroupId();
     * </code>
     *
     * @return integer
     */
    public function getGroupId()
    {
        return $this->group_id;
    }

    /**
     * Return a custom data.
     *
     * <code>
     * $achievementId    = 1;
     * $achievement      = new Gamification\Achievement\Achievement(\JFactory::getDbo());
     * $achievement->load($achievementId);
     *
     * echo $achievement->getCustomData($data);
     * </code>
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function getCustomData($key, $default = null)
    {
        return $this->custom_data->get($key, $default);
    }

    /**
     * Load data from database.
     *
     * <code>
     * $keys = array(
     *    "id" => 1,
     *    "group_id" => 2
     * );
     *
     * $achievement      = new Gamification\Achievement\Achievement(\JFactory::getDbo());
     * $achievement->load($keys);
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
                'a.id, a.title, a.context, a.description, a.activity_text, a.image, a.image_small, a.image_square, ' .
                'a.points_id, a.points_number, a.published, a.custom_data, a.rewards, a.group_id'
            )
            ->from($this->db->quoteName('#__gfy_achievements', 'a'));

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

        $this->bind($result, ['custom_data', 'rewards']);
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
     * Get points number needed to reach this achievement.
     *
     * <code>
     * $achievementId    = 1;
     * $achievement      = new Gamification\Achievement\Achievement(\JFactory::getDbo());
     * $achievement->load($achievementId);
     *
     * $points     = $achievement->getPointsNumber();
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
     * $achievementId    = 1;
     * $achievement      = new Gamification\Achievement\Achievement(\JFactory::getDbo());
     * $achievement->load($achievementId);
     *
     * if ($achievement->getPointsId()) {
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
     * $achievementId    = 1;
     * $achievement      = new Gamification\Achievement\Achievement(\JFactory::getDbo());
     * $achievement->load($achievementId);
     *
     * $points     = $achievement->getPoints();
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
     * $achievement      = new Gamification\Achievement\Achievement(\JFactory::getDbo());
     * $achievement->setPoints($points);
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
    
    /**
     * Save the data to the database.
     *
     * <code>
     * $data = array(
     *        "title"    => "......",
     *        "description"    => "......",
     *        "image"    => "picture.png",
     *        "note"    => '...',
     *        "published" => 1,
     *        "group_id"  => 3
     * );
     *
     * $achievement   = new Gamification\Achievement\Achievement(\JFactory::getDbo());
     * $achievement->bind($data);
     * $achievement->store();
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
        $note         = (!$this->note) ? null : $this->db->quote($this->note);
        $description  = (!$this->description) ? null : $this->db->quote($this->description);
        $activityText = (!$this->activity_text) ? null : $this->db->quote($this->activity_text);

        $customData = ($this->custom_data instanceof Registry) ? $this->custom_data->toString() : '{}';
        $rewards    = ($this->rewards instanceof Registry) ? $this->rewards->toString() : '{}';

        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->update($this->db->quoteName('#__gfy_achievements'))
            ->set($this->db->quoteName('title') . '=' . $this->db->quote($this->title))
            ->set($this->db->quoteName('context') . '=' . $this->db->quote($this->context))
            ->set($this->db->quoteName('description') . '=' . $description)
            ->set($this->db->quoteName('image') . '=' . $this->db->quote($this->image))
            ->set($this->db->quoteName('image_small') . '=' . $this->db->quote($this->image_small))
            ->set($this->db->quoteName('image_square') . '=' . $this->db->quote($this->image_square))
            ->set($this->db->quoteName('note') . '=' . $note)
            ->set($this->db->quoteName('activity_text') . '=' . $activityText)
            ->set($this->db->quoteName('published') . '=' . (int)$this->published)
            ->set($this->db->quoteName('custom_data') . '=' . $this->db->quote($customData))
            ->set($this->db->quoteName('rewards') . '=' . $this->db->quote($rewards))
            ->set($this->db->quoteName('group_id') . '=' . (int)$this->group_id)
            ->set($this->db->quoteName('points_id') . '=' . (int)$this->points_id)
            ->set($this->db->quoteName('points_number') . '=' . (int)$this->points_number)
            ->where($this->db->quoteName('id') . '=' . (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();
    }

    protected function insertObject()
    {
        $customData   = ($this->custom_data instanceof Registry) ? $this->custom_data->toString() : '{}';
        $rewards      = ($this->rewards instanceof Registry) ? $this->rewards->toString() : '{}';
        
        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->insert($this->db->quoteName('#__gfy_achievements'))
            ->set($this->db->quoteName('title') . '=' . $this->db->quote($this->title))
            ->set($this->db->quoteName('context') . '=' . $this->db->quote($this->context))
            ->set($this->db->quoteName('image') . '=' . $this->db->quote($this->image))
            ->set($this->db->quoteName('image_small') . '=' . $this->db->quote($this->image_small))
            ->set($this->db->quoteName('image_square') . '=' . $this->db->quote($this->image_square))
            ->set($this->db->quoteName('published') . '=' . (int)$this->published)
            ->set($this->db->quoteName('custom_data') . '=' . $this->db->quote($customData))
            ->set($this->db->quoteName('rewards') . '=' . $this->db->quote($rewards))
            ->set($this->db->quoteName('points_id') . '=' . (int)$this->points_id)
            ->set($this->db->quoteName('points_number') . '=' . (int)$this->points_number)
            ->set($this->db->quoteName('group_id') . '=' . (int)$this->group_id);

        if ($this->note !== null and $this->note !== '') {
            $query->set($this->db->quoteName('note') . ' = ' . $this->db->quote($this->note));
        }

        if ($this->description !== null and $this->description !== '') {
            $query->set($this->db->quoteName('description') . ' = ' . $this->db->quote($this->description));
        }

        if ($this->activity_text !== null and $this->activity_text !== '') {
            $query->set($this->db->quoteName('activity_text') . ' = ' . $this->db->quote($this->activity_text));
        }

        $this->db->setQuery($query);
        $this->db->execute();

        return $this->db->insertid();
    }
}
