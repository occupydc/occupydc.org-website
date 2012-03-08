<?php

function popyournotes_do( ) {
	global $wp_query, $post;

	$var1		= stripslashes(get_option("popyournotes-var1"));
	$var2		= stripslashes(get_option("popyournotes-var2"));
	$var3		= stripslashes(get_option("popyournotes-var3"));
	for($i = 0; $i < 21; $i++) {
	$opt[$i]					= stripslashes(get_option("popyournotes-option".$i));
	}

	$plug_path = str_replace("\\", "/", plugin_dir_url( __FILE__ ));
	$plug_path = substr($plug_path, 0, -1);

if(! $opt[1]){$opt[1] = "4px solid #444";}
if(! $opt[2]){$opt[2] = "#666";}
if(! $opt[3]){$opt[3] = "50";}
if(! $opt[4]){$opt[4] = "wait";}
if(! $opt[5]){$opt[5] = "false";}

if(! $opt[9]){$opt[9] = "normal";}
if(! $opt[10]){$opt[10] = "slow";}

if($opt[6]){$opt[6] = "width:".$opt[6].";";}
if($opt[7]){$opt[7] = "height:".$opt[7].";";}

if($opt[13]){
$opt[13] = explode(",", $opt[13]);
}
if($opt[15]){
$opt[15] = explode(",", $opt[15]);
}

function doif($plug_path,$var1,$var2,$var3,$opt){

print <<< HTML
<STYLE TYPE="text/css">
#basic-modal-content {display:none;}
#simplemodal-overlay {background-color:$opt[2]; cursor:wait;}
#simplemodal-container { $opt[6] $opt[7] color:$var2; background-color:$var3; border:$opt[1];}
#simplemodal-container a.modalCloseImg {background:url($plug_path/x.png) no-repeat; width:25px; height:29px; display:inline; z-index:3200; position:absolute; top:-15px; right:-16px; cursor:pointer;}
#simplemodal-container #basic-modal-content {padding:8px;}
#popyournotes{display:none;}
</STYLE>

<script>
jQuery(function(){
jQuery("#popyournotes").modal({opacity:"$opt[3]",autoResize:true,overlayClose:$opt[5],
    onOpen: function(dialog){
	dialog.overlay.fadeIn('$opt[10]', function () {
		dialog.data.hide();
		dialog.container.fadeIn('$opt[10]', function () {
			dialog.data.slideDown('$opt[10]');
		});
	});
    },
    onClose: function(dialog){
	dialog.data.fadeOut('$opt[9]', function () {
		dialog.container.slideUp('$opt[9]', function () {
			dialog.overlay.fadeOut('$opt[9]', function () {
				jQuery.modal.close();
				jQuery("#popyournotes").remove();
			});
		});
	});
    }
});
});
</script>
<div id="popyournotes">$var1</div>
HTML;

}


	if($opt[11] == "1"){
		if(is_front_page()){
			doif($plug_path,$var1,$var2,$var3,$opt);
		}
	}
	if($opt[12] == "1"){
		if(is_single($opt[13])){
			doif($plug_path,$var1,$var2,$var3,$opt);
		}
	}
	if($opt[14] == "1"){
		if(is_page($opt[15]) && ! is_front_page()){
			doif($plug_path,$var1,$var2,$var3,$opt);
		}
	}
	if($opt[16] == "1"){
		if(is_archive()){
			doif($plug_path,$var1,$var2,$var3,$opt);
		}
	}
	if($opt[18] == "1"){
		if(is_404()){
			doif($plug_path,$var1,$var2,$var3,$opt);
		}
	}


}

add_action('wp_head', 'popyournotes_do');

?>