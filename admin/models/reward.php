<?php
/**
 * @package      Gamification Platform
 * @subpackage   Components
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

use Joomla\Utilities\ArrayHelper;

// no direct access
defined('_JEXEC') or die;

// Register Observers
JLoader::register('GamificationObserverReward', GAMIFICATION_PATH_COMPONENT_ADMINISTRATOR .'/tables/observers/reward.php');
JObserverMapper::addObserverClassToClass('GamificationObserverReward', 'GamificationTableReward', array('typeAlias' => 'com_gamification.reward'));

class GamificationModelReward extends JModelAdmin
{
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   string $type   The table type to instantiate
     * @param   string $prefix A prefix for the table class name. Optional.
     * @param   array  $config Configuration array for model. Optional.
     *
     * @return  GamificationTableReward
     * @since   1.6
     */
    public function getTable($type = 'Reward', $prefix = 'GamificationTable', $config = array())
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
        $form = $this->loadForm($this->option . '.reward', 'reward', array('control' => 'jform', 'load_data' => $loadData));
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
        $data = JFactory::getApplication()->getUserState($this->option . '.edit.reward.data', array());
        if (!$data) {
            $data = $this->getItem();
            
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
     * @throws \InvalidArgumentException
     * @throws \UnexpectedValueException
     * @throws \RuntimeException
     *
     * @return  int
     */
    public function save($data)
    {
        $id        = ArrayHelper::getValue($data, 'id');
        $title     = ArrayHelper::getValue($data, 'title');
        $groupId   = ArrayHelper::getValue($data, 'group_id', 0, 'int');
        $published = ArrayHelper::getValue($data, 'published', 0, 'int');
        $description = ArrayHelper::getValue($data, 'description');

        // Get advanced options.
        $activityText = ArrayHelper::getValue($data, 'activity_text');
        $number       = ArrayHelper::getValue($data, 'number');
        $note         = ArrayHelper::getValue($data, 'note');
        $points       = ArrayHelper::getValue($data, 'points_number', 0, 'int');
        $pointsId     = ArrayHelper::getValue($data, 'points_id', 0, 'int');

        if (!is_numeric($number) and !$number) {
            $number = null;
        }

        if (!$note) {
            $note = null;
        }

        if (!$description) {
            $description = null;
        }

        if (!$activityText) {
            $activityText = null;
        }

        // Load a record from the database
        $row = $this->getTable();
        /** @var $row GamificationTableReward */

        $row->load($id);

        $row->set('title', $title);
        $row->set('points_number', $points);
        $row->set('points_id', $pointsId);
        $row->set('group_id', $groupId);
        $row->set('published', $published);
        $row->set('note', $note);
        $row->set('description', $description);
        $row->set('activity_text', $activityText);
        $row->set('number', $number);

        $this->prepareImage($row, $data);

        $row->store(true);

        return $row->get('id');
    }

    /**
     * Prepare and sanitise the table prior to saving.
     *
     * @param GamificationTableReward $table
     * @param array                  $data
     *
     * @throws \UnexpectedValueException
     *
     * @since    1.6
     */
    protected function prepareImage($table, $data)
    {
        if (!empty($data['image'])) {
            // Delete old image if I upload new one.
            if ($table->get('image')) {
                $params     = JComponentHelper::getParams($this->option);
                /** @var  $params Joomla\Registry\Registry */

                $filesystemHelper   = new Prism\Filesystem\Helper($params);
                $mediaFolder        = $filesystemHelper->getMediaFolder();

                $fileImage  = JPath::clean(JPATH_ROOT .DIRECTORY_SEPARATOR .$mediaFolder. DIRECTORY_SEPARATOR. $table->get('image'));
                $fileSmall  = JPath::clean(JPATH_ROOT .DIRECTORY_SEPARATOR .$mediaFolder. DIRECTORY_SEPARATOR. $table->get('image_small'));
                $fileSquare = JPath::clean(JPATH_ROOT .DIRECTORY_SEPARATOR .$mediaFolder. DIRECTORY_SEPARATOR. $table->get('image_square'));

                if (is_file($fileImage)) {
                    JFile::delete($fileImage);
                }

                if (is_file($fileSmall)) {
                    JFile::delete($fileSmall);
                }

                if (is_file($fileSquare)) {
                    JFile::delete($fileSquare);
                }

            }
            $table->set('image', $data['image']);
            $table->set('image_small', $data['image_small']);
            $table->set('image_square', $data['image_square']);
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

            $fileImage  = JPath::clean(JPATH_ROOT . DIRECTORY_SEPARATOR .$mediaFolder. DIRECTORY_SEPARATOR . $row->get('image'));
            $fileSmall  = JPath::clean(JPATH_ROOT . DIRECTORY_SEPARATOR .$mediaFolder. DIRECTORY_SEPARATOR . $row->get('image_small'));
            $fileSquare = JPath::clean(JPATH_ROOT . DIRECTORY_SEPARATOR .$mediaFolder. DIRECTORY_SEPARATOR . $row->get('image_square'));

            if (is_file($fileImage)) {
                JFile::delete($fileImage);
            }

            if (is_file($fileSmall)) {
                JFile::delete($fileSmall);
            }

            if (is_file($fileSquare)) {
                JFile::delete($fileSquare);
            }
        }

        $row->set('image', null);
        $row->set('image_small', null);
        $row->set('image_square', null);
        $row->store(true);
    }

    /**
     * Store the file in a folder of the extension.
     *
     * @param array $image
     * @param bool $resizeImage
     *
     * @throws \RuntimeException
     * @throws \Exception
     * @throws \InvalidArgumentException
     *
     * @return array
     */
    public function uploadImage($image, $resizeImage = false)
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

        $destinationFolder  = JPath::clean(JPATH_ROOT .DIRECTORY_SEPARATOR. $mediaFolder);
        $temporaryFolder    = $app->get('tmp_path');

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

        $generatedName = Prism\Utilities\StringHelper::generateRandomString();

        $temporaryFile = $generatedName.'_reward.'. $ext;
        $temporaryDestination = JPath::clean($temporaryFolder .DIRECTORY_SEPARATOR. $temporaryFile);

        // Prepare uploader object.
        $uploader = new Prism\File\Uploader\Local($uploadedFile);
        $uploader->setDestination($temporaryDestination);

        // Upload temporary file
        $file->setUploader($uploader);
        $file->upload();

        $temporaryFile = $file->getFile();
        if (!is_file($temporaryFile)) {
            throw new Exception('COM_GAMIFICATION_ERROR_FILE_CANT_BE_UPLOADED');
        }

        // Resize image
        $image = new JImage();
        $image->loadFile($temporaryFile);
        if (!$image->isLoaded()) {
            throw new Exception(JText::sprintf('COM_GAMIFICATION_ERROR_FILE_NOT_FOUND', $temporaryDestination));
        }

        $imageName  = $generatedName . '_image.png';
        $smallName  = $generatedName . '_small.png';
        $squareName = $generatedName . '_square.png';

        $imageFile  = $destinationFolder .DIRECTORY_SEPARATOR. $imageName;
        $smallFile  = $destinationFolder .DIRECTORY_SEPARATOR. $smallName;
        $squareFile = $destinationFolder .DIRECTORY_SEPARATOR. $squareName;

        $scaleOption = $params->get('image_resizing_scale', JImage::SCALE_INSIDE);

        // Create main image
        if (!$resizeImage) {
            $image->toFile($imageFile, IMAGETYPE_PNG);
        } else {
            $width  = $params->get('image_width', 200);
            $height = $params->get('image_height', 200);
            $image->resize($width, $height, false, $scaleOption);
            $image->toFile($imageFile, IMAGETYPE_PNG);
        }

        // Create small image
        $width  = $params->get('image_small_width', 100);
        $height = $params->get('image_small_height', 100);
        $image->resize($width, $height, false, $scaleOption);
        $image->toFile($smallFile, IMAGETYPE_PNG);

        // Create square image
        $width  = $params->get('image_square_width', 50);
        $height = $params->get('image_square_height', 50);
        $image->resize($width, $height, false, $scaleOption);
        $image->toFile($squareFile, IMAGETYPE_PNG);

        $names = array(
            'image'        => $imageName,
            'image_small'  => $smallName,
            'image_square' => $squareName
        );

        // Remove the temporary file.
        if (JFile::exists($temporaryFile)) {
            JFile::delete($temporaryFile);
        }

        return $names;
    }
}
