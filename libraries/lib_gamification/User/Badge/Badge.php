<?php
/**
 * @package         Gamification\User
 * @subpackage      Badges
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\User\Badge;

use Joomla\DI\ContainerAwareInterface;
use Joomla\DI\ContainerAwareTrait;
use Prism\Database\Table;
use Prism\Utilities\StringHelper;
use Gamification\Badge\Badge as BasicBadge;

defined('JPATH_PLATFORM') or die;

/**
 * This is an object that represents user badge.
 *
 * @package         Gamification\User
 * @subpackage      Badges
 */
class Badge extends Table implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    /**
     * The ID of database record in table '#__gfy_userbadges'.
     *
     * @var int
     */
    protected $id;

    /**
     * This is the ID of the badge record in table '#__gfy_badges'.
     *
     * @var int
     */
    protected $badge_id;

    protected $user_id;
    protected $group_id;
    
    protected $badge;
    
    /**
     * Load user badge data.
     *
     * <code>
     * $keys = array(
     *       'user_id'  => 1,
     *       'group_id' => 2
     * );
     *
     * $userBadge     = new Gamification\User\Badge\Badge(\JFactory::getDbo());
     * $userBadge->load($keys);
     * </code>
     *
     * @param array $keys
     * @param array $options
     *
     * @throws \RuntimeException
     */
    public function load($keys, array $options = array())
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);
        $query
            ->select('a.id, a.badge_id, a.user_id, a.group_id')
            ->from($this->db->quoteName('#__gfy_userbadges', 'a'));

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
        $this->badge = null;
    }

    protected function prepareBadgeObject($badgeId)
    {
        $key = StringHelper::generateMd5Hash(BasicBadge::class, $badgeId);

        if ($this->container !== null) {
            if ($this->container->exists($key)) {
                $this->badge = $this->container->get($key);
            } else {
                $this->badge = new BasicBadge($this->db);
                $this->badge->load($badgeId);

                $this->container->set($key, $this->badge);
            }
        } else {
            $this->badge = new BasicBadge($this->db);
            $this->badge->load($badgeId);
        }
    }
    
    /**
     * Return the ID of the record.
     *
     * <code>
     * $keys = array(
     *       'user_id'  => 1,
     *       'group_id' => 2
     * );
     *
     * $userBadge   = new Gamification\User\Badge\Badge(\JFactory::getDbo());
     * $userBadge->load($keys);
     *
     * if (!$userBadge->getId()) {
     * ....
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
     * Return group ID of the record.
     *
     * <code>
     * $keys = array(
     *       'user_id'  => 1,
     *       'group_id' => 2
     * );
     *
     * $userBadge   = new Gamification\User\Badge\Badge(\JFactory::getDbo());
     * $userBadge->load($keys);
     *
     * echo $userBadge->getGroupId();
     * </code>
     *
     * @return int
     */
    public function getGroupId()
    {
        return (int)$this->group_id;
    }

    /**
     * Return the badge object.
     *
     * <code>
     * $keys = array(
     *       'user_id'   => 1,
     *       'badge_id' => 2
     * );
     *
     * $userPoints  = new Gamification\User\Badge\Badge(JFactory::getDbo());
     * $userPoints->load($keys);
     *
     * $badge      = $userPoints->getPoints();
     * </code>
     *
     * @throws \RuntimeException
     *
     * @return BasicBadge
     */
    public function getBadge()
    {
        // Create a basic points object.
        if ($this->badge === null and $this->badge_id > 0) {
            $this->prepareBadgeObject($this->badge_id);
        }

        return $this->badge;
    }

    /**
     * Set Badge object.
     * This method will reset badge ID, user ID the ID of the record.
     *
     * <code>
     * $userBadge  = new Gamification\User\Badge\Badge(JFactory::getDbo());
     *
     * $basicBadge = new Gamification\Points\Points(JFactory::getDbo());
     * $userBadge->setBadge($basicBadge);
     * </code>
     *
     * @param BasicBadge $badge
     *
     * @throws \OutOfBoundsException
     *
     * @return self
     */
    public function setBadge(BasicBadge $badge)
    {
        $this->badge         = $badge;

        $this->id            = null;
        $this->user_id       = null;
        $this->badge_id      = $badge->getId();

        // Add the badge object in the container.
        $key = StringHelper::generateMd5Hash(BasicBadge::class, $this->badge_id);
        if ($this->container !== null and !$this->container->exists($key)) {
            $this->container->set($key, $this->badge);
        }
        
        return $this;
    }

    protected function updateObject()
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->update($this->db->quoteName('#__gfy_userbadges'))
            ->set($this->db->quoteName('user_id')  . '=' . (int)$this->user_id)
            ->set($this->db->quoteName('group_id') . '=' . (int)$this->group_id)
            ->set($this->db->quoteName('badge_id') . '=' . (int)$this->badge_id)
            ->where($this->db->quoteName('id') . '=' . (int)$this->id);

        $this->db->setQuery($query);
        $this->db->execute();
    }

    protected function insertObject()
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->insert($this->db->quoteName('#__gfy_userbadges'))
            ->set($this->db->quoteName('user_id')  . '=' . (int)$this->user_id)
            ->set($this->db->quoteName('group_id') . '=' . (int)$this->group_id)
            ->set($this->db->quoteName('badge_id') . '=' . (int)$this->badge_id);

        $this->db->setQuery($query);
        $this->db->execute();

        return $this->db->insertid();
    }

    /**
     * Save the data to the database.
     *
     * <code>
     * $data = array(
     *        'user_id'   => 3,
     *        'group_id'  => 4,
     *        'badge_id'  => 2
     * );
     *
     * $userBadge   = new Gamification\User\Badge\Badge(\JFactory::getDbo());
     * $userBadge->bind($data);
     * $userBadge->store();
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
}
