<?php

if (!defined('ABSPATH')) {
    exit;
}

/**
 * @class 		WCMp Order Class
 *
 * @version		3.1.2.0
 * @package		WCMp
 * @author 		WC Marketplace
 */
class WCMp_Order {

    public function __construct() {
        global $WCMp;
        // Init WCMp Vendor Order class
        $WCMp->load_class('vendor-order');
        add_action('woocommerce_new_order_item', array(&$this, 'order_item_meta_2'), 20, 3);
        // Add extra vendor_id to shipping packages
        add_action('woocommerce_checkout_create_order_shipping_item', array(&$this, 'add_meta_date_in_shipping_package'), 10, 4);
        if (is_wcmp_version_less_3_4_0()) {
            
        } else {
            // filters order list table
            add_filter('request', array($this, 'wc_order_list_filter'), 10, 1);
            add_action('admin_head', array($this, 'count_processing_order'), 5);
            add_filter('views_edit-shop_order', array($this, 'shop_order_statuses_get_views') );
            add_filter('wp_count_posts', array($this, 'shop_order_count_orders'), 99, 3 );
            if( !is_user_wcmp_vendor( get_current_user_id() ) ) {
                add_filter('manage_shop_order_posts_columns', array($this, 'wcmp_shop_order_columns'), 99);
                add_action('manage_shop_order_posts_custom_column', array($this, 'wcmp_show_shop_order_columns'), 99, 2);
            }
            if(apply_filters('wcmp_parent_order_to_vendor_order_status_synchronization', true))
                add_action('woocommerce_order_status_changed', array($this, 'wcmp_parent_order_to_vendor_order_status_synchronization'), 90, 3);
            if(apply_filters('wcmp_vendor_order_to_parent_order_status_synchronization', true))
                add_action('woocommerce_order_status_changed', array($this, 'wcmp_vendor_order_to_parent_order_status_synchronization'), 99, 3);
            // WCMp create orders
            add_action('woocommerce_checkout_order_processed', array(&$this, 'wcmp_create_orders'), 10, 3);
            add_action('woocommerce_after_checkout_validation', array($this, 'wcmp_check_order_awaiting_payment'));
            // Order Refund
            add_action('woocommerce_order_refunded', array($this, 'wcmp_order_refunded'), 10, 2);
            add_action('woocommerce_refund_deleted', array($this, 'wcmp_refund_deleted'), 10, 2);
            add_action('woocommerce_create_refund', array( $this, 'wcmp_create_refund' ), 10, 2);
            $this->init_prevent_trigger_vendor_order_emails();
            // Order Trash 
            add_action( 'trashed_post', array( $this, 'trash_wcmp_suborder' ), 10, 1 );
            // Order Delete 
            add_action( 'deleted_post', array( $this, 'delete_wcmp_suborder' ), 10, 1 );
            // Restrict default order edit caps for vendor
            add_action( 'admin_enqueue_scripts', array( $this, 'wcmp_vendor_order_backend_restriction' ), 99 );
        }
    }

    /**
     * Save sold by text in database
     *
     * @param item_id, cart_item
     * @return void 
     */
    public function order_item_meta_2($item_id, $item, $order_id) {
        if (!wcmp_get_order($order_id)) {
            $general_cap = apply_filters('wcmp_sold_by_text', __('Sold By', 'dc-woocommerce-multi-vendor'));
            $vendor = get_wcmp_product_vendors($item['product_id']);
            if ($vendor) {
                wc_add_order_item_meta($item_id, $general_cap, $vendor->page_titl);
                wc_add_order_item_meta($item_id, '_vendor_id', $vendor->id);
            }
        }
    }

    /**
     * 
     * @param object $item
     * @param sting $package_key as $vendor_id
     */
    public function add_meta_date_in_shipping_package($item, $package_key, $package, $order) {
        if (!wcmp_get_order($order->get_id()) && is_user_wcmp_vendor($package_key)) {
            $item->add_meta_data('vendor_id', $package_key, true);
            $package_qty = array_sum(wp_list_pluck($package['contents'], 'quantity'));
            $item->add_meta_data('package_qty', $package_qty, true);
            do_action('wcmp_add_shipping_package_meta_data');
        }
    }

    public function wc_order_list_filter($query) {
        global $typenow;
        $user = wp_get_current_user();
        if ('shop_order' == $typenow) {
            if (current_user_can('administrator') && empty($_REQUEST['s'])) {
                $query['post_parent'] = 0;
            }elseif(in_array('dc_vendor', $user->roles)){
                $query['author'] = $user->ID;
            }
            return apply_filters("wcmp_shop_order_query_request", $query);
        }

        return $query;
    }
    
