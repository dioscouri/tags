<?php
/**
* @package		Tags
* @copyright	Copyright (C) 2009 DT Design Inc. All rights reserved.
* @license		GNU GPLv2 <http://www.gnu.org/licenses/old-licenses/gpl-2.0.html>
* @link 		http://www.dioscouri.com
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');


class TagsSelect extends DSCSelect
{
    
    /**
     * 
     * Enter description here ...
     * @param unknown_type $selected
     * @param unknown_type $name
     * @param unknown_type $attribs
     * @param unknown_type $idtag
     * @param unknown_type $allowAny
     * @param unknown_type $title
     * @return unknown_type
     */
    public static function tag( $selected, $name = 'filter_tag', $attribs = array('class' => 'inputbox', 'size' => '1'), $idtag = null, $allowAny = false, $title='Select Tag' )
    {
        $list = array();
        if($allowAny) {
            $list[] =  self::option('', "- ".JText::_( $title )." -" );
        }

        JModel::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_tags/models' );
        $model = JModel::getInstance( 'Tags', 'TagsModel' );
        $model->setState( 'order', 'tag_name' );
        $model->setState( 'direction', 'ASC' );
        $items = $model->getList();
        foreach (@$items as $item)
        {
            $list[] =  self::option( $item->tag_id, JText::_($item->tag_name) );
        }
        
        return self::genericlist($list, $name, $attribs, 'value', 'text', $selected, $idtag );
    }
    
    /**
     * 
     * Enter description here ...
     * @param unknown_type $selected
     * @param unknown_type $name
     * @param unknown_type $attribs
     * @param unknown_type $idtag
     * @param unknown_type $allowAny
     * @param unknown_type $title
     * @return unknown_type
     */
    public static function scope( $selected, $name = 'filter_scope', $attribs = array('class' => 'inputbox', 'size' => '1'), $idtag = null, $allowAny = false, $title='Select Scope' )
    {
        $list = array();
        if($allowAny) {
            $list[] =  self::option('', "- ".JText::_( $title )." -" );
        }

        JModel::addIncludePath( JPATH_ADMINISTRATOR . '/components/com_tags/models' );
        $model = JModel::getInstance( 'Scopes', 'TagsModel' );
        $model->setState( 'order', 'scope_name' );
        $model->setState( 'direction', 'ASC' );
        $items = $model->getList();
        foreach (@$items as $item)
        {
            $list[] =  self::option( $item->scope_id, $item->scope_name );
        }
        
        return self::genericlist($list, $name, $attribs, 'value', 'text', $selected, $idtag );
    }
}
