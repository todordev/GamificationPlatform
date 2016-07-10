<?php
/**
 * @package         Gamification\User
 * @subpackage      Points
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\User\Points;

use Joomla\DI\ContainerAwareInterface;
use Joomla\DI\ContainerAwareTrait;
use Prism\Database\Table;
use Prism\Utilities\StringHelper;
use Joomla\Utilities\ArrayHelper;
use Gamification\Points\Points as BasicPoints;

defined('JPATH_PLATFORM') or die;

/**
 * This class contains methods that are used for managing user points.
 * The user points are collected units by users.
 *
 * @package         Gamification\User
 * @subpackage      Points
 */
class Points extends Table implements ContainerAwareInterface
{
    use ContainerAwareTrait;
    
    /**
     * Users points ID.
     *
     * @var int
     */
    protected $id;

    protected $user_id;
    protected $points_id;
    protected $points_number = 0;

    /**
     * @var BasicPoints
     */
    protected $points;

    /**
     * Load user points using some indexes - user_id, abbr or points_id.
     *
     * <code>
     * // Load data by points ID.
     * $keys = array(
     *       'user_id'   => 1,
     *       'points_id' => 2
     * );
     *
     * $userPoints    = new Gamification\User\Points\Points(JFactory::getDbo());
     * $userPoints->load($keys);
     *
     * // Load data by abbreviation.
     * $keys = array(
     *       'user_id'  => 1,
     *       'abbr'     => 'P'
     * );
     *
     * $userPoints    = new Gamification\User\Points\Points(JFactory::getDbo());
     * $userPoints->load($keys);
     * </code>
     *
     * @param array $keys
     * @param array $options
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws \UnexpectedValueException
     * @throws \OutOfBoundsException
     */
    public function load($keys, array $options = array())
    {
        $id       = ArrayHelper::getValue($keys, 'id', 0, 'int');
        $userId   = ArrayHelper::getValue($keys, 'user_id', 0, 'int');
        $pointsId = ArrayHelper::getValue($keys, 'points_id', 0, 'int');

        $query  = $this->db->getQuery(true);
        $query
            ->select('a.id, a.points_number, a.points_id, a.user_id')
            ->from($this->db->quoteName('#__gfy_userpoints', 'a'));

        if ($id > 0) {
            $query->where('a.id = ' . (int)$id);
        }
        
        if ($pointsId) {
            $query->where('a.points_id = ' . (int)$pointsId);
        }

        if ($userId > 0) {
            $query->where('a.user_id = ' . (int)$userId);
        }

        $this->db->setQuery($query);
        $result = (array)$this->db->loadAssoc();

        $this->bind($result);
        $this->points = null;

        if (count($result) > 0) {
            $this->preparePointsObject($this->points_id);
        }
    }

    protected function preparePointsObject($pointsId)
    {
        $key = StringHelper::generateMd5Hash(BasicPoints::class, $pointsId);

        if ($this->container !== null) {
            if ($this->container->exists($key)) {
                $this->points = $this->container->get($key);
            } else {
                $this->points = new BasicPoints($this->db);
                $this->points->load($pointsId);

                $this->container->set($key, $this->points);
            }
        } else {
            $this->points = new BasicPoints($this->db);
            $this->points->load($pointsId);
        }
    }

