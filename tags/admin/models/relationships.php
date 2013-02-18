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
Tags::load( 'TagsModelBase', 'models._base' );

class TagsModelRelationships extends TagsModelBase 
{
    protected function _buildQueryWhere(&$query)
    {
       	$filter             = $this->getState('filter');
        $filter_id_from	    = $this->getState('filter_id_from');
        $filter_id_to	    = $this->getState('filter_id_to');
        $filter_scopeid     = $this->getState('filter_scopeid');
        $filter_scope       = $this->getState('filter_scope');
        $filter_scopes		= $this->getState('filter_scopes');
        $filter_tagid       = $this->getState('filter_tagid');
        $filter_tag         = $this->getState('filter_tag');
        $filter_item        = $this->getState('filter_item');
        $filter_item_exact  = $this->getState('filter_item_exact');
        $filter_alias       = $this->getState('filter_alias');
        $filter_scope_identifier = $this->getState('filter_scope_identifier');
        
       	if ($filter) 
       	{
			$key	= $this->_db->Quote('%'.$this->_db->getEscaped( trim( strtolower( $filter ) ) ).'%');
			$where = array();
			$where[] = 'LOWER(tbl.relationship_id) LIKE '.$key;
			$where[] = 'LOWER(tbl.tag_id) LIKE '.$key;
			$where[] = 'LOWER(tag.tag_name) LIKE '.$key;
			$query->where('('.implode(' OR ', $where).')');
       	}
       	
		if (strlen($filter_id_from))
        {
            if (strlen($filter_id_to))
        	{
        		$query->where('tbl.relationship_id >= '.(int) $filter_id_from);	
        	}
        		else
        	{
        		$query->where('tbl.relationship_id = '.(int) $filter_id_from);
        	}
       	}
       	
		if (strlen($filter_id_to))
        {
        	$query->where('tbl.relationship_id <= '.(int) $filter_id_to);
       	}

        if (strlen($filter_tagid))
        {
            $query->where('tbl.tag_id = '.(int) $filter_tagid);
        }        
       	
    	if (strlen($filter_tag))
        {
        	$key	= $this->_db->Quote('%'.$this->_db->getEscaped( trim( strtolower( $filter_tag ) ) ).'%');
        	$query->where('LOWER(tag.tag_name) LIKE '.$key);
       	}

       	if (strlen($filter_alias))
        {
            $key    = $this->_db->Quote( $this->_db->getEscaped( trim( strtolower( $filter_alias ) ) ) );
            $query->where("LOWER(tag.tag_alias) = $key");
        }
        
    	if (strlen($filter_scopeid))
        {
            $query->where('scope.scope_id = '.(int) $filter_scopeid);
        }
        
        if (strlen($filter_scope_identifier))
        {
            $query->where("scope.scope_identifier = '". $this->getDbo()->getEscaped( $filter_scope_identifier ) . "'" );
        }
       	
        if (strlen($filter_scope))
        {
            $key    = $this->_db->Quote('%'.$this->_db->getEscaped( trim( strtolower( $filter_scope ) ) ).'%');
            $query->where('LOWER(scope.scope_name) LIKE '.$key);
        }
        
        if (!empty($filter_scopes))
        {
        	$query->where("scope.scope_id IN(".implode(',', $filter_scopes).")");
        }
        
        if (strlen($filter_item))
        {
            $key    = $this->_db->Quote('%'.$this->_db->getEscaped( trim( strtolower( $filter_item ) ) ).'%');
            $where = array();
            $where[] = 'LOWER(tbl.item_value) LIKE '.$key;
            $query->where('('.implode(' OR ', $where).')');
        }
        
    	if (strlen($filter_item_exact))
        {
            $query->where("tbl.item_value = '$filter_item_exact'");
        }

    }

