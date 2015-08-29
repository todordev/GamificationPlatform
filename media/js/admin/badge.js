jQuery(document).ready(function() {
	
	// Validation script
    Joomla.submitbutton = function(task){
        if (task == 'badge.cancel' || document.formvalidator.isValid(document.getElementById('adminForm'))) {
            Joomla.submitform(task, document.getElementById('adminForm'));
        }
    };

    jQuery("#gfy-remove-image").on("click", function(event){
        event.preventDefault();

        if (confirm(Joomla.JText._("COM_GAMIFICATION_DELETE_IMAGE_QUESTION"))) {
            window.location = jQuery(this).attr("href");
        }

    });

});