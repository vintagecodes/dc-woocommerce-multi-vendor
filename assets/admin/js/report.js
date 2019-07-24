jQuery(document).ready(function($) {
    function vendor_report_sort(b) {
        report_data = {
            action: "vendor_report_sort",
            sort_choosen: b,
            total_sales_data: wcmp_report_vendor.total_sales_arr,
        }, $.post(ajaxurl, report_data, function(b) {
            $(".sort_chart").html(b)
        })
    }

    function product_report_sort(b) {
        report_data = {
            action: "product_report_sort",
            sort_choosen: b,
            total_sales_data: wcmp_report_product.total_sales_arr,
        }, $.post(ajaxurl, report_data, function(b) {
            $(".product_sort_chart").html(b)
        })
    }
    $(".vendor_report_sort").change(function() {
        $(".high_to_low").is(":checked") ? ($(".low_to_high").prop("checked", !1), selected_sorting = $(".vendor_report_sort").val(), sorting_order = selected_sorting + "_desc", vendor_report_sort(sorting_order)) : ($(".low_to_high").prop("checked", !0), selected_sorting = $(".vendor_report_sort").val(), sorting_order = selected_sorting + "_asc", vendor_report_sort(sorting_order))
    }), $(".low_to_high_btn_vendor").click(function() {
        $(".high_to_low").prop("checked", !1), $(".low_to_high").prop("checked", !0), $(".low_to_high_btn_vendor").css({
            "background-color": "#ECEAED",
            "border-top": "solid 1px #B2B0B1",
            "border-left": "solid 1px #B2B0B1",
            "border-right": "solid 1px #B2B0B1",
            "border-bottom": "solid 1px #B2B0B1",
            ".dashicons-arrow-up-alt": "#AF9692"
        }), $(".high_to_low_btn_vendor").css({
            "background-color": "#FAF8F9",
            "border-top": "solid 1px #d6c9c6",
            "border-left": "solid 1px #d6c9c6",
            "border-right": "solid 2px #d6c9c6",
            "border-bottom": "solid 3px #d6c9c6",
            ".dashicons-arrow-down-alt": "#AF938F"
        }), $("i.dashicons-arrow-up-alt").css("color", "#AF9692"), $("i.dashicons-arrow-down-alt").css("color", "#AF938F"), selected_sorting = $(".vendor_report_sort").val(), sorting_order = selected_sorting + "_asc", vendor_report_sort(sorting_order)
    }), $(".high_to_low_btn_vendor").click(function() {
        $(".low_to_high").prop("checked", !1), $(".high_to_low").prop("checked", !0), $(".high_to_low_btn_vendor").css({
            "background-color": "#ECEAED",
            "border-top": "solid 1px #B2B0B1",
            "border-left": "solid 1px #B2B0B1",
            "border-right": "solid 1px #B2B0B1",
            "border-bottom": "solid 1px #B2B0B1",
            ".dashicons-arrow-down-alt": "#AF9692"
        }), $(".low_to_high_btn_vendor").css({
            "background-color": "#FAF8F9",
            "border-top": "solid 1px #d6c9c6",
            "border-left": "solid 1px #d6c9c6",
            "border-right": "solid 2px #d6c9c6",
            "border-bottom": "solid 3px #d6c9c6",
            ".dashicons-arrow-down-alt": "#AF938F"
        }), $("i.dashicons-arrow-up-alt").css("color", "#AF938F"), $("i.dashicons-arrow-down-alt").css("color", "#AF9692"), selected_sorting = $(".vendor_report_sort").val(), sorting_order = selected_sorting + "_desc", vendor_report_sort(sorting_order)
    }), $(".vendor_report_search").click(function() {
        vendor_id = $(this).parent().parent().find("select.vendor_info").val(), selected_vendor_data = {
            action: "vendor_search",
            vendor_id: vendor_id,
            start_date: wcmp_report_vendor.start_date,
            end_date: wcmp_report_vendor.end_date
        }, $.post(ajaxurl, selected_vendor_data, function(b) {
            $(".sort_chart").html(b), $(".report_sort").hide()
        })
    }), $(".product_report_sort").change(function() {
        $(".high_to_low").is(":checked") ? ($(".low_to_high").prop("checked", !1), selected_sorting = $(".product_report_sort").val(), sorting_order = selected_sorting + "_desc", product_report_sort(sorting_order)) : ($(".low_to_high").prop("checked", !0), selected_sorting = $(".product_report_sort").val(), sorting_order = selected_sorting + "_asc", product_report_sort(sorting_order))
    }), $(".low_to_high_btn_product").click(function() {
        $(".high_to_low").prop("checked", !1), $(".low_to_high").prop("checked", !0), $(".low_to_high_btn_product").css({
            "background-color": "#ECEAED",
            "border-top": "solid 1px #B2B0B1",
            "border-left": "solid 1px #B2B0B1",
            "border-right": "solid 1px #B2B0B1",
            "border-bottom": "solid 1px #B2B0B1"
        }), $(".high_to_low_btn_product").css({
            "background-color": "#FAF8F9",
            "border-top": "solid 1px #d6c9c6",
            "border-left": "solid 1px #d6c9c6",
            "border-right": "solid 2px #d6c9c6",
            "border-bottom": "solid 3px #d6c9c6"
        }), $("i.dashicons-arrow-up-alt").css("color", "#AF9692"), $("i.dashicons-arrow-down-alt").css("color", "#AF938F"), selected_sorting = $(".product_report_sort").val(), sorting_order = selected_sorting + "_asc", product_report_sort(sorting_order)
    }), $(".high_to_low_btn_product").click(function() {
        $(".low_to_high").prop("checked", !1), $(".high_to_low").prop("checked", !0), $(".high_to_low_btn_product").css({
            "background-color": "#ECEAED",
            "border-top": "solid 1px #B2B0B1",
            "border-left": "solid 1px #B2B0B1",
            "border-right": "solid 1px #B2B0B1",
            "border-bottom": "solid 1px #B2B0B1",
            ".dashicons-arrow-down-alt": "#AF9692"
        }), $(".low_to_high_btn_product").css({
            "background-color": "#FAF8F9",
            "border-top": "solid 1px #d6c9c6",
            "border-left": "solid 1px #d6c9c6",
            "border-right": "solid 2px #d6c9c6",
            "border-bottom": "solid 3px #d6c9c6",
            ".dashicons-arrow-down-alt": "#AF938F"
        }), $("i.dashicons-arrow-up-alt").css("color", "#AF938F"), $("i.dashicons-arrow-down-alt").css("color", "#AF9692"), selected_sorting = $(".product_report_sort").val(), sorting_order = selected_sorting + "_desc", product_report_sort(sorting_order)
    }), $(".product_report_search").click(function() {
        product_id = $(this).parent().find("select#search_product").val(), selected_vendor_data = {
            action: "product_search",
            product_id: product_id,
            orders: wcmp_report_product.orders,
            start_date: wcmp_report_product.start_date,
            end_date: wcmp_report_product.end_date
        }, $.post(ajaxurl, selected_vendor_data, function(b) {
            $(".sort_chart").html(b)
        })
    }), $(".wc_input_price").change(function() {
        commission_data = {
            action: "commission_suggestion",
            price: $(this).val(),
            product_id: $(".product_id").val()
        }, $.post(ajaxurl, commission_data, function(b) {
            $("._commission_price_preview").val(b)
        })
    })
});