<?php
/**
 * @package Tags
 * @author  Dioscouri Design
 * @link    http://www.dioscouri.com
 * @copyright Copyright (C) 2007 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');


class TagsHelperRoute  extends DSCHelperRoute 
{
    static $itemids = null;
    
    /**
     *
     */
   static function getItems( $option='com_tags' )
    {
        static $items;
        
        $menus      = &JApplication::getMenu('site', array());
        if (empty($menus))
        {
            return array();
        }
        
        if (empty($items))
        {
            $items = array();
        }
        
        if (empty($items[$option]))
        {
            $component  = &JComponentHelper::getComponent($option);
            foreach ($menus->_items as $item)
            {
                if ( !is_object($item) )
                {
                    continue;
                }

                if ($item->componentid == $component->id || (!empty($item->query['option']) && $item->query['option'] == $option) )
                {
                    $items[$option][] = $item;
                }
            }
        }
         
        if (empty($items[$option])) return array();
           return $items[$option]; 
        
       }
    
    
}