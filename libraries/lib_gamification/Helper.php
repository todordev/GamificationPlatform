<?php
/**
 * @package      Gamification
 * @subpackage   Helpers
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification;

use Prism\Integration\Activity;
use Prism\Integration\Notification;

defined('JPATH_PLATFORM') or die;

/**
 * This class provides methods using as helpers in some processes.
 *
 * @package      Gamification
 * @subpackage   Helpers
 */
abstract class Helper
{
    public static function storeActivity($notice, $options)
    {
        $builder = new Activity\Builder($options);
        $builder->build();

        $activity = $builder->getActivity();

        $activity->setContent($notice);
        $activity->store();
    }

    public static function sendNotification($message, $options)
    {
        $builder = new Notification\Builder($options);
        $builder->build();

        $notifier = $builder->getNotification();

        $notifier->setContent($message);
        $notifier->send();
    }

    public static function getGroupsOptions()
    {
        $db    = \JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select("a.id AS value, a.name AS text")
            ->from($db->quoteName("#__gfy_groups", "a"));

        $db->setQuery($query);
        $results = $db->loadAssocList();

        if (!$results) {
            $results = array();
        }

        return $results;
    }

    public static function getRanksOptions()
    {
        $db    = \JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select("a.id AS value, a.title AS text")
            ->from($db->quoteName("#__gfy_ranks", "a"));

        $db->setQuery($query);
        $results = $db->loadAssocList();

        if (!$results) {
            $results = array();
        }

        return $results;
    }
}