    public function init_prevent_trigger_vendor_order_emails(){
        $prevent_vendor_order_emails = apply_filters('wcmp_prevent_vendor_order_emails_trigger', array(
            'recipient' => array(
                'new_order', 
                'cancelled_order'
                ),
            'enabled' => array(
                'customer_on_hold_order', 
                'customer_processing_order', 
                'customer_refunded_order', 
                'customer_partially_refunded_order', 
                'customer_completed_order'
                )
        ));
        if($prevent_vendor_order_emails) :
            foreach ($prevent_vendor_order_emails as $prevent => $email_ids) {
                switch ($prevent) {
                    case 'recipient':
                        if($email_ids){
                            foreach ($email_ids as $email_id) {
                                add_filter( 'woocommerce_email_recipient_'.$email_id, array($this, 'woocommerce_email_recipient_prevent'), 99, 2 );
                            }
                        }
                        break;
                    case 'enabled':
                        if($email_ids){
                            foreach ($email_ids as $email_id) {
                                add_filter( 'woocommerce_email_enabled_'.$email_id, array($this, 'woocommerce_email_enabled_prevent'), 99, 2 );
                            }
                        }
                        break;
                    default:
                        do_action('wcmp_prevent_vendor_order_emails_trigger_action', $email_ids, $prevent);
                        break;
                }
            }
        endif;
    }
    
    public function woocommerce_email_recipient_prevent($recipient, $object ){
        return $object instanceof WC_Order && wp_get_post_parent_id( $object->get_id() ) ? false : $recipient;
    }
    
    public function woocommerce_email_enabled_prevent($enabled, $object ){
        $is_editpost_action = ! empty( $_REQUEST['action'] ) && in_array( $_REQUEST['action'], array('editpost','edit') );

        if ( $is_editpost_action && ! empty( $_REQUEST['post_ID'] ) && wp_get_post_parent_id( $_REQUEST['post_ID'] ) == 0 && $object instanceof WC_Order && $_REQUEST['post_ID'] != $object->get_id() ) {
            return false;
        }
        
        $is_vendor_order = wcmp_get_order($object->get_id());
        
        if ( $is_editpost_action && $is_vendor_order ) return $enabled;

        return $enabled && $object instanceof WC_Order && wp_get_post_parent_id( $object->get_id() ) != 0 && !$is_editpost_action ? false : $enabled;
    }

    public function wcmp_shop_order_columns($columns) {

        $order_title_number = version_compare(WC_VERSION, '3.3.0', '>=') ? 'order_number' : 'order_title';
        if ((!isset($_GET['post_status']) || ( isset($_GET['post_status']) && 'trash' != $_GET['post_status'] ))) {
            $suborder = array('wcmp_suborder' => __('Suborders', 'dc-woocommerce-multi-vendor'));
            $title_number_pos = array_search($order_title_number, array_keys($columns));
            $columns = array_slice($columns, 0, $title_number_pos + 1, true) + $suborder + array_slice($columns, $title_number_pos + 1, count($columns) - 1, true);
        }
        return $columns;
    }

    /**
     * Output custom columns for orders
     *
     * @param  string $column
     */
    public function wcmp_show_shop_order_columns($column, $post_id) {
        switch ($column) {
            case 'wcmp_suborder' :
                $wcmp_suborders = $this->get_suborders($post_id);

                if ($wcmp_suborders) {
                    echo '<ul class="wcmp-order-vendor" style="margin:0px;">';
                    foreach ($wcmp_suborders as $suborder) {
                        $vendor = get_wcmp_vendor(get_post_field('post_author', $suborder->get_id()));
                        $order_uri = apply_filters('wcmp_admin_vendor_shop_order_edit_url', esc_url('post.php?post=' . $suborder->get_id() . '&action=edit'), $suborder->get_id());

                        printf('<li><mark class="%s tips" data-tip="%s">%s</mark> <strong><a href="%s">#%s</a></strong> &ndash; <small class="wcmp-order-for-vendor">%s %s</small></li>', sanitize_title($suborder->get_status()), $suborder->get_status(), $suborder->get_status(), $order_uri, $suborder->get_order_number(), _x('for', 'Order table details', 'dc-woocommerce-multi-vendor'), $vendor->page_title
                        );

                        do_action('wcmp_after_suborder_details', $suborder);
                    }
                    echo '<ul>';
                } else {
                    echo '<span class="na">&ndash;</span>';
                }
                break;
        }
    }

