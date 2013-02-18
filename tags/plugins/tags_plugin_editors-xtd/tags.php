<?php
/**
 * @package Tags
 * @author  Dioscouri Design
 * @link    http://www.dioscouri.com
 * @copyright Copyright (C) 2009 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

/** Import library dependencies */
jimport('joomla.plugin.plugin');

class plgButtonTags extends DSCPlugin
{
    var $_element = 'tags';
    
    function __construct( &$subject, $params )
    {
        $editor = JFactory::getEditor();
        parent::__construct( $subject, $params );
    }
    
    /**
     * Checks to make sure that this plugin is the one being triggered by the extension
     *
     * @access public
     * @return mixed Parameter value
     * @since 1.5
     */
    function _isMe( $row ) 
    {
        $element = $this->_element;
        $success = false;
        if (is_object($row) && !empty($row->element) && $row->element == $element )
        {
            $success = true;
        }
        
        if (is_string($row) && $row == $element ) {
            $success = true;
        }
        
        return $success;
    }
    
    /**
     * Checks if extension is installed
     *
     * @return boolean
     */
    private function isInstalled()
    {
        $success = false;
    
        jimport('joomla.filesystem.file');
        if (JFile::exists( JPATH_ADMINISTRATOR . '/components/com_tags/defines.php' ))
        {
            JLoader::register( "Tags", JPATH_ADMINISTRATOR . "components/com_ambra/defines.php" );
    
            $parentPath = JPATH_ADMINISTRATOR . '/components/com_tags/helpers';
            DSCLoader::discover('TagsHelper', $parentPath, true);
    
            $parentPath = JPATH_ADMINISTRATOR . '/components/com_tags/library';
            DSCLoader::discover('Tags', $parentPath, true);
    
            $helper = new TagsHelperContent();
            if ($helper->getArticleScopeId())
            {
                $success = true;
            }
        }
        return $success;
    }
    
    /**
     *  
     * 
     * @param unknown_type $name
     */
    function onDisplay($name)
    {
        if (!$this->isInstalled())
        {
            return '';
        }
        
        $option = JRequest::getVar('option');
        if ($option == 'com_content')
        {
            $id = JRequest::getVar( 'id', JRequest::getVar( 'id', '0', 'post', 'int' ), 'get', 'int' );
	        $array = JRequest::getVar('cid', array( $id ), 'request', 'array');
	        
	        $identifier = @$array[0]; 
	        $scope = 'com_content.article';
	        
	        JLoader::register('TagsHelperTags', JPATH_ADMINISTRATOR . '/components/com_tags/helpers/tags.php');
	        $helper = new TagsHelperTags();
	        echo $helper->getForm( $identifier, $scope );
        }       
    }
    
}
?>