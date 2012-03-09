jQuery(document).ready(function(){
	var animateSpeed = 500;	
	jQuery("#layout-controls a").click(function(){
		var curClass = jQuery('ul#folio').attr('class');
		var newClass = jQuery(this).attr('class');
		jQuery('ul#folio').fadeOut(animateSpeed,function(){
			jQuery('ul#folio').removeClass(curClass,animateSpeed);
			jQuery('ul#folio').addClass(newClass,animateSpeed);			
		}).fadeIn(animateSpeed);						
		return false;		
	});		
});			