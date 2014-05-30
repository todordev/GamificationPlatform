<?php
/**
 * @package      Gamification Platform
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2014 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

class GamificationModelBadge extends JModelAdmin
{
    /**
     *
     * A folder where images will be saved
     * @var string
     */
    public $imagesFolder = "";

    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   string $type   The table type to instantiate
     * @param   string $prefix A prefix for the table class name. Optional.
     * @param   array  $config Configuration array for model. Optional.
     *
     * @return  JTable  A database object
     * @since   1.6
     */
    public function getTable($type = 'Badge', $prefix = 'GamificationTable', $config = array())
    {
        return JTable::getInstance($type, $prefix, $config);
    }

    /**
     * Method to get the record form.
     *
     * @param   array   $data     An optional array of data for the form to interogate.
     * @param   boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return  JForm   A JForm object on success, false on failure
     * @since   1.6
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Get the form.
        $form = $this->loadForm($this->option . '.badge', 'badge', array('control' => 'jform', 'load_data' => $loadData));
        if (empty($form)) {
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
        $data = JFactory::getApplication()->getUserState($this->option . '.edit.badge.data', array());
        if (empty($data)) {
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
        $id        = JArrayHelper::getValue($data, "id");
        $title     = JArrayHelper::getValue($data, "title");
        $points    = JArrayHelper::getValue($data, "points");
        $pointsId  = JArrayHelper::getValue($data, "points_id");
        $groupId   = JArrayHelper::getValue($data, "group_id");
        $published = JArrayHelper::getValue($data, "published");
        $note      = JArrayHelper::getValue($data, "note");

        if (!$note) {
            $note = null;
        }

        // Load a record from the database
        $row = $this->getTable();
        /** @var $row GamificationTableBadge */

        $row->load($id);

        $row->set("title", $title);
        $row->set("points", $points);
        $row->set("points_id", $pointsId);
        $row->set("group_id", $groupId);
        $row->set("published", $published);
        $row->set("note", $note);

        $this->prepareImage($row, $data);

        $row->store(true);

        return $row->get("id");
    }

    /**
     * Prepare and sanitise the table prior to saving.
     *
     * @param GamificationTableBadge $table
     * @param array                  $data
     *
     * @since    1.6
     */
    protected function prepareImage($table, $data)
    {
        if (!empty($data["image"])) {

            // Delete old image if I upload the new one
            if (!empty($table->image)) {

                // Remove an image from the filesystem
                $fileImage = $this->imagesFolder . DIRECTORY_SEPARATOR . $table->image;

                if (is_file($fileImage)) {
                    JFile::delete($fileImage);
                }

            }
            $table->set("image", $data["image"]);
        }
    }

    public function removeImage($id)
    {
        // Load a record from the database
        $row = $this->getTable();
        $row->load($id);

        if (!empty($row->image)) {
            $file = JPath::clean($this->imagesFolder . DIRECTORY_SEPARATOR . $row->image);

            if (JFile::exists($file)) {
                JFile::delete($file);
            }
        }

        $row->set("image", "");
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

        $uploadedFile = JArrayHelper::getValue($image, 'tmp_name');
        $uploadedName = JArrayHelper::getValue($image, 'name');

        // Joomla! media extension parameters
        $mediaParams = JComponentHelper::getParams("com_media");

        jimport("itprism.file");
        jimport("itprism.file.uploader.local");
        jimport("itprism.file.validator.size");
        jimport("itprism.file.validator.image");

        $file = new ITPrismFile();

        // Prepare size validator.
        $KB            = 1024 * 1024;
        $fileSize      = (int)$app->input->server->get('CONTENT_LENGTH');
        $uploadMaxSize = $mediaParams->get("upload_maxsize") * $KB;

        $sizeValidator = new ITPrismFileValidatorSize($fileSize, $uploadMaxSize);


        // Prepare image validator.
        $imageValidator = new ITPrismFileValidatorImage($uploadedFile, $uploadedName);

        // Get allowed mime types from media manager options
        $mimeTypes = explode(",", $mediaParams->get("upload_mime"));
        $imageValidator->setMimeTypes($mimeTypes);

        // Get allowed image extensions from media manager options
        $imageExtensions = explode(",", $mediaParams->get("image_extensions"));
        $imageValidator->setImageExtensions($imageExtensions);

        $file
            ->addValidator($sizeValidator)
            ->addValidator($imageValidator);

        // Validate the file
        $file->validate();

        // Generate temporary file name
        $ext = JString::strtolower(JFile::makeSafe(JFile::getExt($image['name'])));

        jimport("itprism.string");
        $generatedName = new ITPrismString();
        $generatedName->generateRandomString(16);

        $imageName   = $generatedName . "_badge." . $ext;
        $destination = $this->imagesFolder . DIRECTORY_SEPARATOR . $imageName;

        // Prepare uploader object.
        $uploader = new ITPrismFileUploaderLocal($image);
        $uploader->setDestination($destination);

        // Upload temporary file
        $file->setUploader($uploader);

        $file->upload();

        $source = $file->getFile();

        return basename($source);
    }
}
