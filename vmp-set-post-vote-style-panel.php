<div id="vmp_message_panel" align="center" style="display:none;"></div>
<div class="main_panel" align="center">
	<table class="wp-list-table widefat" cellspacing="0">
		<thead>
			<tr>
				<th colspan="2" align="center" valign="top"><center>Set the positioning and orientation of the voting links here</center></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th colspan="2" align="center" valign="top">&nbsp;</th>
			</tr>
		</tfoot>
		<tbody>
			<?php
				$latest_style = vmp_fetch_latest_post_voting_link_style();
				
				if ( FALSE === $latest_style ){
					$positioning_1 = '';
					$positioning_2 = '';
					$orientation_1 = '';
					$orientation_2 = '';
				}else{
					if ( 0 == $latest_style[ 'positioning' ] ){
						$positioning_1 = 'checked';
						$positioning_2 = '';
					}else{
						$positioning_1 = '';
						$positioning_2 = 'checked';
					}
					
					if ( 0 == $latest_style[ 'orientation' ] ){
						$orientation_1 = 'checked';
						$orientation_2 = '';
					}else{
						$orientation_1 = '';
						$orientation_2 = 'checked';
					}				
				}
				
				$_wpnonce = wp_create_nonce( 'set_post_voting_link_position_and_orientation' );
				$action = admin_url( 'admin-ajax.php?action=set_post_voting_link_position_and_orientation_090713&_wpnonce=' . $_wpnonce );
			?>
			<form name="set_style_form" id="set_style_form" action="<?php echo $action; ?>">
				<tr>
					<th width="50%">Position</th>
					<td width="50%" align="left" valign="top">
						<input type="radio" name="post_voting_link_position" id="post_voting_link_position_1" value="0" <?php echo $positioning_1; ?>/>&nbsp;&nbsp;<strong>Before Post Content</strong>&nbsp;&nbsp;&nbsp;&nbsp;
						<input type="radio" name="post_voting_link_position" id="post_voting_link_position_2" value="1" <?php echo $positioning_2; ?>/>&nbsp;&nbsp;<strong>After Post Content</strong>&nbsp;&nbsp;&nbsp;&nbsp;
					</td>
				</tr>
				<tr>
					<th width="50%">Orientation</th>
					<td width="50%" align="left" valign="top">
						<input type="radio" name="post_voting_link_orientation" id="post_voting_link_orientation_1" value="0" <?php echo $orientation_1; ?>/>&nbsp;&nbsp;<strong>Left</strong>&nbsp;&nbsp;&nbsp;&nbsp;
						<input type="radio" name="post_voting_link_orientation" id="post_voting_link_orientation_2" value="1" <?php echo $orientation_2; ?>/>&nbsp;&nbsp;<strong>Right</strong>&nbsp;&nbsp;&nbsp;&nbsp;
					</td>
				</tr>
				<tr>
					<td colspan="2" align="center" valign="top">
						<input type="submit" name="set_style_button" id="set_style_button" value="Set Positioning And Orientation" title="Click to set voting links positioning and orientation" class="button button-primary button-large"/>
					</td>
				</tr>
			</form>
		</tbody>
	</table>
</div>