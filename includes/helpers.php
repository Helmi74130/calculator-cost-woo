<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Calcul du prix d'une pièce en fonction des dimensions et du prix au m².
 * Hypothèse : les dimensions (A à F) sont en mm.
 * Formule :
 *  - Développé = A + B + C + D + E
 *  - Longueur = F
 *  - Surface (m²) = (Développé x Longueur) / 1 000 000
 *  - Prix HT = Surface * prix_m2
 *  - Prix TTC = Prix HT * 1.2
 *
 * Règle spéciale si produit non en stock ET surface < 4.5m² :
 *  - Prix HT = max(prix_calculé, prix_m2 * 4.5) + 60
 */
function wcdp_calculate_price( $base_price, $dimensions = [], $product = null ) {
    $A = isset( $dimensions['A'] ) ? floatval( $dimensions['A'] ) : 0;
    $B = isset( $dimensions['B'] ) ? floatval( $dimensions['B'] ) : 0;
    $C = isset( $dimensions['C'] ) ? floatval( $dimensions['C'] ) : 0;
    $D = isset( $dimensions['D'] ) ? floatval( $dimensions['D'] ) : 0;
    $E = isset( $dimensions['E'] ) ? floatval( $dimensions['E'] ) : 0;
    $F = isset( $dimensions['F'] ) ? floatval( $dimensions['F'] ) : 0; // Longueur

    // Calcul du développé et de la surface
    $developpe = $A + $B + $C + $D + $E;
    $surface_m2 = ($developpe * $F) / 1000000; // conversion mm² → m²

    // Prix HT et TTC de base
    $prix_ht = $surface_m2 * floatval( $base_price );

    // Vérifier la règle spéciale stock + surface < 4.5m²
    $apply_special_rule = false;
    if ( $product && is_a( $product, 'WC_Product' ) ) {
        $is_in_stock = $product->is_in_stock();
        $backorders_allowed = $product->backorders_allowed();

        // Si produit NON en stock ET surface < 4.5m²
        if ( ! $is_in_stock && ! $backorders_allowed && $surface_m2 < 4.5 ) {
            $apply_special_rule = true;
        }
    }

    if ( $apply_special_rule ) {
        // Prix minimum basé sur 4.5m² + surcoût de 60€
        $prix_minimum = floatval( $base_price ) * 4.5;
        $prix_ht = max( $prix_ht, $prix_minimum ) + 60;
    }

    $prix_ttc = $prix_ht * 1.2;

    // On retourne les deux valeurs, mais WooCommerce utilise le HT
    return [
        'ht'  => round( max( 0, $prix_ht ), 2 ),
        'ttc' => round( max( 0, $prix_ttc ), 2 ),
        'surface_m2' => $surface_m2,
        'special_rule_applied' => $apply_special_rule,
    ];
}