<?php
/**
 * @package	Tags
 * @author 	Dioscouri Design
 * @link 	http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );


class TagsTableRelationships extends DSCTable 
{
	function TagsTableRelationships( &$db ) 
	{
		
		$tbl_key 	= 'relationship_id';
		$tbl_suffix = 'relationships';
		$this->set( '_suffix', $tbl_suffix );
		$name 		= 'tags';
		
		parent::__construct( "#__{$name}_{$tbl_suffix}", $tbl_key, $db );	
	}
	
	/**
	 * Checks the object's integrity before storing to the DB
	 * 
	 * @return unknown_type
	 */
	function check()
	{
	    $db         = $this->getDBO();
        $nullDate   = $db->getNullDate();
        if (empty($this->created_datetime) || $this->created_datetime == $nullDate)
        {
            $date = JFactory::getDate();
            $this->created_datetime = $date->toMysql();
        }
        
	    if (empty($this->created_by))
        {
            $this->created_by = JFactory::getUser()->id;
        }
        
		if (empty($this->tag_id))
		{
			$this->setError( JText::_( "Tag Required" ) );
			return false;
		}
		
	    if (empty($this->scope_id))
        {
            $this->setError( JText::_( "Scope Required" ) );
            return false;
        }

        if (empty($this->item_value))
        {
            $this->setError( JText::_( "Item Required" ) );
            return false;
        }
		return true;
	}
	
	function save($src='', $orderingFilter = '', $ignore = '')
	{
	    if ($return = parent::save($src, $orderingFilter, $ignore ))
	    {
	        if (!empty($this->_isNew))
	        {
	            $tag = JTable::getInstance( 'Tags', 'TagsTable' );
	            $tag->load( array( 'tag_id'=>$this->tag_id ) );
	            if (!empty($tag->tag_id))
	            {
	                $tag->uses = $tag->uses + 1;
	                $tag->store(); 
	            }
	        }
	    }
	    
	    return $return;
	}
	
	/**
	 * Automatically decreases the "uses" count,
	 * for a tag when a relationship is deleted
	 * 
	 * @param void
	 * @return unknown_type
	 */
	function delete( $oid = null )
	{
	    if (!empty($oid)) {
	        $rel = JTable::getInstance( 'Relationships', 'TagsTable' );
	        $rel->load( $oid );
	        $tag_id = $rel->tag_id; 
	    } else {
	        $tag_id = $this->tag_id;
	    }
	    
		if ($return = parent::delete( $oid ))
	    {
	    	$tag = JTable::getInstance( 'Tags', 'TagsTable' );
	        $tag->load( array( 'tag_id'=>$tag_id ) );
	        if ( !empty($tag->tag_id) && !empty( $tag->uses ) )
	        {
	        	$tag->uses = $tag->uses - 1;
	        	$tag->store(); 
	        }
	    }
	    
	    return $return;
	}
	
	/**
	 * Loads a scope object
	 * creating a new one if necessary
	 * 
	 * @param unknown_type $array
	 * @return unknown_type
	 */
	function findScope( $array )
	{
        // get a tags scopes object
        JTable::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tags'.DS.'tables' );
        $scope = JTable::getInstance( 'Scopes', 'TagsTable' );
        $scope->load( array('client_id'=>$array['client_id'], 'scope_identifier'=>$array['scope_identifier']) );
        if (empty($scope->scope_id))
        {
        	$scope->bind($array);
            if (!$scope->save())
            {
                JError::raiseNotice( 'TagsTableRelationships01', "TagsTableRelationships :: ". $scope->getError() );
            }
        }
        return $scope->scope_id;
	}
	
    /**
     * Loads a tags object
     * creating a new one if necessary
     * 
     * @param unknown_type $array
     * @return unknown_type
     */
    function findTag( $array )
    {
        // get a tags objects object
        JTable::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tags'.DS.'tables' );
        $object = JTable::getInstance( 'Tags', 'TagsTable' );
        $object->load( array('tag_name'=>$array['tag_name']) );
        if (empty($object->tag_id))
        {
            $object->tag_name    = $array['tag_name'];
            if (!$object->save())
            {
            	JError::raiseNotice( 'TagsTableRelationships01', "TagsTableRelationships :: ". $object->getError() );
            }
        }
        return $object->object_id;
    }
}
