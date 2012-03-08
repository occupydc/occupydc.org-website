<?php
require_once(dirname(__FILE__) . '/../../../wp-config.php');
require_once(dirname(__FILE__).'/RSSPoster.php');
if(!empty($_POST['feed_id'])){
$feed=rpf_get_feed($_POST['feed_id']);

rpf_process_feed($feed);

}
?>
