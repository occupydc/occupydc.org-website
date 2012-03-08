<?php
$postedFields = the_jalapeno_posted_fields($key);
$messages = the_jalapeno_messages($key);
if ($messages && $messages->hasMessages()) {
?>
<div class="messages">
<?php if ($messages->hasErrors()) : ?>
  <ul class="error-messages">
  <?php foreach ($messages->errors as $message) : ?>
    <li><?php echo $message; ?></li>
  <?php endforeach; ?>
  <?php foreach ($messages->validation as $id => $message) : ?>
    <li><?php echo $message; ?></li>
  <?php endforeach; ?>
  </ul>
<?php endif; ?>
<?php if (!empty($messages->success)) : ?>
  <ul class="success-messages">
    <li><?php echo $messages->success; ?></li>
  </ul>
<?php endif; ?>
</div>
<?php
}
?>

<?php if (!empty($messages->content)) : ?>
<div class="dia-content">
  <?php echo $messages->content; ?>
</div>
<?php else : ?>
<form name="form_<?php echo $key ?>" id="form_<?php echo $key ?>" 
  action="" method="post" class="nsjalapeno--action-form">
  <input type="hidden" name="dia_form_name" value="<?php echo $form['name'] ?>" />
<?php
  $items = $form['formitems'];
     
  foreach($fields as $field) {
    $id = $field['id'];
    
    if (!empty($items[$id])) {
      $attrs = array();
      if (!empty($messages->validation[$id])) {
          $attrs = array('class' => 'error');
      }
      echo $items[$id]->render($postedFields[$id], $attrs);
    }
  }
?>
  <div class="buttonrow">
    <input type="submit" name="diapluginformsubmit_<?php echo $key ?>" value="Send" />
  </div>
</form>
<?php endif; ?>