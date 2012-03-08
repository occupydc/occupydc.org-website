<?php

function rpf_misc_page(){
	$rpf_options = get_option('rpf_options');
	if($_POST['rpf_misc_update'] == 'rpf_misc_update' ){
		$rpf_options['custom_template'] = str_replace('\\','',$_POST['custom_template']);
		$rpf_options['word_limit'] = absint($_POST['word_limit']);
		update_option('rpf_options',$rpf_options);
		echo	'<div id="message" class="updated fade"><p>Saved!</p></div>';
	}
?>
<div class="wrapper">
		<h2>Misc Settings</h2>
		<form method="post" action="">
	<table style="margin-top: 1em;" class="widefat">
	<thead><tr><th scope="col">Content Options</th></tr></thead>

	<tbody>
	<tr>
	<td scope="col">
		
		<b>Word limit(0 means full content):</b><br />
		<p><textarea name="word_limit" rows="1" cols="2"><?php echo $rpf_options['word_limit'];?></textarea></p>
		<br />

	
	</td>
	</tr>

	</tbody>
	</table>

	<table style="margin-top: 1em;" class="widefat">
	<thead><tr><th scope="col">Custom Article Source Template</th></tr></thead>

	<tbody>
	<tr>
	<td scope="col">
	
		
		<p><textarea name="custom_template" rows="2" cols="100"><?php echo $rpf_options['custom_template'];?></textarea></p>
		<p>Variables you can include in the Custom Article Source Template</p>
		<p><b>%FEED_URL%</b> the feed url</p>
		<p><b>%FEED_NAME%</b> the feed name</p>
		<p><b>%SOURCE_URL%</b> the source URL of the post</p>
		<p><b>%AUTHOR%</b> the author you assign to the feed</p>
		<p><b>%CATEGORY%</b> the category you assign to the feed</p>
		<p><b>If no %SOURCE_URL% found, RSS Poster will automatically append it to the end.</b></p>
			
	</td>
	</tr>
	</tbody>
	</table>
	<p><input type="hidden" name="rpf_misc_update" value="rpf_misc_update" /></p>
	<p><input class="button-primary" type="submit" name="save" value="Save" /></p>
		
	</form>
</div>
<?php
}
?>
