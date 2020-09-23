<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/emails/rejected-vendor-account.php
 *
 * @author 		WC Marketplace
 * @package 	dc-product-vendor/Templates
 * @version   0.0.1
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 
global $WCMp;
?>
<?php do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<p><?php printf( esc_html__( "Thanks for creating an account with us on %s. Unfortunately your request has been rejected.",  'dc-woocommerce-multi-vendor' ), esc_html( $blogname )); ?></p>
<p><?php printf( esc_html__( "You may contact the site admin at %s.",  'dc-woocommerce-multi-vendor' ), get_option('admin_email')); ?></p>

<?php do_action( 'wcmp_email_footer' ); ?>