    protected function updateObject()
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->update($this->db->quoteName('#__gfy_userpoints'))
            ->set($this->db->quoteName('points_number') .' = ' . (int)$this->points_number)
            ->where($this->db->quoteName('id') .' = ' . (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();
    }

    protected function insertObject()
    {
        if (!$this->user_id or !$this->points_id) {
            throw new \UnexpectedValueException('It is missing user ID or points ID.');
        }
        
        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->insert($this->db->quoteName('#__gfy_userpoints'))
            ->set($this->db->quoteName('points_number') . ' = ' . (int)$this->points_number)
            ->set($this->db->quoteName('user_id') . ' = ' . (int)$this->user_id)
            ->set($this->db->quoteName('points_id') .' = ' . (int)$this->points_id);

        $this->db->setQuery($query);
        $this->db->execute();

        return $this->db->insertid();
    }
    
    /**
     * Decrease user points.
     *
     * <code>
     * $keys = array(
     *       'user_id'   => 1,
     *       'points_id' => 2
     * );
     *
     * $userPoints   = new Gamification\User\Points\Points(JFactory::getDbo());
     * $userPoints->load($keys);
     *
     * $userPoints->decrease(100);
     * $userPoints->store();
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

    /**
     * Return the number of points and abbreviation as a string.
     *
     * <code>
     * $keys = array(
     *       'user_id'   => 1,
     *       'points_id' => 2
     * );
     *
     * $userPoints  = new Gamification\User\Points\Points(JFactory::getDbo());
     * $userPoints->load($keys);
     *
     * $amount = $userPoints->__toString();
     *
     * // Alternatively
     * echo $userPoints;
     * </code>
     *
     * @return string
     */
    public function __toString()
    {
        if ($this->points !== null) {
            return $this->points_number . ' ' . $this->points->getAbbr();
        }
        
        return (string)$this->points_number;
    }

    /**
     * Return ID of a record.
     *
     * <code>
     * $keys = array(
     *       'user_id'   => 1,
     *       'points_id' => 2
     * );
     *
     * $userPoints  = new Gamification\User\Points\Points(JFactory::getDbo());
     * $userPoints->load($keys);
     *
     * if (!$userPoints->getId()) {
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
     * Return the number of points.
     *
     * <code>
     * $keys = array(
     *       'user_id'   => 1,
     *       'points_id' => 2
     * );
     *
     * $userPoints  = new Gamification\User\Points\Points(JFactory::getDbo());
     * $userPoints->load($keys);
     *
     * $points      = $userPoints->getPointsNumber();
     * </code>
     *
     * @return int
     */
    public function getPointsNumber()
    {
        return (int)$this->points_number;
    }

    /**
     * Return a points object.
     *
     * <code>
     * $keys = array(
     *       'user_id'   => 1,
     *       'points_id' => 2
     * );
     *
     * $userPoints  = new Gamification\User\Points\Points(JFactory::getDbo());
     * $userPoints->load($keys);
     *
     * $points      = $userPoints->getPoints();
     * </code>
     *
     * @throws \RuntimeException
     *
     * @return BasicPoints
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
     * Set a points object.
     * This method will reset points number, user ID and the ID of the record.
     *
     * <code>
     * $basicPoints = new Gamification\Points\Points(JFactory::getDbo());
     *
     * $userPoints  = new Gamification\User\Points\Points(JFactory::getDbo());
     * $userPoints->setPoints($basicPoints);
     * </code>
     *
     * @param BasicPoints $points
     *
     * @return self
     */
    public function setPoints(BasicPoints $points)
    {
        $this->points        = $points;

        $this->id            = null;
        $this->user_id       = null;
        $this->points_id     = $points->getId();
        $this->points_number = 0;

        return $this;
    }

    /**
     * Return user ID.
     *
     * <code>
     * $keys = array(
     *       'user_id'   => 1,
     *       'points_id' => 2
     * );
     *
     * $userPoints  = new Gamification\User\Points\Points(JFactory::getDbo());
     * $userPoints->load($keys);
     *
     * $userId = $userPoints->getUserId();
     * </code>
     *
     * @return int
     */
    public function getUserId()
    {
        return (int)$this->user_id;
    }

    /**
     * Return points ID.
     *
     * <code>
     * $keys = array(
     *       'user_id'   => 1,
     *       'points_id' => 2
     * );
     *
     * $userPoints  = new Gamification\User\Points\Points(JFactory::getDbo());
     * $userPoints->load($keys);
     *
     * $pointsId = $userPoints->getPointsId();
     * </code>
     *
     * @return int
     */
    public function getPointsId()
    {
        return (int)$this->points_id;
    }

    /**
     * Add points to current one.
     *
     * <code>
     * $keys = array(
     *       'user_id'   => 1,
     *       'points_id' => 2
     * );
     *
     * $userPoints  = new Gamification\User\Points\Points(JFactory::getDbo());
     * $userPoints->load($keys);
     *
     * $userPoints->add(1000);
     * </code>
     *
     * @param int $points
     *
     * @return int
     */
    public function add($points)
    {
        $this->points_number += (int)$points;
    }
    
    /**
     * Subtract points from current one.
     *
     * <code>
     * $keys = array(
     *       'user_id'   => 1,
     *       'points_id' => 2
     * );
     *
     * $userPoints  = new Gamification\User\Points\Points(JFactory::getDbo());
     * $userPoints->load($keys);
     *
     * $userPoints->subtract(1000);
     * </code>
     *
     * @param int $points
     *
     * @return int
     */
    public function subtract($points)
    {
        $this->points_number -= (int)$points;
    }

    /**
     * Create a record to the database creating first record
     * where the user will collect points.
     *
     * <code>
     * $keys = array(
     *       'user_id'  => 1,
     *       'points_id' => 2
     * );
     *
     * $data = array(
     *     'user_id'  => 1,
     *     'points_id' => 2,
     *     'points_number' => 300
     * );
     *
     * $userPoints   = new Gamification\User\Points\Points(JFactory::getDbo());
     * $userPoints->load($keys);
     *
     * $userPoints->startCollectingPoints($data);
     * </code>
     *
     * @param array $data
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws \UnexpectedValueException
     */
    public function startCollectingPoints(array $data = array())
    {
        if (empty($data['user_id'])) {
            throw new \InvalidArgumentException(\JText::_('LIB_GAMIFICATION_ERROR_INVALID_PARAMETER_USER_ID'));
        }

        if (empty($data['points_id'])) {
            throw new \InvalidArgumentException(\JText::_('LIB_GAMIFICATION_ERROR_INVALID_PARAMETER_POINTS_ID'));
        }

        // Reset basic Points object if it does not match with points ID.
        if (($this->points !== null and $data['points_id'] > 0) and ($this->points->getId() !== $data['points_id'])) {
            $this->points = null;
        }
        
        // Create a basic points object.
        if ($this->points === null and $data['points_id'] > 0) {
            $this->preparePointsObject($data['points_id']);
        }
        
        $this->id            = null;
        $this->points_id     = $this->points->getId();
        $this->user_id       = (int)$data['user_id'];
        $this->points_number = array_key_exists('points_number', $data) ? $data['points_number'] : 0;
        
        $this->store();
    }
}
