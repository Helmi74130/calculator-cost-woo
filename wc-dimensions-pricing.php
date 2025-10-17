<?php
/**
 * Plugin Name: WooCommerce Dimensions Pricing
 * Description: Ajoute des champs A-F sur la fiche produit et calcule dynamiquement le prix basé sur les dimensions.
 * Version: 1.0.0
 * Author: Wii Studio
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

define( 'WCDP_PATH', plugin_dir_path( __FILE__ ) );
define( 'WCDP_URL', plugin_dir_url( __FILE__ ) );

require_once WCDP_PATH . 'includes/class-wcdp-init.php';

add_action( 'plugins_loaded', [ 'WCDP_Init', 'init' ] );