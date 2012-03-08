<?php

$opt = array();
	// Update Settings
if ( isset($_POST['submit']) ) {
	if (!current_user_can('manage_options')) die(__('You cannot edit the search-by-category options.'));
	check_admin_referer('your_plugin-updatesettings');

	// Get our new option values
	$var1					= $_POST['var1'];
	$var2					= $_POST['var2'];
	$var3					= $_POST['var3'];
	$var4					= $_POST['var4'];
	$type					= $_POST['type'];

	for($i = 1; $i < 21; $i++) {
	$getval = $_POST['opt'.$i];
	$opt[$i]					= $getval;
	}

	// Update the DB with the new option values
	update_option("popyournotes-var1", $var1);
	update_option("popyournotes-var2", $var2);
	update_option("popyournotes-var3", $var3);
	update_option("popyournotes-var4", $var4);
	update_option("popyournotes-type", $type);

	for($i = 1; $i < 21; $i++) {
	update_option("popyournotes-option".$i, $opt[$i]);
	}

	$success = __("Successfully saved!", "pyn");

}

// Get Current DB Values
	$var1					= stripslashes(get_option("popyournotes-var1"));
	$var2					= stripslashes(get_option("popyournotes-var2"));
	$var3					= stripslashes(get_option("popyournotes-var3"));
	$var4					= stripslashes(get_option("popyournotes-var4"));
	$mode = get_option("popyournotes-type");
	for($i = 1; $i < 21; $i++) {
	$opt[$i]					= stripslashes(get_option("popyournotes-option".$i));
	}

	if($mode == "1"){$pli="Using <a href='http://www.laktek.com' target='_blank'>Really Simple Color Picker</a>.";}if($mode == "2"){$pli="Using <a href='http://recurser.com/articles/2007/12/18/jquery-simplecolor-color-picker/' target='_blank'>jQuery simpleColor plugin</a>.";}

	$pyn_save = __("Save Settings", "pyn");

?>

<div class="wrap">
	<div id="icon-options-general" class="icon32"></div> <h2>Pop Your Notes <?php _e("Settings", "pyn"); ?></h2>

	<?php
	if($success){
	echo("<div id='successbox' style='border:1px solid #666;text-align:center;width:0px;margin:20px 0 0 0;height:20px;'>$success</div>");
	}
	?>

	<form action="" method="post" id="your_plugin-config">
		<table class="form-table">
			<?php if (function_exists('wp_nonce_field')) { wp_nonce_field('your_plugin-updatesettings'); } ?>

			<tr>
				<th scope="row" valign="top" style="width:200px;"><h3><?php _e("Basic Settings", "pyn"); ?></h3></th>
				<td></td>
			</tr>

			<tr>
				<th scope="row" valign="top" style="width:200px;"><label for="var1"><?php _e("Contents:"."<br />"."(HTML Tags allowed.)", "pyn"); ?></label></th>
				<td><textarea type="text" name="var1" id="var1" class="regular-text" rows="10" cols="80"><?php echo($var1); ?></textarea></td>
			</tr>

<?php

$pyn_gt1 = __("Text Color", "pyn");
$pyn_gt2 = __("Background Color", "pyn");

if($mode == "1"){
print <<< HTML

			<tr>
				<th scope="row" valign="top" style="width:200px;"><label for="var2">$pyn_gt1:</label></th>
				<td><input type="text" name="var2" id="var2" value="$var2" style="color:#$var2" /></td>
			</tr>
			<tr>
				<th scope="row" valign="top" style="width:200px;"><label for="var3">$pyn_gt2:</label></th>
				<td><input type="text" name="var3" id="var3" value="$var3" style="color:#$var3" /></td>
			</tr>

HTML;

}elseif($mode == "2"){

print <<< HTML

			<tr>
				<th scope="row" valign="top" style="width:200px;"><label for="var2">$pyn_gt1:</label></th>
				<td><input class='simple_color' name="var2" value='$var2'/></td>
			</tr>
			<tr>
				<th scope="row" valign="top" style="width:200px;"><label for="var3">$pyn_gt2:</label></th>
				<td><input class='simple_color' name="var3" value='$var3'/></td>
			</tr>

HTML;
}

?>

			<tr>
				<th scope="row" valign="top" style="width:200px;"></th>
				<td align="right"><span class="submit" style="border: 0;"><input type="submit" name="submit" class="button-primary" value="<?php echo($pyn_save); ?>" /></span></td>
			</tr>

			<tr>
				<th scope="row" valign="top" style="width:200px;"><h3><?php _e("Style Settings", "pyn"); ?></h3></th>
				<td></td>
			</tr>

			<tr>
				<th scope="row" valign="top" style="width:200px;"><label for="opt1"><?php _e("Border", "pyn"); ?>:</label></th>
				<td>border:<input type="text" name="opt1" id="opt1" value="<?php echo($opt[1]); ?>" size="30" />;</td>
			</tr>

