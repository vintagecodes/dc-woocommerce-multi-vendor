<?php

/**
 * The Template for displaying vendor registration form.
 *
 * Override this template by copying it to yourtheme/dc-product-vendor/vendor_registration_form.php
 *
 * @author 		WC Marketplace
 * @package 	WCMp/Templates
 * @version   2.4.3
 */
global $WCMp;
if (!empty($wcmp_vendor_registration_form_data) && is_array($wcmp_vendor_registration_form_data)) {
	if(isset($_POST) && is_array($_POST) && count($_POST) > 0) $form_data = $_POST;
    $sep_count = 0;
    foreach ($wcmp_vendor_registration_form_data as $key => $value) {
        switch ($value['type']) {
            case 'separator':
                ?>
                <div class="clearboth"></div>
                </div>
                <div class="wcmp_regi_form_box">
                <h3 class="reg_header2"><?php echo __($value['label'],'dc-woocommerce-multi-vendor'); ?></h3>
                <?php
                break;
            case 'textbox':
                ?>
                <div class="wcmp-regi-form-row <?php if(!empty($value['cssClass'])){ echo $value['cssClass']; } else {  echo 'wcmp-regi-12'; } ?>">
                    <label><?php echo __($value['label'],'dc-woocommerce-multi-vendor'); ?><?php if($value['required']){ echo ' <span class="required">*</span>'; }?></label>
                    <input type="text" value="<?php if (!empty($form_data['wcmp_vendor_fields'][$key]["value"])) echo esc_attr($form_data['wcmp_vendor_fields'][$key]["value"]); ?>" name="wcmp_vendor_fields[<?php echo $key; ?>][value]" placeholder="<?php echo $value['placeholder']; ?>" <?php if($value['required']){ echo 'required="required"'; }?> />
                    <input type="hidden" name="wcmp_vendor_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="wcmp_vendor_fields[<?php echo $key; ?>][type]" value="textbox" />
                </div>
                <?php
                break;
            case 'email':
                ?>
                <div class="wcmp-regi-form-row <?php if(!empty($value['cssClass'])){ echo $value['cssClass']; } else {  echo 'wcmp-regi-12'; } ?>">
                    <label><?php echo __($value['label'],'dc-woocommerce-multi-vendor'); ?><?php if($value['required']){ echo ' <span class="required">*</span>'; }?></label>
                    <input type="email" value="<?php if (!empty($form_data['wcmp_vendor_fields'][$key]["value"])) echo esc_attr($form_data['wcmp_vendor_fields'][$key]["value"]); ?>" name="wcmp_vendor_fields[<?php echo $key; ?>][value]" placeholder="<?php echo $value['placeholder']; ?>" <?php if($value['required']){ echo 'required="required"'; }?> />
                    <input type="hidden" name="wcmp_vendor_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="wcmp_vendor_fields[<?php echo $key; ?>][type]" value="email" />
                </div>
                <?php
                break;
            case 'textarea':
                ?>
                <div class="wcmp-regi-form-row <?php if(!empty($value['cssClass'])){ echo $value['cssClass']; } else {  echo 'wcmp-regi-12'; } ?>">
                    <label><?php echo __($value['label'],'dc-woocommerce-multi-vendor'); ?><?php if($value['required']){ echo ' <span class="required">*</span>'; }?></label>
                    <textarea <?php if(!empty($value['limit'])){ echo 'maxlength="'.$value['limit'].'"'; } ?> name="wcmp_vendor_fields[<?php echo $key; ?>][value]" placeholder="<?php echo $value['defaultValue']; ?>" <?php if($value['required']){ echo 'required'; }?>><?php if (!empty($form_data['wcmp_vendor_fields'][$key]["value"])){ echo esc_attr($form_data['wcmp_vendor_fields'][$key]["value"]); } ?></textarea>
                    <input type="hidden" name="wcmp_vendor_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="wcmp_vendor_fields[<?php echo $key; ?>][type]" value="textarea" />
                </div>
                <?php
                break;
            case 'url': 
                ?>
                <div class="wcmp-regi-form-row <?php if(!empty($value['cssClass'])){ echo $value['cssClass']; } else {  echo 'wcmp-regi-12'; } ?>">
                    <label><?php echo __($value['label'],'dc-woocommerce-multi-vendor'); ?><?php if($value['required']){ echo ' <span class="required">*</span>'; }?></label>
                    <input type="url" value="<?php if (!empty($form_data['wcmp_vendor_fields'][$key]["value"])) echo esc_attr($form_data['wcmp_vendor_fields'][$key]["value"]); ?>" name="wcmp_vendor_fields[<?php echo $key; ?>][value]" placeholder="<?php echo $value['placeholder']; ?>" <?php if($value['required']){ echo 'required="required"'; }?> />
                    <input type="hidden" name="wcmp_vendor_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="wcmp_vendor_fields[<?php echo $key; ?>][type]" value="url" />
                </div>
                <?php
                break;
            case 'selectbox':
                ?>
                <div class="wcmp-regi-form-row <?php if(!empty($value['cssClass'])){ echo $value['cssClass']; } else {  echo 'wcmp-regi-12'; } ?>">
                    <label><?php echo __($value['label'],'dc-woocommerce-multi-vendor'); ?><?php if($value['required']){ echo ' <span class="required">*</span>'; }?></label>
                    <input type="hidden" name="wcmp_vendor_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="wcmp_vendor_fields[<?php echo $key; ?>][type]" value="selectbox" />
                    <?php
                     switch ($value['selecttype']){
                         case 'dropdown':
                            ?>
                            <select class="select_box" name="wcmp_vendor_fields[<?php echo $key; ?>][value]" <?php if($value['required']){ echo 'required="required"'; }?>>
                            <?php
                            if (!empty($value['options']) && is_array($value['options'])) {
                                foreach ($value['options'] as $option_key => $option_value) {
                                    ?>
                                    <option value="<?php echo $option_value['value']; ?>" <?php if($option_value['selected']){ echo 'selected="selected"'; } ?>><?php echo $option_value['label']; ?></option>
                                    <?php
                                }
                            }
                            ?>
                            </select>
                             <?php
                             break;
                         case 'radio':
                             if (!empty($value['options']) && is_array($value['options'])) {
                                ?>
                                <div class="wcmp-regi-radio-inp-holder">
                                <?php
                                foreach ($value['options'] as $option_key => $option_value) {
                                    ?>
                                    <p><input type="radio" <?php if($option_value['selected']){ echo 'checked="checked"'; } ?> name="wcmp_vendor_fields[<?php echo $key; ?>][value]" value="<?php echo $option_value['value']; ?>" <?php if($value['required']){ echo 'required="required"'; }?>> <?php echo $option_value['label']; ?></p>
                                    <?php
                                }
                                ?>
                                </div>
                                <?php
                            }
                             break;
                         case 'checkboxes':
                            if (!empty($value['options']) && is_array($value['options'])) {
                                foreach ($value['options'] as $option_key => $option_value) {
                                    ?>
                                    <p> <input type="checkbox" <?php if($option_value['selected']){ echo 'checked="checked"'; } ?> name="wcmp_vendor_fields[<?php echo $key; ?>][value][]" class="wcmp-regs-multi-check" value="<?php echo $option_value['value']; ?>" <?php //if($value['required']){ echo 'required="required"'; }?>> <?php echo $option_value['label']; ?></p>
                                    
                                    <?php
                                }
                            }
                            wp_add_inline_script('woocommerce', "(function ($) { 
                                $('.wcmp_regi_main .register').submit(function(e) {
                                    checked = $('.wcmp-regs-multi-check:checked').length;
                                    if(!checked) {
                                        e.preventDefault();
                                        $('.wcmp-regs-multi-check')[0].focus();
                                        return false;
                                    }
                                });
                            })(jQuery)");
                            break;
                         case 'multi-select':
                             ?>
                            <select class="select_box" style="min-height: 59px;" name="wcmp_vendor_fields[<?php echo $key; ?>][value][]" <?php if($value['required']){ echo 'required="required"'; }?> multiple="">
                            <?php
                            if (!empty($value['options']) && is_array($value['options'])) {
                                foreach ($value['options'] as $option_key => $option_value) {
                                    ?>
                                    <option value="<?php echo $option_value['value']; ?>" <?php if($option_value['selected']){ echo 'selected="selected"'; } ?>><?php echo $option_value['label']; ?></option>
                                    <?php
                                }
                            }
                            ?>
                            </select>
                            <?php
                            break;
                     }
                    ?>
                </div>
                <?php
                break;

            case 'checkbox':
                ?>
                <div class="wcmp-regi-form-row <?php if(!empty($value['cssClass'])){ echo $value['cssClass']; } else {  echo 'wcmp-regi-12'; } ?>">
                    <input type="checkbox" name="wcmp_vendor_fields[<?php echo $key; ?>][value]" <?php if (!empty($form_data['wcmp_vendor_fields'][$key]["value"]) && $form_data['wcmp_vendor_fields'][$key]["value"] == 'on') { echo 'checked="checked"';}?> <?php if(!isset($form_data['wcmp_vendor_fields'][$key]["value"]) && $value['defaultValue'] == 'checked'){ echo 'checked="checked"';} ?>  <?php if($value['required']){ echo 'required="required"'; }?> />
                    <label><?php echo __($value['label'],'dc-woocommerce-multi-vendor'); ?><?php if($value['required']){ echo ' <span class="required">*</span>'; }?></label>
                    <input type="hidden" name="wcmp_vendor_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="wcmp_vendor_fields[<?php echo $key; ?>][type]" value="checkbox" />
                </div>
                <?php
                break;
            case 'recaptcha':
                $recaptcha_type = $value['recaptchatype'];
                $sitekey = $value['sitekey'];
                $secretkey = $value['secretkey'];
                $script_url = ($recaptcha_type == 'v3') ? 'https://www.google.com/recaptcha/api.js?render='.$sitekey : 'https://www.google.com/recaptcha/api.js';
                ?>
                <script src="<?php echo $script_url; ?>"></script>
                <?php if($recaptcha_type == 'v3'): ?>
                <script>
                    grecaptcha.ready(function () {
                        grecaptcha.execute('<?php echo $sitekey; ?>', { action: 'wcmp_vendor_registration' }).then(function (token) {
                            var recaptchaResponse = document.getElementById('recaptchav3Response');
                            recaptchaResponse.value = token;
                        });
                    });
                </script>
                <?php endif; ?>
                <div class="wcmp-regi-form-row <?php if(!empty($value['cssClass'])){ echo $value['cssClass']; } else {  echo 'wcmp-regi-12'; } ?>">
                    <label><?php echo ($recaptcha_type == 'v2') ? __($value['label'],'dc-woocommerce-multi-vendor') : ''; ?><?php if($value['required']){ echo ' <span class="required">*</span>'; }?></label>
                    <?php echo $value['script']; ?>
                    <input type="hidden" name="g-recaptchatype" value="<?php echo $recaptcha_type; ?>" />
                    <input type="hidden" name="wcmp_vendor_fields[<?php echo $key; ?>][value]" value="Verified" />
                    <input type="hidden" name="wcmp_vendor_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="wcmp_vendor_fields[<?php echo $key; ?>][type]" value="recaptcha" />
                    <?php if($recaptcha_type == 'v3'): ?>
                    <input type="hidden" name="recaptchav3Response" id="recaptchav3Response" />
                    <input type="hidden" name="recaptchav3_sitekey" value="<?php echo $sitekey; ?>" />
                    <input type="hidden" name="recaptchav3_secretkey" value="<?php echo $secretkey; ?>" />
                    <?php endif; ?>
                </div>
                <?php
                break;
            case 'file':
                ?>
                <div class="wcmp-regi-form-row <?php if(!empty($value['cssClass'])){ echo $value['cssClass']; } else {  echo 'wcmp-regi-12'; } ?>">
                    <label><?php echo __($value['label'],'dc-woocommerce-multi-vendor'); ?><?php if($value['required']){ echo ' <span class="required">*</span>'; }?></label>
                    <input type="file" name="wcmp_vendor_fields[<?php echo $key; ?>][]" <?php if($value['required']){ echo 'required="required"'; }?> <?php if($value['muliple']){ echo 'multiple="true"'; }?> />
                    <input type="hidden" name="wcmp_vendor_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="wcmp_vendor_fields[<?php echo $key; ?>][type]" value="file" />
                </div>
                <?php
                break;
            case 'vendor_address_1':
            case 'vendor_address_2':
            case 'vendor_phone':
            //case 'vendor_country':
            //case 'vendor_state':
            case 'vendor_city':
            case 'vendor_postcode':
            case 'vendor_paypal_email':
                ?>
                <div class="wcmp-regi-form-row <?php if(!empty($value['cssClass'])){ echo $value['cssClass']; } else {  echo 'wcmp-regi-12'; } ?>">
                    <label><?php echo __($value['label'],'dc-woocommerce-multi-vendor'); ?><?php if($value['required']){ echo ' <span class="required">*</span>'; }?></label>
                    <input type="text" value="<?php if (!empty($form_data['wcmp_vendor_fields'][$key]["value"])) echo esc_attr($form_data['wcmp_vendor_fields'][$key]["value"]); ?>" name="wcmp_vendor_fields[<?php echo $key; ?>][value]" placeholder="<?php echo $value['placeholder']; ?>" <?php if($value['required']){ echo 'required="required"'; }?> />
                    <input type="hidden" name="wcmp_vendor_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="wcmp_vendor_fields[<?php echo $key; ?>][type]" value="<?php echo $value['type']; ?>" />
                </div>
                <?php
                break;
            case 'vendor_country':
                ?>
                <div class="wcmp-regi-form-row <?php if(!empty($value['cssClass'])){ echo $value['cssClass']; } else {  echo 'wcmp-regi-12'; } ?>">
                    <label><?php echo __($value['label'],'dc-woocommerce-multi-vendor'); ?><?php if($value['required']){ echo ' <span class="required">*</span>'; }?></label>
                    <!--input type="text" value="<?php if (!empty($form_data['wcmp_vendor_fields'][$key]["value"])) echo esc_attr($form_data['wcmp_vendor_fields'][$key]["value"]); ?>" name="wcmp_vendor_fields[<?php echo $key; ?>][value]" placeholder="<?php echo $value['placeholder']; ?>" <?php if($value['required']){ echo 'required="required"'; }?> /-->
                    <input type="hidden" name="wcmp_vendor_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="wcmp_vendor_fields[<?php echo $key; ?>][type]" value="<?php echo $value['type']; ?>" />
                    <select class="country_to_state select_box" name="wcmp_vendor_fields[<?php echo $key; ?>][value]" <?php if($value['required']){ echo 'required="required"'; }?>>
                        <option value=""><?php _e( 'Select a country&hellip;', 'dc-woocommerce-multi-vendor' ); ?></option>
                        <?php 
                            foreach ( WC()->countries->get_allowed_countries() as $key => $value ) {
                                echo '<option value="' . esc_attr( $key ) . '" '. selected(apply_filters('wcmp_vendor_registration_form_default_country_code', '', $key), $key).'>' . esc_html( $value ) . '</option>';
                            }
                        ?>
                    </select>
                </div>
                <?php
                break;
            case 'vendor_state':
                ?>
                <div class="vendor_state_wrapper wcmp-regi-form-row <?php if(!empty($value['cssClass'])){ echo $value['cssClass']; } else {  echo 'wcmp-regi-12'; } ?>">
                    <label><?php echo __($value['label'],'dc-woocommerce-multi-vendor'); ?><?php if($value['required']){ echo ' <span class="required">*</span>'; }?></label>
                    <!--input type="text" value="<?php if (!empty($form_data['wcmp_vendor_fields'][$key]["value"])) echo esc_attr($form_data['wcmp_vendor_fields'][$key]["value"]); ?>" name="wcmp_vendor_fields[<?php echo $key; ?>][value]" placeholder="<?php echo $value['placeholder']; ?>" <?php if($value['required']){ echo 'required="required"'; }?> /-->
                    <input type="hidden" name="wcmp_vendor_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="wcmp_vendor_fields[<?php echo $key; ?>][type]" value="<?php echo $value['type']; ?>" />
                    <select id="vendor_state" class="state_select select_box" name="wcmp_vendor_fields[<?php echo $key; ?>][value]" <?php if($value['required']){ echo 'required="required"'; }?>>
                        
                    </select>
                </div>
                <?php
                break;
            case 'vendor_description':
                ?>
                <div class="wcmp-regi-form-row <?php if(!empty($value['cssClass'])){ echo $value['cssClass']; } else {  echo 'wcmp-regi-12'; } ?>">
                    <label><?php echo __($value['label'],'dc-woocommerce-multi-vendor'); ?><?php if($value['required']){ echo ' <span class="required">*</span>'; }?></label>
                    <textarea <?php if(!empty($value['limit'])){ echo 'maxlength="'.$value['limit'].'"'; } ?> name="wcmp_vendor_fields[<?php echo $key; ?>][value]" placeholder="<?php echo $value['defaultValue']; ?>" <?php if($value['required']){ echo 'required'; }?>><?php if (!empty($form_data['wcmp_vendor_fields'][$key]["value"])){ echo esc_attr($form_data['wcmp_vendor_fields'][$key]["value"]); } ?></textarea>
                    <input type="hidden" name="wcmp_vendor_fields[<?php echo $key; ?>][label]" value="<?php echo htmlentities($value['label']); ?>" />
                    <input type="hidden" name="wcmp_vendor_fields[<?php echo $key; ?>][type]" value="<?php echo $value['type']; ?>" />
                </div>
                <?php
                break;
        }
    }
    //echo '<input type="hidden" value="'.  json_encode($wcmp_vendor_registration_form_data).'" />';
}