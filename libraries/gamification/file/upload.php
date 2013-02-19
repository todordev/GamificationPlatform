<?php
/**
* @package      ITPrism Components
* @subpackage   Gamification
* @author       Todor Iliev
* @copyright    Copyright (C) 2010 Todor Iliev <todor@itprism.com>. All rights reserved.
* @license      http://www.gnu.org/copyleft/gpl.html GNU/GPL
* Gamification is free software. This vpversion may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/

defined('JPATH_PLATFORM') or die;

/**
 * This class provides functionality for uploading files and   
 * validates the process.
 */
class GamificationFileUpload {
    
    private $file = array();
    private $dest = "";
    
    /**
     * Initilazie the object
     * @param  array $file	An array that comes from JInput
     * @param  array $dest	Destination of the file where is going to be saved
     * @throws Exception 	If there is an error the object will throw an exception.
     */
    public function __construct($file, $dest = null) {
        $this->file = $file;
        $this->dest = $dest;
    }
    
    public function upload($dest = null){
        
        if(!empty($dest)) {
            $this->dest = $dest;
        }
        
        if(!empty($this->file['name'])){
            
            if(!JFile::upload($this->file["tmp_name"], $this->dest)){
                throw new Exception(JText::_('COM_GAMIFICATION_ERROR_FILE_CANT_BE_UPLOADED'), GamificationErrors::CODE_WARNING);
            }
            
        }

        return $this->dest;
    
    }
    
    public function validate(){
        
        $app = JFactory::getApplication();
        
        $serverContentLength = (int)$app->input->server->get('CONTENT_LENGTH');
        
        // Verify file size
        $uploadMaxFileSize   = (int)ini_get('upload_max_filesize');
        $uploadMaxFileSize   = $uploadMaxFileSize * 1024 * 1024;
        
        $postMaxSize         = (int)(ini_get('post_max_size'));
        $postMaxSize         = $postMaxSize * 1024 * 1024;
        
        $memoryLimit         = (int)(ini_get('memory_limit'));
        $memoryLimit         = $memoryLimit * 1024 * 1024;
        
        if(
			$serverContentLength >  $uploadMaxFileSize OR
			$serverContentLength >  $postMaxSize OR
			$serverContentLength >  $memoryLimit
		)
		{ // Log error
		    $KB    = 1024 * 1024;
		    
		    $info = JText::sprintf("COM_GAMIFICATION_ERROR_FILE_INFOMATION", 
		        round($serverContentLength/$KB, 0), 
		        round($uploadMaxFileSize/$KB, 0), 
		        round($postMaxSize/$KB, 0), 
		        round($memoryLimit/$KB, 0)
	        );
	        
	        // Log error
		    JLog::add($info);
		    throw new Exception(JText::_("COM_GAMIFICATION_ERROR_WARNFILETOOLARGE"), GamificationErrors::CODE_WARNING);
		}
		
		// Check for server errors
        if( !empty($this->file['error']) ) {
                
            switch($this->file['error']){
                case UPLOAD_ERR_INI_SIZE:
                    throw new Exception(JText::_('COM_GAMIFICATION_ERROR_UPLOAD_ERR_INI_SIZE'), GamificationErrors::CODE_HIDDEN_WARNING);
                case UPLOAD_ERR_FORM_SIZE:
                    throw new Exception(JText::_('COM_GAMIFICATION_ERROR_UPLOAD_ERR_FORM_SIZE'), GamificationErrors::CODE_HIDDEN_WARNING);
                case UPLOAD_ERR_PARTIAL:
                    throw new Exception(JText::_('COM_GAMIFICATION_ERROR_UPLOAD_ERR_PARTIAL'), GamificationErrors::CODE_HIDDEN_WARNING);
                case UPLOAD_ERR_NO_FILE:
//                    throw new Exception( JText::_( 'COM_GAMIFICATION_ERROR_UPLOAD_ERR_NO_FILE' ), GamificationErrors::CODE_HIDDEN_WARNING);
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    throw new Exception(JText::_('COM_GAMIFICATION_ERROR_UPLOAD_ERR_NO_TMP_DIR'), GamificationErrors::CODE_HIDDEN_WARNING);
                case UPLOAD_ERR_CANT_WRITE:
                    throw new Exception(JText::_('COM_GAMIFICATION_ERROR_UPLOAD_ERR_CANT_WRITE'), GamificationErrors::CODE_HIDDEN_WARNING);
                case UPLOAD_ERR_EXTENSION:
                    throw new Exception(JText::_('COM_GAMIFICATION_ERROR_UPLOAD_ERR_EXTENSION'), GamificationErrors::CODE_HIDDEN_WARNING);
                default:
                    throw new Exception(JText::_('COM_GAMIFICATION_ERROR_UPLOAD_ERR_UNKNOWN'), GamificationErrors::CODE_HIDDEN_WARNING);
            }
        
        }
            
    }
}

