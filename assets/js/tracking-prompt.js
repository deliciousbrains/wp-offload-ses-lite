(function( $, wposesModal ) {

	var trackingPrompt = {

		/**
		 * The element which contains our modal markup.
		 *
		 * {string}
		 */
		modalContainer: '.wposes-tracking-prompt',

		/**
		 * Whether or not the modal is currently open.
		 *
		 * {bool}
		 */
		modalOpen: false,

		/**
		 * Whether or not the modal has already been shown.
		 *
		 * {bool}
		 */
		modalShown: false,

		/**
		 * Show the prompt.
		 */
		showPrompt: function() {
			wposesModal.setDismissibleState( true );
			wposesModal.open( this.modalContainer, false, 'wposes-tracking-modal' );
		},

		/**
		 * Hide the prompt.
		 */
		hidePrompt: function() {
			wposesModal.setDismissibleState( true );
			wposesModal.close();
		}

	};

	// Event Handlers
	$( document ).ready( function() {

		$( '#wposes-enable-open-tracking-wrap' ).on( 'click', function( e ) {
			if ( $( this ).hasClass( 'on' ) && ! trackingPrompt.modalShown ) {
				trackingPrompt.showPrompt();
				trackingPrompt.modalShown = true;
			}
		} );

		$( '#wposes-enable-click-tracking-wrap' ).on( 'click', function( e ) {
			if ( $( this ).hasClass( 'on' ) && ! trackingPrompt.modalShown ) {
				trackingPrompt.showPrompt();
				trackingPrompt.modalShown = true;
			}
		} );

	} );

})( jQuery, wposesModal );
