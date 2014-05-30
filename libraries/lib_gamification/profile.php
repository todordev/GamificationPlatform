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
 * This class contains methods used for gamifying users.
 *
 * @package         GamificationPlatform
 * @subpackage      GamificationLibrary
 *
 * @todo            It is not completed. Complete it!
 */
class GamificationProfile
{
    public $id = null;
    public $name = null;
    public $username = null;

    protected $db = null;

    /**
     * Initialize user profile and his gamification units.
     *
     * <code>
     *
     * $userId   = 1;
     *
     * $profile  = new GamificationProfile();
     * $profile->load($userId);
     *
     * </code>
     *
     * @param integer $id User ID
     * @param array   $options This options are used for specifying the things for loading.
     */
    public function __construct($id = 0, $options = array())
    {
        $this->db = JFactory::getDbo();

        if (!empty($id)) {
            $this->load($id, $options);
        }
    }

    /**
     * Load profile data.
     *
     * <code>
     *
     * $userId   = 1;
     *
     * $profile  = new GamificationProfile();
     * $profile->load($userId);
     *
     * </code>
     *
     * @param integer $id User ID
     * @param array   $options This options are used for specifying the things for loading.
     *
     */
    public function load($id = null, $options = array())
    {
        // Create a new query object.
        $query = $this->db->getQuery(true);

        $query
            ->select("a.id, a.name, a.username")
            ->from($this->db->quoteName("#__users", "a"))
            ->where("a.id = " . (int)$id);

        $this->db->setQuery($query);
        $result = $this->db->loadAssoc();

        if (!empty($result)) {
            $this->bind($result);
        }
    }

    /**
     * Set the data to the object parameters.
     *
     * <code>
     *
     * $data = array(
     *        "name"        => "Todor",
     *        "username"  => "ITPrism"
     * );
     *
     * $profile   = new GamificationProfile();
     * $profile->bind($data);
     *
     * </code>
     *
     * @param array $src
     * @param array $ignored
     */
    public function bind($src, $ignored = array())
    {
        foreach ($src as $key => $value) {
            if (!in_array($key, $ignored)) {
                $this->$key = $value;
            }
        }
    }
}