    public function wcmp_create_orders($order_id, $posted_data, $order) {
        global $WCMp;
        //check parent order exist
        if (wp_get_post_parent_id($order_id) != 0)
            return false;

        $order = wc_get_order($order_id);
        $items = $order->get_items();
        $vendor_items = array();

        foreach ($items as $item_id => $item) {
            if (isset($item['product_id']) && $item['product_id'] !== 0) {
                // check vendor product
                $has_vendor = get_wcmp_product_vendors($item['product_id']);
                if ($has_vendor) {
                    $variation_id = isset($item['variation_id']) && !empty($item['variation_id']) ? $item['variation_id'] : 0;
                    $variation = isset($item['variation']) && !empty($item['variation']) ? $item['variation'] : array();
                    $item_commission = $WCMp->commission->get_item_commission($item['product_id'], $variation_id, $item, $order_id, $item_id);
                    $commission_values = $WCMp->commission->get_commission_amount($item['product_id'], $has_vendor->term_id, $variation_id, $item_id, $order);
                    $commission_rate = array('mode' => $WCMp->vendor_caps->payment_cap['revenue_sharing_mode'], 'type' => $WCMp->vendor_caps->payment_cap['commission_type']);
                    $commission_rate['commission_val'] = isset($commission_values['commission_val']) ? $commission_values['commission_val'] : 0;
                    $commission_rate['commission_fixed'] = isset($commission_values['commission_fixed']) ? $commission_values['commission_fixed'] : 0;
                    $item['commission'] = $item_commission;
                    $item['commission_rate'] = $commission_rate;
                    $vendor_items[$has_vendor->id][$item_id] = $item;
                }
            }
        }
        // if there is no vendor available
        if (count($vendor_items) == 0)
            return false;
        // update parent order meta
        update_post_meta($order_id, 'has_wcmp_sub_order', true);
        $vendor_orders = array();
        foreach ($vendor_items as $vendor_id => $items) {
            if (!empty($items)) {
                $vendor_orders[] = self::create_vendor_order(array(
                            'order_id' => $order_id,
                            'vendor_id' => $vendor_id,
                            'posted_data' => $posted_data,
                            'line_items' => $items
                ));
            }
        }
        if ($vendor_orders) :
            foreach ($vendor_orders as $vendor_order_id) {
                do_action('wcmp_checkout_vendor_order_processed', $vendor_order_id, $posted_data, $order);
            }
        endif;
    }

    /**
     * Create a new vendor order programmatically
     *
     * Returns a new vendor_order object on success which can then be used to add additional data.
     *
     * @since 
     * @param array $args
     * @param boolean $data_migration (default: false) for data migration
     * @return WCMp_Order|WP_Error
     */
    public static function create_vendor_order($args = array(), $data_migration = false) {
        global $WCMp;
        $default_args = array(
            'vendor_id' => 0,
            'order_id' => 0,
            'posted_data' => array(),
            'vendor_order_id' => 0,
            'line_items' => array()
        );

        $args = wp_parse_args($args, $default_args);
        $order = wc_get_order($args['order_id']);
        $data = array();

        if ($args['vendor_order_id'] > 0) {
            $updating = true;
            $data['ID'] = $args['vendor_order_id'];
        } else {
            $updating = false;
            $data = apply_filters('wcmp_create_vendor_order_new_order_data', array(
                'post_date' => gmdate('Y-m-d H:i:s', $order->get_date_created('edit')->getOffsetTimestamp()),
                'post_date_gmt' => gmdate('Y-m-d H:i:s', $order->get_date_created('edit')->getTimestamp()),
                'post_type' => 'shop_order',
                'post_status' => 'wc-' . ( $order->get_status('edit') ? $order->get_status('edit') : apply_filters('wcmp_create_vendor_order_default_order_status', 'pending') ),
                'ping_status' => 'closed',
                'post_author' => absint($args['vendor_id']),
                'post_title' => sprintf(__('Vendor Order &ndash; %s', 'dc-woocommerce-multi-vendor'), strftime(_x('%B %e, %Y @ %I:%M %p', 'Commission date parsed by strftime', 'dc-woocommerce-multi-vendor'), current_time('timestamp'))),
                'post_password' => uniqid('wcmp_order_'),
                'post_parent' => absint($args['order_id']),
                'post_excerpt' => isset($args['posted_data']['order_comments']) ? $args['posted_data']['order_comments'] : '',
                    )
            );
        }

        if ($updating) {
            $vendor_order_id = wp_update_post($data);
        } else {
            $vendor_order_id = wp_insert_post($data, true);
            $args['vendor_order_id'] = $vendor_order_id;
        }

        if (is_wp_error($vendor_order_id)) {
            return $vendor_order_id;
        }

        $vendor_order = wc_get_order($vendor_order_id);

        $wc_checkout = WC()->checkout();
        $checkout_fields = !is_admin() && !is_ajax() ? $wc_checkout->checkout_fields : array();
        
        self::create_wcmp_order_line_items($vendor_order, $args);
        self::create_wcmp_order_shipping_lines($vendor_order, WC()->session->get('chosen_shipping_methods'), WC()->shipping->get_packages(), $args, $data_migration);
        
        //self::create_wcmp_order_tax_lines( $vendor_order, $args );
        // Add customer checkout fields data to vendor order
        if (empty($checkout_fields)) {
            $types = array('billing', 'shipping');
            foreach ($types as $type) {
                $vendor_order->set_address($order->get_address($type), $type);
            }
        }

        if (!empty($wc_checkout)) {
            foreach ($checkout_fields as $section => $checkout_meta_keys) {
                if ('account' != $section) {
                    foreach ($checkout_meta_keys as $order_meta_key => $order_meta_values) {
                        $meta_key = 'shipping' == $section || 'billing' == $section ? '_' . $order_meta_key : $order_meta_key;
                        $meta_value_to_save = isset($args['posted_data'][$order_meta_key]) ? $args['posted_data'][$order_meta_key] : get_post_meta($order->get_id(), $meta_key, true);
                        update_post_meta($vendor_order_id, $meta_key, $meta_value_to_save);
                    }
                }
            }
        }
        // Add vendor order meta data
        $order_meta = apply_filters('wcmp_vendor_order_meta_data', array(
            '_payment_method',
            '_payment_method_title',
            '_customer_user',
            '_prices_include_tax',
            '_order_currency',
            '_order_key',
            '_customer_ip_address',
            '_customer_user_agent',
        ));

        foreach ($order_meta as $key) {
            update_post_meta($vendor_order_id, $key, get_post_meta($order->get_id(), $key, true));
        }

        update_post_meta($vendor_order_id, '_order_version', $WCMp->version);
        update_post_meta($vendor_order_id, '_vendor_id', absint($args['vendor_id']));
        update_post_meta($vendor_order_id, '_created_via', 'wcmp_vendor_order');
        
        if($data_migration)
            update_post_meta($vendor_order_id, '_order_migration', true);

        /**
         * Action hook to adjust order before save.
         *
         * @since 3.1.2.0
         */
        do_action('wcmp_checkout_create_order', $order, $args);

        // Save the order.
        $v_order_id = $vendor_order->save();
        $vendor_order = wc_get_order($v_order_id);
        do_action('wcmp_checkout_update_order_meta', $v_order_id, $args);
        $vendor_order->calculate_totals();
        return $v_order_id;
    }

