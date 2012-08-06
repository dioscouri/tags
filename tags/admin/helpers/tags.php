<?php
/**
 * @version 1.5
 * @package Tags
 * @author  Dioscouri Design
 * @link    http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');
if ( !class_exists('Tags') ) {
    JLoader::register( "Tags", JPATH_ADMINISTRATOR.DS."components".DS."com_tags".DS."defines.php" );
}

class TagsHelperTags extends JObject
{
	/**
	 * Get tags for the tagged item
	 * 
	 * @param int scope id
	 * @param int item id
	 * @return object tag
	 */
	function getTags( $item_value, $scope_id )
	{
		JModel::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tags'.DS.'models' );
    	$model = JModel::getInstance( 'Relationships', 'TagsModel' );
		$model->setState( 'filter_scopeid', $scope_id );
		$model->setState( 'filter_item_exact', $item_value );
		$model->setState( 'order','tag.tag_name' );
		$tags = $model->getList();     
        
        return $tags;
	}
	
	/**
	 * Checks if tag with given name exists,
	 * if not it creates one
	 * 
	 * @param string $tag_name
	 * @return int $tag_id
	 */
	function checkTagId( $tag_name )
	{
		$tag_id = '';
		
		JModel::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tags'.DS.'models' );
	    $model = JModel::getInstance( 'Tags', 'TagsModel' );
	    $model->setState( 'select', 'tbl.tag_id' );
		$model->setState( 'filter_name_exact', $tag_name );
		$tag_id = $model->getResult();
		
		if ( empty($tag_id) ) { 
		
			$table = $model->getTable();
			$table->tag_name = $tag_name;
			$table->created_by = JFactory::getUser()->id;
			$table->save();
			        	
			$tag_id = $table->tag_id;
		}
        
        return $tag_id;
	}
	
	/**
	 * Get tag id
	 * 
	 * @param string $tag_name
	 * @return int $tag_id
	 */
	function getTagId( $tag_name )
	{	
		JModel::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tags'.DS.'models' );
	    $model = JModel::getInstance( 'Tags', 'TagsModel' );
	    $model->setState( 'select', 'tbl.tag_id' );
		$model->setState( 'filter_name_exact', $tag_name );
		$tag_id = $model->getResult();
		        
        return $tag_id;
	}
	
	/**
	 * Purge relationships on given tag names array.
	 * Check relationships against tag names, if 
	 * relationships contain tag ids for tag names 
	 * which doesn't exists in array of tag names 
	 * than delete that relationship.
	 * 
	 * @param array $tag_name
	 * @param int $item_id
	 */
	function purgeRelationships( $tag_names, $item_id, $scope_id )
	{
		JModel::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tags'.DS.'models' );
	    $model = JModel::getInstance( 'Relationships', 'TagsModel' );
	    $model->setState( 'filter_item_exact', $item_id );
	    $model->setState( 'filter_scopeid', $scope_id );
	    $relationships = $model->getList();
	    
	    $tag_ids = array();
	    
	    foreach( $tag_names as $tag )
	    {
	    	$tag_id = $this->getTagId( $tag );
	    	if( !empty( $tag_id ) )
	    	{
	    		$tag_ids[] = $tag_id;
	    	}	
	    }
	    
	    foreach ( $relationships as $relationship )
	    {
	    	$flag = false;
	    	
	    	foreach ( $tag_ids as $tag_id )
	    	{
	    		if( $relationship->tag_id == $tag_id )
	    		{
	    			$flag = true;
	    		}	    		
	    	}
	    	
	    	if( !$flag )
	    	{
	    		JTable::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tags'.DS.'tables' );
	    		$table = JTable::getInstance( 'Relationships', 'TagsTable' );
	    		$table->load( $relationship->relationship_id );
	    		$table->delete();
	    	}
	    }
	}
	
	/**
	 * Delete all realtionships for a given $item_value
	 * 
	 * @param int $item_value
	 * @return void
	 */
	function deleteAllRelationships( $item_value, $scope_id )
	{
		JModel::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tags'.DS.'models' );
	    $model = JModel::getInstance( 'Relationships', 'TagsModel' );
	    $model->setState( 'select', 'tbl.relationship_id' );
	    $model->setState( 'filter_item_exact', $item_value );
	    $model->setState( 'filter_scopeid', $scope_id );
	    $relationships = $model->getList();
	    
	    if(!empty($relationships))
	    {
		    foreach ( $relationships as $relationship ) {
		    	JTable::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tags'.DS.'tables' );
		    	$table = JTable::getInstance( 'Relationships', 'TagsTable' );
		    	$table->load( $relationship->relationship_id );
		    	$table->delete();
		    }
	    }	    
	}
	
	/**
	 * Deletes a relationship
	 * 
	 * @param int $relationship_id
	 * @return void
	 */
	function deleteRelationship( $relationship_id )
	{
		// remove relationship
       	JTable::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tags'.DS.'tables' );
	   	$relations_table = JTable::getInstance('Relationships', 'TagsTable');
	   	$relations_table->load( $relationship_id );
	   	$relations_table->delete();
	}
	
	/**
	 * Adds relationship for given parameters
	 * 
	 * @param int $tag_id
	 * @param int $scope_id
	 * @param int $item_value
	 * @return int $relationship_id
	 */
	function addRelationship( $tag_id, $scope_id, $item_value )
	{
		 JModel::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tags'.DS.'models' );
	     $model = JModel::getInstance( 'Relationships', 'TagsModel' );
	     $model->setState( 'select', 'tbl.relationship_id' );
	     $model->setState( 'filter_tagid', $tag_id );
	     $model->setState( 'filter_scopeid', $scope_id );
	     $model->setState( 'filter_item_exact', $item_value );
	     
	     $relationship_id = $model->getResult();
		
		if ( empty($relationship_id) ) { 
		
			$table = $model->getTable();
			$table->tag_id = $tag_id;
		 	$table->scope_id = $scope_id;
		 	$table->item_value = $item_value;
		 	$table->created_by = JFactory::getUser()->id;
		 	$table->save();
			        	
			$relationship_id = $table->relationship_id;
		}
        
        return $relationship_id;		 
	}
	
	/**
	 * Loads the standard form for adding tags to an item
	 * 
	 * @param unknown_type $identifier
	 * @param unknown_type $scope
	 */
	public function getForm( $identifier, $scope )
	{
	    $html = '';
	    
	    $app = JFactory::getApplication();
	    JLoader::register( "TagsViewTags", JPATH_ADMINISTRATOR."/components/com_tags/views/tags/view.html.php" );

	    $config = array();
	    $config['base_path'] = JPATH_ADMINISTRATOR . "/components/com_tags";
	    
	    $view = new TagsViewTags( $config );
	    $model = Tags::getClass("TagsModelRelationships", "models.relationships");
	    if (empty($identifier)) 
	    {
	        $items = array();
	    } 
    	    else 
	    {
	        $model->setState('filter_item_exact', $identifier);
	        $model->setState('filter_scope_identifier', $scope);
	        $items = $model->getList();	        
	    }
	    
	    $view->set( '_controller', 'tags' );
	    $view->set( '_view', 'tags' );
	    $view->set( '_doTask', true);
	    $view->set( 'hidemenu', false);
	    $view->setModel( $model, true );
	    $view->assign( 'tags', $items );
	    $view->assign( 'identifier', $identifier );
	    $view->assign( 'scope', $scope );
	    
	    $view->setLayout( 'tags_form' );
	    
        $html = $view->loadTemplate();
        
        return $html;
	}
	
	/**
	 * Gets scope id
	 *
	 * @param void
	 * @return unknown_type
	 */
	public function getScopeId( $scope )
	{
        JTable::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_tags/tables' );
        
        $table = JTable::getInstance( 'Scopes', 'TagsTable' );
        $table->load( array( 'scope_identifier'=>$scope ) );
	        	
        $scope_id = $table->scope_id;
	
	    return $scope_id;
	}
	
	/**
	 * Adds an array of tags (string names) to an item (as defined by identifier and scope) 
	 * 
	 * @param unknown_type $identifier
	 * @param unknown_type $scope
	 * @param array $tags
	 */
	public function addRelationships( $identifier, $scope, $tags=array() )
	{
	    JTable::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tags'.DS.'tables' );
	    JModel::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tags'.DS.'models' );

	    $rmodel = JModel::getInstance('Relationships', 'TagsModel');
	    $rmodel->setState( 'filter_scope_identifier', $scope );
	    $rmodel->setState( 'filter_item_exact', $identifier );
	    $current_tags = $rmodel->getList();
	    
	    foreach ($tags as $tag) 
	    {
	        $relationship_already_exists = false;
	        foreach (@$current_tags as $tag)
	        {
	            if( @$tag->tag_name == $tag_name )
	            {
	                $relationship_already_exists = true;
	            }
	        }
	        
	        if (!$relationship_already_exists)
	        {
	            // get tag_id by tag_name if it doesn't exisists then create new one
	            $model = JModel::getInstance('Tags', 'TagsModel');
	            $model->setState( 'select', 'tbl.tag_id');
	            $model->setState( 'filter_name_exact', $tag );
	            $tag_id = $model->getResult();
	            
	            // add new tag if the tag doesn't exsist
	            if( empty($tag_id) )
	            {
	                $tags_table = $model->getTable();
	                $tags_table->tag_name = $tag;
	                $tags_table->save();
	                $tag_id = $tags_table->tag_id;
	            }
	            
	            // add new relationship
	            $relations_table = JTable::getInstance('Relationships', 'TagsTable');
	            $relations_table->tag_id = $tag_id;
	            $relations_table->scope_id = $this->getScopeId( $scope );
	            $relations_table->item_value = $identifier;
	            $relations_table->save();	            
	        }

	    }
	}
}