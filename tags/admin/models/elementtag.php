<?php
/**
 * @version 1.5
 * @package Tags
 * @media  Dioscouri Design
 * @link    http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this scope is being included by a parent scope */
defined('_JEXEC') or die('Restricted access');



class TagsModelElementTag extends DSCModelElement
{
    var $title_key = 'tag_name';
    var $select_title_constant = 'Select a Tag';
    var $select_constant = 'Select';
    var $clear_constant = 'Clear';

    public function getTable($name='Tags', $prefix='TagsTable', $options = array())
    {
        JTable::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_tags/tables' );
        return parent::getTable($name, $prefix, $options);
    }

    protected function _buildQueryWhere(&$query)
    {
        $filter     = $this->getState('filter');
        $filter_id_from = $this->getState('filter_id_from');
        $filter_id_to   = $this->getState('filter_id_to');
		$filter_title    = $this->getState('filter_title');
        $filter_alias    = $this->getState('filter_alias');
        $filter_enabled = $this->getState('filter_enabled');
        $filter_name = $this->getState('filter_name');

    
        if ($filter)
        {
            $key    = $this->_db->Quote('%'.$this->_db->getEscaped( trim( strtolower( $filter ) ) ).'%');
    
            $where = array();
            $where[] = 'LOWER(tbl.scope_id) LIKE '.$key;
            $where[] = 'LOWER(tbl.scope_alias) LIKE '.$key;
            $where[] = 'LOWER(tbl.scope_name) LIKE '.$key;
            $query->where('('.implode(' OR ', $where).')');
        }
    
        if (strlen($filter_id_from))
        {
            if (strlen($filter_id_to))
            {
                $query->where('tbl.scope_id >= '.(int) $filter_id_from);
            }
            else
            {
                $query->where('tbl.scope_id = '.(int) $filter_id_from);
            }
        }
    
        if (strlen($filter_id_to))
        {
            $query->where('tbl.scope_id <= '.(int) $filter_id_to);
        }
    
        if (strlen($filter_title))
        {
            $key    = $this->_db->Quote('%'.$this->_db->getEscaped( trim( strtolower( $filter_title ) ) ).'%');
            $query->where('LOWER(tbl.scope_alias) LIKE '.$key);
        }
    
        if (strlen($filter_name))
        {
            $key    = $this->_db->Quote('%'.$this->_db->getEscaped( trim( strtolower( $filter_name ) ) ).'%');
            $query->where('LOWER(tbl.scope_name) LIKE '.$key);
        }
    
        if (strlen($filter_enabled))
        {
            $query->where('tbl.scope_enabled = '.$this->_db->Quote($filter_enabled));
        }
    
      
    }
}
?>
