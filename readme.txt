=== WP Offload SES Lite ===
Contributors: deliciousbrains, bradt, SylvainDeaure
Tags: email,ses,amazon,webservice,deliverability,newsletter,autoresponder,mail,wp_mail,smtp,service
Requires at least: 5.0
Tested up to: 5.2
Requires PHP: 5.5+
Stable tag: 1.1

WP Offload SES Lite sends all outgoing WordPress emails through Amazon Simple Email Service (SES) instead of the local wp_mail() function.

== Description ==

WP SES is now WP Offload SES Lite. WP Offload SES Lite sends all outgoing WordPress emails through Amazon Simple Email Service (SES) instead of the local wp_mail() function.

This ensures high email deliverability, email traffic statistics and a powerful managed infrastructure.

With WP Offload SES Lite, you can:

* Effortlessly configure your site to send all email via Amazon SES with our step-by-step setup wizard
* Configure the default email address and name that WordPress uses for notifications
* Send verification requests for new domains and email addresses
* Send a test email to make sure everything is working before enabling site-wide email sending
* View statistics on your Amazon SES send rate
* Set up a custom "Reply To" and "Return Path" address
* Configure multisite subsites to use different email settings, or enforce the same settings for your whole network
* Integrate with your favorite form and newsletter plugins, including Ninja Forms, Contact Form 7, Gravity Forms, Email Subscribers & Newsletters, and more

**Upgrade for Email Support and More Features**

https://www.youtube.com/watch?v=gUH3fMlrU10&rel=0

Get email open and click tracking for all your Amazon SES emails with the following features and more:

* View reports for email opens and link clicks
* Queue email to handle rate limits and retry failures
* Email support
* More features coming soon!

