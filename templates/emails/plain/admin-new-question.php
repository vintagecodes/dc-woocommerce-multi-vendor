<?php
/**
 * The template for displaying demo plugin content.
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/emails/plain/admin-new-question.php
 *
 * @author 		WC Marketplace
 * @package 	dc-product-vendor/Templates
 * @version   0.0.1
 */
 
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}
global $WCMp;
$question = isset( $question ) ? $question : '';
echo $email_heading . "\n\n"; 
echo sprintf(  __( "Greetings Admin",  'dc-woocommerce-multi-vendor' ) ); 
echo '\n\n';
echo sprintf(  __( "A new query has added by your buyer - %s",  'dc-woocommerce-multi-vendor' ), $customer_name ); 
echo '\n';
echo sprintf(  __( "Query for : %s",  'dc-woocommerce-multi-vendor' ), $vendor->page_title );
echo '\n';
echo sprintf(  __( "Query : %s",  'dc-woocommerce-multi-vendor' ), $question );
echo '\n';
$question_link = apply_filters( 'admin_plain_question_redirect_link', admin_url( 'admin.php?page=wcmp-to-do' ) ); 
echo sprintf(  __( "You can approve or reject query from here : %s",  'dc-woocommerce-multi-vendor' ), $question_link );
echo '\n\n';
echo sprintf( __( 'Note: Quick replies help to maintain a friendly customer-buyer relationship', 'dc-woocommerce-multi-vendor'));

echo apply_filters( 'wcmp_email_footer_text', get_option( 'wcmp_email_footer_text' ) );