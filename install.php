<?php
/**
 * @package      ITPrism Components
 * @subpackage   Gamification
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 * Gamification is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Script file of the component
 */
class pkg_gamificationInstallerScript {
    
    /**
     * method to install the component
     *
     * @return void
     */
    public function install($parent) {
    }
    
    /**
     * method to uninstall the component
     *
     * @return void
     */
    public function uninstall($parent) {
    }
    
    /**
     * method to update the component
     *
     * @return void
     */
    public function update($parent) {
    }
    
    /**
     * method to run before an install/update/uninstall method
     *
     * @return void
     */
    public function preflight($type, $parent) {
    }
    
    /**
     * method to run after an install/update/uninstall method
     *
     * @return void
     */
    public function postflight($type, $parent) {
        
        if(strcmp($type, "install") == 0) {
            
            if(!defined("COM_GAMIFICATION_PATH_COMPONENT_ADMINISTRATOR")) {
                define("COM_GAMIFICATION_PATH_COMPONENT_ADMINISTRATOR", JPATH_ADMINISTRATOR . DIRECTORY_SEPARATOR . "components" . DIRECTORY_SEPARATOR ."com_gamification");
            }
            
            // Register Component helpers
            JLoader::register("GamificationInstallHelper", COM_GAMIFICATION_PATH_COMPONENT_ADMINISTRATOR.DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."installer.php");
        
            jimport('joomla.filesystem.path');
            jimport('joomla.filesystem.folder');
            jimport('joomla.filesystem.file');
            
            $params             = JComponentHelper::getParams("com_gamification");
            $this->imagesFolder = JFolder::makeSafe($params->get("images_directory", "images/gamification"));
            $this->imagesPath   = JPath::clean( JPATH_SITE.DIRECTORY_SEPARATOR.$this->imagesFolder );
            $this->bootstrap    = JPath::clean( JPATH_SITE.DIRECTORY_SEPARATOR."media".DIRECTORY_SEPARATOR."com_gamification".DIRECTORY_SEPARATOR."css".DIRECTORY_SEPARATOR. "admin".DIRECTORY_SEPARATOR."bootstrap.min.css" );
        
            $style = '<style>'.file_get_contents($this->bootstrap).'</style>';
            echo $style;
            
            // Create images folder
            if(!is_dir($this->imagesPath)){
                GamificationInstallHelper::createFolder($this->imagesPath);
            }
            
            // Start table with the information
            GamificationInstallHelper::startTable();
        
            // Requirements
            GamificationInstallHelper::addRowHeading(JText::_("COM_GAMIFICATION_MINIMUM_REQUIREMENTS"));
            
            // Display result about verification for existing folder 
            $title  = JText::_("COM_GAMIFICATION_IMAGE_FOLDER");
            $info   = $this->imagesFolder;
            if(!is_dir($this->imagesPath)) {
                $result = array("type" => "important", "text" => JText::_("JNO"));
            } else {
                $result = array("type" => "success"  , "text" => JText::_("JYES"));
            }
            GamificationInstallHelper::addRow($title, $result, $info);
            
            // Display result about verification for writeable folder 
            $title  = JText::_("COM_GAMIFICATION_WRITABLE_FOLDER");
            $info   = $this->imagesFolder;
            if(!is_writable($this->imagesPath)) {
                $result = array("type" => "important", "text" => JText::_("JNO"));
            } else {
                $result = array("type" => "success"  , "text" => JText::_("JYES"));
            }
            GamificationInstallHelper::addRow($title, $result, $info);
            
            // Display result about verification for GD library
            $title  = JText::_("COM_GAMIFICATION_GD_LIBRARY");
            $info   = "";
            if(!extension_loaded('gd') AND function_exists('gd_info')) {
                $result = array("type" => "important", "text" => JText::_("COM_GAMIFICATION_WARNING"));
            } else {
                $result = array("type" => "success"  , "text" => JText::_("JON"));
            }
            GamificationInstallHelper::addRow($title, $result, $info);
            
            // Display result about verification for cURL library
            $title  = JText::_("COM_GAMIFICATION_CURL_LIBRARY");
            $info   = "";
            if( !extension_loaded('curl') ) {
                $info   = JText::_("COM_GAMIFICATION_CURL_INFO");
                $result = array("type" => "important", "text" => JText::_("JOFF"));
            } else {
                $result = array("type" => "success"  , "text" => JText::_("JON"));
            }
            GamificationInstallHelper::addRow($title, $result, $info);
            
            // Display result about verification Magic Quotes
            $title  = JText::_("COM_GAMIFICATION_MAGIC_QUOTES");
            $info   = "";
            if( get_magic_quotes_gpc() ) {
                $info   = JText::_("COM_GAMIFICATION_MAGIC_QUOTES_INFO");
                $result = array("type" => "important", "text" => JText::_("JON"));
            } else {
                $result = array("type" => "success"  , "text" => JText::_("JOFF"));
            }
            GamificationInstallHelper::addRow($title, $result, $info);
            
            // Display result about verification FileInfo
            $title  = JText::_("COM_GAMIFICATION_FILEINFO");
            $info   = "";
            if( !function_exists('finfo_open') ) {
                $info   = JText::_("COM_GAMIFICATION_FILEINFO_INFO");
                $result = array("type" => "important", "text" => JText::_("JOFF"));
            } else {
                $result = array("type" => "success", "text" => JText::_("JON"));
            }
            GamificationInstallHelper::addRow($title, $result, $info);
            
            // Installed extensions
            
            CrowdFundingInstallHelper::addRowHeading(JText::_("COM_GAMIFICATION_INSTALLED_EXTENSIONS"));
            
            // CrowdFunding Library
            $result = array("type" => "success"  , "text" => JText::_("COM_GAMIFICATION_INSTALLED"));
            CrowdFundingInstallHelper::addRow(JText::_("COM_GAMIFICATION_GAMIFICATION_LIBRARY"), $result, JText::_("COM_GAMIFICATION_LIBRARY"));
            
            // End table
            GamificationInstallHelper::endTable();
            
        }
        
        echo JText::sprintf("COM_GAMIFICATION_MESSAGE_REVIEW_SAVE_SETTINGS", JRoute::_("index.php?option=com_gamification"));
        
        jimport("itprism.version");
        if(!class_exists("ITPrismVersion")) {
            echo JText::_("COM_GAMIFICATION_MESSAGE_INSTALL_ITPRISM_LIBRARY");
        }
    }
}
