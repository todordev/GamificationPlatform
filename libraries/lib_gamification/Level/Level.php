<?php
/**
 * @package         Gamification
 * @subpackage      Levels
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\Level;

use Gamification\Mechanic\PointsBased;
use Gamification\Rank\Rank;
use Gamification\Points\Points;
use Joomla\DI\ContainerAwareInterface;
use Joomla\DI\ContainerAwareTrait;
use Prism\Utilities\StringHelper;
use Prism\Database\Table;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods used for managing a level.
 *
 * @package         Gamification
 * @subpackage      Levels
 */
class Level extends Table implements ContainerAwareInterface, PointsBased
{
    use ContainerAwareTrait;

    /**
     * Badge ID.
     *
     * @var integer
     */
    protected $id;

    protected $title;
    protected $points_number;
    protected $value;
    protected $published;
    protected $points_id;
    protected $rank_id;
    protected $group_id;

    /**
     * @var Rank
     */
    protected $rank;

    /**
     * @var Points
     */
    protected $points;

    /**
     * Get level ID.
     *
     * <code>
     * $levelId    = 1;
     *
     * $level      = new Gamification\Level\Level(\JFactory::getDbo());
     * $level->load($levelId);
     *
     * if (!$level->getId()) {
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
     * Get title.
     *
     * <code>
     * $levelId    = 1;
     *
     * $level      = new Gamification\Level\Level(\JFactory::getDbo());
     * $level->load($levelId);
     *
     * $points     = $level->getTitle();
     * </code>
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get points number need to be collected to accomplish this level.
     *
     * <code>
     * $levelId    = 1;
     *
     * $level      = new Gamification\Level\Level(\JFactory::getDbo());
     * $level->load($levelId);
     *
     * echo $level->getPointsNumber();
     * </code>
     *
     * @return int
     */
    public function getPointsNumber()
    {
        return $this->points_number;
    }

    /**
     * Get points ID used for the level.
     *
     * <code>
     * $levelId = 1;
     *
     * $level   = new Gamification\Level\Level(\JFactory::getDbo());
     * $level->load($levelId);
     *
     * $pointsId    = $level->getPointsId();
     * </code>
     *
     * @return int
     */
    public function getPointsId()
    {
        return (int)$this->points_id;
    }

    /**
     * Get rank ID used for the level.
     *
     * <code>
     * $levelId = 1;
     *
     * $level   = new Gamification\Level\Level(\JFactory::getDbo());
     * $level->load($levelId);
     *
     * $rankId    = $level->getRankId();
     * </code>
     *
     * @return int
     */
    public function getRankId()
    {
        return (int)$this->rank_id;
    }

    /**
     * Get group ID of the level.
     *
     * <code>
     * $levelId = 1;
     *
     * $level   = new Gamification\Level\Level(\JFactory::getDbo());
     * $level->load($levelId);
     *
     * $groupId = $level->getGroupId();
     * </code>
     *
     * @return int
     */
    public function getGroupId()
    {
        return (int)$this->group_id;
    }

    /**
     * Get the value of the level.
     *
     * <code>
     * $levelId = 1;
     *
     * $level   = new Gamification\Level\Level(\JFactory::getDbo());
     * $level->load($levelId);
     *
     * $value   = $level->getValue();
     * </code>
     *
     * @return int
     */
    public function getValue()
    {
        return (int)$this->value;
    }

