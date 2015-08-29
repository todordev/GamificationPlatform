jQuery(document).ready(function() {

	jQuery("#js-gfy-notifications").on("click", ".js-gfy-btn-remove-notification", function(event){

		event.preventDefault();
		
		var id 		  =  jQuery(this).data("element-id");
		var elementId = "#js-gfy-note-element"+id;

		var fields = {
			id: id,
			format: "raw"
		};
		
		jQuery.ajax({
			type: "POST",
			url: "index.php?option=com_gamification&task=notification.remove",
			dataType: "text json",
			data: fields
		}).done(function() {
			jQuery(elementId).fadeOut('slow', function() {
				jQuery(this).remove();
			});
		});
		
	});
});
