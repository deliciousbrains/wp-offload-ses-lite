(function( $, wposesModal ) {

	wposes.Tools = wposes.Tools ? wposes.Tools : {};

	var wposes_verified_senders = {
		/**
		 * Register our triggers
		 *
		 * We want to capture clicks on specific links, but also value change in
		 * the pagination input field. The links contain all the information we
		 * need concerning the wanted page number or ordering, so we'll just
		 * parse the URL to extract these variables.
		 *
		 * The page number input is trickier: it has no URL so we have to find a
		 * way around. We'll use the hidden inputs added in TT_Example_List_Table::display()
		 * to recover the ordering variables, and the default paged input added
		 * automatically by WordPress.
		 */
		init: function() {
			// This will have its utility when dealing with the page number input
			var timer;
			var delay = 500;
			// Pagination links, sortable link
			$( '#tab-verified-senders .tablenav-pages a, #tab-verified-senders .manage-column.sortable a, #tab-verified-senders .manage-column.sorted a' ).on( 'click', function( e ) {
				// We don't want to actually follow these links
				e.preventDefault();

				// Simple way: use the URL to extract our needed variables
				var query = this.search.substring( 1 );

				var data = {
					paged: wposes_verified_senders.__query( query, 'paged' ) || '1',
					order: wposes_verified_senders.__query( query, 'order' ) || 'asc',
					orderby: wposes_verified_senders.__query( query, 'orderby' ) || 'sender'
				};
				wposes_verified_senders.update( data );
			});
			// Page number input
			$( 'input[name=paged]' ).on( 'keyup', function( e ) {
				// If user hit enter, we don't want to submit the form
				// We don't preventDefault() for all keys because it would
				// also prevent to get the page number!
				if ( 13 === e.which ) {
					e.preventDefault();
				}

				// This time we fetch the variables in inputs
				var data = {
					paged: parseInt( $( 'input[name=paged]' ).val() ) || '1',
					order: $( 'input[name=order]' ).val() || 'asc',
					orderby: $( 'input[name=orderby]' ).val() || 'sender'
				};
				// Now the timer comes to use: we wait half a second after
				// the user stopped typing to actually send the call. If
				// we don't, the keyup event will trigger instantly and
				// thus may cause duplicate calls before sending the intended
				// value
				window.clearTimeout( timer );
				timer = window.setTimeout( function() {
					wposes_verified_senders.update( data );
				}, delay );
			});
		},
		/**
		 * AJAX call
		 *
		 * Send the call and replace table parts with updated version!
		 *
		 * @param	object	data The data to pass through AJAX
		 */
		update: function( data ) {
			$.ajax({
				// /wp-admin/admin-ajax.php
				url: ajaxurl,
				// Add action and nonce to our collected data
				data: $.extend(
					{
						wposes_verified_senders_nonce: $( '#wposes_verified_senders_nonce' ).val(),
						action: 'wposes_get_verified_senders_list'
					},
					data
				),
				// Handle the successful result
				success: function( response ) {
					// WP_List_Table::ajax_response() returns json
					var parsed_response = {};

					try {
						parsed_response = JSON.parse( response );
					} catch ( error ) {
						console.log( error );
					}

					// Add the requested rows
					if ( parsed_response.rows.length ) {
						$( '#tab-verified-senders #the-list' ).html( parsed_response.rows );
					}

					// Update column headers for sorting
					if ( parsed_response.column_headers.length ) {
						$( '#tab-verified-senders .wp-list-table thead tr, #tab-verified-senders .wp-list-table tfoot tr' ).html( parsed_response.column_headers );
					}

					// Update pagination for navigation
					if ( parsed_response.pagination.bottom.length ) {
						$( '#tab-verified-senders .tablenav.top .tablenav-pages' ).html( $( parsed_response.pagination.top ).html() );
					}

					if ( parsed_response.pagination.top.length ) {
						$( '#tab-verified-senders .tablenav.bottom .tablenav-pages' ).html( $( parsed_response.pagination.bottom ).html() );
					}

					// Init back our event handlers
					wposes_verified_senders.init();
				}
			});
		},
		/**
		 * Filter the URL Query to extract variables
		 *
		 * @see http://css-tricks.com/snippets/javascript/get-url-variables/
		 *
		 * @param    string    query The URL query part containing the variables
		 * @param    string    variable Name of the variable we want to get
		 *
		 * @return   string|boolean The variable value if available, false else.
		 */
		__query: function( query, variable ) {
			var vars = query.split( '&' );
			for ( var i = 0; i < vars.length; i++ ) {
				var pair = vars[ i ].split( '=' );

				if ( pair[0] === variable ) {
					return pair[1];
				}
			}
			return false;
		}
	};

	/**
	 * The object that handles the verify senders modal.
	 */
	wposes.Tools.VerifySender = {

		/**
		 * The element which contains our modal markup.
		 *
		 * {string}
		 */
		modalContainer: '.wposes-verify-sender-prompt',

		/**
		 * Whether or not the modal is currently open.
		 *
		 * {bool}
		 */
		modalOpen: false,

		/**
		 * Show the verify sender prompt.
		 */
		showPrompt: function() {
			wposesModal.setDismissibleState( true );
			wposesModal.open( this.modalContainer );
		},

		/**
		 * Hide the verify sender prompt.
		 */
		hidePrompt: function() {
			wposesModal.setDismissibleState( true );
			wposesModal.close();
		},

		/**
		 * Handle prompt response.
		 */
		handlePromptResponse: function() {
			var sender_field = $( '#wposes-verify-domain' );
			var sender_type = $( 'input[name="sender-type"]:checked' ).val();

			if ( 'email' === sender_type ) {
				sender_field = $( '#wposes-verify-email' );
			}

			if ( ! sender_field[0].checkValidity() ) {
				sender_field[0].reportValidity();
				return;
			}

			var sender = sender_field.val();

			wposes.Tools.VerifySender.sendVerificationRequest( sender, sender_type );
		},

		/**
		 * Send a verification request to Amazon.
		 *
		 * @param string sender
		 * @param string sender_type
		 */
		sendVerificationRequest: function( sender, sender_type ) {
			$.ajax( {
				url: ajaxurl,
				type: 'POST',
				dataType: 'JSON',
				data: {
					_nonce: wposes.nonces.wposes_verify_sender,
					action: 'wposes-verify-sender',
					sender: sender,
					sender_type: sender_type
				},
				error: function( jqXHR, textStatus, errorThrown ) {
					alert( 'error' + errorThrown );
				},
				success: function( data, textStatus, jqXHR ) {
					wposes.Tools.VerifySender.handleVerificationResponse( sender, sender_type, data );
				}
			} );
		},

		/**
		 * Handle the response to the verification request.
		 *
		 * @param string sender
		 * @param string sender_type
		 * @param object response
		 */
		handleVerificationResponse: function( sender, sender_type, response ) {
			wposes_verified_senders.update();

			// Open the modal if not already
			if ( ! wposes.Tools.VerifySender.modalOpen ) {
				wposes.Tools.VerifySender.showPrompt();
			}

			if ( response.errors ) {
				$( '.wposes-verification-errors' ).html( Object.values( response.errors ).join( ' ' ) ).show();
			} else if ( 'domain' === sender_type ) {
				$( '#wposes-dns-name' ).html( '_amazonses.' + sender );
				$( '#wposes-dns-value' ).html( response.VerificationToken );
				$( '#wposes-verified-sender-form' ).removeClass().addClass( 'wposes-update-dns' );
			} else {
				$( '#wposes-verified-sender-form' ).removeClass().addClass( 'wposes-confirm-email' );
			}

			$( '.wposes-sender' ).html( sender );
		},

		/**
		 * Show the delete sender confirmation prompt.
		 *
		 * @param string sender
		 */
		deleteSenderPrompt: function( sender ) {
			if ( ! wposes.Tools.VerifySender.modalOpen ) {
				wposes.Tools.VerifySender.showPrompt();
				$( '#wposes-verified-sender-form' ).removeClass().addClass( 'wposes-delete-sender' );
			}

			$( '.wposes-sender' ).html( sender );
		},

		/**
		 * Delete the sender from SES.
		 */
		deleteSender: function() {
			var sender = $( '#wposes-delete-sender .wposes-sender' ).text();

			$.ajax( {
				url: ajaxurl,
				type: 'POST',
				dataType: 'JSON',
				data: {
					wposes_verified_senders_nonce: $( '#wposes_verified_senders_nonce' ).val(),
					action: 'wposes_delete_sender',
					sender: sender
				},
				error: function( jqXHR, textStatus, errorThrown ) {
					alert( 'error' + errorThrown );
				},
				success: function( data, textStatus, jqXHR ) {
					wposes.Tools.VerifySender.hidePrompt();
					wposes_verified_senders.update();
				}
			} );
		},

		/**
		 * Reset prompt back to the default values.
		 */
		resetPrompt: function() {
			$( '#wposes-verified-sender-form' ).removeClass().addClass( 'wposes-add-sender' );
			$( '#wposes-verify-domain, #wposes-verify-email' ).val( '' );
			$( '#wposes-domain' ).prop( 'checked', true );
			$( '.wposes-show-email, .wposes-verification-errors' ).hide();
			$( '.wposes-show-domain' ).show();
		}

	};

	// Event Handlers
	$( document ).ready( function() {
		// Initiate the verified senders table
		wposes_verified_senders.init();

		// Refresh the verified senders table
		$( 'body' ).on( 'click', '#wposes-refresh-verified-senders', function() {
			$( this ).addClass( 'rotate' ).on( 'transitionend', function() {
				$( this ).removeClass( 'rotate' );
			} );
			wposes_verified_senders.update();
		} );

		// Open the Add New Sender Modal
		$( 'body' ).on( 'click', '#wposes-add-new-verified-sender', function( event ) {
			event.preventDefault();
			wposes.Tools.VerifySender.showPrompt();
		} );

		// Launch the delete sender modal
		$( 'body' ).on( 'click', '.wposes-remove-sender', function( event ) {
			event.preventDefault();

			var sender = $( this ).data( 'sender' );
			wposes.Tools.VerifySender.deleteSenderPrompt( sender );
		} );

		// View the necessary DNS record for a domain
		$( 'body' ).on( 'click', '.wposes-view-dns', function( event ) {
			event.preventDefault();

			var sender = $( this ).data( 'sender' );
			var token = $( this ).data( 'token' );

			wposes.Tools.VerifySender.handleVerificationResponse( sender, 'domain', { 'VerificationToken': token } );
		} );

		// Resend the verification request for an email address
		$( 'body' ).on( 'click', '.wposes-resend-verification', function( event ) {
			event.preventDefault();

			var sender = $( this ).data( 'sender' );
			wposes.Tools.VerifySender.sendVerificationRequest( sender, 'email' );
		} );

		$( 'body' ).on( 'wposes-modal-open', function( event ) {
			wposes.Tools.VerifySender.modalOpen = true;

			// For copy-able text
			if ( 'function' === typeof document.execCommand ) {
				$( '#wposes-modal' ).addClass( 'wposes-click-to-copy' )
					.on( 'click', '[data-wposes-copy]', function() {
						wposes.selectText( this );
						document.execCommand( 'copy' );
						window.getSelection().removeAllRanges();
					} )
				;
			}

			// Listen for prompt response
			$( '#wposes-verify-sender-btn' ).on( 'click', function( event ) {
				event.preventDefault();
				wposes.Tools.VerifySender.handlePromptResponse();
			} );

			// Listen for delete sender confirmation
			$( '#wposes-delete-sender-btn' ).on( 'click', function( event ) {
				event.preventDefault();
				wposes.Tools.VerifySender.deleteSender();
			} );

			// Listen for sender type toggles
			$( '#wposes-verified-sender-form input[type="radio"]' ).on( 'change', function() {
				if ( 'wposes-email' === this.id ) {
					$( '.wposes-show-domain, .wposes-verification-errors' ).hide();
					$( '.wposes-show-email' ).show();
				} else {
					$( '.wposes-show-email, .wposes-verification-errors' ).hide();
					$( '.wposes-show-domain' ).show();
				}
			} );
		} );

		$( 'body' ).on( 'wposes-modal-close', function() {
			wposes.Tools.VerifySender.modalOpen = false;
			setTimeout( function() {
				wposes.Tools.VerifySender.resetPrompt();
			}, 151 );
		} );

	} );

})( jQuery, wposesModal );
