<?php
/**
 * @package      Gamification Platform
 * @subpackage   Plugins
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * UserIdeasVote is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.helper');
jimport('joomla.plugin.plugin');

/**
 * This plugin calculates and updates the game mechanics.
 * This plugin use only points.
 *
 * @package      Gamification Platform
 * @subpackage   Plugins
 */
class plgSystemGamification extends JPlugin
{
    protected $userId;

    /**
     * Update some gamification mechanics of the user - levels, badges, ranks,...
     */
    public function onAfterRoute()
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite * */

        if ($app->isAdmin()) {
            return;
        }

        $document = JFactory::getDocument();
        /** @var $document JDocumentHTML * */

        $type = $document->getType();
        if (strcmp("html", $type) != 0) {
            return;
        }

        $this->userId = JFactory::getUser()->id;
        if (!$this->userId) {
            return;
        }

        $this->loadLanguage();

        // Get points
        jimport("gamification.points");
        jimport("gamification.userpoints");

        $pointsId = $this->params->get("points");
        $points   = GamificationPoints::getInstance($pointsId);

        // Get user points
        $userPoints = null;
        if ($points->getId() and $points->isPublished()) {

            $keys = array(
                "user_id"   => $this->userId,
                "points_id" => $points->getId()
            );

            $userPoints = GamificationUserPoints::getInstance($keys);
        }

        // Update level value
        if ($this->params->get("enable_leveling", 0)) {
            $this->updateLevel($userPoints);
        }

        // Update rank value
        if ($this->params->get("enable_ranking", 0)) {
            $this->updateRank($userPoints);
        }

        // Update badge value
        if ($this->params->get("enable_badging", 0)) {
            $this->updateBadge($userPoints);
        }
    }

    protected function updateLevel($userPoints)
    {
        // Get user level
        jimport("gamification.userlevel.points");

        $level = GamificationUserLevelPoints::getInstance($userPoints);

        if (!$level->id) { // Create a level record

            $data = array(
                "user_id"  => $userPoints->user_id,
                "group_id" => $userPoints->group_id
            );

            $level->startLeveling($data);

        } else { // Level UP

            if ($level->levelUp()) {

                // Level with rank
                if (!empty($level->rank_id)) {
                    $rank = $level->getRank();

                    $note = JText::sprintf("PLG_SYSTEM_GAMIFICATION_LEVEL_RANK_NOTIFICATION", $level->getLevel(), $rank->getTitle());
                    $this->notify($note);

                    $info = JText::sprintf("PLG_SYSTEM_GAMIFICATION_LEVEL_RANK_ACTIVITY", $level->getLevel(), $rank->getTitle());
                    $this->storeActivity($info);

                } else { // Level without rank

                    $note = JText::sprintf("PLG_SYSTEM_GAMIFICATION_LEVEL_NOTIFICATION", $level->getLevel());
                    $this->notify($note);

                    $info = JText::sprintf("PLG_SYSTEM_GAMIFICATION_LEVEL_ACTIVITY", $level->getLevel());
                    $this->storeActivity($info);
                }
            }
        }
    }

    protected function updateRank($userPoints)
    {
        // Get user rank
        jimport("gamification.userrank.points");

        $rank = GamificationUserRankPoints::getInstance($userPoints);

        if (!$rank->id) { // Create a rank record

            $data = array(
                "user_id"  => $userPoints->user_id,
                "group_id" => $userPoints->group_id
            );

            $rank->startRanking($data);

        } else { // Give a new rank

            if ($rank->giveRank()) {

                // Prepare the link to the rank image.
                $image = $rank->getImage();
                if (!empty($image)) {
                    $image = $this->getImagePath($image);
                }

                $note = JText::sprintf("PLG_SYSTEM_GAMIFICATION_RANK_NOTIFICATION", $rank->getTitle());
                $this->notify($note, $image);

                $info = JText::sprintf("PLG_SYSTEM_GAMIFICATION_RANK_ACTIVITY", $rank->getTitle());
                $this->storeActivity($info, $image);
            }

        }
    }

    protected function updateBadge($userPoints)
    {
        // Get user rank
        jimport("gamification.userbadges.points");

        $badges = GamificationUserBadgesPoints::getInstance($userPoints);

        $badge = $badges->giveBadge();

        // Send a notification to user about the new badge
        if (!empty($badge->badge_id)) {

            // Prepare the link to the badge image.
            $image = $badge->getImage();
            if (!empty($image)) {
                $image = $this->getImagePath($image);
            }

            $note = JText::sprintf("PLG_SYSTEM_GAMIFICATION_BADGE_NOTIFICATION", $badge->getTitle());
            $this->notify($note, $image);

            $info = JText::sprintf("PLG_SYSTEM_GAMIFICATION_BADGE_ACTIVITY", $badge->getTitle());
            $this->storeActivity($info, $image);
        }
    }

    public function notify($message, $image = null)
    {
        $service = $this->params->get("notification_integration");

        jimport("itprism.integrate.notification");
        $notification = ITPrismIntegrateNotification::factory($service);

        $notification->setNote($message);
        $notification->setUserId($this->userId);

        if (!empty($image)) {
            $notification->setImage($image);
        }

        $notification->send();
    }

    public function storeActivity($info, $image = null)
    {
        $service = $this->params->get("activity_integration");

        jimport("itprism.integrate.activity");

        $activity = ITPrismIntegrateActivity::factory($service);
        $activity->setInfo($info);
        $activity->setUserId($this->userId);

        // Application to JomSocial object
        if (strcmp("jomsocial", $service) == 0) {
            $activity->setApp("gamification.points");
        }

        if (!empty($image)) {
            $activity->setImage($image);
        }

        $activity->store();
    }

    private function getImagePath($image)
    {
        $componentParams = JComponentHelper::getParams("com_gamification");
        $imagesFolder    = $componentParams->get("images_directory", "images/gamification");
        $image           = $imagesFolder . "/" . $image;

        return $image;
    }
}
