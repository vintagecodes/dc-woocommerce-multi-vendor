<?php

class WCMp_Settings_Vendor_General {

    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    private $tab;
    private $subsection;

    /**
     * Start up
     */
    public function __construct($tab, $subsection) {
        $this->tab = $tab;
        $this->subsection = $subsection;
        $this->options = get_option("wcmp_{$this->tab}_{$this->subsection}_settings_name");
        $this->settings_page_init();
    }

    /**
     * Register and add settings
     */
    public function settings_page_init() {
        global $WCMp;
        $pages = get_pages();
        $woocommerce_pages = array(wc_get_page_id('shop'), wc_get_page_id('cart'), wc_get_page_id('checkout'), wc_get_page_id('myaccount'));
        $pages_array = array();
        if($pages){
            foreach ($pages as $page) {
                if (!in_array($page->ID, $woocommerce_pages)) {
                    $pages_array[$page->ID] = $page->post_title;
                }
            }
        }
        $settings_tab_options = array("tab" => "{$this->tab}",
            "ref" => &$this,
            "subsection" => "{$this->subsection}",
            "sections" => array(
                "wcmp_pages_section" => array("title" => __('WCMp Pages', 'dc-woocommerce-multi-vendor'), // Section one
                    "fields" => array(
                        "wcmp_vendor" => array('title' => __('Vendor Dashboard', 'dc-woocommerce-multi-vendor'), 'type' => 'select', 'id' => 'wcmp_vendor', 'label_for' => 'wcmp_vendor', 'name' => 'wcmp_vendor', 'options' => $pages_array, 'hints' => __('Choose your preferred page for vendor dashboard', 'dc-woocommerce-multi-vendor')), // Select
                        "vendor_registration" => array('title' => __('Vendor Registration', 'dc-woocommerce-multi-vendor'), 'type' => 'select', 'id' => 'vendor_registration', 'label_for' => 'vendor_registration', 'name' => 'vendor_registration', 'options' => $pages_array, 'hints' => __('Choose your preferred page for vendor registration', 'dc-woocommerce-multi-vendor')), // Select
                    ),
                ),
                "wcmp_vendor_general_settings_endpoint_ssection" => array(
                    "title" => __("WCMp Vendor Dashboard Endpoints", 'dc-woocommerce-multi-vendor')
                    , "fields" => array(
                        'wcmp_vendor_announcements_endpoint' => array('title' => __('Vendor Announcements', 'dc-woocommerce-multi-vendor'), 'type' => 'text', 'id' => 'wcmp_vendor_announcements_endpoint', 'label_for' => 'wcmp_vendor_announcements_endpoint', 'name' => 'wcmp_vendor_announcements_endpoint', 'hints' => __('Set endpoint for vendor announcements page', 'dc-woocommerce-multi-vendor'), 'placeholder' => 'vendor-announcements'),
                        'wcmp_store_settings_endpoint' => array('title' => __('Storefront', 'dc-woocommerce-multi-vendor'), 'type' => 'text', 'id' => 'wcmp_store_settings_endpoint', 'label_for' => 'wcmp_store_settings_endpoint', 'name' => 'wcmp_store_settings_endpoint', 'hints' => __('Set endpoint for shopfront page', 'dc-woocommerce-multi-vendor'), 'placeholder' => 'storefront'),
                        'wcmp_profile_endpoint' => array('title' => __('Vendor Profile', 'dc-woocommerce-multi-vendor'), 'type' => 'text', 'id' => 'wcmp_profile_endpoint', 'label_for' => 'wcmp_profile_endpoint', 'name' => 'wcmp_profile_endpoint', 'hints' => __('Set endpoint for vendor profile management page', 'dc-woocommerce-multi-vendor'), 'placeholder' => 'profile'),
                        'wcmp_vendor_policies_endpoint' => array('title' => __('Vendor Policies', 'dc-woocommerce-multi-vendor'), 'type' => 'text', 'id' => 'wcmp_vendor_policies_endpoint', 'label_for' => 'wcmp_vendor_policies_endpoint', 'name' => 'wcmp_vendor_policies_endpoint', 'hints' => __('Set endpoint for vendor policies page', 'dc-woocommerce-multi-vendor'), 'placeholder' => 'vendor-policies'),
                        'wcmp_vendor_billing_endpoint' => array('title' => __('Vendor Billing', 'dc-woocommerce-multi-vendor'), 'type' => 'text', 'id' => 'wcmp_vendor_billing_endpoint', 'label_for' => 'wcmp_vendor_billing_endpoint', 'name' => 'wcmp_vendor_billing_endpoint', 'hints' => __('Set endpoint for vendor billing page', 'dc-woocommerce-multi-vendor'), 'placeholder' => 'vendor-billing'),
                        'wcmp_vendor_shipping_endpoint' => array('title' => __('Vendor Shipping', 'dc-woocommerce-multi-vendor'), 'type' => 'text', 'id' => 'wcmp_vendor_shipping_endpoint', 'label_for' => 'wcmp_vendor_shipping_endpoint', 'name' => 'wcmp_vendor_shipping_endpoint', 'hints' => __('Set endpoint for vendor shipping page', 'dc-woocommerce-multi-vendor'), 'placeholder' => 'vendor-shipping'),
                        'wcmp_vendor_report_endpoint' => array('title' => __('Vendor Report', 'dc-woocommerce-multi-vendor'), 'type' => 'text', 'id' => 'wcmp_vendor_report_endpoint', 'label_for' => 'wcmp_vendor_report_endpoint', 'name' => 'wcmp_vendor_report_endpoint', 'hints' => __('Set endpoint for vendor report page', 'dc-woocommerce-multi-vendor'), 'placeholder' => 'vendor-report'),
                        'wcmp_vendor_banking_overview_endpoint' => array('title' => __('Banking Overview', 'dc-woocommerce-multi-vendor'), 'type' => 'text', 'id' => 'wcmp_vendor_banking_overview_endpoint', 'label_for' => 'wcmp_vendor_banking_overview_endpoint', 'name' => 'wcmp_vendor_banking_overview_endpoint', 'hints' => __('Set endpoint for vendor banking overview page', 'dc-woocommerce-multi-vendor'), 'placeholder' => 'banking-overview'),
                        
                        'wcmp_add_product_endpoint' => array('title' => __('Add Product', 'dc-woocommerce-multi-vendor'), 'type' => 'text', 'id' => 'wcmp_add_product_endpoint', 'label_for' => 'wcmp_add_product_endpoint', 'name' => 'wcmp_add_product_endpoint', 'hints' => __('Set endpoint for add new product page', 'dc-woocommerce-multi-vendor'), 'placeholder' => 'add-product'),
                        'wcmp_edit_product_endpoint' => array('title' => __('Edit Product', 'dc-woocommerce-multi-vendor'), 'type' => 'text', 'id' => 'wcmp_edit_product_endpoint', 'label_for' => 'wcmp_edit_product_endpoint', 'name' => 'wcmp_edit_product_endpoint', 'hints' => __('Set endpoint for edit product page', 'dc-woocommerce-multi-vendor'), 'placeholder' => 'edit-product'),
                        'wcmp_products_endpoint' => array('title' => __('Products List', 'dc-woocommerce-multi-vendor'), 'type' => 'text', 'id' => 'wcmp_products_endpoint', 'label_for' => 'wcmp_products_endpoint', 'name' => 'wcmp_products_endpoint', 'hints' => __('Set endpoint for products list page', 'dc-woocommerce-multi-vendor'), 'placeholder' => 'products'),
                        'wcmp_add_coupon_endpoint' => array('title' => __('Add Coupon', 'dc-woocommerce-multi-vendor'), 'type' => 'text', 'id' => 'wcmp_add_coupon_endpoint', 'label_for' => 'wcmp_add_coupon_endpoint', 'name' => 'wcmp_add_coupon_endpoint', 'hints' => __('Set endpoint for add new coupon page', 'dc-woocommerce-multi-vendor'), 'placeholder' => 'add-coupon'),
                        'wcmp_coupons_endpoint' => array('title' => __('Coupons List', 'dc-woocommerce-multi-vendor'), 'type' => 'text', 'id' => 'wcmp_coupons_endpoint', 'label_for' => 'wcmp_coupons_endpoint', 'name' => 'wcmp_coupons_endpoint', 'hints' => __('Set endpoint for coupons list page', 'dc-woocommerce-multi-vendor'), 'placeholder' => 'coupons'),
                        
                        "wcmp_vendor_orders_endpoint" => array('title' => __('Vendor Orders', 'dc-woocommerce-multi-vendor'), 'type' => 'text', 'id' => 'wcmp_vendor_orders_endpoint', 'label_for' => 'wcmp_vendor_orders_endpoint', 'name' => 'wcmp_vendor_orders_endpoint', 'hints' => __('Set endpoint for vendor orders page', 'dc-woocommerce-multi-vendor'), 'placeholder' => 'vendor-orders'),
                        'wcmp_vendor_withdrawal_endpoint' => array('title' => __('Vendor Widthdrawals', 'dc-woocommerce-multi-vendor'), 'type' => 'text', 'id' => 'wcmp_vendor_withdrawal_endpoint', 'label_for' => 'wcmp_vendor_withdrawal_endpoint', 'name' => 'wcmp_vendor_withdrawal_endpoint', 'hints' => __('Set endpoint for vendor widthdrawals page', 'dc-woocommerce-multi-vendor'), 'placeholder' => 'vendor-withdrawal'),
                        'wcmp_transaction_details_endpoint' => array('title' => __('Transaction Details', 'dc-woocommerce-multi-vendor'), 'type' => 'text', 'id' => 'wcmp_transaction_details_endpoint', 'label_for' => 'wcmp_transaction_details_endpoint', 'name' => 'wcmp_transaction_details_endpoint', 'hints' => __('Set endpoint for transaction details page', 'dc-woocommerce-multi-vendor'), 'placeholder' => 'transaction-details'),
                        'wcmp_vendor_knowledgebase_endpoint' => array('title' => __('Vendor Knowledgebase', 'dc-woocommerce-multi-vendor'), 'type' => 'text', 'id' => 'wcmp_vendor_knowledgebase_endpoint', 'label_for' => 'wcmp_vendor_knowledgebase_endpoint', 'name' => 'wcmp_vendor_knowledgebase_endpoint', 'hints' => __('Set endpoint for vendor knowledgebase page', 'dc-woocommerce-multi-vendor'), 'placeholder' => 'vendor-knowledgebase'),
                        'wcmp_vendor_tools_endpoint' => array('title' => __('Vendor Tools', 'dc-woocommerce-multi-vendor'), 'type' => 'text', 'id' => 'wcmp_vendor_tools_endpoint', 'label_for' => 'wcmp_vendor_tools_endpoint', 'name' => 'wcmp_vendor_tools_endpoint', 'hints' => __('Set endpoint for vendor tools page', 'dc-woocommerce-multi-vendor'), 'placeholder' => 'vendor-tools'),
                        'wcmp_vendor_products_qnas_endpoint' => array('title' => __('Vendor Products Q&As', 'dc-woocommerce-multi-vendor'), 'type' => 'text', 'id' => 'wcmp_vendor_products_qnas_endpoint', 'label_for' => 'wcmp_vendor_products_qnas_endpoint', 'name' => 'wcmp_vendor_products_qnas_endpoint', 'hints' => __('Set endpoint for vendor products Q&As page', 'dc-woocommerce-multi-vendor'), 'placeholder' => 'products-qna'),
                    )
                )
            ),
        );

        $WCMp->admin->settings->settings_field_withsubtab_init(apply_filters("settings_{$this->tab}_{$this->subsection}_tab_options", $settings_tab_options));
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function wcmp_vendor_general_settings_sanitize($input) {
        global $WCMp;
        $new_input = array();
        $hasError = false;
        
        if(isset($input['wcmp_vendor'])){
            $new_input['wcmp_vendor'] = $input['wcmp_vendor'];
        }
        if(isset($input['vendor_registration'])){
            $new_input['vendor_registration'] = $input['vendor_registration'];
        }
        if (isset($input['wcmp_vendor_announcements_endpoint']) && !empty($input['wcmp_vendor_announcements_endpoint'])) {
            $new_input['wcmp_vendor_announcements_endpoint'] = sanitize_text_field($input['wcmp_vendor_announcements_endpoint']);
        }
        if (isset($input['wcmp_store_settings_endpoint']) && !empty($input['wcmp_store_settings_endpoint'])) {
            $new_input['wcmp_store_settings_endpoint'] = sanitize_text_field($input['wcmp_store_settings_endpoint']);
        }
        if (isset($input['wcmp_profile_endpoint']) && !empty($input['wcmp_profile_endpoint'])) {
            $new_input['wcmp_profile_endpoint'] = sanitize_text_field($input['wcmp_profile_endpoint']);
        }
        if (isset($input['wcmp_vendor_billing_endpoint']) && !empty($input['wcmp_vendor_billing_endpoint'])) {
            $new_input['wcmp_vendor_billing_endpoint'] = sanitize_text_field($input['wcmp_vendor_billing_endpoint']);
        }
        if (isset($input['wcmp_vendor_policies_endpoint']) && !empty($input['wcmp_vendor_policies_endpoint'])) {
            $new_input['wcmp_vendor_policies_endpoint'] = sanitize_text_field($input['wcmp_vendor_policies_endpoint']);
        }
        if (isset($input['wcmp_vendor_shipping_endpoint']) && !empty($input['wcmp_vendor_shipping_endpoint'])) {
            $new_input['wcmp_vendor_shipping_endpoint'] = sanitize_text_field($input['wcmp_vendor_shipping_endpoint']);
        }
        if (isset($input['wcmp_vendor_report_endpoint']) && !empty($input['wcmp_vendor_report_endpoint'])) {
            $new_input['wcmp_vendor_report_endpoint'] = sanitize_text_field($input['wcmp_vendor_report_endpoint']);
        }
        if (isset($input['wcmp_vendor_banking_overview_endpoint']) && !empty($input['wcmp_vendor_banking_overview_endpoint'])) {
            $new_input['wcmp_vendor_banking_overview_endpoint'] = sanitize_text_field($input['wcmp_vendor_banking_overview_endpoint']);
        }
        if (isset($input['wcmp_vendor_orders_endpoint']) && !empty($input['wcmp_vendor_orders_endpoint'])) {
            $new_input['wcmp_vendor_orders_endpoint'] = sanitize_text_field($input['wcmp_vendor_orders_endpoint']);
        }
        if (isset($input['wcmp_vendor_withdrawal_endpoint']) && !empty($input['wcmp_vendor_withdrawal_endpoint'])) {
            $new_input['wcmp_vendor_withdrawal_endpoint'] = sanitize_text_field($input['wcmp_vendor_withdrawal_endpoint']);
        }
        if (isset($input['wcmp_transaction_details_endpoint']) && !empty($input['wcmp_transaction_details_endpoint'])) {
            $new_input['wcmp_transaction_details_endpoint'] = sanitize_text_field($input['wcmp_transaction_details_endpoint']);
        }
        if (isset($input['wcmp_vendor_knowledgebase_endpoint']) && !empty($input['wcmp_vendor_knowledgebase_endpoint'])) {
            $new_input['wcmp_vendor_knowledgebase_endpoint'] = sanitize_text_field($input['wcmp_vendor_knowledgebase_endpoint']);
        }
        if (isset($input['wcmp_vendor_tools_endpoint']) && !empty($input['wcmp_vendor_tools_endpoint'])) {
            $new_input['wcmp_vendor_tools_endpoint'] = sanitize_text_field($input['wcmp_vendor_tools_endpoint']);
        }
        
        if (isset($input['wcmp_add_product_endpoint']) && !empty($input['wcmp_add_product_endpoint'])) {
            $new_input['wcmp_add_product_endpoint'] = sanitize_text_field($input['wcmp_add_product_endpoint']);
        }
        if (isset($input['wcmp_edit_product_endpoint']) && !empty($input['wcmp_edit_product_endpoint'])) {
            $new_input['wcmp_edit_product_endpoint'] = sanitize_text_field($input['wcmp_edit_product_endpoint']);
        }
        if (isset($input['wcmp_products_endpoint']) && !empty($input['wcmp_products_endpoint'])) {
            $new_input['wcmp_products_endpoint'] = sanitize_text_field($input['wcmp_products_endpoint']);
        }
        if (isset($input['wcmp_add_coupon_endpoint']) && !empty($input['wcmp_add_coupon_endpoint'])) {
            $new_input['wcmp_add_coupon_endpoint'] = sanitize_text_field($input['wcmp_add_coupon_endpoint']);
        }
        if (isset($input['wcmp_coupons_endpoint']) && !empty($input['wcmp_coupons_endpoint'])) {
            $new_input['wcmp_coupons_endpoint'] = sanitize_text_field($input['wcmp_coupons_endpoint']);
        }
        if (isset($input['wcmp_vendor_products_qnas_endpoint']) && !empty($input['wcmp_vendor_products_qnas_endpoint'])) {
            $new_input['wcmp_vendor_products_qnas_endpoint'] = sanitize_text_field($input['wcmp_vendor_products_qnas_endpoint']);
        }
        if (!$hasError) {
            add_settings_error(
                    "wcmp_{$this->tab}_{$this->subsection}_settings_name", esc_attr("wcmp_{$this->tab}_{$this->subsection}_settings_admin_updated"), __('Vendor Settings Updated', 'dc-woocommerce-multi-vendor'), 'updated'
            );
        }
        flush_rewrite_rules();
        return apply_filters("settings_{$this->tab}_{$this->subsection}_tab_new_input", $new_input, $input);
    }

}
