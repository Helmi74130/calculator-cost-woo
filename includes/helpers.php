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
 */
function wcdp_calculate_price( $base_price, $dimensions = [] ) {
    $A = isset( $dimensions['A'] ) ? floatval( $dimensions['A'] ) : 0;
    $B = isset( $dimensions['B'] ) ? floatval( $dimensions['B'] ) : 0;
    $C = isset( $dimensions['C'] ) ? floatval( $dimensions['C'] ) : 0;
    $D = isset( $dimensions['D'] ) ? floatval( $dimensions['D'] ) : 0;
    $E = isset( $dimensions['E'] ) ? floatval( $dimensions['E'] ) : 0;
    $F = isset( $dimensions['F'] ) ? floatval( $dimensions['F'] ) : 0; // Longueur

    // Calcul du développé et de la surface
    $developpe = $A + $B + $C + $D + $E;
    $surface_m2 = ($developpe * $F) / 1000000; // conversion mm² → m²

    // Prix HT et TTC
    $prix_ht = $surface_m2 * floatval( $base_price );
    $prix_ttc = $prix_ht * 1.2;

    // On retourne les deux valeurs, mais WooCommerce utilise le HT
    return [
        'ht'  => round( max( 0, $prix_ht ), 2 ),
        'ttc' => round( max( 0, $prix_ttc ), 2 ),
    ];
}