<?php
/**
 * @package         Gamification
 * @subpackage      Ranks
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\Rank;

use Joomla\DI\ContainerAwareInterface;
use Joomla\DI\ContainerAwareTrait;
use Prism\Utilities\StringHelper;
use Prism\Database\Table;
use Gamification\Points\Points;
use Gamification\Mechanic\PointsBased;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods used for managing a rank.
 *
 * @package         Gamification
 * @subpackage      Ranks
 */
class Rank extends Table implements ContainerAwareInterface, PointsBased
{
    use ContainerAwareTrait;
    
    /**
     * Rank ID.
     *
     * @var integer
     */
    protected $id;

    protected $title;
    protected $description;
    protected $activity_text;
    protected $note;
    protected $points_number;
    protected $image;
    protected $published;
    protected $group_id;
    protected $points_id;

    /**
     * @var Points
     */
    protected $points;

    /**
     * Get rank ID.
     *
     * <code>
     * $rankId    = 1;
     *
     * $rank      = new Gamification\Rank\Rank(\JFactory::getDbo());
     * $rank->load($rankId);
     *
     * if (!$rank->getId()) {
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
     * Get rank title.
     *
     * <code>
     * $rankId = 1;
     * $rank   = new Gamification\Rank\Rank(\JFactory::getDbo());
     * $rank->load($rankId);
     *
     * $title  = $rank->getTitle();
     * </code>
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get rank description.
     *
     * <code>
     * $rankId = 1;
     * $rank   = new Gamification\Rank\Rank(\JFactory::getDbo());
     * $rank->load($rankId);
     *
     * echo $rank->getDescription();
     * </code>
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Get rank note.
     *
     * <code>
     * $rankId = 1;
     * $rank   = new Gamification\Rank\Rank(\JFactory::getDbo());
     * $rank->load($rankId);
     *
     * echo $rank->getNote();
     * </code>
     *
     * @return string
     */
    public function getNote()
    {
        return $this->note;
    }
    
    /**
     * Get points number need to be collected to accomplish this rank.
     *
     * <code>
     * $rankId    = 1;
     *
     * $rank      = new Gamification\Rank\Rank(\JFactory::getDbo());
     * $rank->load($rankId);
     *
     * $points     = $rank->getPointsNumber();
     * </code>
     *
     * @return number
     */
    public function getPointsNumber()
    {
        return $this->points_number;
    }

    /**
     * Get the rank where the level is positioned.
     *
     * <code>
     * $rankId    = 1;
     *
     * $rank      = new Gamification\Rank\Rank(\JFactory::getDbo());
     * $rank->load($rankId);
     *
     * $pointsId    = $rank->getPointsId();
     * </code>
     *
     * @return int
     */
    public function getPointsId()
    {
        return (int)$this->points_id;
    }

    /**
     * Get the group ID.
     *
     * <code>
     * $rankId    = 1;
     *
     * $rank      = new Gamification\Rank\Rank(\JFactory::getDbo());
     * $rank->load($rankId);
     *
     * $groupId    = $rank->getGroupId();
     * </code>
     *
     * @return int
     */
    public function getGroupId()
    {
        return (int)$this->group_id;
    }
    
    /**
     * Get rank image.
     *
     * <code>
     * $rankId = 1;
     * $rank   = new Gamification\Rank\Rank(\JFactory::getDbo());
     * $rank->load($rankId);
     *
     * $image  = $rank->getImage();
     * </code>
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Return the text that will be used as activity information. It is possible
     * to replace placeholders with dynamically generated data.
     *
     * <code>
     * $rankId    = 1;
     * $rank      = new Gamification\Rank\Rank(\JFactory::getDbo());
     * $rank->load($rankId);
     *
     * $data = array(
     *     "name" => "John Dow",
     *     "title" => "...",
     *     "target" => "..."
     * );
     *
     * echo $rank->getActivityText($data);
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
     * Check for published rank.
     *
     * <code>
     * $rankId     = 1;
     * $rank       = new Gamification\Rank\Rank(\JFactory::getDbo());
     *
     * if(!$rank->isPublished()) {
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
     * Load rank data.
     *
     * <code>
     * $keys    = array(
     *    "group_id" => 1,
     *    "points_id"  => 2
     * );
     *
     * $rank   = new Gamification\Rank\Rank(\JFactory::getDbo());
     * $rank->load($keys);
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
            ->select('a.id, a.title, a.description, a.note, a.activity_text, a.image, a.published, a.points_number, a.points_id, a.group_id')
            ->from($this->db->quoteName('#__gfy_ranks', 'a'));

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
     * $rankId    = 1;
     * $rank      = new Gamification\Rank\Rank(\JFactory::getDbo());
     * $rank->load($rankId);
     *
     * $points     = $rank->getPoints();
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
     * $rank      = new Gamification\Rank\Rank(\JFactory::getDbo());
     * $rank->setPoints($points);
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
     *        'title'    => '......',
     *        'description'    => '......',
     *        'activity_text'    => '......',
     *        'points_number'    => 100,
     *        'image'    => '....',
     *        'note'    => '....',
     *        'published' => 1,
     *        'points_id' => 2,
     *        'group_id'  => 4
     * );
     *
     * $rank   = new Gamification\Rank\Rank(\JFactory::getDbo());
     * $rank->bind($data);
     * $rank->store();
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
            ->update($this->db->quoteName('#__gfy_ranks'))
            ->set($this->db->quoteName('title') . '  = ' . $this->db->quote($this->title))
            ->set($this->db->quoteName('description') . '  = ' . $description)
            ->set($this->db->quoteName('activity_text') . '  = ' . $activityText)
            ->set($this->db->quoteName('points_number') . '  = ' . $this->db->quote($this->points_number))
            ->set($this->db->quoteName('image') . '  = ' . $this->db->quote($this->image))
            ->set($this->db->quoteName('note') . '  = ' . $note)
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
            ->insert($this->db->quoteName('#__gfy_ranks'))
            ->set($this->db->quoteName('title') . '  = ' . $this->db->quote($this->title))
            ->set($this->db->quoteName('points_number') . '  = ' . $this->db->quote($this->points_number))
            ->set($this->db->quoteName('image') . '  = ' . $this->db->quote($this->image))
            ->set($this->db->quoteName('published') . '  = ' . (int)$this->published)
            ->set($this->db->quoteName('points_id') . '  = ' . (int)$this->points_id)
            ->set($this->db->quoteName('group_id') . '  = ' . (int)$this->group_id);

        if ($this->note !== null and $this->note !== '') {
            $query->set($this->db->quoteName('note') . '  = ' . $this->db->quote($this->note));
        }

        if ($this->description !== null and $this->description !== '') {
            $query->set($this->db->quoteName('description') . '  = ' . $this->db->quote($this->description));
        }

        if ($this->activity_text !== null and $this->activity_text !== '') {
            $query->set($this->db->quoteName('activity_text') . '  = ' . $this->db->quote($this->activity_text));
        }

        $this->db->setQuery($query);
        $this->db->execute();

        return $this->db->insertid();
    }
}
