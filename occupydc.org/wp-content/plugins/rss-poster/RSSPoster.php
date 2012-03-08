<?php
add_action('the_content','rpf_content_filter');
add_filter('wp_head', 'rpf_init');
$rpf_options = get_option('rpf_options');

function rpf_content_filter($content){
	if(is_admin())
		return $content;
	
	$id = get_post_meta(get_the_ID(), 'rpf_feed_id', true);
	
	if(!$id)
		return $content;//might be normal content

	global $rpf_options;
	if($rpf_options['word_limit'] >0 )
		$content = rpf_truncate($content, $rpf_options['word_limit']);


	$sourcelink=get_post_meta(get_the_ID(), 'rpf_sourcepermalink', true);

	if($sourcelink)
		$link='<p>'.rpf_custom_template($sourcelink,$id).'</p>';

	$content=$content.$link;
	
	return $content;


}

function rpf_custom_template($sourcelink,$feed_id){

	

	global $rpf_options;
	$custom_template = $rpf_options['custom_template'];

	if( false ===  strpos($custom_template, '%SOURCE_URL%' ) )
		$custom_template .= ' <a href="%SOURCE_URL%">%SOURCE_URL%</a> ';

	
	
	$feed = rpf_get_feed($feed_id);
	
	$user = get_userdata($feed['author']);
	$author = $user->nickname;

	$category = get_cat_name($feed['category']);

	$feed_url = $feed['url'];

	$feed_name = $feed['name'];
	
	$custom_template = str_replace(
		array('%SOURCE_URL%','%AUTHOR%','%CATEGORY%','%FEED_URL%','%FEED_NAME%'),
		array($sourcelink,$author,$category,$feed_url,$feed_name),
		$custom_template

	);
	
	return $custom_template;


}
function rpf_truncate($text,$count) { 
	$count=intval($count);
	if(  $count<1 || substr_count($text,' ') < $count )
		return $text;

	$temp = explode(' ', $text);

	$text = implode(' ', array_slice($temp, 0, $count));

	return $text;
	
}
function rpf_init(){
	
	global $rpf_options;
	
	$now_time=time();

	$interval=60*60*3;
	$lastactive=$rpf_options['lastactive'];
	if(!$lastactive){
		$lastactive=$now_time;
		$rpf_options['lastactive']=$lastactive;
		update_option('rpf_options', $rpf_options);
		
	}
	
	if ( ($now_time - $lastactive ) >= $interval ) {
		
		$rpf_options['lastactive'] = $now_time+$interval;
		update_option('rpf_options', $rpf_options);
		rpf_process_feeds();
	}
	
}



function rpf_process_feeds(){

	@set_time_limit(0);
	
	
	$feeds=rpf_get_feeds();
	if($feeds){

		rpf_log("<b>----------Processing all feeds-------------</b>");
		foreach($feeds as $feed){
		
			rpf_process_feed($feed);

		}
	}else{
		
		rpf_log("No feeds data found!");
	}
	

}


