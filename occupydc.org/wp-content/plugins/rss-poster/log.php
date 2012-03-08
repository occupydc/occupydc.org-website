<?php

function rpf_log_page(){
	global $wpdb;
	$rpf_options=get_option('rpf_options');
	if(isset($_POST['rpf_clean_logs'])){
		
      		$wpdb->query("DELETE FROM {$rpf_options['db']['log']} WHERE 1=1 ");
		
    	}

	$logs_per_page = 30;

	$total = $wpdb->get_var("SELECT COUNT(*) as cnt FROM {$rpf_options['db']['log']} ");
	$page = 0;
	if(isset($_GET['p'])){

		if($_GET['p']=='last')
			$page=ceil($total / $logs_per_page);
		else
			$page = intval($_GET['p']);
	}
	
	
	$logs = rpf_get_logs($page,$logs_per_page);
        $baseurl=get_option('siteurl') . '/wp-admin/admin.php?page=rpf_log_page';
	$paging = paginate_links(array(
		'base' =>  $baseurl . '&s=logs&%_%',
		'format' => 'p=%#%',
		'total' =>  ceil($total / $logs_per_page),
		'current' => $page,
		'end_size' => 3
	));
	
?>

	<div class="wrapper">
	<h2>Log</h2>
	 <div class="logs_bar">
      	<form method="post">
       
        <input type="hidden" name="rpf_clean_logs" value="1" />
        <p id="clean_logs" class="submit">
          <input type="submit" value="Clean logs" />
        </p>
       </form>
    
      <div id="logs_pages">
        <?php echo $paging ? $paging . ' - ' : '' ?> Displaying <?php echo $total ?> log entries
      </div>
    </div>
    
    <?php if($logs): ?>
    <ul id="logs">
    <?php foreach($logs as $log): ?>
      <li><?php echo rpf_timezone_mysql('F j, g:i:s a', $log->created_on) ?> - <?php echo $log->message ?></li>
    <?php endforeach ?>
    </ul>
    <?php else: ?>
    <p><?php echo 'No logs to show'; ?>
    <?php endif ?>
    
  </div>
	</div>
<?php
}
?>
