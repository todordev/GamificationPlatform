<?php
/**
 * @package      Gamification
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Gamification Html Helper
 *
 * @package        Gamification
 * @subpackage     Components
 * @since          1.6
 */
abstract class JHtmlGamification
{
    public static function points($value, $name, $abbr)
    {
        if (!$value) {
            $html = '--';
        } else {
            $html = '<span class="hasTooltip" title="' . htmlspecialchars($name, ENT_QUOTES, "UTF-8") . '">' . $value . ' [ ' . $abbr . ' ]' . '</span>';
        }

        return $html;
    }

    public static function helptip($note)
    {
        $html = "";
        if (!empty($note)) {
            $html = '<a class="btn btn-mini hasTooltip" href="javascript: void(0);" title="' . htmlspecialchars($note, ENT_QUOTES, "UTF-8") . '"><i class="icon-question-sign"></i></a>';
        }

        return $html;
    }

    public static function rank(Gamification\User\Rank $rank, $mediaPath, $tip = false, $placeholders = array())
    {
        $title   = "";
        $class   = "";
        $classes = array();

        if (!empty($tip) and $rank->getDescription()) {

            JHtml::_("bootstrap.tooltip");

            $classes[] = "hasTooltip";

            $description  = strip_tags(Joomla\String\String::trim($rank->getDescription($placeholders)));
            $title = ' title="' . htmlspecialchars($description, ENT_QUOTES, "UTF-8") . '"';

        }

        // Prepare class property
        if (!empty($classes)) {
            $class = ' class="' . implode(" ", $classes) . '"';
        }

        // Prepare alt property
        $alt = strip_tags(Joomla\String\String::trim($rank->getTitle()));
        if (!empty($alt)) {
            $alt = ' alt="' . htmlspecialchars($alt, ENT_QUOTES, "UTF-8") . '"';
        }

        $html = '<img src="' . $mediaPath."/".$rank->getImage() . '"' . $class . $alt . $title . ' />';

        return $html;
    }

    public static function badge(Gamification\User\Badge $badge, $mediaPath, $tip = false, $placeholders = array())
    {
        $title   = "";
        $class   = "";
        $classes = array();

        if (!empty($tip) and $badge->getDescription()) {

            JHtml::_("bootstrap.tooltip");

            $classes[] = "hasTooltip";

            $description  = strip_tags(Joomla\String\String::trim($badge->getDescription($placeholders)));
            $title = ' title="' . htmlspecialchars($description, ENT_QUOTES, "UTF-8") . '"';

        }

        // Prepare class property
        if (!empty($classes)) {
            $class = ' class="' . implode(" ", $classes) . '"';
        }

        // Prepare alt property
        $alt = strip_tags(Joomla\String\String::trim($badge->getTitle()));
        if (!empty($alt)) {
            $alt = ' alt="' . htmlspecialchars($alt, ENT_QUOTES, "UTF-8") . '"';
        }

        $html = '<img src="' . $mediaPath."/".$badge->getImage() . '"' . $class . $alt . $title . ' />';

        return $html;
    }

    /**
     * @param Gamification\User\ProgressBar     $progress
     * @param string $name User name
     * @param bool $tip
     *
     * @return string
     */
    public static function progress($progress, $name = "", $tip = false)
    {
        $titleCurrent = "";
        $titleNext    = "";
        $classes      = array();

        $html = array();

        $userPoints   = $progress->getPoints();
        $gameMechanic = $progress->getGameMechanic();

        // Prepare current level
        if (!empty($tip)) {
            JHtml::_("bootstrap.tooltip");
            $classes[]    = "hasTooltip";
            $titleCurrent = ' title="' . JText::sprintf("MOD_GAMIFICATIONPROFILE_POINTS_INFORMATION", $name, $userPoints) . '"';
        }

        // START Labels
        $html[] = '<div class="gfy-progress-labels">';

        $html[] = '<div class="gfy-prgss-lbl-current">';
        $html[] = htmlspecialchars($progress->getTitleCurrent(), ENT_QUOTES, "UTF-8");
        $html[] = '</div>';

        // Prepare next level
        if ($progress->hasNext()) {

            $nextUnit       = $progress->getNextUnit();
            $nextUnitTitle  = htmlspecialchars($nextUnit->getTitle(), ENT_QUOTES, "UTF-8");

            $html[] = '<div class="gfy-prgss-lbl-next">';
            $html[] = $nextUnitTitle;
            $html[] = '</div>';

            if (!empty($tip)) {

                $nextUnitPoints = $nextUnit->getPoints();
                $neededPoints   = abs($nextUnitPoints - $userPoints);

                switch ($gameMechanic) {

                    case "badges":
                        $titleNext = ' title="' . JText::sprintf("MOD_GAMIFICATIONPROFILE_POINTS_BADGES_INFORMATION_REACH", $neededPoints, $nextUnitTitle) . '"';
                        break;

                    case "ranks":
                        $titleNext = ' title="' . JText::sprintf("MOD_GAMIFICATIONPROFILE_POINTS_RANKS_INFORMATION_REACH", $neededPoints, $nextUnitTitle) . '"';
                        break;

                    default: // Levels
                        $titleNext = ' title="' . JText::sprintf("MOD_GAMIFICATIONPROFILE_POINTS_LEVELS_INFORMATION_REACH", $neededPoints, $nextUnitTitle) . '"';
                        break;
                }
            }

        }

        // END Labels
        $html[] = '</div>';

        $html[] = '<div class="clearfix"></div>';
        $html[] = '<div class="progress">';
        $html[] = '<div class="progress-bar progress-bar-success ' . implode(" ", $classes) . '" ' . $titleCurrent . ' style="width: ' . $progress->getPercentage() . '%;" role="progressbar" aria-valuenow="'.$progress->getPercentage().'" aria-valuemin="0" aria-valuemax="100" ></div>';

        if ($progress->hasNext()) {
            $html[] = '<div class="progress-bar progress-bar-danger ' . implode(" ", $classes) . '" ' . $titleNext . ' style="width: ' . $progress->getPercentNext() . '%;" role="progressbar" aria-valuenow="'.$progress->getPercentNext().'" aria-valuemin="0" aria-valuemax="100"></div>';
        }

        $html[] = '</div>';

        return implode("\n", $html);
    }

    public static function iconLink($url, $title = "")
    {
        $html = array();

        if (!empty($url)) {

            $hasTooltip = "";
            if (!empty($title)) {
                $hasTooltip = " hasTooltip";
                $title = 'title="'. htmlentities($title, ENT_QUOTES, "UTF-8").'"';
            }

            $html[] = '<a class="btn btn-mini btn-link'.$hasTooltip.'" href="' . $url . '" target="_blank" '.$title.'>';
            $html[] = '<i class="icon-link"></i>';
            $html[] = '</a>';
        }

        return implode($html);
    }

    public static function iconPicture($url)
    {
        if (!$url) {
            return "";
        }

        $html[] = '<a class="btn btn-mini btn-link" href="' . $url . '" target="_blank">';
        $html[] = '<i class="icon-picture"></i>';
        $html[] = '</a>';

        return implode($html);
    }

    public static function link($url, $title, $attributes = array())
    {
        $html = array();

        if (!empty($url) and !empty($title)) {

            $class = (isset($attributes["class"])) ? 'class="'.$attributes["class"].'"' : "";

            $html[] = '<a '.$class.' href="' . $url . '" rel="nofollow">';
            $html[] = htmlentities($title, ENT_QUOTES, "UTF-8");
            $html[] = '</a>';
        }

        return implode($html);
    }
}