function rpf_process_feed($feed){
	@set_time_limit(0);
	if(empty($feed['url'])){
		
		return false;
	}

	
	rpf_log("Processing feed <b>{$feed['url']}</b>");

	$lastactive = $feed['lastactive'];
	
	$now=time();

	$frequency = $feed['frequency']*60*60;
	
	if( ($now - $lastactive) >= $frequency ){
		
		rpf_update_feed_lastactive($feed['id'],$now);
		
	}else{
		
		rpf_log("It's not the time to update <b>{$feed['url']}</b>. <a href='http://www.wprssposter.com/faq.html#cronjob'>Explanation</a>");
		return false;
	}

	global $rpf_options;

	$max_items=$feed['max_items'];

	if(empty($max_items)||!is_numeric($max_items))

		$max_items=3;
	
	$simplepie = rpf_fetch_feed($feed['url'],$max_items);
	$error=$simplepie->error();

	if($error){
		
		rpf_log("Feed Error: <b>$error</b>");
		return false;
	}
	
    	$count = 0;
    
	foreach($simplepie->get_items() as $item){
		if( rpf_is_duplicate(  $item->get_title() ) ){
       			rpf_log('Filtering duplicate post');
        		continue;
		}
		if(false == rpf_process_item($item,$feed))
			continue;
		$count++;
		if($count == $max_items)
			break;
      	}

    	if($count==0)
		rpf_log("No new or qualified post for <b>{$feed['url']}</b> <a href='http://www.wprssposter.com/faq.html#newandqualified'>Explanation</a>");
	else
		rpf_log( "Fetch $count posts from <b>{$feed['url']}</b>" );
		
    	
    	return true;
    

}
function rpf_process_item($item,$feed){
	

	global $wpdb;

	$title = $item->get_title();

	$link = $item->get_permalink();

	if(false !== strpos($link,'news.google.com')){
		$link=urldecode(substr($link,strpos($link,'url=')+4));
	}elseif(false !== strpos($link,'/**')){
		$link=urldecode(substr($link,strpos($link,'/**')+3));
		
	}	
	
	$content = rpf_full_feed($link);
	
	if(!$content ){
		//rpf_log("Cannot grab full content from <b>$link</b>");
		return false;
	}
	$title=rpf_title_fix($title);

	$content=rpf_content_fix($content);

	if(empty($title)||empty($content)){
		
		return false;
	}

	$content=rpf_parse_images($content,$item->get_base());
	$meta = array(
	     'rpf_feed_id' => $feed['id'],
	     'rpf_sourcepermalink' => $link
    	);
	
	$rpf_date = date("Y-m-d H:i:s", time());
	if($feed['publish_date'] === 'Publish Immediately'){

	}elseif($feed['publish_date'] === 'RSS Publication Date'){
		
		$rpf_date = $item->get_date("Y-m-d H:i:s");
	}


	
	rpf_insert_post( $title,$content,$feed['category'],$feed['tags'],$meta,$feed['post_status'],$rpf_date,$feed['author']);
 	rpf_log("<b>{$title}</b> added");
	global $wpdb;
	global $rpf_options;
	$hash=rpf_item_hash($item->get_title());
	$sql="insert into {$rpf_options['db']['post']} (hash) values ('{$hash}')";
	$wpdb->query($sql);

	return true;


}
function rpf_title_fix($title){
	if($title && strpos($title,' - ')){
		
		$backup=$title;
		$backup=preg_replace('/([-])/','$1[D]',$backup);

		$backup=explode('[D]',$backup);

		if( strlen($backup[0])>10 || count($backup)>=2 )
			unset($backup[count($backup)-1]);
		else
			return $title;
		
		$title=trim(implode('',$backup),' - ');
	}
	return $title;

}

