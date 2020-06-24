=== WP Offload SES Lite ===
Contributors: deliciousbrains, bradt, SylvainDeaure
Tags: amazon ses,smtp,email delivery,gmail smtp,newsletter
Requires at least: 5.0
Tested up to: 5.4
Requires PHP: 5.5+
Stable tag: 1.4.2

Fix your email delivery problems by sending your WordPress emails through Amazon SES's powerful email sending infrastructure.

== Description ==

https://www.youtube.com/watch?v=m2fSx-DAgfY&feature=youtu.be&t=212

Are your WordPress site emails not being delivered? That's pretty common. Over 20,000 sites trust WP Offload SES Lite to send their site email.

WordPress' default email sending functions just don't cut it these days. You absolutely need to set up something more.

Some folks set up an SMTP plugin to use their existing email provider (e.g. Gmail, Outlook.com, Yahoo, etc) to send their WordPress emails but then find out the hard way (i.e. emails not getting delivered) that there's a daily hard limit on the number of emails they can send. Sending WordPress emails through SMTP is simply not worth the risk.

Other folks try sending services like Postmark, Mailgun, Sendgrid, etc but realize that they're expensive and their WordPress plugins are subpar ([check out our reviews for details](https://deliciousbrains.com/most-wordpress-email-plugins-suck/?utm_campaign=WP%2BOffload%2BSES&utm_source=wordpress.org&utm_medium=free%2Bplugin%2Blisting)).

With WP Offload SES Lite, you get the high deliverability, powerful managed infrastructure, and low cost of Amazon SES but with the support of a quality WordPress plugin that's easy to set up and lets you know when there are sending failures.

With WP Offload SES Lite, you can:

* Effortlessly configure your site to send all email via Amazon SES with our step-by-step setup wizard
* Configure the default email address and name that WordPress uses for notifications
* Verify sending domains and email addresses
* Send a test email to make sure everything is working before enabling site-wide email sending
* View a list of all emails sent from your site
* View statistics on your Amazon SES send rate
* Set up a custom "Reply To" and "Return Path" address
* Weekly health report in your inbox gives you confidence your emails are sending
* Configure multisite subsites to use different email settings, or enforce the same settings for your whole network
* Integrate with your favorite form and newsletter plugins, including Ninja Forms, Contact Form 7, Gravity Forms, Email Subscribers & Newsletters, and more

### Upgrade to WP Offload SES <del>Lite</del>

Get email open and click reporting and more with an upgrade to WP Offload SES:

= Open & Click Reporting =

It's important to be able to measure the engagement of your site emails. Are people actually opening certain emails? Are they clicking links? With that information, you can try to update an email's subject line and see if the open rate improves. Or update the email copy and see if more people click on the links. With WP Offload SES, you can access all of this instantly, from your WordPress dashboard.

= Auto-Retry Email Sending Failures =

Every Amazon SES account has a max send rate. If you try to send more emails per second than your account rate, Amazon SES will return an error and refuse to send the email which could result in dropped emails if not handled properly. WP Offload SES is aware of your SES account's send rate and will stay within the limit, but in the event of a failed send (e.g. a networking issue) the robust queue system will retry sending those emails and keep track of failures.

= Manually Retry Email Sending Failures =

Let’s say there was a networking issue that prevented your site from connecting to Amazon SES to send your email. WP Offload SES will automatically retry sending a few times before giving up and calling it a failure. If that happens, you can simply retry those failures once connectivity to Amazon SES is restored. With WP Offload SES none of your emails will get dropped into the ether because of a failure.

= Manually Resend Any Sent Emails =

Let’s say that one of your users accidentally deleted a message that was sent to them. You can find that email in WP Offload SES and resend it in just a few clicks. No more manually assembling the pieces of the email you think they need because you don’t have a copy of the original email.

= Search for Any Email =

Amazon SES doesn’t log emails sent on its own, let alone allow you to search for a sent email. WP Offload SES logs every email sent and allows you to filter by date and search by recipient and/or email subject.

= View a Specific Email =

WP Offload SES saves the full content of every email sent through your WordPress site, allowing you to view exactly what your customers were sent.

= Analyze Engagement for a Specific Email =

Would you like to know if a customer has viewed a specific email they’ve been sent? Or maybe you want to know if they’ve clicked the links in the email? Simply search for the email, click View Email and you can see how many times they opened that particular email and how many times they clicked on the links. No more time consuming back-and-forth with the customer, asking if they’ve received an email or not.

= PriorityExpert™ Email Support =

