<?php
/**
 * @package         Gamification
 * @subpackage      Rewards
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\Reward;

use Joomla\DI\ContainerAwareInterface;
use Joomla\DI\ContainerAwareTrait;
use Prism\Utilities\StringHelper;
use Prism\Database\Table;
use Gamification\Points\Points;
use Gamification\Mechanic\PointsBased;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing a reward.
 *
 * @package         Gamification
 * @subpackage      Rewards
 */
class Reward extends Table implements ContainerAwareInterface, PointsBased
{
    use ContainerAwareTrait;

    /**
     * Reward ID.
     *
     * @var integer
     */
    protected $id;

    protected $title;
    protected $description;
    protected $activity_text;
    protected $points_number;
    protected $image;
    protected $note;
    protected $number;
    protected $published;
    protected $points_id;
    protected $group_id;

    /**
     * @var Points
     */
    protected $points;

    /**
     * Get reward ID.
     *
     * <code>
     * $rewardId    = 1;
     *
     * $reward      = new Gamification\Reward\Reward(\JFactory::getDbo());
     * $reward->load($rewardId);
     *
     * if (!$reward->getId()) {
     * // ...
     * }
     * </code>
     *
     * @return int
     */
    public function getId()
    {
        return (int)$this->id;
    }

    /**
     * Get reward title.
     *
     * <code>
     * $rewardId    = 1;
     * $reward      = new Gamification\Reward\Reward(\JFactory::getDbo());
     * $title      = $reward->getTitle();
     * </code>
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get the number of points need to receive this reward.
     *
     * <code>
     * $rewardId    = 1;
     * $reward      = new Gamification\Reward\Reward(\JFactory::getDbo());
     * $reward->load($rewardId);
     *
     * $points     = $reward->getPointsNumber();
     * </code>
     *
     * @return number
     */
    public function getPointsNumber()
    {
        return $this->points_number;
    }

    /**
     * Get the points ID used for the reward.
     *
     * <code>
     * $rewardId    = 1;
     *
     * $reward      = new Gamification\Reward\Reward(\JFactory::getDbo());
     * $reward->load($rewardId);
     *
     * $pointsId   = $reward->getPointsId();
     * </code>
     *
     * @return int
     */
    public function getPointsId()
    {
        return (int)$this->points_id;
    }

