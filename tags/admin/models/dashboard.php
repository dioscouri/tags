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
defined('_JEXEC') or die('Restricted access');


class TagsModelDashboard extends DSCModel 
{
	 function getTable($name='Config', $prefix='TagsTable', $options = array())
    {
        JTable::addIncludePath( JPATH_ADMINISTRATOR.'/components/com_tags/tables' );
        return parent::getTable($name, $prefix, $options);
    }	
		
}