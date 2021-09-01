<?php

class WCMp_Settings_Payment {

    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    private $tab;
    private $automatic_payment_method;
    private $withdrawl_order_status;

    /**
     * Start up
     */
    public function __construct($tab) {
        $this->tab = $tab;
        $this->options = get_option("wcmp_{$this->tab}_settings_name");
        $this->settings_page_init();
    }

    /**
     * Register and add settings
     */
    public function settings_page_init() {
        global $WCMp;

        $this->automatic_payment_method = apply_filters('automatic_payment_method', array('paypal_masspay' => __('PayPal Masspay (Stop Waiting and Pay Vendors Immediately with PayPal Real-Time Split Payment using <a href="https://wc-marketplace.com/product/wcmp-paypal-marketplace/">WCMp PayPal Marketplace</a>. Please visit our site)', 'dc-woocommerce-multi-vendor'), 'paypal_payout' => __('Paypal Payout', 'dc-woocommerce-multi-vendor'), 'stripe_masspay' => __('Stripe Connect', 'dc-woocommerce-multi-vendor'), 'direct_bank' => __('Direct Bank Transfer', 'dc-woocommerce-multi-vendor'), 'razorpay_block' => __('Razorpay', 'dc-woocommerce-multi-vendor')));
        $automatic_method = array();
        $gateway_charge = array();
        $i = 0;
        $desabled_payment_shipping = get_wcmp_vendor_settings( 'is_vendor_shipping_on', 'general' ) && 'Enable' === get_wcmp_vendor_settings( 'is_vendor_shipping_on', 'general' ) ? '' : array('disabled' => 'disabled');
        foreach ($this->automatic_payment_method as $key => $val) {
            if ($i == 0) {
                $automatic_method['payment_method_' . $key] = array('title' => __('Allowed Payment Methods', 'dc-woocommerce-multi-vendor'), 'type' => 'checkbox', 'id' => 'payment_method_' . $key, 'class' => 'automatic_payment_method', 'label_for' => 'payment_method_' . $key, 'text' => $val, 'name' => 'payment_method_' . $key, 'value' => 'Enable', 'data-display-label' => $val);
            } else if ($key == 'direct_bank') {
                $automatic_method['payment_method_' . $key] = array('title' => __('', 'dc-woocommerce-multi-vendor'), 'type' => 'checkbox', 'id' => 'payment_method_' . $key, 'class' => 'automatic_payment_method', 'label_for' => 'payment_method_' . $key, 'text' => $val, 'name' => 'payment_method_' . $key, 'value' => 'Enable', 'data-display-label' => $val);
            } else if ($key == 'razorpay_block') {
                $automatic_method['payment_method_' . $key] = array('title' => __('', 'dc-woocommerce-multi-vendor'), 'custom_tags'=> array('disabled' => 'disabled'), 'type' => 'checkbox', 'id' => 'payment_method_' . $key, 'class' => 'automatic_payment_method', 'label_for' => 'payment_method_' . $key, 'text' => __('For use <a href="https://wordpress.org/plugins/wcmp-razorpay-split-payment/">Razorpay</a> please visit our site.', 'dc-woocommerce-multi-vendor'), 'name' => 'payment_method_' . $key, 'value' => 'Enable', 'data-display-label' => $val);
            } else {
                $automatic_method['payment_method_' . $key] = array('title' => __('', 'dc-woocommerce-multi-vendor'), 'type' => 'checkbox', 'id' => 'payment_method_' . $key, 'class' => 'automatic_payment_method', 'label_for' => 'payment_method_' . $key, 'text' => $val, 'name' => 'payment_method_' . $key, 'value' => 'Enable', 'data-display-label' => $val);
            }
            $gateway_charge['gateway_charge_' . $key] = array('title' => __('', 'dc-woocommerce-multi-vendor'), 'type' => 'text', 'id' => 'gateway_charge_' . $key, 'class' => 'payment_gateway_charge regular-text', 'label_for' => 'gateway_charge_' . $key, 'name' => 'gateway_charge_' . $key, 'placeholder' => __('For ', 'dc-woocommerce-multi-vendor') . $val, 'desc' => __('Gateway Charge For ', 'dc-woocommerce-multi-vendor') . $val );
            $gateway_charge['gateway_charge_fixed_with_' . $key] = array('title' => __('', 'dc-woocommerce-multi-vendor'), 'type' => 'text', 'id' => 'gateway_charge_fixed_with_' . $key, 'class' => 'payment_gateway_charge regular-text', 'label_for' => 'gateway_charge_fixed_with_' . $key, 'name' => 'gateway_charge_fixed_with_' . $key, 'placeholder' => __('For ', 'dc-woocommerce-multi-vendor') . $val, 'desc' => __('Gateway Charge For ', 'dc-woocommerce-multi-vendor') . $val );
            $i++;
        }

        $this->withdrawl_order_status = apply_filters('withdrawl_order_status', array('on-hold' => __('On hold', 'dc-woocommerce-multi-vendor'), 'processing' => __('Processing', 'dc-woocommerce-multi-vendor'), 'completed' => __('Completed', 'dc-woocommerce-multi-vendor')));
        $withdrawl_status = array();
        $status_i = 0;
        foreach ($this->withdrawl_order_status as $key => $val) {
            if ($status_i == 0) {
                $withdrawl_status['order_withdrawl_status' . $key] = array('title' => __('Restrict Order Status from Withdrawl', 'dc-woocommerce-multi-vendor'), 'type' => 'checkbox', 'id' => 'order_withdrawl_status' . $key, 'class' => 'withdrawl_order_status', 'label_for' => 'order_withdrawl_status' . $key, 'text' => $val, 'name' => 'order_withdrawl_status' . $key, 'value' => $key, 'data-display-label' => $val);
            } else {
                $withdrawl_status['order_withdrawl_status' . $key] = array('title' => __('', 'dc-woocommerce-multi-vendor'), 'type' => 'checkbox', 'id' => 'order_withdrawl_status' . $key, 'class' => 'withdrawl_order_status', 'label_for' => 'order_withdrawl_status' . $key, 'text' => $val, 'name' => 'order_withdrawl_status' . $key, 'value' => $key, 'data-display-label' => $val);
            }

            $status_i++;
        }

        $settings_tab_options = array("tab" => "{$this->tab}",
            "ref" => &$this,
            "sections" => array(
                "revenue_sharing_mode_section" => array("title" => __('Revenue Sharing Mode', 'dc-woocommerce-multi-vendor'), // Section one
                    "fields" => array(
                        "revenue_sharing_mode" => array('title' => __('Mode ', 'dc-woocommerce-multi-vendor'), 'type' => 'radio', 'id' => 'revenue_sharing_mode', 'label_for' => 'revenue_sharing_mode', 'name' => 'revenue_sharing_mode', 'dfvalue' => 'vendor', 'options' => array('admin' => __('Admin fees', 'dc-woocommerce-multi-vendor'), 'vendor' => __('Vendor commissions', 'dc-woocommerce-multi-vendor')), 'desc' => ''), // Radio
                    ),
                ),
                "what_to_pay_section" => array("title" => __('What to Pay', 'dc-woocommerce-multi-vendor'), // Section one
                    "fields" => array(
                        "commission_type" => array('title' => __('Commission Type', 'dc-woocommerce-multi-vendor'), 'type' => 'select', 'id' => 'commission_typee', 'label_for' => 'commission_typee', 'name' => 'commission_type', 'options' => wcmp_get_available_commission_types(array('' => __('Choose Commission Type', 'dc-woocommerce-multi-vendor'))), 'desc' => __('Choose your preferred commission type. It will affect all commission calculations.', 'dc-woocommerce-multi-vendor')), // Select
                        "default_commission" => array('title' => __('Commission Value', 'dc-woocommerce-multi-vendor'), 'type' => 'text', 'id' => 'default_commissionn', 'label_for' => 'default_commissionn', 'name' => 'default_commission', 'desc' => __('This will be the default commission(in percentage or fixed) paid to vendors if product and vendor-specific commission is not set. ', 'dc-woocommerce-multi-vendor')), // Text
                        "default_percentage" => array('title' => __('Commission Percentage', 'dc-woocommerce-multi-vendor'), 'type' => 'text', 'id' => 'default_percentage', 'label_for' => 'default_percentage', 'name' => 'default_percentage', 'desc' => __('This will be the default percentage paid to vendors if product and vendor specific commission is not set. ', 'dc-woocommerce-multi-vendor')), // Text
                        "fixed_with_percentage" => array('title' => __('Fixed Amount', 'dc-woocommerce-multi-vendor'), 'type' => 'text', 'id' => 'fixed_with_percentage', 'label_for' => 'fixed_with_percentage', 'name' => 'fixed_with_percentage', 'desc' => __('Fixed (per transaction)', 'dc-woocommerce-multi-vendor')), // Text
                        "fixed_with_percentage_qty" => array('title' => __('Fixed Amount', 'dc-woocommerce-multi-vendor'), 'type' => 'text', 'id' => 'fixed_with_percentage_qty', 'label_for' => 'fixed_with_percentage_qty', 'name' => 'fixed_with_percentage_qty', 'desc' => __('Fixed (per unit)', 'dc-woocommerce-multi-vendor')), // Text
                        "fixed_with_percentage_per_vendor" => array('title' => __('Fixed Amount', 'dc-woocommerce-multi-vendor'), 'type' => 'text', 'id' => 'fixed_with_percentage_per_vendor', 'label_for' => 'fixed_with_percentage_per_vendor', 'name' => 'fixed_with_percentage_per_vendor', 'desc' => __('Fixed (per vendor)', 'dc-woocommerce-multi-vendor')), // Text
                        "commission_include_coupon" => array('title' => __('Share Coupon Discount', 'dc-woocommerce-multi-vendor'), 'type' => 'checkbox', 'id' => 'commission_include_couponn', 'label_for' => 'commission_include_couponn', 'text' => __('Vendors commission will be calculated AFTER deducting the discount, otherwise, the site owner will bear the cost of the coupon.', 'dc-woocommerce-multi-vendor'), 'name' => 'commission_include_coupon', 'value' => 'Enable'), // Checkbox
                        "give_tax" => array('title' => __('Tax', 'dc-woocommerce-multi-vendor'), 'type' => 'checkbox', 'id' => 'give_taxx', 'label_for' => 'give_taxx', 'name' => 'give_tax', 'text' => __('Transfer the tax collected (per product) to the vendor. ', 'dc-woocommerce-multi-vendor'), 'value' => 'Enable'), // Checkbox
                        "give_shipping" => array('title' => __('Shipping', 'dc-woocommerce-multi-vendor'), 'type' => 'checkbox', 'id' => 'give_shippingg', 'custom_tags'=> $desabled_payment_shipping, 'label_for' => 'give_shippingg', 'name' => 'give_shipping', 'text' => __('Transfer shipping charges collected (per product) to the vendor.', 'dc-woocommerce-multi-vendor'), 'value' => 'Enable', 'hints' => __('Make sure your generel settings shipping is on', 'dc-woocommerce-multi-vendor')), // Checkbox
                        "commission_threshold" => array('title' => __('Disbursement Threshold', 'dc-woocommerce-multi-vendor'), 'type' => 'text', 'id' => 'commission_threshold', 'label_for' => 'commission_threshold', 'name' => 'commission_threshold', 'desc' => __('Threshold amount required to disburse commission.', 'dc-woocommerce-multi-vendor')), // Text
                        "commission_threshold_time" => array('title' => __('Withdrawal Locking Period', 'dc-woocommerce-multi-vendor'), 'type' => 'number', 'id' => 'commission_threshold_time', 'label_for' => 'commission_threshold_time', 'name' => 'commission_threshold_time', 'desc' => __('Minimum time required before an individual commission is ready for withdrawal.', 'dc-woocommerce-multi-vendor'), 'placeholder' => __('in days', 'dc-woocommerce-multi-vendor')), // Text
                    ),
                ),
                "wcmp_default_settings_section" => array("title" => __('How/When to Pay ', 'dc-woocommerce-multi-vendor'), // Section one
                    "fields" => array_merge($automatic_method, array(
                        "payment_gateway_charge" => array('title' => __('Payment Gateway Charge', 'dc-woocommerce-multi-vendor'), 'type' => 'checkbox', 'id' => 'payment_gateway_charge', 'label_for' => 'payment_gateway_charge', 'name' => 'payment_gateway_charge', 'text' => __('If checked, you can set payment gateway charge to the vendor for commission disbursement. ', 'dc-woocommerce-multi-vendor'), 'value' => 'Enable'),
                        "gateway_charges_cost_carrier" => array('title' => __('Who bear the gateway charges', 'dc-woocommerce-multi-vendor'), 'type' => 'select', 'id' => 'gateway_charges_cost_carrier', 'label_for' => 'gateway_charges_cost_carrier', 'name' => 'gateway_charges_cost_carrier', 'options' => array('vendor' => __('Vendor', 'dc-woocommerce-multi-vendor'), 'admin' => __('Site owner', 'dc-woocommerce-multi-vendor'), 'separate' => __('Separately', 'dc-woocommerce-multi-vendor')), 'desc' => __('Choose your preferred gateway charges carrier.', 'dc-woocommerce-multi-vendor')), // Select
                        "payment_gateway_charge_type" => array('title' => __('Gateway Charge Type', 'dc-woocommerce-multi-vendor'), 'type' => 'select', 'id' => 'payment_gateway_charge_type', 'label_for' => 'payment_gateway_charge_type', 'name' => 'payment_gateway_charge_type', 'options' => array('percent' => __('Percentage', 'dc-woocommerce-multi-vendor'), 'fixed' => __('Fixed Amount', 'dc-woocommerce-multi-vendor'), 'fixed_with_percentage' => __('%age + Fixed', 'dc-woocommerce-multi-vendor')), 'desc' => __('Choose your preferred gateway charge type.', 'dc-woocommerce-multi-vendor')), // Select
                            ), $gateway_charge, array(
                        "choose_payment_mode_automatic_disbursal" => array('title' => __('Disbursement Schedule', 'dc-woocommerce-multi-vendor'), 'type' => 'checkbox', 'id' => 'wcmp_disbursal_mode_admin', 'label_for' => 'wcmp_disbursal_mode_admin', 'name' => 'wcmp_disbursal_mode_admin', 'text' => __('If checked, automatically vendors commission will disburse. ', 'dc-woocommerce-multi-vendor'), 'value' => 'Enable'), // Checkbox
                        "payment_schedule" => array('title' => __('Set Schedule', 'dc-woocommerce-multi-vendor'), 'type' => 'radio', 'id' => 'payment_schedule', 'label_for' => 'payment_schedule', 'name' => 'payment_schedule', 'dfvalue' => 'daily', 'options' => array('weekly' => __('Weekly', 'dc-woocommerce-multi-vendor'), 'daily' => __('Daily', 'dc-woocommerce-multi-vendor'), 'monthly' => __('Monthly', 'dc-woocommerce-multi-vendor'), 'fortnightly' => __('Fortnightly', 'dc-woocommerce-multi-vendor'), 'hourly' => __('Hourly', 'dc-woocommerce-multi-vendor'))), // Radio
                            ), array("choose_payment_mode_request_disbursal" => array('title' => __('Withdrawal Request', 'dc-woocommerce-multi-vendor'), 'type' => 'checkbox', 'id' => 'wcmp_disbursal_mode_vendor', 'label_for' => 'wcmp_disbursal_mode_vendor', 'name' => 'wcmp_disbursal_mode_vendor', 'text' => __('Vendors can request for commission withdrawal. ', 'dc-woocommerce-multi-vendor'), 'value' => 'Enable'), // Checkbox                                                                            
                        "commission_transfer" => array('title' => __('Withdrawal Charges', 'dc-woocommerce-multi-vendor'), 'type' => 'text', 'id' => 'commission_transfer', 'label_for' => 'commission_transfer', 'name' => 'commission_transfer', 'desc' => __('Vendors will be charged this amount per withdrawal after the quota of free withdrawals is over.', 'dc-woocommerce-multi-vendor')), // Text
                        "no_of_orders" => array('title' => __('Number of Free Withdrawals', 'dc-woocommerce-multi-vendor'), 'type' => 'number', 'id' => 'no_of_orders', 'label_for' => 'no_of_orders', 'name' => 'no_of_orders', 'desc' => __('Number of free withdrawal requests.', 'dc-woocommerce-multi-vendor')), // Text                                                                                                          
                            ), $withdrawl_status
                    ),
                ),
            ),
        );

        $WCMp->admin->settings->settings_field_init(apply_filters("settings_{$this->tab}_tab_options", $settings_tab_options));
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function wcmp_payment_settings_sanitize($input) {
        $new_input = array();
        $hasError = false;
        if (isset($input['revenue_sharing_mode'])) {
            $new_input['revenue_sharing_mode'] = sanitize_text_field($input['revenue_sharing_mode']);
        }
        if (isset($input['is_mass_pay'])) {
            $new_input['is_mass_pay'] = sanitize_text_field($input['is_mass_pay']);
        }
        if (isset($input['default_commission'])) {
            $new_input['default_commission'] = floatval(sanitize_text_field($input['default_commission']));
        }
        if (isset($input['default_percentage'])) {
            $new_input['default_percentage'] = floatval(sanitize_text_field($input['default_percentage']));
        }
        if (isset($input['fixed_with_percentage_qty'])) {
            $new_input['fixed_with_percentage_qty'] = floatval(sanitize_text_field($input['fixed_with_percentage_qty']));
        }
        if (isset($input['fixed_with_percentage_per_vendor'])) {
            $new_input['fixed_with_percentage_per_vendor'] = floatval(sanitize_text_field($input['fixed_with_percentage_per_vendor']));
        }
        if (isset($input['fixed_with_percentage'])) {
            $new_input['fixed_with_percentage'] = floatval(sanitize_text_field($input['fixed_with_percentage']));
        }
        if (isset($input['commission_threshold'])) {
            $new_input['commission_threshold'] = floatval(sanitize_text_field($input['commission_threshold']));
        }
        if (isset($input['commission_threshold_time'])) {
            $new_input['commission_threshold_time'] = intval(sanitize_text_field($input['commission_threshold_time']));
        }
        if (isset($input['commission_transfer'])) {
            $new_input['commission_transfer'] = floatval(sanitize_text_field($input['commission_transfer']));
        }
        if (isset($input['no_of_orders'])) {
            $new_input['no_of_orders'] = intval(sanitize_text_field($input['no_of_orders']));
        }
        if (isset($input['commission_type'])) {
            $new_input['commission_type'] = sanitize_text_field($input['commission_type']);
        }
        if (isset($input['commission_include_coupon'])) {
            $new_input['commission_include_coupon'] = sanitize_text_field($input['commission_include_coupon']);
        }
        if (isset($input['give_tax'])) {
            $new_input['give_tax'] = sanitize_text_field($input['give_tax']);
        }
        if (isset($input['give_shipping'])) {
            $new_input['give_shipping'] = sanitize_text_field($input['give_shipping']);
        }
        if (isset($input['wcmp_disbursal_mode_admin'])) {
            $new_input['wcmp_disbursal_mode_admin'] = sanitize_text_field($input['wcmp_disbursal_mode_admin']);
        }
        if (isset($input['wcmp_disbursal_mode_vendor'])) {
            $new_input['wcmp_disbursal_mode_vendor'] = sanitize_text_field($input['wcmp_disbursal_mode_vendor']);
        }
        foreach ($this->automatic_payment_method as $key => $val) {
            if (isset($input['payment_method_' . $key])) {
                $new_input['payment_method_' . $key] = sanitize_text_field($input['payment_method_' . $key]);
            }
            if (isset($input['gateway_charge_' . $key])) {
                $new_input['gateway_charge_' . $key] = floatval(sanitize_text_field($input['gateway_charge_' . $key]));
            }
            if (isset($input['gateway_charge_fixed_with_' . $key])) {
                $new_input['gateway_charge_fixed_with_' . $key] = floatval(sanitize_text_field($input['gateway_charge_fixed_with_' . $key]));
            }
        }
        foreach ($this->withdrawl_order_status as $key => $val) {
            if (isset($input['order_withdrawl_status' . $key])) {
                $new_input['order_withdrawl_status' . $key] = sanitize_text_field($input['order_withdrawl_status' . $key]);
            }
        }
        if (isset($input['payment_schedule'])) {
            $new_input['payment_schedule'] = $input['payment_schedule'];
        }
        if (isset($input['wcmp_disbursal_mode_admin'])) {
            $schedule = wp_get_schedule('masspay_cron_start');
            if ($schedule != $input['payment_schedule']) {
                if (wp_next_scheduled('masspay_cron_start')) {
                    $timestamp = wp_next_scheduled('masspay_cron_start');
                    wp_unschedule_event($timestamp, 'masspay_cron_start');
                }
                wp_schedule_event(time(), $input['payment_schedule'], 'masspay_cron_start');
            }
        } else {
            if (wp_next_scheduled('masspay_cron_start')) {
                $timestamp = wp_next_scheduled('masspay_cron_start');
                wp_unschedule_event($timestamp, 'masspay_cron_start');
            }
        }
        if (isset($input['payment_gateway_charge'])) {
            $new_input['payment_gateway_charge'] = sanitize_text_field($input['payment_gateway_charge']);
        }
        if(isset($input['payment_gateway_charge_type'])){
            $new_input['payment_gateway_charge_type'] = sanitize_text_field($input['payment_gateway_charge_type']);
        }
        if(isset($input['gateway_charges_cost_carrier'])){
            $new_input['gateway_charges_cost_carrier'] = sanitize_text_field($input['gateway_charges_cost_carrier']);
        }
        if (!$hasError) {
            add_settings_error(
                    "wcmp_{$this->tab}_settings_name", esc_attr("wcmp_{$this->tab}_settings_admin_updated"), __('Payment Settings Updated', 'dc-woocommerce-multi-vendor'), 'updated'
            );
        }
        return apply_filters("settings_{$this->tab}_tab_new_input", $new_input, $input);
    }

    /**
     * Print the Section text
     */
//    public function wcmp_default_settings_section_info() {
//        global $WCMp;
//        _e('Payment can be done only if vendors have valid PayPal Email Id in their profile. You can add from [Users->Edit Users->PayPal Email]', 'dc-woocommerce-multi-vendor');
//    }

    /**
     * Print the Section text
     */
    public function revenue_sharing_mode_section_info() {
        echo '<div class="wcmp_payment_help">';
        _e("If you are not sure about how to setup commissions and payment options in your marketplace, kindly read this <a href='https://wc-marketplace.com/knowledgebase/setting-up-commission-and-other-payments-for-wcmp/' terget='_blank'>article</a> before proceeding.", 'dc-woocommerce-multi-vendor');
        echo '</div>';
        ?>
        <style type="text/css">
             .wcmp_payment_help {
                display: inline-block;
                padding: 10px;
                background: #ffffff;
                color: #333;
                font-style: italic;
                max-width: 300px;
                position: absolute;
                right: 20px;
                z-index: 9;
            }
            @media (max-width: 960px){
                .wcmp_payment_help {
                    position: relative;
                    right: auto;
                }
            }
        </style>
        <?php

    }

}
