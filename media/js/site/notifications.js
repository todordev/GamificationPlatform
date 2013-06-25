jQuery(document).ready(function() {

	jQuery("#js-gfy-notifications").on("click", "a.gfy-btn-remove-notification", function(event){

		event.preventDefault();
		
		var id 		  =  jQuery(this).data("element-id");
		var elementId = "#js-gfy-note-element"+id;
		var url 	  = jQuery(this).attr("href");
		
		var fields = {
			id: id,
			format: "raw"
		};
		
		jQuery.ajax({
			type: "POST",
			url: url,
			dataType: "text json",
			data: fields
		}).done(function(response) {
			jQuery(elementId).fadeOut('slow', function() {
				jQuery(this).remove();
			});
		});
		
	});
});
