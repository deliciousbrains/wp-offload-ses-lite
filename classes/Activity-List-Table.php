<?php
/**
 * Displays the WP Offload SES activity list table.
 *
 * @package WP Offload SES
 * @author  Delicious Brains
 */

namespace DeliciousBrains\WP_Offload_SES;

use DeliciousBrains\WP_Offload_SES\WP_Offload_SES;
use DeliciousBrains\WP_Offload_SES\Utils;

// TODO: we may want to include our own version of this class.
if ( ! class_exists( '\WP_List_Table' ) ) {
	require_once( ABSPATH . '/wp-admin/includes/wp-list-table.php' );
}

/**
 * Class Activity_List_Table
 *
 * @since 1.0.0
 */
class Activity_List_Table extends \WP_List_Table {

	/**
	 * The WordPress database object.
	 *
	 * @var \WPDB
	 */
	private $database;

	/**
	 * The emails table.
	 *
	 * @var string
	 */
	private $emails_table;

	/**
	 * Constructs the Activity_List_Table class.
	 */
	public function __construct() {
		global $wpdb;

		// Set the current screen if doing AJAX to prevent notices.
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			set_current_screen( 'options-general' );
		}

		$this->screen       = get_current_screen();
		$this->database     = $wpdb;
		$this->emails_table = $this->database->base_prefix . 'oses_emails';

		/**
		 * Construct the WP_List_Table parent class.
		 *
		 * @param array
		 */
		parent::__construct(
			array(
				'singular' => 'activity',
				'plural'   => 'activity',
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
			'cb'        => '<input type="checkbox" />',
			'date'      => __( 'Date', 'wp-offload-ses' ),
			'subject'   => __( 'Subject', 'wp-offload-ses' ),
			'recipient' => __( 'Recipient', 'wp-offload-ses' ),
			'status'    => __( 'Status', 'wp-offload-ses' ),
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
			'date'      => array( 'date', false ),
			'subject'   => array( 'subject', false ),
			'recipient' => array( 'recipient', false ),
			'status'    => array( 'status', false ),
		);

		return $sortable_columns;
	}

	/**
	 * Return the individual data for each column.
	 *
	 * @param array  $item        The email data.
	 * @param string $column_name The column to display.
	 *
	 * @return string
	 */
	public function column_default( $item, $column_name ) {
		return $item[ $column_name ];
	}

	/**
	 * Handles the checkbox column output.
	 *
	 * @param array $email The array of info about the email.
	 */
	public function column_cb( $email ) {
	    $id = esc_attr( $email['id'] );
		?>
		<input id="cb-select-<?php echo $id; ?>" type="checkbox" name="email[]" value="<?php echo $id; ?>" />
		<?php
	}

	/**
	 * Handles the date column output.
	 *
	 * @param array $email The array of info about the email.
	 */
	public function column_date( $email ) {
		$formatted = Utils::get_date_and_time( $email['date'] );

		if ( ! $formatted ) {
			return false;
		}

		return $formatted['date'] . '<br>' . $formatted['time'];
	}

	/**
	 * Handles the subject output.
	 *
	 * @param array $email The array of info about the email.
	 */
	public function column_subject( $email ) {
		global $wp_offload_ses;

		$subject = esc_html( $email['subject'] );
		$actions = $wp_offload_ses->get_email_action_links( $email['id'], $email['status'] );

		if ( $wp_offload_ses->is_pro() ) {
			$subject = '<a href="#activity" class="wposes-view-email wposes-subject-link" data-email="' . esc_attr( $email['id'] ) . '">' . $subject . '</a>';
		}

		return $subject . $this->row_actions( $actions );
	}

	/**
	 * Handles the recipient output.
	 *
	 * @param array $email The array of info about the email.
	 */
	public function column_recipient( $email ) {
		$email['recipient'] = maybe_unserialize( $email['recipient'] );

		if ( is_array( $email['recipient'] ) ) {
			return implode( ', ', $email['recipient'] );
		}

		return esc_html( $email['recipient'] );
	}

	/**
	 * Handles the status output.
	 *
	 * @param array $email The array of info about the email.
	 */
	public function column_status( $email ) {
		global $wp_offload_ses;

		return sprintf( '<span class="email-status-%s">%s</span>', $email['status'], $wp_offload_ses->get_email_status_i18n( $email['status'] ) );
	}

