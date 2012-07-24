<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<?php JHTML::_('script', 'tags.js', 'media/com_tags/js/'); ?>
<?php JHTML::_('stylesheet', 'tags.css', 'media/com_tags/css/'); ?>
<?php JHTML::_('stylesheet', 'tags.css', 'plugins/rsevents/tags/media/'); ?>
<?php
	$removeUrl = 'index.php?format=raw&option=com_tags&controller=tags&task=doTaskAjax&element=tags.rsevents&elementTask=removeTag&unsaved_tag_index=';	
?>

<form action="" method="post">
<?php if (!count(@$vars)) { ?>
	<div class="no_tags">
		<?php echo JText::_('No tags found'); ?>
	</div>
<?php } else { ?>
	<?php $i=0; ?>
	<?php foreach (@$vars as $var) : ?>
	    <div id="tag<?php echo $i; ?>" name="tag_name" class="tags">	    	
	    	<input type='hidden' name='vars[]' value='<?php echo @$var; ?>'>
	        <div class="tag_name">
	        	<?php echo @$var; ?>
	        </div>
	        <img src="<?php echo JURI::root().'administrator/images/publish_x.png'; ?>" class="x_img href" onclick="tagsDoTask('<?php echo $removeUrl.$i; ?>', 'added_tags', document.adminForm, 'Deleting');" />        
	    </div>    
	<?php $i++; ?>
	<?php endforeach; ?>
<?php } ?>
</form>