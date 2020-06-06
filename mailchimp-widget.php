<?php
/*
Plugin Name: Cleancoded MailChimp Widget
Plugin URI: https://github.com/cleancoded/widget-mailchimp
Description: Widgets for Mailchip
Author: Cleancoded
Text Domain: Cleancoded
Version: 1.0
Author URI: https://cleancoded.com/
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

function cleancoded_mailchimp_widget_generic_error() {
	printf('
		<div class="notice notice-error">
			<p>%s</p>
		</div>',
		__('There was an issue with the MailChimp Widget.', 'ns-mailchimp-widget')
	);
}

load_plugin_textdomain('ns-mailchimp-widget', plugins_url('language/', __FILE__));

try {

	if (version_compare(PHP_VERSION, '5.6.29') === -1) {
		throw new Error(
			__(
				'Please upgrade to a more recent version of <a href="http://php.net/downloads.php">PHP</a>(at least 5.6.29) to use the MailChimp Widget.',
				'ns-mailchimp-widget'
			)
		);
	}

	if (!function_exists('curl_init')) {
		throw new Error(
			__(
				'Please install <a href="http://php.net/manual/en/curl.installation.php">PHP with cURL support</a> to use the MailChimp Widget.',
				'ns-mailchimp-widget'
			)
		);
	}

	require __DIR__ . '/vendor/autoload.php';

	MailChimpWidget\Settings::init();

	$options = get_option('ns-mailchimp-widget');
	if (!empty($options['api-key'])) {
		add_action('widgets_init', function() {
			register_widget(new MailChimpWidget\Widget);
		});
		if (empty($options['styles'])) {
			wp_enqueue_style(
				'ns_mailchimpwidget',
				plugins_url('./stylesheets/style.css', __FILE__));
		}
	} else {
		add_action('admin_notices', function() {
			printf('
			<div class="notice notice-warning is-dismissible">
				<p>%s</p>
			</div>',
			sprintf(
				__("You'll need to set up the MailChimp Widget plugin settings before using it.
				You can do that <a href='%s'>here</a>.", 'ns-mailchimp-widget'),
				admin_url('/options-general.php?page=mailchimp-widget-settings')));
		});
	}

} catch(Error $e) {
	add_action('admin_notices', function() use ($e) {
		printf('
		<div class="notice notice-error">
			<p>%s</p>
		</div>
		',
		$e->getMessage());
	});
} catch(Error $e) {
	add_action('admin_notices', 'cleancoded_mailchimp_widget_generic_error');
}
