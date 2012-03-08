<div class="nsjalapeno--action--signers">
  <h4>Last signed by:</h4>
  <ul>
<?php foreach ($signers as $signer) : ?>
  <?php $date = strtotime($signer->Last_Modified); ?>
	<li>
	  <div class="name"><?php echo "$signer->First_Name" ?></div>
	  <?php if (!empty($signer->City) || !empty($signer->State)) { ?>
	    <div class="city"><?php 
	       echo !empty($signer->City) ? "$signer->City" : '';
	       echo (!empty($signer->City) && !empty($signer->State)) ? ', ' : '';
		   echo !empty($signer->State) ? $signer->State : '';
		?></div>
	  <?php } ?>
	  <div class="date"><?php echo date('H:m a \o\n M j Y', $date); ?></div>
	</li>
<?php endforeach; ?>
  </ul>
</div>