	/**
	 * Gets the total number of items in the table.
	 *
	 * @return int
	 */
	public function get_total_items() {
		$where       = $this->get_where();
		$query       = "SELECT
						$this->emails_table.email_subject
						FROM $this->emails_table
						$where";
		$total_items = $this->database->query( $query );

		if ( false === $total_items ) {
			$total_items = 0;
		}

		return $total_items;
	}

	/**
	 * Gets the orderby used in SQL.
	 *
	 * @return string
	 */
	public function get_orderby() {
		$orderby = 'date';

		if ( isset( $_REQUEST['orderby'] ) ) {
			$sortable = array_keys( $this->get_sortable_columns() );

			if ( in_array( $_REQUEST['orderby'], $sortable ) ) {
				$orderby = $_REQUEST['orderby'];
			}
		}

		return $orderby;
	}

	/**
	 * Gets the order used in SQL.
	 *
	 * @return string
	 */
	public function get_order() {
		$order = 'DESC';

		if ( isset( $_REQUEST['order'] ) && 'asc' === $_REQUEST['order'] ) {
			$order = 'ASC';
		}

		return $order;
	}

	/**
	 * Get the WHERE used in SQL.
	 *
	 * @return string
	 */
	public function get_where() {
		$where     = array();
		$date      = ! empty( $_REQUEST['date'] ) ? $_REQUEST['date'] : false;
		$subject   = ! empty( $_REQUEST['subject'] ) ? $_REQUEST['subject'] : false;
		$recipient = ! empty( $_REQUEST['recipient'] ) ? $_REQUEST['recipient'] : false;
		$status    = ! empty( $_REQUEST['status'] ) ? $_REQUEST['status'] : false;

		// Build the WHERE queries (we may be searching multiple things here).
		if ( is_multisite() && ! Utils::is_network_admin() ) {
			$subsite_id = get_current_blog_id();
			$where[]    = $this->database->prepare( "($this->emails_table.subsite_id = %d)", $subsite_id );
		}

		if ( $date ) {
			$year    = substr( $date, 0, 4 );
			$month   = substr( $date, 4 );
			$start   = strtotime( "$month/01/$year" );
			$end     = strtotime( "$month/01/$year 1 month - 1 second" );
			$where[] = $this->database->prepare( 'email_created BETWEEN FROM_UNIXTIME(%s) AND FROM_UNIXTIME(%s)', $start, $end );
		}

		if ( $subject ) {
			$subject = '%' . $this->database->esc_like( stripslashes( $subject ) ) . '%';
			$where[] = $this->database->prepare( "($this->emails_table.email_subject LIKE %s)", $subject );
		}

		if ( $recipient ) {
			$recipient = '%' . $this->database->esc_like( $recipient ) . '%';
			$where[]   = $this->database->prepare( "($this->emails_table.email_to LIKE %s)", $recipient );
		}

		if ( $status && 'all' !== $status ) {
			$where[] = $this->database->prepare( 'email_status = %s', $status );
		}

		// Glue it all back together.
		if ( ! empty( $where ) ) {
			$where = 'WHERE ' . implode( ' AND ', $where );
		} else {
			$where = '';
		}

		return $where;
	}

	/**
	 * Get the data used to populate the table.
	 *
	 * @param int $current_page The current page of the list table.
	 * @param int $per_page     The number of items to return per page.
	 *
	 * @return array
	 */
	public function get_data( $current_page, $per_page ) {
		$offset  = ( $current_page - 1 ) * $per_page;
		$count   = $per_page;
		$orderby = $this->get_orderby();
		$order   = $this->get_order();
		$where   = $this->get_where();

		$query = "SELECT 
					email_id AS `id`,
					email_created AS `date`,
					email_subject AS `subject`,
					email_to AS `recipient`,
					email_status AS `status`
				FROM {$this->emails_table}
				$where
				ORDER BY $orderby $order
				LIMIT $offset, $count";

		$results = $this->database->get_results( $query, ARRAY_A );

		return $results;
	}

	/**
	 * Display the no items message
	 */
	public function no_items() {
		_e( 'No emails found. Check back later!', 'wp-offload-ses' );
	}

	/**
	 * Get the available bulk actions.
	 *
	 * @return array
	 */
	public function get_bulk_actions() {
		return array(
			'resend' => __( 'Send', 'wp-offload-ses' ),
			'cancel' => __( 'Cancel', 'wp-offload-ses' ),
			'delete' => __( 'Delete Permanently', 'wp-offload-ses' ),
		);
	}

