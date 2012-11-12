<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php $form = @$this->form; ?>
<?php $row = @$this->row; ?>

<form action="<?php echo JRoute::_( @$form['action'] ) ?>" method="post" class="adminform" name="adminForm" enctype="multipart/form-data" >


			<table class="table table-striped table-bordered">
				<tr>
					<td style="width: 100px; text-align: right;" class="key">
						<?php echo JText::_( 'Tag ID' ); ?>:
					</td>
					<td>
						<?php echo TagsSelect::tag( @$row->tag_id, 'tag_id' ); ?>
					</td>
                    <td>
                    </td>
				</tr>
                <tr>
                    <td style="width: 100px; text-align: right;" class="key">
                        <?php echo JText::_( 'Scope ID' ); ?>:
                    </td>
                    <td>
                        <?php echo TagsSelect::scope( @$row->scope_id, 'scope_id' ); ?>
                    </td>
                    <td>
                    </td>
                </tr>
                <tr>
                    <td style="width: 100px; text-align: right;" class="key">
                        <?php echo JText::_( 'Item Value' ); ?>:
                    </td>
                    <td>
                        <input name="item_value" value="<?php echo @$row->item_value; ?>"  maxlength="250" type="text" class="input-small" />
                    </td>
                    <td>
                        <?php // echo JText::_( "TAGS RELATIONSHIPS ITEM VALUE TIP" ); ?>
                    </td>
                </tr>
			</table>
			<input type="hidden" name="id" value="<?php echo @$row->relationship_id; ?>" />
			<input type="hidden" name="task" value="" />

</form>