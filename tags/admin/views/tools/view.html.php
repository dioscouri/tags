<?php
/**
 * @package	Tags
 * @author 	Dioscouri Design
 * @link 	http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');


class TagsViewTools extends DSCView 
{
	/**
	 * 
	 * @param $tpl
	 * @return unknown_type
	 */
	function display($tpl=null) 
	{
		$layout = $this->getLayout();
		switch(strtolower($layout))
		{
			case "view":
				$this->_form($tpl);
			  break;
			case "form":
				JRequest::setVar('hidemainmenu', '1');
				$this->_form($tpl);
			  break;
			case "default":
			default:
				$this->_default($tpl);
			  break;
		}
		Tags::load( 'TagsGrid', 'library.grid' );
        
		parent::display($tpl);
	}

    function _defaultToolbar()
    {
    }

    function _viewToolbar()
    {
        JToolBarHelper::custom( 'view', 'forward', 'forward', JText::_('Submit'), false );
        JToolBarHelper::cancel( 'close', JText::_( 'Close' ) );
    }
}
