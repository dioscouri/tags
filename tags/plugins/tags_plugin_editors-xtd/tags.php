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

class plgButtonTags extends JPlugin
{
    var $_element = 'tags';
    
    function __construct( &$subject, $params )
    {
        $editor = &JFactory::getEditor();
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
     *  
     * 
     * @param unknown_type $name
     */
    function onDisplay($name)
    {
        $option = JRequest::getVar('option');
        if ($option == 'com_content')
        {
            $id = JRequest::getVar( 'id', JRequest::getVar( 'id', '0', 'post', 'int' ), 'get', 'int' );
	        $array = JRequest::getVar('cid', array( $id ), 'request', 'array');
	        
	        if (!empty($array[0]))
	        {
	        	JLoader::register('TagsHelperContent', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tags'.DS.'helpers'.DS.'content.php');
        		$helper = new TagsHelperContent();     
	        	$tags = $helper->getTagsForArticle( $array[0] );
	        }
	        
	        $vars = new JObject();
	        $vars->tags = @$tags;
	        
	        echo $this->getLayout('form', $vars);
        }       
    }
    
    /**
     * Gets the parsed layout file
     * 
     * @param string $layout The name of  the layout file
     * @param object $vars Variables to assign to
     * @param string $plugin The name of the plugin
     * @param string $group The plugin's group
     * @return string
     * @access protected
     */
    function getLayout($layout, $vars = false, $plugin = '', $group = 'editors-xtd' )
    {
        if (empty($plugin)) 
        {
            $plugin = $this->_element;
        }
        
        ob_start();
        $layout = $this->getLayoutPath( $plugin, $group, $layout ); 
        include($layout);
        $html = ob_get_contents(); 
        ob_end_clean();
        
        return $html;
    }
    
    
    /**
     * Get the path to a layout file
     *
     * @param   string  $plugin The name of the plugin file
     * @param   string  $group The plugin's group
     * @param   string  $layout The name of the plugin layout file
     * @return  string  The path to the plugin layout file
     * @access protected
     */
    function getLayoutPath($plugin, $group, $layout = 'default')
    {
        $app = JFactory::getApplication();

        // get the template and default paths for the layout
        $templatePath = JPATH_SITE.DS.'templates'.DS.$app->getTemplate().DS.'html'.DS.'plugins'.DS.$group.DS.$plugin.DS.$layout.'.php';
        $defaultPath = JPATH_SITE.DS.'plugins'.DS.$group.DS.$plugin.DS.'tmpl'.DS.$layout.'.php';

        // if the site template has a layout override, use it
        jimport('joomla.filesystem.file');
        if (JFile::exists( $templatePath )) 
        {
            return $templatePath;
        } 
            else 
        {
            return $defaultPath;
        }
    }
}
?>