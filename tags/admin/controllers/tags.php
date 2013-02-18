<?php
/**
 * @version	1.5
 * @package	Tags
 * @author 	Dioscouri Design
 * @link 	http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

class TagsControllerTags extends TagsController 
{
	/**
	 * constructor
	 */
	function __construct() 
	{
		parent::__construct();
		
		$this->set('suffix', 'tags');
		$this->registerTask( 'admin_only.enable', 'boolean' );
		$this->registerTask( 'admin_only.disable', 'boolean' );
	}

    /**
     * Sets the model's default state based on values in the request
     *
     * @return array()
     */
    function _setModelState()
    {
    	$state = parent::_setModelState();
        $app = JFactory::getApplication();
        $model = $this->getModel( $this->get('suffix') );
        $ns = $this->getNamespace();

        $state = array();

        $state['filter_name']       = $app->getUserStateFromRequest($ns.'filter_name', 'filter_name', '', '');
        $state['filter_admin']       = $app->getUserStateFromRequest($ns.'filter_admin', 'filter_admin', '', '');
        
        foreach (@$state as $key=>$value)
        {
            $model->setState( $key, $value );
        }
        return $state;
    }
    
    /**
     * Adds a new tag to an item
     *
     * @return unknown_type
     */
    public function addTag()
    {
        $elements = json_decode( preg_replace('/[\n\r]+/', '\n', JRequest::getVar( 'elements', '', 'post', 'string' ) ) );
    
        // convert elements to array that can be binded
        $helper = new DSCHelper();
        $submitted_values = $helper->elementsToArray( $elements );
    
        // prepare needed variables
        $identifier = JRequest::getVar('identifier');
        $scope = JRequest::getVar('scope');
        $tag_name = trim( $submitted_values['tag_name'] );
        $user = JFactory::getUser();
        $rmodel = $this->getModel('relationships');
        
        if (!empty($identifier))
        {
            $rmodel->setState( 'filter_scope_identifier', $scope );
            $rmodel->setState( 'filter_item_exact', $identifier );
            $current_tags = $rmodel->getList();

            $tag_already_exists = false;
            foreach (@$current_tags as $tag)
            {
                if( @$tag->tag_name == $tag_name )
                {
                    $tag_already_exists = true;
                }
            }
    
            if( !$tag_already_exists && !empty( $tag_name ) )
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
                $relations_table->scope_id = $this->getScopeId( $scope );
                $relations_table->item_value = $identifier;
                $relations_table->created_by = $user->id;
                $relations_table->save();
            }
    
            $current_tags = $rmodel->getList(true);
            
            $layout = 'tags_form_existing';
            $items = $current_tags;
        }
        else
        {
            $vars = array();
            
            if ( !empty($submitted_values['unsaved_tags']) )
            {
                $vars = $submitted_values['unsaved_tags'];
    
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
    
            $layout = 'tags_form_new';
            $items = $vars;
        }
        
        $view = $this->getView('tags', 'html');
        $view->set( '_controller', 'tags' );
        $view->set( '_view', 'tags' );
        $view->set( '_doTask', true);
        $view->set( 'hidemenu', false);
        $view->setModel( $rmodel, true );
        $view->assign( 'tags', $items );
        $view->assign( 'identifier', $identifier );
        $view->assign( 'scope', $scope );
         
        $view->setLayout( $layout );
        
        $html = $view->loadTemplate();
        
        $response = array();
        $response['msg'] = $html;
        echo ( json_encode( $response ) );
    }
    
    /**
     *
     * Enter description here ...
     * @return unknown_type
     */
    public function removeTag()
    {
        $elements = json_decode( preg_replace('/[\n\r]+/', '\n', JRequest::getVar( 'elements', '', 'post', 'string' ) ) );
    
        // convert elements to array that can be binded
        $helper = new DSCHelper();
        $submitted_values = $helper->elementsToArray( $elements );

        $rmodel = $this->getModel('relationships');
        
        $identifier = JRequest::getVar('identifier');
        $scope = JRequest::getVar('scope');
    
        if ( !empty($identifier) )
        {
            $relationship_id = JRequest::getInt( 'relationship_id' );
    
            // remove relationship
            JTable::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tags'.DS.'tables' );
            $relations_table = JTable::getInstance('Relationships', 'TagsTable');
            $relations_table->load( $relationship_id );
            $relations_table->delete();

            $rmodel->setState( 'filter_scope_identifier', $scope );
            $rmodel->setState( 'filter_item_exact', $identifier );
            $current_tags = $rmodel->getList(true);
            
            $layout = 'tags_form_existing';
            $items = $current_tags;
        }
            else
        {
            $vars = array();

            if ( !empty($submitted_values['unsaved_tags']) )
            {
                // prepare needed variables
                $vars = $submitted_values['unsaved_tags'];
                $tag_index = JRequest::getVar( 'unsaved_tag_index' );
    
                // search and destroy
                if( is_array( $vars ) )
                {
                    foreach( $vars as $key => $value ) 
                    {
                        if ( $key == $tag_index )
                        {
                            unset( $vars[$key] );
                            break;
                        }
                    }
                    	
                    $vars = array_values( $vars );
                }
            }
    
            $layout = 'tags_form_new';
            $items = $vars;
        }

        $view = $this->getView('tags', 'html');
        $view->set( '_controller', 'tags' );
        $view->set( '_view', 'tags' );
        $view->set( '_doTask', true);
        $view->set( 'hidemenu', false);
        $view->setModel( $rmodel, true );
        $view->assign( 'tags', $items );
        $view->assign( 'identifier', $identifier );
        $view->assign( 'scope', $scope );
         
        $view->setLayout( $layout );
        
        $html = $view->loadTemplate();
        
        $response = array();
        $response['msg'] = $html;
        echo ( json_encode( $response ) );
    }
    
    /**
     * Gets scope id
     *
     * @param void
     * @return unknown_type
     */
    private function getScopeId( $scope )
    {
        JTable::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_tags/tables' );
    
        $table = JTable::getInstance( 'Scopes', 'TagsTable' );
        $table->load( array( 'scope_identifier'=>$scope ) );
    
        $scope_id = $table->scope_id;
    
        return $scope_id;
    }
}

?>