    /**
     * Add line items to the order.
     *
     * @param WC_Order $order Order instance.
     * @param WC_Cart  $cart  Cart instance.
     */
    public static function create_wcmp_order_line_items($order, $args) {
        $line_items = $args['line_items'];
        $commission_rate_items = array();
        foreach ($line_items as $item_id => $order_item) {
            $item = new WC_Order_Item_Product();
            $product = wc_get_product($order_item['product_id']);
      
            $item->set_props(
                    array(
                        'quantity' => $order_item['quantity'],
                        'variation' => $order_item['variation'],
                        'subtotal' => $order_item['line_subtotal'],
                        'total' => $order_item['line_total'],
                        'subtotal_tax' => $order_item['line_subtotal_tax'],
                        'total_tax' => $order_item['line_tax'],
                        'taxes' => $order_item['line_tax_data'],
                    )
            );

            if ($product) {
                $item->set_props(
                        array(
                            'name' => $product->get_name(),
                            'tax_class' => $product->get_tax_class(),
                            'product_id' => $product->is_type('variation') ? $product->get_parent_id() : $product->get_id(),
                            'variation_id' => $product->is_type('variation') ? $product->get_id() : 0,
                        )
                );
            }

            $item->set_backorder_meta();
            $item->add_meta_data('_vendor_order_item_id', $item_id);
            // Add commission data
            $item->add_meta_data('_vendor_item_commission', $order_item['commission']);
            
            $item->add_meta_data('_vendor_id', $args['vendor_id']);
            // BW compatibility with old meta.
            $vendor = get_wcmp_vendor($args['vendor_id']);
            $general_cap = apply_filters('wcmp_sold_by_text', __('Sold By', 'dc-woocommerce-multi-vendor'));
            $item->add_meta_data($general_cap, $vendor->page_title);


            do_action('wcmp_vendor_create_order_line_item', $item, $item_id, $order_item, $order);
            // Add item to order and save.
            $order->add_item($item);
            // temporary commission rate save with order_item_id
            if(isset($order_item['commission_rate']) && $order_item['commission_rate'])
                $commission_rate_items[$item_id] = $order_item['commission_rate'];
        }
        /**
         * Temporary commission rates save for vendor order.
         *
         * @since 3.1.2.0
         */
        update_post_meta(absint($args['vendor_order_id']), 'order_items_commission_rates', $commission_rate_items);
        
    }

    /**
     * Add shipping lines to the order.
     *
     * @param WC_Order $order                   Order Instance.
     * @param array    $chosen_shipping_methods Chosen shipping methods.
     * @param array    $packages                Packages.
     */
    public static function create_wcmp_order_shipping_lines($order, $chosen_shipping_methods, $packages, $args = array(), $migration = false) {
        $vendor_id = isset($args['vendor_id']) ? $args['vendor_id'] : 0;
        $parent_order_id = isset($args['order_id']) ? $args['order_id'] : 0;

        if(!$migration){
        
            foreach ($packages as $package_key => $package) {
                if ($package_key == $vendor_id && isset($chosen_shipping_methods[$package_key], $package['rates'][$chosen_shipping_methods[$package_key]])) {
                    $shipping_rate = $package['rates'][$chosen_shipping_methods[$package_key]];
                    $item = new WC_Order_Item_Shipping();
                    $item->legacy_package_key = $package_key; // @deprecated For legacy actions.
                    $item->set_props(
                            array(
                                'method_title' => $shipping_rate->label,
                                'method_id' => $shipping_rate->method_id,
                                'instance_id' => $shipping_rate->instance_id,
                                'total' => wc_format_decimal($shipping_rate->cost),
                                'taxes' => array(
                                    'total' => $shipping_rate->taxes,
                                ),
                            )
                    );

                    foreach ($shipping_rate->get_meta_data() as $key => $value) {
                        $item->add_meta_data($key, $value, true);
                    }

                    $item->add_meta_data('vendor_id', $package_key, true);
                    $package_qty = array_sum(wp_list_pluck($package['contents'], 'quantity'));
                    $item->add_meta_data('package_qty', $package_qty, true);

                    /**
                     * Action hook to adjust item before save.
                     *
                     * @since 3.1.2.0
                     */
                    do_action('wcmp_vendor_create_order_shipping_item', $item, $package_key, $package, $order);

                    // Add item to order and save.
                    $order->add_item($item);
                }
            }
        }else{
            // Backward compatibilities for WCMp old orders
            $parent_order = wc_get_order($parent_order_id);
            if($parent_order){
                $shipping_items = $parent_order->get_items('shipping');
                
                foreach ($shipping_items as $item_id => $item) {
                    $shipping_vendor_id = $item->get_meta('vendor_id', true);
                    if($shipping_vendor_id == $vendor_id){
                        $shipping = new WC_Order_Item_Shipping();
                        $shipping->set_props(
                                array(
                                    'method_title' => $item['method_title'],
                                    'method_id' => $item['method_id'],
                                    'instance_id' => $item['instance_id'],
                                    'total' => wc_format_decimal($item['total']),
                                    'taxes' => $item['taxes'],
                                )
                        );

                        foreach ($item->get_meta_data() as $key => $value) {
                            $shipping->add_meta_data($key, $value, true);
                        }

                        $shipping->add_meta_data('vendor_id', $vendor_id, true);
                        $package_qty = $item->get_meta('package_qty', true);
                        $shipping->add_meta_data('package_qty', $package_qty, true);

                        $order->add_item($shipping);
                    }
                }
            }
        }
    }