	/**
	 * Get the views.
	 *
	 * @return array|bool
	 */
	public function get_views() {
		$where = '';

		if ( is_multisite() && ! Utils::is_network_admin() ) {
			$subsite_id = get_current_blog_id();
			$where      = $this->database->prepare( "WHERE $this->emails_table.subsite_id = %d", $subsite_id );
		}

		$query = "SELECT
					email_status,
					COUNT(*) AS num_emails
				FROM {$this->emails_table}
				$where
				GROUP BY email_status";

		$results = $this->database->get_results( $query, ARRAY_A );
		$total   = array_sum( wp_list_pluck( $results, 'num_emails' ) );

		if ( 0 === $total ) {
			return false;
		}

		$views['all'] = sprintf( __( 'All %s', 'wp-offload-ses' ), '<span class="count">(' . $total . ')</span>' );

		foreach ( $results as $row ) {
			$status = $row['email_status'];
			$count  = '<span class="count">(' . $row['num_emails'] . ')</span>';

			if ( 'sent' === $status ) {
				$views['sent'] = sprintf( __( 'Sent %s', 'wp-offload-ses' ), $count );
			}

			if ( 'failed' === $status ) {
				$views['failed'] = sprintf( __( 'Failed %s', 'wp-offload-ses' ), $count );
			}

			if ( 'queued' === $status ) {
				$views['queued'] = sprintf( __( 'Queued %s', 'wp-offload-ses' ), $count );
			}

			if ( 'cancelled' === $status ) {
				$views['cancelled'] = sprintf( __( 'Cancelled %s', 'wp-offload-ses' ), $count );
			}
		}

		return $views;
	}

	/**
	 * Display the list of views available on this table.
	 *
	 * @since 3.1.0
	 */
	public function render_views() {
		$views = $this->get_views();

		if ( ! $views ) {
			return false;
		}

		/**
		 * Filters the list of available list table views.
		 *
		 * The dynamic portion of the hook name, `$this->screen->id`, refers
		 * to the ID of the current screen, usually a string.
		 *
		 * @since 3.5.0
		 *
		 * @param string[] $views An array of available list table views.
		 */
		$views = apply_filters( "views_{$this->screen->id}", $views );
		if ( empty( $views ) ) {
			return;
		}
		$this->screen->render_screen_reader_content( 'heading_views' );

		echo "<ul class='subsubsub'>\n";
		foreach ( $views as $class => $view ) {
			if ( isset( $_REQUEST['status'] ) ) {
				$current = esc_attr( $_REQUEST['status'] );
			} else {
				$current = 'all';
			}

			$current = ( $current === $class ) ? 'current' : '';
			$views[ $class ] = "\t<li class='$class'><a href='#activity' class='$current' data-status='$class'>$view</a>";
		}
		echo implode( " |</li>\n", $views ) . "</li>\n";
		echo '</ul>';
	}

	/**
	 * Set the necessary args for the table.
	 */
	public function prepare_items() {
		$per_page              = 20;
		$current_page          = $this->get_pagenum();
		$columns               = $this->get_columns();
		$hidden                = array();
		$sortable              = $this->get_sortable_columns();
		$this->_column_headers = array( $columns, $hidden, $sortable );
		$total_items           = $this->get_total_items();
		$this->items           = $this->get_data( $current_page, $per_page );

		$this->set_pagination_args(
			array(
				'total_items' => $total_items,
				'per_page'    => $per_page,
				'total_pages' => ceil( $total_items / $per_page ),
				'orderby'     => ! empty( $_REQUEST['orderby'] ) && '' != $_REQUEST['orderby'] ? $_REQUEST['orderby'] : 'subject',
				'order'       => ! empty( $_REQUEST['order'] ) && '' != $_REQUEST['order'] ? $_REQUEST['order'] : 'desc',
			)
		);
	}

	/**
	 * Display the bulk actions.
	 */
	public function bulk_actions( $which = 'top' ) {
		if ( 'top' !== $which ) {
			return;
		}

		parent::bulk_actions( 'top' );
	}

	/**
	 * Process the bulk actions.
	 *
	 * @return string|bool
	 */
	public function process_bulk_actions() {
		global $wp_offload_ses;

		check_ajax_referer( 'wposes-activity-nonce', 'wposes_activity_nonce' );

		$actions   = array( 'resend', 'cancel', 'delete' );
		$processed = 0;
		$errors    = 0;

		if ( isset( $_REQUEST['bulk_action'] ) && isset( $_REQUEST['bulk_selected'] ) && in_array( $_REQUEST['bulk_action'], $actions, true ) ) {
			$action = $_REQUEST['bulk_action'];
			$method = $action . '_email';
			$emails = (array) $_REQUEST['bulk_selected'];
		} else {
			return false;
		}

		foreach ( $emails as $email ) {
			if ( ! $wp_offload_ses->$method( $email ) ) {
				$errors++;
				continue;
			}
			$processed++;
		}

		return $wp_offload_ses->get_email_action_notice( $action, $processed, $errors );
	}

