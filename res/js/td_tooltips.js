jQuery(document).ready(function() {  
	jQuery($("a.td-tooltip-trigger")).on('mouseover',function(event){
		var tooltip = jQuery(this).prop("rel");
		var pos = jQuery(this).position();  
		var height = jQuery(this).height();
		var width = jQuery(this).width();  
    
		jQuery("#" + tooltip).css({  
			left: pos.left + 30 + 'px',
			top: pos.top + height + 8 + 'px' 
		});  
    
		jQuery("#" + tooltip).show(); 
    });

	jQuery($("a.td-tooltip-trigger")).on('mouseout',function(){
		var tooltip = jQuery(this).prop("rel");
		jQuery("#" + tooltip).hide(); 
	});
});