(function($){

// Make sure the namespace exists
window.nsjalapeno = window.nsjalapeno || {};


nsjalapeno.proxy = function(thisRef, fnName) {
  return function(){
    return thisRef[fnName].apply(thisRef, arguments);
  };
};


nsjalapeno.formeditor = {};



/**
 * Editor Class
 *
 * The class for the form editor.
 *
 * @param source string - the source for the edit
 * @param element element - the element to make the form editor
 */
nsjalapeno.formeditor.Editor = function(source, element, options){
  this.source = source;
  this.element = $(element);
  this.isCreated = false;
  
  this._editorItems = {}; // this._editorItems[controller uid] = controller
  
  this._replaceables = {};
  this._layoutHelpers = {};
  
  this._toolbars = {}; // this._toolbar[toolbar name][controller uid] = controller
  this._toolbarsElements = {};
  
  this.options = $.extend( nsjalapeno.formeditor.Editor.defaultOptions, options);

};


// Methods for Editor
nsjalapeno.formeditor.Editor.prototype = {
  /**
   * Create the editor
   */
  create: function(){
    if(this.isCreated){
      return;
    }
    this.canvas = $('<div class="nsjalapeno--canvas nsjalapeno--sortable nsjalapeno--sortable"></div>').appendTo(this.element);
    
    // Render
    // String modify
    var stringOut = this._renderEditString(this.source);
    // Create DOM by inserting
    this.canvas.html(stringOut); 
    
    // TODO append required items to editor if not already
    // this._appendRequiredItems();
    
    
    // Attach behaviors
    this._attachEditBehaviors();
    
    /*
    var out = this._renderEditForReplaceables(this.source);
    this.canvas.html(out);
    
    this.setupSortable(this.canvas);
    */
    this.createToolbars();
    this.isCreated = true;
  },
  
  /**
   * Get the saved results from the editor.
   *
   * @return string
   */
  save: function(){
    if(!this.isCreated){
      return '';
    }
    
    
    return this.renderFinal(this.canvas.contents());
  },
  
  
  /**
   * Renders the source for the editor
   *
   * @param source string - the source 
   *
   * @return string - the source with the replaceables inserted
   */
  _renderEditForReplaceables: function(source){
    var input=source;
    var output='';
    var m=null;
    
    m = nsjalapeno.formeditor.Editor.replaceableToken.exec(input);
    while( m ){
      output += m[1];
      output += this._renderEditForReplaceable( m[3] );
      input = m[5];
      
      m = nsjalapeno.formeditor.Editor.replaceableToken.exec(input);
    }
    
    output += input;
    
    return output;
  },
  
  
  
  
  /**
   * Parses and modifies the string source for the Editor
   *
   * The first step in converting the source for the editor.
   * 
   * @param source string
   * 
   * @return string - the string to be used for the editor
   */
  _renderEditString: function(source){
    var output = '';
    var input=source;
    var m=null;
    
    m = nsjalapeno.formeditor.Editor.replaceableToken.exec(input);
    while( m ){
      output += m[1];
      output += this._renderEditForReplaceable( m[3] );
      input = m[5];
      
      m = nsjalapeno.formeditor.Editor.replaceableToken.exec(input);
    }
    
    output += input;
    
    return output;
  },
  
  
  /**
   * Renders the source for a single replaceable
   */
  _renderEditForReplaceable: function(source){
    var m, output='', type, id, extra;
    
    m = nsjalapeno.formeditor.Editor.replaceableContentsToken.exec(source);
    if( m ){
      type = m[1];
      id = m[2];
      extra = m[4];
      
      if(this._replaceables[type] && this._replaceables[type][id]){
        var controller = this._replaceables[type][id];
        output = controller.renderEdit();
      }
    }
    
    return output;
  },
  
  
  
  /**
   * Attach the edit behaviors to the canvas
   */
  _attachEditBehaviors: function(){
    
    // Attach controllers to items that yet to have controllers
    // <div data-editor-item-type="layoutHelper" data-editor-item-key="two-columns">...</div>
    // A controller can be attached to multiple items
    
    // attach delete buttons
    var self = this;
    this.canvas.find('[data-controller]').each(function(){
      var c = self.getController(this);
      if(c){
        c.attachEditBehaviors(this);
      }
    });
    
    this.setupSortable(this.canvas);
  },
  
  
  
  /**
   * Renders the element(s) for output
   */
  renderFinal: function(elements){
    var out = '', editor=this;
    
    elements.each(function(){
      out += editor._renderFinalElement(this);
    });
    
    return out;
  },
  
  
  
  _renderFinalElement: function(element){
    // Text node: return value
    if(element.nodeType == Node.TEXT_NODE){
      return element.nodeValue;
    }
    
    // Element node
    if(element.nodeType == Node.ELEMENT_NODE){
      var $el = $(element);
      var uid = $el.attr('data-controller');
      
      // Element node with controller: use defined render
      if(uid && this._editorItems[uid]){
        return this._editorItems[uid].renderFinal();
      }
      
      // Generic element node
      var elClone = $el.clone().get(0);
      var contents = $el.contents();
      
      // remove empty attribues
      var i = 0;
      while(i<elClone.attributes.length){
        if(!elClone.attributes[i].nodeValue){
          elClone.removeAttribute(elClone.attributes[i].nodeName);
        } else {
          ++i;
        }
      }
      
      var elStr = $('<div>').append(elClone).html();
      if(contents.length){
        // Generic element with children
        var startTag='', endTag='';
        
        // get just the start and end tag
        var m = nsjalapeno.formeditor.Editor.splitTag.exec(elStr);
        if( m ){
          startTag = m[1];
          endTag = m[3];   
        } 
        
        return startTag + this.renderFinal(contents) + endTag;
        
      } else {
        // Empty generic element
        return elStr;
      }
      
    }
    
    return ''; // just in case it is not an element or text node
  },
  
  
  
  /**
   * Get the controller for an item
   */
  getController: function(el){
    var $el = $(el);
    var uid = $el.attr('data-controller');
    if(uid && this._editorItems[uid]){
      return this._editorItems[uid];
    }
    
    return null;
  },
  
  
  
  /**
   * Get the item for a controller
   *
   * @param key controller or string - either the key or the controller itself
   */
  itemsInCanvasForController: function(key){
    if(typeof key == 'object' && key.controllerUid){
      key = key.controllerUid;
    } else if(typeof key != 'string'){
      return null; // don't know what else to do
    }
    
    var o = this.canvas.find('[data-controller='+key+']');
    
    if(o.length){
      return o;
    } else {
      return null;
    }
  },
  
  
  
  /**
   *  Register an instance of a replaceable
   */
  registerItemsInstance: function(replaceable){
    this._editorItems[replaceable.controllerUid] = replaceable;
  },
  
  
  
  /**
   * Setup sortable
   */
  setupSortable: function(element){
    // check if the element itself is sortable
    if(element.hasClass('nsjalapeno--sortable')){
      element.sortable(/*this._sortableOptionsFactory()*/);
    }
    
    // setup any sortable child regions 
    element.find('.nsjalapeno--sortable').sortable(this._sortableOptionsFactory());
  },
  
  
  
  /**
   * Register a replaceable
   *
   * @param type string - the type of replaceable
   * @param id string - the id
   * @param replaceContent string - the element to replace
   * @param groupingName string (optional) - the name of the grouping to put it in, defaults to 'Default'
   * @param required boolean (optional) - is the replaceable required to be in the editor, default to false
   *   (it will add it if it is required and not present in the source)
   */
  registerReplaceable: function(type, id, replaceContent, toolbarName, required){
    toolbarName = typeof toolbarName == 'string' ? toolbarName : 'Default';
    required = required == 'true' || required === true;
    
    // make sure that we don't reference an empty item
    this._replaceables[type] = this._replaceables[type] || {};
    this._toolbars[toolbarName] = this._toolbars[toolbarName] || {};
    
    
    // Create replaceable record
    var controller = new nsjalapeno.formeditor.Replaceable({
      'id': id,
      'toolbarName' : toolbarName,
      'type': type,
      'replaced': replaceContent,
      '_type' : 'replaceable',
      'required': required
    }, this);
    
    
    this._replaceables[type][id] = controller;
    
    // add replaceable to toolbar
    this._toolbars[toolbarName][id] = controller;
  },
  
  
  
  
  /**
   * Create the toolbars
   */
  createToolbars: function(){
    var group, itemList, controller, single, canvasItems, el;
    
    for( var toolbarName in this._toolbars){
      group = $('<fieldset class="nsjalapeno--toolbar"><h4>'+toolbarName+'</h4></fieldset>');
      if(this.options.insertToolbars == 'before') {
        group.prependTo(this.element);
      } else {
        group.appendTo(this.element);
      }
      
      itemList = $('<div class="nsjalapeno--draggable"/>').appendTo(group);
      this._toolbarsElements[toolbarName] = itemList;
      
      // add all the items
      for( var id in this._toolbars[toolbarName] ){
      
        controller = this._toolbars[toolbarName][id];
        single = controller.isSingleInstances();
        canvasItems = this.itemsInCanvasForController(controller);
        
         // The condition to not add the item to the toolbar is when the item is only allowed to have a single instance and it already is in the editor
        el = $(controller.createToolbarElement());
        el.attr('data-controller', controller.controllerUid);
        el.attr('data-drag-item-id', nsjalapeno.formeditor.getUid()); // this is the only to find the element when it is dropped on the canvas
        if(single && canvasItems){
          el.hide();
        }
        itemList.append(el);
        
      }
      
      
      // Add the draggable behavior
      itemList.children().draggable(this._draggableOptionsFactory());
    }
    
    return;
  },
  
  
  
  
  
  
  
  /**
   * 
   */
  _sortableRecieveHelper: function(event, ui){
    var controller = this.getController(ui.sender);
    // add a unique id to the item that will be copied into the canvas so we can find it and then replace it
    var uid = ui.item.attr('data-drag-item-id');
    
    
    // use a quick timeout to interact after drag if finished up
    var self = this;
    setTimeout(function(){
      // replace the inserted item in the canvas with the actual item
      var replaceEl = $(controller.renderEdit());
      self.canvas.find('[data-drag-item-id='+uid+']').after(replaceEl).remove();
      controller.attachEditBehaviors(replaceEl);
      ui.item.attr('data-drag-item-id', null);
      
      // Remove the toolbar item if it is a single instances
      if(controller.isSingleInstances()){
        ui.sender.hide(); // hide or remove?
      }
    }, 10);
    
  },
    
  
  
  /**
   * 
   */
  _sortableBeforeStopHelper: function(event, ui){
    ui.item.removeClass('nsjalapeno--being-dragged');
  },
  
  
  
  
  
  
  
  /**
   * Generate sortable options
   */
  _sortableOptionsFactory: function(){
    return {
      connectWith: '.nsjalapeno--sortable',
      handle: '*:not(.nsjalapeno--sortable) .nsjalapeno--drag-handle, > .nsjalapeno--drag-handle',
      receive: nsjalapeno.proxy(this, '_sortableRecieveHelper'),
      beforeStop: nsjalapeno.proxy(this, '_sortableBeforeStopHelper')
    };
  },

  
  
  
  /**
   *
   */
  _draggableOptionsFactory: function(){
    return {
      connectToSortable: this.canvas,
      helper: 'clone',
      revert: 'invalid',
      start: nsjalapeno.proxy(this, '_draggableStartHelper'),
      stop: nsjalapeno.proxy(this, '_draggableStopHelper')
    };
  },
  
  
  
  _draggableStartHelper: function(event, ui){
    $(event.target).addClass('nsjalapeno--being-dragged');
  },
  
  
  _draggableStopHelper: function(event, ui){
    $(event.target).removeClass('nsjalapeno--being-dragged');
  },
  
  
  
  /**
   * Remove an item from the canvas
   * 
   * @param el element - the element to remove
   * @param controller - the controller for the element
   */
  removeItemFromCanvas: function(el, controller){
    el = $(el);
    
    if(this._toolbarsElements[controller.toolbarName]){
      var toolbarEl = this._toolbarsElements[controller.toolbarName].children('[data-controller='+controller.controllerUid+']');
      if( toolbarEl.length ){
        toolbarEl.show();
      }
    }
    
    el.remove();
  }
  
  
  
};



// Statics for Editor

// Regexp for a token:
//  m[1] => out content before
//  m[2] => open replaceable literal
//  m[3] => replaceable contents
//  m[4] => close replaceable literal
//  m[5] => remaining in source
nsjalapeno.formeditor.Editor.replaceableToken = /^([\s\S]*?)(\{\{)([\s\S]+?)(\}\})([\s\S]*)$/; // use [\s\S] instead of . since the . does not match newlines

// Regexp for a token contents:
//  m[1] => type
//  m[2] => id
//  m[4] => extra
nsjalapeno.formeditor.Editor.replaceableContentsToken = /^\s*(\w+)\s*:\s*(\w+)(\s+([\s\S]+?))?\s*$/;

// Regexp for getting the open and close tag
//  m[1] => open tag
//  m[2] => contents of the tag
//  m[3] => close tag
nsjalapeno.formeditor.Editor.splitTag = /^(<[^>]+>)([\s\S]*)(<\/[^>]+>)$/;

// Sortable settings for the editor
/*nsjalapeno.formeditor.Editor.sortableOptions = {
  connectWith: '.nsjalapeno--sortable',
  handle: '*:not(.nsjalapeno--sortable) .nsjalapeno--drag-handle, > .nsjalapeno--drag-handle',
  receive: function(event, ui){
  }
};*/


nsjalapeno.formeditor.Editor.defaultOptions = {
  'insertToolbars': 'before'
};





/**
 * Register a layout helper
 */
nsjalapeno.formeditor.registerLayoutHelper = function(){
 
};







/*
 Editor Item signature

 Editor items have the follow signature
 
 string controllerUid
 string renderFinal()
 renderEdit()
 isSingleInstances()
 isRequired()
 attachEditBehaviors()
*/



/**
 * Class for Replaceable
 *
 * @param options
 */
nsjalapeno.formeditor.Replaceable = function(options, editor){
  this.controllerUid = nsjalapeno.formeditor.getUid();
  this.element = null;
  this.replaced = options.replaced;
  this.isDeletable = options.isDeletable;
  this.type = options.type;
  this.id = options.id;
  this.editor = editor;
  this.editor.registerItemsInstance(this);
  this.required = options.required;
  this.toolbarName = options.toolbarName;
};

nsjalapeno.formeditor.Replaceable.prototype = {
  /**
   * Renders the replaceable for the final output
   *
   * @return string
   */
  renderFinal: function(){
    return '{{'+this.type+':'+this.id+'}}';
  },
  
  
  
  /**
   * Renders the replaceable for the Editor
   *
   * @return string - the string to put in for the replaceable.
   */
  renderEdit: function(){
    return '<div class="nsjalapeno--replaceable nsjalapeno--item" data-controller="'+this.controllerUid+'">'+this.replaced+'</div>';
  },
  
  
  
  /**
   * Creates an element to be inserted into the editor based on an action
   *
   * @return jQuery element 
   */
  createInsertElement: function(){
    var el = $(this.renderEdit());
    return this.attachEditBehaviors(el);
  },
  
  
  
  /**
   * Creates an element to represent the item in the toolbar
   *
   * @return jQuery element
   */
  createToolbarElement: function(){
    return $('<div class="nsjalapeno--replaceable nsjalapeno--item" data-controller="'+this.controllerUid+'"><div class="nsjalapeno--drag-handle"></div>'+this.replaced+'</div>');
  },
  
  
  
  
  /**
   * Binds the object to the element it represents in the editor
   *
   * @param element element 
   */
  bindToElement: function(element){
    this.element = $(element);
  },
  
  
  
  /**
   * Checks if there can only be one instances of these object in the editor.
   *
   * returns boolean - true if only one instance can exists in the editor
   */
  isSingleInstances: function(){
    return true;
  },
  
  
  
  /**
   * Checks if the item is required
   *
   * returns boolean - true if required, false otherwise
   */
  isRequired: function(){
    return this.required;
  },
  
  
  
  /**
   * 
   */
  attachEditBehaviors: function(el){
    el = $(el); 
    
    // drag handle
    el.prepend('<div class="nsjalapeno--drag-handle"></div>');
    
    // buttons
    var buttons = $('<div class="nsjalapeno--item-buttons"></div>').prependTo(el);
    el.hover(
      function(){
        buttons.css('display', 'block');
      },
      function(){
        buttons.css('display', '');
      }
    );
    
    // close button
    if(!this.isRequired()){
      var close = $('<span class="nsjalapeno--button" title="Remove"><span class="nsjalapeno--icon--close nsjalapeno--icon"></span></span>');
      buttons.append(close);
      var editor = this.editor;
      var controller = this;
      close.click(function(event){
        editor.removeItemFromCanvas(el, controller);
      });
    }
  
  }
  
  
  
  
  
};




/**
 * Get unique ID
 * 
 * Returns a unique ID string
 */
nsjalapeno.formeditor._uid = 1;
nsjalapeno.formeditor.getUid = function(){
  return 'nsjalapeno'+(nsjalapeno.formeditor._uid++);
};




/**
 * Renders the output for the final
 *
 * @param element element(s) - the element to render
 *
 * @return string - source
 */
nsjalapeno.formeditor.renderFinal = function(element){
  
  return '[render]';
};




})(jQuery);