<?php
$pyn_gt3 = __("Overlay Color", "pyn");

if($mode == "1"){
print <<< HTML
			<tr>
				<th scope="row" valign="top" style="width:200px;"><label for="opt1">$pyn_gt3:</label></th>
				<td><input type="text" name="opt2" id="opt2" value="$opt[2]" size="20" /></td>
			</tr>
HTML;
}elseif($mode == "2"){
print <<< HTML
			<tr>
				<th scope="row" valign="top" style="width:200px;"><label for="opt2">$pyn_gt3:</label></th>
				<td><input class='simple_color' name="opt2" value='$opt[2]'/></td>
			</tr>
HTML;
}
?>

			<tr>
				<th scope="row" valign="top" style="width:200px;"><label for="opt3"><?php _e("Overlay Opacity", "pyn"); ?>:</label></th>
				<td><input type="text" name="opt3" id="opt3" value="<?php echo($opt[3]); ?>" size="1" />%</td>
			</tr>
			<tr>
				<th scope="row" valign="top" style="width:200px;"><label for="opt4"><?php _e("Overlay Cursor Type", "pyn"); ?>:</label></th>
				<td><input type="text" name="opt4" id="opt4" value="<?php echo($opt[4]); ?>" size="8" /></td>
			</tr>
			<tr>
				<th scope="row" valign="top" style="width:200px;"><label for="opt5"><?php _e("When clicking Overlay", "pyn"); ?>:</label></th>
				<td><input type="radio" name="opt5" value="false" <?php if($opt[5] == "false"){echo("checked");} ?>/> <?php _e("Do Nothing", "pyn"); ?>　<input type="radio" name="opt5" value="true" <?php if($opt[5] == "true"){echo("checked");} ?>/> <?php _e("Close Modal", "pyn"); ?>　</td>
			</tr>
			<tr>
				<th scope="row" valign="top" style="width:200px;"><label for="opt6"><?php _e("Width(px or %)", "pyn"); ?>:<br /></label></th>
				<td><input type="text" name="opt6" id="opt6" value="<?php echo($opt[6]); ?>" size="20" /></td>
			</tr>
			<tr>
				<th scope="row" valign="top" style="width:200px;"><label for="opt7"><?php _e("Height(px or %)", "pyn"); ?>:<br /></label></th>
				<td><input type="text" name="opt7" id="opt7" value="<?php echo($opt[7]); ?>" size="20" /></td>
			</tr>
			<tr>
				<th scope="row" valign="top" style="width:200px;"></th>
				<td><?php _e("If not set width / height, Modal box will resize own size fit to inner contents.", "pyn"); ?></td>
			</tr>

<?php
$pyn_gt1 = __("Slow", "pyn");
$pyn_gt2 = __("Normal", "pyn");
$pyn_gt3 = __("Fast", "pyn");
?>

			<tr>
				<th scope="row" valign="top" style="width:200px;"><label for="opt10"><?php _e("Showing Duration", "pyn"); ?>:</label></th>
				<td>
				<input type="radio" name="opt10" value="slow" <?php if($opt[10] == "slow"){echo("checked");} ?>/> <?php echo($pyn_gt1); ?>　
				<input type="radio" name="opt10" value="normal" <?php if($opt[10] == "normal"){echo("checked");} ?>/> <?php echo($pyn_gt2); ?>　
				<input type="radio" name="opt10" value="fast" <?php if($opt[10] == "fast"){echo("checked");} ?>/> <?php echo($pyn_gt3); ?>　
				</td>
			</tr>

			<tr>
				<th scope="row" valign="top" style="width:200px;"><label for="opt9"><?php _e("Closing Duration", "pyn"); ?>:</label></th>
				<td>
				<input type="radio" name="opt9" value="slow" <?php if($opt[9] == "slow"){echo("checked");} ?>/> <?php echo($pyn_gt1); ?>　
				<input type="radio" name="opt9" value="normal" <?php if($opt[9] == "normal"){echo("checked");} ?>/> <?php echo($pyn_gt2); ?>　
				<input type="radio" name="opt9" value="fast" <?php if($opt[9] == "fast"){echo("checked");} ?>/> <?php echo($pyn_gt3); ?>　
				</td>
			</tr>

			<tr>
				<th scope="row" valign="top" style="width:200px;"></th>
				<td align="right"><span class="submit" style="border: 0;"><input type="submit" name="submit" class="button-primary" value="<?php echo($pyn_save); ?>" /></span></td>
			</tr>

			<tr>
				<th scope="row" valign="top" style="width:200px;"><h3><?php _e("Conditional Settings", "pyn"); ?></h3></th>
				<td></td>
			</tr>

