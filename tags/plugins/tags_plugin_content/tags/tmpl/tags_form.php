<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<?php JHTML::_('script', 'tags.js', 'media/com_tags/js/'); ?>
<?php JHTML::_('stylesheet', 'tags.css', 'media/com_tags/css/'); ?>

<?php
	$removeUrl = 'index.php?format=raw&option=com_tags&view=tags&task=doTaskAjax&element=tags.content&elementTask=removeTag&unsaved_tag_index=';
?>

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
	        <img src="<?php echo DSC::getURL('images'); ?>publish_x.png" class="x_img href" onclick="Dsc.doTask('<?php echo $removeUrl.$i; ?>', 'added_tags', document.adminForm, 'Deleting');" />        
	    </div>    
	<?php $i++; ?>
	<?php endforeach; ?>
<?php } ?>