function rpf_content_fix($text){
	preg_match_all('@(<a.+?href=\".+?\">)(.*?</a>)@',$text,$m);
	$urls = $m[1];
	if(count($urls)){
		foreach($urls as $pos => $link){
			if(false === stripos($link,'http://') && false === stripos($link,'https://')){
		
				$text=str_replace($link,'',$text);
		
				$text=str_replace($m[2][$pos],str_replace('</a>','',$m[2][$pos]),$text);
			}
		}
	}
	$text=preg_replace("/[&|#|&#]+[a-z0-9]+;/i","",$text);
	$text=preg_replace('@<[dtrx][^>]*>@','',$text);
	$text=preg_replace('@</[dtrx][^>]*>@','',$text);
	return $text;

}
function rpf_parse_images($content,$link){
	
	preg_match_all('/<img(.+?)src=\"(.+?)\"(.*?)>/', $content, $images);
	$urls = $images[2];
       
      	if(count($urls)){

		foreach($urls as $pos => $url){
			$oldurl=$url;
			$meta=parse_url($url);
			
			if(!isset($meta['host'])){

				$meta=parse_url($link);
				$url=$meta['scheme'].'://'.$meta['host'].'/'.$url;
				
   			}
			
          		$newurl = rpf_cache_image($url);
          		if($newurl)
            			$content = str_replace($oldurl, $newurl, $content);
			else
				$content = str_replace($images[0][$pos],'',$content);
        	} 
        }
	return $content;
   	


}
function rpf_cache_image($url){
	if( strpos($url, "icon_") !== FALSE)
	      return false;
	global $rpf_options;
	$contents = rpf_get_file($url);
	
	if( !$contents )
		return false;
	$basename = basename($url);
	$paresed_url = parse_url($basename);
	
	$filename = substr(md5(time()), 0, 5) . '_' . $paresed_url['path'];
    	
	$cachepath = RPF_CACHE;
	$pluginpath = RPF_URL_ROOT;
    	$real_cachepath=dirname(__FILE__).'/'.$cachepath;
	if(is_writable(	$real_cachepath ) ){
		
		if($contents){

			file_put_contents($real_cachepath . $filename, $contents);
			$i=@exif_imagetype($real_cachepath . $filename);
			if($i)
				return $pluginpath . $cachepath . rawurlencode($filename);
		}
	}else{
		
		rpf_log($real_cachepath . " directory is not writable" );
		
	}
    
	return false;

}
function rpf_insert_post($title,$content,$category=array(1),$tags_input='',$meta='',$post_status='publish',$rpf_date,$post_author=1){
	
	
	$category=(array)$category;

	if(!$rpf_date)
		$rpf_date = time();
	
		
	$postid = wp_insert_post(array(
    		'post_title' 	          => $title,
  		'post_content'  	  => $content,
  		'post_category'           => $category,
		'tags_input'		  => $tags_input,
		'post_status'		  => $post_status,
		'post_author'             => $post_author,
  		"post_date" 		  => get_date_from_gmt($rpf_date),
    	));
	if($meta)
		foreach($meta as $key => $value) 
			rpf_insert_post_meta($postid, $key, $value);
	
		
	return $postid;

}

function rpf_full_feed($permalink){

	require_once(RPFINC.'readability.php');

	if ($permalink && $html = rpf_get_file($permalink)) {
		
		$html = rpf_convert_to_utf8($html);

		$content = grabArticleHtml($html);

		
	}else
		return false;

	if( false !== stripos($content,'readability was unable to parse this page for content') )
		return false;
	if( false !== stripos($content, 'return go_back();') )
		return false;
	
	return $content;

}
function rpf_insert_post_meta($postid, $key, $value) {
	global $wpdb;
		
	$result = $wpdb->query( "INSERT INTO $wpdb->postmeta (post_id,meta_key,meta_value ) " 
					                . " VALUES ('$postid','$key','$value') ");
					
	return $wpdb->insert_id;		
}

function rpf_is_duplicate($title){

	global $wpdb;
	global $rpf_options;

	$hash = rpf_item_hash($title);

	$row = $wpdb->get_row("SELECT * FROM {$rpf_options['db']['post']} "
                          . "WHERE hash = '{$hash}'");  
	if($row)
		return true;
	return false;


}

function rpf_item_hash($data){

	return sha1($data);
}
function rpf_get_file($url){

	if(ini_get('allow_url_fopen') != 1) {
		@ini_set('allow_url_fopen', '1');
	}

	if(ini_get('allow_url_fopen') != 1) {
		
		$ch = curl_init();
 
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //Set curl to return the data instead of printing it to the browser.
		curl_setopt($ch, CURLOPT_URL, $url);
 
		$data = curl_exec($ch);
		curl_close($ch);
 
		return $data;

    
	} else {
		return @file_get_contents($url);
	}
	
	return false;

 
}
function rpf_fetch_feed($url,$max_items){
  
    $url=str_replace(' ','+',$url);

     # SimplePie
    if(! class_exists('SimplePie'))
      require_once( RPFINC . 'simplepie.class.php' );
   
    $feed = new SimplePie();
    $feed->enable_order_by_date(false); 
    $feed->set_feed_url($url);
    $feed->set_item_limit($max_items);
    $feed->set_stupidly_fast(true);
    $feed->enable_cache(false);    
    $feed->init();
    $feed->handle_content_type(); 
    
    return $feed;

}

