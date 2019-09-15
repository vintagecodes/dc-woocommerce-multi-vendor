<?php

/**
 * WCMp Vendor Dashboard Shortcode Class
 *
 * @version		2.2.0
 * @package		WCMp/shortcode
 * @author 		WC Marketplace
 */
class WCMp_Vendor_Dashboard_Shortcode {

    public function __construct() {
        
    }

    /**
     * Output the vendor dashboard shortcode.
     *
     * @access public
     * @param array $atts
     * @return void
     */
    public static function output($attr) {
        global $WCMp, $wp;
        $WCMp->nocache();
        if (!defined('WCMP_DASHBAOARD')) {
            define('WCMP_DASHBAOARD', true);
        }
        if (!is_user_logged_in()) {
            if (( 'no' === get_option('woocommerce_registration_generate_password') && !is_user_logged_in())) {
                wp_enqueue_script('wc-password-strength-meter');
            }
            echo '<div class="wcmp-dashboard woocommerce">';
            wc_get_template('myaccount/form-login.php');
            echo '</div>';
        } else if (!is_user_wcmp_vendor(get_current_vendor_id())) {
        	$user = wp_get_current_user();
        	
        	if ($user && in_array('dc_pending_vendor', $user->roles)) {
        		$WCMp->template->get_template('shortcode/pending_vendor_dashboard.php');
        	} else if ($user && in_array('dc_rejected_vendor', $user->roles)) {
        		$WCMp->template->get_template('shortcode/rejected_vendor_dashboard.php');
        	} else {
        		$WCMp->template->get_template('shortcode/non_vendor_dashboard.php');
            }
        } else {
            do_action('wcmp_dashboard_setup');
            $WCMp->template->get_template('shortcode/vendor_dashboard.php');
        }
    }

}
