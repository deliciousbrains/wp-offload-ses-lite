var wposesModal = (function( $ ) {

	var modal = {
		prefix: 'wposes',
		loading: false,
		dismissible: true
	};

	var modals = {};

	/**
	 * Target to key
	 *
	 * @param {string} target
	 *
	 * @return {string}
	 */
	function targetToKey( target ) {
		return target.replace( /[^a-z]/g, '' );
	}

	/**
	 * Check if modal exists in DOM or in Memory.
	 *
	 * @param {string} target
	 *
	 * @return {boolean}
	 */
	modal.exists = function( target ) {
		var key = targetToKey( target );

		if ( undefined !== modals[ key ] ) {
			return true;
		}

		if ( $( target ).length ) {
			return true;
		}

		return false;
	};

	/**
	 * Open modal
	 *
	 * @param {string}   target
	 * @param {function} callback
	 * @param {string}   customClass
	 */
	modal.open = function( target, callback, customClass ) {
		var key = targetToKey( target );

		// Overlay
		$( 'body' ).append( '<div id="wposes-overlay"></div>' );
		var $overlay = $( '#wposes-overlay' );

		// Modal container
		if ( modal.dismissible ) {
			$overlay.append( '<div id="wposes-modal"><span class="close-wposes-modal">×</span></div>' );
		} else {
			$overlay.append( '<div id="wposes-modal"></div>' );
		}

		var $modal = $( '#wposes-modal' );

		if ( undefined === modals[ key ] ) {
			var content = $( target );
			modals[ key ] = content.clone( true ).css( 'display', 'block' );
			content.remove();
		}
		$modal.data( 'wposes-modal-target', target ).append( modals[ key ] );

		if ( undefined !== customClass ) {
			$modal.addClass( customClass );
		}

		if ( 'function' === typeof callback ) {
			callback( target );
		}

		// Handle modals taller than window height,
		// overflow & padding-right remove duplicate scrollbars.
		$( 'body' ).addClass( 'wposes-modal-open' );
		$modal.css( 'display', 'table' );

		$overlay.fadeIn( 150 );
		$modal.fadeIn( 150 );

		$( 'body' ).trigger( 'wposes-modal-open', [ target ] );
	};

	/**
	 * Close modal
	 *
	 * @param {function} callback
	 */
	modal.close = function( callback ) {
		if ( modal.loading || ! modal.dismissible ) {
			return;
		}

		var target = $( '#wposes-modal' ).data( 'wposes-modal-target' );

		$( '#wposes-overlay' ).fadeOut( 150, function() {
			$( 'body' ).removeClass( 'wposes-modal-open' );

			$( this ).remove();

			if ( 'function' === typeof callback ) {
				callback( target );
			}
		} );

		$( 'body' ).trigger( 'wposes-modal-close', [ target ] );
	};

	/**
	 * Set loading state
	 *
	 * @param {boolean} state
	 */
	modal.setLoadingState = function( state ) {
		modal.loading = state;
	};

	/**
	 * Set dismissible state.
	 *
	 * @param {boolean} state
	 */
	modal.setDismissibleState = function( state ) {
		modal.dismissible = state;
	};

	// Setup click handlers
	$( document ).ready( function() {

		$( 'body' ).on( 'click', '[data-wposes-modal]', function( e ) {
			e.preventDefault();
			modal.open( $( this ).data( 'wposes-modal' ) + '.' + modal.prefix );
		} );

		$( 'body' ).on( 'click', '.wposes-modal-cancel, .close-wposes-modal, #wposes-overlay', function( e ) {
			if ( e.target !== this ) {
				return true;
			}

			e.preventDefault();
			modal.close();
		} );
	} );

	// Key handler.
	$( document ).on( 'keyup', function( e ) {
		if ( 'Escape' === e.key ) {
			modal.close();
		}
	} );

	return modal;

})( jQuery );
