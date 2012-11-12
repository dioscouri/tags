<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php JHTML::_('script', 'tags.js', 'media/com_tags/js/'); ?>
<?php $state = @$this->state; ?>
<?php $form = @$this->form; ?>
<?php $items = @$this->items; ?>

<form action="<?php echo JRoute::_( @$form['action'] )?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">

	<?php echo DSCGrid::pagetooltip( JRequest::getVar('view') ); ?>
	
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
                	<?php echo DSCGrid::sort( 'ID', "tbl.tag_id", @$state->direction, @$state->order ); ?>
                </th>
                <th style="text-align: left;">
                	<?php echo DSCGrid::sort( 'Name', "tbl.tag_name", @$state->direction, @$state->order ); ?>
                </th>
                <th style="width: 50px;">
                    <?php echo DSCGrid::sort( 'Uses', "tbl.uses", @$state->direction, @$state->order ); ?>
                </th>
                <th style="width: 50px;">
                    <?php echo DSCGrid::sort( 'Admin Only?', "tbl.admin_only", @$state->direction, @$state->order ); ?>
                </th>
                <th style="width: 150px;">
                    
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
                	<input id="filter_name" type="text" name="filter_name" value="<?php echo @$state->filter_name; ?>" size="25"/>
                </th>
                <th style="text-align: center;">
                	 <div class="range">
                        <div class="rangeline">
                            <input type="text" placeholder="<?php echo JText::_("From"); ?>" id="filter_id_from" name="filter_uses_from" value="<?php echo @$state->filter_uses_from; ?>" size="5" class="input input-tiny" />
                        </div>
                        <div class="rangeline">
                            <input type="text" placeholder="<?php echo JText::_("To"); ?>" id="filter_id_to" name="filter_uses_to" value="<?php echo @$state->filter_uses_to; ?>" size="5" class="input input-tiny" />
                        </div>
                    </div>
                   
                </th>
                <th>
                    <?php echo TagsSelect::booleans( @$state->filter_admin, 'filter_admin', $attribs, 'filter_admin', true, null, 'Yes', 'No' ); ?>
                </th>
                <th style="text-align: left;">

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
					<?php echo DSCGrid::checkedout( $item, $i, 'tag_id' ); ?>
				</td>
				<td style="text-align: center;">
					<a href="<?php echo $item->link; ?>">
						<?php echo $item->tag_id; ?>
					</a>
				</td>
				<td style="text-align: left;">
					<a href="<?php echo $item->link; ?>">
						<?php echo $item->tag_name; ?>
					</a>
					<br/>
					<?php echo JText::_( "Alias" ); ?>:<?php echo $item->tag_alias; ?>
				</td>
				<td style="text-align: center;">
					<?php echo $item->uses; ?>
				</td>
                <td style="text-align: center;">
					<?php echo DSCGrid::enable( $item->admin_only, $i, 'admin_only.' ); ?>
				</td>
                <td style="text-align: center;">
                    [
                    <a href="<?php echo $item->link_view; ?>">
                        <?php echo JText::_( "View Tagged Items" ); ?>
                    </a>
                    ]
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