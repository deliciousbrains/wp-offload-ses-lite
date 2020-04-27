(function( $, wposesModal ) {

	var healthReportPrompt = {

		/**
		 * The element which contains our modal markup.
		 *
		 * {string}
		 */
		modalContainer: '.wposes-health-report-prompt',

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
			wposesModal.open( this.modalContainer, false, 'wposes-health-report-modal' );
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
		$( '#wposes-settings-form' ).submit( function( e ) {
			var health_report_enabled = $( '#wposes-enable-health-report-wrap' ).hasClass( 'on' ) ? true : false;
			var health_report_frequency = $( 'select[name="health-report-frequency"]' ).val();
			var health_report_recipients = $( 'select[name="health-report-recipients"]' ).val();

			if ( ! wposes.is_pro && health_report_enabled ) {
				if ( 'daily' === health_report_frequency ) {
					e.preventDefault();
					healthReportPrompt.showPrompt();
					$( '.wposes-upgrade-custom-recipients' ).hide();
					$( '.wposes-upgrade-daily-reports' ).show();
				} else if ( 'custom' === health_report_recipients ) {
					e.preventDefault();
					healthReportPrompt.showPrompt();
					$( '.wposes-upgrade-daily-reports' ).hide();
					$( '.wposes-upgrade-custom-recipients' ).show();
				}
			}
		} );
	} );

})( jQuery, wposesModal );
