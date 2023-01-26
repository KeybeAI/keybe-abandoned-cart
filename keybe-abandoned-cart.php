<?php

/**
 * Plugin Name: Keybe Abandoned Cart
 * Plugin URI: https://keybe.ai/
 * Description: Whatsapp notifications for abandoned cart. With keybe will boost your sales.
 * Version: 0.1
 * Author: Keybe.ai
 * Author URI: https://keybe.ai
 * Text Domain: keybe-abandoned-cart
 * Requires at least: 5.8
 * Requires PHP: 7.2
 *
 */

defined('ABSPATH') || exit;

include 'includes/settings-page.php';

// Post order data to Keybe and unset "abandono el carrito" to "no" on existing user
// Get settings from settings page
$options = get_option('keybe_settings');
$keybe_app_id = $options['keybe_app_id'];
$keybe_company_id = $options['keybe_company_id'];
$keybe_api_key = $options['keybe_api_key'];
$keybe_country_code = $options['keybe_country_code'];
if ($keybe_app_id && $keybe_company_id && $keybe_api_key && $keybe_country_code) {
	$active = 1;
} else {
	$active = 0;
}

if ($active === 1) :
	add_action('woocommerce_checkout_order_processed', function ($order_id) {
		$order = new WC_Order($order_id);
		if ($order->status != 'failed') {
			$url = "https://wrzy3jtldi.execute-api.us-east-1.amazonaws.com/prod/woocommerce/sync-abandoned-cart";
			$options = get_option('keybe_settings');
			$keybe_app_id = $options['keybe_app_id'];
			$keybe_company_id = $options['keybe_company_id'];
			$keybe_api_key = $options['keybe_api_key'];
			$keybe_country_code = $options['keybe_country_code'];
			$phone = $keybe_country_code . $order->get_billing_phone();
			$response = wp_remote_post(
				$url,
				array(
					'headers' => array('Content-Type' => 'application/json; charset=utf-8'),
					'body' => json_encode(array(
						'firstname' => $order->get_billing_first_name(),
						'lastname' =>  $order->get_billing_last_name(),
						'phone' =>   $phone,
						'email' =>  $order->get_billing_email(),
						'orde_id' => $order->get_id(),
						'order_status' => $order->get_status(),
						'companyUUID' => $keybe_company_id,
						'appUUID' => $keybe_app_id,
						'publicKey' => $keybe_api_key,
					))
				)
			);
		}
	});
	add_action('rest_api_init', function () {
		register_rest_route('keybe-data/v1', '/company-data', array(
			'methods' => 'GET',
			'callback' => 'keybe_settings',
		));
	});

	function keybe_settings()
	{
		return get_option('keybe_settings');
	}
	function keybe_abandoned_cart_script_tag()
	{
		// Register the external script with the desired attributes
		$script_attributes = array(
			'async' => true,
			'nonce' => wp_create_nonce('keybe_abandoned_cart_script_nonce'),
			'type' => 'module',
		);
		wp_register_script('keybe-abandoned-cart-script',  plugin_dir_url(__FILE__) . '/includes/js/abandoned-cart.js', array(), '', true, $script_attributes);
		if (is_checkout()) {
			wp_enqueue_script('keybe-abandoned-cart-script');
		}
	}
	add_action('wp_enqueue_scripts', 'keybe_abandoned_cart_script_tag');
endif;