function rpf_get_feeds(){

	$rtn_feeds=array();
	global $rpf_options;
	
	$feeds=$rpf_options['feed'];
	
	foreach($feeds as $feed){
	
		
		$rtn_feeds[]=$feed;
			
	}
	return $rtn_feeds;


}

function rpf_get_feed($id){
	
	$feeds=rpf_get_feeds();
	
	foreach($feeds as $feed){
		
		if($feed['id'] == $id)		
			return $feed;
		
	}
	return false;


}

function rpf_update_feed($_feed){

	global $rpf_options;

	$feeds=$rpf_options['feed'];
	
		
	foreach($_feed as $k => $v)
		$_feed[$k]=str_replace('\\','',$v);

	foreach($feeds as $key => $feed){
		
		if($feed['id'] == $_feed['id']){

			if(empty($_feed['name'])||empty($_feed['url'])){
			
				unset($feeds[$key]);
				$feeds[]=array(
				'id' => $_feed['id'],
				'name' => '',
				'url' => '',
				'category' => 1,
				'tags' =>'',
				'frequency' => 8,
				'max_items' => 3,
				'lastactive' =>'0',
				'post_status' => 'publish',
				'publish_date' => 'Publish Immediately',
				'author' => 1
				);
				break;		
		
			}else{
				if($_feed['url'] == $feed['url'] && $_feed['name'] == $feed['name'])
					$_feed['lastactive']=$feed['lastactive'];
				else
					$_feed['lastactive']='0';

				unset($feeds[$key]);
				$feeds[$key]=$_feed;
				break;
			}
			
		}	
			
		
	}
	$rpf_options['feed']=$feeds;
	update_option('rpf_options',$rpf_options);
	
}
function rpf_update_feed_lastactive($id,$lastactive){

	global $rpf_options;

	$feeds=$rpf_options['feed'];

	foreach($feeds as $key => $feed){
		
		if($feed['id'] == $id){
			$backup=$feed;
			unset($feeds[$key]);
			$backup['lastactive']=$lastactive;
			$feeds[$key]=$backup;
			break;
			
		}	
			
		
	}
	$rpf_options['feed']=$feeds;
	update_option('rpf_options',$rpf_options);

}


