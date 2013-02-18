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

class TagsHelperContent extends JObject
{
	/**
     * Gets content scope id
     * 
     * @param void
     * @return unknown_type
     */    
    function getArticleScopeId()
    {    	
    	JModel::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tags'.DS.'models' );
	    $model = JModel::getInstance( 'Scopes', 'TagsModel' );
		$model->setState( 'select', 'tbl.scope_id' );
		$model->setState( 'filter_name', 'Content Article' );
		$model->setState( 'filter_identifier', 'com_content.article' );
		$model->setState( 'filter_url', 'index.php?option=com_content&view=article&id=' );
		$model->setState( 'filter_table', '#__content' );
		$model->setState( 'filter_table_field', 'id' );
		$model->setState( 'filter_table_name_field', 'title' );
		$scope_id = $model->getResult();
		
		if (empty($scope_id)) { 
		
			$table = $model->getTable();
			$table->scope_name			   = 'Content Article';
			$table->scope_identifier       = 'com_content.article';
        	$table->scope_url              = 'index.php?option=com_content&view=article&id=';
       		$table->scope_table            = '#__content';
        	$table->scope_table_field      = 'id';
        	$table->scope_table_name_field = 'title';
		
			$table->save();
			
			$scope_id = $table->scope_id;
		}
        
        return $scope_id;
    }
    
  	/**
     * Returns tags for an article
     * 
     * @param int
     * @return object
     */
    function getTagsForArticle( $article_id )
    {
    	JModel::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tags'.DS.'models' );
    	$model = JModel::getInstance( 'Relationships', 'TagsModel' );
		$model->setState( 'filter_scopeid', $this->getArticleScopeId() );
		$model->setState( 'filter_item_exact', $article_id );
		$model->setState( 'order','tag.tag_name' );
		$tags = $model->getList();     
        
        return $tags;
    }
}