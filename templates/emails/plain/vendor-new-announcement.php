<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/emails/plain/vendor-new-announcement.php
 *
 * @author      WC Marketplace
 * @package     dc-product-vendor/Templates
 * @version   0.0.1
 */


if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly
global $WCMp;

echo $email_heading . "\n\n";

echo sprintf( __('%s', 'dc-woocommerce-multi-vendor'),  $post_title) . "\n\n";

$announcement_link = esc_url(wcmp_get_vendor_dashboard_endpoint_url(get_wcmp_vendor_settings('wcmp_vendor_announcements_endpoint', 'vendor', 'general', 'vendor-announcements')));

echo sprintf(__('This is to inform you that we recently updated the article %s :','dc-woocommerce-multi-vendor'), $post_title). "\n";

echo sprintf(__('Vendor Name: %s', 'dc-woocommerce-multi-vendor'), $vendor->page_title)."\n";

echo sprintf(apply_filters('wcmp_announcement_content', $post_content))."\n";

echo sprintf( __('You can always check the changes from here  %s. We would request you to check the same and take the necessary action if required.', 'dc-woocommerce-multi-vendor'), $announcement_link ) . "\n";

printf( __( "View the announcement: %s",  'dc-woocommerce-multi-vendor' ), esc_url(wcmp_get_vendor_dashboard_endpoint_url(get_wcmp_vendor_settings('wcmp_vendor_announcements_endpoint', 'vendor', 'general', 'vendor-announcements')))) . "\n";

printf( __('%s continued use of the Store, will be subject to the updated terms.', 'dc-woocommerce-multi-vendor'), $single );

echo apply_filters( 'wcmp_email_footer_text', get_option( 'wcmp_email_footer_text' ) ); 
