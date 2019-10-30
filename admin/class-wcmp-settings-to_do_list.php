<?php

class WCMp_Settings_To_Do_List {

    /**
     * Holds the values to be used in the fields callbacks
     */
    private $options;
    private $tab;

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
        do_action('before_wcmp_to_do_list');
        //pending vendor
        $get_pending_vendors = get_users('role=dc_pending_vendor');
        if (!empty($get_pending_vendors)) {
            ?>
            <h3><?php echo apply_filters('to_do_pending_vendor_text', __('Pending Vendor Approval', 'dc-woocommerce-multi-vendor')); ?></h3>
            <table class="form-table" id="to_do_list">
                <tbody>
                    <tr>
                        <th style="width:50%" ><?php _e('Pending User', 'dc-woocommerce-multi-vendor'); ?> </th>
                        <?php do_action('wcmp_todo_pending_vendor_approval_table_header'); ?>
                        <th><?php _e('Edit', 'dc-woocommerce-multi-vendor'); ?></th>
                        <th><?php _e('Activate', 'dc-woocommerce-multi-vendor'); ?></th>
                        <th><?php _e('Reject', 'dc-woocommerce-multi-vendor'); ?></th>
                        <th><?php _e('Dismiss', 'dc-woocommerce-multi-vendor'); ?></th>
                    </tr>
                    <?php
                    foreach ($get_pending_vendors as $pending_vendor) {
                        $dismiss = get_user_meta($pending_vendor->ID, '_dismiss_to_do_list', true);
                        if ($dismiss)
                            continue;
                        ?>
                        <tr>
                            <td style="width:50%" class="username column-username"><?php echo get_avatar($pending_vendor->ID, 32); ?><?php echo $pending_vendor->user_login; ?></td>
                            <?php do_action('wcmp_todo_pending_vendor_approval_table_row_data', $pending_vendor); ?>
                            <td class="edit"><a target="_blank" href="<?php echo apply_filters( 'wcmp_todo_pending_user_list_edit_action_url', admin_url('admin.php?page=vendors&action=edit&ID='.$pending_vendor->ID) ); ?>"><input type="button" class="vendor_edit_button" value="Edit" /> </a> </td>
                            <td class="activate"><input class="activate_vendor" type="button" class="activate_vendor" data-id="<?php echo $pending_vendor->ID; ?>" value="Activate" ></td>
                            <td class="reject"><input class="reject_vendor" type="button" class="reject_vendor" data-id="<?php echo $pending_vendor->ID; ?>" value="Reject"></td>
                            <td class="dismiss"><input class="vendor_dismiss_button" type="button" data-type="user" data-id="<?php echo $pending_vendor->ID; ?>" id="dismiss_request" name="dismiss_request" value="Dismiss"></td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
            <?php
        }
        $vendor_ids = array();
        $vendors = get_wcmp_vendors();
        if (!empty($vendors) && is_array($vendors)) {
            foreach ($vendors as $vendor) {
                $vendor_ids[] = $vendor->id;
            }
        }
        //coupon
        $args = array(
            'posts_per_page' => -1,
            'author__in' => $vendor_ids,
            'post_type' => 'shop_coupon',
            'post_status' => 'pending',
        );
        $get_pending_coupons = new WP_Query($args);
        $get_pending_coupons = $get_pending_coupons->get_posts();
        if (!empty($get_pending_coupons)) {
            ?>
            <h3><?php _e('Pending Coupons Approval', 'dc-woocommerce-multi-vendor'); ?></h3>
            <table class="form-table" id="to_do_list">
                <tbody>
                    <tr>
                        <?php
                        $table_headers = apply_filters('wcmp_todo_pending_coupon_approval_table_headers', array(
                            'vendor' => __('Vendor Name', 'dc-woocommerce-multi-vendor'),
                            'coupon' => __('Coupon Name', 'dc-woocommerce-multi-vendor'),
                            'edit' => __('Edit', 'dc-woocommerce-multi-vendor'),
                            'dismiss' => __('Dismiss', 'dc-woocommerce-multi-vendor'),
                        ));
                        if ($table_headers) :
                            foreach ($table_headers as $key => $label) {
                                ?>
                                <th><?php echo $label; ?> </th>
                            <?php
                            }
                        endif;
                        ?>
                    </tr>
                    <?php
                    foreach ($get_pending_coupons as $get_pending_coupon) {
                        $dismiss = get_post_meta($get_pending_coupon->ID, '_dismiss_to_do_list', true);
                        if ($dismiss)
                            continue;
                        ?>
                        <tr>
                            <?php
                            $currentvendor = get_wcmp_vendor($get_pending_coupon->post_author);
                            $vendor_term = get_term($currentvendor->term_id);
                            if ($table_headers) :
                                foreach ($table_headers as $key => $label) {
                                    switch ($key) {
                                        case 'vendor':
                                            ?>
                                            <td class="coupon column-coupon"><a href="user-edit.php?user_id=<?php echo $get_pending_coupon->post_author; ?>&amp;wp_http_referer=%2Fwordpress%2Fdc_vendor%2Fwp-admin%2Fusers.php%3Frole%3Ddc_vendor" target="_blank"><?php echo $vendor_term->name; ?></a></td>
                                            <?php break;
                                        case 'coupon':
                                            ?>
                                            <td class="coupon column-coupon"><?php echo $get_pending_coupon->post_title; ?></td>
                                            <?php break;
                                        case 'edit':
                                            ?>
                                            <td class="edit"><a target="_blank" href="post.php?post=<?php echo $get_pending_coupon->ID; ?>&action=edit"><input type="button" class="vendor_edit_button" value="Edit" /> </a> </td>
                                            <?php break;
                                        case 'dismiss':
                                            ?>
                                            <td class="dismiss"><input class="vendor_dismiss_button" type="button" data-type="shop_coupon" data-id="<?php echo $get_pending_coupon->ID; ?>" id="dismiss_request" name="dismiss_request" value="Dismiss"></td>
                                            <?php
                                            break;
                                        default:
                                            do_action('wcmp_todo_pending_coupon_approval_table_row_data', $key, $get_pending_coupon);
                                            break;
                                    }
                                }
                            endif;
                            ?>
                        </tr>
            <?php } ?>
                </tbody>
            </table>
            <?php
        }

        //product
        $args = array(
            'posts_per_page' => -1,
            'author__in' => $vendor_ids,
            'post_type' => 'product',
            'post_status' => 'pending',
        );
        $get_pending_products = new WP_Query($args);
        $get_pending_products = $get_pending_products->get_posts();
        if (!empty($get_pending_products)) {
            ?>
            <h3><?php _e('Pending Products Approval', 'dc-woocommerce-multi-vendor'); ?></h3>
            <table class="form-table" id="to_do_list">
                <tbody>
                    <tr>
                        <?php
                        $table_headers = apply_filters('wcmp_todo_pending_product_approval_table_headers', array(
                            'vendor' => __('Vendor Name', 'dc-woocommerce-multi-vendor'),
                            'product' => __('Product Name', 'dc-woocommerce-multi-vendor'),
                            'edit' => __('Edit', 'dc-woocommerce-multi-vendor'),
                            'dismiss' => __('Dismiss', 'dc-woocommerce-multi-vendor'),
                        ));
                        if ($table_headers) :
                            foreach ($table_headers as $key => $label) {
                                ?>
                                <th><?php echo $label; ?> </th>
                        <?php
                        }
                    endif;
                    ?>
                    </tr>
                        <?php
                        foreach ($get_pending_products as $get_pending_product) {
                            $dismiss = get_post_meta($get_pending_product->ID, '_dismiss_to_do_list', true);
                            if ($dismiss)
                                continue;
                            ?>
                        <tr>
                            <?php
                            $currentvendor = get_wcmp_vendor($get_pending_product->post_author);
                            $vendor_term = get_term($currentvendor->term_id);
                            if ($table_headers) :
                                foreach ($table_headers as $key => $label) {
                                    switch ($key) {
                                        case 'vendor':
                                            ?>
                                            <td class="vendor column-coupon"><a href="user-edit.php?user_id=<?php echo $get_pending_product->post_author; ?>&amp;wp_http_referer=%2Fwordpress%2Fdc_vendor%2Fwp-admin%2Fusers.php%3Frole%3Ddc_vendor" target="_blank"><?php echo $vendor_term->name; ?></a></td>
                                            <?php break;
                                        case 'product':
                                            ?>
                                            <td class="coupon column-coupon"><?php echo $get_pending_product->post_title; ?></td>
                                            <?php break;
                                        case 'edit':
                                            ?>
                                            <td class="edit"><a target="_blank" href="post.php?post=<?php echo $get_pending_product->ID; ?>&action=edit"><input type="button" class="vendor_edit_button" value="Edit" /> </a> </td>
                                <?php break;
                            case 'dismiss':
                                ?>
                                            <td class="dismiss"><input class="vendor_dismiss_button" data-type="product" data-id="<?php echo $get_pending_product->ID; ?>"  type="button" id="dismiss_request" name="dismiss_request" value="Dismiss"></td>
                                <?php
                                break;
                            default:
                                do_action('wcmp_todo_pending_product_approval_table_row_data', $key, $get_pending_product);
                                break;
                        }
                    }
                endif;
                ?>
                        </tr>
            <?php } ?>
                </tbody>
            </table>
            <?php
        }


        //commission
        $args = array(
            'post_type' => 'wcmp_transaction',
            'post_status' => 'wcmp_processing',
            'meta_key' => 'transaction_mode',
            'meta_value' => 'direct_bank',
            'posts_per_page' => -1
        );
        $transactions = get_posts($args);

        if (!empty($transactions)) {
            ?>
            <h3>
                <?php _e('Pending Bank Transfer', 'dc-woocommerce-multi-vendor'); ?>
                <form method="post" style="display: inline-flex;">
                    <?php wp_nonce_field( 'wcmp_todo_pending_bank_transfer_export','wcmp_admin_bank_transfer_export_nonce' ); ?>
                    <?php 
                    $transactions_ids = wp_list_pluck( $transactions, 'ID' );
                    echo '<input type="hidden" name="transactions_ids" value="'. wp_json_encode( $transactions_ids ) .'">';
                    do_action( 'wcmp_todo_pending_bank_transfer_exporter_form_fields' );
                    ?>
                    <button class="button"><?php _e('Export', 'woocommerce'); ?></button>
                </form>
            </h3>
            <table class="form-table" id="to_do_list">
                <tbody>
                    <tr>
                    <?php
                    $table_headers = apply_filters('wcmp_todo_pending_bank_transfer_table_headers', array(
                        'vendor' => __('Vendor Name', 'dc-woocommerce-multi-vendor'),
                        'commission' => __('Commission', 'dc-woocommerce-multi-vendor'),
                        'amount' => __('Amount', 'dc-woocommerce-multi-vendor'),
                        'account_details' => __('Account Detail', 'dc-woocommerce-multi-vendor'),
                        'notify_vendor' => __('Notify the Vendor', 'dc-woocommerce-multi-vendor'),
                        'dismiss' => __('Dismiss', 'dc-woocommerce-multi-vendor'),
                    ));
                    if ($table_headers) :
                        foreach ($table_headers as $key => $label) {
                            ?>
                                <th><?php echo $label; ?> </th>
                        <?php
                        }
                    endif;
                    ?>
                    </tr>
                        <?php
                        foreach ($transactions as $transaction) {
                            $dismiss = get_post_meta($transaction->ID, '_dismiss_to_do_list', true);
                            $vendor_term_id = $transaction->post_author;
                            $currentvendor = get_wcmp_vendor_by_term($vendor_term_id);
                            $vendor_term = get_term($vendor_term_id);
                            if ($dismiss || !$currentvendor) {
                                continue;
                            }
                            $account_name = get_user_meta($currentvendor->id, '_vendor_account_holder_name', true);
                            $account_no = get_user_meta($currentvendor->id, '_vendor_bank_account_number', true);
                            $bank_name = get_user_meta($currentvendor->id, '_vendor_bank_name', true);
                            $iban = get_user_meta($currentvendor->id, '_vendor_iban', true);
                            $amount = get_post_meta($transaction->ID, 'amount', true) - get_post_meta($transaction->ID, 'transfer_charge', true) - get_post_meta($transaction->ID, 'gateway_charge', true);
                            ?>
                        <tr>
                            <?php
                            if ($table_headers) :
                                foreach ($table_headers as $key => $label) {
                                    switch ($key) {
                                        case 'vendor':
                                            ?>
                                            <td class="vendor column-coupon"><a href="user-edit.php?user_id=<?php echo $currentvendor->id; ?>&amp;wp_http_referer=%2Fwordpress%2Fdc_vendor%2Fwp-admin%2Fusers.php%3Frole%3Ddc_vendor" target="_blank"><?php echo $vendor_term->name; ?></a></td>
                                            <?php break;
                                        case 'commission':
                                            ?>
                                            <td class="commission column-coupon"><?php echo $transaction->post_title; ?></td>
                                                <?php break;
                                            case 'amount':
                                                ?>
                                            <td class="commission_val column-coupon"><?php echo wc_price($amount); ?></td>
                                                <?php
                                                break;
                                            case 'account_details':
                                                $address_array = apply_filters('wcmp_todo_pending_bank_transfer_row_account_details_data', array(
                                                    __('Account Name-', 'dc-woocommerce-multi-vendor') . ' ' . $account_name,
                                                    __('Account No -', 'dc-woocommerce-multi-vendor') . ' ' . $account_no,
                                                    __('Bank Name -', 'dc-woocommerce-multi-vendor') . ' ' . $bank_name,
                                                    __('IBAN -', 'dc-woocommerce-multi-vendor') . ' ' . $iban,
                                                        ), $currentvendor, $transaction);
                                                ?>
                                            <td class="account_detail"><?php echo implode('<br/>', $address_array); ?></td>
                                <?php break;
                            case 'notify_vendor':
                                ?>
                                            <td class="done"><input class="vendor_transaction_done_button" data-transid="<?php echo $transaction->ID; ?>" data-vendorid="<?php echo $vendor_term_id; ?>" type="button" id="done_request" name="done_request" value="Done"></td>
                                <?php break;
                            case 'dismiss':
                                ?>
                                            <td class="dismiss"><input class="vendor_dismiss_button" data-type="dc_commission" data-id="<?php echo $transaction->ID; ?>" type="button" id="dismiss_request" name="dismiss_request" value="Dismiss"></td>
                                <?php
                                break;
                            default:
                                do_action('wcmp_todo_pending_bank_transfer_table_row_data', $key, $get_pending_coupon);
                                break;
                        }
                    }
                endif;
                ?>   
                        </tr>
            <?php } ?>
                </tbody>
            </table>
            <?php
        }
        do_action('after_wcmp_to_do_list');
    }

}
