<?php
/**
 * @package      Gamification
 * @subpackage   Observers
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification\Observer\User;

use Joomla\Utilities\ArrayHelper;
use Gamification\Observer\PointsObserver;
use Gamification\User\Points;
use Gamification\Helper;

defined('JPATH_PLATFORM') or die;

/**
 * Abstract class defining methods that can be
 * implemented by an Observer class of a JTable class (which is an Observable).
 * Attaches $this Observer to the $table in the constructor.
 * The classes extending this class should not be instantiated directly, as they
 * are automatically instantiated by the JObserverMapper
 *
 * @package      Gamification
 * @subpackage   Observers
 * @link         http://docs.joomla.org/JTableObserver
 * @since        3.1.2
 */
class Leveling extends PointsObserver
{
    /**
     * Context that are allowed to be processed.
     *
     * @var array
     */
    protected $allowedContext = array("com_user.registration", "com_content.article");

    /**
     * The pattern for this table's TypeAlias
     *
     * @var    string
     * @since  3.1.2
     */
    protected $typeAliasPattern = null;

    protected $sendNotification = false;
    protected $storeActivity    = false;

    /**
     * Creates the associated observer instance and attaches it to the $observableObject
     * $typeAlias can be of the form "{variableName}.type", automatically replacing {variableName} with table-instance variables variableName
     *
     * @param   \JObservableInterface $observableObject The subject object to be observed
     * @param   array                $params           ( 'typeAlias' => $typeAlias )
     *
     * @return  self
     *
     * @since   3.1.2
     */
    public static function createObserver(\JObservableInterface $observableObject, $params = array())
    {
        $observer = new self($observableObject);
        $observer->typeAliasPattern = ArrayHelper::getValue($params, 'typeAlias');
        $observer->sendNotification = ArrayHelper::getValue($params, 'send_notification', false, "bool");
        $observer->storeActivity    = ArrayHelper::getValue($params, 'store_activity', false, "bool");

        return $observer;
    }

    /**
     * Pre-processor for $table->store($data)
     *
     * @param   Points $userPoints
     * @param   array $options
     *
     * @return  void
     */
    public function onAfterPointsIncrease($userPoints, $options = array())
    {
        // Get the context.
        $alias = (isset($options["context"])) ? $options["context"] : "";

        // Check for allowed context.
        if (!in_array($alias, $this->allowedContext)) {
            return;
        }

        $keys = array(
            "user_id"  => $userPoints->getUserId(),
            "group_id" => $userPoints->getGroupId()
        );

        // Get user level
        $level = new Points\Level(\JFactory::getDbo());
        $level->load($keys);

        $level->setUserPoints($userPoints);

        if (!$level->getId()) { // Create a level record
            $level->startLeveling($keys);
        } else { // Level UP

            if ($level->levelUp($options) and ($this->storeActivity or $this->sendNotification)) {

                $params = \JComponentHelper::getParams("com_gamification");

                $user = \JFactory::getUser($userPoints->getUserId());

                $optionsActivitiesNotifications = array(
                    "social_platform" => "",
                    "user_id" => $user->get("id"),
                    "context_id" => $user->get("id"),
                    "app" => "gamification.level"
                );

                $activityService = $params->get("integration_activities");
                if ($this->storeActivity and $activityService) {
                    $optionsActivitiesNotifications["social_platform"] = $activityService;

                    $message = \JText::sprintf("LIB_GAMIFICATION_LEVELING_LEVEL_UP", $user->get("name"), $level->getLevel());
                    Helper::storeActivity($message, $optionsActivitiesNotifications);
                }

                $notificationService = $params->get("integration_notifications");
                if ($this->sendNotification and $notificationService) {
                    $optionsActivitiesNotifications["social_platform"] = $notificationService;

                    $message = \JText::sprintf("LIB_GAMIFICATION_LEVELING_NOTIFICATION", $level->getLevel());
                    Helper::sendNotification($message, $optionsActivitiesNotifications);
                }
            }
        }
    }
}
