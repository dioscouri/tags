<?php
/**
 * @package	Tags
 * @author 	Dioscouri Design
 * @link 	http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or die( 'Restricted access' );

class TagsTableTags extends DSCTable 
{
	function TagsTableTags( &$db ) 
	{
		
		$tbl_key 	= 'tag_id';
		$tbl_suffix = 'tags';
		$this->set( '_suffix', $tbl_suffix );
		$name 		= 'tags';
		
		parent::__construct( "#__{$name}_{$tbl_suffix}", $tbl_key, $db );	
	}
	
	function check()
	{
		if (empty($this->tag_name))
		{
			$this->setError( JText::_( "Tag Name Required" ) );
			return false;
		}
		
        $nullDate   = $this->_db->getNullDate();
        if (empty($this->created_datetime) || $this->created_datetime == $nullDate)
        {
            $date = JFactory::getDate();
            $this->created_datetime = $date->toMysql();
        }
        
        jimport( 'joomla.filter.output' );
        if (empty($this->tag_alias)) 
        {
            $this->tag_alias = $this->tag_name;
        }
        $this->tag_alias = JFilterOutput::stringURLSafe($this->tag_alias);
        
        if (empty($this->created_by))
        {
            $this->created_by = JFactory::getUser()->id;
        }
        
		return true;
	}
}
