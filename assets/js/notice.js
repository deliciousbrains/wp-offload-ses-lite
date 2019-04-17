(function( $ ) {

	var $body = $( 'body' );

	$body.on( 'click', '.wposes-notice .notice-dismiss', function( e ) {
		var id = $( this ).parents( '.wposes-notice' ).attr( 'id' );
		if ( id ) {
			var data = {
				action: 'wposes-dismiss-notice',
				notice_id: id,
				_nonce: wposes_notice.nonces.dismiss_notice
			};

			$.ajax( {
				url: ajaxurl,
				type: 'POST',
				dataType: 'JSON',
				data: data,
				error: function( jqXHR, textStatus, errorThrown ) {
					alert( wposes_notice.strings.dismiss_notice_error + errorThrown );
				}
			} );
		}
	} );

	$body.on( 'click', '.wposes-notice-toggle', function( e ) {
		e.preventDefault();
		var $link = $( this );
		var label = $link.data( 'hide' );

		$link.data( 'hide', $link.html() );
		$link.html( label );

		$link.closest( '.wposes-notice' ).find( '.wposes-notice-toggle-content' ).toggle();
	} );

})( jQuery );
