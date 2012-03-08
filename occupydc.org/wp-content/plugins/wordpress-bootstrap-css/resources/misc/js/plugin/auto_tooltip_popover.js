jQuery( document ).ready(
	function () {
		jQuery( '*[rel=tooltip],*[data-tooltip=tooltip]' ).tooltip();
		jQuery( '*[rel=popover]')
			.popover(); 
		
		jQuery( '*[data-popover=popover]')
			.popover();
		
		jQuery('.btn').button();
	}
); 