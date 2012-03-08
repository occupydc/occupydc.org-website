<div id="jalapeno-status-messages" style="<?php echo count($statuses)? '' : 'display: none'; ?>">
  <ul>
    <?php for($i=0; $i<count($statuses); ++$i): ?>
      <li><?php echo $statuses[$i];?></li>
    <?php endfor; ?>
  </ul>
</div>

<div id="jalapeno-error-messages" style="<?php echo count($errors)? '' : 'display: none'; ?>">
  <ul>
    <?php for($i=0; $i<count($errors); ++$i): ?>
      <li><?php echo $errors[$i];?></li>
    <?php endfor; ?>
  </ul>
</div>