(function( $ ) {

	var wposes_report_table = {
		/**
		 * Register our triggers
		 *
		 * We want to capture clicks on specific links, but also value change in
		 * the pagination input field. The links contain all the information we
		 * need concerning the wanted page number or ordering, so we'll just
		 * parse the URL to extract these variables.
		 *
		 * The page number input is trickier: it has no URL so we have to find a
		 * way around. We'll use the hidden inputs added in Reports_List_Table::display()
		 * to recover the ordering variables, and the default paged input added
		 * automatically by WordPress.
		 */
		init: function() {
			// This will have its utility when dealing with the page number input
			var timer;
			var delay = 500;
			// Pagination links, sortable link
			$( '#tab-reports .tablenav-pages a, #tab-reports .manage-column.sortable a, #tab-reports .manage-column.sorted a' ).on( 'click', function( e ) {
				// We don't want to actually follow these links
				e.preventDefault();

				// Simple way: use the URL to extract our needed variables
				var query = this.search.substring( 1 );

				var data = {
					paged: wposes_report_table.__query( query, 'paged' ) || '1',
					order: wposes_report_table.__query( query, 'order' ) || 'desc',
					orderby: wposes_report_table.__query( query, 'orderby' ) || 'emails_sent'
				};
				wposes_report_table.update( data );
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
					orderby: $( 'input[name=orderby]' ).val() || 'title'
				};
				// Now the timer comes to use: we wait half a second after
				// the user stopped typing to actually send the call. If
				// we don't, the keyup event will trigger instantly and
				// thus may cause duplicate calls before sending the intended
				// value
				window.clearTimeout( timer );
				timer = window.setTimeout( function() {
					wposes_report_table.update( data );
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
						wposes_reports_nonce: $( '#wposes_reports_nonce' ).val(),
						action: 'wposes_reports_table'
					},
					data
				),
				// Handle the successful result
				success: function( response ) {
					// WP_List_Table::ajax_response() returns json
					var parsed_response = $.parseJSON( response );

					// Add the requested rows
					if ( parsed_response.rows.length ) {
						$( '#tab-reports #the-list' ).html( parsed_response.rows );
					}

					// Update column headers for sorting
					if ( parsed_response.column_headers.length ) {
						$( '#tab-reports .wp-list-table thead tr, #tab-reports .wp-list-table tfoot tr' ).html( parsed_response.column_headers );
					}

					// Update pagination for navigation
					if ( parsed_response.pagination.bottom.length ) {
						$( '#tab-reports .tablenav.top .tablenav-pages' ).html( $( parsed_response.pagination.top ).html() );
					}

					if ( parsed_response.pagination.top.length ) {
						$( '#tab-reports .tablenav.bottom .tablenav-pages' ).html( $( parsed_response.pagination.bottom ).html() );
					}

					// Init back our event handlers
					wposes_report_table.init();
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

	var wposes_reports_from = $( '#wposes-reports-from' ).datepicker( {
		changeMonth: true,
		numberOfMonths: 1
	} );

	var wposes_reports_to = $( '#wposes-reports-to' ).datepicker( {
		changeMonth: true,
		numberOfMonths: 1,
		maxDate: 0
	} );

	function getDate( element ) {
		var date;
		var dateFormat = $.datepicker._defaults.dateFormat;

		try {
			date = $.datepicker.parseDate( dateFormat, element.value );
		} catch ( error ) {
			date = null;
		}

		return date;
	}

	function get_search_data() {
		var data = {
			'search': $( '#wposes-reports-search-search-input' ).val(),
			'from': $( '#wposes-reports-from' ).val(),
			'to': $( '#wposes-reports-to' ).val()
		};

		return data;
	}

	// Event handlers.
	$( document ).ready( function() {
		wposes_report_table.init();

		wposes_reports_from.on( 'change', function() {
			wposes_reports_to.datepicker( 'option', 'minDate', getDate( this ) );
			wposes_report_table.update( get_search_data() );
		} );

		wposes_reports_to.on( 'change', function() {
			wposes_reports_from.datepicker( 'option', 'maxDate', getDate( this ) );
			wposes_report_table.update( get_search_data() );
		} );

		$( '#wposes-reports-form' ).on( 'submit', function( e ) {
			e.preventDefault();
			wposes_report_table.update( get_search_data() );
		} );

	} );

})( jQuery );
