<script type="text/javascript">

jQuery(function($){
window.tb_position = function() {
  var tbWindow = $('#TB_window'), width = $(window).width(), H = $(window).height(), W = ( 720 < width ) ? 720 : width;
  
  // If there is no respect_dimensions=true, then do the weird WordPress override for the dimensions
  if(window.urlNoQuery && urlNoQuery[1] && urlNoQuery[1].indexOf('respect_dimensions=true') == -1) {
    if ( tbWindow.size() ) {
      tbWindow.width( W - 50 ).height( H - 45 );
      $('#TB_iframeContent').width( W - 50 ).height( H - 75 );
      tbWindow.css({'margin-left': '-' + parseInt((( W - 50 ) / 2),10) + 'px'});
      if ( typeof document.body.style.maxWidth != 'undefined' )
        tbWindow.css({'top':'20px','margin-top':'0'});
    };
  // otherwise, do the default Thickbox way and actually use the width and height
  } else {
    var isIE6 = typeof document.body.style.maxHeight === "undefined";
    jQuery("#TB_window").css({marginLeft: '-' + parseInt((TB_WIDTH / 2),10) + 'px', width: TB_WIDTH + 'px'});
    if ( ! isIE6 ) { // take away IE6
      jQuery("#TB_window").css({marginTop: '-' + parseInt((TB_HEIGHT / 2),10) + 'px'});
    }
  }
  

  return $('a.thickbox').each( function() {
    var href = $(this).attr('href');
    if ( ! href || href.indexOf('respect_dimensions=true') != -1) return; // prevent removing of the height and width with this flag
    href = href.replace(/&width=[0-9]+/g, '');
    href = href.replace(/&height=[0-9]+/g, '');
    $(this).attr( 'href', href + '&width=' + ( W - 80 ) + '&height=' + ( H - 85 ) );
  });
};

});



</script>