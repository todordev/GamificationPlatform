<?php
/**
 * @package      Gamification
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
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
            $html = '<a class="btn hasTooltip" href="javascript: void(0);" title="' . htmlspecialchars($note, ENT_QUOTES, "UTF-8") . '"><i class="icon-question-sign"></i></a>';
        }

        return $html;
    }

    public static function badge($image, $alt, $tip = false, $note = "")
    {
        $title   = "";
        $class   = "";
        $classes = array();

        if (!empty($tip) and !empty($note)) {

            JHtml::_("bootstrap.tooltip");

            $classes[] = "hasTooltip";

            $note  = strip_tags(JString::trim($note));
            $title = ' title="' . htmlspecialchars($note, ENT_QUOTES, "UTF-8") . '"';

        }

        // Prepare class property
        if (!empty($classes)) {
            $class = ' class="' . implode(" ", $classes) . '"';
        }

        // Prepare alt property
        $alt = strip_tags(JString::trim($alt));
        if (!empty($alt)) {
            $alt = ' alt="' . htmlspecialchars($alt, ENT_QUOTES, "UTF-8") . '"';
        }

        $html = '<img src="' . $image . '"' . $class . $alt . $title . ' />';

        return $html;
    }

    /**
     * @param      $progress
     * @param bool $tip
     *
     * @return string
     *
     * @todo fix doc or remove this method
     */
    public static function progress($progress, $tip = false)
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
            $titleCurrent = ' title="' . JText::sprintf("MOD_GAMIFICATIONPROFILE_POINTS_INFORMATION", $userPoints) . '"';
        }

        // START Labels
        $html[] = '<div class="gfy-progress-labels">';

        $html[] = '<div class="gfy-prgss-lbl-current">';
        $html[] = htmlspecialchars($progress->getTitleCurrent(), ENT_QUOTES, "UTF-8");
        $html[] = '</div>';

        // Prepare next level
        if ($progress->hasNext()) {

            $titleNext = htmlspecialchars($progress->getTitleNext(), ENT_QUOTES, "UTF-8");

            $html[] = '<div class="gfy-prgss-lbl-next">';
            $html[] = $titleNext;
            $html[] = '</div>';

            if (!empty($tip)) {
                $pointsNext   = $progress->getPointsNext();
                $neededPoints = abs($pointsNext - $userPoints);

                switch ($gameMechanic) {

                    case "badges":
                        $titleNext = ' title="' . JText::sprintf("MOD_GAMIFICATIONPROFILE_POINTS_BADGES_INFORMATION_REACH", $neededPoints, $titleNext) . '"';
                        break;

                    case "ranks":
                        $titleNext = ' title="' . JText::sprintf("MOD_GAMIFICATIONPROFILE_POINTS_RANKS_INFORMATION_REACH", $neededPoints, $titleNext) . '"';
                        break;

                    default:
                        $titleNext = ' title="' . JText::sprintf("MOD_GAMIFICATIONPROFILE_POINTS_LEVELS_INFORMATION_REACH", $neededPoints, $titleNext) . '"';
                        break;
                }
            }

        }

        // END Labels
        $html[] = '</div>';

        $html[] = '<div class="clearfix"></div>';
        $html[] = '<div class="progress">';
        $html[] = '<div class="bar bar-success ' . implode(" ", $classes) . '" ' . $titleCurrent . ' style="width: ' . $progress->getPercent() . '%;"></div>';

        if ($progress->hasNext()) {
            $html[] = '<div class="bar bar-warning ' . implode(" ", $classes) . '" ' . $titleNext . ' style="width: ' . $progress->getPercentNext() . '%;"></div>';
        }

        $html[] = '</div>';

        return implode("\n", $html);
    }

    public static function boolean($value, $title = "")
    {
        $title = addslashes(htmlspecialchars(JString::trim($title), ENT_COMPAT, 'UTF-8'));

        if (!$value) { // unpublished
            $class = "unpublish";
        } else {
            $class = "ok";
        }

        if (!empty($title)) {
            $title = ' title="' . $title . '"';
        }

        $html[] = '<a class="btn btn-micro" rel="tooltip" ';
        $html[] = ' href="javascript:void(0);" ' . $title . '">';
        $html[] = '<i class="icon-' . $class . '"></i>';
        $html[] = '</a>';

        return implode($html);
    }
}
