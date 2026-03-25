<?php
// phpcs:disable WordPress.NamingConventions.PrefixAllGlobals.NonPrefixedVariableFound
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
?>
<div id="tab-create-iam-user"  data-prefix="wposes" class="wposes-tab wposes-content">
	<h2><?php esc_html_e( 'Create an IAM User', 'wp-offload-ses' ); ?></h2>

	<p><?php esc_html_e( 'First, you\'ll need to create an IAM user to get the access keys you need to use WP Offload SES. If you already have your AWS access keys and are confident you\'ve set them up with the correct permissions, you can skip this step.', 'wp-offload-ses' ); ?></p>

	<p><?php echo wp_kses( __( 'Setting up a new user is done in two steps, <strong>Creating the User</strong> and <strong>Creating Access Keys</strong>.', 'wp-offload-ses' ), array( 'strong' => array() ) ); ?></p>

	<h3><?php esc_html_e( 'Creating the user', 'wp-offload-ses' ); ?></h3>

	<ol>
		<li>
			<?php echo wp_kses(
				sprintf(
					__( '<a href="%1$s" target="_blank">Log in to the AWS console</a> and navigate to <a href="%2$s" target="_blank">the IAM Users page</a>.', 'wp-offload-ses' ),
					'http://console.aws.amazon.com/console/home',
					'https://console.aws.amazon.com/iam/home#users'
				),
				array( 'a' => array( 'href' => array(), 'target' => array() ) )
			); ?>
		</li>
		<li><?php echo wp_kses( __( 'Click the <strong>Create user</strong> button.', 'wp-offload-ses' ), array( 'strong' => array() ) ); ?></li>
		<li><?php echo wp_kses( __( 'Enter a name for the user in the <strong>User name</strong> field.', 'wp-offload-ses' ), array( 'strong' => array() ) ); ?></li>
		<li><?php echo wp_kses( __( 'Leave the checkbox for <strong>Provide user access to the AWS Management Console - optional</strong> unchecked (this user is for programmatic access only) and click <strong>Next</strong>.', 'wp-offload-ses' ), array( 'strong' => array() ) ); ?></li>
		<li><?php echo wp_kses( __( 'On the permissions page, select <strong>Attach policies directly</strong>.', 'wp-offload-ses' ), array( 'strong' => array() ) ); ?></li>
		<li><?php echo wp_kses( __( 'Type <strong>AmazonSESFullAccess</strong> in the search bar and check the box to add the permission.', 'wp-offload-ses' ), array( 'strong' => array() ) ); ?></li>
		<li><?php echo wp_kses( __( 'Click the <strong>Next</strong> button at the bottom of the page.', 'wp-offload-ses' ), array( 'strong' => array() ) ); ?></li>
		<li><?php echo wp_kses( __( 'In the final step, review your choices and click the <strong>Create user</strong> button at the bottom of the page.', 'wp-offload-ses' ), array( 'strong' => array() ) ); ?></li>
	</ol>

	<h3><?php esc_html_e( 'Creating Access Keys', 'wp-offload-ses' ); ?></h3>
	<ol>
		<li>
			<?php echo wp_kses(
				sprintf(
					__( 'Click on the newly created user name in the <a href="%1$s" target="_blank">IAM Users page</a> in the AWS Console to open the user details page', 'wp-offload-ses' ),
					'https://console.aws.amazon.com/iam/home#users'
				),
				array( 'a' => array( 'href' => array(), 'target' => array() ) )
			); ?>
		</li>
		<li><?php echo wp_kses( __( 'Go to the <strong>Security credentials</strong> tab and scroll down to the <strong>Access keys</strong> section.', 'wp-offload-ses' ), array( 'strong' => array() ) ); ?></li>
		<li><?php echo wp_kses( __( 'Click the <strong>Create access key</strong> button.', 'wp-offload-ses' ), array( 'strong' => array() ) ); ?></li>
		<li><?php echo wp_kses( __( 'Select the <strong>Application running outside AWS</strong> use case, check the confirmation checkbox that appears, and click <strong>Next</strong>.', 'wp-offload-ses' ), array( 'strong' => array() ) ); ?></li>
		<li><?php echo wp_kses( __( 'Optionally add a description tag for the access key (recommended but not required), then click <strong>Create access key</strong>.', 'wp-offload-ses' ), array( 'strong' => array() ) ); ?></li>
		<li><?php esc_html_e( 'You will be shown the security credentials for the user.', 'wp-offload-ses' ); ?></li>
	</ol>

	<p><?php echo wp_kses( __( 'The security credentials for the user consist of an <strong>Access Key ID</strong> and a <strong>Secret Access Key</strong>. Amazon will not show these again so copy them somewhere safe, or download them as a .csv file. If you lose them, you can always create a new set of keys from the console but you cannot retrieve the secret key again later.', 'wp-offload-ses' ), array( 'strong' => array() ) ); ?></p>

	<?php
		$args = array(
			'previous_hash' => 'start',
			'next_hash'     => 'setup-access-keys',
			'next_title'    => __( 'Next: Enter Access Keys', 'wp-offload-ses' ),
			'step'          => 1,
		);
		$this->render_view( 'setup/nav', $args );
	?>
</div>
