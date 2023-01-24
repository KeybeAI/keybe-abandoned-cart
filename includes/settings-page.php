<?php
/**
 * Keybe Settings Page
 */

defined('ABSPATH') || exit;



function add_keybe_plugin_menu() {
	add_submenu_page('options-general.php', 'keybe Plugin', 'keybe Abandoned Cart', 'manage_options', 'keybe-abandoned-cart', 'keybe_plugin_function');
}
add_action('admin_menu', 'add_keybe_plugin_menu');

function keybe_settings_init() {
	register_setting( 'keybe-setting', 'keybe_settings' );
	add_settings_section('keybe-abandoned-cart-section', __( 'keybe Abandoned Cart', 'keybe-abandoned-cart' ), 'keybe_settings_section_callback', 'keybe-setting' );
	add_settings_field( 'keybe_app_id', __( 'App ID:', 'keybe-abandoned-cart' ), 'keybe_app_id', 'keybe-setting', 'keybe-abandoned-cart-section' );
	add_settings_field( 'keybe_company_id', __( 'Company ID:', 'keybe-abandoned-cart' ), 'keybe_company_id', 'keybe-setting', 'keybe-abandoned-cart-section' );
	add_settings_field( 'keybe_api_key', __( 'PublicKey:', 'keybe-abandoned-cart' ), 'keybe_api_key', 'keybe-setting', 'keybe-abandoned-cart-section' );
	add_settings_field( 'keybe_country_code', __( 'Country Code:', 'keybe-abandoned-cart' ), 'keybe_country_code', 'keybe-setting', 'keybe-abandoned-cart-section' );
}
add_action( 'admin_init', 'keybe_settings_init' );

function keybe_settings_section_callback(  ) {
	$plugin_url = plugin_dir_url(__FILE__);
	echo __( '<p><strong>Keybe Account settings</strong></p>', 'keybe-abandoned-cart' );

  echo __( '<p>You can find your API keys in your Keybe account <br>This plugin will send whatsapp notifications only for abandoned carts</p>', 'keybe-abandoned-cart' );
	
	echo __( '<p>Go to <a href="https://keybe.app/admin/configurations/app" target="_blank">https://keybe.app/admin/configurations/app</a> and get your credentials under API Keys tab.</p>', 'keybe-abandoned-cart' );
  
	echo __('<img style="max-width:750px; width:100%" src="'.$plugin_url.'/img/example.png"> <hr>', 'keybe-abandoned-cart');

	echo __( '<h3>Settings</h3><hr>', 'keybe-abandoned-cart' );
}

function keybe_app_id(){
	$options = get_option( 'keybe_settings' ); 
	$app_id = $options["keybe_app_id"];

	echo __("<input type='text' name='keybe_settings[keybe_app_id]' value='$app_id'>");
}
function keybe_company_id(){
	$options = get_option( 'keybe_settings' ); 
	$company_id = $options["keybe_company_id"];
	echo __("<input type='text' name='keybe_settings[keybe_company_id]' value='$company_id'>");
}

function keybe_api_key(){
	$options = get_option( 'keybe_settings' ); 
	$api_key = $options["keybe_api_key"];
	echo __("<input type='text' name='keybe_settings[keybe_api_key]' value='$api_key'>");
}

function keybe_country_code(){
	$options = get_option( 'keybe_settings' ); 
	$country_code = $options["keybe_country_code"];
	echo __("<input type='text' name='keybe_settings[keybe_country_code]' value='$country_code'> <p style='font-size: 11px; max-width: 200px'>Include the country code with + example: +57 <br> Check the list <a href='https://en.wikipedia.org/wiki/List_of_country_calling_codes' target='_blank'>Here!</a></p>");
}

function keybe_plugin_function(){ ?>
	<form action='options.php' method='post'> <?php
			settings_fields( 'keybe-setting' );
			do_settings_sections( 'keybe-setting' );
			submit_button(); ?>
	</form> <?php
}