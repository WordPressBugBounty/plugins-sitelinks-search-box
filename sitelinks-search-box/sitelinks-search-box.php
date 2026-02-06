<?php
/**
 * Plugin Name: Sitelinks Search Box
 * Plugin URI: http://apasionados.es
 * Description: Adds the JSON-LD schema.org markup for the "Google Sitelinks Search Box" on the homepage.
 * Version: 1.5
 * Author: Apasionados.es
 * Author URI: http://apasionados.es
 * License: GPL2
 * Text Domain: ap_sitelinks_search_box
 * Domain Path: /languages
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Prevent direct access.
}

define( 'AP_SLSB_TEXTDOMAIN', 'ap_sitelinks_search_box' );

/**
 * Load translations at the correct time.
 */
function ap_slsb_load_textdomain() {
	load_plugin_textdomain(
		AP_SLSB_TEXTDOMAIN,
		false,
		dirname( plugin_basename( __FILE__ ) ) . '/languages'
	);
}
add_action( 'plugins_loaded', 'ap_slsb_load_textdomain' );

/**
 * Whether a schema plugin is already likely handling sitelinks search box.
 * (Yoast SEO commonly does.)
 */
function ap_slsb_is_schema_handled_elsewhere() {
	return defined( 'WPSEO_VERSION' ) || class_exists( 'WPSEO_Frontend' ) || function_exists( 'wpseo_init' );
}

/**
 * Output JSON-LD for Google's Sitelinks Search Box on the front page.
 */
function ap_slsb_output_jsonld() {
	if ( ! is_front_page() ) {
		return;
	}

	if ( ap_slsb_is_schema_handled_elsewhere() ) {
		return;
	}

	$site_url   = trailingslashit( home_url( '/' ) );
	$search_url = add_query_arg( 's', '{search_term}', $site_url );

	$data = array(
		'@context'        => 'https://schema.org',
		'@type'           => 'WebSite',
		'url'             => esc_url_raw( $site_url ),
		'potentialAction' => array(
			'@type'       => 'SearchAction',
			'target'      => esc_url_raw( $search_url ),
			'query-input' => 'required name=search_term',
		),
	);

	echo "\n" . '<script type="application/ld+json">' . "\n";
	echo wp_json_encode( $data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . "\n";
	echo "</script>\n";
}

// wp_head is typical for structured data. If you prefer footer, switch back to wp_footer.
add_action( 'wp_head', 'ap_slsb_output_jsonld', 20 );
