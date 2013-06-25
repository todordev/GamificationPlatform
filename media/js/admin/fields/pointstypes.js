window.addEvent("domready", function() {
			
    function updatePointsValues() {
    	
    	var values = $$(".points-type");
        
        var pointsTypeValues = new Array();
        Array.each(values, function(value, index){
                    
            var pointsTypeObject = {
                id: value.get("data-id"),
                value: value.get("value")
            };
            
            pointsTypeValues.push(pointsTypeObject);
            
        });
                    
        var pointsType = JSON.encode(pointsTypeValues);
    	document.id("jform_params_points_types").set("value", pointsType);
    	
    }
            
    document.id("points-elements").addEvent("keyup:relay(.points-type)", function(event, target){
        
        event.preventDefault();
        updatePointsValues();
        
	});
    
    // Update current values on load
    updatePointsValues();
});