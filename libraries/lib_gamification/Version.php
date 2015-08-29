<?php
/**
 * @package      Gamification
 * @subpackage   Version
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2015 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

namespace Gamification;

defined('JPATH_PLATFORM') or die;

/**
 * This class provides information about extension version.
 *
 * @package      Gamification
 * @subpackage   Version
 */
class Version
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
    public $release = '2';

    /**
     * Sub Release Level
     *
     * @var integer
     */
    public $devLevel = '0';

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
    public $releaseDate = '31 August, 2015';

    /**
     * A link to license page.
     *
     * @var string
     */
    public $license = '<a href="http://www.gnu.org/licenses/gpl-3.0.en.html" target="_blank">GNU/GPLv3</a>';

    /**
     * Copyright Text
     *
     * @var string
     */
    public $copyright = '&copy; 2015 ITPrism. All rights reserved.';

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
     * Minimum required version of Prism library.
     *
     * @var string
     */
    public $requiredPrismVersion = '1.3';

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
