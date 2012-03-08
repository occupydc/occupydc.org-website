<?php $forms = $this->getForms(); ?>
<?php if (empty($forms)) : ?>
  <option value="">- <?php _e('Add a Salsa action form first'); ?> -</option>
<?php else : ?>
  <option value="">- <?php _e('Pick a form to edit'); ?> -</option>
  <?php foreach($forms as $key => $form) : ?>
    <option value="<?php echo $key; ?>"><?php echo $key; ?> - <?php echo $form['name']; ?></option>
  <?php endforeach; ?>
<?php endif; ?>
