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


class TagsTableScopes extends DSCTable 
{
	function TagsTableScopes( &$db ) 
	{
		
		$tbl_key 	= 'scope_id';
		$tbl_suffix = 'scopes';
		$this->set( '_suffix', $tbl_suffix );
		$name 		= 'tags';
		
		parent::__construct( "#__{$name}_{$tbl_suffix}", $tbl_key, $db );	
	}
	
	function check()
	{
		if (empty($this->scope_name))
		{
			$this->setError( JText::_( "Scope Name Required" ) );
			return false;
		}
		
	    if (empty($this->scope_identifier))
        {
            $this->setError( JText::_( "Scope Identifier Required" ) );
            return false;
        }
		return true;
	}
}
