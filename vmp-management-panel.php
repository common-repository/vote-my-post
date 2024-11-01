<?php
	if ( isset( $_SESSION[ 'messages' ] ) && ! empty( $_SESSION[ 'messages' ] ) ){
?>
<div id="vmp_message_panel_alt" align="center" style="display:none;">
<?php echo $_SESSION[ 'messages' ]; unset( $_SESSION[ 'messages' ] ); ?>
</div>
<?php
	}
?>
<div id="vmp_message_panel" align="center" style="display:none;">
</div>
<div class="main_panel" align="center">
	<?php
	$returned = vmp_fetch_all_voted_posts();
	$posts = $returned[ 'posts' ]; 
	
	$total_page_links = $returned[ 'total_page_links' ];
	
	if ( is_array( $posts ) && ! empty( $posts ) ){
		$_wpnonce = wp_create_nonce( 'reset_multiple_post_vote_counters' );
		$action = admin_url( 'admin.php?action=reset_multiple_post_vote_counters_092113&_wpnonce=' . $_wpnonce );
	?>
	<form name="reset_multiple_post_vote_counters_form" id="reset_multiple_post_vote_counters_form" action="<?php echo $action; ?>" method="post">
		<table class="wp-list-table widefat reset_post_votes_table" cellspacing="0">
			<thead>
				<tr>
					<th colspan="5" align="lef" valign="top"><center>Reset vote counters for each of your posts here</center></th>
				</tr>
				<tr>
					<th width="20%" align="left" valign="top" style="padding-left:0px; margin-left:0px;"><input type="checkbox" name="select_all_post" id="select_all_post"/></th>
					<th width="20%" align="left" valign="top">Post Title</th>
					<th width="20%" align="left" valign="top">Upvote Count</th>
					<th width="20%" align="left" valign="top">Downvote Count</th>
					<th width="20%" align="left" valign="top">Operation</th>
				</tr>
			</thead>
			<tfoot>
				<tr>
					<th colspan="2" align="left" valign="top">
						<select name="group_op" id="group_op_alt">
							<option value="0">-Select-</option>
							<option value="1">Reset Counter</option>
						</select>
					</th>
					<th colspan="3" align="center" valign="top">
						<table>
							<tr>
								<?php 
								if ( 1 < $total_page_links ){
									vmp_display_page_links( $total_page_links, 'reset_post_counters' ); 	
								}
								?>
							</tr>
						</table>
					</th>
				</tr>
			</tfoot>
			<tbody>			
				<?php
					foreach ( $posts as $post ){
				?>
				<tr id="row_<?php echo $post[ 'post_id' ]; ?>">
					<td width="20%" align="left" valign="top">
						<input type="checkbox" name="post_selectors[]" class="post_selector"/>
						<input type="hidden" name="ids[]" value="<?php echo $post[ 'id' ]; ?>"/>
						<input type="hidden" name="post_ids[]" value="<?php echo $post[ 'post_id' ]; ?>"/>
					</td>
					<td width="20%" align="left" valign="top"><?php echo $post[ 'post_title' ]; ?></td>
					<td width="20%" align="left" valign="top"><?php echo $post[ 'upvote_count' ]; ?></td>
					<td width="20%" align="left" valign="top"><?php echo $post[ 'downvote_count' ]; ?></td>
					<td width="20%" align="left" valign="top">
						<a href="javascript:void(0);" class="button button-primary button-large reset_post_vote_counter">Reset</a>
					</td>
				</tr>
				<?php
					}
				?>			
			</tbody>
		</table>
	</form>
	<?php
	}else{
	?>
	<table class="wp-list-table widefat" cellspacing="0">
		<tr><th align="center" valign="top"><center>No posts have been voted yet</center></th></tr>
	</table>
	<?php
	}	
	?>
</div>