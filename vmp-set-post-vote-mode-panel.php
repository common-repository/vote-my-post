<div id="vmp_message_panel" align="center" style="display:none;">
</div>
<div class="main_panel" align="center">
	<?php
	$returned = vmp_fetch_all_posts();
	$posts = $returned[ 'posts' ];
	$total_page_links = $returned[ 'total_page_links' ];
	
	if ( is_array( $posts ) && ! empty( $posts ) ){
	?>
	<table class="wp-list-table widefat" cellspacing="0">
		<thead>
			<tr>
				<th colspan="4" align="lef" valign="top"><center>Set a voting mode for each of your posts here</center></th>
			</tr>
			<tr>
				<th width="25%" align="left" valign="top" style="padding-left:0px; margin-left:0px;"><input type="checkbox" name="select_all_post" id="select_all_post"/></th>
				<th width="25%" align="left" valign="top">Post Name</th>
				<th width="25%" align="left" valign="top">Voting Mode</th>
				<th width="25%" align="left" valign="top">Operation</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<th colspan="2" align="left" valign="top">
					<select name="group_op" id="group_op">
						<option value="0">-Select-</option>
						<option value="1">Set Mode</option>
					</select>
				</th>
				<th colspan="2" align="center" valign="top">
					<table>
						<tr>
							<?php 
							if ( 1 < $total_page_links ){
								vmp_display_page_links( $total_page_links, 'set_post_voting_mode' ); 	
							}
							?>
						</tr>
					</table>
				</th>
			</tr>
		</tfoot>
		<tbody>
			<?php
				$_wpnonce = wp_create_nonce( 'set_multiple_post_voting_mode' );
				$action = admin_url( 'admin-ajax.php?action=set_multiple_post_voting_mode_090813&_wpnonce=' . $_wpnonce );
			?>
			<form name="multiple_post_voting_mode_toggle_form" id="multiple_post_voting_mode_toggle_form" action="<?php echo $action; ?>">
				<?php
					$count = 1;
					foreach ( $posts as $post ){
						$vote_mode = vmp_fetch_existing_voting_mode( $post[ 'ID' ] );
						
						if ( 0 == $vote_mode ){
							$selected_1 = 'selected';
							$selected_2 = '';
						}else if ( 1 == $vote_mode ){
							$selected_1 = '';
							$selected_2 = 'selected';
						}else if ( FALSE === $vote_mode ){
							$selected_1 = '';
							$selected_2 = '';
						}else{
							
						}
				?>
				<tr>
					<td width="25%" align="left" valign="top"><input type="checkbox" name="post_selector_<?php echo $count; ?>" class="post_selector"/></td>
					<td width="25%" align="left" valign="top"><?php echo $post[ 'post_title' ]; ?></td>
					<td width="25%" align="left" valign="top">
						<select name="post_voting_modes[]" id="post_voting_mode_<?php echo $count; ?>">
							<option value="0" <?php echo $selected_1; ?>>Open</option>
							<option value="1" <?php echo $selected_2; ?>>Restricted</option>
						</select>
					</td>
					<td width="25%" align="left" valign="top">
						<input type="hidden" name="post_ids[]" id="this_post_id_<?php echo $count; ?>" value="<?php echo $post[ 'ID' ]; ?>"/>
						<input type="submit" name="set_voting_mode_<?php echo $count; ?>" id="set_voting_mode_<?php echo $count; ?>" Value="Set Mode" class="button button-primary button-large set_voting_mode"/>
					</td>
				</tr>
				<?php
						$count++;
					}
				?>
			</form>
		</tbody>
	</table>
	<?php
	}else{
	?>
	<table class="wp-list-table widefat" cellspacing="0">
		<tr><th align="center" valign="top"><center>No posts found on your site</center></th></tr>
	</table>
	<?php
	}	
	?>
</div>