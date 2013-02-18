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

class TagsControllerTag extends TagsController
{
	/**
	 * constructor
	 */
	function __construct()
	{
		parent::__construct();

		$this->set('suffix', 'tag');
	}

	/**
	 * Sets the model's state
	 *
	 * @return array()
	 */
	function _setModelState()
	{
		$state = parent::_setModelState();
		$app = JFactory::getApplication();
		$model = $this->getModel( $this->get('suffix') );
		$ns = $this->getNamespace();

		$state['filter_alias'] = $app->getUserStateFromRequest($ns.'.tag', 'tag', '', 'cmd');
		$state['filter_tagid'] = $app->getUserStateFromRequest($ns.'.id', 'id', '', '');
        
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
        
        $tag_id = JRequest::getInt( 'id' );
        $tag_alias = JRequest::getVar( 'tag' );
        
        $tag_model = $this->getModel( 'tags' );
        $tag = $tag_model->getTable();
        
        $model =& $this->getModel( $this->get('suffix') );
        
        if (!empty($tag_alias))
        {
            // use it
            $tag->load( array( 'tag_alias'=>$tag_alias ) );
            $model->setState( 'filter_alias', $tag_alias );
        }
        elseif (!empty($tag_id))
        {
            // use it
            $tag->load( $tag_id );
            $model->setState( 'filter_tagid', $tag_id );
        }
        else
        {
            // default to the state, priority to id
            $this->_setModelState();
        
            $filter_tagid = $model->getState( 'filter_tagid' );
            if (!empty($filter_tagid))
            {
                $tag->load( $filter_tagid );
            }
                else
            {
                $filter_alias = $model->getState( 'filter_alias' );
                if (!empty($filter_alias))
                {
                    $tag->load( array( 'tag_alias'=>$filter_alias ) );
                }
            }
        }

        $items = $model->getList();
        $view->assign( 'items', $items );
        $view->assign( 'tag', $tag );
        
        parent::display();
	}
}

?>