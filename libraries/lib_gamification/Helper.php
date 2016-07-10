<?php
/**
 * @package      Gamification
 * @subpackage   Helpers
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification;

use Prism\Integration\Activity;
use Prism\Integration\Notification;
use Joomla\Utilities\ArrayHelper;
use Joomla\Registry\Registry;

defined('JPATH_PLATFORM') or die;

/**
 * This class provides methods using as helpers in some processes.
 *
 * @package      Gamification
 * @subpackage   Helpers
 */
abstract class Helper
{
    public static function storeActivity($notice, Registry $options)
    {
        $builder  = new Activity\Factory($options);
        $activity = $builder->create();

        $activity->setContent($notice);
        $activity->store();
    }

    public static function sendNotification($message, Registry $options)
    {
        $builder  = new Notification\Factory($options);
        $notifier = $builder->create();

        $notifier->setContent($message);
        $notifier->send();
    }

    public static function getGroupsOptions()
    {
        $db    = \JFactory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select('a.id AS value, a.name AS text')
            ->from($db->quoteName('#__gfy_groups', 'a'));

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
            ->select('a.id AS value, a.title AS text')
            ->from($db->quoteName('#__gfy_ranks', 'a'));

        $db->setQuery($query);
        $results = $db->loadAssocList();

        if (!$results) {
            $results = array();
        }

        return $results;
    }

    /**
     * Prepare custom data.
     *
     * @param array $data
     *
     * @throws \InvalidArgumentException
     * @return string
     */
    public static function prepareCustomData($data)
    {
        $customData = ArrayHelper::getValue($data, 'custom_data', [], 'array');

        $results = array();
        $filter  = \JFilterInput::getInstance();

        foreach ($customData as $values) {
            $key   = trim($filter->clean($values['key'], 'cmd'));
            $value = trim($filter->clean($values['value'], 'string'));

            if (!$key) {
                continue;
            }

            $results[$key] = $value;
        }

        $customData = new Registry($results);

        return $customData->toString();
    }

    /**
     * Prepare rewards that will be given for accomplishing this unit.
     *
     * @param array  $data
     *
     * @throws \InvalidArgumentException
     * @return string
     */
    public static function prepareRewards($data)
    {
        $rewards = ArrayHelper::getValue($data, 'rewards', [], 'array');

        $rewards['points'] = trim($rewards['points']);
        $rewards['points_id'] = (int)$rewards['points_id'];

        // Prepare badge IDs.
        $results = array();
        foreach ($rewards['badge_id'] as $itemId) {
            $itemId   = (int)$itemId;
            if (!$itemId) {
                continue;
            }

            $results[] = $itemId;
        }
        $rewards['badge_id'] = $results;

        // Prepare rank IDs.
        $results = array();
        foreach ($rewards['rank_id'] as $itemId) {
            $itemId   = (int)$itemId;
            if (!$itemId) {
                continue;
            }

            $results[] = $itemId;
        }
        $rewards['rank_id'] = $results;

        // Prepare badge IDs.
        $results = array();
        foreach ($rewards['reward_id'] as $itemId) {
            $itemId   = (int)$itemId;
            if (!$itemId) {
                continue;
            }

            $results[] = $itemId;
        }
        $rewards['reward_id'] = $results;

        $rewards = new Registry($rewards);

        return $rewards->toString();
    }
}
