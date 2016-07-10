<?php
/**
 * @package      Gamification Platform
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

use Joomla\Utilities\ArrayHelper;
use Joomla\Registry\Registry;

// no direct access
defined('_JEXEC') or die;

// Register Observers
JLoader::register('GamificationObserverBadge', GAMIFICATION_PATH_COMPONENT_ADMINISTRATOR .'/tables/observers/badge.php');
JObserverMapper::addObserverClassToClass('GamificationObserverBadge', 'GamificationTableBadge', array('typeAlias' => 'com_gamification.badge'));

class GamificationModelBadge extends JModelAdmin
{
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   string $type   The table type to instantiate
     * @param   string $prefix A prefix for the table class name. Optional.
     * @param   array  $config Configuration array for model. Optional.
     *
     * @return  GamificationTableBadge  A database object
     * @since   1.6
     */
    public function getTable($type = 'Badge', $prefix = 'GamificationTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to get the record form.
     *
     * @param   array   $data     An optional array of data for the form to interrogate.
     * @param   boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return  JForm   A JForm object on success, false on failure
     * @since   1.6
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm($this->option . '.badge', 'badge', array('control' => 'jform', 'load_data' => $loadData));
        if (!$form) {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return  mixed   The data for the form.
     * @since   1.6
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $app  = JFactory::getApplication();
        $data = $app->getUserState($this->option . '.edit.badge.data', array());
        if (!$data) {
            $data = $this->getItem();

            // Set previous used group.
            if (!$data->id) {
                $data->group_id = $app->getUserState($this->option . '.badge.group_id', 0);
            }

            if ((int)$data->points_number === 0) {
                $data->points_number = '';
            }
        }

        return $data;
    }

    /**
     * Save data into the DB
     *
     * @param array $data The data about item
     *
     * @throws \InvalidArgumentException
     * @throws \RuntimeException
     * @throws \UnexpectedValueException
     *
     * @return  int
     */
    public function save($data)
    {
        $id        = ArrayHelper::getValue($data, 'id');
        $title     = ArrayHelper::getValue($data, 'title');
        $points    = ArrayHelper::getValue($data, 'points_number');
        $pointsId  = ArrayHelper::getValue($data, 'points_id');
        $groupId   = ArrayHelper::getValue($data, 'group_id');
        $published = ArrayHelper::getValue($data, 'published');
        $note      = ArrayHelper::getValue($data, 'note');
        $params    = ArrayHelper::getValue($data, 'params', [], 'array');

        $description  = ArrayHelper::getValue($data, 'description');
        $activityText = ArrayHelper::getValue($data, 'activity_text');

        $customData = Gamification\Helper::prepareCustomData($data);
        $params     = new Registry($params);
        
        // Load a record from the database
        $row = $this->getTable();
        /** @var $row GamificationTableBadge */

        $row->load($id);

        $row->set('title', $title);
        $row->set('points_number', $points);
        $row->set('points_id', $pointsId);
        $row->set('group_id', $groupId);
        $row->set('published', $published);
        $row->set('note', $note);
        $row->set('description', $description);
        $row->set('activity_text', $activityText);
        $row->set('custom_data', $customData);
        $row->set('params', $params->toString());

        $this->prepareTable($row);
        $this->prepareImage($row, $data);

        $row->store(true);

        return $row->get('id');
    }

    /**
     * Prepare and sanitise the table prior to saving.
     *
     * @param GamificationTableBadge $table
     *
     * @throws \RuntimeException
     */
    protected function prepareTable($table)
    {
        if (!$table->get('note')) {
            $table->set('note', null);
        }

        if (!$table->get('description')) {
            $table->set('note', null);
        }

        if (!$table->get('activity_text')) {
            $table->set('note', null);
        }

        // get maximum order number
        if (!$table->get('id') and !$table->get('ordering')) {
            // Set ordering to the last item if not set
            $db    = JFactory::getDbo();
            $query = $db->getQuery(true);
            $query
                ->select('MAX(a.ordering)')
                ->from($db->quoteName('#__gfy_badges', 'a'))
                ->where('a.group_id = ' . (int)$table->get('group_id'));

            $db->setQuery($query, 0, 1);
            $max = (int)$db->loadResult();

            $table->set('ordering', $max + 1);
        }
    }

    /**
     * Prepare and sanitise the table prior to saving.
     *
     * @param GamificationTableBadge $table
     * @param array                  $data
     *
     * @throws \UnexpectedValueException
     *
     * @since    1.6
     */
    protected function prepareImage($table, $data)
    {
        if (!empty($data['image'])) {
            // Delete old image if I upload the new one
            if ($table->get('image')) {
                $params     = JComponentHelper::getParams($this->option);
                /** @var  $params Joomla\Registry\Registry */

                $filesystemHelper   = new Prism\Filesystem\Helper($params);
                $mediaFolder        = $filesystemHelper->getMediaFolder();

                $file = JPath::clean(JPATH_ROOT .DIRECTORY_SEPARATOR. $mediaFolder .DIRECTORY_SEPARATOR. $table->get('image'));

                if (JFile::exists($file)) {
                    JFile::delete($file);
                }
            }
            $table->set('image', $data['image']);
        }
    }

    public function removeImage($id)
    {
        // Load a record from the database
        $row = $this->getTable();
        $row->load($id);

        if ($row->get('image')) {
            $params     = JComponentHelper::getParams($this->option);
            /** @var  $params Joomla\Registry\Registry */

            $filesystemHelper   = new Prism\Filesystem\Helper($params);
            $mediaFolder        = $filesystemHelper->getMediaFolder();

            $file = JPath::clean(JPATH_ROOT .DIRECTORY_SEPARATOR. $mediaFolder .DIRECTORY_SEPARATOR. $row->get('image'));

            if (JFile::exists($file)) {
                JFile::delete($file);
            }
        }

        $row->set('image', null);
        $row->store(true);
    }

    /**
     * Store the file in a folder of the extension.
     *
     * @param array $image
     *
     * @throws \InvalidArgumentException
     * @throws \Exception
     * @throws \UnexpectedValueException
     * @throws \RuntimeException
     *
     * @return string
     */
    public function uploadImage($image)
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        $uploadedFile = ArrayHelper::getValue($image, 'tmp_name');
        $uploadedName = ArrayHelper::getValue($image, 'name');
        $errorCode    = ArrayHelper::getValue($image, 'error');

        $params     = JComponentHelper::getParams($this->option);
        /** @var  $params Joomla\Registry\Registry */

        $filesystemHelper   = new Prism\Filesystem\Helper($params);
        $mediaFolder        = $filesystemHelper->getMediaFolder();

        $destinationFolder = JPath::clean(JPATH_ROOT .DIRECTORY_SEPARATOR. $mediaFolder);

        // Joomla! media extension parameters
        $mediaParams = JComponentHelper::getParams('com_media');
        /** @var $mediaParams Joomla\Registry\Registry */

        $file = new Prism\File\File();

        // Prepare size validator.
        $KB            = 1024 * 1024;
        $fileSize      = (int)$app->input->server->get('CONTENT_LENGTH');
        $uploadMaxSize = $mediaParams->get('upload_maxsize') * $KB;

        // Prepare file validators.
        $sizeValidator   = new Prism\File\Validator\Size($fileSize, $uploadMaxSize);
        $serverValidator = new Prism\File\Validator\Server($errorCode, array(UPLOAD_ERR_NO_FILE));
        $imageValidator  = new Prism\File\Validator\Image($uploadedFile, $uploadedName);

        // Get allowed mime types from media manager options
        $mimeTypes = explode(',', $mediaParams->get('upload_mime'));
        $imageValidator->setMimeTypes($mimeTypes);

        // Get allowed image extensions from media manager options
        $imageExtensions = explode(',', $mediaParams->get('image_extensions'));
        $imageValidator->setImageExtensions($imageExtensions);

        $file
            ->addValidator($sizeValidator)
            ->addValidator($serverValidator)
            ->addValidator($imageValidator);

        // Validate the file
        if (!$file->isValid()) {
            throw new RuntimeException($file->getError());
        }

        // Generate temporary file name
        $ext = strtolower(JFile::makeSafe(JFile::getExt($image['name'])));

        $generatedName = Prism\Utilities\StringHelper::generateRandomString(16);

        $imageName   = $generatedName . '_badge.' . $ext;
        $destination = JPath::clean($destinationFolder .DIRECTORY_SEPARATOR. $imageName);

        // Prepare uploader object.
        $uploader = new Prism\File\Uploader\Local($uploadedFile);
        $uploader->setDestination($destination);

        // Upload temporary file
        $file->setUploader($uploader);

        $file->upload();

        $source = $file->getFile();

        return basename($source);
    }

    /**
     * A protected method to get a set of ordering conditions.
     *
     * @param    GamificationTableBadge $table
     *
     * @return    array    An array of conditions to add to add to ordering queries.
     * @since    1.6
     */
    protected function getReorderConditions($table)
    {
        $condition   = array();
        $condition[] = 'group_id = ' . (int)$table->get('group_id');

        return $condition;
    }
}