    /**
     * Add tax lines to the order.
     *
     * @param WC_Order $order Order instance.
     * @param WC_Cart  $cart  Cart instance.
     */
    public static function create_wcmp_order_tax_lines($order, $vendor_order_data) {
        $line_items = $vendor_order_data['line_items'];
        $item_total_tax = 0;
        foreach ($line_items as $item_id => $order_item) {
            $item_total_tax += (float) $order_item['line_total'];
        }


        foreach (array_keys($cart->get_cart_contents_taxes() + $cart->get_shipping_taxes() + $cart->get_fee_taxes()) as $tax_rate_id) {
            if ($tax_rate_id && apply_filters('woocommerce_cart_remove_taxes_zero_rate_id', 'zero-rated') !== $tax_rate_id) {
                $item = new WC_Order_Item_Tax();
                $item->set_props(
                        array(
                            'rate_id' => $tax_rate_id,
                            'tax_total' => $cart->get_tax_amount($tax_rate_id),
                            'shipping_tax_total' => $cart->get_shipping_tax_amount($tax_rate_id),
                            'rate_code' => WC_Tax::get_rate_code($tax_rate_id),
                            'label' => WC_Tax::get_rate_label($tax_rate_id),
                            'compound' => WC_Tax::is_compound($tax_rate_id),
                        )
                );

                /**
                 * Action hook to adjust item before save.
                 *
                 * @since 3.0.0
                 */
                do_action('woocommerce_checkout_create_order_tax_item', $item, $tax_rate_id, $order);

                // Add item to order and save.
                $order->add_item($item);
            }
        }
    }

    /**
     * Get suborders if available.
     *
     * @param int $order_id.
     * @param array $args.
     * @return object suborders.
     */
    public function get_suborders($order_id, $args = array()) {
        $default = array(
            'post_parent' => $order_id,
            'post_type' => 'shop_order',
            'numberposts' => -1,
            'post_status' => 'any'
        );
        $args = wp_parse_args($args, $default);
        $orders = array();
        $posts = get_posts($args);
        foreach ($posts as $post) {
            $orders[] = wc_get_order($post->ID);
        }
        return $orders;
    }

    public function wcmp_parent_order_to_vendor_order_status_synchronization($order_id, $old_status, $new_status) {
        //Check if order have sub-order
        $status_to_sync = apply_filters('wcmp_parent_order_to_vendor_order_statuses_to_sync',array('processing'));
        if($new_status == $status_to_sync) :
            if (wp_get_post_parent_id( $order_id ) || get_post_meta($order_id, 'wcmp_vendor_order_status_synchronized', true))
                return false;

            $wcmp_suborders = $this->get_suborders($order_id);

            if ($wcmp_suborders) {

                if (empty($new_status)) {
                    $order = wc_get_order($order_id);
                    $new_status = $order->get_status('edit');
                }

                foreach ($wcmp_suborders as $suborder) {
                    $suborder->update_status($new_status, _x('Update by admin: ', 'Order note', 'dc-woocommerce-multi-vendor'));
                }
                update_post_meta($order_id, 'wcmp_vendor_order_status_synchronized', true);
            }
        endif;
    }
    