    /**
     * Check for published level.
     *
     * <code>
     * $levelId     = 1;
     * $level       = new Gamification\Level\Level(\JFactory::getDbo());
     *
     * if(!$level->isPublished()) {
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
     * Load level data using the table object.
     *
     * <code>
     * $keys    = array(
     *    "group_id" => 1,
     *    "rank_id"  => 2,
     * );
     *
     * $level      = new Gamification\Level\Level(\JFactory::getDbo());
     * $level->load($keys);
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
            ->select('a.id, a.title, a.points_number, a.value, a.published, a.points_id, a.rank_id, a.group_id')
            ->from($this->db->quoteName('#__gfy_levels', 'a'));

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
    
    protected function prepareRankObject($rankId)
    {
        if ($rankId > 0) {
            $key = StringHelper::generateMd5Hash(Rank::class, $rankId);

            if ($this->container !== null) {
                if ($this->container->exists($key)) {
                    $this->rank = $this->container->get($key);
                } else {
                    $this->rank = new Rank($this->db);
                    $this->rank->setContainer($this->container);
                    $this->rank->load($rankId);

                    $this->container->set($key, $this->rank);
                }
            } else {
                $this->rank = new Rank($this->db);
                $this->rank->load($rankId);
            }
        }
    }

    /**
     * Get the rank where the level is positioned.
     *
     * <code>
     * $levelId = 1;
     * $level   = new Gamification\Level\Level(\JFactory::getDbo());
     *
     * $rank    = $level->getRank();
     * </code>
     *
     * @return null|Rank
     */
    public function getRank()
    {
        // Create a basic points object.
        if ($this->rank === null and $this->rank_id > 0) {
            $this->prepareRankObject($this->rank_id);
        }

        return $this->rank;
    }

    /**
     * Set Rank object.
     *
     * <code>
     * $rankId   = 1;
     * $rank     = new Gamification\Rank\Rank(\JFactory::getDbo());
     * $rank->load($rankId);
     *
     * $level      = new Gamification\Level\Level(\JFactory::getDbo());
     * $level->setRank($rank);
     * </code>
     *
     * @param Rank $rank
     * @throws \UnexpectedValueException
     * @throws \OutOfBoundsException
     *
     * @return self
     */
    public function setRank(Rank $rank)
    {
        $this->rank = $rank;

        if ($this->rank_id > 0 and $this->rank_id !== $rank->getId()) {
            throw new \UnexpectedValueException('The points ID already exists and it does not much with new Rank object.');
        }

        $this->rank_id = $rank->getId();

        // Add the points object in the container.
        $key = StringHelper::generateMd5Hash(Points::class, $this->rank_id);
        if ($this->container !== null and !$this->container->exists($key)) {
            $this->container->set($key, $this->rank);
        }

        return $this;
    }
    
    /**
     * Get points object.
     *
     * <code>
     * $levelId    = 1;
     * $level      = new Gamification\Level\Level(\JFactory::getDbo());
     * $level->load($levelId);
     *
     * $points     = $level->getPoints();
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
     * $level      = new Gamification\Level\Level(\JFactory::getDbo());
     * $level->setPoints($points);
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
     *        'points_number'    => 100,
     *        'value'    => 1,
     *        'published' => 1,
     *        'points_id' => 2,
     *        'rank_id'   => 3,
     *        'group_id'  => 4
     * );
     *
     * $level   = new Gamification\Level\Level(\JFactory::getDbo());
     * $level->bind($data);
     *
     * $level->store();
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
        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->update($this->db->quoteName('#__gfy_levels'))
            ->set($this->db->quoteName('title') . '  = ' . $this->db->quote($this->title))
            ->set($this->db->quoteName('points_number') . '  = ' . $this->db->quote($this->points_number))
            ->set($this->db->quoteName('value') . '  = ' . $this->db->quote($this->value))
            ->set($this->db->quoteName('published') . '  = ' . (int)$this->published)
            ->set($this->db->quoteName('points_id') . '  = ' . (int)$this->points_id)
            ->set($this->db->quoteName('rank_id') . '  = ' . (int)$this->rank_id)
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
            ->insert($this->db->quoteName('#__gfy_levels'))
            ->set($this->db->quoteName('title') . '  = ' . $this->db->quote($this->title))
            ->set($this->db->quoteName('points_number') . '  = ' . $this->db->quote($this->points_number))
            ->set($this->db->quoteName('value') . '  = ' . $this->db->quote($this->value))
            ->set($this->db->quoteName('published') . '  = ' . (int)$this->published)
            ->set($this->db->quoteName('points_id') . '  = ' . (int)$this->points_id)
            ->set($this->db->quoteName('rank_id') . '  = ' . (int)$this->rank_id)
            ->set($this->db->quoteName('group_id') . '  = ' . (int)$this->group_id);

        $this->db->setQuery($query);
        $this->db->execute();

        return $this->db->insertid();
    }
}
