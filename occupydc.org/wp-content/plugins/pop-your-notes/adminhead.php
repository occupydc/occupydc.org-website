<?php
$plug_path = str_replace("\\", "/", plugin_dir_url( __FILE__ ));
$plug_path = substr($plug_path, 0, -1);

$var2					= $_POST['var2'];
$var3					= $_POST['var3'];
$opt1					= "border:".$_POST['opt1']." !important;";

print <<< HTML

<link rel="stylesheet" href="$plug_path/really-simple-color-picker/colorPicker.css" type="text/css" />

<script src="$plug_path/really-simple-color-picker/jquery.colorPicker.js" type="text/javascript"></script>
<script src="$plug_path/simpleColor/jquery.simpleColor.js" type="text/javascript"></script>
<script src="$plug_path/jquery.easing.1.3.js" type="text/javascript"></script>

<script>
jQuery(function(){
	jQuery('#var2').colorPicker();
	jQuery('#var3').colorPicker();
	jQuery('#opt2').colorPicker();

	jQuery('.simple_color').simpleColor({
			cellWidth: 9,
			cellHeight: 9,
			border: '1px solid #333333',
			buttonClass: 'button',
			boxWidth: '50px'
	});
	jQuery('input#alert_button1').click( function() {
		alert(jQuery('input.simple_color')[0].value)
	});
	jQuery('input#alert_button2').click( function() {
		alert(jQuery('input.simple_color')[1].value)
	});
	jQuery("div#successbox").animate({ 
	    width: "50%"
	}, 2000, "easeOutBounce" );

});
</script>

<style type="text/css">
	input#alert_button {
		margin-top: 20px;
	}
	.simpleColorDisplay {
		float: left;
	}
	.button {
		clear: right;
	}
	.form-table{
	width:800px;
	border-spacing:none;
	}
	.form-table textarea{
	width:600px;
	}
	div#successbox{
	color: $var2 ;
	background-color: $var3 ;
	$opt1;
	}
</style>

HTML;

?>