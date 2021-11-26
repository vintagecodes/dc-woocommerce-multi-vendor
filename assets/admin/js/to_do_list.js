jQuery(document).ready(function($) {		
	$('.activate_vendor').click(function (e) {
		 e.preventDefault();
		 var data = {
				action : 'activate_pending_vendor',
				user_id : $(this).attr('data-id')
		 }	
		 $.post(ajaxurl, data, function(responsee) {
		 		 window.location= window.location ;
		 });
	});
	
	$('.reject_vendor').click(function (e) {
		 e.preventDefault();
		 var data = {
				action : 'reject_pending_vendor',
				user_id : $(this).attr('data-id')
		 }
		 $.post(ajaxurl, data, function(responsee) {
		 		 window.location= window.location ;
		 });
	});
	
	$('.vendor_dismiss_submit, .vendor_dismiss_button').click(function (e) {
		e.preventDefault();
		var id = $(this).attr('data-id');
		var reason = $('#dismiss-reason-'+id).val();
		var data_type = $(this).attr('data-type');
		var data = {
				action : 'dismiss_vendor_to_do_list',
				id : id,
				type: $(this).attr('data-type'),
				reason : reason,
		}
		
		$.post(ajaxurl, data, function(responsee) {
			if (data_type == 'user' || data_type == 'shop_coupon' || data_type == 'product' || data_type == 'dc_commission') {
				window.location = window.location;
			} else {} 		 
		});
	});
	
	$('.vendor_transaction_done_button').click(function (e) {
		 e.preventDefault();
		 var data = {
				action : 'transaction_done_button',
				trans_id : $(this).attr('data-transid'),
				vendor_id : $(this).attr('data-vendorid')
		 }	
		 $.post(ajaxurl, data, function(responsee) {
		 		 window.location = window.location ;
		 });
	});
	
});