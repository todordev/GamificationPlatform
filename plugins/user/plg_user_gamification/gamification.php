<?php
/**
 * @package      Gamification
 * @subpackage   Plugins
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

/**
 * This class provides functionality
 * for creating accounts used for storing
 * and managing gamification units.
 *
 * @package        Gamification
 * @subpackage     Plugins
 */
class plgUserGamification extends JPlugin
{
    /**
     * @var Joomla\Registry\Registry
     */
    public $params;

    /**
     * Method is called after user data is stored in the database
     *
     * @param    array   $user    Holds the new user data.
     * @param    boolean $isNew   True if a new user is stored.
     * @param    boolean $success True if user was succesfully stored in the database.
     * @param    string  $msg     Message.
     *
     * <code>
     *
     * </code>
     * @return    void
     * @since    1.6
     * @throws    Exception on error.
     */
    public function onUserAfterSave($user, $isNew, $success, $msg)
    {
        if ($isNew and $this->params->get("points_give", 0)) {
            // Give points
            $this->givePoints($user);
        }
    }

    /**
     * Method is called after user log in to the system.
     *
     * @param    array $user    An associative array of JAuthenticateResponse type.
     * @param    array $options An associative array containing these keys: ["remember"] => bool, ["return"] => string, ["entry_url"] => string.
     *
     * @return    void
     * @since    1.6
     * @throws    Exception on error.
     *
     * @todo     Remove this method because it is used only for testing.
     */
    public function onUserLogin($user, $options)
    {
        // Get user id
        $userName = JArrayHelper::getValue($user, 'username');

        $db    = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select("a.id, a.name, a.username, a.email, a.registerDate")
            ->from($db->quoteName("#__users") . " AS a")
            ->where("a.username = " . $db->quote($userName));

        $db->setQuery($query, 0, 1);
        $user = $db->loadAssoc();

        // Give points
        if ($this->params->get("points_give", 0)) {
            $this->givePoints($user);
        }

    }

    /**
     * Add points to user account after registration.
     *
     * @param array $user
     */
    protected function givePoints($user)
    {
        $userId = JArrayHelper::getValue($user, 'id');
        $name   = JArrayHelper::getValue($user, 'name');

        $pointsTypesValues = $this->params->get("points_types", 0);

        // Parse point types
        $pointsTypes = array();
        if (!empty($pointsTypesValues)) {
            $pointsTypes = json_decode($pointsTypesValues);
        }

        if (!empty($pointsTypes)) {

            $this->loadLanguage();

            jimport("gamification.points");
            jimport("gamification.userpoints");

            foreach ($pointsTypes as $pointsType) {

                $points = GamificationPoints::getInstance($pointsType->id);

                if ($points->getId() and $points->isPublished()) {

                    $keys = array(
                        "user_id"   => $userId,
                        "points_id" => $points->getId()
                    );

                    $userPoints = new GamificationUserPoints($keys);

                    $userPoints->increase($pointsType->value);
                    $userPoints->store();



                    // Send notification and store activity

                    // Notification service
                    $iService = $this->params->get("notification_integration");
                    if (!empty($iService)) {

                        $message = JText::sprintf("PLG_USER_GAMIFICATION_NOTIFICATION_AFTER_REGISTRATION", $pointsType->value, $points->getTitle());
                        $this->notify($iService, $message, $userId);

                    }

                    // Activity service
                    $iService = $this->params->get("activity_integration");
                    if (!empty($iService)) {

                        $points = htmlspecialchars($pointsType->value . " " . $userPoints->getTitle(), ENT_QUOTES, "UTF-8");
                        $notice = JText::sprintf("PLG_USER_GAMIFICATION_ACTIVITY_AFTER_REGISTRATION", $name, $points);
                        $this->storeActivity($iService, $notice, $userId);

                    }

                }

            }


        }

    }

    public function notify($service, $message, $userId)
    {
        jimport("itprism.integrate.notification");
        $notifier = ITPrismIntegrateNotification::factory($service);

        $notifier->setNote($message);
        $notifier->setUserId($userId);

        $notifier->send();
    }

    public function storeActivity($service, $notice, $userId)
    {
        jimport("itprism.integrate.activity");

        $activity = ITPrismIntegrateActivity::factory($service);
        $activity->setInfo($notice);
        $activity->setUserId($userId);

        // Application to JomSocial object
        if (strcmp("jomsocial", $service) == 0) {
            $activity->setApp("gamification.points");
        }

        $activity->store();
    }
}
