<?php
/**
 * Displays the verified senders list table.
 *
 * @author  Delicious Brains
 * @package WP Offload SES
 */

namespace DeliciousBrains\WP_Offload_SES;

use DeliciousBrains\WP_Offload_SES\WP_Offload_SES;

// TODO: we may want to include our own version of this class.
if ( ! class_exists( '\WP_List_Table' ) ) {
	require_once( ABSPATH . '/wp-admin/includes/wp-list-table.php' );
}

/**
 * Class Verified_Senders_List_Table
 *
 * @since 1.0.0
 */
class Verified_Senders_List_Table extends \WP_List_Table {

	/**
	 * Constructs the Verified_Senders_List_Table class.
	 */
	public function __construct() {
		// Set the current screen if doing AJAX to prevent notices.
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			set_current_screen( 'options-general' );
		}

		$this->screen = get_current_screen();

		/**
		 * Construct the WP_List_Table parent class.
		 *
		 * @param array
		 */
		parent::__construct(
			array(
				'singular' => 'sender',
				'plural'   => 'senders',
				'ajax'     => true,
				'screen'   => $this->screen,
			)
		);
	}

	/**
	 * Get the column names for the table.
	 *
	 * @return array
	 */
	public function get_columns() {
		$columns = array(
			'sender'  => __( 'Sender', 'wp-offload-ses' ),
			'type'    => __( 'Type', 'wp-offload-ses' ),
			'status'  => __( 'Status', 'wp-offload-ses' ),
			'actions' => __( 'Actions', 'wp-offload-ses' ),
		);

		return $columns;
	}

	/**
	 * Get the sortable columns.
	 *
	 * @return array
	 */
	public function get_sortable_columns() {
		$sortable_columns = array(
			'sender' => array( 'sender', false ),
			'type'   => array( 'type', false ),
			'status' => array( 'status', false ),
		);

		return $sortable_columns;
	}

	/**
	 * Query the SES API for the verified senders.
	 *
	 * @return array
	 */
	public function get_data() {
		/** @var WP_Offload_SES $wp_offload_ses */
		global $wp_offload_ses;

		$data       = array();
		$identities = $wp_offload_ses->get_ses_api()->get_identities();

		if ( is_wp_error( $identities ) ) {
			return $data;
		}

		$statuses = $wp_offload_ses->get_ses_api()->get_identity_verification_attributes( $identities );

		if ( is_wp_error( $statuses ) ) {
			return $data;
		}

		foreach ( $identities as $identity ) {
			$status = $statuses[ $identity ];

			array_push(
				$data,
				array(
					'sender'  => $identity,
					'type'    => isset( $status['VerificationToken'] ) ? 'Domain' : 'Email',
					'status'  => '<span class="sender-' . $status['VerificationStatus'] . '">' . $status['VerificationStatus'] . '</span>',
					'actions' => $this->get_sender_actions( $identity, $status ),
				)
			);
		}

		return $data;
	}

	/**
	 * Get the available actions for the provided sender.
	 *
	 * @param string $identity The identity to get actions for.
	 * @param array  $status   The status of the identity.
	 *
	 * @return string
	 */
	public function get_sender_actions( $identity, $status ) {
		$actions = '<a class="wposes-remove-sender" data-sender="' . $identity . '" href="#">' . __( 'Remove', 'wp-offload-ses' ) . '</a>';

		if ( 'Pending' === $status['VerificationStatus'] ) {
			if ( isset( $status['VerificationToken'] ) ) {
				$actions = '<a class="wposes-view-dns" data-sender="' . $identity . '" data-token="' . $status['VerificationToken'] . '" href="#">' . __( 'View DNS', 'wp-offload-ses' ) . '</a> | ' . $actions;
			} else {
				$actions = '<a class="wposes-resend-verification" data-sender="' . $identity . '" href="#">' . __( 'Resend Verification', 'wp-offload-ses' ) . '</a> | ' . $actions;
			}
		}

		return $actions;
	}

	/**
	 * Return the individual data for each column.
	 *
	 * @param array  $item        The verified sender.
	 * @param string $column_name The column to display.
	 *
	 * @return string
	 */
	public function column_default( $item, $column_name ) {
		return $item[ $column_name ];
	}

	/**
	 * Display the no items message
	 */
	public function no_items() {
		_e( 'No verified senders found.', 'wp-offload-ses' );
	}

	public function extra_tablenav( $which = 'bottom' ) {
		if ( 'bottom' !== $which ) {
			return;
		}
		?>
		<button id="wposes-add-new-verified-sender" class="button"><?php _e( '+ Add New', 'wp-offload-ses' ); ?></button>
		<?php
	}

	/**
	 * Reorder the data based on table sort
	 *
	 * @param string $a The data to sort.
	 * @param string $b The data to sort.
	 *
	 * @return int
	 */
	public function usort_reorder( $a, $b ) {
		$orderby = ( ! empty( $_REQUEST['orderby'] ) ) ? $_REQUEST['orderby'] : 'sender'; // If no sort, default to sender.
		$order   = ( ! empty( $_REQUEST['order'] ) ) ? $_REQUEST['order'] : 'asc'; // If no order, default to asc.
		$result  = strcmp( $a[ $orderby ], $b[ $orderby ] ); // Determine sort order.

		return ( 'asc' === $order ) ? $result : -$result; // Send final sort direction to usort.
	}

	/**
	 * Set the necessary args for the table.
	 */
	public function prepare_items() {
		$per_page = 5;
		$columns  = $this->get_columns();
		$hidden   = array();
		$sortable = $this->get_sortable_columns();

		$this->_column_headers = array( $columns, $hidden, $sortable );

		$data = $this->get_data();

		usort( $data, array( $this, 'usort_reorder' ) );

		$current_page = $this->get_pagenum();
		$total_items  = count( $data );
		$data         = array_slice( $data, ( ( $current_page - 1 ) * $per_page ), $per_page );

		$this->items = $data;

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page ),
				'orderby'     => ! empty( $_REQUEST['orderby'] ) && '' != $_REQUEST['orderby'] ? $_REQUEST['orderby'] : 'sender',
				'order'       => ! empty( $_REQUEST['order'] ) && '' != $_REQUEST['order'] ? $_REQUEST['order'] : 'asc',
			)
		);
	}

	/**
	 * Display the table.
	 */
	public function display() {
		wp_nonce_field( 'wposes-verified-senders-nonce', 'wposes_verified_senders_nonce' );

		$order   = ! empty( $this->_pagination_args['order'] ) ? $this->_pagination_args['order'] : 'asc';
		$orderby = ! empty( $this->_pagination_args['orderby'] ) ? $this->_pagination_args['orderby'] : 'sender';

		echo '<input type="hidden" id="order" name="order" value="' . esc_attr( $order ) . '" />';
		echo '<input type="hidden" id="orderby" name="orderby" value="' . esc_attr( $orderby ) . '" />';

		parent::display();
	}

	/**
	 * Display the table over AJAX.
	 */
	public function ajax_response() {
		check_ajax_referer( 'wposes-verified-senders-nonce', 'wposes_verified_senders_nonce' );
		$this->prepare_items();

		extract( $this->_args );
		extract( $this->_pagination_args, EXTR_SKIP );

		ob_start();
		if ( ! empty( $_REQUEST['no_placeholder'] ) ) {
			$this->display_rows();
		} else {
			$this->display_rows_or_placeholder();
		}
		$rows = ob_get_clean();

		ob_start();
		$this->print_column_headers();
		$headers = ob_get_clean();

		ob_start();
		$this->pagination( 'top' );
		$pagination_top = ob_get_clean();

		ob_start();
		$this->pagination( 'bottom' );
		$pagination_bottom = ob_get_clean();

		$response                         = array( 'rows' => $rows );
		$response['pagination']['top']    = $pagination_top;
		$response['pagination']['bottom'] = $pagination_bottom;
		$response['column_headers']       = $headers;

		if ( isset( $total_items ) ) {
			$response['total_items_i18n'] = sprintf( _n( '1 item', '%s items', $total_items ), number_format_i18n( $total_items ) );
		}

		if ( isset( $total_pages ) ) {
			$response['total_pages']      = $total_pages;
			$response['total_pages_i18n'] = number_format_i18n( $total_pages );
		}

		die( wp_json_encode( $response ) );
	}

}
