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

// before executing any tasks, check the integrity of the installation
Tags::getClass( 'TagsHelperDiagnostics', 'helpers.diagnostics' )->checkInstallation();

// Require the base controller
Tags::load( 'TagsController', 'controller' );
// Require the base controller


// Require specific controller if requested
$controller = JRequest::getWord('controller', JRequest::getVar( 'view' ) );
if (!Tags::load( 'TagsController'.$controller, "controllers.$controller" ))
    $controller = '';

if (empty($controller))
{
    // redirect to default
	$default_controller = new TagsController();
	$redirect = "index.php?option=com_tags&view=" . $default_controller->default_view;
    $redirect = JRoute::_( $redirect, false );
    JFactory::getApplication()->redirect( $redirect );
}

JHTML::_('stylesheet', 'admin.css', 'media/com_tags/css/');

$doc = JFactory::getDocument();
$uri = JURI::getInstance();
$js = "var com_tags = {};\n";
$js.= "com_tags.jbase = '".$uri->root()."';\n";
$doc->addScriptDeclaration($js);

$parentPath = JPATH_ADMINISTRATOR . '/components/com_tags/helpers';
DSCLoader::discover('TagsHelper', $parentPath, true);

$parentPath = JPATH_ADMINISTRATOR . '/components/com_tags/library';
DSCLoader::discover('Tags', $parentPath, true);

JHTML::_('script', 'common.js', 'media/dioscouri/js/');
JHTML::_('stylesheet', 'common.css', 'media/dioscouri/css/');
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