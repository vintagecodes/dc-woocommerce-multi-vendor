<?php

class WCMp_Settings_Vendor_Registration {

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
        ?>
<h4><?php echo __('Setting panel to add extra fields in vendor registration page, along with the','dc-woocommerce-multi-vendor'); ?> <a href="<?php echo admin_url('admin.php').'?page=wc-settings&tab=account'; ?>"><?php echo __('WooCommerce registration form','dc-woocommerce-multi-vendor'); ?></a></h4>
        <div id="nav-menus-frame" ng-app="vendor_registration">
            <div id="menu-settings-column" class="metabox-holder" ng-controller="postbox_menu">
                <div id="side-sortables" class="meta-box-sortables ui-sortable">
                    <div class="postbox" ng-class="postboxClass">
                        <div class="postbox-header">
                            <h3 class="hndle ui-sortable-handle">
                                <span><?php echo __('Form Fields','dc-woocommerce-multi-vendor'); ?></span>
                            </h3>
                            <button ng-click="togglePostbox()" type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Toggle panel: Format</span><span class="toggle-indicator" aria-hidden="true"></span></button>
                        </div>
                        <div class="inside">
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('textbox', 'Text Box', $event)" class="button-secondary"><?php echo __('Textbox','dc-woocommerce-multi-vendor'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('email', 'Email', $event)" class="button-secondary"><?php echo __('Email','dc-woocommerce-multi-vendor'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('url', 'Url', $event)" class="button-secondary"><?php echo __('Url','dc-woocommerce-multi-vendor'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('textarea', 'Text Area', $event)" class="button-secondary"><?php echo __('Textarea','dc-woocommerce-multi-vendor'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('selectbox', 'Select Box', $event)" class="button-secondary"><?php echo __('List','dc-woocommerce-multi-vendor'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('checkbox', 'Checkbox', $event)" class="button-secondary"><?php echo __('Checkbox','dc-woocommerce-multi-vendor'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('recaptcha', 'Recaptcha', $event)" class="button-secondary"><?php echo __('Recaptcha','dc-woocommerce-multi-vendor'); ?></a>
                            </p>    
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('file', 'Attachment', $event)" class="button-secondary"><?php echo __('Attachment','dc-woocommerce-multi-vendor'); ?></a>
                            </p> 
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('separator', 'Section', $event)" class="button-secondary"><?php echo __('Section','dc-woocommerce-multi-vendor'); ?></a>
                            </p>
                        </div>
                    </div>
                </div>
                <div id="side-sortables" class="meta-box-sortables ui-sortable">
                    <div class="postbox" ng-class="vendorStoreFieldClass">
                        <div class="postbox-header">
                            <h3 class="hndle ui-sortable-handle">
                                <span><?php echo __('Vendor Store Fields','dc-woocommerce-multi-vendor'); ?></span>
                            </h3>
                            <button ng-click="togglevendorStoreField()" type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Toggle panel: Format</span><span class="toggle-indicator" aria-hidden="true"></span></button>
                        </div>
                        <div class="inside">
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('vendor_description', 'Store Description', $event)" class="button-secondary"><?php echo __('Store Description','dc-woocommerce-multi-vendor'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('vendor_address_1', 'Address 1', $event)" class="button-secondary"><?php echo __('Address 1','dc-woocommerce-multi-vendor'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('vendor_address_2', 'Address 2', $event)" class="button-secondary"><?php echo __('Address 2','dc-woocommerce-multi-vendor'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('vendor_phone', 'Phone', $event)" class="button-secondary"><?php echo __('Phone','dc-woocommerce-multi-vendor'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('vendor_country', 'Country', $event)" class="button-secondary"><?php echo __('Country','dc-woocommerce-multi-vendor'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('vendor_state', 'State', $event)" class="button-secondary"><?php echo __('State','dc-woocommerce-multi-vendor'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('vendor_city', 'City', $event)" class="button-secondary"><?php echo __('City','dc-woocommerce-multi-vendor'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('vendor_postcode', 'PostCode', $event)" class="button-secondary"><?php echo __('Postcode','dc-woocommerce-multi-vendor'); ?></a>
                            </p>
                            <p class="button-controls">
                                <a href="#" ng-click="addFormField('vendor_paypal_email', 'Paypal Email', $event)" class="button-secondary"><?php echo __('PayPal Email','dc-woocommerce-multi-vendor'); ?></a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div id="poststuff" ng-controller="postbox_content">
                <div id="post-body">
                    <div id="post-body-content">
                        <div id="wcmp-vendor-form">
                            <input type="button" value="Save" ng-click="saveFormData()" class="button-primary menu-save">
                            <a disabled="" ng-show="showSaveSpinner" class="button-secondary" href="#"><span style="visibility: visible; float: left;" class="spinner"></span></a>
                            
                            <div ng-if="fields.length === 0" class="wcmp-form-empty-container"><?php echo __('Build your form here','dc-woocommerce-multi-vendor'); ?></div>
                            
                            <ul class="meta-box-sortables" ui-sortable="fieldSortableOptions" ng-model="fields">
                                <li ng-repeat="(parentIndex,field) in fields track by $index">
                                    <div class="postbox" ng-class="{'closed' : field.hidden }">
                                        <div class="postbox-header">
                                            <h3 class="hndle ui-sortable-handle" ng-dblclick="togglePostboxField($index)">
                                                <span>{{field.label}}</span>
                                            </h3>
                                            <button ng-click="togglePostboxField($index)" type="button" class="handlediv" aria-expanded="true"><span class="screen-reader-text">Toggle panel: Format</span><span class="toggle-indicator" aria-hidden="true"></span></button>
                                        </div>
                                        <div class="inside">
                                            <div id="post-formats-select">
                                                <div ng-include src="partialUrl+field.partial"></div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            </ul>
                            <input type="button" value="Save" ng-click="saveFormData()" class="button-primary menu-save">
                            <a disabled="" ng-show="showSaveSpinner" class="button-secondary" href="#"><span style="visibility: visible; float: left;" class="spinner"></span></a>
                            <h4><?php printf(__('Use %s, %s, %s CSS class to customize the form','dc-woocommerce-multi-vendor'), '[wcmp-regi-12]', '[wcmp-regi-6]', '[wcmp-regi-4]'); ?></h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
}
