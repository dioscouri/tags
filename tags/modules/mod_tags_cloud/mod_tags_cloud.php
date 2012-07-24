<?php
/**
 * @version	1.5
 * @package	Tags
 * @author 	Dioscouri Design
 * @link 	http://www.dioscouri.com
 * @copyright Copyright (C) 2009 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

// Check the registry to see if our Tags class has been overridden
if ( !class_exists('Tags') ) 
{
    JLoader::register( "Tags", JPATH_ADMINISTRATOR."/components/com_tags/defines.php" );
}
    
require_once( dirname(__FILE__).DS.'helper.php' );

// include lang files
$element = strtolower( 'com_Tags' );
$lang =& JFactory::getLanguage();
$lang->load( $element, JPATH_BASE );
$lang->load( $element, JPATH_ADMINISTRATOR );

$mainframe =& JFactory::getApplication();
$document = &JFactory::getDocument();

$helper = new modTagsCloudHelper( $params ); 
$items = $helper->getData();

$display_null = $params->get( 'display_null', '1' );
$null_text = $params->get( 'null_text', '' );

if (!empty($items) || (empty($items) && $display_null) )
{
    require( JModuleHelper::getLayoutPath( 'mod_tags_cloud', $params->get('layout', 'default') ) );    
}
    else 
{
    // don't display anything
}

