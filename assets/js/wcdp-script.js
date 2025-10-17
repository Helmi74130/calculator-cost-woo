(function($){
    $(document).ready(function(){

        let base_price = 0;

        function parseFloatSafe(v){
            var f = parseFloat(v);
            return isNaN(f) ? 0 : f;
        }

        function calculateAndShow() {
            // Récupération des valeurs (mm)
            let A = parseFloatSafe($('#wcdp_A').val());
            let B = parseFloatSafe($('#wcdp_B').val());
            let C = parseFloatSafe($('#wcdp_C').val());
            let D = parseFloatSafe($('#wcdp_D').val());
            let E = parseFloatSafe($('#wcdp_E').val());
            let F = parseFloatSafe($('#wcdp_F').val()); // longueur

            // Développé et surface (en m²)
            let developpe = A + B + C + D + E;
            let surface = (developpe * F) / 1000000; // conversion mm² -> m²

            // Calcul du prix
            let prix_ht = surface * base_price;
            let prix_ttc = prix_ht * 1.2;

            prix_ht = Math.round(prix_ht * 100) / 100;
            prix_ttc = Math.round(prix_ttc * 100) / 100;

            if (prix_ttc > 0) {
                $('#wcdp-price-display').html(
                    `<strong>${WCDP_Data.i18n.calculated_price}</strong> ${prix_ttc.toFixed(2)} € TTC`
                );
                // On envoie le prix HT à WooCommerce
                $('#wcdp_calculated_price').val(prix_ht);
            } else {
                $('#wcdp-price-display').text('');
                $('#wcdp_calculated_price').val('');
            }
        }

        // Recalcul sur saisie
        let timer;
        $('#wcdp-dimensions').on('input', '.wcdp-input', function(){
            clearTimeout(timer);
            timer = setTimeout(calculateAndShow, 200);
        });

        // --- Produit variable : mise à jour du prix selon variation ---
        $('form.cart').on('show_variation', function(event, variation){
            if (variation && variation.display_price) {
                base_price = parseFloatSafe(variation.display_price);
                calculateAndShow();
            }
        });

        $('form.cart').on('reset_data', function(){
            base_price = 0;
            $('#wcdp-price-display').text('');
            $('#wcdp_calculated_price').val('');
        });

        // --- Produit simple : prix initial ---
        if ($('.variations_form').length === 0) {
            let product_price = $('.summary .price').first().text().replace(/[^0-9.,]/g, '');
            base_price = parseFloatSafe(product_price);
        }

        calculateAndShow();
    });
})(jQuery);
