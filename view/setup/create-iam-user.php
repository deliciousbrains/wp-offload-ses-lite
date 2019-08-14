<div id="tab-create-iam-user"  data-prefix="wposes" class="wposes-tab wposes-content">
	<h2><?php _e( 'Create an IAM User', 'wp-offload-ses' ); ?></h2>

	<p><?php _e( 'First you\'ll need to create an IAM user to get the access keys you need to use WP Offload SES. If you already have your AWS access keys and are confident you\'ve set them up with the correct permissions, you can skip this step.', 'wp-offload-ses' ); ?></p>

	<ol>
		<li>
			<?php printf(
					__( '<a href="%1$s" target="_blank">Login to the AWS console</a> and navigate to <a href="%2$s" target="_blank">the IAM Users page</a>.', 'wp-offload-ses' ),
					'http://console.aws.amazon.com/console/home',
					'https://console.aws.amazon.com/iam/home#users'
				);
			?>
		</li>
		<li><?php _e( 'Click the <strong>Add user</strong> button.', 'wp-offload-ses' ); ?></li>
		<li><?php _e( 'Enter a name for the user in the <strong>User name</strong> field.', 'wp-offload-ses' ); ?></li>
		<li><?php _e( 'Under <strong>Select AWS access type</strong> select the checkbox for <strong>Programmatic access</strong>.', 'wp-offload-ses' ); ?></li>
		<li><?php _e( 'Click the <strong>Next: Permissions</strong> button.', 'wp-offload-ses' ); ?></li>
		<li><?php _e( 'Click <strong>Attach existing policies directly</strong> in the <strong>Set Permissions</strong> section.', 'wp-offload-ses' ); ?></li>
		<li><?php _e( 'Type <strong>AmazonSESFullAccess</strong> in the search bar and check the box to add the permission.', 'wp-offload-ses' ); ?></li>
		<li><?php _e( 'Click the <strong>Next: Tags</strong> button at the bottom of the page.', 'wp-offload-ses' ); ?></li>
		<li><?php _e( 'Click the <strong>Next: Review</strong> button at the bottom of the page.', 'wp-offload-ses' ); ?></li>
		<li><?php _e( 'Click the <strong>Create user</strong> button.', 'wp-offload-ses' ); ?></li>
	</ol>

	<p><?php _e( 'You will be shown the security credentials for the user, which consists of an <strong>Access Key ID</strong> and a <strong>Secret Access Key</strong>. Amazon will not show these again so copy them somewhere safe, or download them as a .csv file. If you lose them, you can always create a new set of keys from the console but you cannot retrieve the secret key again later.', 'wp-offload-ses' ); ?></p>

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