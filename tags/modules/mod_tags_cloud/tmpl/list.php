<?php
/**
 * @package    Tags
 * @author     Dioscouri Design
 * @link     http://www.dioscouri.com
 * @copyright Copyright (C) 2009 Dioscouri Design. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
*/

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Restricted access');

// Add CSS
$document->addStyleSheet( JURI::root(true).'/modules/mod_tags_cloud/tmpl/mod_tags_cloud.css');

if (!empty($items))
{
    ?>
    <div class="mod_tags_cloud_items_<?php echo $params->get('layout', 'default'); ?><?php echo $params->get('moduleclass_sfx'); ?>">
    <?php
    $count=0;
    if( $params->get( 'include_tag_links' ) )
    {
	    foreach ($items as $item) : ?>
	        <div class="mod_tags_cloud_size_<?php echo $item->class; ?> mod_tags_cloud_item<?php echo $params->get('moduleclass_sfx'); ?>">
	            <a href="<?php echo JRoute::_( $item->link_alias ); ?>">
	                <?php echo $item->tag_name; ?>
	            </a>
	        </div>
	        <?php
	    endforeach;
    }
    else 
	{
	    foreach ($items as $item) : ?>
	        <div class="mod_tags_cloud_size_<?php echo $item->class; ?> mod_tags_cloud_item<?php echo $params->get('moduleclass_sfx'); ?>">
	            
	                <?php echo $item->tag_name; ?>
	            
	        </div>
	        <?php
	    endforeach;
    }
    ?>
    </div>
    <div style="clear: both;">
    	<br/>
	    <?php
	    if( $params->get( 'view_all_tags' ) )
	    {
	    	?>
	    	<a href="<?php echo JRoute::_( 'index.php?option=com_tags&view=tags' ); ?>">
		    	<?php echo JText::_( 'VIEW ALL TAGS' ); ?>
		    </a>
		    <?php
	    }    
    	?>	
    </div>
    <div style="clear: both;"></div>
    <?php 
}
    elseif ($display_null == '1') 
{
    echo JText::_( $null_text );
}