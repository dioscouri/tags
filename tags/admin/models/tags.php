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

Tags::load( 'TagsModelBase', 'models._base' );

class TagsModelTags extends TagsModelBase  
{
    protected function _buildQueryWhere(&$query)
    {
       	$filter            = $this->getState('filter');
        $filter_id_from	   = $this->getState('filter_id_from');
        $filter_id_to	   = $this->getState('filter_id_to');
        $filter_name	   = $this->getState('filter_name');
        $filter_name_exact = $this->getState('filter_name_exact');
        $filter_alias      = $this->getState('filter_alias');
        $filter_uses_from  = $this->getState('filter_uses_from');
        $filter_uses_to    = $this->getState('filter_uses_to');
        $filter_admin      = $this->getState('filter_admin');
        
       	if ($filter) 
       	{
			$key	= $this->_db->Quote('%'.$this->_db->getEscaped( trim( strtolower( $filter ) ) ).'%');

			$where = array();
			$where[] = 'LOWER(tbl.tag_id) LIKE '.$key;
			$where[] = 'LOWER(tbl.tag_name) LIKE '.$key;
			$where[] = 'LOWER(tbl.tag_alias) LIKE '.$key;
			
			$query->where('('.implode(' OR ', $where).')');
       	}
       	
		if (strlen($filter_id_from))
        {
            if (strlen($filter_id_to))
        	{
        		$query->where('tbl.tag_id >= '.(int) $filter_id_from);	
        	}
        		else
        	{
        		$query->where('tbl.tag_id = '.(int) $filter_id_from);
        	}
       	}
       	
		if (strlen($filter_id_to))
        {
        	$query->where('tbl.tag_id <= '.(int) $filter_id_to);
       	}
       	
        if (strlen($filter_uses_from))
        {
            if (strlen($filter_uses_to))
            {
                $query->where('tbl.uses >= '.(int) $filter_uses_from);  
            }
                else
            {
                $query->where('tbl.uses = '.(int) $filter_uses_from);
            }
        }
        
        if (strlen($filter_uses_to))
        {
            $query->where('tbl.uses <= '.(int) $filter_uses_to);
        }       	
       	
    	if (strlen($filter_name))
        {
        	$key	= $this->_db->Quote('%'.$this->_db->getEscaped( trim( strtolower( $filter_name ) ) ).'%');
        	$query->where('LOWER(tbl.tag_name) LIKE '.$key);
       	}
       	
    	if (strlen($filter_name_exact))
        {        	
        	$query->where("LOWER(tbl.tag_name) = '".$filter_name_exact."'");
       	}
        
        if (strlen($filter_alias))
        {
            $key    = $this->_db->Quote('%'.$this->_db->getEscaped( trim( strtolower( $filter_alias ) ) ).'%');
            $query->where('LOWER(tbl.tag_alias) LIKE '.$key);
        }
        
        if (strlen($filter_admin))
        {
            $query->where('tbl.admin_only = '.(int) $filter_admin);
        }
    }
	protected function prepareItem( &$item, $key=0, $refresh=false )
    {
		$item->link = 'index.php?option=com_tags&view=tags&task=edit&id='.$item->tag_id;
			$item->link_view = 'index.php?option=com_tags&view=relationships&filter_tagid='.$item->tag_id;
			$item->link_alias = 'index.php?option=com_tags&view=tag&tag='.$item->tag_alias;
			$item->link_id = 'index.php?option=com_tags&view=tag&id='.$item->tag_id;
    }
        		
	
	
	function getMaxUses()
	{
	    if (empty($this->max_uses))
	    {
            $query = "SELECT MAX(uses) FROM #__tags_tags;";
            $db = $this->getDBO();
            $db->setQuery( $query );
            $this->max_uses = $db->loadResult();
	    }
	    
	    return $this->max_uses;
	}
	
    function getMinUses()
    {
        if (empty($this->min_uses))
        {
            $query = "SELECT MIN(uses) FROM #__tags_tags;";
            $db = $this->getDBO();
            $db->setQuery( $query );
            $this->min_uses = $db->loadResult();
        }
        
        return $this->min_uses;
    }
}
