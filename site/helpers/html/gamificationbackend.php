<?php
/**
 * @package      Gamification
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Gamification Html Back-end Helper
 *
 * @package        Gamification
 * @subpackage     Components
 * @since          1.6
 */
abstract class JHtmlGamificationbackend
{
    public static function helptip($note)
    {
        $html = '';
        if ($note !== null and $note !== '') {
            $html = '<a class="btn btn-mini hasTooltip" href="javascript: void(0);" title="' . htmlspecialchars($note, ENT_QUOTES, 'UTF-8') . '"><i class="icon-question-sign"></i></a>';
        }

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
            $html[] = '<i class="icon-link"></i>';
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
        $html[] = '<i class="icon-picture"></i>';
        $html[] = '</a>';

        return implode($html);
    }

    public static function goals()
    {

 
    }
}
