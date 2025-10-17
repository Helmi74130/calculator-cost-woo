<?php
/**
 * Template part for the dimensions fields. If you want to override, copy to yourtheme/woocommerce/single-product/
 */
?>
<div id="wcdp-dimensions" class="wcdp-dimensions">
  <?php foreach ( range('A','F') as $k ) : ?>
  <p class="wcdp-field">
    <label for="wcdp_<?php echo esc_attr($k); ?>">Dimension <?php echo esc_html($k); ?></label>
    <input type="number" step="any" min="0" name="wcdp_<?php echo esc_attr($k); ?>"
      id="wcdp_<?php echo esc_attr($k); ?>" class="wcdp-input" />
  </p>
  <?php endforeach; ?>

  <p id="wcdp-price-display" style="font-weight:600;margin:10px 0;"></p>
  <input type="hidden" name="wcdp_calculated_price" id="wcdp_calculated_price" value="" />
</div>