<?php
/**
 * @version 1.5
 * @package Tags
 * @author  Dioscouri Design
 * @link    http://www.dioscouri.com
 * @copyright Copyright (C) 2008 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

class plgSystemTagsComContent extends JObject 
{
	var $_loadItem = false;
	
    function plgSystemTagsComContent()
    {
        parent::__construct();
    }
    // TODO ADJUST LINES BELOW TO THE TAGS
    /**
     * Function creates a logs entry for current page
     * 
     * @return null
     */
    function createLogEntry()
    {
        // get the verb if possible 
        if (!$verb = $this->getVerb())
        {
            // don't do anything
            return false;
        }
        
        // get the object if possible 
        if (!$object = $this->getObjects())
        {
            // don't do anything
            return false;
        }
        
        $subject = array(
		                'value'=>JFactory::getUser()->id,   // required. the subject's unique identifier, generally a user id # 
		                'name'=>JFactory::getUser()->name,  // required. the subject's name, generally a user's name or username.
		                'type'=>'user'                      // optional. 'user' is the default
		            );
        
        
        //check if have multiple objects
        if(count($object)>1)
        {
        	foreach ($object as $value) { 
		        // get a scout logs object
		        JTable::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_scout'.DS.'tables' );
		        $log = JTable::getInstance( 'Logs', 'ScoutTable' );	       	        
	               	
		        // set the subject
		        $log->setSubject($subject); 
		
		        // set the verb
		        $log->setVerb( $verb );
		        
		        // set the object
		        $log->setObject( $value );
		        
		        if (!$log->save())
		        {
		        	JError::raiseNotice( 'plgScoutComContent01', "plgScoutComContent :: ". $log->getError() );
		        }
	        }
        }
        else 
        {
        	$object = $this->getObject();
        	
	        // get a scout logs object
	        JTable::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_scout'.DS.'tables' );
	        $log = JTable::getInstance( 'Logs', 'ScoutTable' );
	        
	        // set the subject
	        $log->setSubject($subject);
	
	        // set the verb
	        $log->setVerb( $verb );
	        
	        // set the object
	        $log->setObject( $object );
	        
	        if (!$log->save())
	        {
	        	JError::raiseNotice( 'plgScoutComContent01', "plgScoutComContent :: ". $log->getError() );
	        }
        }            

        return true;
    }
    
    /**
     * Based on the $task variable, sets the verb array's properties
     * 
     * @return array
     */
    function getVerb()
    {
    	$return = array();
    	
    	$task = JRequest::getVar('task');
    	switch ($task)
    	{
            case "remove":
                $return['value'] = 'deleted';
                $return['name'] = 'Deleted';
                $this->_loadItem = true;
                break;
            case "unpublish":
                $return['value'] = 'unpublished';
                $return['name'] = 'Unpublished';
                $this->_loadItem = true;
                break;
            case "publish":
                $return['value'] = 'published';
                $return['name'] = 'Published';
                $this->_loadItem = true;
                break;
            case "orderup":
            case "orderdown":
            case "reorder":
                $return['value'] = 'reordered';
                $return['name'] = 'ReOrdered';
                $this->_loadItem = true;
                break;
            case "accesspublic":
            case "accessregistered":
            case "accessspecial":
                $return['value'] = 'changedacl';
                $return['name'] = 'Changed ACL';
                $this->_loadItem = true;
                break;
            case "save":
            case "apply":
            default:
            	// don't do anything for these tasks
            	return false;
                break;
    	}
    
    	return $return;
    }
    
    /**
     * Based on the post, sets the object array's properties
     * 
     * @return array
     */
    function getObject()
    {
        // set the object's variables
        $app = JFactory::getApplication();
        $client_id = $app->isAdmin() ? '1' : '0';
        switch($client_id)
        {
            case "1":
                $scope_url = 'index.php?option=com_content&view=article&task=edit&cid[]=';
                break;
            case "0":
            default:
                $scope_url = 'index.php?option=com_content&view=article&task=edit&id=';
                break;
        }
        
        // get the id of item being edited or delteed or pub/unpub
        $id = JRequest::getVar( 'id', JRequest::getVar( 'id', '0', 'post', 'int' ), 'get', 'int' );
        $array = JRequest::getVar('cid', array( $id ), 'post', 'array');
        if (empty($array[0]))
        {
        	return false;
        } 
            else
        {
	        $value = $array[0];
        }
        
        if ($this->_loadItem && is_numeric($value)) 
        {
        	$row =& JTable::getInstance('content');
        	$row->load($value);
        	$title = $row->title;
        }

        $return =
            array(
                'value'=>$value,                                // required. the object's unique identifier. (in the case of content article, is the article id #)
                'name'=>$title,                                 // required. the object's plain english name. 
                'scope_identifier'=>'com_content&view=article',  // required. is unique to this site+component+view(+layout) combo
                'scope_name'=>'Content Manager',        // optional. only necessary if this scope is a new one
                'scope_url'=>$scope_url,                        // optional. only necessary if this is a new scope, and this url is unique to this site+component+view(+layout) combo
                'client_id'=>$client_id                         // optional. if missing, log object sets it.
            );
            
        return $return;    	
    }
    
/**
     * Based on the post, sets the objects array's properties
     * 
     * @return array
     */
    function getObjects()
    {               
        // get the id of item being edited or delteed or pub/unpub
        $id = JRequest::getVar( 'id', JRequest::getVar( 'id', '0', 'post', 'int' ), 'get', 'int' );
        $array = JRequest::getVar('cid', array( $id ), 'post', 'array');
        
        foreach ($array as $val)
        {
	        // set the object's variables
	        $app = JFactory::getApplication();
	        $client_id = $app->isAdmin() ? '1' : '0';
	        switch($client_id)
	        {
	            case "1":
	                $scope_url = 'index.php?option=com_content&view=article&task=edit&cid[]=';
	                break;
	            case "0":
	            default:
	                $scope_url = 'index.php?option=com_content&view=article&task=edit&id=';
	                break;
	        }
        
	        if (empty($val))
	        {
	        	return false;
	        } 
	        else
	        {
		        $value = $val;
	        }
	        
	        if ($this->_loadItem && is_numeric($value)) 
	        {
	        	$row =& JTable::getInstance('content');
	        	$row->load($value);
	        	$title = $row->title;
	        }
	        
	        $return[] =
            array(
                'value'=>$value,                                // required. the object's unique identifier. (in the case of content article, is the article id #)
                'name'=>$title,                                 // required. the object's plain english name. 
                'scope_identifier'=>'com_content&view=article',  // required. is unique to this site+component+view(+layout) combo
                'scope_name'=>'Content Manager',        // optional. only necessary if this scope is a new one
                'scope_url'=>$scope_url,                        // optional. only necessary if this is a new scope, and this url is unique to this site+component+view(+layout) combo
                'client_id'=>$client_id                         // optional. if missing, log object sets it.
            );
        }     
           
        return $return;    	
    }
}