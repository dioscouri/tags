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

if ( !class_exists('Tags') ) 
    JLoader::register( "Tags", JPATH_ADMINISTRATOR.DS."components".DS."com_tags".DS."defines.php" );

class TagsHelperTags extends DSCHelperDiagnostics
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
}