    /**
     * Get reward image.
     *
     * <code>
     * $rewardId    = 1;
     * $reward      = new Gamification\Reward\Reward(\JFactory::getDbo());
     * $image      = $reward->getImage();
     * </code>
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Get reward note.
     *
     * <code>
     * $rewardId    = 1;
     * $reward      = new Gamification\Reward\Reward(\JFactory::getDbo());
     * $note       = $reward->getNote();
     * </code>
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }

    /**
     * Get reward number.
     *
     * <code>
     * $rewardId    = 1;
     * $reward      = new Gamification\Reward\Reward(\JFactory::getDbo());
     * $number      = $reward->getNumber();
     * </code>
     *
     * @return int|null
     */
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * Return reward description.
     *
     * <code>
     * $rewardId    = 1;
     * $reward      = new Gamification\Reward\Reward(\JFactory::getDbo());
     * $reward->load($rewardId);
     *
     * echo $reward->getDescription();
     * </code>
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Return the text that will be used as activity information. It is possible
     * to replace placeholders with dynamically generated data.
     *
     * <code>
     * $rewardId    = 1;
     * $reward      = new Gamification\Reward\Reward(\JFactory::getDbo());
     * $reward->load($rewardId);
     *
     * $data = array(
     *     "name" => "John Dow",
     *     "title" => "...",
     *     "target" => "..."
     * );
     *
     * echo $reward->getActivityText($data);
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
     * Check for published reward.
     *
     * <code>
     * $rewardId     = 1;
     * $reward       = new Gamification\Reward\Reward(\JFactory::getDbo());
     *
     * if(!$reward->isPublished()) {
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
     * Get the group ID of the reward.
     *
     * <code>
     * $rewardId    = 1;
     *
     * $reward      = new Gamification\Reward\Reward(\JFactory::getDbo());
     * $reward->load($rewardId);
     *
     * $groupId    = $reward->getGroupId();
     * </code>
     *
     * @return int
     */
    public function getGroupId()
    {
        return (int)$this->group_id;
    }

    /**
     * Check for available rewards.
     *
     * <code>
     * $rewardId    = 1;
     *
     * $reward      = new Gamification\Reward\Reward(\JFactory::getDbo());
     * $reward->load($rewardId);
     *
     * if ($reward->hasRewards()) {
     * // ...
     * }
     * </code>
     *
     * @return bool
     */
    public function hasRewards()
    {
        if ($this->number === null) {
            return true;
        }

        return (bool)(is_numeric($this->number) and  (int)$this->number > 0);
    }

    /**
     * Load reward data from database.
     *
     * <code>
     * $keys = array(
     *    "group_id" => 1,
     *    "points_id" => 2
     * );
     *
     * $reward      = new Gamification\Reward\Reward(\JFactory::getDbo());
     * $reward->load($keys);
     * </code>
     *
     * @param int|array $keys
     * @param array $options
     *
     * @throws \RuntimeException
     */
    public function load($keys, array $options = array())
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->select('a.id, a.title, a.description, a.activity_text, a.points_number, a.image, a.note, a.number, a.published, a.points_id, a.group_id')
            ->from($this->db->quoteName('#__gfy_rewards', 'a'));

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
     * Get points object.
     *
     * <code>
     * $rewardId    = 1;
     * $reward      = new Gamification\Reward\Reward(\JFactory::getDbo());
     * $reward->load($rewardId);
     *
     * $points     = $reward->getPoints();
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
     * $reward      = new Gamification\Reward\Reward(\JFactory::getDbo());
     * $reward->setPoints($points);
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
     *        "activity_text"    => "......",
     *        "points_number"    => 100,
     *        "image"    => "picture.png",
     *        "note"    => null,
     *        "number"    => 10,
     *        "published" => 1,
     *        "points_id" => 2,
     *        "group_id"  => 3
     * );
     *
     * $reward   = new Gamification\Reward\Reward(\JFactory::getDbo());
     * $reward->bind($data);
     * $reward->store();
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
        $note        = (!$this->note) ? null : $this->db->quote($this->note);
        $description = (!$this->description) ? null : $this->db->quote($this->description);
        $activityText = (!$this->activity_text) ? null : $this->db->quote($this->activity_text);
        $number      = (!is_numeric($this->number) and !$this->number) ? null : $this->db->quote($this->number);

        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->update($this->db->quoteName('#__gfy_rewards'))
            ->set($this->db->quoteName('title') . '  = ' . $this->db->quote($this->title))
            ->set($this->db->quoteName('points') . '  = ' . $this->db->quote($this->points))
            ->set($this->db->quoteName('image') . '  = ' . $this->db->quote($this->image))
            ->set($this->db->quoteName('note') . '  = ' . $note)
            ->set($this->db->quoteName('number') . '  = ' . $number)
            ->set($this->db->quoteName('description') . '  = ' . $description)
            ->set($this->db->quoteName('activity_text') . '  = ' . $activityText)
            ->set($this->db->quoteName('published') . '  = ' . (int)$this->published)
            ->set($this->db->quoteName('points_id') . '  = ' . (int)$this->points_id)
            ->set($this->db->quoteName('group_id') . '  = ' . (int)$this->group_id)
            ->where($this->db->quoteName('id') . '  = ' . (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();
    }

    protected function insertObject()
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->insert($this->db->quoteName('#__gfy_rewards'))
            ->set($this->db->quoteName('title') . '  = ' . $this->db->quote($this->title))
            ->set($this->db->quoteName('points') . '  = ' . $this->db->quote($this->points))
            ->set($this->db->quoteName('image') . '  = ' . $this->db->quote($this->image))
            ->set($this->db->quoteName('published') . '  = ' . (int)$this->published)
            ->set($this->db->quoteName('points_id') . '  = ' . (int)$this->points_id)
            ->set($this->db->quoteName('group_id') . '  = ' . (int)$this->group_id);

        if ($this->note !== null and $this->note !== '') {
            $query->set($this->db->quoteName('note') . ' = ' . $this->db->quote($this->note));
        }

        if ($this->activity_text !== null and $this->activity_text !== '') {
            $query->set($this->db->quoteName('activity_text') . ' = ' . $this->db->quote($this->activity_text));
        }
        
        if ($this->description !== null and $this->description !== '') {
            $query->set($this->db->quoteName('description') . ' = ' . $this->db->quote($this->description));
        }

        if (is_numeric($this->number) and (int)$this->number > 0) {
            $query->set($this->db->quoteName('number') . ' = ' . (int)$this->number);
        }

        $this->db->setQuery($query);
        $this->db->execute();

        return $this->db->insertid();
    }
}
