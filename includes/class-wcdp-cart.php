<?php
if (! defined('ABSPATH')) {
  exit;
}

class WCDP_Cart
{

  // Ajoute les données du client (dimensions + prix calculé) au cart item
  public static function add_cart_item_data($cart_item_data, $product_id)
  {
    if (isset($_REQUEST['wcdp_A']) || isset($_REQUEST['wcdp_calculated_price'])) {
      $dimensions = [];
      for ($i = ord('A'); $i <= ord('F'); $i++) {
        $k = 'wcdp_' . chr($i);
        $dimensions[chr($i)] = isset($_REQUEST[$k]) ? wc_clean(wp_unslash($_REQUEST[$k])) : '';
      }

      $cart_item_data['wcdp_dimensions'] = $dimensions;
      $cart_item_data['wcdp_calculated_price'] = isset($_REQUEST['wcdp_calculated_price']) ? floatval($_REQUEST['wcdp_calculated_price']) : 0;

      // Ensure unique key so similar products with different dimensions are separate entries
      $cart_item_data['unique_key'] = md5(microtime() . rand());
    }

    return $cart_item_data;
  }

  // Restore from session
  public static function get_cart_item_from_session($cart_item, $values)
  {
    if (isset($values['wcdp_dimensions'])) {
      $cart_item['wcdp_dimensions'] = $values['wcdp_dimensions'];
    }
    if (isset($values['wcdp_calculated_price'])) {
      $cart_item['wcdp_calculated_price'] = $values['wcdp_calculated_price'];
    }
    return $cart_item;
  }

  // Recalcule le prix pour chaque item du panier avant totals
  public static function update_cart_prices($cart)
  {
    if (is_admin() && ! defined('DOING_AJAX')) {
      return;
    }

    foreach ($cart->get_cart() as $cart_item_key => $cart_item) {
      $product = $cart_item['data'];
      $base_price = $product->get_price();

      // Si le prix calculé est déjà envoyé depuis le front, on garde le HT
      if (isset($cart_item['wcdp_calculated_price']) && $cart_item['wcdp_calculated_price'] > 0) {
        $prix_ht = floatval($cart_item['wcdp_calculated_price']);
      } elseif (isset($cart_item['wcdp_dimensions'])) {
        // Sinon, on le recalcule côté serveur
        $result = wcdp_calculate_price($base_price, $cart_item['wcdp_dimensions']);
        $prix_ht = $result['ht'];
      } else {
        continue;
      }

      // Appliquer le prix HT à WooCommerce
      if ($prix_ht > 0) {
        $cart_item['data']->set_price($prix_ht);
      }
    }
  }



  // Affichage des meta dans le panier
  public static function display_cart_item_meta($item_data, $cart_item)
  {
    if (isset($cart_item['wcdp_dimensions']) && is_array($cart_item['wcdp_dimensions'])) {
      foreach ($cart_item['wcdp_dimensions'] as $k => $v) {
        if ($v !== '' && $v !== null) {
          $item_data[] = [
            'key'   => 'Dim ' . $k,
            'value' => esc_html($v),
          ];
        }
      }
    }
    return $item_data;
  }

  // Sauvegarde des meta dans la commande
  public static function add_order_item_meta( $item_id, $values, $cart_item_key ) {
    if ( isset( $values['wcdp_dimensions'] ) && is_array( $values['wcdp_dimensions'] ) ) {
        foreach ( $values['wcdp_dimensions'] as $k => $v ) {
            if ( $v !== '' && $v !== null ) {
                wc_add_order_item_meta( $item_id, 'Dim ' . $k, $v . ' mm' );
            }
        }
    }

    if ( isset( $values['wcdp_calculated_price'] ) ) {
        wc_add_order_item_meta( $item_id, 'Prix calculé (HT)', $values['wcdp_calculated_price'] . ' €' );
    }
}

}