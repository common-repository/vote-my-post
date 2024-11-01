jQuery( document ).ready( function(){
	// ajax post the set style form
	jQuery( '#set_style_button' ).bind( 'click', function( event ){
		event.preventDefault();
		
		jQuery( '#vmp_message_panel' ).slideUp( 'slow' ).delay(  1000 );
		
		// post ajax request
		jQuery.ajax( {
			url:jQuery( '#set_style_form' ).attr( 'action' ),
			method:'post',
			dataType:'json',
			data:jQuery( '#set_style_form' ).serialize(),
			success:function( response ){
				jQuery( '#vmp_message_panel' ).html( response.html );
			}
		} );
		
		jQuery( '#vmp_message_panel' ).slideDown( 'slow' );
	} );
	
	// ajax set voting mode for a single post
	jQuery( '.set_voting_mode' ).bind( 'click', function( event ){
		event.preventDefault();
		
		jQuery( '#vmp_message_panel' ).slideUp( 'slow' ).delay(  1000 );
		
		// split the clicked element's id
		var id = jQuery( this ).attr( 'id' );
		var id_parts = id.split( '_' );
		var index = id_parts[ 3 ];
		
		// get the post id
		var this_post_id = jQuery( '#this_post_id_' + index ).val();
		
		// get the post voting mode
		var post_voting_mode = jQuery( '#post_voting_mode_' + index ).val();
		
		// get ajax request
		jQuery.ajax({
			url:ajax_object.ajax_url,
			method:'get',
			dataType:'json',
			data:{
				action:'set_this_post_voting_mode_090813',
				post_id:this_post_id,
				post_voting_mode:post_voting_mode
			},
			success:function( response ){
				jQuery( '#vmp_message_panel' ).html( response.html );
				jQuery( '#select_all_post' ).removeAttr( 'checked' ); // reset the all post selector to default ( unchecked )
				jQuery( '.post_selector' ).removeAttr( 'checked' ); // reset individual post selectors to default ( unchecked )
			}
		});
		
		jQuery( '#vmp_message_panel' ).slideDown( 'slow' );	
	} );
	
	// select all posts at a go
	jQuery( '#select_all_post' ).bind( 'click', function( event ){
		jQuery( '.post_selector' ).trigger( 'click' );
	} );
	
	// ajax post the set voting mode form
	jQuery( '#group_op' ).bind( 'change', function( event ){		
		if ( jQuery( '#group_op' ).val() != 0 ){
			jQuery( '#vmp_message_panel' ).slideUp( 'slow' ).delay(  1000 ); // slide the message panel up and add an arbitrary delay
			// post ajax request
			jQuery.ajax( {
				url:jQuery( '#multiple_post_voting_mode_toggle_form' ).attr( 'action' ),
				method:'post',
				dataType:'json',
				data:jQuery( '#multiple_post_voting_mode_toggle_form' ).serialize(),
				success:function( response ){
					jQuery( '#vmp_message_panel' ).html( response.html );
					jQuery( '#group_op' ).attr( 'value', 0 ); // reset the select box to default
					jQuery( '#select_all_post' ).removeAttr( 'checked' ); // reset the all post selector to default ( unchecked )
					jQuery( '.post_selector' ).removeAttr( 'checked' ); // reset individual post selectors to default ( unchecked )
				}
			} );
			
			jQuery( '#vmp_message_panel' ).slideDown( 'slow' );
		}
	} );
	
	// ajax get the upvote click
	jQuery( '#up_count' ).bind( 'click', function(){		
		jQuery( '#vmp_message' ).empty();
		
		jQuery.ajax( {
			url:ajax_object.ajax_url,
			method:'get',
			dataType:'json',
			data:{
				action:'count_vote_090813',
				post_id:ajax_object.post_id,
				type:'upvote'
			},
			success:function( response ){
				if ( 'error' == response.type ){
					jQuery( '#vmp_message' ).html( response.html );	
				}else if( 'success' == response.type ){
					jQuery( '#vmp_up_counter' ).html( response.html );
				}else{
					
				}
			}	
		} );
	} );
	
	// ajax get the downvote click
	jQuery( '#down_count' ).bind( 'click', function(){
		jQuery( '#vmp_message' ).empty();
		
		jQuery.ajax( {
			url:ajax_object.ajax_url,
			method:'get',
			dataType:'json',
			data:{
				action:'count_vote_090813',
				post_id:ajax_object.post_id,
				type:'downvote'
			},
			success:function( response ){
				if ( 'error' == response.type ){
					jQuery( '#vmp_message' ).html( response.html );
				}else if( 'success' == response.type ){
					jQuery( '#vmp_down_counter' ).html( response.html );
				}else{
					
				}
			}	
		} );
	} );
	
	// ajax reset vote counter for a single post
	jQuery( '.reset_post_votes_table' ).on( 'click', '.reset_post_vote_counter', function( event ){
		event.preventDefault();
		
		var flag = false;
		
		jQuery( '#vmp_message_panel' ).slideUp( 'slow' ).delay(  1000 );
		
		// get the parent row id
		var parent_row_id = jQuery( jQuery( this ).parent() ).parent().attr( 'id' );
		
		// get the post ID
		var parent_row_id_parts = parent_row_id.split( '_' );
		var id = parent_row_id_parts[ 1 ];
		
		// get ajax request
		jQuery.ajax({
			url:ajax_object.ajax_url,
			method:'get',
			dataType:'json',
			data:{
				action:'reset_this_post_vote_counter_092113',
				id:id
			},
			success:function( response ){
				if ( 'error' == response.type ){
					flag = true;
					jQuery( '#vmp_message_panel' ).html( response.html );
				}else{
					jQuery( '#' + parent_row_id ).empty();
					jQuery( '#' + parent_row_id ).html( response.html );
				}
				
				jQuery( '#select_all_post' ).removeAttr( 'checked' ); // reset the all post selector to default ( unchecked )
				jQuery( '.post_selector' ).removeAttr( 'checked' ); // reset individual post selectors to default ( unchecked )
			}
		});
		
		if ( true == flag ){
			jQuery( '#vmp_message_panel' ).slideDown( 'slow' );
		}
	} );
	
	// submit multiple selected posts to reset their vote counters
	jQuery( '#group_op_alt' ).bind( 'change', function( event ) {
		if ( jQuery( '#group_op_alt' ).val() != 0 ){
			jQuery( '#vmp_message_panel_alt' ).slideUp( 'slow' );
			jQuery( '#reset_multiple_post_vote_counters_form' ).submit();	
		}
	} );
	
	jQuery( '#vmp_message_panel_alt' ).slideDown( 'slow' );
} );