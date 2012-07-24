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

class TagsControllerRelationships extends TagsController 
{
	/**
	 * constructor
	 */
	function __construct() 
	{
		parent::__construct();
		
		$this->set('suffix', 'relationships');
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

        $state['filter_item']       = $app->getUserStateFromRequest($ns.'filter_item', 'filter_item', '', '');
        $state['filter_scope']       = $app->getUserStateFromRequest($ns.'filter_scope', 'filter_scope', '', '');
        $state['filter_tagid']       = $app->getUserStateFromRequest($ns.'filter_tagid', 'filter_tagid', '', '');
        $state['filter_tag']       = $app->getUserStateFromRequest($ns.'filter_tag', 'filter_tag', '', '');
        
        foreach (@$state as $key=>$value)
        {
            $model->setState( $key, $value );
        }
        return $state;
    }
    
    function display()
    {
        $viewType   = JFactory::getDocument()->getType();
        $viewName   = JRequest::getCmd( 'view', $this->getName() );
        $viewLayout = JRequest::getCmd( 'layout', 'default' );
        $view = & $this->getView( $viewName, $viewType, '', array( 'base_path'=>$this->_basePath) );
        
        $this->_setModelState();
        $model =& $this->getModel( $this->get('suffix') );
        
        $tag_model = $this->getModel( 'tags' );
        $tag = $tag_model->getTable();
        $filter_tagid = $model->getState( 'filter_tagid' );
        if (!empty($filter_tagid))
        {
            $tag->load( $filter_tagid );
        }
        $view->assign( 'tag', $tag );
        
        parent::display();
    }
}

?>