<?php defined('_JEXEC') or die('Restricted access'); ?>
<?php $form = @$this->form; ?>
<?php $row = @$this->row; ?>

<form action="<?php echo JRoute::_( @$form['action'] ) ?>" method="post" class="adminform" name="adminForm" enctype="multipart/form-data" >

			<table class="table table-striped table-bordered">
				<tr>
					<td style="width: 100px; text-align: right;" class="key">
						<?php echo JText::_( 'Name' ); ?>:
					</td>
					<td>
						<input  name="scope_name" value="<?php echo @$row->scope_name; ?>" size="48" maxlength="250" type="text" />
					</td>
				</tr>
                <tr>
                    <td style="width: 100px; text-align: right;" class="key">
                        <?php echo JText::_( 'Identifier' ); ?>:
                    </td>
                    <td>
                        <input name="scope_identifier" value="<?php echo @$row->scope_identifier; ?>" size="48" maxlength="250" type="text" />
                    </td>
                </tr>
                <tr>
                    <td style="width: 100px; text-align: right;" class="key">
                        <?php echo JText::_( 'URL' ); ?>:
                    </td>
                    <td>
                        <input name="scope_url" value="<?php echo @$row->scope_url; ?>"  maxlength="250" type="text" class="input-xxlarge span-4" />
                    </td>
                </tr>
                <tr>
                    <td style="width: 100px; text-align: right;" class="key">
                        <?php echo JText::_( 'DB Table' ); ?>:
                    </td>
                    <td>
                        <input name="scope_table" value="<?php echo @$row->scope_table; ?>" size="75" maxlength="250" type="text" />
                    </td>
                </tr>
                <tr>
                    <td style="width: 100px; text-align: right;" class="key">
                        <?php echo JText::_( 'DB Table ID Field' ); ?>:
                    </td>
                    <td>
                        <input name="scope_table_field" value="<?php echo @$row->scope_table_field; ?>" size="75" maxlength="250" type="text" />
                    </td>
                </tr>
                <tr>
                    <td style="width: 100px; text-align: right;" class="key">
                        <?php echo JText::_( 'DB Table Name Field' ); ?>:
                    </td>
                    <td>
                        <input name="scope_table_name_field" value="<?php echo @$row->scope_table_name_field; ?>" size="75" maxlength="250" type="text" />
                    </td>
                </tr>
                
			</table>
			<input type="hidden" name="id" value="<?php echo @$row->scope_id; ?>" />
			<input type="hidden" name="task" value="" />

</form>