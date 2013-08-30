jQuery(document).ready(function() {
	
	var listOrder   = document.getElementById("filter_order").value;
	
	Joomla.orderTable = function() {
		var table 	  	= document.getElementById("sortTable");
		var direction 	= document.getElementById("directionTable");
		var order 		= table.options[table.selectedIndex].value;
		var listOrder   = document.getElementById("filter_order").value;
		
		if (order != listOrder) {
			dirn = 'asc';
		} else {
			dirn = direction.options[direction.selectedIndex].value;
		}
		Joomla.tableOrdering(order, dirn, '');
	}
	
	// Clear the search filter and submit the form
	jQuery("#search-filter-clear").on("click", function(){
		jQuery("#filter_search").val("");
		jQuery("#adminForm").submit();
	});
	
});