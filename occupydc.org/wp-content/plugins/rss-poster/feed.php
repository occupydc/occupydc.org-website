<?php

add_action('admin_footer','rpf_feed_js');

function rpf_print_feeds($feeds=''){
	?>

<thead><tr><td></td><td>Feed Name</td><td>Feed URL</td><td>Category</td><td>Tags(Separated by comma)</td><td>Author</td><td>Update Every(Hours)</td><td>Get Articles</td><td>Post Status</td><td>Publish Date</td></tr></thead>

<?php

		
		$x++;
		if($feeds){
			usort($feeds,'rpf_sort');
			while (list($name, $ops) = each($feeds)) {

				echo "
					<tr>
					<td><input type='hidden'value='{$ops['id']}' name='rpf_feed[$x][id]'></td>
					<td><input type='text' style='width:90%;' size='25' value='{$ops['name']}' name='rpf_feed[$x][name]' /></td>
					<td><input type='text' style='width:90%;' size='45' value='{$ops['url']}' name='rpf_feed[$x][url]' /></td>
					<td><select name='rpf_feed[$x][category]'>". rpf_print_categories($ops['category'])."</select></td>
					<td><input type='text' style='width:90%;' value='{$ops['tags']}' name='rpf_feed[$x][tags]'></input></td>
					<td><select name='rpf_feed[$x][author]'>". rpf_print_authors($ops['author'])."</select></td>
					<td><select name='rpf_feed[$x][frequency]'>".rpf_print_hours($ops['frequency'])."</select></td>
					<td><input type='text' size='2' value = '{$ops['max_items']}' name='rpf_feed[$x][max_items]'></td>
					<td><select name='rpf_feed[$x][post_status]'>".rpf_print_post_status($ops['post_status'])."</select></td>
					<td><select name='rpf_feed[$x][publish_date]'>".rpf_print_publish_date($ops['publish_date'])."</select></td>
					<td><input onclick='rpf_feed_js({$ops['id']})' class='button' type='button' value='Fetch Now'></input></td>

					</tr>
	
					";

				$x++;

			}

		}
		
}

function rpf_feed_page(){
	
	$rpf_options=get_option('rpf_options');
	

	if (isset($_POST['rpf_save'])) {
		
		$feeds=$_POST['rpf_feed'];
		
		foreach($feeds as $feed){

			rpf_update_feed($feed);
	
		}

		echo	'<div id="message" class="updated fade"><p>Saved!</p></div>';

	}elseif (isset($_POST['rpf_reset'])) {

		rpf_reinstall();
		echo	'<div id="message" class="updated fade"><p>You are done reinstalling RSS Poster!</p></div>';



	}elseif($_GET['action'] == 'done'){

		echo	'<div id="message" class="updated fade"><p>Fetch Done! See <a href="admin.php?page=rpf_log_page&s=logs&p=last">log</a> for details.</p></div>';
	}
	


?>


	<div class="wrapper">

<form method="post">
<h2>RSS Poster - Free Version</h2>		
<h4>RSS Poster - Free Version will automatically grab FULL articles from up to five RSS Feeds.</h4>
<h2>Instruction</h2>
<p style='font-size:13px;'>To add a feed, simply fill the Feed Name and Feed URL boxes with related data.Then select the other options as preferred.</p>
<p style='font-size:13px;'>To replace a feed, write over the previous options.</p>
<p style='font-size:13px;'>To delete a feed, simply leave Feed Name or Feed URL box blank.</p>
<p style='font-size:13px;'>When finished hit the UPDATE SETTINGS button below, that's it!</p>
<p style='font-size:13px;'><b>Please note: </b>RSS Poster will automatically process all feeds to grab full articles at a specific interval and you don't need to handle with complicated cron job command. That's automated!</p>
<p style='font-size:13px;'>Please read the <a href="http://www.wprssposter.com/faq.html">FAQ</a> of RSS Poster.</p>
<p style='font-size:13px;'>Leave your feedback <a href="http://www.wprssposter.com/blog/">here</a>.</p>

<h4>Popular NEWS Feeds</h4>
<p style='font-size:11px;'>http://news.google.com/news?q=your keyword&output=rss</p>
<p style='font-size:11px;'>http://news.search.yahoo.com/news/rss?p=your keyword</p>
<p style='font-size:11px;'>http://api.bing.com/rss.aspx?Source=News&Market=en-US&Version=2.0&Query=your keyword</p>

<img src="<?php echo RPF_URL_ROOT;?>images/ajax-loader.gif" id="wait" style="display:none;"/>

<div id="TBcontent" style="display: none;">
<p>Do you want to see log for details?</p>
<input type="submit" id="TByes" value="Yes" />
<input type="submit" id="TBno" value="No" />
</div>
<table width="70%" id="rpf_feed">
<?php rpf_print_feeds(rpf_get_feeds());?>
</table>
<p><input type="submit" class="button-primary" value="UPDATE SETTINGS" name="rpf_save"></p>
<p><input type="submit" onclick="return confirm('Backup your data before reinstalling!')" class="button" value="Reinstall RSS Poster" name="rpf_reset"></p>
</form>
<b>Extra Features on RSS Poster PRO Version.</b><br>
- <a href="http://www.wprssposter.com/faq.html#rewrite">Rewriter Feature</a>.<br>
- Unlimited feeds option. <br>
- Feeds Data Export.<br>
- Feeds Data Import. <br>
- Custom time interval between posts of each feed.<br>
- Cache image or not.<br>
- Control the frequency that RSS Poster will automatically process and update all feeds.<br>
- Optional Cron Job.<br>
- Ping.fm Module.<br>
- Onlywire.com Module.<br>
- Excluding/Including post when fetching.<br>
- Remove "Related Posts" or something from original content.<br>
- Display article source or not.<br>
<a href="http://www.wprssposter.com/purchase.html">Get the PRO Version</a>

</div>
<?php
}
function rpf_sort($a,$b){

	return ($a['id']>$b['id'])?1:-1;

}
function rpf_print_categories($id=''){
	
	$categories=  get_categories('hide_empty=0');
	$results='';
	foreach ($categories as $cat) {

		$option = "<option value='". $cat->cat_ID . "'";

		if($id == $cat->cat_ID )
$option .= " selected='selected' ";

		$option .= '>';

		$option .= $cat->cat_name;

		$option .= '</option>';

		$results.=$option;
	}
            
	return $results;
}

