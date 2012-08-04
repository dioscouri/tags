<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php $form = @$this->form; ?>
<?php $row = @$this->row; ?>

<form action="<?php echo JRoute::_( @$form['action'] ) ?>" method="post" class="adminform" name="adminForm" id="adminForm" enctype="multipart/form-data" >

	<fieldset>
		<legend><?php echo JText::_('Form'); ?></legend>
			<table class="admintable">
				<tr>
					<td style="width: 100px; text-align: right;" class="key">
						<?php echo JText::_( 'Name' ); ?>:
					</td>
					<td>
						<input name="tag_name" value="<?php echo @$row->tag_name; ?>" size="48" maxlength="250" type="text" />
					</td>
				</tr>
                <tr>
                    <td style="width: 100px; text-align: right;" class="key">
                        <?php echo JText::_( 'Alias' ); ?>:
                    </td>
                    <td>
                        <input name="tag_alias" value="<?php echo @$row->tag_alias; ?>" size="48" maxlength="250" type="text" />
                    </td>
                </tr>
                <tr>
    				<td style="width: 100px; text-align: right;" class="key">
    					<label for="admin_only">
    						<?php echo JText::_( 'Admin Only' ); ?>:
    					</label>
    				</td>
    				<td>
    					<?php echo JHTML::_( 'select.booleanlist', 'admin_only', '', @$row->admin_only ); ?>
    				</td>
    			</tr>
			</table>
			<input type="hidden" name="id" value="<?php echo @$row->tag_id; ?>" />
			<input type="hidden" name="task" value="" />
	</fieldset>
</form>