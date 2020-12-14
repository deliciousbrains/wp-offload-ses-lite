( function( $ ) {

	var $body = $( 'body' );
	var $tabs = $( '.wposes-tab' );
	var $settings = $( '.wposes-settings' );
	var $activeTab;
	var defaultTab = 'general';

	if ( ! wposes.is_setup ) {
		defaultTab = 'start';
	}

	if ( ! wposes.show_settings_tabs ) {
		defaultTab = 'network-settings';
	}

	wposes.tabs = {

		defaultTab: defaultTab,

		/**
		 * Toggle settings tab
		 *
		 * @param string hash
		 * @param bool   persist_updated_notice
		 */
		toggle: function( hash, persist_updated_notice ) {
			hash = wposes.tabs.sanitizeHash( hash );

			$tabs.hide();
			$activeTab = $( '#tab-' + hash );
			$activeTab.show();
			$( '.nav-tab' ).removeClass( 'nav-tab-active' );
			$( 'a.nav-tab[data-tab="' + hash + '"]' ).addClass( 'nav-tab-active' );
			$( '.wposes-main' ).data( 'tab', hash );

			var sub_nav_tabs = [ 'network-settings', 'general', 'verified-senders', 'send-test-email', 'aws-access-keys', 'licence' ];

			if ( $.inArray( hash, sub_nav_tabs ) !== -1 ) {
				$( 'a.nav-tab[data-tab="settings"]' ).addClass( 'nav-tab-active' );
				$( '#wposes-settings-sub-nav' ).show();
			} else {
				$( '#wposes-settings-sub-nav' ).hide();
			}

			if ( ! persist_updated_notice ) {
				$( '.wposes-updated' ).removeClass( 'show' );
			}

			if ( 'support' === hash ) {
				wposes.tabs.getDiagnosticInfo();
			}
		},

		/**
		 * Update display of diagnostic info.
		 */
		getDiagnosticInfo: function() {
			var $debugLog = $( '.debug-log-textarea' );

			$debugLog.html( wposes.strings.get_diagnostic_info );

			var data = {
				action: 'wposes-get-diagnostic-info',
				_nonce: wposes.nonces.get_diagnostic_info
			};

			$.ajax( {
				url: ajaxurl,
				type: 'POST',
				dataType: 'JSON',
				data: data,
				error: function( jqXHR, textStatus, errorThrown ) {
					$debugLog.html( errorThrown );
				},
				success: function( data, textStatus, jqXHR ) {
					if ( 'undefined' !== typeof data[ 'success' ] ) {
						$debugLog.html( data[ 'diagnostic_info' ] );
					} else {
						$debugLog.html( wposes.strings.get_diagnostic_info_error );
						$debugLog.append( data[ 'error' ] );
					}
				}
			} );
		},

		/**
		 * Sanitize hash to ensure it references a real tab.
		 *
		 * @param string hash
		 *
		 * @return string
		 */
		sanitizeHash: function( hash ) {
			var $newTab = $( '#tab-' + hash );

			if ( 0 === $newTab.length ) {
				hash = wposes.tabs.defaultTab;
			}

			return hash;
		}
	};

	/**
	 * Reload the page, and show the persistent updated notice.
	 *
	 * Intended for use on plugin settings page.
	 */
	wposes.reloadUpdated = function() {
		var url = location.pathname + location.search;

		if ( ! location.search.match( /[?&]updated=/ ) ) {
			url += '&updated=1';
		}

		url += location.hash;

		location.assign( url );
	};

    /**
	 * Set checkbox
	 *
	 * @param string checkbox_wrap
	 */
	function setCheckbox( checkbox_wrap ) {
		var $switch = $activeTab.find( '#' + checkbox_wrap );
		var $checkbox = $switch.find( 'input[type=checkbox]' );

		$switch.toggleClass( 'on' ).find( 'span' ).toggleClass( 'checked' );
		var switchOn = $switch.find( 'span.on' ).hasClass( 'checked' );
		$checkbox.prop( 'checked', switchOn ).trigger( 'change' );
	}

	/**
	 * Update the UI with the current active tab set in the URL hash.
	 */
	function renderCurrentTab() {

		// If rendering the default tab, or a bare hash clean the hash.
		if ( '#' + wposes.tabs.defaultTab === location.hash ) {
			location.hash = '';

			return;
		}

		wposes.tabs.toggle( location.hash.replace( '#', '' ), true );

		$( document ).trigger( 'wposes.tabRendered', [ location.hash.replace( '#', '' ) ] );
	}

	/**
	 * Access Keys API object
	 * @constructor
	 */
	var AccessKeys = function() {
		this.$key = $tabs.find( 'input[name="aws-access-key-id"]' );
		this.$secret = $tabs.find( 'input[name="aws-secret-access-key"]' );
		this.$spinner = $tabs.find( '[data-wposes-aws-keys-spinner]' );
		this.$feedback = $tabs.find( '[data-wposes-aws-keys-feedback]' );
	};

	/**
	 * Set the access keys using the values in the settings fields.
	 */
	AccessKeys.prototype.set = function() {
		this.sendRequest( 'set', {
			'aws-access-key-id': this.$key.val(),
			'aws-secret-access-key': this.$secret.val()
		} ).done( function( response ) {
			if ( response.success ) {
				this.$secret.val( wposes.strings.not_shown_placeholder );
			}
		}.bind( this ) );
	};

	/**
	 * Remove the access keys from the database and clear the fields.
	 */
	AccessKeys.prototype.remove = function() {
		this.sendRequest( 'remove' )
			.done( function( response ) {
				if ( response.success ) {
					this.$key.val( '' );
					this.$secret.val( '' );
				}
			}.bind( this ) )
		;
	};

	/**
	 * Send the request to the server to update the access keys.
	 *
	 * @param {string} action The action to perform with the keys
	 * @param {undefined|Object} params Extra parameters to send with the request
	 *
	 * @returns {jqXHR}
	 */
	AccessKeys.prototype.sendRequest = function( action, params ) {
		var data = {
			action: 'wposes-aws-keys-' + action,
			_ajax_nonce: wposes.nonces[ 'aws_keys_' + action ]
		};

		if ( _.isObject( params ) ) {
			data = _.extend( data, params );
		}

		if ( wposes.is_setup ) {
			this.$spinner.addClass( 'is-active' );
		}

		return $.post( ajaxurl, data )
			.done( function( response ) {
				this.$feedback
					.toggleClass( 'notice-success', response.success )
					.toggleClass( 'notice-error', ! response.success );

				if ( wposes.is_setup || ! response.success ) {
					if ( response.data && response.data.message ) {
						this.$feedback.html( '<p>' + response.data.message + '</p>' ).show();
					}
				}

				if ( response.success && wposes.is_setup ) {
					wposes.reloadUpdated();
				}
			}.bind( this ) )
			.always( function() {
				this.$spinner.removeClass( 'is-active' );
			}.bind( this ) )
		;
	};

	wposes.AccessKeys = AccessKeys;

	wposes.isVerifiedIdentity = function( identity ) {
		var is_found = false;
		wposes.verified_senders.forEach( function( verified_sender ) {
			/**
			 * This covers both email and domain identities,
			 * a bit liberal but should catch most issues.
			 */
			if ( identity.endsWith( verified_sender ) ) {
				is_found = true;
			}
		} );

		return is_found;
	};

	/**
	 * Selects the inner text of a given HTML element.
	 *
	 * @param {HTMLElement} element
	 *
	 * Based on
	 * @link https://stackoverflow.com/a/987376/1037938
	 */
	wposes.selectText = function( element ) {
		var range;
		var selection;

		if ( document.body.createTextRange ) {
			range = document.body.createTextRange();
			range.moveToElementText( element );
			range.select();
		} else if ( window.getSelection ) {
			selection = window.getSelection();
			range = document.createRange();
			range.selectNodeContents( element );
			selection.removeAllRanges();
			selection.addRange( range );
		}
	};

	/*
	* Check for browser support to enable click-to-copy functionality.
	* If it exists, we add a class for styling, and bind the click listener to make it happen.
	*/
	if ( 'function' === typeof document.execCommand ) {
		$( '.wposes-main' ).addClass( 'wposes-click-to-copy' )
			.on( 'click', '[data-wposes-copy]', function() {
				wposes.selectText( this );
				document.execCommand( 'copy' );
				window.getSelection().removeAllRanges();
			} )
		;
	}

	$( document ).ready( function() {
		renderCurrentTab();

		/**
		 * Set the hashchange callback to update the rendered active tab.
		 */
		window.onhashchange = function( event ) {

			// Strip the # if still on the end of the URL
			if ( 'function' === typeof history.replaceState && '#' === location.href.slice( -1 ) ) {
				history.replaceState( {}, '', location.href.slice( 0, -1 ) );
			}

			$( '#setting-error-settings_updated' ).remove();

			renderCurrentTab();
		};

		// Move any compatibility errors below the nav tabs
		var $navTabs = $( '.wposes-main #wposes-settings-sub-nav' );
		$( '.wposes-compatibility-notice, div.updated, div.error, div.notice' ).not( '.below-h2, .inline' ).insertAfter( $navTabs );

		$tabs.on( 'change', '.sub-toggle', function( e ) {
			var setting = $( this ).attr( 'id' );
			$( '.wposes-setting.' + setting ).toggleClass( 'hide' );
		} );

		$( '.wposes-switch' ).on( 'click', function( e ) {
			if ( ! $( this ).hasClass( 'disabled' ) ) {
				setCheckbox( $( this ).attr( 'id' ) );
			}
		} );

		// Process the "Send Test Email" form
		$( 'body' ).on( 'click', '#wposes-send-test-email-btn', function( e ) {
			e.preventDefault();

			var spinner = $( '#tab-send-test-email .spinner' );
			var email_field = $( '#wposes-test-email-address' );
			var success_msg = $( '#wposes-test-email-sent' );
			var error_msg = $( '#wposes-test-email-error' );

			if ( ! email_field[0].checkValidity() ) {
				email_field[0].reportValidity();
				return;
			}

			success_msg.hide();
			error_msg.hide();

			spinner.addClass( 'is-active' );

			$.ajax( {
				url: ajaxurl,
				type: 'POST',
				dataType: 'JSON',
				data: {
					action: 'wposes-send-test-email',
					_nonce: wposes.nonces.wposes_send_test_email,
					email_address: $( '#wposes-test-email-address' ).val()
				},
				error: function( jqXHR, textStatus, errorThrown ) {
					$( '#tab-send-test-email .spinner' ).removeClass( 'is-active' );
					console.log( jqXHR, textStatus, errorThrown );
				},
				success: function( response, textStatus, jqXHR ) {
					spinner.removeClass( 'is-active' );

					if ( false === response.success && 'undefined' !== typeof response.data ) {
						$( '#wposes-test-email-error p' ).text( response.data );
						error_msg.show();
						return;
					}

					success_msg.show();
				}
			} );
		} );

		// Show / hide helper description
		$( 'body' ).on( 'click', '.general-helper', function( e ) {
			e.preventDefault();
			var icon = $( this );
			var bubble = $( this ).next();

			// Close any that are already open
			$( '.helper-message' ).not( bubble ).hide();

			var position = icon.position();
			if ( bubble.hasClass( 'bottom' ) ) {
				bubble.css( {
					'left': ( ( position.left - bubble.width() / 2 ) - 5 ) + 'px',
					'top': ( position.top + icon.height() + 9 ) + 'px'
				} );
			} else {
				bubble.css( {
					'left': ( position.left + icon.width() + 9 ) + 'px',
					'top': ( position.top + icon.height() / 2 - 18 ) + 'px'
				} );
			}

			bubble.toggle();
			e.stopPropagation();
		} );

		$( 'body' ).on( 'click', function() {
			$( '.helper-message' ).hide();
		} );

		$( '.helper-message' ).on( 'click', function( e ) {
			e.stopPropagation();
		} );

		$tabs.on( 'click', '[data-wposes-toggle-access-keys-form]', function( event ) {
			event.preventDefault();
			$( '#wposes_access_keys' ).toggle();
		} );

		$tabs.on( 'click', '[data-wposes-aws-keys-action]', function( event ) {
			event.preventDefault();
			var action = $( this ).data( 'wposesAwsKeysAction' );
			var api = new AccessKeys();

			if ( 'function' === typeof api[action] ) {
				api[action]();
			}
		} );

		$( '#wposes-enable-health-report-wrap' ).on( 'click', function( e ) {
			if ( $( this ).hasClass( 'on' ) ) {
				$( '#wposes-health-report-sub-settings' ).show();

				if ( 'custom' === $( 'select[name="health-report-recipients"]' ).val() ) {
					$( '.wposes-health-report-custom-recipients-container' ).show();
				} else {
					$( '.wposes-health-report-custom-recipients-container' ).hide();
				}
			} else {
				$( '#wposes-health-report-sub-settings' ).hide();
			}
		} );

		$( 'select[name="health-report-recipients"]' ).on( 'change', function( e ) {
			if ( 'custom' === $( this ).val() ) {
				$( '.wposes-health-report-custom-recipients-container' ).show();
				$( 'input[name="health-report-custom-recipients"]' ).attr( 'required', 'required' );
			} else {
				$( '.wposes-health-report-custom-recipients-container' ).hide();
				$( 'input[name="health-report-custom-recipients"]' ).removeAttr( 'required' );
			}
		} );

	} );

})( jQuery );
