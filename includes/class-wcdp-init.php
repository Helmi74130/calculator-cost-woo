<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

class WCDP_Init {

    public static function init() {
        self::includes();
        self::hooks();
    }

    private static function includes() {
        require_once WCDP_PATH . 'includes/class-wcdp-frontend.php';
        require_once WCDP_PATH . 'includes/class-wcdp-cart.php';
        require_once WCDP_PATH . 'includes/helpers.php';
    }

    private static function hooks() {
        // Frontend: afficher champs et scripts
        add_action( 'woocommerce_before_add_to_cart_button', [ 'WCDP_Frontend', 'display_fields' ] );
        add_action( 'wp_enqueue_scripts', [ 'WCDP_Frontend', 'enqueue_scripts' ] );

        // Ajouter les données au moment de l'ajout au panier
        add_filter( 'woocommerce_add_cart_item_data', [ 'WCDP_Cart', 'add_cart_item_data' ], 10, 2 );
        add_filter( 'woocommerce_get_cart_item_from_session', [ 'WCDP_Cart', 'get_cart_item_from_session' ], 10, 2 );

        // Recalculer les prix dans le panier
        add_action( 'woocommerce_before_calculate_totals', [ 'WCDP_Cart', 'update_cart_prices' ], 20, 1 );

        // Afficher les meta dans le panier et la commande
        add_filter( 'woocommerce_get_item_data', [ 'WCDP_Cart', 'display_cart_item_meta' ], 10, 2 );
        add_action( 'woocommerce_add_order_item_meta', [ 'WCDP_Cart', 'add_order_item_meta' ], 10, 3 );
    }
}