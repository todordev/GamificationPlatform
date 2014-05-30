<?php
/**
 * @package      GamificationPlatform
 * @subpackage   GamificationLibrary
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

/**
 * This class provides information about extension version.
 *
 * @package      GamificationPlatform
 * @subpackage   GamificationLibrary
 */
class GamificationVersion
{
    /**
     * Extension name
     *
     * @var string
     */
    public $product = 'Gamification Platform';

    /**
     * Main Release Level
     *
     * @var integer
     */
    public $release = '1';

    /**
     * Sub Release Level
     *
     * @var integer
     */
    public $devLevel = '3';

    /**
     * Release Type
     *
     * @var integer
     */
    public $releaseType = 'Lite';

    /**
     * Development Status
     *
     * @var string
     */
    public $devStatus = 'Stable';

    /**
     * Date
     *
     * @var string
     */
    public $releaseDate = '30 May, 2014';

    /**
     * A link to license page.
     *
     * @var string
     */
    public $license = '<a href="http://www.gnu.org/copyleft/gpl.html" target="_blank">GNU/GPL</a>';

    /**
     * Copyright Text
     *
     * @var string
     */
    public $copyright = '&copy; 2014 ITPrism. All rights reserved.';

    /**
     * URL to extension page.
     *
     * @var string
     */
    public $url = '<a href="http://itprism.com/free-joomla-extensions/ecommerce-gamification/game-mechanics-platform" target="_blank">Gamification Platform</a>';

    /**
     * Backlink to extension page.
     *
     * @var string
     */
    public $backlink = '<div style="width:100%; text-align: left; font-size: xx-small; margin-top: 10px;"><a href="http://itprism.com/free-joomla-extensions/ecommerce-gamification/game-mechanics-platform" target="_blank">Joomla! Gamification Platform</a></div>';

    /**
     * Link to the website of the developer.
     *
     * @var string
     */
    public $developer = '<a href="http://itprism.com" target="_blank">ITPrism</a>';

    /**
     *  Build long format of the version text.
     *
     * @return string Long format version.
     */
    public function getLongVersion()
    {
        return
            $this->product . ' ' . $this->release . '.' . $this->devLevel . ' ' .
            $this->devStatus . ' ' . $this->releaseDate;
    }

    /**
     *  Build medium format of the version text.
     *
     * @return string Medium format version
     */
    public function getMediumVersion()
    {
        return
            $this->release . '.' . $this->devLevel . ' ' .
            $this->releaseType . ' ( ' . $this->devStatus . ' )';
    }

    /**
     *  Build short format of the version text
     *
     * @return string Short version format
     */
    public function getShortVersion()
    {
        return $this->release . '.' . $this->devLevel;
    }
}
