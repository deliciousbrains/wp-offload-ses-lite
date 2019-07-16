(function( $, wposesModal ) {

	/**
	 * Show a notice for bulk and row actions.
	 */
	function show_email_action_notice( notice ) {
		if ( 'undefined' === typeof notice ) {
			return false;
		}

		$( '.wposes-notice' ).remove();
		$( '#tab-activity' ).prepend( notice );

		$( '.notice-dismiss' ).on( 'click', function( e ) {
			$( '.wposes-notice' ).remove();
		} );
	}

	var viewEmailPrompt = {

		/**
		 * The element which contains our modal markup.
		 *
		 * {string}
		 */
		modalContainer: '.wposes-view-email-prompt',

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
			wposesModal.open( this.modalContainer, false, 'wposes-view-email-modal' );
		},

		/**
		 * Hide the prompt.
		 */
		hidePrompt: function() {
			wposesModal.setDismissibleState( true );
			wposesModal.close();
		},

		/**
		 * Get more info about the email.
		 */
		getEmail: function( email_id ) {
			$.ajax( {
				url: ajaxurl,
				type: 'POST',
				dataType: 'JSON',
				data: {
					wposes_activity_nonce: $( '#wposes_activity_nonce' ).val(),
					action: 'wposes_get_email',
					email_id: email_id
				},
				error: function( jqXHR, textStatus, errorThrown ) {
					alert( 'error' + errorThrown );
				},
				success: function( response, textStatus, jqXHR ) {
					viewEmailPrompt.showPrompt();
					$( '.wposes-view-email-prompt' ).html( response.data );
				}
			} );
		}

	};

	var wposes_activity_table = {
		/**
		 * Register our triggers
		 *
		 * We want to capture clicks on specific links, but also value change in
		 * the pagination input field. The links contain all the information we
		 * need concerning the wanted page number or ordering, so we'll just
		 * parse the URL to extract these variables.
		 *
		 * The page number input is trickier: it has no URL so we have to find a
		 * way around. We'll use the hidden inputs added in Activity_List_Table::display()
		 * to recover the ordering variables, and the default paged input added
		 * automatically by WordPress.
		 */
		init: function() {
			// This will have its utility when dealing with the page number input
			var timer;
			var delay = 500;
			// Pagination links, sortable link
			$( '#tab-activity .tablenav-pages a, #tab-activity .manage-column.sortable a, #tab-activity .manage-column.sorted a' ).on( 'click', function( e ) {
				// We don't want to actually follow these links
				e.preventDefault();

				// Simple way: use the URL to extract our needed variables
				var query = this.search.substring( 1 );

				var data = {
					paged: wposes_activity_table.__query( query, 'paged' ) || '1',
					order: wposes_activity_table.__query( query, 'order' ) || 'desc',
					orderby: wposes_activity_table.__query( query, 'orderby' ) || 'emails_sent'
				};
				wposes_activity_table.update( data );
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
					paged: parseInt( $( '#tab-activity input[name=paged]' ).val() ) || '1',
					order: $( '#tab-activity input[name=order]' ).val() || 'asc',
					orderby: $( '#tab-activity input[name=orderby]' ).val() || 'title'
				};
				// Now the timer comes to use: we wait half a second after
				// the user stopped typing to actually send the call. If
				// we don't, the keyup event will trigger instantly and
				// thus may cause duplicate calls before sending the intended
				// value
				window.clearTimeout( timer );
				timer = window.setTimeout( function() {
					wposes_activity_table.update( data );
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
						wposes_activity_nonce: $( '#wposes_activity_nonce' ).val(),
						action: 'wposes_activity_table',
						paged: parseInt( $( '#tab-activity input[name=paged]' ).val() ) || '1'
					},
					data,
					wposes_activity_table.get_search_data()
				),
				// Handle the successful result
				success: function( response ) {
					// WP_List_Table::ajax_response() returns json
					var parsed_response = $.parseJSON( response );

					// Add the requested rows
					if ( parsed_response.rows.length ) {
						$( '#tab-activity #the-list' ).html( parsed_response.rows );
					}

					// Update column headers for sorting
					if ( parsed_response.column_headers.length ) {
						$( '#tab-activity .wp-list-table thead tr, #tab-activity .wp-list-table tfoot tr' ).html( parsed_response.column_headers );
					}

					// Update pagination for navigation
					if ( parsed_response.pagination.bottom.length ) {
						$( '#tab-activity .tablenav.top .tablenav-pages' ).html( $( parsed_response.pagination.top ).html() );
					}

					if ( parsed_response.pagination.top.length ) {
						$( '#tab-activity .tablenav.bottom .tablenav-pages' ).html( $( parsed_response.pagination.bottom ).html() );
					}

					if ( parsed_response.views.length ) {
						$( '#wposes-views-wrap' ).html( parsed_response.views );
					}

					if ( parsed_response.bulk_actions_result.length ) {
						show_email_action_notice( parsed_response.bulk_actions_result );
					}

					// Init back our event handlers
					wposes_activity_table.init();
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
		},
		/**
		 * Get user-supplied search data.
		 *
		 * @return object
		 */
		get_search_data: function() {
			var data = {
				'date': $( '#wposes-filter-by-date' ).val(),
				'subject': $( '#wposes-subject-search' ).val(),
				'recipient': $( '#wposes-recipient-search' ).val(),
				'status': $( '.subsubsub a.current' ).data( 'status' ),
				'bulk_action': $( '#bulk-action-selector-top' ).val(),
				'bulk_selected': []
			};

			$( 'input[name="email[]"]:checked' ).each( function() {
				data.bulk_selected.push( $( this ).val() );
			});

			return data;
		}
	};

	// Event handlers.
	$( document ).ready( function() {
		wposes_activity_table.init();

		if ( ! wposes.is_pro ) {
			$( 'body' ).on( 'click', '.row-actions span a', function( e ) {
				e.preventDefault();
				var link = $( this );
				var position = link.position();
				var bubble = $( '.wposes-upgrade-helper' );

				bubble.css( {
					'left': ( ( position.left - bubble.width() / 2 ) - 5 ) + 'px',
					'top': ( position.top + link.height() + 9 ) + 'px'
				} );

				bubble.toggle();
				e.stopPropagation();
			} );
		}

		// Filter based on date/search terms.
		$( '#wposes-activity-form' ).on( 'submit', function( e ) {
			e.preventDefault();
			wposes_activity_table.update( { paged: 1 } );
		} );

		// Filter based on email status.
		$( '#wposes-views-wrap' ).on( 'click', 'a', function( e ) {
			e.preventDefault();
			$( '#wposes-views-wrap a' ).removeClass( 'current' );
			$( this ).addClass( 'current' );
			wposes_activity_table.update( { paged: 1 } );
		} );

		// View an email.
		$( '#tab-activity' ).on( 'click', '.wposes-view-email', {}, function( e ) {
			e.preventDefault();

			if ( ! wposes.is_pro ) {
				return;
			}

			var email = $( this ).data( 'email' );
			viewEmailPrompt.getEmail( email );
		} );

		/**
		 * Process a row action over AJAX.
		 * @param {string} action
		 * @param {int} email_id
		 */
		function ajax_row_action( action, email_id ) {
			if ( ! wposes.is_pro ) {
				return false;
			}

			$.ajax( {
				url: ajaxurl,
				type: 'POST',
				dataType: 'JSON',
				data: {
					wposes_activity_nonce: $( '#wposes_activity_nonce' ).val(),
					action: 'process_row_action',
					email_id: email_id,
					row_action: action
				},
				error: function( jqXHR, textStatus, errorThrown ) {
					alert( 'error' + errorThrown );
				},
				success: function( response, textStatus, jqXHR ) {
					show_email_action_notice( response.data );
					wposes_activity_table.update();
				}
			} );
		}

		// Resend an email.
		$( 'body' ).on( 'click', '.wposes-resend-email', {}, function( e ) {
			e.preventDefault();
			viewEmailPrompt.hidePrompt();
			ajax_row_action( 'resend', $( this ).data( 'email' ) );
		} );

		// Cancel an email.
		$( 'body' ).on( 'click', '.wposes-cancel-email', {},  function( e ) {
			e.preventDefault();
			viewEmailPrompt.hidePrompt();
			ajax_row_action( 'cancel', $( this ).data( 'email' ) );
		} );

		// Delete an email.
		$( 'body' ).on( 'click', '.wposes-delete-email', {},  function( e ) {
			e.preventDefault();
			viewEmailPrompt.hidePrompt();
			ajax_row_action( 'delete', $( this ).data( 'email' ) );
		} );

	} );

})( jQuery, wposesModal );
