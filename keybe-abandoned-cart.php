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

//  Script to capture data from abandoned cart
add_action('woocommerce_after_checkout_form', 'keybe_abandoned_cart_script');

function keybe_abandoned_cart_script(){
	// Get settings from settings page
	$options = get_option('keybe_settings');
	$keybe_app_id = $options['keybe_app_id'];
	$keybe_company_id = $options['keybe_company_id'];
	$keybe_api_key = $options['keybe_api_key'];
	$keybe_country_code = $options['keybe_country_code'];
	if ($keybe_app_id && $keybe_company_id && $keybe_api_key && $keybe_country_code){
		$active = 1;
	} else {
		$active = 0;
	}

	if ($active === 1) : ?>
			<script>
				console.log('KeyBe Abandoned Cart Ready!! 🚀');
				jQuery('form[name="checkout"]').change((item) => {
					if (["billing_phone", "billing_email"].includes(item.target.name)) {
						var values = jQuery('form[name="checkout"]').serializeArray();
						console.log(values);
						let country_code = '$keybe_country_code';
						fetch('https://wrzy3jtldi.execute-api.us-east-1.amazonaws.com/prod/woocommerce/generate-abandoned-cart', {
							method: 'POST',
							headers: new Headers({
								'Content-Type': 'application/json'
							}),
							body: JSON.stringify({
								userData: {
									name: values.find((item) => item.name === 'billing_first_name')?.value,
									lastName: values.find((item) => item.name === 'billing_last_name')?.value,
									phone: country_code + values.find((item) => item.name === 'billing_phone')?.value,
									email: values.find((item) => item.name === 'billing_email')?.value,
								},
								keybeClientData: {
									companyUUID: '$keybe_company_id',
									appUUID: '$keybe_app_id',
									publicKey: '$keybe_api_key',
									haveSearchPhoneUser: true,
								},
							}),
						});
					}
				})
			</script>
	<?php endif;
}

// Post order data to Keybe and unset "abandono el carrito" to "no" on existing user
// Get settings from settings page
$options = get_option('keybe_settings');
$keybe_app_id = $options['keybe_app_id'];
$keybe_company_id = $options['keybe_company_id'];
$keybe_api_key = $options['keybe_api_key'];
$keybe_country_code = $options['keybe_country_code'];
if ($keybe_app_id && $keybe_company_id && $keybe_api_key && $keybe_country_code){
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
endif;