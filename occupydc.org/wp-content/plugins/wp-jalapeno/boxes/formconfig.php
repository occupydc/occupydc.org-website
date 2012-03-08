<?php 
/** Renders the form fields in the configuration form */
?>
<div class="form-name">
  Action Key: <span class="name"><?php echo $form['key']; ?></span><br/>
  Form Name: <span class="name"><?php echo $form['name']; ?></span>
  <p class="clear"><input type="submit" class="button-primary" name="refresh_form" value="<?php _e('Refresh'); ?>" id="form-source-refresh-button" onclick="diacontext.refreshForm({'key':'<?php echo $key?>', 'url':'<?php echo $parent->getAjaxUrl();  ?>'});" /></p>    
</div>

<div class="form-pieces">
    <ul id="form-dia" class="dia-form">
        <?php foreach($form['formitems'] as $keyField => $valueField) : ?>
        <li data-key="<?php echo $keyField; ?>" data-required="<?php echo $valueField->required? 'true': 'false'; ?>" ><?php echo $valueField->render(NULL, NULL, true); ?></li>
        <?php endforeach; ?>
    </ul>
    
    <textarea id="form-source" name="form-source"><?php echo $form['tokens']; ?></textarea>
</div>

<?php /* <div class="help"><b>Drag the fields from the Available Field box into the Display Fields box. Reorder them as you wish and then save</b></div>
<div class="message"><h4>Display Fields</h4></div>
*/ ?>

<div id="form-editor"></div>
<p class="clear">
  <input type="submit" class="button-primary" name="save_form" value="<?php _e('Save'); ?>" formid="<?php echo $key; ?>" posturl="<?php echo $parent->getAjaxUrl(); ?>" id="form-source-save-button"/>
</p>
<p class="note">
  <em>Note:</em> If any of your targets are based on your supporter's location, you <strong>must</strong> include
  the Zip field in your form, and its value must be required!  If you are targeting US House members or members of
  state legislative bodies, you should also include the Street field in your form.
</p>
<h3>To include this form:</h3>
<h4>On a page:</h4>
<p>Use the custom tag <code>[jalapeno-action:<?php echo $form['key']; ?>]</code> anywhere in your page content.</p>

<h4>In the sidebar:</h4>
<p>Add a new <a href="widgets.php">Jalape&ntilde;o Action widget</a> to your sidebar and configure it to use this action.</p>

<h4>In a template:</h4>
<p>Call the function <code>the_jalapeno_action(<?php echo $form['key']; ?>)</code> within your template.</p>

<h3>To see who took this action:</h3>
<h4>In the sidebar:</h4>
<p>Add a new <a href="widgets.php">Jalape&ntilde;o Action Signatures widget</a> to your sidebar and configure it to use this action.</p>

<h4>In a template:</h4>
<p>Call the function <code>the_jalapeno_action_signatures(<?php echo $form['key']; ?>, $max)</code> within your template.</p>