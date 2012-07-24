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

if ( !class_exists('Tags') ) 
    JLoader::register( "Tags", JPATH_ADMINISTRATOR.DS."components".DS."com_tags".DS."defines.php" );

Tags::load( "TagsHelperRoute", 'helpers.route' );

/**
 * Build the route
 * Is just a wrapper for TagsHelperRoute::build()
 * 
 * @param unknown_type $query
 * @return unknown_type
 */
function TagsBuildRoute(&$query)
{
    return TagsHelperRoute::build($query);
}

/**
 * Parse the url segments
 * Is just a wrapper for TagsHelperRoute::parse()
 * 
 * @param unknown_type $segments
 * @return unknown_type
 */
function TagsParseRoute($segments)
{
    return TagsHelperRoute::parse($segments);
}