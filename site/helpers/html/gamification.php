<?php
/**
 * @package      Gamification
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

use Gamification\User\Rank\Rank as UserRank;
use Gamification\User\Badge\Badge as UserBadge;

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
            if ($abbr) {
                $abbr = ' [ '.$abbr.' ]';
            } else {
                $abbr = ' ( '.$name.' )';
            }

            $html = '<span class="hasTooltip" title="' . htmlspecialchars($name, ENT_QUOTES, 'UTF-8') . '">' . $value . $abbr . '</span>';
        }

        return $html;
    }

    public static function helptip($note)
    {
        $html = '';
        if ($note !== null and $note !== '') {
            $html = '<a class="btn btn-mini hasTooltip" href="javascript: void(0);" title="' . htmlspecialchars($note, ENT_QUOTES, 'UTF-8') . '"><span class="fa fa-question-circle"></span></a>';
        }

        return $html;
    }

    public static function rank(UserRank $rank, $mediaPath, $tip = false, $placeholders = array())
    {
        $title   = '';
        $class   = '';
        $classes = array();

        if ($tip and $rank->getRank()->getActivityText()) {
            JHtml::_('bootstrap.tooltip');

            $classes[] = 'hasTooltip';

            $description  = strip_tags(trim($rank->getRank()->getActivityText($placeholders)));
            $title = ' title="' . htmlspecialchars($description, ENT_QUOTES, 'UTF-8') . '"';
        }

        // Prepare class property
        if (count($classes) > 0) {
            $class = ' class="' . implode(' ', $classes) . '"';
        }

        // Prepare alt property
        $alt = strip_tags(trim($rank->getRank()->getTitle()));
        if ($alt !== null and $alt !== '') {
            $alt = ' alt="' . htmlspecialchars($alt, ENT_QUOTES, 'UTF-8') . '"';
        }

        $html = '<img src="' . $mediaPath.'/'.$rank->getRank()->getImage() . '"' . $class . $alt . $title . ' />';

        return $html;
    }

    public static function badge(UserBadge $badge, $mediaPath, $tip = false, $placeholders = array())
    {
        $title   = '';
        $class   = '';
        $classes = array();

        if ($tip and $badge->getBadge()->getActivityText()) {
            JHtml::_('bootstrap.tooltip');
            $classes[] = 'hasTooltip';
            $description  = strip_tags(trim($badge->getBadge()->getActivityText($placeholders)));
            $title = ' title="' . htmlspecialchars($description, ENT_QUOTES, 'UTF-8') . '"';
        }

        // Prepare class property
        if (count($classes) > 0) {
            $class = ' class="' . implode(' ', $classes) . '"';
        }

        // Prepare alt property
        $alt = strip_tags(trim($badge->getBadge()->getTitle()));
        if ($alt !== null and $alt !== '') {
            $alt = ' alt="' . htmlspecialchars($alt, ENT_QUOTES, 'UTF-8') . '"';
        }

        $html = '<img src="' . $mediaPath.'/'.$badge->getBadge()->getImage() . '"' . $class . $alt . $title . ' />';

        return $html;
    }

    public static function iconLink($url, $title = '')
    {
        $html = array();

        if ($url) {
            $hasTooltip = '';
            if ($title) {
                $hasTooltip = ' hasTooltip';
                $title = 'title="'. htmlentities($title, ENT_QUOTES, 'UTF-8').'"';
            }

            $html[] = '<a class="btn btn-mini btn-link'.$hasTooltip.'" href="' . $url . '" target="_blank" '.$title.'>';
            $html[] = '<i class="fa fa-link"></i>';
            $html[] = '</a>';
        }

        return implode($html);
    }

    public static function iconPicture($url)
    {
        if (!$url) {
            return '';
        }

        $html[] = '<a class="btn btn-mini btn-link" href="' . $url . '" target="_blank">';
        $html[] = '<span class="fa fa-picture-o"></span>';
        $html[] = '</a>';

        return implode($html);
    }

    public static function link($url, $title, $attributes = array())
    {
        $html = array();

        if ($url and $title) {
            $class = (array_key_exists('class', $attributes)) ? 'class="'.$attributes['class'].'"' : '';

            $html[] = '<a '.$class.' href="' . $url . '" rel="nofollow">';
            $html[] = htmlentities($title, ENT_QUOTES, 'UTF-8');
            $html[] = '</a>';
        }

        return implode($html);
    }

    /**
     * @param Prism\Integration\Profiles\ProfilesInterface $socialProfiles
     * @param $options
     *
     * @return string
     */
    public static function avatar($socialProfiles, $options)
    {
        $class = '';
        if (array_key_exists('class', $options)) {
            $class = ' class="'.$options['class'].'"';
        }

        // Display social profile.
        if ($socialProfiles !== null) {
            $avatar = $socialProfiles->getAvatar($options['user_id'], $options['size']);

            if (!$avatar) {
                $avatar = '<img '.$class.' src="'.$options['default'].'">';
            } else {
                $avatar = '<img '.$class.' src="'.$avatar.'" alt="'.$options['name'].'">';
            }

            $link   =  $socialProfiles->getLink($options['user_id']);
        } else {
            $avatar = '<img '.$class.' src="'.$options['default'].'" >';
            $link   = '';
        }

        if ($link !== '') {
            $avatar = '<a href="'.$link.'">'.$avatar.'</a>';
        }
        
        return $avatar;
    }
    
    public static function number($number)
    {
        if ($number === null) {
            return JText::_('COM_GAMIRIFCATION_UNLIMITED');
        }

        return (int)$number;
    }
}
