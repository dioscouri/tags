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

Tags::load('TagsViewBase', 'views._base');

class TagsViewTags extends TagsViewBase 
{
    public function _defaultToolbar()
    {
        JToolBarHelper::publishList( "admin_only.enable", "Admin Only" );
        JToolBarHelper::unpublishList( "admin_only.disable", "Visible to Public" );
        parent::_defaultToolbar();
    }    
}
