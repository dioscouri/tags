<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<?php JHTML::_('script', 'tags.js', 'media/com_tags/js/'); ?>
<?php JHTML::_('stylesheet', 'tags.css', 'media/com_tags/css/'); ?>
<?php
	$tags = $vars->tags;
    jimport('joomla.html.pane');
    $pane = JPane::getInstance('sliders', array('allowAllClose' => true));
    JHTML::_('behavior.tooltip');
?>
<?php 
$addUrl = 'index.php?format=raw&option=com_tags&view=tags&task=doTaskAjax&element=tags.content&elementTask=addTag';
$removeUrl = 'index.php?format=raw&option=com_tags&view=tags&task=doTaskAjax&element=tags.content&elementTask=removeTag&relationship_id=';
?>

<fieldset>
<legend><?php echo JText::_( "Tags" ); ?></legend>

<div style="background-color: #FFFFFF;">
		<table class="admintable" style="width: 100%;">
		<tr>
		    <td class="key hasTip" style="width: 100px; padding-right: 5px;" title="Add a tag to this article">
		        <?php echo JText::_( "Add a new tag" ); ?>
		    </td>
		    <td>
		        <input type="text" name="tag_name" id="tag_name" value="" onkeypress="Tags.handleKeyPress(event,this.form)" />
		        <input id="add_tags_button" type="button" value="<?php echo JText::_( 'Add' ); ?>" onclick="Dsc.doTask('<?php echo $addUrl; ?>', 'added_tags', document.adminForm, 'Adding'); document.getElementById( 'tag_name' ).value='';">
		    </td>
		</tr>
		<tr>
		    <td class="key hasTip" style="width: 100px; padding-right: 5px;" title="<?php echo JText::_( "Current tags" ); ?>">
		        <?php echo JText::_( "Current tags" ); ?>
		    </td>
		    <td>	        
		        <div id="added_tags" class="added_tags">
		        	<?php if (!count(@$tags)) { ?>
		        		<div class="no_tags">
							<?php echo JText::_('None found'); ?>
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
		        </div>
		    </td>
		</tr>
		</table>
</div>
</fieldset>