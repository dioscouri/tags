<?php
/**
 * @package Extra Fields
 * @author  Dioscouri Design JTable
 * @link    http://www.dioscouri.com
 * @copyright Copyright (C) 2009 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

/** Import library dependencies */
jimport('joomla.plugin.plugin');

class plgContentTags extends JPlugin
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
     * Adds a new tag to an article
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
        $article_id = $submitted_values['id'];
        $tag_name = trim( $submitted_values['tag_name'] );
        $user = JFactory::getUser(); 
        
        if (!empty($article_id))
        {
        	JLoader::register('TagsHelperContent', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tags'.DS.'helpers'.DS.'content.php');
        	$content_helper = new TagsHelperContent(); 
	        $added_tags = $content_helper->getTagsForArticle( $article_id ); 
	        
	        // check if tag is already added to the article
	        foreach (@$added_tags as $tag)
	        {
	        	if( @$tag->tag_name == $tag_name )
	        	{	  
	        		$vars = new JObject();             
			           
			        $vars->tags = array();
			        $vars->id = $article_id;        
			        $vars->tags = $content_helper->getTagsForArticle( $article_id );
			            
			       	$html = $this->getLayout('tags_list', $vars); 
				        
				    return $html;
	        	}
	        }
	        	
	        if( !empty( $tag_name ) )
		    {
			    // get tag_id by tag_name if it doesn't exisists then create new one
			    JModel::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tags'.DS.'models' );
			    $model = JModel::getInstance('Tags', 'TagsModel');
			    $model->setState( 'select', 'tbl.tag_id');
			    $model->setState( 'filter_name_exact', $tag_name );
			    $tag_id = $model->getResult();
			
			    // add new tag if the tag doesn't exsist
			    if( empty($tag_id) )
			    {
			       	$tags_table = $model->getTable();
			       	$tags_table->tag_name = $tag_name;
			       	$tags_table->created_by = $user->id;
			       	$tags_table->save();
			        	
			       	$tag_id = $tags_table->tag_id;
			    }
			
			    // add new relationship
			    JTable::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tags'.DS.'tables' );
			    $relations_table = JTable::getInstance('Relationships', 'TagsTable');
			    $relations_table->tag_id = $tag_id;
			    $relations_table->scope_id = $content_helper->getContentScopeId();
			    $relations_table->item_value = $article_id;
			    $relations_table->created_by = $user->id;
			    $relations_table->save();
		    }
		
		    $vars = new JObject();
		    $vars->tags = array();
		    $vars->id = $article_id;
	    	                
            $vars->tags = $content_helper->getTagsForArticle( $article_id );
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
	      	        	        	
        	$layout = 'tags_form';       	
        }

        $html = $this->getLayout($layout, $vars);
		return $html;
    }
    
    /**
     * 
     * Enter description here ...
     * @return unknown_type
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
        
        $article_id = $submitted_values['id'];
        
        if ( !empty($article_id) )
        {
        	$relationship_id = JRequest::getInt( 'relationship_id' );
        
        	// remove relationship
        	JTable::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tags'.DS.'tables' );
	    	$relations_table = JTable::getInstance('Relationships', 'TagsTable');
	    	$relations_table->load( $relationship_id );
	    	$relations_table->delete();

        	$vars = new JObject();
        	$vars->tags = array();
       		$vars->id = $article_id;   
                
            // we're working on an existing content item     
            JLoader::register('TagsHelperContent', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tags'.DS.'helpers'.DS.'content.php');
        	$content_helper = new TagsHelperContent();              
            $vars->tags = $content_helper->getTagsForArticle( $article_id );
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
        	        	
        	$layout = 'tags_form';
        }
        
        //$html = "inside content.removetag, would have removed the tag: '$relationship_id' then displayed a list of the article's tags";
        $html = $this->getLayout( $layout, $vars); 
        return $html;
    }
        
    /**
     * Example after save content method
     * Article is passed by reference, but after the save, so no changes will be saved.
     * Method is called right after the content is saved
     *
     *
     * @param   object      A JTableContent object
     * @param   bool        If the content is just about to be created
     * @return  void
     */
    function onAfterContentSave( &$article, $isNew )
    {
        // this plugin event is triggered after an article is saved.  
        // You will need to use it to save the tags that are added to NEW content articles
 
    	if( $isNew )
        {
        	if ( !class_exists('Tags') ) 
    			JLoader::register( "Tags", JPATH_ADMINISTRATOR.DS."components".DS."com_tags".DS."defines.php" );
        	
	        // prepare needed variables
	        $article_id = $article->id;
	        $vars = JRequest::getVar( 'vars' );
	        $user = JFactory::getUser(); 
	       
	    	foreach( $vars as $tag_name ) {
	    		
				// get tag_id by tag_name if it doesn't exisists then create new one
			    JModel::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tags'.DS.'models' );
			    $model = JModel::getInstance('Tags', 'TagsModel');
			    $model->setState( 'select', 'tbl.tag_id');
			    $model->setState( 'filter_name_exact', $tag_name );
			    $tag_id = $model->getResult();
			
			    // add new tag if the tag doesn't exsist
			    if( empty($tag_id) )
			    {
			       	$tags_table = $model->getTable();
			       	$tags_table->tag_name = $tag_name;
			       	$tags_table->created_by = $user->id;
			       	
			       	if( $tags_table->save() )
			       	{
			          	$tag_id = $tags_table->tag_id;
			       	}
			       		else 
			       	{
			       		$app = JFactory::getApplication();
						$app->enqueueMessage( JText::_( 'ERROR SAVING NEW TAG NAME' ).$tags_table->getError(), 'notice' );
						
						return false;
			       	}
			    }
			    
			    JLoader::register('TagsHelperContent', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tags'.DS.'helpers'.DS.'content.php');
	        	$content_helper = new TagsHelperContent(); 
			
			    // add new relationship
			    JTable::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tags'.DS.'tables' );
			    $relations_table = JTable::getInstance('Relationships', 'TagsTable');
			    $relations_table->tag_id = $tag_id;
			    $relations_table->scope_id = $content_helper->getContentScopeId();
			    $relations_table->item_value = $article_id;
			    $relations_table->created_by = $user->id;
			    
			    if( !$relations_table->save() )
			    {
			    	$app = JFactory::getApplication();
					$app->enqueueMessage( JText::_( 'ERROR SAVING RELATIONSHIP' ).$relations_table->getError(), 'notice' );
						
					return false;	
			    }
			    
	    	}
        }
        
        return true;
    }
    
	/**
     * Delete relationships on deletion of article     
     *
     * @param   int 		the article id
     */
    function content_onAfterDeleteArticle( $article_id )
    {
    	JLoader::register('TagsHelperContent', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tags'.DS.'helpers'.DS.'content.php');
	    $content_helper = new TagsHelperContent(); 
    	$scope_id = $content_helper->getContentScopeId();
    	
    	JLoader::register('TagsHelperTags', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tags'.DS.'helpers'.DS.'tags.php');
        $helper = new TagsHelperTags();
        
        $helper->deleteAllRelationships( $item_id, $scope_id );	   
    }
    
    /**
     * Example prepare content method
     *
     * Method is called by the view
     *
     * @param   object      The article object.  Note $article->text is also available
     * @param   object      The article params
     * @param   int         The 'page' number
     */
    function onPrepareContent( &$article, &$params, $limitstart )
    {
    	   
    }

    /**
     * Example after display title method
     *
     * Method is called by the view and the results are imploded and displayed in a placeholder
     *
     * @param   object      The article object.  Note $article->text is also available
     * @param   object      The article params
     * @param   int         The 'page' number
     * @return  string
     */
    function onAfterDisplayTitle( &$article, &$params, $limitstart )
    {
        return '';
    }

    /**
     * Example before display content method
     *
     * Method is called by the view and the results are imploded and displayed in a placeholder
     *
     * @param   object      The article object.  Note $article->text is also available
     * @param   object      The article params
     * @param   int         The 'page' number
     * @return  string
     */
    function onBeforeDisplayContent( &$article, &$params, $limitstart )
    {
        return '';
    }

    /**
     * Example after display content method
     *
     * Method is called by the view and the results are imploded and displayed in a placeholder
     *
     * @param   object      The article object.  Note $article->text is also available
     * @param   object      The article params
     * @param   int         The 'page' number
     * @return  string
     */
    function onAfterDisplayContent( &$article, &$params, $limitstart )
    {
        return '';
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
    function getLayout($layout, $vars = false, $plugin = '', $group = 'content' )
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