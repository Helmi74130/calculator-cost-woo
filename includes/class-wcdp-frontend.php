<?php
if (! defined('ABSPATH')) {
  exit;
}

class WCDP_Frontend
{

  public static function enqueue_scripts()
  {
    if (! is_product()) {
      return;
    }

    wp_enqueue_style('wcdp-style', WCDP_URL . 'assets/css/wcdp-style.css');
    wp_enqueue_script('wcdp-script', WCDP_URL . 'assets/js/wcdp-script.js', ['jquery'], '1.1.0', true);

    wp_localize_script('wcdp-script', 'WCDP_Data', [
      'ajax_url' => admin_url('admin-ajax.php'),
      'nonce'    => wp_create_nonce('wcdp_nonce'),
      'i18n'     => [
        'calculated_price' => __('Prix calculé :', 'wcdp'),
        'invalid'          => __('Dimensions invalides', 'wcdp'),
      ],
    ]);
  }


  public static function display_fields() {
    $labels = [
        'A' => 'Cote A (mm)',
        'B' => 'Cote B (mm)',
        'C' => 'Cote C (mm)',
        'D' => 'Cote D (mm)',
        'E' => 'Cote E (mm)',
        'F' => 'Longueur (mm)',
    ];

    $html = '<div class="wcdp-dimensions" id="wcdp-dimensions">';
    foreach ( $labels as $k => $label ) {
        $html .= '<p class="wcdp-field">';
        $html .= '<label for="wcdp_' . $k . '">' . esc_html( $label ) . '</label>';
        $html .= '<input type="number" step="any" min="0" name="wcdp_' . $k . '" id="wcdp_' . $k . '" class="wcdp-input" />';
        $html .= '</p>';
    }
    $html .= '<p id="wcdp-price-display" style="font-weight:600;margin:10px 0;"></p>';
    $html .= '<input type="hidden" name="wcdp_calculated_price" id="wcdp_calculated_price" value="" />';
    $html .= '</div>';

    echo $html;
}

}