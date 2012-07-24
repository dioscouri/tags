<?php
/**
 * @version    1.5
 * @package    Tags
 * @author     Dioscouri Design
 * @link     http://www.dioscouri.com
 * @copyright Copyright (C) 2009 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');
jimport( 'joomla.application.component.model' );

class modTagsCloudHelper extends JObject
{
    /**
     * Sets the modules params as a property of the object
     * @param unknown_type $params
     * @return unknown_type
     */
    function __construct( $params )
    {
        $this->params = $params;
    }
    
    /**
     * Sample use of the products model for getting products with certain properties
     * See admin/models/products.php for all the filters currently built into the model 
     * 
     * @param $parameters
     * @return unknown_type
     */
    function getData()
    {
        // Check the registry to see if our Tags class has been overridden
        if ( !class_exists('Tags') ) 
            JLoader::register( "Tags", JPATH_ADMINISTRATOR.DS."components".DS."com_tags".DS."defines.php" );
        
        // load the config class
        Tags::load( 'TagsConfig', 'defines' );
                
        JTable::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tags'.DS.'tables' );
        JModel::addIncludePath( JPATH_ADMINISTRATOR.DS.'components'.DS.'com_tags'.DS.'models' );
        
        $items = null;

        if( $this->params->get( 'scope_filter' ) )
        {
	        // get the model
	        $model = JModel::getInstance( 'Tags', 'TagsModel' );
	        $model->setState( 'limit', $this->params->get( 'max_results', '15') );
	        $model->setState( 'order', 'tbl.tag_name' );
	        $model->setState( 'direction', 'ASC' );
	        
	        $max = $model->getMaxUses();
	        $min = $model->getMinUses();
	        $sizes = $this->params->get('sizes', '8');
	        
	        $range = $max - $min;
	        $sizing = $range / $sizes;
	        if ($sizing < 1)
	        {
	            $sizing = 1;
	        }
	        
	        if ($items = $model->getList())
	        {
	            foreach ($items as $item)
	            {
	                $item->class = round($item->uses / $sizing);
	            }
	        }
        }
        	else
        {
        	$scopes = $this->params->get('scope_id', NULL);
        	
        	// get the model
	        $model = JModel::getInstance( 'Tag', 'TagsModel' );
	        $model->setState( 'limit', $this->params->get( 'max_results', '15') );
	        $model->setState( 'order', 'tag.tag_name' );
	        $model->setState( 'direction', 'ASC' );
	        if( !empty($scopes) && is_array($scopes) )
	        {
	        	$model->setState( 'filter_scopes', $scopes);
	        }
	        else if( !empty($scopes) && !is_array($scopes) )
	        {
	        	$model->setState( 'filter_scopeid', $scopes);	
	        }
	        
        	$max = $model->getMaxUses( $scopes );
	        $min = $model->getMinUses( $scopes );
	        $sizes = $this->params->get('sizes', '8');
	        
	        $range = $max - $min;
	        $sizing = $range / $sizes;
	        if ($sizing < 1)
	        {
	            $sizing = 1;
	        }
	        
	        if ($items = $model->getList())
	        {
	            foreach ($items as $item)
	            {
	                $item->class = round($item->uses / $sizing);
	            }
	        }
        }
        
        return $items;
    }
}
?>