    public function wcmp_vendor_order_to_parent_order_status_synchronization($order_id, $old_status, $new_status){
        // parent order synchronization
        $parent_order_id = wp_get_post_parent_id( $order_id );
        if($parent_order_id){
            remove_action('woocommerce_order_status_changed', array($this, 'wcmp_parent_order_to_vendor_order_status_synchronization'), 90, 3);
            $status_to_sync = apply_filters('wcmp_vendor_order_to_parent_order_statuses_to_sync',array('completed', 'refunded'));

            $wcmp_suborders = $this->get_suborders( $parent_order_id );
            $new_status_count  = 0;
            $suborder_count    = count( $wcmp_suborders );
            $suborder_statuses = array();
            $suborder_totals = 0;
            foreach ( $wcmp_suborders as $suborder ) {
                $suborder_totals += $suborder->get_total();
                $suborder_status = $suborder->get_status( 'edit' );
                if ( $new_status == $suborder_status ) {
                    $new_status_count ++;
                }

                if ( ! isset( $suborder_statuses[ $suborder_status ] ) ) {
                    $suborder_statuses[ $suborder_status ] = 1;
                } else {
                    $suborder_statuses[ $suborder_status ] ++;
                }
            }

            $parent_order = wc_get_order( $parent_order_id );
            if($parent_order->get_total() == $suborder_totals){
                if ( $suborder_count == $new_status_count && in_array( $new_status, $status_to_sync ) ) {
                    $parent_order->update_status( $new_status, _x( "Sync from vendor's suborders: ", 'Order note', 'dc-woocommerce-multi-vendor' ) );
                } elseif ( $suborder_count != 0 ) {
                    /**
                     * If the parent order have only 1 suborder I can sync it with the same status.
                     * Otherwise I set the parent order to processing
                     */
                    if ( $suborder_count == 1 ) {
                            $parent_order->update_status( $new_status, _x( "Sync from vendor's suborders: ", 'Order note', 'dc-woocommerce-multi-vendor' ) );
                    } /**
                     * Check only for suborder > 1 to exclude orders without suborder
                     */
                    elseif ( $suborder_count > 1 ) {
                        $check = 0;
                        foreach ( $status_to_sync as $status ) {
                            if ( ! empty( $suborder_statuses[ $status ] ) ) {
                                $check += $suborder_statuses[ $status ];
                            }
                        }

                        $parent_order->update_status( $check == $suborder_count ? 'completed' : 'processing', _x( "Sync from vendor's suborders: ", 'Order note', 'dc-woocommerce-multi-vendor' ) );
                    }
                }
            }
            add_action('woocommerce_order_status_changed', array($this, 'wcmp_parent_order_to_vendor_order_status_synchronization'), 90, 3);
        }
    }

    public function wcmp_check_order_awaiting_payment() {
        // Insert or update the post data
        $order_id = absint(WC()->session->order_awaiting_payment);

        // Resume the unpaid order if its pending
        if ($order_id > 0) {
            $order = wc_get_order($order_id);
            if ($order->has_status(array('pending', 'failed'))) {
                $wcmp_suborders = $this->get_suborders($order_id);
                if ($wcmp_suborders) {
                    foreach ($wcmp_suborders as $suborder) {
                        wc_delete_shop_order_transients($suborder->get_id());
                        wp_delete_post($suborder->get_id(), true);
                    }
                }
            }
        }
    }

