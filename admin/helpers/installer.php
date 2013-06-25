<?php
/**
 * @package      Gamification Platform
 * @subpackage   Components Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Gamification Platform is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;

/**
 * These class contains methods using for upgrading the extension
 */
class GamificationInstallHelper {
	
    public static function startTable() {
        echo '
        <div style="width: 600px;">
        <table class="table table-bordered table-striped">';
    }
    
    public static function endTable() {
        echo "</table></div>";
    }
    
    public static function addRowHeading($heading) {
	    echo '
	    <tr class="info">
            <td colspan="3">'.$heading.'</td>
        </tr>';
	}
	
	/**
	 * Display an HTML code for a row
	 * 
	 * @param string $title
	 * @param array $result 
	 * array(
	 * 	type => success, important, warning,
	 * 	text => yes, no, off, on, warning,...
	 * )
	 */
	public static function addRow($title, $result, $info) {
	    
	    $outputType = JArrayHelper::getValue($result, "type", "");
	    $outputText = JArrayHelper::getValue($result, "text", "");
	    
	    $output     = "";
	    if(!empty($outputType) AND !empty($outputText)) {
            $output = '<span class="label label-'.$outputType.'">'.$outputText.'</span>';	        
	    }
	        
	    echo '
	    <tr>
            <td>'.$title.'</td>
            <td>'.$output.'</td>
            <td>'.$info.'</td>
        </tr>';
	}
    
    public static function createFolder($imagesPath) {
        
        // Create image folder
        if(true !== JFolder::create($imagesPath)) {
            JLog::add(JText::sprintf("COM_GAMIFICATION_ERROR_CANNOT_CREATE_FOLDER", $imagesPath));
        } else {
            
            // Copy index.html
            $indexFile = $imagesPath . DIRECTORY_SEPARATOR ."index.html";
            $html = '<html><body bgcolor="#FFFFFF"></body></html>';
            if(true !== JFile::write($indexFile,$html)) {
                JLog::add(JText::sprintf("COM_GAMIFICATION_ERROR_CANNOT_SAVE_FILE", $indexFile));
            }
            
        }
        
    }
    
}