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
KLoader::load('site::plg.koowa.default');
jimport('joomla.application.component.model');

class plgKoowaTags extends plgKoowaDefault
{
    var $_element = 'tags';
            
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
     * When events edit form is displayed
     * 
     * @param KConfig $args passed arguments
     * @return html
     */
    function ninjaboard_onDisplayPost()
    {
    	$option = KRequest::get('get.option', 'string');
    	$view   = KRequest::get('get.view', 'string');
        if ($option == 'com_ninjaboard')        	
        {
        	JLoader::register('TagsHelperTags', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tags'.DS.'helpers'.DS.'tags.php');
        	$helper = new TagsHelperTags();             
                       	
        	$vars = new JObject();
        	
        	if( $view=='post' )
        		$post_id = KRequest::get('get.id', 'string');
        	
        	if( empty( $post_id ) )
        	{
        		$vars->tags = null;
        	}
        	else 
        	{
	        	$vars->tags  = $helper->getTags( $post_id, $this->getNinjaboardScopeId() );	        	
        	}
        	
	       	echo $this->getLayout('tags_form', $vars);
        }
    }
    
	/**
     * Adds a new tag to an post
     * 
     * @return unknown_type
     */
    function addTag( KConfig $args )
    {
    	if ( !$this->_isMe( $args['element'] ) )
        {
            return '';
        }
        
        // prepare needed variables     
        $view   = KRequest::get('get.view', 'string');         
        if( $view=='post' )
        {
        	$post_id = KRequest::get('get.id', 'string');        
        }
        else 
        {
        	$post_id = null;
        }
        $tag_name = KRequest::get('get.tag_name', 'string');
        $scope_id = $this->getNinjaboardScopeId();
        
        JLoader::register('TagsHelperTags', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tags'.DS.'helpers'.DS.'tags.php');
        $tags_helper = new TagsHelperTags();
     
        if (!empty($post_id))
        {       	  
	        $added_tags = $tags_helper->getTags( $post_id, $scope_id ); 
	        
	        // check if tag is already added to the article
	        foreach (@$added_tags as $tag)
	        {
	        	if( @$tag->tag_name == $tag_name )
	        	{	  
	        		$vars = new JObject();             
			           
			        $vars->tags = array();        
			        $vars->tags = $added_tags;
			            
			       	$html = $this->getLayout('tags_list', $vars); 
				        
				    return $html;
	        	}
	        }
	        	
	        if( !empty( $tag_name ) )
		    {
			    $tag_id = $tags_helper->checkTagId( $tag_name );
			
			    // add new relationship
			    $tags_helper->addRelationship( $tag_id, $scope_id, $post_id );			    
		    }
		
		    $vars = new JObject();
		    $vars->tags = array();
	    	                
            $vars->tags = $tags_helper->getTags( $post_id, $scope_id ); 
            $layout = 'tags_list';     	
        }
        	else 
        {  	      	
        	if ( !empty($_SESSION['tags']) )
	      	{
        		$vars = $_SESSION['tags'];
        		
		      	if( !in_array ( $tag_name , $vars ) && !empty( $tag_name ) )
		      	{
	        		$vars[] = $tag_name;
	        		sort( $vars );
	        		$_SESSION['tags'] = $vars;
		      	}
	      	}
        		else
        	{
        		if( !empty( $tag_name ) )
		      	{
		      		$vars[] = $tag_name;
		      		$_SESSION['tags'] = $vars;	
		      	}        			
        	}
	      	       	        	
        	$layout = 'tags_list_newpost'; 
        	     	
        }

        $html = $this->getLayout($layout, $vars);
		return $html;
    }
    
	/**
     * Removes tags from the post
     * 
     * @return unknown_type
     */
    function removeTag( $element  )
    {
        if (!$this->_isMe( $element) )
        {
            return '';
        }
        
    	// prepare needed variables     
        $view   = KRequest::get('get.view', 'string');         
        if( $view=='post' )
        {
        	$post_id = KRequest::get('get.id', 'string');        
        }
        else 
        {
        	$post_id = null;
        }
        
        JLoader::register('TagsHelperTags', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tags'.DS.'helpers'.DS.'tags.php');
        $tags_helper = new TagsHelperTags();
        
        if ( !empty($post_id) )
        {
        	$relationship_id = KRequest::get('get.relationship_id', 'string');
        
        	// remove relationship
        	$tags_helper->deleteRelationship( $relationship_id );

        	$vars = new JObject();
        	$vars->tags = array(); 
                
            // we're working on an existing content item            
            $vars->tags = $tags_helper->getTags( $post_id, $this->getNinjaboardScopeId() );
            $layout = 'tags_list';
        }
            else
        {
            // this is the form for a new content article, which needs to be treated differently since it doesn't have an ID yet
        	if ( !empty($_SESSION['tags']) )
	      	{
	      		// prepare needed variables
        		$vars = $_SESSION['tags'];
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
					$_SESSION['tags'] = $vars;
		      	}
	      	}
        	        	
        	$layout = 'tags_list_newpost';
        }
        
        //$html = "inside content.removetag, would have removed the tag: '$relationship_id' then displayed a list of the article's tags";
        $html = $this->getLayout( $layout, $vars); 
        return $html;
    }
    
    /**
     * When post is saved
     * 
     * @param KConfig $args passed arguments
     */
    function ninjaboard_onSavePost(KConfig $args)  
    {			
		if ( !class_exists('Tags') ) 
    		JLoader::register( "Tags", JPATH_ADMINISTRATOR.DS."components".DS."com_tags".DS."defines.php" );
        	
	    // prepare needed variables
	    $post_id = $args['post_id'];
	    $scope_id = $this->getNinjaboardScopeId();
	    $vars = $_SESSION['tags'];
	        
	    JLoader::register('TagsHelperTags', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tags'.DS.'helpers'.DS.'tags.php');
        $tags_helper = new TagsHelperTags();
	       
	    foreach( $vars as $tag_name ) {
	    	$tag_id = $tags_helper->checkTagId( $tag_name );
			
			// add new relationship
			$tags_helper->addRelationship( $tag_id, $scope_id, $post_id );			    
	    }
    	
	    $_SESSION['tags'] = array();
    }
    
    /**
     * When post is saved
     * 
     * @param KConfig $args passed arguments
     */
    function ninjaboard_onDeletePost(KConfig $args)  
    {
    	$scope_id = $this->getNinjaboardScopeId();
    	
    	JLoader::register('TagsHelperTags', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tags'.DS.'helpers'.DS.'tags.php');
        $helper = new TagsHelperTags();
        
        $helper->deleteAllRelationships( $args['post_id'], $scope_id );
    }
    
	/**
     * When topic is deleted
     * 
     * @param KConfig $args passed arguments
     * @return void
     */
    function ninjaboard_onDeleteTopic(KConfig $args)  
    {
    	$topic_id = $args['topic_id'];
                 
        if( !empty($topic_id) )
        {
	    	$posts = $this->getTopicPosts( $topic_id );
	    	$scope_id = $this->getNinjaboardScopeId();
	    	JLoader::register('TagsHelperTags', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tags'.DS.'helpers'.DS.'tags.php');
	        $helper = new TagsHelperTags();
	    	
	    	foreach($posts as $post_id)
	    	{           
	        	$helper->deleteAllRelationships( $post_id, $scope_id );
	    	}	    	
        }
    }   
    
    /**
     * When forum is deleted
     * 
     * @param KConfig $args passed arguments
     * @return void
     */
    function ninjaboard_onDeleteForum(KConfig $args)  
    {
    	$forum_id = $args['forum_id'];
                 
        if( !empty($forum_id) )
        {
	    	$topics = $this->getForumTopics( $forum_id );
	    	
	    	$scope_id = $this->getNinjaboardScopeId();
	    	JLoader::register('TagsHelperTags', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tags'.DS.'helpers'.DS.'tags.php');
	        $helper = new TagsHelperTags();
	    	
	    	foreach($topics as $topic_id)
	    	{  
	    		$posts = $this->getTopicPosts( $topic_id );

	    		foreach($posts as $post_id)
		    	{
		        	$helper->deleteAllRelationships( $post_id, $scope_id );
		    	}
	    	}	    	
        }
    }
    
	/**
     * Get post ids for the given topic id
     * 
     * @param int topic id
     * @return int post ids
     */
    function getTopicPosts($topic_id)
    {
    	$db = &JFactory::getDBO();
    	$query = "SELECT ninjaboard_post_id FROM #__ninjaboard_posts
    			  WHERE  ninjaboard_topic_id = " . $topic_id;
    	$db->setQuery( $query );
	    $posts = $db->loadResultArray();
    	
    	return $posts;
    }
    
	/**
     * Get topic ids for the given forum id
     * 
     * @param int topic id
     * @return int post ids
     */
    function getForumTopics($forum_id)
    {
    	$db = &JFactory::getDBO();
    	$query = "SELECT ninjaboard_topic_id FROM #__ninjaboard_topics
    			  WHERE  forum_id = " . $forum_id;
    	$db->setQuery( $query );
	    $topics = $db->loadResultArray();
    	
    	return $topics;
    }
    
	/**
     * Check Ninjaboard scope
	 *  
     * @param void
     * @return int scope id
     */    
    function getNinjaboardScopeId()
    {
    	JModel::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tags'.DS.'models' );
	    $model = JModel::getInstance( 'Scopes', 'TagsModel' );
		$model->setState( 'select', 'tbl.scope_id' );
		$model->setState( 'filter_name', 'Ninjaboard' );
		$model->setState( 'filter_identifier', 'com_ninjaboard&view=post' );
		$model->setState( 'filter_url', 'index.php?option=com_ninjaboard&view=post&id=' );
		$model->setState( 'filter_table', '#__ninjaboard_posts' );
		$model->setState( 'filter_table_field', 'ninjaboard_post_id' );
		$model->setState( 'filter_table_name_field', 'subject' );
		$scope_id = $model->getResult();
		
		if (empty($scope_id)) { 
		
			$table = $model->getTable();
			$table->scope_name			   = 'Ninjaboard';
			$table->scope_identifier       = 'com_ninjaboard&view=post';
        	$table->scope_url              = 'index.php?option=com_ninjaboard&view=post&id=';
       		$table->scope_table            = '#__ninjaboard_posts';
        	$table->scope_table_field      = 'ninjaboard_post_id';
        	$table->scope_table_name_field = 'subject';
		
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
    function getLayout($layout, $vars = false, $plugin = '', $group = 'koowa' )
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