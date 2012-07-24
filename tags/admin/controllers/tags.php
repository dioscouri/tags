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
        
        foreach (@$state as $key=>$value)
        {
            $model->setState( $key, $value );
        }
        return $state;
    }
}

?>