    /**
     * Handle a refund via the edit order screen.
     * Called after wp_ajax_woocommerce_refund_line_items action
     *
     * @use woocommerce_order_refunded action
     * @see woocommerce\includes\class-wc-ajax.php:2295
     */
    public function wcmp_order_refunded($order_id, $parent_refund_id) {
        
        if (!wp_get_post_parent_id($order_id)) { 
            $create_vendor_refund = false;
            $create_refund = true;
            $refund = false;
            $parent_line_item_refund = 0;
            $refund_amount = wc_format_decimal(sanitize_text_field($_POST['refund_amount']));
            $refund_reason = !empty($_POST['refund_reason']) ? sanitize_text_field($_POST['refund_reason']) : '';
            $line_item_qtys = !empty($_POST['line_item_qtys']) ? json_decode(sanitize_text_field(stripslashes($_POST['line_item_qtys'])), true) : array();
            $line_item_totals = !empty($_POST['line_item_totals']) ? json_decode(sanitize_text_field(stripslashes($_POST['line_item_totals'])), true) : array();
            $line_item_tax_totals = !empty($_POST['line_item_tax_totals']) ? json_decode(sanitize_text_field(stripslashes($_POST['line_item_tax_totals'])), true) : array();
            $api_refund = !empty($_POST['api_refund']) && $_POST['api_refund'] === 'true' ? true : false;
            $restock_refunded_items = !empty($_POST['restock_refunded_items']) && $_POST['restock_refunded_items'] === 'true' ? true : false;
            $order = wc_get_order($order_id);
            $parent_order_total = wc_format_decimal($order->get_total());
            $wcmp_suborders = $this->get_suborders($order_id);

            //calculate line items total from parent order
            foreach ($line_item_totals as $item_id => $total) {
                // check if there have vendor line item to refund
                $item = $order->get_item($item_id);
                if($item->get_meta('_vendor_id') && $total != 0) $create_vendor_refund = true;
                $parent_line_item_refund += wc_format_decimal($total);
            }
            
            foreach ($wcmp_suborders as $suborder) {
                $suborder_items_ids = array_keys($suborder->get_items());
                $suborder_total = wc_format_decimal($suborder->get_total());
                $max_refund = wc_format_decimal($suborder_total - $suborder->get_total_refunded());
                $child_line_item_refund = 0;

                // Prepare line items which we are refunding
                $line_items = array();
                $item_ids = array_unique(array_merge(array_keys($line_item_qtys, $line_item_totals)));

                foreach ($item_ids as $item_id) {
                    $child_item_id = $this->get_vendor_order_item_id($item_id);
                    if ($child_item_id && in_array($child_item_id, $suborder_items_ids)) {
                        $line_items[$child_item_id] = array(
                            'qty' => 0,
                            'refund_total' => 0,
                            'refund_tax' => array()
                        );
                    }
                }

                foreach ($line_item_qtys as $item_id => $qty) {
                    $child_item_id = $this->get_vendor_order_item_id($item_id);
                    if ($child_item_id && in_array($child_item_id, $suborder_items_ids)) {
                        $line_items[$child_item_id]['qty'] = max($qty, 0);
                    }
                }

                foreach ($line_item_totals as $item_id => $total) {
                    
                    $child_item_id = $this->get_vendor_order_item_id($item_id);
                    if ($child_item_id && in_array($child_item_id, $suborder_items_ids)) {
                        $total = wc_format_decimal($total);
                        $child_line_item_refund += $total;
                        $line_items[$child_item_id]['refund_total'] = $total;
                    }
                }

                foreach ($line_item_tax_totals as $item_id => $tax_totals) {
                    // check if there have vendor line item to refund
                    $item = $order->get_item($item_id);
                    if($item->get_meta('vendor_id')){
                        foreach ($tax_totals as $value) {
                            if($value != 0) $create_vendor_refund = true;
                        }
                    }
                    $child_item_id = $this->get_vendor_order_item_id($item_id);
                    if ($child_item_id && in_array($child_item_id, $suborder_items_ids)) {
                        $line_items[$child_item_id]['refund_tax'] = array_map('wc_format_decimal', $tax_totals);
                    }
                }

                //calculate refund amount percentage
                $suborder_refund_amount = ( ( ( $refund_amount - $parent_line_item_refund ) * $suborder_total ) / $parent_order_total );
                $suborder_total_refund = wc_format_decimal($child_line_item_refund + $suborder_refund_amount);

                if (!$refund_amount || $max_refund < $suborder_total_refund || 0 > $suborder_total_refund) {
                    /**
                     * Invalid refund amount.
                     * Check if suborder total != 0 create a partial refund, exit otherwise
                     */
                    $surplus = wc_format_decimal($suborder_total_refund - $max_refund);
                    $suborder_total_refund = $suborder_total_refund - $surplus;
                    $create_refund = $suborder_total_refund > 0 ? true : false;
                }

                if ($create_vendor_refund && $create_refund) {
                    // Create the refund object
                    $refund = wc_create_refund(array(
                        'amount' => $suborder_total_refund,
                        'reason' => $refund_reason,
                        'order_id' => $suborder->get_id(),
                        'line_items' => $line_items,
                        )
                    );
                    if($refund)
                        add_post_meta($refund->get_id(), '_parent_refund_id', $parent_refund_id);
                }
            }
        }
    }

    /**
     * Handle a refund via the edit order screen.
     */
    public static function wcmp_refund_deleted($refund_id, $parent_order_id) {
        check_ajax_referer('order-item', 'security');

        if (!current_user_can('edit_shop_orders')) {
            wp_die( -1 );
        }

        if (!wp_get_post_parent_id($parent_order_id)) {
            global $wpdb;
            $child_refund_ids = $wpdb->get_col($wpdb->prepare("SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key=%s AND meta_value=%s", '_parent_refund_id', $refund_id));

            foreach ($child_refund_ids as $child_refund_id) {
                if ($child_refund_id && 'shop_order_refund' === get_post_type($child_refund_id)) {
                    $order_id = wp_get_post_parent_id($child_refund_id);
                    wc_delete_shop_order_transients($order_id);
                    wp_delete_post($child_refund_id);
                }
            }
        }
    }
    
    /**
     * Handle a refund before save.
     */
    public static function wcmp_create_refund($refund, $args) {
        
        $order = wc_get_order( $args['order_id'] );
        
        if ( ! $order ) {
            throw new Exception( __( 'Invalid vendor order ID.', 'dc-woocommerce-multi-vendor' ) );
        }
        if(is_wcmp_vendor_order($order)) :
            
            $remaining_refund_amount = $order->get_remaining_refund_amount();
            $remaining_refund_items  = $order->get_remaining_refund_items();
            $refund_item_count       = 0;

            // Trigger notification emails.
            if ( ( $remaining_refund_amount - $args['amount'] ) > 0 || ( $order->has_free_item() && ( $remaining_refund_items - $refund_item_count ) > 0 ) ) {
                do_action( 'wcmp_vendor_order_partially_refunded', $order->get_id(), $refund->get_id() );
            } else {
                if ( is_null( $args['reason'] ) ) {
                    $refund->set_reason( __( 'Order fully refunded', 'dc-woocommerce-multi-vendor' ) );
                }
                do_action( 'wcmp_vendor_order_fully_refunded', $order->get_id(), $refund->get_id() );
            }
        endif;
    }
    
