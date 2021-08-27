<?php

class WCMp_Settings_Commission_Variation {

    /**
     * Holds the values to be used in the fields callbacks
     */
    private $tab;
    private $subsection;

    /**
     * Start up
     */
    public function __construct( $tab, $subsection ) {
        $this->tab = $tab;
        $this->subsection = $subsection;
        $this->settings_page_init();
    }

    /**
     * Register and add settings
     */
    public function settings_page_init() {
        global $WCMp;
        $wcmp_variation_commission_options = get_option( 'wcmp_variation_commission_options', array() );
        if ( $WCMp->vendor_caps->payment_cap['commission_type'] && $WCMp->vendor_caps->payment_cap['commission_type'] == 'commission_by_product_price') {
            $vendor_commission_by_products = is_array($wcmp_variation_commission_options) && isset( $wcmp_variation_commission_options['vendor_commission_by_products'] ) ? $wcmp_variation_commission_options['vendor_commission_by_products'] : array();
            $WCMp->wcmp_wp_fields->dc_generate_form_field( apply_filters( 'wcmp_menu_manager_fields', array(
                "vendor_commission_by_products" => array('label' => __('Commission By Product Price', 'dc-woocommerce-multi-vendor'), 'type' => 'multiinput', 'class' => 'wcmp-text wcmp_ele commission_mode_field commission_mode_by_products', 'label_class' => 'wcmp_title wcmp_ele wcmp_full_title commission_mode_field commission_mode_by_products', 'desc_class' => 'commission_mode_field commission_mode_by_products instructions', 'value' => $vendor_commission_by_products, 'desc' => sprintf( __( 'Commission rules depending upon product price. e.g 80&#37; commission when product cost < %s1000, %s100 fixed commission when product cost > %s1000 and so on. You may define any number of such rules. Please be sure, <b> do not set conflicting rules.</b>', 'dc-woocommerce-multi-vendor' ), get_woocommerce_currency_symbol(), get_woocommerce_currency_symbol(), get_woocommerce_currency_symbol() ),  'options' => array( 
                    "cost" => array('label' => __('Product Cost', 'dc-woocommerce-multi-vendor'), 'type' => 'number', 'class' => 'wcmp-text wcmp_non_negative_input wcmp_ele', 'label_class' => 'wcmp_title wcmp_ele', 'attributes' => array( 'min' => '0.1', 'step' => '0.1') ),
                    "rule" => array('label' => __('Rule', 'dc-woocommerce-multi-vendor'), 'type' => 'select', 'class' => 'wcmp-select wcmp_ele', 'label_class' => 'wcmp_title wcmp_ele', 'options' => array( 'upto' => __( 'Up to', 'dc-woocommerce-multi-vendor' ), 'greater'   => __( 'More than', 'dc-woocommerce-multi-vendor' ) ) ),
                    "type" => array('label' => __('Commission Type', 'dc-woocommerce-multi-vendor'), 'type' => 'select', 'class' => 'wcmp-select wcmp_ele', 'label_class' => 'wcmp_title wcmp_ele', 'options' => array( 'percent' => __( 'Percent', 'dc-woocommerce-multi-vendor' ), 'fixed'   => __( 'Fixed', 'dc-woocommerce-multi-vendor' ), 'percent_fixed' => __( 'Percent + Fixed', 'dc-woocommerce-multi-vendor' ) ) ),
                    "commission" => array('label' => __('Commission Percent(%)', 'dc-woocommerce-multi-vendor'), 'type' => 'number', 'placeholder' => 0, 'class' => 'wcmp-text wcmp_non_negative_input wcmp_ele', 'label_class' => 'wcmp_title wcmp_ele', 'attributes' => array( 'min' => '0.1', 'step' => '0.1') ),
                    "commission_fixed" => array('label' => __('Commission Fixed', 'dc-woocommerce-multi-vendor') . '(' . get_woocommerce_currency_symbol() . ')', 'type' => 'number', 'placeholder' => 0, 'class' => 'wcmp-text wcmp_non_negative_input wcmp_ele', 'label_class' => 'wcmp_title wcmp_ele', 'attributes' => array( 'min' => '0.1', 'step' => '0.1') ),
                ) ),
            ) ) );
        } elseif ($WCMp->vendor_caps->payment_cap['commission_type'] == 'commission_by_purchase_quantity') {
            $vendor_commission_by_quantity = is_array($wcmp_variation_commission_options) && isset( $wcmp_variation_commission_options['vendor_commission_by_quantity'] ) ? $wcmp_variation_commission_options['vendor_commission_by_quantity'] : array();
            $WCMp->wcmp_wp_fields->dc_generate_form_field( apply_filters( 'wcmp_menu_manager_fields', array(
                "vendor_commission_by_quantity" => array('label' => __('Commission By Purchase Quantity', 'dc-woocommerce-multi-vendor'), 'name' => 'commission[commission_by_quantity]', 'type' => 'multiinput', 'class' => 'wcmp-text wcmp_ele commission_mode_field commission_mode_by_quantity', 'label_class' => 'wcmp_title wcmp_ele wcmp_full_title commission_mode_field commission_mode_by_quantity', 'desc_class' => 'commission_mode_field commission_mode_by_quantity instructions', 'value' => $vendor_commission_by_quantity, 'desc' => __( 'Commission rules depending upon purchased product quantity. e.g 80&#37; commission when purchase quantity 2, 80&#37; commission when purchase quantity > 2 and so on. You may define any number of such rules. Please be sure, do not set conflicting rules.', 'dc-woocommerce-multi-vendor' ),  'options' => array( 
                    "quantity" => array('label' => __('Purchase Quantity', 'dc-woocommerce-multi-vendor'), 'type' => 'number', 'class' => 'wcmp-text wcmp_non_negative_input wcmp_ele', 'label_class' => 'wcmp_title wcmp_ele', 'attributes' => array( 'min' => '1', 'step' => '1') ),
                    "rule" => array('label' => __('Rule', 'dc-woocommerce-multi-vendor'), 'type' => 'select', 'class' => 'wcmp-select wcmp_ele', 'label_class' => 'wcmp_title wcmp_ele', 'options' => array( 'upto' => __( 'Up to', 'dc-woocommerce-multi-vendor' ), 'greater'   => __( 'More than', 'dc-woocommerce-multi-vendor' ) ) ),
                    "type" => array('label' => __('Commission Type', 'dc-woocommerce-multi-vendor'), 'type' => 'select', 'class' => 'wcmp-select wcmp_ele', 'label_class' => 'wcmp_title wcmp_ele', 'options' => array( 'percent' => __( 'Percent', 'dc-woocommerce-multi-vendor' ), 'fixed'   => __( 'Fixed', 'dc-woocommerce-multi-vendor' ), 'percent_fixed' => __( 'Percent + Fixed', 'dc-woocommerce-multi-vendor' ) ) ),
                    "commission" => array('label' => __('Commission Percent(%)', 'dc-woocommerce-multi-vendor'), 'type' => 'number', 'placeholder' => 0, 'class' => 'wcmp-text wcmp_non_negative_input wcmp_ele', 'label_class' => 'wcmp_title wcmp_ele', 'attributes' => array( 'min' => '0.1', 'step' => '0.1') ),
                    "commission_fixed" => array('label' => __('Commission Fixed', 'dc-woocommerce-multi-vendor') . '(' . get_woocommerce_currency_symbol() . ')', 'type' => 'number', 'placeholder' => 0, 'class' => 'wcmp-text wcmp_non_negative_input wcmp_ele', 'label_class' => 'wcmp_title wcmp_ele', 'attributes' => array( 'min' => '0.1', 'step' => '0.1') ),
                ) ) ) ) );
        }
        ?>
        <button class="button button-primary vendor-update-btn wcmp-primary-btn" id="wcmp_vendor_submit_commission" name="wcmp_vendor_submit_commission" value="update"><?php esc_html_e('Update', 'dc-woocommerce-multi-vendor'); ?></button>
        <?php
    }
}