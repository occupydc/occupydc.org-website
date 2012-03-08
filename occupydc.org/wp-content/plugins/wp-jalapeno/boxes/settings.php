<div class="wrap nosubsub" id="page_content">
  <div class="icon32" id="icon-wp-jalapeno"><br/></div>
  <h2><small>New Signature </small><?php _e('WP Jalape&ntilde;o Settings'); ?></h2>
  <br class="clear" />
  <?php $this->themeAdminMessages(); ?>
  <form method="post" action="options.php" onsubmit="return diacontext.checkSettings();">
      <?php settings_fields( 'dia-group' ); ?>
      <table class="form-table">
          <tr valign="top">
            <th scope="row">Salsa Host Server: </th>
            <?php $host = $this->getFieldValue('dia_server'); ?>
            <?php if (empty($host)) { $host = 'http://sandbox.salsalabs.com'; } ?>
            <td><input class="widefat" type="text" name="dia_server" id="dia_server" value="<?php echo $host; ?>" /></td>
            <td>This is the Salsa node where you've configured your actions</td>
          </tr>
          <tr valign="top">
            <th scope="row">Username: </th>
            <td><input class="widefat" type="text" name="dia_username" id="dia_username" value="<?php echo $this->getFieldValue('dia_username'); ?>" /></td>
            <td>The credentials you use for logging into the Salsa node</td>
          </tr>
          <tr valign="top">
            <th scope="row">Password: </th>
            <td><input class="widefat" type="password" name="dia_password" id="dia_password" value="<?php echo $this->getFieldValue('dia_password'); ?>" /></td>
          </tr>
          <tr valign="top">
            <th scope="row">Use default stylesheet</th>
            <td><input type="checkbox" name="dia_enable_css" id="dia_enable_css" value="1" <?php echo get_option('dia_enable_css') == 1 ? 'checked="checked"' : '' ?>/></td>
          </tr>
      </table>
      <div class="submit">
          <input type="submit" class="button-primary" value="Save Options" />
      </div>
  </form>
  <div class="submit">
      <input type="submit" class="button-primary" value="Test Connection" onclick="diacontext.testConnection({'url':'<?php echo $this->getAjaxUrl();  ?>'});"/>
  </div>
</div>