    public function get_vendor_order_item_id( $item_id ) {
        global $wpdb;
        $vendor_item_id = $wpdb->get_var( $wpdb->prepare( "SELECT order_item_id FROM {$wpdb->order_itemmeta} WHERE meta_key=%s AND meta_value=%d", '_vendor_order_item_id', absint( $item_id ) ) );
        return $vendor_item_id;
    }

    public static function exclude_coping_order_data() {
        return apply_filters('wcmp_exclude_coping_order_data', array(
            'id', 'parent_id', 'created_via', 'date_created', 'date_modified', 'status', 'discount_total', 'discount_tax', 'shipping_total', 'shipping_tax',
            'cart_tax', 'total', 'total_tax', 'order_key', 'date_completed', 'date_paid', 'number', 'meta_data', 'line_items', 'tax_lines', 'shipping_lines',
            'fee_lines', 'coupon_lines'
        ));
    }

    public function count_processing_order() {
        global $wpdb;

        $count = 0;
        $status = 'wc-processing';
        $order_statuses = array_keys(wc_get_order_statuses());

        if (!in_array($status, $order_statuses)) {
            return 0;
        }

        $cache_key = WC_Cache_Helper::get_cache_prefix('orders') . $status;
        $cache_group = 'wcmp_order';

        $cached = wp_cache_get($cache_group . '_' . $cache_key, $cache_group);

        if ($cached) {
            return 0;
        }

        foreach (wc_get_order_types('order-count') as $type) {
            $query = "SELECT COUNT( * ) FROM {$wpdb->posts} WHERE post_type = %s AND post_status = %s AND post_parent = 0";
            $count += $wpdb->get_var($wpdb->prepare($query, $type, $status));
        }

        wp_cache_set($cache_key, $count, 'counts');
        wp_cache_set($cache_group . '_' . $cache_key, true, $cache_group);
    }
    
    public function shop_order_statuses_get_views($views){
        $user = wp_get_current_user();
        if(current_user_can( 'administrator' ) || in_array('administrator', $user->roles) || in_array('dc_vendor', $user->roles)){
            unset($views['mine']);
        }
        return $views;
    }
    
    public function shop_order_count_orders($counts, $type, $perm = ''){
        global $wpdb;
        $user = wp_get_current_user();
        if($type == 'shop_order' && current_user_can( 'administrator' )){
            $post_statuses = wc_get_order_statuses();
            foreach ($counts as $status => $count) {
                if( array_key_exists($status, $post_statuses) && $count > 0 ){
                    $actual_counts = $wpdb->get_var($wpdb->prepare("SELECT COUNT( * ) FROM {$wpdb->posts} WHERE post_type = %s AND post_status = %s AND post_parent = 0", $type, $status));
                    if($actual_counts != $count){
                        $counts->$status = $actual_counts;
                    }
                }
            }
        }elseif($type == 'shop_order' && in_array('dc_vendor', $user->roles)) {
            $post_statuses = wc_get_order_statuses();
            foreach ($counts as $status => $count) {
                if( array_key_exists($status, $post_statuses) && $count > 0 ){
                    $actual_counts = $wpdb->get_var($wpdb->prepare("SELECT COUNT( * ) FROM {$wpdb->posts} WHERE post_type = %s AND post_status = %s AND post_parent != 0 AND post_author = %s", $type, $status, $user->ID));
                    if($actual_counts != $count){
                        $counts->$status = $actual_counts;
                    }
                }
            }
        }
        return $counts;
    }
    
    public function trash_wcmp_suborder( $order_id ) {
        if ( wp_get_post_parent_id( $order_id ) == 0 ) {
            $wcmp_suborders = $this->get_suborders($order_id);
            if ( $wcmp_suborders ) {
                foreach ( $wcmp_suborders as $suborder ) {
                    wp_trash_post( $suborder->get_id() );
                }
            }
        }
    }
    
    public function delete_wcmp_suborder( $order_id ) {
        if ( wp_get_post_parent_id( $order_id ) == 0 ) {
            $wcmp_suborders = $this->get_suborders($order_id);
            if ( $wcmp_suborders ) {
                foreach ( $wcmp_suborders as $suborder ) {
                    wp_delete_post( $suborder->get_id(), true );
                }
            }
        }
    }
    
    public function wcmp_vendor_order_backend_restriction(){
        if(is_user_wcmp_vendor(get_current_user_id())){
            $inline_css = "
                #order_data .order_data_column a.edit_address { display: none; }
                #order_data .order_data_column .wc-customer-user label a{ display: none; }
                #woocommerce-order-items .woocommerce_order_items_wrapper table.woocommerce_order_items th.line_tax .delete-order-tax{ display: none; }
                #woocommerce-order-items .wc-order-edit-line-item-actions a, #woocommerce-order-items .wc-order-edit-line-item-actions a { display: none; }
                #woocommerce-order-items .add-items .button.add-line-item, #woocommerce-order-items .add-items .button.add-coupon { display: none; }
                ";
            wp_add_inline_style('woocommerce_admin_styles', $inline_css);
        }
        
    }

}
