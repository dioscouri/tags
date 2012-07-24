<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<?php JHTML::_('script', 'tags.js', 'media/com_tags/js/'); ?>
<?php JHTML::_('stylesheet', 'tags.css', 'media/com_tags/css/'); ?>
<?php JHTML::_('stylesheet', 'tags.css', 'plugins/koowa/tags/media/'); ?>
<?php
	$tags      = $vars->tags;
	$addUrl    = 'index.php?format=raw&option=com_tags&controller=tags&task=doTaskKoowaAjax&element=tags.koowa&elementTask=addTag&id='.JRequest::getVar( 'id').'&tag_name=';
	$removeUrl = 'index.php?format=raw&option=com_tags&controller=tags&task=doTaskKoowaAjax&element=tags.koowa&elementTask=removeTag&id='.JRequest::getVar( 'id').'&relationship_id=';
	
	$post_id = JRequest::getVar('id');
?>
<form action="" method="post">
	<input type="text" name="tag_name" id="tag_name" value="" />
	<input type="button" value="<?php echo JText::_( 'Save' ); ?>" onclick="tagsDoTask('<?php echo $addUrl; ?>'+document.getElementById( 'tag_name' ).value, 'added_tags', document.adminForm, 'Adding'); document.getElementById( 'tag_name' ).value='';">
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
</form>