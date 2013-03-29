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

jimport('joomla.application.component.modeladmin');

class GamificationModelBadge extends JModelAdmin {
    
    /**
     * 
     * A folder where images will be saved
     * @var string
     */
    public $imagesFolder  = "";
    
    /**
     * @var     string  The prefix to use with controller messages.
     * @since   1.6
     */
    protected $text_prefix = 'COM_GAMIFICATION';
    
    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param   type    The table type to instantiate
     * @param   string  A prefix for the table class name. Optional.
     * @param   array   Configuration array for model. Optional.
     * @return  JTable  A database object
     * @since   1.6
     */
    public function getTable($type = 'Badge', $prefix = 'GamificationTable', $config = array()){
        return JTable::getInstance($type, $prefix, $config);
    }
    
    /**
     * Method to get the record form.
     *
     * @param   array   $data       An optional array of data for the form to interogate.
     * @param   boolean $loadData   True if the form is to load its own data (default case), false if not.
     * @return  JForm   A JForm object on success, false on failure
     * @since   1.6
     */
    public function getForm($data = array(), $loadData = true){
        
        // Get the form.
        $form = $this->loadForm($this->option.'.badge', 'badge', array('control' => 'jform', 'load_data' => $loadData));
        if(empty($form)){
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
    protected function loadFormData(){
        // Check the session for previously entered form data.
        $data = JFactory::getApplication()->getUserState($this->option.'.edit.badge.data', array());
        if(empty($data)){
            $data = $this->getItem();
        }
        
        return $data;
    }
    
    /**
     * Save data into the DB
     * 
     * @param array   $data   The data about item
     * @return     	  Item ID
     */
    public function save($data){
        
        $id           = JArrayHelper::getValue($data, "id");
        $title        = JArrayHelper::getValue($data, "title");
        $points       = JArrayHelper::getValue($data, "points");
        $pointsType   = JArrayHelper::getValue($data, "points_type");
        $groupId      = JArrayHelper::getValue($data, "group_id");
        $published    = JArrayHelper::getValue($data, "published");
        
        // Load a record from the database
        $row = $this->getTable();
        $row->load($id);
        
        $row->set("title",       $title);
        $row->set("points",      $points);
        $row->set("points_type", $pointsType);
        $row->set("group_id",    $groupId);
        $row->set("published",   $published);
        
        $this->prepareTable($row, $data);
        
        $row->store();
        
        return $row->id;
    
    }
    
	/**
	 * Prepare and sanitise the table prior to saving.
	 * @since	1.6
	 */
	protected function prepareTable($table, $data) {
	    
        if(!empty($data["image"])){
            
            // Delete old image if I upload the new one
            if(!empty($table->image)){
                
                // Remove an image from the filesystem
                $fileImage = $this->imagesFolder .DIRECTORY_SEPARATOR. $table->image;
                
                if(is_file($fileImage)) {
                    JFile::delete($fileImage);
                }
                
            
            }
            $table->set("image", $data["image"]);
        }
        
	}
	
	public function removeImage($id) {
	    
	    
	    // Load a record from the database
        $row = $this->getTable();
        $row->load($id);
        
        if(!empty($row->image)) {
            $file = $this->imagesFolder.DIRECTORY_SEPARATOR.$row->image;
            
            if(is_file($file)) {
                JFile::delete($file);
            }
        }
        
        $row->set("image", "");
        $row->store();
        
	}
	
}