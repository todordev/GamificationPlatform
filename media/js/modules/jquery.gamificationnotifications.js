;(function ( $, window, document, undefined ) {

	"use strict";
	
    // Create the defaults once
    var pluginName = "GamificationNotifications",
        defaults = {
            resultsLimit: 5
        },
        popoverClicked = false;

    // The actual plugin constructor
    function GamificationNotifications(element, options) {
        this.element = element;

        this.options = $.extend( {}, defaults, options );

        this._defaults = defaults;
        this._name     = pluginName;

        this._numberContainer  = $(this.element).find('#gfy-ntfy-number');
        this._contentContainer = $(this.element).find('#gfy-ntfy-content');
        this.init();
        
        this.displayNumber();
    }

    GamificationNotifications.prototype = {

        init: function() {
        	
        	var self = this;
            
        	$(this.element).on("click", function(event) {
        		event.preventDefault();
        		
        		var timestamp     = new Date().getTime();
        		var resultsLimit  = parseInt(self.options.resultsLimit);
        		
        		$(self._contentContainer).unbind("click");
        		
        		if(!self.popoverClicked) {
        			$.ajax({
        				type: "GET",
        				url: "index.php?option=com_gamification&format=raw&view=notifications&layout=raw&t="+timestamp+"&rl="+resultsLimit,
        				dataType: "html"
        			}).done(function(response){
        				
        				$(self._contentContainer).popover({
        					html: true,
        					placement: "bottom",
        					content: response,
        					container: "body"
        				}).popover('show');

        				self.popoverClicked = true;
        			    
        			});
        		} else {
        			self._contentContainer.popover('destroy')
        			self.popoverClicked = false;
        		}
        		
        		
        	});
        	
        },

        displayNumber: function(element, options) {
        	
        	var self = this;
        	
        	$.ajax({
        		type: "GET",
        		url: "index.php?option=com_gamification&format=raw&task=notifications.getNumber",
        		dataType: "text json"
        	}).done(function(response){
        		
        		var results = parseInt(response.data.results);
        		
        		if(results > 0) {
        			$(self._numberContainer).text(results).show();
        			var title = $(document).attr("title");
        			
        			$(document).attr("title", "("+ results + ") "+ title) ;
        		}
        	});
        	
        }
    };

    // A really lightweight plugin wrapper around the constructor,
    // preventing against multiple instantiations
    $.fn[pluginName] = function ( options ) {
        return this.each(function () {
            if (!$.data(this, "plugin_" + pluginName)) {
                $.data(this, "plugin_" + pluginName, new GamificationNotifications( this, options ));
            }
        });
    };

})( jQuery, window, document );