PriorityExpert™ email support guarantees that a developer will handle your support request. A developer will reply to your very first email and a developer who works on the software will see it through to conclusion. It also means that your request will be assigned the highest priority in our queue.

[Compare WP Offload SES Lite and WP Offload SES →](https://deliciousbrains.com/wp-offload-ses/upgrade/?utm_campaign=WP%2BOffload%2BSES&utm_source=wordpress.org&utm_medium=free%2Bplugin%2Blisting)

https://www.youtube.com/watch?v=gUH3fMlrU10&rel=0

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

= What about SMTP plugins? =

Unfortunately, sending emails through your existing email provider over SMTP is prone to hitting rate limits, as standard email services like Gmail, Outlook.com, Yahoo, etc aren’t designed for the large number of emails that are sent when sending out invoices, password reset emails, support requests, etc. SMTP is also missing some key features like an email queue which means emails that don’t send successfully on the first try just get dropped.

= Will it work with Postmark, Mailgun, Sendgrid, and other email sending services? =

At the moment WP Offload SES only supports Amazon SES. We're considering supporting additional services like Mailgun, Sendgrid, Postmark, etc in the future. If this is something you'd like to see, [let us know](https://wordpress.org/support/plugin/wp-ses/).

= Does this plugin auto-retry email delivery failures? =

While you can see a list of all emails sent from your site with this plugin, WP Offload SES Lite does not auto-retry failures (nor does Amazon SES). If you’d like a plugin that will auto-retry for you, upgrade to [WP Offload SES](https://deliciousbrains.com/wp-offload-ses/upgrade/?utm_campaign=WP%2BOffload%2BSES&utm_source=wordpress.org&utm_medium=free%2Bplugin%2Blisting). 

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
4. Activity tab

== Changelog ==

= 1.4.2 - 2020-06-19 =
* Improvement: Health Report no longer includes the Health Report in email summaries
* Bug fix: Email queue getting stuck if there is a fatal error while processing an email
* Bug fix: Missing mbstring module causes fatal error

= 1.4.1 - 2020-04-27 =
* New: Added London, central Canada, and São Paulo as available regions
* New: Added 7 day log duration
* New: Added `wposes_send_cron_error_email` filter for disabling cron error emails
* Improvement: Reduce interval between cron health checks to reduce false-positives
* Bug fix: Cron health check not working properly on multisite installs
* Bug fix: Cron health check email being sent twice in some situations
* Bug fix: Cron health check still runs when sending via SES is disabled

= 1.4 - 2020-03-11 =
* New: Added email queue to handle bulk email sending
* New: Store a copy of attachments to prevent conflicts with other plugins
* Improvement: Health report no longer center aligned
* Bug fix: Health report not being sent in some situations
* Bug fix: Invalid headers causing email to fail
* Bug fix: Index on email log table too large for some MySQL servers
* Bug fix: Filter for infinite log duration no longer working
* Bug fix: Open tracking image missing alt attribute
* Bug fix: Activity tab UI controls not aligned since WordPress 5.3
* Bug fix: "From name" wrapped in quotes in some situations

= 1.3 - 2019-12-11 =
* [Release Summary Blog Post](https://deliciousbrains.com/wp-offload-ses-1-4-released-email-health-report/?utm_campaign=WP%2BOffload%2BSES&utm_source=wordpress.org&utm_medium=free%2Bplugin%2Blisting)
* New: Weekly/monthly health report in your inbox gives you confidence your emails are sending
* New: Added Frankfurt, Mumbai, and Sydney as available regions
* Bug fix: Click tracking improperly encodes anchor links

= 1.2.2 - 2019-09-11 =
* Bug fix: Broken link to plugin settings page if subsite settings are disabled
* Bug fix: Undefined index `SERVER_NAME` in some environments

= 1.2.1 - 2019-08-07 =
* Bug fix: Plugin no longer working as an mu-plugin
* Bug fix: Activity tab doesn't properly handle multiple recipients
* Bug fix: "Tags" step missing from Setup wizard
* Bug fix: Missing SimpleXMLElement module causes fatal error
* Bug fix: Error when adding email headers in some situations

= 1.2 - 2019-07-16 =
* [Release Summary Blog Post](https://deliciousbrains.com/wp-offload-ses-1-3-released-search-view-resend/?utm_campaign=WP%2BOffload%2BSES&utm_source=wordpress.org&utm_medium=free%2Bplugin%2Blisting)
* New: List of sent and failed emails in the new Activity tab
* New: Notice when an email fails to send
* Bug fix: PHP notice logged in some multisite installs

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