    protected function _buildQueryJoins(&$query)
    {
        $query->join('LEFT', '#__tags_tags AS tag ON tbl.tag_id = tag.tag_id');
        $query->join('LEFT', '#__tags_scopes AS scope ON tbl.scope_id = scope.scope_id');
    }
    
    protected function _buildQueryFields(&$query)
    {
        $fields = array();
        $fields[] = " tag.tag_name AS tag_name ";
        $fields[] = " tag.tag_alias AS tag_alias ";
        $fields[] = " tag.uses AS uses ";
        $fields[] = " tag.created_by AS created_by ";
        $fields[] = " scope.scope_name AS scope_name ";
        $fields[] = " scope.scope_identifier as scope_identifier ";
        $fields[] = " scope.scope_url as scope_url ";
        $fields[] = " scope.scope_table as scope_table ";
        $fields[] = " scope.scope_table_field as scope_table_field ";
        $fields[] = " scope.scope_table_name_field as scope_table_name_field ";
        
        $query->select( $this->getState( 'select', 'tbl.*' ) );     
        $query->select( $fields );
    }
    
	protected function prepareItem( &$item, $key=0, $refresh=false )
    {
        	$item->item_name = $this->getItemName( $item );
			$item->link = 'index.php?option=com_tags&view=relationships&task=edit&id='.$item->relationship_id;
			$item->link_view = $item->scope_url . $item->item_value;
			$item->link_alias = 'index.php?option=com_tags&view=tag&tag='.$item->tag_alias;
    }
	
	
	
	
	function getItemName( $item='' )
	{
	    if (empty($item))
	    {
	        $item = $this->_item;
    	    if (empty($item))
            {
                return null;
            }
	    }
	    
	    if (empty($item->scope_table_name_field) || empty($item->scope_table) || empty($item->scope_table_field))
	    {
	        return $item->item_value;
	    }
	    
        // TODO rewrite this using the query builder
        $query = "
            SELECT 
                ".$item->scope_table_name_field."
            FROM
                ".$item->scope_table."
            WHERE 
                `".$item->scope_table_field."` = '" . $item->item_value . "'
            LIMIT 1
	    ";
        
        $db = $this->getDBO();
        $db->setQuery( $query );
        $result = $db->loadResult();
        return $result;
	}
	
	/**
	 * Returns maximum uses for array of scope ids
	 * 
	 * @param int $scope_ids
	 * @return int $max_uses
	 */
	function getMaxUses( $scope_ids )
	{
	    if ( !empty( $scope_ids ) )
	    {
            $query = "SELECT MAX(uses) FROM #__tags_tags AS tags
                      LEFT JOIN #__tags_relationships AS rels ON tags.tag_id = rels.tag_id ";
            if( !is_array( $scope_ids ) )
            {
            	$query .= "WHERE rels.scope_id = " . $scope_ids;
            }
            else 
            {
            	$query .= "WHERE rels.scope_id IN(".implode(',', $scope_ids).")";
            }
            
                      
            $db = $this->getDBO();
            $db->setQuery( $query );
            $max_uses = $db->loadResult();
	    }
	    
	    return $max_uses;
	}
	
	/**
	 * Returns minimum uses for array of scope ids
	 * 
	 * @param int $scope_ids
	 * @return int $min_uses
	 */
    function getMinUses( $scope_ids )
    {
        if ( !empty( $scope_ids ) )
        {
            $query = "SELECT MIN(uses) FROM #__tags_tags AS tags
                      LEFT JOIN #__tags_relationships AS rels ON tags.tag_id = rels.tag_id ";
        	if( !is_array( $scope_ids ) )
            {
            	$query .= "WHERE rels.scope_id = " . $scope_ids;
            }
            else 
            {
            	$query .= "WHERE rels.scope_id IN(".implode(',', $scope_ids).")";
            }
            $db = $this->getDBO();
            $db->setQuery( $query );
            $min_uses = $db->loadResult();
        }
        
        return $min_uses;
    }
}
