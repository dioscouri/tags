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

class TagsControllerTools extends TagsController 
{
	/**
	 * constructor
	 */
	function __construct() 
	{
		parent::__construct();
		
		$this->set('suffix', 'tools');
	}

    /**
     * Displays item
     * @return void
     */
    function view()
    {
        $model = $this->getModel( $this->get('suffix') );
        $model->getId();
        $row = $model->getItem();

        if (empty($row->published))
        {
            $table = $model->getTable();
            $table->load( $row->id );
            $table->published = 1;
            if ($table->save())
            {
                $redirect = "index.php?option=com_tags&view=".$this->get('suffix')."&task=view&id=".$model->getId();
                $redirect = JRoute::_( $redirect, false );
                $this->setRedirect( $redirect );
                return;
            }
        }
        
        parent::view();
    }
}

?>