[Compare WP Offload SES Lite and WP Offload SES →](https://deliciousbrains.com/wp-offload-ses/upgrade/?utm_campaign=WP%2BOffload%2BSES&utm_source=wordpress.org&utm_medium=free%2Bplugin%2Blisting)

== Installation ==

= Basic Setup =

1. Install and activate the plugin
1. Go to Settings > WP Offload SES in the WordPress dashboard menu
1. Follow the steps in the setup wizard to configure your AWS account and WP Offload SES Lite

== Frequently Asked Questions ==

= Where can I get support for the plugin? =

If you upgrade to [WP Offload SES](https://deliciousbrains.com/wp-offload-ses/upgrade/?utm_campaign=WP%2BOffload%2BSES&utm_source=wordpress.org&utm_medium=free%2Bplugin%2Blisting), we will gladly provide you with email support. We take pride in delivering exceptional customer support. We do not provide email support for the free version.

= What are the minimum requirements? =

* Latest version of WordPress or the one previous
* PHP 5.5+ compiled with the cURL extension
* MySQL 5.5+
* Apache 2+ or Nginx 1.4+
* Amazon Web Services account

= Can you help me about... (an Amazon concern) =

We are not otherwise linked to Amazon or Amazon Web Services.
Please direct your specific Amazon questions to Amazon support.

= How does this work on WordPress Multisite? =

You can configure the entire network to use the same settings by configuring the plugin via the Network Admin settings screen. If a subsite should have different settings, you can override some or all of the network settings by going to the WP Offload SES Lite settings page for that subsite.

You can also use the `WPOSES_SETTINGS` constant to define settings that will be applied to all subsites and can’t be overridden via the UI:

	define( 'WPOSES_SETTINGS', serialize( array(
		// Send site emails via Amazon SES.
		'send-via-ses'          => true,
		// Enable open tracking.
		'enable-open-tracking'  => true,
		// Enable click tracking.
		'enable-click-tracking' => true,
		// Amazon SES region (e.g. 'us-east-1' - leave blank for default region).
		'region'                => 'us-east-1',
		// Changes the default email address used by WordPress
		'default-email'         => 'your-email@example.com',
		// Changes the default email name used by WordPress.
		'default-email-name'    => 'Your Name Here',
		// Sets the "Reply-To" header for all outgoing emails.
		'reply-to'              => 'your-email@example.com',
		// Sets the "Return-Path" header used by Amazon SES.
		'return-path'           => 'your-email@example.com',
		// Amount of days to keep email logs (e.g. 30, 60, 90, 180, 365, 730)
		'log-duration'          => '30',
	) ) );

See our [documentation](https://deliciousbrains.com/wp-offload-ses/doc/settings-constants/?utm_campaign=WP%2BOffload%2BSES&utm_source=wordpress.org&utm_medium=free%2Bplugin%2Blisting) on this for more information.

= Do you have a hook that is fired when an email is sent? =

Yes, the `wposes_mail_sent` hook could be used to log emails, or post email info to an API or database:

	function myMailSentHook( $to, $subject, $message, $headers, $attachments ) {
		// Your code here.
	}
	add_action( 'wposes_mail_sent', 'myMailSentHook', 10, 5 );

= Can I create a translation for your plugin? =

Yes, please do! It's easy.

1. Visit [translate.wordpress.org](https://translate.wordpress.org)
1. Choose your locale
1. Click on *Plugins*
1. Search for *WP Offload SES Lite*
1. Click on WP Offload SES Lite
1. Translate!

= Will WP Offload SES Lite work with other plugins that send email? =

Yes, WP Offload SES should be compatible with any plugin that uses the standard `wp_mail()` function for sending email. If you’ve found a plugin that isn’t using that function for sending mail, you may want to reach out to the plugin developer and see if that can be changed.

= Why aren't my AWS access keys working? =

Please double check the credentials match up with the credentials you received when creating your IAM user, and that your IAM user has the `AmazonSESFullAccess` permission.

== Screenshots ==

1. Setup wizard
2. Main settings page
3. Verified senders

== Changelog ==

= 1.1 - 2019-05-07 =
* New: Multisite network level setting to enable subsite settings
* New: Multisite subsite level setting to override network settings
* Improvement: Increase default log retention to 90 days

= 1.0 - 2019-04-17 =
* New: Redesigned UI
* New: Setup wizard
* New: Codebase rewritten from the ground up
* New: Verify any domain or email address
* New: Requires PHP 5.5+

= 0.8.2 - 2019-01-09 =
* Add discount for launch of WP Offload SES

= 0.8.1 - 2018-06-06 =
* Added dismissable admin notice that WP SES will soon require PHP 5.5+

= 0.8 - 2017-12-28 =
* WP SES has been acquired by [Delicious Brains Inc](https://deliciousbrains.com/?utm_campaign=WP%2BOffload%2BSES&utm_source=wordpress.org&utm_medium=free%2Bplugin%2Blisting) (big improvements on the way including a pro upgrade)
* Refreshed UI to be consistent with the latest WordPress styles
* Updated in-plugin instructions to be much clearer
* Updated set up documentation
* Now works with translate.wordpress.org

= 0.7.2.1 =
* Fix for stats report, thanks to @Ange1Rob0t

= 0.7.2 =
* Fix for use as "must use plugin" in a wpmu setup, thanks to @positonic

= 0.7.1 =
* fix deprecated get_currentuserinfo()

= 0.7.0 =
* PHP 7.0 Compatibility

= 0.4.8 =
* Experimental support for cc: and Bcc: in custom header
* Domain verification is ok

= 0.4.0 =
* Serbo-Croatian Translation by https://webhostinggeeks.com/
* Fixed Reply-to: extraction Regexp
* fixes from hbradleyiii https://wordpress.org/support/topic/bug-with-force-plugin-activation-option
* better handling of custom headers
* removed ListVerifiedEmailAddresses deprecated api call, now using ListIdentities.
* added wpses_mailsent hook
* several minor fixes.

= 0.3.58 =
* Tries to always auto-activate in answer to https://wordpress.org/support/topic/the-plugin-get-inactive-after-a-few-minutes
* small fixes

= 0.3.56 =
* fixed sender name format
* fixed regexp for some header recognition
* now supports comma separated emails in to: header

= 0.3.54 =
* bad ses lib include fixed
* Added "force plugin activation" for some use case with IAM credentials

= 0.3.52 =
* Warning if Curl not installed
* Attachments support for use with Contact Form (finally !)
* Notice fixed

= 0.3.50 =
* Notice fixed, setup documentation slightly tweaked

= 0.3.48 =
* Experimental "WP Better Email" Plugin compatibility

= 0.3.46 =
* Maintenance release - fixes some notices and old code.

= 0.3.45 =
* Maintenance release - fixes some notices.

= 0.3.44 =
* Added Amazon SES Endpoint selection. EU users can now select EU region.

= 0.3.42 =
* Added Spanish translation, thanks to Andrew of webhostinghub.com

= 0.3.4 =
* Auto activation via WP_SES_AUTOACTIVATE define, see FAQ.

= 0.3.2 =
* Tweaked header parsing thanks to bhansson

= 0.3.1 =
* Added Reply-To
* Added global WPMU setup (To be fully tested)

= 0.2.9 =
* Updated SES access class
* WP 3.5.1 compatibility
* Stats sorting
* Allow Removal of verified e-mail address
* Added wp_mail filter
* "Forgotten password" link is now ok.
* Various bugfixes

= 0.2.2 =
Reference Language is now English.
WP SES est fourni avec les textes en Francais.

= 0.2.1 =
Added some functions

* SES Quota display
* SES Statistics
* Can set email return_path
* Full email test form
* Can partially de-activate plugin for intensive testing.

= 0.1.2 =
First public Beta release

* Functionnal version
* Internationnal Version
* fr_FR and en_US locales

= 0.1 =
* Proof of concept

== Upgrade Notice ==

= 0.4.8 =
Domain verification is ok

= 0.4.2 =
Experimental support for cc: and Bcc: in custom header

= 0.4.0 =
Removed deprecated SES call, several bugfixes, added sr_RS translation.

= 0.2.9 =
Pre-release, mainly bugfixes, before another update.

= 0.2.2 =
All default strings are now in english.

= 0.2.1 =
Quota and statistics Integration

= 0.1.2 =
First public Beta release


