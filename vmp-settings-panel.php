<?php
	if ( isset( $_SESSION[ 'messages' ] ) && ! empty( $_SESSION[ 'messages' ] ) ){
?>
<div id="vmp_message_panel_alt" align="center" style="display:none;">
<?php echo $_SESSION[ 'messages' ]; unset( $_SESSION[ 'messages' ] ); ?>
</div>
<?php
	}
?>
<div class="main_panel" align="center">
	<?php
		$_wpnonce = wp_create_nonce( 'set_plugin_options' );
		$action = admin_url( 'admin.php?action=set_plugin_options_092113&_wpnonce=' . $_wpnonce );
		
		$settings = array();
		$settings = vmp_fetch_settings();
		
		if ( 1 == $settings[ 0 ] ){
			$checked_1 = 'checked';
			$checked_2 = '';
		}else{
			$checked_1 = '';
			$checked_2 = 'checked';
		}
	?>
	<form name="vmp_set_plugin_options" id="vmp_set_plugin_options" action="<?php echo $action; ?>" method="post"/>
		<table class="wp-list-table widefat" cellspacing="0">
			<thead>
				<tr>
					<th colspan="3" align="left" valign="top"><center>Plugin option settings</center></th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<th align="left" valign="top">Can a registered user vote a post multiple times?</th>
					<td align="left" valign="top">
						<input type="radio" name="vmp_multiple_voting" id="vmp_multiple_voting_yes" value="1" <?php echo $checked_1; ?>>&nbsp;&nbsp;&nbsp;&nbsp;Yes
					</td>
					<td align="left" valign="top">
						<input type="radio" name="vmp_multiple_voting" id="vmp_multiple_voting_no" value="0" <?php echo $checked_2; ?>>&nbsp;&nbsp;&nbsp;&nbsp;No
					</td>
				</tr>
				<tr><th colspan="3" align="left" valign="top">&nbsp;</th></tr>
				<tr>
					<th align="left" valign="top">A user can cast vote again for a particular post after: </th>
					<td align="left" valign="top">
						<input type="text" name="vmp_consecutive_voting_interval" id="vmp_consecutive_voting_interval" value="<?php echo $settings[ 1 ]; ?>"/>&nbsp;&nbsp;&nbsp;&nbsp;In Seconds
					</td>
					<td align="left" valign="top">
						E.G. put 3600 for an interval of 1 hour
					</td>
				</tr>
				<tr><th colspan="3" align="left" valign="top">&nbsp;</th></tr>
				<tr>
					<th colspan="3" align="left" valign="top"><center>Pagination settings</center></th>
				</tr>
				<tr>
					<th align="left" valign="top">Rows to display per page: </th>
					<td colspan="2" align="left" valign="top">
						<input type="text" name="vmp_rows_per_page" id="vmp_rows_per_page" value="<?php echo $settings[ 2 ]; ?>"/>
					</td>
				</tr>
				<tr>
					<th align="left" valign="top">Maximum page links to display at a time: </th>
					<td colspan="2" align="left" valign="top">
						<input type="text" name="vmp_max_page_links" id="vmp_max_page_links" value="<?php echo $settings[ 3 ]; ?>"/>
					</td>
				</tr>
				<tr>
					<th align="left" valign="top">Number of neighbouring links to the current one: </th>
					<td colspan="2" align="left" valign="top">
						<input type="text" name="vmp_neighbours" id="vmp_neighbours" value="<?php echo $settings[ 4 ]; ?>"/>
					</td>
				</tr>
			</tbody>
			<tfoot>
				<tr>
					<th align="left" valign="top">&nbsp;</th>
					<th colspan="2" align="left" valign="top">
						<input type="submit" name="vmp_submit_options" id="vmp_submit_options" value="Submit" class="button button-primary button-large"/>
					</th>
				</tr>
			</tfoot>
		</table>
	</form>
</div>