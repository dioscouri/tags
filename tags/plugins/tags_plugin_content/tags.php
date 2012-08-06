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

class plgContentTags extends DSCPlugin
{
    var $_element = 'tags';
    
    public function __construct( &$subject, $params )
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
    protected function _isMe( $row ) 
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
     * @param unknown_type $article
     * @param unknown_type $isNew
     * @return boolean
     */
    public function onAfterContentSave( &$article, $isNew )
    {
        $context = 'com_content.form';
        return $this->doContentSave($context, $article, $isNew);            
    }
    
    /**
     * 
     * @param unknown_type $context
     * @param unknown_type $article
     * @param unknown_type $isNew
     * @return boolean
     */
    public function onContentAfterSave($context, &$article, $isNew)
    {
        return $this->doContentSave($context, $article, $isNew);
    }
    
    /**
     * 
     * @param unknown_type $context
     * @param unknown_type $article
     * @param unknown_type $isNew
     * @return boolean
     */
    private function doContentSave($context, &$article, $isNew)
    {
        // this plugin event is triggered after an article is saved.  
        // You will need to use it to save the tags that are added to NEW content articles
 
    	if( $isNew )
        {
        	if ( !class_exists('Tags') ) 
    			JLoader::register( "Tags", JPATH_ADMINISTRATOR.DS."components".DS."com_tags".DS."defines.php" );
        	
	        // prepare needed variables
	        $article_id = $article->id;
	        $vars = JRequest::getVar( 'unsaved_tags' );
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
			    $relations_table->scope_id = $content_helper->getArticleScopeId();
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
     * 
     * @param unknown_type $article
     * @param unknown_type $isNew
     * @return boolean
     */
    public function onAfterContentDelete( $article )
    {
        $context = 'com_content.form';
        return $this->doContentSave($context, $article);            
    }
    
    /**
     * 
     * @param unknown_type $context
     * @param unknown_type $article
     * @param unknown_type $isNew
     * @return boolean
     */
    public function onContentAfterDelete($context, $article)
    {
        return $this->doContentSave($context, $article);
    }
    
    /**
     * 
     * @param unknown_type $context
     * @param unknown_type $article
     * @param unknown_type $isNew
     * @return boolean
     */
    private function doContentDelete($context, $article)
    {
    	JLoader::register('TagsHelperContent', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tags'.DS.'helpers'.DS.'content.php');
	    $content_helper = new TagsHelperContent(); 
    	$scope_id = $content_helper->getArticleScopeId();
    	
    	JLoader::register('TagsHelperTags', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tags'.DS.'helpers'.DS.'tags.php');
        $helper = new TagsHelperTags();
        
        $helper->deleteAllRelationships( $article->id, $scope_id );	   
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
    public function onPrepareContent( &$article, &$params, $limitstart )
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
    public function onAfterDisplayTitle( &$article, &$params, $limitstart )
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
    public function onBeforeDisplayContent( &$article, &$params, $limitstart )
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
    public function onAfterDisplayContent( &$article, &$params, $limitstart )
    {
        return '';
    }
}
?>