function rpf_print_post_status($opt=''){
	
	$status=array('publish','draft');
	foreach ($status as $s) {

		$option = "<option value='". $s . "'";

		if($opt == $s )
$option .= " selected='selected' ";

		$option .= '>';

		$option .= $s;

		$option .= '</option>';

		$results.=$option;
	}
            
	return $results;
}

function rpf_print_hours($opt=''){
	
	$default=array(4,8,16,24,32,40,48);
	foreach($default as $i){

		$option = "<option value='". $i . "'";

		if($opt == $i )
$option .= " selected='selected' ";

		$option .= '>';

		$option .= $i;

		$option .= '</option>';

		$results.=$option;
	}
            
	return $results;
}
function rpf_print_authors($opt){

	global $wpdb;
	$users = $wpdb->get_results("SELECT user_id,meta_value FROM $wpdb->usermeta WHERE (meta_key = 'nickname')",ARRAY_A);
	
	foreach($users as $user){

		$option = "<option value='". $user['user_id'] . "'";

		if($opt == $user['user_id'] )
			$option .= " selected='selected' ";

		$option .= '>';

		$option .= $user['meta_value'];

		$option .= '</option>';

		$results.=$option;

	}
	return $results;



}
function rpf_print_publish_date($opt=''){
	
	$status=array('Publish Immediately','RSS Publication Date');
	foreach ($status as $s) {

		$option = "<option value='". $s . "'";

		if($opt == $s )
$option .= " selected='selected' ";

		$option .= '>';

		$option .= $s;

		$option .= '</option>';

		$results.=$option;
	}
            
	return $results;
}


function rpf_feed_js(){
?>

<script type='text/javascript'>
function rpf_feed_js(feed_id){
	feed_id="0"+feed_id;

	showDiv("#wait");
	
	jQuery('body').fadeTo('slow',0.5);



	jQuery.ajax({
	type : 'POST',
	url : "<?php echo RPF_PROCESS_FEED_FILE; ?>",
	data: {
		feed_id : feed_id
		
	},
	timeout: 600000,
	async: true,
	dataType: "html",
	success : function(data){
		jQuery('#wait').hide();
		window.location.replace("admin.php?page=rpf_feed_page&action=done");
		
	},
	error: function (xhr, status, error) {
        	alert("Error! " + xhr.statusText);
    	}
	});
	



}

function showDiv(obj){
 jQuery(obj).show();
 center(obj);
 jQuery(window).scroll(function(){
  center(obj);
 });
 jQuery(window).resize(function(){
  center(obj);
 }); 
}
function center(obj){
 var windowWidth = document.documentElement.clientWidth;   
 var windowHeight = document.documentElement.clientHeight;   
 var popupHeight = jQuery(obj).height();   
 var popupWidth = jQuery(obj).width();    
 jQuery(obj).css({   
  "position": "absolute",   
  "top": (windowHeight-popupHeight)/2+jQuery(document).scrollTop(),   
  "left": (windowWidth-popupWidth)/2   
 });  
}

</script>
<?php
}
?>
