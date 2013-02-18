<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php JHTML::_('script', 'tags.js', 'media/com_tags/js/'); ?>
<?php $state = @$this->state; ?>
<?php $form = @$this->form; ?>
<?php $items = @$this->items; ?>

<form action="<?php echo JRoute::_( @$form['action'] )?>" method="post" name="adminForm" enctype="multipart/form-data">

	<?php echo DSCGrid::pagetooltip( JRequest::getVar('view') ); ?>
	<div class="pull-left">
	<?php if (!empty($state->filter_tagid)) { 
                    echo JText::_( "Items Tagged As" ) . ": " . $this->tag->tag_name;
                }?>
                
                <input type="hidden" name="filter_tagid" id="filter_tagid" value="<?php echo @$state->filter_tagid; ?>" />
	</div>
 		<?php echo DSCGrid::searchform(@$state->filter ) ?>

	<table class="table table-striped table-bordered" style="clear: both;">
		<thead>
            <tr>
                <th style="width: 5px;">
                	<?php echo JText::_("Num"); ?>
                </th>
                <th style="width: 20px;">
                	<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( @$items ); ?>);" />
                </th>
                <th style="width: 50px;">
                	<?php echo DSCGrid::sort( 'ID', "tbl.relationship_id", @$state->direction, @$state->order ); ?>
                </th>
                <th style="text-align: left;">
                	<?php echo DSCGrid::sort( 'Tagged Item', "tbl.item_value", @$state->direction, @$state->order ); ?>
                </th>
                <th style="text-align: left;">
                    <?php echo DSCGrid::sort( 'Scope', "scope.scope_name", @$state->direction, @$state->order ); ?>
                </th>
                <th style="text-align: center;">
                    <?php echo DSCGrid::sort( 'Tag', "tag.tag_name", @$state->direction, @$state->order ); ?>
                </th>
            </tr>
            <tr class="filterline">
                <th colspan="3">
                	<?php $attribs = array('class' => 'inputbox', 'onchange' => 'document.adminForm.submit();'); ?>
                	 <div class="range">
                        <div class="rangeline">
                            <input type="text" placeholder="<?php echo JText::_("From"); ?>" id="filter_id_from" name="filter_id_from" value="<?php echo @$state->filter_id_from; ?>" size="5" class="input input-tiny" />
                        </div>
                        <div class="rangeline">
                            <input type="text" placeholder="<?php echo JText::_("To"); ?>" id="filter_id_to" name="filter_id_to" value="<?php echo @$state->filter_id_to; ?>" size="5" class="input input-tiny" />
                        </div>
                    </div>
                </th>
                <th style="text-align: left;">
                	<input type="text" id="filter_item" name="filter_item" value="<?php echo @$state->filter_item; ?>" size="25"/>
                </th>
                <th style="text-align: left;">
                    <input type="text" id="filter_scope" name="filter_scope" value="<?php echo @$state->filter_scope; ?>" size="25"/>
                </th>
                <th style="text-align: center;">
                    <input type="text" id="filter_tag" name="filter_tag" value="<?php echo @$state->filter_tag; ?>" size="25"/>
                </th>
            </tr>
			<tr>
				<th colspan="20" style="font-weight: normal;">
					<div style="float: right; padding: 5px;"><?php echo @$this->pagination->getResultsCounter(); ?></div>
					<div style="float: left;"><?php echo @$this->pagination->getListFooter(); ?></div>
				</th>
			</tr>
		</thead>
        <tfoot>
            <tr>
                <td colspan="20">
                    <div style="float: right; padding: 5px;"><?php echo @$this->pagination->getResultsCounter(); ?></div>
                    <?php echo @$this->pagination->getPagesLinks(); ?>
                </td>
            </tr>
        </tfoot>
        <tbody>
		<?php $i=0; $k=0; ?>
        <?php foreach (@$items as $item) : ?>
            <tr class='row<?php echo $k; ?>'>
				<td align="center">
					<?php echo $i + 1; ?>
				</td>
				<td style="text-align: center;">
					<?php echo DSCGrid::checkedout( $item, $i, 'relationship_id' ); ?>
				</td>
				<td style="text-align: center;">
					<a href="<?php echo $item->link; ?>">
						<?php echo $item->relationship_id; ?>
					</a>
				</td>
				<td style="text-align: left;">
					<a href="<?php echo JRoute::_( JURI::root(true) . "/" . $item->link_view ); ?>" target="_blank">
						<?php echo (!empty($item->item_name)) ? $item->item_name . " [" . $item->item_value . "]"  : $item->item_value; ?>
					</a>
				</td>
                <td style="text-align: left;">
                    <?php echo $item->scope_name; ?>
                </td>
                <td style="text-align: center;">
                    <?php echo $item->tag_name; ?>
                </td>
			</tr>
			<?php $i=$i+1; $k = (1 - $k); ?>
			<?php endforeach; ?>
			
			<?php if (!count(@$items)) : ?>
			<tr>
				<td colspan="10" align="center">
					<?php echo JText::_('No items found'); ?>
				</td>
			</tr>
			<?php endif; ?>
		</tbody>
	</table>

	<input type="hidden" name="order_change" value="0" />
	<input type="hidden" name="id" value="" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="" />
	<input type="hidden" name="filter_order" value="<?php echo @$state->order; ?>" />
	<input type="hidden" name="filter_direction" value="<?php echo @$state->direction; ?>" />
	
	<?php echo $this->form['validate']; ?>
</form>