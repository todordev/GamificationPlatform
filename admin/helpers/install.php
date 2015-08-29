<?php
/**
 * @package      Gamification Platform
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * These class contains methods using for upgrading the extension
 */
class GamificationInstallHelper
{
    public static function startTable()
    {
        echo '
        <div style="width: 600px;">
        <table class="table table-bordered table-striped">';
    }

    public static function endTable()
    {
        echo "</table></div>";
    }

    public static function addRowHeading($heading)
    {
        echo '
	    <tr class="info">
            <td colspan="3">' . $heading . '</td>
        </tr>';
    }

    /**
     * Display an HTML code for a row
     *
     * @param string $title
     * @param array  $result
     * @param string $info
     *
     * array(
     *    type => success, important, warning,
     *    text => yes, no, off, on, warning,...
     *    )
     */
    public static function addRow($title, $result, $info)
    {
        $outputType = JArrayHelper::getValue($result, "type", "");
        $outputText = JArrayHelper::getValue($result, "text", "");

        $output = "";
        if (!empty($outputType) and !empty($outputText)) {
            $output = '<span class="label label-' . $outputType . '">' . $outputText . '</span>';
        }

        echo '
	    <tr>
            <td>' . $title . '</td>
            <td>' . $output . '</td>
            <td>' . $info . '</td>
        </tr>';
    }

    public static function createFolder($imagesPath)
    {
        // Create image folder
        if (true !== JFolder::create($imagesPath)) {
            JLog::add(JText::sprintf("COM_GAMIFICATION_ERROR_CANNOT_CREATE_FOLDER", $imagesPath));
        } else {

            // Copy index.html
            $indexFile = $imagesPath . DIRECTORY_SEPARATOR . "index.html";
            $html      = '<html><body style="background-color: #fff"></body></html>';
            if (true !== JFile::write($indexFile, $html)) {
                JLog::add(JText::sprintf("COM_GAMIFICATION_ERROR_CANNOT_SAVE_FILE", $indexFile));
            }
        }
    }
}
