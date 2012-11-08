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


class TagsModelTools extends DSCModel 
{	
    protected function _buildQueryWhere(&$query)
    {
       	$filter     = $this->getState('filter');

       	if ($filter) 
       	{
			$key	= $this->_db->Quote('%'.$this->_db->getEscaped( trim( strtolower( $filter ) ) ).'%');

			$where = array();
			$where[] = 'LOWER(tbl.id) LIKE '.$key;
			$where[] = 'LOWER(tbl.name) LIKE '.$key;
			$where[] = 'LOWER(tbl.element) LIKE '.$key;
			
			$query->where('('.implode(' OR ', $where).')');
       	}
       	
		$query->where("LOWER(tbl.folder) = 'tags'");
		$query->where("tbl.element LIKE 'tool_%'");
    }
	
	protected function prepareItem( &$item, $key=0, $refresh=false )
    {
        	$item->link = 'index.php?option=com_tags&view=tools&task=view&id='.$item->id;
    }
    	
	
}
