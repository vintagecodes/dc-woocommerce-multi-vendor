/* global wcmp_vendor_shipping_script_data */
(function ($) {
    if (typeof wcmp_vendor_shipping_script_data === 'undefined') {
        return false;
    }
    var app = app || {
        build: function () {
            this.init();
            this.bindEvents();
        },

        init: function () {
            this.modify_shipping_methods = '.modify-shipping-methods';
            this.vendor_shipping_methods = '#vendor-shipping-methods';
            this.shipping_by_zone_holder = '#wcmp_settings_form_shipping_by_zone';
            this.shipping_zone_table = this.shipping_by_zone_holder + ' .shipping-zone-table';
            this.shipping_zone_list = '.shipping-zone-list';
            this.shipping_method_manage_form = '#wcmp_shipping_method_manage_form';
            this.show_shipping_methods = this.vendor_shipping_methods + ' .show-shipping-methods';
            this.add_shipping_methods = this.vendor_shipping_methods + ' .add-shipping-method';
            this.edit_shipping_method = this.vendor_shipping_methods + ' .edit-shipping-method';
            this.update_shipping_method = this.vendor_shipping_methods + ' .update-shipping-method';
            this.delete_shipping_method = this.vendor_shipping_methods + ' .delete-shipping-method';
            this.limit_zone_location = this.vendor_shipping_methods + ' #limit_zone_location';
            this.method_status = this.vendor_shipping_methods + ' .method-status';
            this.modal_close_link = '.modal-close-link';
            this.modal_dialog = '.wcmp-modal-dialog';
        },

        bindEvents: function () {
            $(this.modify_shipping_methods).on('click', this.modifyShippingMethods.bind(this));
            $(document).on('zone_settings_loaded', this.zoneLoadedEvents.bind(this));
            $( document.body ).on( 'change', '.wc-shipping-zone-method-selector select', this.onChangeShippingMethodSelector );
            /* delegate events */
            $(document).delegate(this.shipping_zone_list, 'click', this.goToShippingZones.bind(this));
            $(document).delegate(this.show_shipping_methods, 'click', this.showShippingMethods.bind(this));
            $(document).delegate(this.add_shipping_methods, 'click', this.addShippingMethod.bind(this));
            $(document).delegate(this.edit_shipping_method, 'click', this.editShippingMethod.bind(this));
            $(document).delegate(this.update_shipping_method, 'click', this.updateShippingMethod.bind(this));
            $(document).delegate(this.delete_shipping_method, 'click', this.deleteShippingMethod.bind(this));
            $(document).delegate(this.limit_zone_location, 'click', this.limitZoneLocation.bind(this));
            $(document).delegate(this.method_status, 'change', this.toggleShippingMethod.bind(this));
            $(document).delegate(this.modal_close_link, 'click', this.closeModal.bind(this));
        },

        modifyShippingMethods: function (event, zoneID) {
            var appObj = this;
            $('#wcmp_settings_form_shipping_by_zone').block({
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            });
            
            if (typeof event !== "undefined") {
                event.preventDefault();
                zoneID = $(event.currentTarget).data('zoneId');
            }

            var ajaxRequest = $.ajax({
                method: 'post',
                url: wcmp_vendor_shipping_script_data.ajaxurl,
                data: {
                    action: 'wcmp-get-shipping-methods-by-zone',
                    zoneID: zoneID,
                },
                success: function (response) {
                    $(appObj.vendor_shipping_methods).html(response.data.html).show();
                    $(appObj.shipping_zone_table).hide();
                },
                complete: function () {
                    $('#wcmp_settings_form_shipping_by_zone').unblock();
                    $(document).trigger('zone_settings_loaded');
                }
            });
        },

        zoneLoadedEvents: function (event) {
            this.limitZoneLocation(event);
        },

        goToShippingZones: function (event) {
            event.preventDefault();
            $(this.vendor_shipping_methods).html('').hide();
            $(this.shipping_zone_table).show();
            window.location.reload();
        },
        
        onChangeShippingMethodSelector: function() {
            var description = $( this ).find( 'option:selected' ).data( 'description' );
            $( this ).parents('.wc-shipping-zone-method-selector').find( '.wc-shipping-zone-method-description' ).html( '' );
            $( this ).parents('.wc-shipping-zone-method-selector').find( '.wc-shipping-zone-method-description' ).html( description );
        },

        showShippingMethods: function (event) {
            event.preventDefault();

            /* make popup */
            $('#wcmp_shipping_method_add_container').show();
            $('#wcmp_shipping_method_add_container ' + this.modal_dialog).show();
        },

        addShippingMethod: function (event) {
            event.preventDefault();

            var appObj = this;

            var zoneId = $('#zone_id').val(),
                    shippingMethod = $('#shipping_method option:selected').val();
            if (zoneId == '') {
                // alert(wcmp_dashboard_messages.shiping_zone_not_found);
            } else if (shippingMethod == '') {
                // alert(wcmp_dashboard_messages.shiping_method_not_selected);
            } else {
                var data = {
                    action: 'wcmp-add-shipping-method',
                    zoneID: zoneId,
                    method: shippingMethod
                };

                $('#wcmp_shipping_method_add_button').block({
                    message: null,
                    overlayCSS: {
                        background: '#fff',
                        opacity: 0.6
                    }
                });

                // $('#wcmp_settings_save_button').click();

                var ajaxRequest = $.ajax({
                    method: 'post',
                    url: wcmp_vendor_shipping_script_data.ajaxurl,
                    data: data,
                    success: function (response) {
                        if (response.success) {
                            $('#wcmp_shipping_method_add_container').hide();
                            appObj.modifyShippingMethods(undefined, zoneId);
                        } else {

                        }
                    },
                });
            }
        },

        editShippingMethod: function (event) {
            event.preventDefault();
            $( '.wcmp-zone-method-content' ).block({
                    message: null,
                    overlayCSS: {
                            background: '#fff',
                            opacity: 0.6
                    }
            });
            
            var instanceId = $(event.currentTarget).parents('.edit_del_actions').data('instance_id'),
                methodId = $(event.currentTarget).parents('.edit_del_actions').data('method_id'),
                zoneId = $(event.currentTarget).parents('.edit_del_actions').data('zone_id'),
                data = {
                    action: 'wcmp-vendor-configure-shipping-method',
                    zoneId: zoneId,
                    instanceId: instanceId,
                    methodId: methodId
                };
                
            var ajaxRequest = $.ajax({
                method: 'post',
                url: wcmp_vendor_shipping_script_data.ajaxurl,
                data: data,
                success: function (response) {
                    if(response){
                        $( '.wcmp-zone-method-content' ).unblock();
                        /* make popup */
                        $('#wcmp_shipping_method_edit_container #method_id_selected').val(methodId);
                        $('#wcmp_shipping_method_edit_container #instance_id_selected').val(instanceId);
                        $('#wcmp_shipping_method_edit_container #zone_id_selected').val(zoneId);
                        $('#shipping-form-fields').html(response.settings_html);
                        $('#wcmp_shipping_method_edit_container').show();
                    }
                },
            });
        },

        updateShippingMethod: function (event) {
            event.preventDefault();

            var appObj = this;

            var methodID = $('#wcmp_shipping_method_edit_container #method_id_selected').val(),
                    instanceId = $('#wcmp_shipping_method_edit_container #instance_id_selected').val(),
                    zoneId = $('#zone_id').val(),
                    data = {
                        action: 'wcmp-update-shipping-method',
                        zoneID: zoneId,
                        args: {
                            instance_id: instanceId,
                            zone_id: zoneId,
                            method_id: methodID,
                            settings: $('#wcmp-vendor-edit-shipping-form').serializeArray()
                        }
                    };

            $('#wcmp_shipping_method_edit_button').block({
                message: null,
                overlayCSS: {
                    background: '#fff',
                    opacity: 0.6
                }
            });
            // $('#wcmp_settings_save_button').click();

            var ajaxRequest = $.ajax({
                method: 'POST',
                url: wcmp_vendor_shipping_script_data.ajaxurl,
                data: data,
                success: function (response) {
                    if (response.success) {
                        appObj.modifyShippingMethods(undefined, zoneId);
                    } else {
                        alert(response.data);
                    }
                },
            });
        },

        deleteShippingMethod: function (event) {
            event.preventDefault();

            var appObj = this;

            if (confirm(wcmp_vendor_shipping_script_data.i18n.deleteShippingMethodConfirmation)) {
                var currentTarget = $(event.target).is(this.delete_shipping_method) ? event.target : $(event.target).closest(this.delete_shipping_method),
                        instance_id = $(currentTarget).parents('.edit_del_actions').attr('data-instance_id'),
                        zoneId = $('#zone_id').val();
                var data = data = {
                    action: 'wcmp-delete-shipping-method',
                    zoneID: zoneId,
                    instance_id: instance_id
                };

                if (zoneId == '') {
                    // alert( wcmp_dashboard_messages.shiping_zone_not_found );
                } else if (instance_id == '') {
                    // alert( wcmp_dashboard_messages.shiping_method_not_found );
                } else {
                    // $('#wcmp_settings_save_button').click();

                    var ajaxRequest = $.ajax({
                        method: 'post',
                        url: wcmp_vendor_shipping_script_data.ajaxurl,
                        data: data,
                        success: function (response) {
                            if (response.success) {
                                appObj.modifyShippingMethods(undefined, zoneId);
                            } else {
                                alert(resp.data);
                            }
                        },
                    });
                }
            }
        },

        limitZoneLocation: function (event) {
            if ($('#limit_zone_location').is(':checked')) {
                $('.hide_if_zone_not_limited').show();
            } else {
                $('.hide_if_zone_not_limited').hide();
            }
        },

        toggleShippingMethod: function (event) {
            event.preventDefault();

            var appObj = this;

            var checked = $(event.target).is(':checked'),
                    value = $(event.target).val(),
                    zoneId = $('#zone_id').val();

            var data = {
                action: 'wcmp-toggle-shipping-method',
                zoneID: zoneId,
                instance_id: value,
                checked: checked,
            };

            if (zoneId == '') {
                // alert( wcmp_dashboard_messages.shiping_zone_not_found );
            } else if (value == '') {
                // alert( wcmp_dashboard_messages.shiping_method_not_found );
            } else {
                $('.wcmp-container').block({
                    message: null,
                    overlayCSS: {
                        background: '#fff',
                        opacity: 0.6
                    }
                });

                var ajaxRequest = $.ajax({
                    method: 'post',
                    url: wcmp_vendor_shipping_script_data.ajaxurl,
                    data: data,
                    success: function (response) {
                        if (response.success) {
                            $('.wcmp-container').unblock();
                        } else {
                            $('.wcmp-container').unblock();
                            alert(response.data);
                        }
                    },
                });
            }
        },

        closeModal: function (event) {
            event.preventDefault();

            var appObj = this;

            var modalDialog = $(event.target).parents(appObj.modal_dialog);

            if (modalDialog.length) {
                modalDialog.hide();
                $(this.modal_dialog).hide();
            }
        },
    };

    $(app.build.bind(app));

})(jQuery);