	/**
	 * Display the table.
	 */
	public function display() {
		?>
		<form id="wposes-activity-form" method="post">
		<?php
		wp_nonce_field( 'wposes-activity-nonce', 'wposes_activity_nonce' );

		$order   = ! empty( $this->_pagination_args['order'] ) ? $this->_pagination_args['order'] : 'desc';
		$orderby = ! empty( $this->_pagination_args['orderby'] ) ? $this->_pagination_args['orderby'] : 'date';

		echo '<input type="hidden" id="order" name="order" value="' . esc_attr( $order ) . '" />';
		echo '<input type="hidden" id="orderby" name="orderby" value="' . esc_attr( $orderby ) . '" />';

		parent::display();
		?>
		</form>
		<?php
	}

	/**
	 * Displays the table nav.
	 *
	 * @param string $which Top or bottom.
	 */
	public function display_tablenav( $which = 'top' ) {
		if ( ! $this->has_items() ) {
			return false;
		}

		parent::display_tablenav( $which );
	}

	/**
	 * Display our custom search field.
	 *
	 * @param string $which Top or bottom.
	 */
	public function extra_tablenav( $which = 'top' ) {
		if ( 'top' !== $which || ! $this->has_items() ) {
			return;
		}
		?>
		<div id="wposes-activity-actions" class="alignleft actions">
			<?php $this->email_months_dropdown(); ?>
			<input id="wposes-subject-search" type="text" name="subject" placeholder="<?php _e( 'All Subjects', 'wp-offload-ses' ); ?>" />
			<input id="wposes-recipient-search" type="text" name="recipient" placeholder="<?php _e( 'All Recipients', 'wp-offload-ses' ); ?>" />
			<input type="submit" id="wposes-filter-btn" name="filter_action" class="button action" value="<?php _e( 'Filter', 'wp-offload-ses' ); ?>" />
		</div>
		<?php
	}

	/**
	 * Display the date select dropdown.
	 */
	public function email_months_dropdown() {
		global $wp_locale;

		$months = $this->database->get_results(
			"SELECT DISTINCT YEAR(email_created) AS year, MONTH(email_created) AS month
			FROM {$this->emails_table}
			ORDER BY email_created DESC"
		);
		$m = isset( $_GET['m'] ) ? (int) $_GET['m'] : 0;
		?>
		<select name="m" id="wposes-filter-by-date">
			<option <?php selected( $m, 0 ); ?> value="0"><?php _e( 'All dates', 'wp-offload-ses' ); ?></option>
			<?php
			foreach ( $months as $arc_row ) {
				if ( 0 == $arc_row->year ) {
					continue;
				}

				$month = zeroise( $arc_row->month, 2 );
				$year  = $arc_row->year;

				printf(
					"<option %s value='%s'>%s</option>\n",
					selected( $m, $year . $month, false ),
					esc_attr( $arc_row->year . $month ),
					sprintf( __( '%1$s %2$d' ), $wp_locale->get_month( $month ), $year )
				);
			}
			?>
			</select>
			<?php
	}

	/**
	 * Display the table over AJAX.
	 */
	public function ajax_response() {
		check_ajax_referer( 'wposes-activity-nonce', 'wposes_activity_nonce' );

		$bulk_actions_result = $this->process_bulk_actions();

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

		ob_start();
		$this->render_views();
		$views = ob_get_clean();

		$response                         = array( 'rows' => $rows );
		$response['pagination']['top']    = $pagination_top;
		$response['pagination']['bottom'] = $pagination_bottom;
		$response['column_headers']       = $headers;
		$response['views']                = $views;

		if ( isset( $total_items ) ) {
			$response['total_items_i18n'] = sprintf( _n( '1 item', '%s items', $total_items ), number_format_i18n( $total_items ) );
		}

		if ( isset( $total_pages ) ) {
			$response['total_pages']      = $total_pages;
			$response['total_pages_i18n'] = number_format_i18n( $total_pages );
		}

		$response['bulk_actions_result'] = '';
		if ( $bulk_actions_result ) {
			$response['bulk_actions_result'] = $bulk_actions_result;
		}

		die( wp_json_encode( $response ) );
	}

}
