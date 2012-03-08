<div id="form_items" class="">
    <div id="container">
        <div class="table">
      <table class="form-table">
          <tr valign="top">
            <th scope="row">Form ID: </th>
            <td>
            <?php
                $forms = $this->getForms();
                if (is_array($forms)) { ?>
                <select name="dia_formid" id="dia_formid" onchange="diacontext.loadForm({'url':'<?php echo $this->getAjaxUrl(); ?>','object':this});">
                    <option value="">Select form to load</option>
                    <?php
                    foreach($forms as $key => $form) {
             ?>
                <option value="<?php echo $key; ?>"><?php echo $form['name']; ?></option>
             <?php
                    }
             ?>
                </select>
             <?php
                } else {
             ?>
                <label>No forms yet saved.</label>
            <?php
                }
            ?>
            </td>
          </tr>
      </table>
        </div>
      <div id="form_message">&nbsp;</div>
    </div>
    <div id="forms_item"></div>
</div>
