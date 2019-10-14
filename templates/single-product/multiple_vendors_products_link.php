<?php
/**
 * Single Product Multiple vendors
 *
 * This template can be overridden by copying it to yourtheme/dc-product-vendor/single-product/multiple_vendors_products_link.php.
 *
 * HOWEVER, on occasion WCMp will need to update template files and you (the theme developer).
 * will need to copy the new files to your theme to maintain compatibility. We try to do this.
 * as little as possible, but it does happen. When this occurs the version of the template file will.
 * be bumped and the readme will list any important changes.
 *
 * 
 * @author  WC Marketplace
 * @package dc-woocommerce-multi-vendor/Templates
 * @version 2.3.4
 */
global $WCMp, $product, $wpdb; 

$more_products = get_wcmp_more_spmv_products( $product->get_id() );
if ( count( $more_products ) >= 1 ) {
    $button_text = apply_filters( 'wcmp_more_vendors', __('More Vendors', 'dc-woocommerce-multi-vendor') );
    $button_text = apply_filters( 'wcmp_single_product_more_vendors_text', $button_text, $product );
    echo '<a  href="#" class="goto_more_offer_tab button">' . $button_text . '</a>';
}

