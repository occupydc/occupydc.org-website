jQuery( document ).ready(
	function () {
		jQuery( '*[rel=twipsy],*[data-twipsy=twipsy]' ).twipsy( { live: true } );
		jQuery( '*[rel=popover]')
			.popover( { offset: 10 } )
			.click( function(e) { e.preventDefault() } ); 
		
		jQuery( '*[data-popover=popover]')
			.popover( { offset: 10 } );
	}
); 