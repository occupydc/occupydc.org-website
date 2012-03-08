/*
	Javascript functions for DIA Plugin
*/
(function(window, $, undefined){
window.diacontext = window.diacontext || {};

diacontext.checkSettings = function() {
    var error=false;

    jQuery('#dia_username').removeClass('dia_error');
    jQuery('#dia_key').removeClass('dia_error');

    var pattern = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+.[a-zA-Z]{2,4}$/;
    var isok = pattern.test(jQuery('#dia_username').val());
    if (!isok) {
        jQuery('#dia_username').addClass('dia_error');
        error=true;
    }

    /*
    pattern = /^[0-9]+$/;
    var isok = pattern.test(jQuery('#dia_chapterkey').val());
    if (!isok) {
        jQuery('#dia_chapterkey').addClass('dia_error');
        error=true;
    }
    */

    if (error) {
        diacontext.addErrorMessage('Please check your data and save again...');
        diacontext.updateMessages();
        alert('Please check your data and save again...');
    }

    return !error;
};

diacontext.checkFormSettings = function() {
    var error=false;

    return !error;
};

diacontext.testConnection = function(params) {
    var data = {
        action: 'testConnection',
        username: jQuery('#dia_username').val(),
        password: jQuery('#dia_password').val(),
        server: jQuery('#dia_server').val()
    };
    jQuery.post(params['url'], data, function(response) {
        alert(response);
    });

};

diacontext.addNewActionForm = function(params) {
    var key = jQuery('#newformkey').val();
    if (key.length==0) {
        diacontext.addErrorMessage('Please check your data and save again...');
        diacontext.updateMessages();
        //alert('Please enter an action key...');
        return;
    }

    var data = {
        action: 'addFormKey',
        actionkey: key
    };
    jQuery.post(params['url'], data, function(response) {
        if (response['error']) {
            diacontext.addErrorMessages(response['error']);
        }
        else {
            jQuery('#diapreview').html(response['formname']);
            jQuery('#dia_formid').html(response['content'])
              .val(key).trigger('change');
            jQuery('#newformkey').val('');
            
            diacontext.addStatusMessage(response['formname']+' loaded succesfully!');
        }
        diacontext.updateMessages();
    },'json');
};

diacontext.refreshForm = function(params) {
    var key = params['key'];
    var data = {
        action: 'refreshForm',
        formid: key
    };
    jQuery.post(params['url'], data, function(response) {
      if (response['error']) {
        diacontext.addErrorMessages(response['error']);
      }
      else {
          jQuery('#diapreview').html(response['formname']);
          jQuery('#dia_formid').html(response['content'])
            .val(key).trigger('change');
          
          diacontext.addStatusMessage(response['formname']+' refreshed successfully!');
      }
      diacontext.updateMessages();
    }, 'json');
};

diacontext.loadForm = function(params) {
    var data = {
        action: 'loadForm',
        formid: params['object'].value
    };
    
    if(params['object'].value != ''){
      jQuery.post(params['url'], data, function(response) {
          jQuery('#forms_item').html(response);
          $('#jalapeno-form-editor').show();
          setTimeout(function(){diacontext.loadFormEditor('#forms_item');}, 10);
      });
      
      $('#jalapeno-select-form .select-form-box').addClass('form-selected');
    } else {
      $('#jalapeno-select-form .select-form-box').removeClass('form-selected');
      $('#forms_item').empty();
      $('#jalapeno-form-editor').hide();
    }
};

// load up the form
$(document).ready(function() {
  $('#dia_formid').change();
});

diacontext.loadFormEditor = function(refElem){
  refElem = $(refElem);
  
  // Add the editor area
  var editorElem =  $('#form-editor');
  
  // get the source
  var sourceInput = refElem.find('#form-source');
  var source = sourceInput.val();
  
  // create the editor
  var editor = new nsjalapeno.formeditor.Editor(source, editorElem);
  
  // get all the fields
  refElem.find('ul#form-dia > li').each(function(){
    var $this = $(this);
    var fieldKey = $this.attr('data-key');
    var isRequired = $this.attr('data-required') == 'true';
    var fieldContent = $this.html();
    
    editor.registerReplaceable('field', fieldKey, fieldContent, 'Available Fields', isRequired);
    
  });
  
  
  // create the editor
  editor.create();
  
  var saveButton = $('input#form-source-save-button');
  saveButton.click(function(event){
    var s = editor.save();
    sourceInput.val(s);

    var data = {
        action: 'saveForm',
        formid: $(this).attr('formid'),
        content: s
    };
    var url=$(this).attr('posturl');
    jQuery.post(url, data, function(response) {
        diacontext.addStatusMessage(response);
        diacontext.updateMessages();
        //alert(response);
    });

    event.stopPropagation();
    return false;
  });
};


diacontext._statusMessageQueue = [];
diacontext._errorMessageQueue = [];

diacontext.addStatusMessage = function($message){
  diacontext._statusMessageQueue.push($message);
};

diacontext.addErrorMessage = function($message){
  diacontext._errorMessageQueue.push($message);
};

diacontext.addErrorMessages = function($messages) {
  for (var i in $messages) {
    diacontext.addErrorMessage($messages[i]);
  }
}

diacontext.updateStatusMessages = function(){
  diacontext._updateMessages('#jalapeno-status-messages', diacontext._statusMessageQueue);
};

diacontext.updateErrorMessages = function(){
  diacontext._updateMessages('#jalapeno-error-messages', diacontext._errorMessageQueue);
};

diacontext.updateMessages = function(){
  diacontext.updateStatusMessages();
  diacontext.updateErrorMessages();
};



diacontext._updateMessages = function(el, queue){
  var ms = $(el);
  var ul = ms.children('ul');
  ul.empty();
  
  
  if(queue.length){
    while(queue.length){
      ul.append('<li>'+(queue.shift())+'</li>');
    }  
    
    ms.css('display', '');
  } else {
    ms.css('display', 'none');
  }
  
};

})(window, jQuery);