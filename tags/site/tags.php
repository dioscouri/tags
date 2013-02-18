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

// Check the registry to see if our Tags class has been overridden
if ( !class_exists('Tags') ) 
    JLoader::register( "Tags", JPATH_ADMINISTRATOR.DS."components".DS."com_tags".DS."defines.php" );

// load the config class
Tags::load( 'TagsConfig', 'defines' );

// set the options array
$options = array( 'site'=>'site', 'type'=>'components', 'ext'=>'com_tags' );

// Require the base controller
Tags::load( 'TagsController', 'controller', $options );

// Require specific controller if requested
$controller = JRequest::getWord('controller', JRequest::getVar( 'view' ) );
if (!Tags::load( 'TagsController'.$controller, "controllers.$controller", $options ))
    $controller = '';

if (empty($controller))
{
    // redirect to default
    $redirect = "index.php?option=com_tags&view=tags";
    $redirect = JRoute::_( $redirect, false );
    JFactory::getApplication()->redirect( $redirect );
}

$doc = JFactory::getDocument();
$uri = JURI::getInstance();
$js = "var com_tags = {};\n";
$js.= "com_tags.jbase = '".$uri->root()."';\n";
$doc->addScriptDeclaration($js);

// load the plugins
JPluginHelper::importPlugin( 'tags' );

// Create the controller
$classname = 'TagsController'.$controller;
$controller = Tags::getClass( $classname );

// ensure a valid task exists
$task = JRequest::getVar('task');
if (empty($task))
{
    $task = 'display';  
}
JRequest::setVar( 'task', $task );

// Perform the requested task
$controller->execute( $task );

// Redirect if set by the controller
$controller->redirect();

?>