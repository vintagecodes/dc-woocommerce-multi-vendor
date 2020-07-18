<?php
/**
 * Vendor Product search form.
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/widget/vendor-product-searchform.php
 *
 * @author 		WC Marketplace
 * @package 	dc-product-vendor/Templates
 * @version     3.5.4
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<form role="search" method="get" class="wcmp-vproduct-search woocommerce-product-search" action="">
	<label class="screen-reader-text" for="woocommerce-product-search-field-<?php echo isset( $index ) ? absint( $index ) : 0; ?>"><?php esc_html_e( 'Search for:', 'woocommerce' ); ?></label>
	<input type="search" id="woocommerce-product-search-field-<?php echo isset( $index ) ? absint( $index ) : 0; ?>" class="search-field" placeholder="<?php echo esc_attr__( 'Search products&hellip;', 'woocommerce' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />
	<button type="submit" value="<?php echo esc_attr_x( 'Search', 'submit button', 'woocommerce' ); ?>"><?php echo esc_html_x( 'Search', 'submit button', 'woocommerce' ); ?></button>
	<input type="hidden" name="post_type" value="product" />
</form>