<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<?php JHTML::_('script', 'tags.js', 'media/com_tags/js/'); ?>
<?php JHTML::_('stylesheet', 'tags.css', 'media/com_tags/css/'); ?>
<?php JHTML::_('stylesheet', 'tags.css', 'plugins/rsevents/tags/media/'); ?>
<?php
	$tags      = $vars->tags;
	$removeUrl = 'index.php?format=raw&option=com_tags&controller=tags&task=doTaskAjax&element=tags.rsevents&elementTask=removeTag&relationship_id=';	
?>
	<div id="added_tags" class="added_tags">		    
		<?php if (!count(@$tags)) { ?>
			<div class="no_tags">
				<?php echo JText::_('No tags found'); ?>
			</div>
		<?php } else { ?>
			<?php $i=0; ?>
			<?php foreach (@$tags as $tag) : ?>
		    	<div id="tag<?php echo $i; ?>" class="tags">    		
		        	<div class="tag_name">
		        		<?php echo @$tag->tag_name; ?>
		        	</div>
		        	<img src="<?php echo JURI::root().'administrator/images/publish_x.png'; ?>" class="x_img href" onclick="tagsDoTask('<?php echo $removeUrl.$tag->relationship_id; ?>', 'added_tags', document.adminForm, 'Deleting');" />
		    	</div>
			<?php $i++; ?>
			<?php endforeach; ?>
		<?php } ?>
	</div>