function rpf_log($message){
   	global $wpdb;
    	global $rpf_options;
    	$message = $wpdb->escape($message);
      	$time = current_time('mysql', true);
      	$wpdb->query("INSERT INTO {$rpf_options['db']['log']} (message, created_on) VALUES ('{$message}', '{$time}') "); 
    		
}
function rpf_get_logs($page,$logs_per_page){
	global $wpdb;
  	global $rpf_options;
	

	if($page == 0) $page = 1;
	$page--;
	$orderby = "created_on";
	$ordertype = 'ASC';
	$start = $page * $logs_per_page;
	$end = $start + $logs_per_page;
	$limit = "LIMIT {$start}, {$end}";
	
  	
  	return $wpdb->get_results("SELECT * FROM {$rpf_options['db']['log']} ORDER BY $orderby $ordertype $limit");
}
function rpf_timezone_mysql($format, $time){
    return mysql2date($format, $time);    
}
//////////////////////////////////////////////
// Convert $html to UTF8
// (uses HTTP headers and HTML to find encoding)
// adapted from http://stackoverflow.com/questions/910793/php-detect-encoding-and-make-everything-utf-8
//////////////////////////////////////////////
function rpf_convert_to_utf8($html, $header=null)
{
	$accept = array(
		'type' => array('application/rss+xml', 'application/xml', 'application/rdf+xml', 'text/xml', 'text/html'),
		'charset' => array_diff(mb_list_encodings(), array('pass', 'auto', 'wchar', 'byte2be', 'byte2le', 'byte4be', 'byte4le', 'BASE64', 'UUENCODE', 'HTML-ENTITIES', 'Quoted-Printable', '7bit', '8bit'))
	);
	$encoding = null;
	if ($html || $header) {
		if (is_array($header)) $header = implode("\n", $header);
		if (!$header || !preg_match_all('/^Content-Type:\s+([^;]+)(?:;\s*charset=([^;"\'\n]*))?/im', $header, $match, PREG_SET_ORDER)) {
			// error parsing the response
		} else {
			$match = end($match); // get last matched element (in case of redirects)
			if (!in_array(strtolower($match[1]), $accept['type'])) {
				// type not accepted
				// TODO: avoid conversion
			}
			if (isset($match[2])) $encoding = trim($match[2], '"\'');
		}
		if (!$encoding) {
			if (preg_match('/^<\?xml\s+version=(?:"[^"]*"|\'[^\']*\')\s+encoding=("[^"]*"|\'[^\']*\')/s', $html, $match)) {
				$encoding = trim($match[1], '"\'');
			} elseif(preg_match('/<meta\s+http-equiv=["\']Content-Type["\'] content=["\'][^;]+;\s*charset=([^;"\'>]+)/i', $html, $match)) {
				if (isset($match[1])) $encoding = trim($match[1]);
			}
		}
		if (!$encoding) {
			$encoding = 'utf-8';
		} else {
			if (!in_array($encoding, array_map('strtolower', $accept['charset']))) {
				// encoding not accepted
				// TODO: avoid conversion
			}
			if (strtolower($encoding) != 'utf-8') {
				if (strtolower($encoding) == 'iso-8859-1') {
					// replace MS Word smart qutoes
					$trans = array();
					$trans[chr(130)] = '&sbquo;';    // Single Low-9 Quotation Mark
					$trans[chr(131)] = '&fnof;';    // Latin Small Letter F With Hook
					$trans[chr(132)] = '&bdquo;';    // Double Low-9 Quotation Mark
					$trans[chr(133)] = '&hellip;';    // Horizontal Ellipsis
					$trans[chr(134)] = '&dagger;';    // Dagger
					$trans[chr(135)] = '&Dagger;';    // Double Dagger
					$trans[chr(136)] = '&circ;';    // Modifier Letter Circumflex Accent
					$trans[chr(137)] = '&permil;';    // Per Mille Sign
					$trans[chr(138)] = '&Scaron;';    // Latin Capital Letter S With Caron
					$trans[chr(139)] = '&lsaquo;';    // Single Left-Pointing Angle Quotation Mark
					$trans[chr(140)] = '&OElig;';    // Latin Capital Ligature OE
					$trans[chr(145)] = '&lsquo;';    // Left Single Quotation Mark
					$trans[chr(146)] = '&rsquo;';    // Right Single Quotation Mark
					$trans[chr(147)] = '&ldquo;';    // Left Double Quotation Mark
					$trans[chr(148)] = '&rdquo;';    // Right Double Quotation Mark
					$trans[chr(149)] = '&bull;';    // Bullet
					$trans[chr(150)] = '&ndash;';    // En Dash
					$trans[chr(151)] = '&mdash;';    // Em Dash
					$trans[chr(152)] = '&tilde;';    // Small Tilde
					$trans[chr(153)] = '&trade;';    // Trade Mark Sign
					$trans[chr(154)] = '&scaron;';    // Latin Small Letter S With Caron
					$trans[chr(155)] = '&rsaquo;';    // Single Right-Pointing Angle Quotation Mark
					$trans[chr(156)] = '&oelig;';    // Latin Small Ligature OE
					$trans[chr(159)] = '&Yuml;';    // Latin Capital Letter Y With Diaeresis
					$html = strtr($html, $trans);
				}
				if(!class_exists('SimplePie_Misc'))
					require_once(RPFINC.'simplepie.class.php');

				$html = SimplePie_Misc::change_encoding($html, $encoding, 'utf-8');

				/*
				if (function_exists('iconv')) {
					// iconv appears to handle certain character encodings better than mb_convert_encoding
					$html = iconv($encoding, 'utf-8', $html);
				} else {
					$html = mb_convert_encoding($html, 'utf-8', $encoding);
				}
				*/
			}
		}
	}
	return $html;
}
if ( ! function_exists( 'exif_imagetype' ) ) {
    function exif_imagetype ( $filename ) {
        return @getimagesize( $filename );
    }
}
?>
