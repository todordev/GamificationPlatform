/**
 * JavaScript Helper
 */
var GamificationHelper = {
		
	displayMessageSuccess: function(title, text) {
		
	    jQuery.pnotify({
	        title: title,
	        text: text,
	        icon: "icon-ok",
	        type: "success",
        });
	},
	displayMessageFailure: function(title, text) {
		
		jQuery.pnotify({
	        title: title,
	        text: text,
	        icon: 'icon-warning-sign',
	        type: "error",
        });
	}
}