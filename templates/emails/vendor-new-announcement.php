<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/emails/vendor-new-announcement.php
 *
 * @author      WC Marketplace
 * @package     dc-product-vendor/Templates
 * @version   0.0.1
 */
if (!defined('ABSPATH'))
    exit; // Exit if accessed directly 
global $WCMp;
do_action( 'woocommerce_email_header', $email_heading, $email );
$text_align = is_rtl() ? 'right' : 'left';
?>

<p><?php printf(esc_html__('%s', 'dc-woocommerce-multi-vendor'),  $post_title); ?></p>

<?php $announcement_link = esc_url(wcmp_get_vendor_dashboard_endpoint_url(get_wcmp_vendor_settings('wcmp_vendor_announcements_endpoint', 'vendor', 'general', 'vendor-announcements'))); ?>

<p><?php printf(esc_html__('This is to inform you that we recently updated the article %s :', 'dc-woocommerce-multi-vendor'), $post_title); ?></p>

<p><?php printf(esc_html__('Vendor Name: %s', 'dc-woocommerce-multi-vendor'), $vendor->page_title); ?></p>

<?php printf(apply_filters('wcmp_announcement_content', $post_content)); ?>

<p><?php printf(esc_html__('You can always check the changes from here  %s. We would request you to check the same and take the necessary action if required.', 'dc-woocommerce-multi-vendor'), $announcement_link ); ?></p>

<p><?php printf(esc_html__('%s continued use of the Store, will be subject to the updated terms.', 'dc-woocommerce-multi-vendor'), $single); ?></p>

<?php do_action('wcmp_email_footer'); ?>