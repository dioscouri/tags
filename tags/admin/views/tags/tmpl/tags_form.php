<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<?php JHTML::_('script', 'tags.js', 'media/com_tags/js/'); ?>
<?php JHTML::_('stylesheet', 'tags.css', 'media/com_tags/css/'); ?>
<?php
$tags = $this->tags;
?>
<?php 
$addUrl = 'index.php?option=com_tags&view=tags&task=addTag&format=raw&scope='. $this->scope . '&identifier=' . $this->identifier;
$removeUrl = 'index.php?option=com_tags&view=tags&task=removeTag&format=raw&scope='. $this->scope . '&identifier=' . $this->identifier . '&relationship_id=';
?>

<fieldset>
<legend><?php echo JText::_( "Tags" ); ?></legend>

<div style="background-color: #FFFFFF;">
		<table class="table table-striped table-bordered">
		<tbody>
		<tr>
		    <td class="dsc-key hasTip" style="width: 100px; padding-right: 5px;" title="Add a tag to this item">
		        <?php echo JText::_( "Add a new tag" ); ?>
		    </td>
		    <td>
                <input type="text" name="tag_name" id="tag_name" value="" onkeypress="Tags.handleKeyPress(event,this.form)" />
                <input class="btn btn-large" id="add_tags_button" type="button" value="<?php echo JText::_( 'Add' ); ?>" onclick="Dsc.doTask('<?php echo $addUrl; ?>', 'added_tags', this.form, 'Adding'); document.getElementById( 'tag_name' ).value='';">
		    </td>
		</tr>
		<tr>
		    <td class="dsc-key hasTip" style="width: 100px; padding-right: 5px;" title="<?php echo JText::_( "Current tags" ); ?>">
		        <?php echo JText::_( "Current tags" ); ?>
		    </td>
		    <td>	        
		        <div id="added_tags" class="added_tags">
		        	<?php if (empty($tags)) { ?>
		        		<div class="no_tags">
							<?php echo JText::_('None found'); ?>
						</div>
		        	<?php } else { ?> 
			        	<?php $i=0; ?>
			        	<?php foreach ($tags as $tag) : ?>
			        		<div id="tag<?php echo $i; ?>" class="tags">
			        			<div class="tag_name">
			        			<?php echo $tag->tag_name; ?>
			        			</div>
			        			<img src="<?php echo DSC::getURL('images'); ?>publish_x.png" class="x_img href" onclick="Dsc.doTask('<?php echo $removeUrl.$tag->relationship_id; ?>', 'added_tags', this.form, 'Deleting');" />
			        		</div>
			        	<?php $i++; ?>
			        	<?php endforeach; ?>
		        	<?php } ?>
		        </div>
		    </td>
		</tr>
		</tbody>
		</table>
</div>
</fieldset>