<?php
/**
 * @package      Gamification Platform
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

class GamificationModelChallenge extends JModelAdmin
{
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   string $type   The table type to instantiate
     * @param   string $prefix A prefix for the table class name. Optional.
     * @param   array  $config Configuration array for model. Optional.
     *
     * @return  GamificationTableChallenge
     * @since   1.6
     */
    public function getTable($type = 'Challenge', $prefix = 'GamificationTable', $config = array())
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
        $form = $this->loadForm($this->option . '.challenge', 'challenge', array('control' => 'jform', 'load_data' => $loadData));
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
        $data = JFactory::getApplication()->getUserState($this->option . '.edit.challenge.data', array());
        if (!$data) {
            $data = $this->getItem();
        }

        return $data;
    }

    /**
     * Save data into the DB
     *
     * @param array $data The data about item
     *
     * @return  int
     */
    public function save($data)
    {
        $id        = Joomla\Utilities\ArrayHelper::getValue($data, 'id');
        $title     = Joomla\Utilities\ArrayHelper::getValue($data, 'title');
        $groupId   = Joomla\Utilities\ArrayHelper::getValue($data, 'group_id', 0, 'int');
        $published = Joomla\Utilities\ArrayHelper::getValue($data, 'published', 0, 'int');
        $note      = Joomla\Utilities\ArrayHelper::getValue($data, 'note');
        $description = Joomla\Utilities\ArrayHelper::getValue($data, 'description');

        if (!$note) {
            $note = null;
        }

        if (!$description) {
            $description = null;
        }

        // Load a record from the database
        $row = $this->getTable();
        /** @var $row GamificationTableChallenge */

        $row->load($id);

        $row->set('title', $title);
        $row->set('group_id', $groupId);
        $row->set('published', $published);
        $row->set('note', $note);
        $row->set('description', $description);

        $this->prepareImage($row, $data);

        $row->store(true);

        return $row->get('id');
    }

    /**
     * Prepare and sanitise the table prior to saving.
     *
     * @param GamificationTableAchievement $table
     * @param array                  $data
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

                if (is_file($file)) {
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

        $row->set('image', '');
        $row->store();
    }

    /**
     * Store the file in a folder of the extension.
     *
     * @param array $image
     *
     * @return string
     */
    public function uploadImage($image)
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        $uploadedFile = Joomla\Utilities\ArrayHelper::getValue($image, 'tmp_name');
        $uploadedName = Joomla\Utilities\ArrayHelper::getValue($image, 'name');
        $errorCode    = Joomla\Utilities\ArrayHelper::getValue($image, 'error');

        $params     = JComponentHelper::getParams($this->option);
        /** @var  $params Joomla\Registry\Registry */

        $filesystemHelper   = new Prism\Filesystem\Helper($params);
        $mediaFolder        = $filesystemHelper->getMediaFolder();

        $destinationFolder  = JPath::clean(JPATH_ROOT .DIRECTORY_SEPARATOR. $mediaFolder);

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

        $imageName   = $generatedName . '_challenge.' . $ext;
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
}
