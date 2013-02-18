<?php
/**
 * @package Tags
 * @author  Dioscouri Design
 * @link    http://www.dioscouri.com
 * @copyright Copyright (C) 2011 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

/** Import library dependencies */
jimport('joomla.plugin.plugin');

class plgRSEventsTags extends JPlugin
{
    var $_element = 'tags';
    
    function __construct( &$subject, $params )
    {
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
     * Adds a new tag to an event
     * 
     * @return unknown_type
     */
    public function addTag( $element )
    {
        if ( !$this->_isMe( $element ) )
        {
            return '';
        }

        $elements = json_decode( preg_replace('/[\n\r]+/', '\n', JRequest::getVar( 'elements', '', 'post', 'string' ) ) );
        
        // convert elements to array that can be binded
        Tags::load( 'TagsHelperBase', 'helpers._base' );
        $helper = TagsHelperBase::getInstance();
        $submitted_values = $helper->elementsToArray( $elements );
        
        // prepare needed variables              
        $event_id = $submitted_values['cid'];
        $tag_name = trim( $submitted_values['tag_name'] );
        $user = JFactory::getUser(); 
        $scope_id = $this->getRSEventsScopeId();
        
        JLoader::register('TagsHelperTags', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tags'.DS.'helpers'.DS.'tags.php');
        $tags_helper = new TagsHelperTags();
        
        if (!empty($event_id))
        {        	  
	        $added_tags = $tags_helper->getTags( $event_id, $scope_id ); 
	        
	        // check if tag is already added to the article
	        foreach (@$added_tags as $tag)
	        {
	        	if( @$tag->tag_name == $tag_name )
	        	{	  
	        		$vars = new JObject();             
			           
			        $vars->tags = array();
			        $vars->id   = $event_id;        
			        $vars->tags = $added_tags;
			            
			       	$html = $this->getLayout('tags_list', $vars); 
				        
				    return $html;
	        	}
	        }
	        	
	        if( !empty( $tag_name ) )
		    {
			    $tag_id = $tags_helper->checkTagId( $tag_name );
			
			    // add new relationship
			    $tags_helper->addRelationship( $tag_id, $scope_id, $event_id );			    
		    }
		
		    $vars = new JObject();
		    $vars->tags = array();
		    $vars->id = $event_id;
	    	                
            $vars->tags = $tags_helper->getTags( $event_id, $scope_id ); 
            $layout = 'tags_list';     	
        }
        	else 
        {           	     	
	      	if ( !empty($submitted_values['vars']) )
	      	{
        		$vars = $submitted_values['vars'];
        		
		      	if( !in_array ( $tag_name , $vars ) && !empty( $tag_name ) )
		      	{
	        		$vars[] = $tag_name;
	        		sort( $vars );
		      	}
	      	}
        		else
        	{
        		if( !empty( $tag_name ) )
		      	{
		      		$vars[] = $tag_name;	
		      	}        			
        	}
	      	        	        	
        	$layout = 'tags_list_newevent';       	
        }

        $html = $this->getLayout($layout, $vars);
		return $html;
    }
    
	/**
     * Remove tag relationships for an event
     * 
     * @param string $element plugin element
     * @return string $html
     */
    function removeTag( $element  )
    {
        if (!$this->_isMe( $element) )
        {
            return '';
        }

        $elements = json_decode( preg_replace('/[\n\r]+/', '\n', JRequest::getVar( 'elements', '', 'post', 'string' ) ) );
        
        // convert elements to array that can be binded
        Tags::load( 'TagsHelperBase', 'helpers._base' );
        $helper = TagsHelperBase::getInstance();
        $submitted_values = $helper->elementsToArray( $elements );
        
        $event_id = $submitted_values['cid'];
        
        JLoader::register('TagsHelperTags', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tags'.DS.'helpers'.DS.'tags.php');
        $tags_helper = new TagsHelperTags();
        
        if ( !empty($event_id) )
        {
        	$relationship_id = JRequest::getInt( 'relationship_id' );
        
        	// remove relationship
        	$tags_helper->deleteRelationship( $relationship_id );

        	$vars = new JObject();
        	$vars->tags = array();
       		$vars->id = $event_id;   
                
            // we're working on an existing content item            
            $vars->tags = $tags_helper->getTags( $event_id, $this->getRSEventsScopeId() );
            $layout = 'tags_list';
        }
            else
        {
            // this is the form for a new content article, which needs to be treated differently since it doesn't have an ID yet
        	if ( !empty($submitted_values['vars']) )
	      	{
	      		// prepare needed variables
        		$vars = $submitted_values['vars'];
        		$tag_index = JRequest::getVar( 'unsaved_tag_index' );
        		
        		// search and destroy
		      	if( is_array( $vars ) )
		      	{
			      	foreach( $vars as $key => $value ) {
						if ( $key == $tag_index )
						{
							unset( $vars[$key] );
						}
					}
					
					$vars = array_values( $vars );
		      	}
	      	}
        	        	
        	$layout = 'tags_list_newevent';
        }
        
        //$html = "inside content.removetag, would have removed the tag: '$relationship_id' then displayed a list of the article's tags";
        $html = $this->getLayout( $layout, $vars); 
        return $html;
    }
    
    /**
     * Triggered after saving RSEvent
     * 
     * @param object $event
     * @param bool $isNew
     * @return void
     */
    function rsevents_onAfterSaveEvent( $event, $isNew )
    {   
    	if( $isNew )
        {
        	if ( !class_exists('Tags') ) 
    			JLoader::register( "Tags", JPATH_ADMINISTRATOR.DS."components".DS."com_tags".DS."defines.php" );
        	
	        // prepare needed variables
	        $event_id = $event->IdEvent;
	        $scope_id = $this->getRSEventsScopeId();
	        $vars = JRequest::getVar( 'vars' );
	        
	        JLoader::register('TagsHelperTags', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tags'.DS.'helpers'.DS.'tags.php');
        	$tags_helper = new TagsHelperTags();
	       
	    	foreach( $vars as $tag_name ) {
	    	
			    $tag_id = $tags_helper->checkTagId( $tag_name );
			
			    // add new relationship
			    $tags_helper->addRelationship( $tag_id, $scope_id, $event_id );			    
	    	}
        }
    }
    
	/**
     * Triggered after deleting RSEvent
     * 
     * @param int event id
     * @return void
     */
    function rsevents_onAfterDeleteEvent( $event_id )
    {
    	$scope_id = $this->getRSEventsScopeId();
    	
    	JLoader::register('TagsHelperTags', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tags'.DS.'helpers'.DS.'tags.php');
        $helper = new TagsHelperTags();
        
        $helper->deleteAllRelationships( $event_id, $scope_id );	
    }
        
    /**
     * When events edit form is displayed
     * 
     * @param object $event
     * @return html
     */
    function rsevents_onAfterDisplayEvent( $event )
    {
    	$option = JRequest::getVar('option');
        if ($option == 'com_rsevents')
        {     
        	JLoader::register('TagsHelperTags', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tags'.DS.'helpers'.DS.'tags.php');
        	$helper = new TagsHelperTags();             
                       	
        	$vars = new JObject();
        	$vars->event = $event;
        	if( empty( $event->IdEvent ) )
        	{
        		$vars->tags = null;
        	}
        	else 
        	{
	        	$vars->tags  = $helper->getTags( $event->IdEvent, $this->getRSEventsScopeId() );
        	}
        	
	       	echo $this->getLayout('tags_form', $vars);
        }
    }
    
	/**
     * Check RSEvents scope
	 *  
     * @param void
     * @return int scope id
     */    
    function getRSEventsScopeId()
    {
    	JModel::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tags'.DS.'models' );
	    $model = JModel::getInstance( 'Scopes', 'TagsModel' );
		$model->setState( 'select', 'tbl.scope_id' );
		$model->setState( 'filter_name', 'RSEvent' );
		$model->setState( 'filter_identifier', 'com_rsevents&view=events' );
		$model->setState( 'filter_url', 'index.php?option=com_rsevents&view=events&layout=show&cid=' );
		$model->setState( 'filter_table', '#__rsevents_events' );
		$model->setState( 'filter_table_field', 'IdEvent' );
		$model->setState( 'filter_table_name_field', 'EventName' );
		$scope_id = $model->getResult();
		
		if (empty($scope_id)) { 
		
			$table = $model->getTable();
			$table->scope_name			   = 'RSEvent';
			$table->scope_identifier       = 'com_rsevents&view=events';
        	$table->scope_url              = 'index.php?option=com_rsevents&view=events&layout=show&cid=';
       		$table->scope_table            = '#__rsevents_events';
        	$table->scope_table_field      = 'IdEvent';
        	$table->scope_table_name_field = 'EventName';
		
			$table->save();
			
			$scope_id = $table->scope_id;
		}
        
        return $scope_id;
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
    function getLayout($layout, $vars = false, $plugin = '', $group = 'rsevents' )
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