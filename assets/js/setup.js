( function( $ ) {

		function verify_identity( sender, sender_type ) {
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
					if ( 'undefined' !== typeof data.errors ) {
						$( '.wposes-verification-errors' ).show();
						return;
					}

					wposes.verified_senders.push( sender );

					if ( 'domain' === sender_type ) {
						$( '.wposes-domain-dns' ).html(
							'<tr>' +
								'<th>Type</th>' +
								'<th>Name</th>' +
								'<th>Value</th>' +
							'<tr>' +
								'<td>TXT</td>' +
								'<td><code data-wposes-copy>_amazonses.' + sender + '</code></td>' +
								'<td><code data-wposes-copy>' + data['VerificationToken'] + '</code></td>' +
							'</tr>'
						);
					}

					$( '.wposes-sender' ).html( sender );
					window.location.hash = 'complete-verification';
				}
			} );
		}

		function ajax_save_settings( form_data, redirect_hash ) {
			$.ajax( {
				url: ajaxurl,
				type: 'POST',
				dataType: 'JSON',
				data: {
					_nonce: wposes.nonces.ajax_save_settings,
					action: 'wposes-ajax-save-settings',
					settings: form_data
				},
				error: function( jqXHR, textStatus, errorThrown ) {
					alert( 'error' + errorThrown );
				},
				success: function( response, textStatus, jqXHR ) {
					if ( 'undefined' !== typeof response.data.verified_senders ) {
						wposes.verified_senders = response.data.verified_senders;
					}

					if ( 'settings' === redirect_hash ) {
						// Workaround to make sure page is loaded correctly without doing a location.reload()
						window.location = wposes.plugin_url + '&setup-complete=true#settings';
					} else {
						$( '#setting-error-settings_updated' ).remove();
						window.location.hash = redirect_hash;
					}
				}
			} );
		}

		// Save access keys and region
		$( '#tab-setup-access-keys .wposes-wizard-next-btn' ).on( 'click', function( e ) {
			e.preventDefault();
			var form_data = $( '#wposes-setup-region' ).serialize();
			var region_text = $( 'select[name="region"] option:selected' ).text();
			$( '.wposes-region' ).text( region_text );

			var api = new wposes.AccessKeys();
			var key = $( 'input[name="aws-access-key-id"]' );
			var secret = $( 'input[name="aws-secret-access-key"]' );

			if ( ( key.is( ':disabled' ) && secret.is( ':disabled' ) ) ) {
				ajax_save_settings( form_data, 'sandbox-mode' );
			} else {
				$.when( api['sendRequest']( 'set', {
					'aws-access-key-id': key.val(),
					'aws-secret-access-key': secret.val()
				} ) ).then( function( response, textStatus, jqXHR ) {
					if ( response.success ) {
						secret.val( wposes.strings.not_shown_placeholder );
						ajax_save_settings( form_data, 'sandbox-mode' );
					} else {
						if ( 'undefined' !== typeof response.data.access_keys_defined && true === response.data.access_keys_defined ) {
							// User defined keys in wp-config.php and can continue
							ajax_save_settings( form_data, 'sandbox-mode' );
						} else {
							// Display any errors
							$( '#wposes_access_keys' ).show();
						}
					}
				} );
			}
		} );

		// Listen for sender type toggles
		$( '.wposes-sender-type-toggle input[type="radio"]' ).on( 'change', function() {
			if ( 'wposes-email' === this.id ) {
				$( '.wposes-show-domain' ).hide();
				$( '.wposes-show-email' ).show();
			} else {
				$( '.wposes-show-email' ).hide();
				$( '.wposes-show-domain' ).show();
			}
		} );

		// Verify the senders with Amazon
		$( '#tab-verify-sender .wposes-wizard-next-btn' ).on( 'click', function( e ) {
			e.preventDefault();

			var sender_type = $( 'input[name="sender-type"]:checked' ).val();
			var wposes_verify_sender_field = $( '#wposes-setup-verify-domain' );

			if ( 'email' === sender_type ) {
				wposes_verify_sender_field = $( '#wposes-setup-verify-email' );
			}

			if ( ! wposes_verify_sender_field[0].checkValidity() ) {
				wposes_verify_sender_field[0].reportValidity();
				return;
			}

			var sender = wposes_verify_sender_field.val();

			verify_identity( sender, sender_type );
		} );

		// Save and complete setup
		$( '#tab-configure-wp-offload-ses .wposes-wizard-next-btn' ).on( 'click', function( e ) {
			e.preventDefault();

			var notification_email = $( 'input[name="default-email"]' );
			notification_email[0].setCustomValidity( '' );

			if ( ! wposes.isVerifiedIdentity( notification_email.val() ) ) {
				notification_email[0].setCustomValidity( wposes.strings.email_not_verified );
				notification_email[0].reportValidity();
				return;
			}

			var form_data = $( '#wposes-setup-final-settings' ).serialize();
			ajax_save_settings( form_data, 'settings' );
		} );

		// Make sure Setup tab always goes to the right step
		$( document ).on( 'wposes.tabRendered', function() {
			var setup_tabs = [
				'start',
				'create-iam-user',
				'access-keys',
				'sandbox-mode',
				'verify-senders',
				'complete-verification',
				'configure-wp-offload-ses'
			];

			if ( -1 < setup_tabs.indexOf( location.hash.substring( 1 ) ) ) {
				$( '.nav-tab.start' ).attr( 'href', location.hash ).addClass( 'nav-tab-active' );
			}

			$( '.wposes-verification-errors' ).hide();
		} );

})( jQuery );
