<?php

class WCMp_Settings_Payment_Razorpay {

    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    private $tab;
    private $subsection;
    private $key_id = '';
    private $key_secret = '';

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
        $settings_tab_options = array("tab" => "{$this->tab}",
            "ref" => &$this,
            "subsection" => "{$this->subsection}",
            "sections" => array(
                "wcmp_payment_razorpay_payout_settings_section" => array("title" => __('Razorpay setting', 'dc-woocommerce-multi-vendor'),
                    "fields" => array(
                        "key_id" => array(
                            'title' => __('Key ID', 'dc-woocommerce-multi-vendor'),
                            'type' => 'text',
                            'id' => 'key_id',
                            'label_for' => 'key_id',
                            'name' => 'key_id',
                            'dfvalue' => $this->key_id
                        ),
                        "key_secret" => array(
                            'title' => __('Key Secret', 'dc-woocommerce-multi-vendor'),
                            'type' => 'text',
                            'id' => 'key_secret',
                            'label_for' => 'key_secret',
                            'name' => 'key_secret',
                            'dfvalue' => $this->key_secret
                        ),
                    ),
                )
            ),
        );

        $WCMp->admin->settings->settings_field_withsubtab_init(
            apply_filters("settings_{$this->tab}_{$this->subsection}_tab_options", $settings_tab_options)
        );
    }

    /**
     * Sanitize each setting field as needed
     *
     * @param array $input Contains all settings fields as array keys
     */
    public function wcmp_payment_razorpay_payout_settings_sanitize($input) {
        $new_input = array();
        $hasError = false;
        if (isset($input['key_id'])) {
            $new_input['key_id'] = sanitize_text_field($input['key_id']);
        }
        if (isset($input['key_secret'])) {
            $new_input['key_secret'] = sanitize_text_field($input['key_secret']);
        }
        if (!$hasError) {
            add_settings_error(
                    "wcmp_{$this->tab}_{$this->subsection}_settings_name",
                    esc_attr("wcmp_{$this->tab}_{$this->subsection}_settings_admin_updated"),
                    __('Razorpay Payout Settings Updated', 'dc-woocommerce-multi-vendor'),
                    'updated'
            );
        }
        return apply_filters("settings_{$this->tab}_{$this->subsection}_tab_new_input", $new_input, $input);
    }
}
