<?php defined( '_JEXEC' ) or die( 'Restricted access' ); ?>
<?php JHTML::_('script', 'tags.js', 'media/com_tags/js/'); ?>
<?php JHTML::_('stylesheet', 'tags.css', 'media/com_tags/css/'); ?>
<?php JHTML::_('stylesheet', 'tags.css', 'plugins/editors-xtd/tags/media/'); ?>
<?php
	$tags = $vars->tags;
    jimport('joomla.html.pane');
    $pane = JPane::getInstance('sliders', array('allowAllClose' => true));
    JHTML::_('behavior.tooltip');
?>
<?php 
$addUrl = 'index.php?format=raw&option=com_tags&task=doTaskAjax&element=tags.content&elementTask=addTag';
$removeUrl = 'index.php?format=raw&option=com_tags&task=doTaskAjax&element=tags.content&elementTask=removeTag&relationship_id=';
?>
<?php 
    echo $pane->startPane("content_tag");
    echo $pane->startPanel( "Tags", "tags" );
?>


<div style="background-color: #FFFFFF;">
		<table class="admintable" style="width: 100%;">
		<tr>
		    <td class="key hasTip" style="width: 50px; padding-right: 5px;" title="Enter a tag for this article">
		        <?php echo JText::_( "Tag" ); ?>
		    </td>
		    <td>
		        <input type="text" name="tag_name" id="tag_name" value="" />
		        <input type="button" value="<?php echo JText::_( 'Save' ); ?>" onclick="tagsDoTask('<?php echo $addUrl; ?>', 'added_tags', document.adminForm, 'Adding'); document.getElementById( 'tag_name' ).value='';">
		    </td>
		</tr>
		<tr>
		    <td class="key hasTip" style="width: 50px; padding-right: 5px;" title="Added tags">
		        <?php echo JText::_( "Added tags" ); ?>
		    </td>
		    <td>	        
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
			        			<img src="images/publish_x.png" class="x_img href" onclick="tagsDoTask('<?php echo $removeUrl.$tag->relationship_id; ?>', 'added_tags', document.adminForm, 'Deleting');" />
			        		</div>
			        	<?php $i++; ?>
			        	<?php endforeach; ?>
		        	<?php } ?>
		        </div>
		    </td>
		</tr>
		</table>
</div>
<?php 
    echo $pane->endPanel();
    echo $pane->endPane();
?>