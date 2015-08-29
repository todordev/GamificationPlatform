<?php
/**
 * @package      Gamification Platform
 * @subpackage   Plugins
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport("Prism.init");
jimport("Gamification.init");

/**
 * This plugin calculates and updates the game mechanics.
 * This plugin use only points.
 *
 * @package      Gamification Platform
 * @subpackage   Plugins
 */
class plgSystemGamification extends JPlugin
{
    /**
     * Affects constructor behavior. If true, language files will be loaded automatically.
     *
     * @var    boolean
     * @since  3.1
     */
    protected $autoloadLanguage = true;

    protected $userId;

    /**
     * Register some observers to update mechanic values.
     */
    public function onAfterInitialise()
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        if ($app->isAdmin()) {
            return;
        }

        $document = JFactory::getDocument();
        /** @var $document JDocumentHTML */

        $type = $document->getType();
        if (strcmp("html", $type) != 0) {
            return;
        }

        // Register Observers

        // Register observer for leveling when system increase points value.
        if ($this->params->get("enable_leveling", 0)) {
            $options = array(
                'typeAlias' => 'com_gamification.leveling',
                'send_notification' => $this->params->get("leveling_send_notification", 0),
                'store_activity' => $this->params->get("leveling_store_activity", 0)
            );

            JObserverMapper::addObserverClassToClass('Gamification\\Observer\\User\\Leveling', 'Gamification\\User\\Points', $options);
        }

        if ($this->params->get("enable_badging", 0)) {
            $options = array(
                'typeAlias' => 'com_gamification.badging',
                'send_notification' => $this->params->get("badging_send_notification", 0),
                'store_activity' => $this->params->get("badging_store_activity", 0)
            );

            JObserverMapper::addObserverClassToClass('Gamification\\Observer\\User\\Badging', 'Gamification\\User\\Points', $options);
        }

        if ($this->params->get("enable_ranking", 0)) {
            $options = array(
                'typeAlias' => 'com_gamification.ranking',
                'send_notification' => $this->params->get("ranking_send_notification", 0),
                'store_activity' => $this->params->get("ranking_store_activity", 0)
            );

            JObserverMapper::addObserverClassToClass('Gamification\\Observer\\User\\Ranking', 'Gamification\\User\\Points', $options);
        }
    }
}
