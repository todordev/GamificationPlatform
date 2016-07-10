jQuery(document).ready(function($) {

	var containerId = '#'+ $.gfyData.containerId;
	var buttonId    = '#'+ $.gfyData.buttonId;

	$(buttonId).on('click', function(){

		// Generate random index.
		var index = Math.random().toString(36).substring(7);

		var fieldKey = $('<input>', {
			type: "text",
			name: "jform[custom_data]["+index+"][key]",
			placeholder: Joomla.JText._('COM_GAMIFICATION_KEY').toLowerCase()
		});

		var fieldValue = $('<input>', {
			type: "text",
			name: "jform[custom_data]["+index+"][value]",
			placeholder: Joomla.JText._('COM_GAMIFICATION_VALUE').toLowerCase(),
			class: 'input-xxlarge'
		});

		var btnRemove = $('<button class="btn btn-danger btn-mini js-gfy-cdremovebtn" type="button"><i class="icon icon-remove"></i></button>');

		var div = $('<div>', {
			id: index
		});

		div.prepend(fieldKey).append(fieldValue).append(btnRemove);

		$('#'+ $.gfyData.containerId).append(div);
	});

	$(containerId).on('click', '.js-gfy-cdremovebtn', function(){
		$(this).closest('div').remove();
	});

});
