<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<?php JHTML::_('script', 'tags.js', 'media/com_tags/js/'); ?>
<?php JHTML::_('stylesheet', 'tags.css', 'media/com_tags/css/'); ?>

<?php
	$tags = $vars->tags;
	$removeUrl = 'index.php?format=raw&option=com_tags&view=tags&task=doTaskAjax&element=tags.content&elementTask=removeTag&relationship_id=';
?>

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
        	<img src="<?php echo DSC::getURL('images'); ?>publish_x.png" class="x_img href" onclick="Dsc.doTask('<?php echo $removeUrl.$tag->relationship_id; ?>', 'added_tags', document.adminForm, 'Deleting');" />
    	</div>
	<?php $i++; ?>
	<?php endforeach; ?>
<?php } ?>