<?php
$pyn_gt1 = __("Show", "pyn");
$pyn_gt2 = __("ID/Slug(Comma-Separated)", "pyn");
?>

			<tr>
				<th scope="row" valign="top" style="width:200px;"><label for="opt10"><?php _e("Front Page<br />(is_front_page)", "pyn"); ?>:</label></th>
				<td>
				<input type="checkbox" name="opt11" value="1" <?php if($opt[11] == "1"){echo("checked");} ?> /> <?php echo($pyn_gt1); ?>　
				</td>
			</tr>

			<tr>
				<th scope="row" valign="top" style="width:200px;"><label for="opt10"><?php _e("Posts<br />(is_single)", "pyn"); ?>:</label></th>
				<td>
				<input type="checkbox" name="opt12" value="1" <?php if($opt[12] == "1"){echo("checked");} ?> /> <?php echo($pyn_gt1); ?>　<br />
				<?php echo($pyn_gt2); ?>:<input type="text" name="opt13" id="opt13" value="<?php echo($opt[13]); ?>" size="50" />
				</td>
			</tr>

			<tr>
				<th scope="row" valign="top" style="width:200px;"><label for="opt10"><?php _e("Pages<br />(is_page)", "pyn"); ?>:</label></th>
				<td>
				<input type="checkbox" name="opt14" value="1" <?php if($opt[14] == "1"){echo("checked");} ?> /> <?php echo($pyn_gt1); ?>　<br />
				<?php echo($pyn_gt2); ?>:<input type="text" name="opt15" id="opt15" value="<?php echo($opt[15]); ?>" size="50" />
				</td>
			</tr>

			<tr>
				<th scope="row" valign="top" style="width:200px;"><label for="opt16"><?php _e("Archive<br />(is_archive)", "pyn"); ?>:</label></th>
				<td>
				<input type="checkbox" name="opt16" value="1" <?php if($opt[16] == "1"){echo("checked");} ?> /> <?php echo($pyn_gt1); ?>　
				</td>
			</tr>

			<tr>
				<th scope="row" valign="top" style="width:200px;"><label for="opt17"><?php _e("404<br />(is_404)", "pyn"); ?>:</label></th>
				<td>
				<input type="checkbox" name="opt17" value="1" <?php if($opt[17] == "1"){echo("checked");} ?> /> <?php echo($pyn_gt1); ?>　
				</td>
			</tr>

			<tr>
				<th scope="row" valign="top" style="width:200px;"></th>
				<td align="right"><span class="submit" style="border: 0;"><input type="submit" name="submit" class="button-primary" value="<?php echo($pyn_save); ?>" /></span></td>
			</tr>


			<tr>
				<th scope="row" valign="top" style="width:200px;"><h3><?php _e("Advanced Settings", "pyn"); ?></h3></th>
				<td></td>
			</tr>

			<tr>
				<th scope="row" valign="top" style="width:200px;"><label for="opt4"><?php _e("Plugin Arguments", "pyn"); ?>:</label></th>
				<td><textarea type="text" name="opt8" id="opt8" class="regular-text" rows="2" cols="80"><?php echo($opt8); ?></textarea></td>
			</tr>


			<tr>
				<th scope="row" valign="top" style="width:200px;"><label for="var5"><?php _e("Colorpicker Plugin", "pyn"); ?>:</label></th>
				<td><input type="radio" name="type" value="1" <?php if($mode == "1"){echo("checked");} ?>/> <?php _e("Really Simple Color Picker", "pyn"); ?>　<input type="radio" name="type" value="2" <?php if($mode == "2"){echo("checked");} ?>/> <?php _e("jQuery simpleColor plugin", "pyn"); ?>　</td>
			</tr>

			<tr>
				<th scope="row" valign="top" style="width:200px;"><label for="var5-info"></label></th>
				<td><?php echo($pli); ?></td>
			</tr>

			<tr>
				<th scope="row" valign="top" style="width:200px;"></th>
				<td align="right"><span class="submit" style="border: 0;"><input type="submit" name="submit" class="button-primary" value="<?php echo($pyn_save); ?>" /></span></td>
			</tr>


			<tr>
				<th scope="row" valign="top" style="width:200px;"><h3><?php _e("About", "pyn"); ?></h3></th>
				<td>
				</td>
			</tr>

			<tr>
				<th scope="row" valign="top" style="width:200px;"><h3></h3></th>
				<td>
				<strong>Pop Your Notes</strong> - 1.0.1<br />
				Fueled by <a href='http://jquery.com/' target="_blank">jQuery</a>, <a href='http://www.ericmmartin.com/projects/simplemodal//' target="_blank">SimpleModal</a>, <a href='http://www.laktek.com' target='_blank'>Really Simple Color Picker</a>, <a href='http://recurser.com/articles/2007/12/18/jquery-simplecolor-color-picker/' target='_blank'>jQuery simpleColor plugin</a>.<br />
				Made as wordpress plugin by Studio Switch LLC, and Licensed under the <a href='http://www.gnu.org/licenses/gpl.html' target='_blank'>GPL</a> license.
				</td>
			</tr>

		</table>

	</form>

<div style="height:30px;"></div>

</div>