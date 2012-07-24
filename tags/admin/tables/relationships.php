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
	
	function save()
	{
	    if ($return = parent::save())
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
		if ($return = parent::delete( $oid ))
	    {
	    	$tag = JTable::getInstance( 'Tags', 'TagsTable' );
	        $tag->load( array( 'tag_id'=>$this->tag_id ) );
	        if ( !empty($tag->tag_id) && !empty( $tag->uses ) )
	        {
	        	$tag->uses = $tag->uses - 1;
	        	$tag->store(); 
	        }
	    }
	    
	    return $return;
	}
	
	/**
	 * Given a named array with values, 
	 * will intelligently set the relationship object's properties
	 *  
	 * @param array $array
	 * @return boolean
	 */
	function setObject( $array )
	{
		// using these:
//                'value'=>'22',                                  // required. the object's unique identifier. (in the case of content article, is the article id #)
//                'name'=>'What\'s New in 1.5?',                  // required. the object's plain english name. 
//                'scope_identifier'=>'com_content&view=article', // required. is unique to this site+component+view(+layout) combo
//                'scope_name'=>'The Core Content Manager',       // optional. only necessary if this scope is a new one
//                'scope_url'=>'index.php?option=com_content&view=article&task=edit&cid[]=',  // optional. only necessary if this is a new scope, and this url is unique to this site+component+view(+layout) combo
//                'client_id'=>$client_id                            // optional. if missing, defaults to front-end (0). admin-side = '1';

        // check that client_id is set
        $valid_clients = array('0', '1');
        if (empty($array['client_id']) || !in_array( (int) $array['client_id'], $valid_clients))
        {
	        $app = JFactory::getApplication();
	        $array['client_id'] = $app->isAdmin() ? '1' : '0';
        }
		
        // set $this->object_id
        // by first getting the scope_id
            // create a new scope if necessary
            $array['scope_id'] = $this->findScope( $array );
            
        // then getting the related object_id
            // create the object if necessary
            $this->object_id = $this->findObject($array);
            
        // TODO make this method return false with error reporting    
        return true;
	}
	
    /**
     * Given a named array with values, 
     * will intelligently set the relationship object's properties
     *  
     * @param array $array
     * @return boolean
     */
    function setSubject( $array )
    {
        // using these:
//                'value'=>'62',      // required. the subject's unique identifier, generally a user id #
//                'name'=>'Admin',    // required. the subject's name, generally a user's name or username.
//                'type'=>'user'      // optional. 'user' is the default
    	
        // set $this->subject_id
        // by first getting the subjecttype_id
            // create a new subjecttype if necessary
            $array['subjecttype_id'] = $this->findSubjectType( $array );
            
        // then getting the related subject_id
            // create the subject if necessary
            $this->subject_id = $this->findSubject($array);
            
        // TODO make this method return false with error reporting    
        return true;
    }
    
    /**
     * Given a named array with values, 
     * will intelligently set the relationship object's properties
     *  
     * @param array $array
     * @return boolean
     */
    function setVerb( $array )
    {
        // using these:
//                'value'=>'modified',    // required. unique identifier for this action
//                'name'=>'Modified'      // optional. if this is a new verb, this is the plain English name for it
        
        // set $this->verb_id            
            // create the verb if necessary
            $this->verb_id = $this->findVerb($array);
            
        // TODO make this method return false with error reporting    
        return true;
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
