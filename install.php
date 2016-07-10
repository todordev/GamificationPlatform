<?php
/**
 * @package      Gamification Platform
 * @subpackage   Component
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Script file of the component
 */
class pkg_gamificationInstallerScript
{
    /**
     * Method to install the component.
     *
     * @param object $parent
     *
     * @return void
     */
    public function install($parent)
    {
    }

    /**
     * Method to uninstall the component.
     *
     * @param $parent
     *
     * @return void
     */
    public function uninstall($parent)
    {
    }

    /**
     * Method to update the component.
     *
     * @param $parent
     *
     * @return void
     */
    public function update($parent)
    {
    }

    /**
     * Method to run before an install/update/uninstall method.
     *
     * @param $type
     * @param $parent
     *
     * @return void
     */
    public function preflight($type, $parent)
    {
    }

    /**
     * Method to run after an install/update/uninstall method.
     *
     * @param string $type
     * @param string $parent
     *
     * @return void
     */
    public function postflight($type, $parent)
    {
        if (!defined('GAMIFICATION_PATH_COMPONENT_ADMINISTRATOR')) {
            define('GAMIFICATION_PATH_COMPONENT_ADMINISTRATOR', JPATH_ADMINISTRATOR . '/components/com_gamification');
        }

        // Register Component helpers
        JLoader::register('GamificationInstallHelper', GAMIFICATION_PATH_COMPONENT_ADMINISTRATOR . '/helpers/install.php');

        jimport('Prism.init');
        jimport('Gamification.init');

        $params       = JComponentHelper::getParams('com_gamification');
        $mediaFolder  = JFolder::makeSafe($params->get('local_media_folder', 'media/gamification'));
        $mediaPath    = JPath::clean(JPATH_SITE .DIRECTORY_SEPARATOR. $mediaFolder);

        // Create images folder
        if (!JFolder::exists($mediaPath)) {
            GamificationInstallHelper::createFolder($mediaPath);
        }

        // Start table with the information
        GamificationInstallHelper::startTable();

        // Requirements
        GamificationInstallHelper::addRowHeading(JText::_('COM_GAMIFICATION_MINIMUM_REQUIREMENTS'));

        // Display result about verification for existing folder
        $title = JText::_('COM_GAMIFICATION_MEDIA_FOLDER_EXISTS');
        $info  = $mediaPath;
        if (!is_dir($mediaPath)) {
            $result = array('type' => 'important', 'text' => JText::_('JNO'));
        } else {
            $result = array('type' => 'success', 'text' => JText::_('JYES'));
        }
        GamificationInstallHelper::addRow($title, $result, $info);

        // Display result about verification for writable folder
        $title = JText::_('COM_GAMIFICATION_WRITABLE_FOLDER');
        $info  = $mediaPath;
        if (!is_writable($mediaPath)) {
            $result = array('type' => 'important', 'text' => JText::_('JNO'));
        } else {
            $result = array('type' => 'success', 'text' => JText::_('JYES'));
        }
        GamificationInstallHelper::addRow($title, $result, $info);

        // Display result about verification for GD library
        $title = JText::_('COM_GAMIFICATION_GD_LIBRARY');
        $info  = '';
        if (!extension_loaded('gd') and function_exists('gd_info')) {
            $result = array('type' => 'important', 'text' => JText::_('COM_GAMIFICATION_WARNING'));
        } else {
            $result = array('type' => 'success', 'text' => JText::_('JON'));
        }
        GamificationInstallHelper::addRow($title, $result, $info);

        // Display result about verification Magic Quotes
        $title = JText::_('COM_GAMIFICATION_MAGIC_QUOTES');
        $info  = '';
        if (get_magic_quotes_gpc()) {
            $info   = JText::_('COM_GAMIFICATION_MAGIC_QUOTES_INFO');
            $result = array('type' => 'important', 'text' => JText::_('JON'));
        } else {
            $result = array('type' => 'success', 'text' => JText::_('JOFF'));
        }
        GamificationInstallHelper::addRow($title, $result, $info);

        // Display result about verification FileInfo
        $title = JText::_('COM_GAMIFICATION_FILEINFO');
        $info  = '';
        if (!function_exists('finfo_open')) {
            $info   = JText::_('COM_GAMIFICATION_FILEINFO_INFO');
            $result = array('type' => 'important', 'text' => JText::_('JOFF'));
        } else {
            $result = array('type' => 'success', 'text' => JText::_('JON'));
        }
        GamificationInstallHelper::addRow($title, $result, $info);

        // Display result about verification for cURL library
        $title = JText::_('COM_GAMIFICATION_CURL_LIBRARY');
        $info  = '';
        if (!extension_loaded('curl')) {
            $info   = JText::_('COM_GAMIFICATION_PHP_CURL_INFO');
            $result = array('type' => 'important', 'text' => JText::_('JNO'));
        } else {
            $currentVersion = GamificationInstallHelper::getCurlVersion();
            $text           = JText::sprintf('COM_GAMIFICATION_CURRENT_V_S', $currentVersion);

            if (version_compare($currentVersion, '7.34.0', '<')) {
                $info   = JText::sprintf('COM_GAMIFICATION_REQUIRES_V_S', '7.34.0+');
                $result = array('type' => 'warning', 'text' => $text);
            } else {
                $result = array('type' => 'success', 'text' => $text);
            }
        }
        GamificationInstallHelper::addRow($title, $result, $info);
        
        // Display result about verification Open SSL
        $title = JText::_('COM_GAMIFICATION_OPEN_SSL');
        $info  = '';
        if (!function_exists('curl_init')) {
            $result = array('type' => 'important', 'text' => JText::_('JNO'));
        } else {
            $currentVersion = GamificationInstallHelper::getOpenSslVersion();
            $text           = JText::sprintf('COM_GAMIFICATION_CURRENT_V_S', $currentVersion);

            if (version_compare($currentVersion, '1.0.1.3', '<')) {
                $info   = JText::sprintf('COM_GAMIFICATION_REQUIRES_V_S', '1.0.1.3+');
                $result = array('type' => 'warning', 'text' => $text);
            } else {
                $result = array('type' => 'success', 'text' => $text);
            }
        }
        GamificationInstallHelper::addRow($title, $result, $info);
        
        // Display result about PHP version
        $title = JText::_('COM_GAMIFICATION_PHP_VERSION');
        $info  = '';
        if (version_compare(PHP_VERSION, '5.5.19') < 0) {
            $result = array('type' => 'important', 'text' => JText::_('COM_GAMIFICATION_WARNING'));
        } else {
            $result = array('type' => 'success', 'text' => JText::_('JYES'));
        }
        GamificationInstallHelper::addRow($title, $result, $info);

        // Display result about MySQL Version.
        $title = JText::_('COM_GAMIFICATION_MYSQL_VERSION');
        $info  = '';
        $dbVersion = JFactory::getDbo()->getVersion();
        if (version_compare($dbVersion, '5.5.3', '<')) {
            $result = array('type' => 'important', 'text' => JText::_('COM_GAMIFICATION_WARNING'));
        } else {
            $result = array('type' => 'success', 'text' => JText::_('JYES'));
        }
        GamificationInstallHelper::addRow($title, $result, $info);

        // Display result about verification of installed Prism Library
        $info  = '';
        if (!class_exists('Prism\\Version')) {
            $title  = JText::_('COM_GAMIFICATION_PRISM_LIBRARY');
            $info   = JText::_('COM_GAMIFICATION_PRISM_LIBRARY_DOWNLOAD');
            $result = array('type' => 'important', 'text' => JText::_('JNO'));
        } else {
            $prismVersion   = new Prism\Version();
            $text           = JText::sprintf('COM_GAMIFICATION_CURRENT_V_S', $prismVersion->getShortVersion());

            if (class_exists('Gamification\\Version')) {
                $componentVersion = new Gamification\Version();
                $title            = JText::sprintf('COM_GAMIFICATION_PRISM_LIBRARY_S', $componentVersion->requiredPrismVersion);

                if (version_compare($prismVersion->getShortVersion(), $componentVersion->requiredPrismVersion, '<')) {
                    $info   = JText::_('COM_GAMIFICATION_PRISM_LIBRARY_DOWNLOAD');
                    $result = array('type' => 'warning', 'text' => $text);
                }

            } else {
                $title  = JText::_('COM_GAMIFICATION_PRISM_LIBRARY');
                $result = array('type' => 'success', 'text' => $text);
            }
        }
        GamificationInstallHelper::addRow($title, $result, $info);

        // Installed extensions

        GamificationInstallHelper::addRowHeading(JText::_('COM_GAMIFICATION_INSTALLED_EXTENSIONS'));

        // Gamification Library
        $result = array('type' => 'success', 'text' => JText::_('COM_GAMIFICATION_INSTALLED'));
        GamificationInstallHelper::addRow(JText::_('COM_GAMIFICATION_GAMIFICATION_LIBRARY'), $result, JText::_('COM_GAMIFICATION_LIBRARY'));

        // Gamification System Gamification
        $result = array('type' => 'success', 'text' => JText::_('COM_GAMIFICATION_INSTALLED'));
        GamificationInstallHelper::addRow(JText::_('COM_GAMIFICATION_PLUGIN_SYSTEM_GAMIFICATION'), $result, JText::_('COM_GAMIFICATION_PLUGIN'));

        // End table
        GamificationInstallHelper::endTable();

        echo JText::sprintf('COM_GAMIFICATION_MESSAGE_REVIEW_SAVE_SETTINGS', JRoute::_('index.php?option=com_gamification'));

        if (!class_exists('Prism\\Version')) {
            echo JText::_('COM_GAMIFICATION_MESSAGE_INSTALL_PRISM_LIBRARY');
        } else {
            if (class_exists('Gamification\\Version')) {
                $prismVersion     = new Prism\Version();
                $componentVersion = new Gamification\Version();
                if (version_compare($prismVersion->getShortVersion(), $componentVersion->requiredPrismVersion, '<')) {
                    echo JText::_('COM_GAMIFICATION_MESSAGE_INSTALL_PRISM_LIBRARY');
                }
            }
        }
    }
}
