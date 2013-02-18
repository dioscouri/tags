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
JLoader::register('K2Plugin', JPATH_ADMINISTRATOR.'/components/com_k2/lib/k2plugin.php');

class plgK2Tags extends K2Plugin
{
    var $_element = 'tags';
    var $pluginName = 'tags';
    var $pluginNameHumanReadable = 'K2 synch with Tags';
    
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
     * onAfterK2Save event after saving item
     * 
     * @param object $item
     * @param bool $isNew
     * @return void
     */
    function onAfterK2Save (& $item, $isNew)
    {
        $db = &JFactory::getDBO();
        
        $item_id = $item->id;
        $scope_id = $this->getK2ScopeId();
        
        $query = "SELECT tagID FROM #__k2_tags_xref WHERE itemID = ". $item_id;
        $db->setQuery($query);
        $tagIDs = $db->loadResultArray();
        
        JLoader::register('TagsHelperTags', JPATH_ADMINISTRATOR.'/components/com_tags/helpers/tags.php');
        $helper = new TagsHelperTags();
        
        if( !empty( $tagIDs ) )
        {                   
            // add tags
            foreach( $tagIDs as $tagID )
            {
                $query = "SELECT * FROM #__k2_tags WHERE id = ". $tagID;
                $db->setQuery($query);
                $k2_tag = $db->loadObject();
                
                if( !empty($k2_tag) && $k2_tag->published )
                {                   
                    $added_tag_id = $helper->checkTagId( $k2_tag->name ); 
                    
                    if( !empty( $added_tag_id ) )
                    {                   
                        $helper->addRelationship( $added_tag_id, $scope_id, $item_id );
                    }
                }
            }
            
            // delete relationships
            $tags = array();            
            foreach( $tagIDs as $tagID )
            {
                $query = "SELECT name FROM #__k2_tags WHERE id = ". $tagID;
                $db->setQuery($query);
                $tags[] =  $db->loadResult();
            }
            $helper->purgeRelationships( $tags, $item_id, $scope_id );
        }
            else 
        {           
            // delete all relationships for this item
            $helper->deleteAllRelationships( $item_id, $scope_id );        
        }
    }    
    
    /**
     * Check K2 scope
     *  
     * @param void
     * @return unknown_type
     */    
    function getK2ScopeId()
    {
        JModel::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_tags/models' );
        $model = JModel::getInstance( 'Scopes', 'TagsModel' );
        $model->setState( 'select', 'tbl.scope_id' );
        $model->setState( 'filter_name', 'K2 Item' );
        $model->setState( 'filter_identifier', 'com_k2&view=item' );
        $model->setState( 'filter_url', 'index.php?option=com_k2&view=item&id=' );
        $model->setState( 'filter_table', '#__k2_items' );
        $model->setState( 'filter_table_field', 'id' );
        $model->setState( 'filter_table_name_field', 'title' );
        $scope_id = $model->getResult();
        
        if (empty($scope_id)) { 
        
            $table = $model->getTable();
            $table->scope_name             = 'K2 Item';
            $table->scope_identifier       = 'com_k2&view=item';
            $table->scope_url              = 'index.php?option=com_k2&view=item&id=';
            $table->scope_table            = '#__k2_items';
            $table->scope_table_field      = 'id';
            $table->scope_table_name_field = 'title';
        
            $table->save();
            
            $scope_id = $table->scope_id;
        }
        
        return $scope_id;
    }
    
    /**
     * After deleting item from trash
     * 
     * @param object $item
     * @return void
     */
    function k2_onAfterDeleteItem( $item_id )
    {
        $scope_id = $this->getK2ScopeId();
        
        JLoader::register('TagsHelperTags', JPATH_ADMINISTRATOR.'/components/com_tags/helpers/tags.php');
        $helper = new TagsHelperTags();
        
        $helper->deleteAllRelationships( $item_id, $scope_id );
    }
}
?>