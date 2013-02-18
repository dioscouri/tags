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
	}

	/**
	 * Sets the model's state
	 *
	 * @return array()
	 */
	function _setModelState( $model_name='' )
	{
		$state = parent::_setModelState();
		$app = JFactory::getApplication();
		if (empty($model_name)) { $model_name = $this->get('suffix'); } 
		$model = $this->getModel( $model_name );
		$ns = $this->getNamespace();

		$state['filter_enabled']  = 1;      
        
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
                
        $scope_id = JRequest::getInt( 'id' );

        JModel::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tags'.DS.'models' );
	    $model = JModel::getInstance( 'Tag', 'TagsModel' );
        
        if (!empty($scope_id))
        {
            // use it
            $model->setState( 'filter_scopeid', $scope_id );           
        }
        
        $query = $model->getQuery();
        $query->group( 'tbl.tag_id');
        $model->setQuery( $query );
        
        $app = JFactory::getApplication();
        $ns = $app->getName().'::'.'com.tags.model.'.$model->getTable()->get('_suffix');
        $state = array();
        $limit  = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
        $limitstart = JRequest::getVar('limitstart', '0', 'request', 'int');
        $state['limitstart'] = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
        $state['limit']     = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
        $state['order']     = $app->getUserStateFromRequest($ns.'.filter_order', 'filter_order', 'tbl.'.$model->getTable()->getKeyName(), 'cmd');
        $state['direction'] = $app->getUserStateFromRequest($ns.'.filter_direction', 'filter_direction', 'ASC', 'word');
	    foreach (@$state as $key=>$value)
        {
            $model->setState( $key, $value );
        }
        
        $items = $model->getList();
        $view->assign( 'scope_tags', $items );
        $view->setModel( $model );
        
        parent::display();
	}
}

?>